<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Commissions";
$breadcrumbes[1]['class'] = "";
$breadcrumbes[2]['title'] = "Advances";
$breadcrumbes[2]['class'] = "advances_commission.php";
$page_title = "Advance Commissions Builder";

// global advance commission rule code start
  $globalAdvSql = "SELECT md5(pf.id) as id,pf.display_id,pf.created_at,pf.charged_to,pf.rule_type,COUNT(DISTINCT pa.product_id) as total_products,pf.status,pm.price,pm.price_calculated_on,pm.id as matId,pm.pricing_model
            FROM prd_fees pf
            LEFT JOIN prd_assign_fees pa ON(pa.prd_fee_id=pf.id AND pa.is_deleted='N')
            LEFT JOIN prd_matrix pm ON(pa.fee_id=pm.product_id AND pm.is_deleted='N')
            WHERE pf.setting_type='ServiceFee' AND pf.rule_type='Global' AND pf.is_deleted='N'
            GROUP BY pf.id 
            ORDER BY pf.id ASC";
  $globalAdvRes = $pdo->select($globalAdvSql);

  $globalRuleChargedUser = $pdo->selectOne("SELECT GROUP_CONCAT(DISTINCT(pf.charged_to)) as chargedUser FROM prd_fees pf WHERE pf.setting_type='ServiceFee' AND pf.rule_type='Global' AND pf.status='Active' AND pf.is_deleted='N'");
  
  $globalChargedArr = !empty($globalRuleChargedUser['chargedUser']) ? explode(",", $globalRuleChargedUser['chargedUser']) : array();
// global advance commission rule code ends

$sch_params = array();
$incr = '';
$SortBy = "pf.created_at";
$SortDirection = "DESC";
$currSortDirection = "ASC";

$has_querystring = false;
if (!empty($_GET["sort_by"])) {
  $has_querystring = true;
  $SortBy = $_GET["sort_by"];
}

if (!empty($_GET["sort_direction"])) {
  $has_querystring = true;
  $currSortDirection = $_GET["sort_direction"];
}

$is_ajaxed = !empty($_GET['is_ajaxed']) ? $_GET['is_ajaxed'] : '';
$status = !empty($_GET['status']) ? $_GET['status'] : '';
$display_id = !empty($_GET['display_id']) ? $_GET['display_id'] : '';
$agent_id = !empty($_GET['agent_id']) ? $_GET['agent_id'] : '';

$display_id = cleanSearchKeyword($display_id);
$agent_id = cleanSearchKeyword($agent_id); 
 
if ($status != "") {
  $sch_params[':status'] = makeSafe($status);
  $incr .= " AND pf.status = :status";
}
if ($display_id != "") {
  $sch_params[':display_id'] = makeSafe($display_id);
  $incr .= " AND pf.display_id = :display_id";
}
if ($agent_id != "") {
  $sch_params[':agent_id'] = makeSafe($agent_id);
  $incr .= " AND a.rep_id = :agent_id";
}

