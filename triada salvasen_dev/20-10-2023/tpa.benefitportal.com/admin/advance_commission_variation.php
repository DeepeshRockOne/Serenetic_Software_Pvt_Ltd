<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$page_title = "Advance Commissions Builder";

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Commissions";
$breadcrumbes[1]['class'] = "";
$breadcrumbes[2]['title'] = "Advances";
$breadcrumbes[2]['link'] = 'advances_commission.php';
$breadcrumbes[3]['title'] = "Variation";


$response = array();
$advRuleId = !empty($_GET['advRuleId']) ? $_GET['advRuleId'] : 0;
$is_clone = isset($_GET['is_clone']) ? $_GET['is_clone'] : 'N';
$chargedTo = isset($_GET['chargedTo']) ? $_GET['chargedTo'] : 'Agents';

$advRes = array();
$feeRow = array();

if(!empty($advRuleId)){
  $advSql = "SELECT id,display_id,charged_to,rule_type,status,agent_id
  				FROM prd_fees WHERE setting_type='ServiceFee' AND is_deleted = 'N' AND md5(id) = :id";
  $advRes = $pdo->selectOne($advSql, array(":id" => $advRuleId));
}

  if(!empty($advRes['id'])){

    $displayId = checkIsset($advRes['display_id']);
    $status = checkIsset($advRes['status']);
    $agentId = checkIsset($advRes['agent_id']);

    $feeSql = "SELECT GROUP_CONCAT(id) as id
    FROM prd_main WHERE is_deleted = 'N' AND md5(prd_fee_id) = :id
    ORDER BY id";
    $feeRow = $pdo->selectOne($feeSql, array(":id" => $advRuleId));
    $advFeeIds = !empty($feeRow['id']) ? $feeRow['id'] : 0;

    if($is_clone == "Y"){
      $agentId = 0;
      $displayId=get_advance_comm_id();
    }else{
      //************* Activity Code Start *************
        $description['ac_message'] =array(
        'ac_red_1'=>array(
        'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
        'title'=>$_SESSION['admin']['display_id'],
        ),
        'ac_message_1' =>' Read Advance Commission Rule ',
        'ac_red_2'=>array(
        'href'=> $ADMIN_HOST.'/add_member_advance_global_rule.php?advRuleId='.$advRuleId,
        'title'=> $advRes['display_id'],
        ),
        );
        activity_feed(3, $_SESSION['admin']['id'], 'Admin', $advRes['id'], 'prd_fees',"Admin Read Advance Commission Rule", $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
      //************* Activity Code End *************
    }

    if($is_clone == "Y" && !empty($advFeeIds)){
      $feeIds = array();
      $advanceFees = explode(",", $advFeeIds);
      foreach ($advanceFees as $key => $prdId) {

        $advFeeRow = $pdo->selectOne("SELECT * FROM prd_main where id = :id", array(':id' => $prdId));

        $insParams = array(
          'product_code' => get_advance_comm_fee_id(),
          'product_type' => $advFeeRow['product_type'],
          'type' => 'Fees',
          'fee_type' => $advFeeRow['fee_type'],
          'initial_purchase' => $advFeeRow['initial_purchase'],
          'is_fee_on_renewal' => $advFeeRow['is_fee_on_renewal'],
          'fee_renewal_type' => $advFeeRow['fee_renewal_type'],
          'fee_renewal_count' => $advFeeRow['fee_renewal_count'],
          'payment_type' => $advFeeRow['payment_type'],
          'pricing_model' => $advFeeRow['pricing_model'],
          'advance_month' => $advFeeRow['advance_month'],
          'status' => $advFeeRow['status'],
          'record_type' => $advFeeRow['record_type'],
        );

        $advNewFeeId = $pdo->insert('prd_main',$insParams);
        array_push($feeIds, $advNewFeeId);


        $assignSql = "SELECT id,product_id FROM prd_assign_fees where fee_id=:fee_id AND is_deleted='N'";
        $assignRow = $pdo->select($assignSql, array(":fee_id" => $advFeeRow['id']));

        if(!empty($assignRow)){
          foreach ($assignRow as $key => $value) {
            $insert_params = array(
              'product_id' => $value['product_id'],
              'fee_id' => $advNewFeeId,
            ); 
            $prd_assign_fees = $pdo->insert("prd_assign_fees", $insert_params);
          }
        }

        $feePlanSql = "SELECT id,plan_type,price_calculated_on,price_calculated_type,price,product_id,pricing_effective_date,pricing_termination_date,payment_type,pricing_model
                      FROM prd_matrix where product_id = :product_id AND is_deleted='N'";
        $feePlanRow = $pdo->select($feePlanSql, array(":product_id" => $advFeeRow['id']));

        if(!empty($feePlanRow)){
          foreach ($feePlanRow as $key => $pm) {
            $plan_params = array(
              'product_id' => $advNewFeeId,
              'plan_type' => $pm['plan_type'],  
              'price_calculated_on' => $pm['price_calculated_on'],  
              'price_calculated_type' => $pm['price_calculated_type'],  
              'pricing_effective_date' => $pm['pricing_effective_date'],  
              'pricing_termination_date' => $pm['pricing_termination_date'],  
              'payment_type' => $pm['payment_type'],  
              'pricing_model' => $pm['pricing_model'],  
              'price' => $pm['price'],
            );
            $matID = $pdo->insert("prd_matrix", $plan_params);  
            
            $feeCatSql = "SELECT id,min_total,max_total 
                      FROM prd_matrix_criteria WHERE prd_matrix_id = :matrix_id AND is_deleted='N'";
            $feeCatRes = $pdo->select($feeCatSql, array(":matrix_id" => $pm['id']));

            if(!empty($feeCatRes)){
              foreach ($feeCatRes as $key => $catRow) {
                $insParams = array(
                    'product_id' => $advNewFeeId,
                    'prd_matrix_id' => $matID,
                    'min_total' => $catRow['min_total'],
                    'max_total' => $catRow['max_total'],
                  );
                $catId = $pdo->insert('prd_matrix_criteria',$insParams);
              }
            }
          }
        }
      }
      $advFeeIds = implode(",", $feeIds);
    }
  }else{
    $displayId=get_advance_comm_id();
    $status = "Active";
    $agentId = 0;
  }

if (isset($_GET['delete']) && isset($_GET['feeId'])) {
  $feeId = $_GET['feeId'];
  $response = array();
  if (!empty($feeId)) {

    $selPrdFees = "SELECT pm.id as advFeeId,pm.product_code as display_id
                  FROM prd_main pm
                  WHERE md5(pm.id) =:id AND pm.is_deleted='N'";
    $resPrdFees = $pdo->selectOne($selPrdFees,array(':id'=>$feeId));

    if(!empty($resPrdFees)){

      $advFeeId = $resPrdFees['advFeeId'];

      $insParams = array("is_deleted"=>'Y');
      $updWhere=array(
            'clause'=>'id=:id',
            'params'=>array(":id"=>$advFeeId)
        );
        $pdo->update("prd_main",$insParams,$updWhere);

      $insParams = array("is_deleted"=>'Y');
      $updWhere=array(
            'clause'=>'product_id=:product_id AND is_deleted="N"',
            'params'=>array(":product_id"=>$advFeeId)
        );
      $pdo->update("prd_matrix",$insParams,$updWhere);

      $insParams = array("is_deleted"=>'Y');
      $updWhere=array(
            'clause'=>'fee_id=:fee_id AND is_deleted="N"',
            'params'=>array(":fee_id"=>$advFeeId)
        );
      $pdo->update("prd_assign_fees",$insParams,$updWhere);

      $insParams = array("is_deleted"=>'Y');
      $updWhere=array(
          'clause'=>'product_id=:id AND is_deleted="N"',
          'params'=>array(":id"=>$advFeeId)
      );
      $pdo->update("agent_product_rule",$insParams,$updWhere);

      //************* Activity Code Start *************
        $description['ac_message'] =array(
          'ac_red_1'=>array(
            'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
            'title'=>$_SESSION['admin']['display_id'],
          ),
          'ac_message_1' =>' Deleted Advance Commission Fee',
          'ac_red_2'=>array(
            //'href'=> '',
            'title'=>$resPrdFees['display_id'],
          ),
        ); 
        activity_feed(3, $_SESSION['admin']['id'], 'Admin', $advFeeId, 'prd_main','Admin Deleted Advance Commission Fee', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
      //************* Activity Code End *************

      $response['status']="success";
      $response['message']="Advance Commission Fee Deleted Successfully";
    }else{
      $response['status'] = 'fail';
      $response['message'] = "Advance Commission Fee Not Found";
    }
  }
  echo json_encode($response);
  exit();
}


$rincr = "";
$rparams = array();

if(!empty($chargedTo)){
  $rincr .= " AND charged_to=:charged_to";
  $rparams[":charged_to"] =  $chargedTo;
}

if(!empty($advRuleId) && $is_clone == "N"){
  $rincr .= " AND md5(id) != :id";
  $rparams[":id"] =  $advRuleId;
}

$sqlRuleAgents = "SELECT GROUP_CONCAT(DISTINCT(agent_id)) as agentIds
          FROM prd_fees WHERE setting_type='ServiceFee' AND is_deleted = 'N' AND rule_type='Variation' $rincr";
$resRuleAgents = $pdo->selectOne($sqlRuleAgents, $rparams);


$aincr = "";

if(!empty($resRuleAgents["agentIds"])){
  $aincr .= " AND c.id NOT IN(".$resRuleAgents["agentIds"].")";
}

$agents = $pdo->select("SELECT c.id,c.rep_id,CONCAT(c.fname,' ',c.lname) as agent_name FROM customer c JOIN customer_settings cs on(cs.customer_id = c.id) where c.type = 'Agent' AND cs.agent_coded_level != 'LOA' $aincr");

$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache);
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache);

$template = 'advance_commission_variation.inc.php';
include_once 'layout/end.inc.php';
?>