if (count($sch_params) > 0) {
  $has_querystring = true;
}
if (isset($_GET['pages']) && $_GET['pages'] > 0) {
  $has_querystring = true;
  $per_page = $_GET['pages'];
}
$query_string = $has_querystring ? (!empty($_GET['page']) ? str_replace('page=' . $_GET['page'], "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';

$options = array(
  'results_per_page' => $per_page,
  'url' => 'advances_commission.php?' . $query_string,
  'db_handle' => $pdo->dbh,
  'named_params' => $sch_params,
);

$page = (!empty($_GET['page']) && $_GET['page'] > 0) ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);

if ($is_ajaxed) {
  try {
    $sel_sql = "SELECT md5(pf.id) as id,pf.display_id,pf.created_at,pf.charged_to,pf.rule_type,COUNT(DISTINCT pa.product_id) as total_products,pf.status,pm.price,pm.price_calculated_on,pm.id as matId,pm.pricing_model,a.rep_id as agentId,CONCAT(a.fname,' ',a.lname) as agentName,pm.pricing_model
            FROM prd_fees pf
            JOIN prd_assign_fees pa ON(pa.prd_fee_id=pf.id AND pa.is_deleted='N')
            JOIN customer a ON(a.id=pf.agent_id)
            LEFT JOIN prd_matrix pm ON(pa.fee_id=pm.product_id AND pm.is_deleted='N')
            WHERE pf.setting_type='ServiceFee' AND pf.rule_type='Variation' AND pf.is_deleted='N'" . $incr . " GROUP BY pf.id ORDER BY $SortBy $currSortDirection";
    $paginate = new pagination($page, $sel_sql, $options);
    if ($paginate->success == true) {
      $fetch_rows = $paginate->resultset->fetchAll();
      $total_rows = count($fetch_rows);
    }
  }catch (paginationException $e) {
    echo $e;
    exit();
  }
  include_once 'tmpl/advances_commission.inc.php';
  exit;
}


if (isset($_GET['delete']) && isset($_GET['rule_id'])) {
  $ruleId = $_GET['rule_id'];
  $response = array();
  if (!empty($ruleId)) {
    $selPrdFees = "SELECT pf.id as advRuleId,pf.display_id,GROUP_CONCAT(DISTINCT(pm.id)) as prd_ids
                  FROM prd_fees pf
                  JOIN prd_main pm ON(pm.prd_fee_id=pf.id)
                  WHERE md5(pf.id) =:id AND pf.is_deleted='N'";
    $resPrdFees = $pdo->selectOne($selPrdFees,array(':id'=>$ruleId));

    if(!empty($resPrdFees)){
      $advRuleId = $resPrdFees['advRuleId'];
      $feePrds = $resPrdFees['prd_ids'];

      $updParam = array('is_deleted' => 'Y');

      $updWhere = array(
        'clause' => 'id = :id AND is_deleted="N"',
        'params' => array(
          ':id' => makeSafe($advRuleId)
        )
      );
      $pdo->update("prd_fees", $updParam, $updWhere);

      $updWhere = array(
        'clause' => 'prd_fee_id = :id AND is_deleted="N"',
        'params' => array(':id' => $advRuleId)
      );
      $pdo->update('prd_main', $updParam, $updWhere);
    
      $updWhere = array(
        'clause' => 'product_id IN ('.$feePrds.') AND is_deleted="N"',
        'params' => array()
      );
      $pdo->update("prd_matrix",$updParam, $updWhere);

      $updWhere = array(
        'clause' => 'product_id IN ('.$feePrds.') AND is_deleted="N"',
        'params' => array()
      );
      $pdo->update("prd_matrix_criteria",$updParam, $updWhere);
    
      $updWhere = array(
        'clause' => 'prd_fee_id=:fee_id AND is_deleted="N"',
        'params' => array(":fee_id" => $advRuleId)
      );
      $pdo->update('prd_assign_fees', $updParam, $updWhere);

      //************* Activity Code Start *************
        $description['ac_message'] =array(
          'ac_red_1'=>array(
            'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
            'title'=>$_SESSION['admin']['display_id'],
          ),
          'ac_message_1' =>' Deleted Advance Commission Rule',
          'ac_red_2'=>array(
            //'href'=> '',
            'title'=>$resPrdFees['display_id'],
          ),
        ); 
        activity_feed(3, $_SESSION['admin']['id'], 'Admin', $advRuleId, 'prd_fees','Admin Deleted Advance Commission Rule', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
      //************* Activity Code End *************

      $response['status']="success";
      $response['message']="Advance Commission Rule Deleted Successfully";
    }else{
      $response['status'] = 'fail';
      $response['message'] = "Advance Commission Rule Not Found";
    }
  }
  echo json_encode($response);
  exit();
}

if(isset($_GET['rule_status']) && isset($_GET['rule_id'])){

  $ruleStatus = $_GET['rule_status'];
  $ruleId = $_GET['rule_id'];
  $ruleType = $_GET['ruleType'];
  $chargedTo = $_GET['chargedTo'];

  if(!empty($ruleId) && !empty($ruleStatus)){
    $resGlobalRule = array();
    $resAdvRule = array();

      $incr = '';
      $schParams = array();

      if(!empty($ruleId)){
        $incr .= " AND md5(id) !=:id";
        $schParams[":id"] = $ruleId;
      }
      if(!empty($chargedTo)){
        $incr .= " AND charged_to = :chargedTo";
        $schParams[":chargedTo"] = $chargedTo; 
      }
      if($ruleType == 'Global'){
        $selGlobalRule = "SELECT id,status,display_id,setting_type FROM prd_fees WHERE setting_type='ServiceFee' AND rule_type='Global' AND status='Active' AND is_deleted='N' $incr";
        $resGlobalRule = $pdo->selectOne($selGlobalRule,$schParams);
      }
    
    if(!empty($resGlobalRule['id'])){
      setNotifyError("Active Global Advance Commission rule already in System");
      redirect("advances_commission.php");
    }else{
      $selAdvRule = "SELECT id,status,display_id,setting_type FROM prd_fees WHERE setting_type='ServiceFee' AND md5(id) =:id";
      $resAdvRule = $pdo->selectOne($selAdvRule,array(':id'=>$ruleId));
    }

    if ($resAdvRule) {
      $update_params = array(
        'status' => makeSafe($ruleStatus),
      );
      $update_where = array(
        'clause' => 'id = :id',
        'params' => array(
          ':id' => makeSafe($resAdvRule['id'])
        )
      );
      $pdo->update("prd_fees", $update_params, $update_where);

      $description['ac_message'] =array(
        'ac_red_1'=>array(
          'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
          'title'=>$_SESSION['admin']['display_id'],
        ),
        'ac_message_1' =>' Updated Advance Commission Rule Status',
        'ac_red_2'=>array(
          'href'=> "advances_commission.php",
          'title'=> $resAdvRule['display_id'],
        ),
      ); 

      $description['key_value']['desc_arr']['status']='From '.$resAdvRule['status'].' To '. $ruleStatus;

      activity_feed(3, $_SESSION['admin']['id'], 'Admin', $resAdvRule['id'], 'prd_fees',"Admin Updated Advance Commission Rule Status", $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));

      setNotifySuccess("Advance Commission status changed successfully");
      redirect("advances_commission.php");
    }
  }
}

$template = 'advances_commission.inc.php';
include_once 'layout/end.inc.php';
?>