<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Commissions";
$breadcrumbes[2]['title'] = "PMPMs";
$page_title = "PMPMs";

$productSql="SELECT p.id,p.name,p.product_code,p.type,c.title FROM prd_main p 
            LEFT JOIN prd_category c ON (c.id = p.category_id)
            WHERE p.type!='Fees' AND p.name != '' AND p.is_deleted='N' AND p.record_type='Primary'
            ORDER BY c.title,name ASC";
$productRes=$pdo->select($productSql);
$agents = $pdo->select("SELECT id,rep_id,CONCAT(fname,' ',lname) as agent_name,email FROM customer where type = 'agent'");

$company_arr=array();
if($productRes){
    foreach ($productRes as $key => $row) {
      if($row['type']=='Kit'){
            $row['title']= 'Product Kits';
        }
        if (!array_key_exists($row['title'], $company_arr)) {
                $company_arr[$row['title']] = array();
        }
        array_push($company_arr[$row['title']], $row);
    }
}


$selPmpmRules = "SELECT pc.id,pc.rule_code
                FROM pmpm_commission pc
                WHERE pc.is_deleted = 'N'
                GROUP BY pc.id ORDER BY pc.created_at DESC";
$resPmpmRules = $pdo->select($selPmpmRules);

$status = !empty($_GET["status"]) ? $_GET["status"] : '';

$sch_params = array();
$incr = '';
$SortBy = "pc.created_at";
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
$is_export = !empty($_GET['is_export']) ? $_GET['is_export'] : '';
$pmpmCommIds = !empty($_GET['pmpmCommIds']) ? $_GET['pmpmCommIds'] : array();
$search_date_options = (isset($_GET["join_range"])) ? $_GET["join_range"] : "";
$search_effective_date_options = (isset($_GET["effective_join_range"])) ? $_GET["effective_join_range"] : "";
$from_date = (isset($_GET["from_date"])) ? $_GET["from_date"] : "";
$end_date = (isset($_GET["end_date"])) ? $_GET["end_date"] : "";
$added_date = (isset($_GET["added_date"])) ? $_GET["added_date"] : "";
$effective_from_date = (isset($_GET["effective_from_date"])) ? $_GET["effective_from_date"] : "";
$effective_end_date = (isset($_GET["effective_end_date"])) ? $_GET["effective_end_date"] : "";
$effective_date = (isset($_GET["effective_date"])) ? $_GET["effective_date"] : "";
$agent_ids = !empty($_GET["receiving_agents"]) ? $_GET["receiving_agents"] : '';
$product = !empty($_GET["product"]) ? implode(',',$_GET["product"]) : '';
$status = !empty($_GET['status']) ? $_GET['status'] : '';
$effective_date = !empty($_GET['effective_date']) ? $_GET['effective_date'] : '';

if (!empty($pmpmCommIds)) {
  $incr.=" AND pc.id IN (".implode(',', $pmpmCommIds).")";
}
if (!empty($agent_ids)) {
  $agent_ids = "'" . implode("','", $agent_ids) . "'";
  $incr.=" AND pc.agent_id IN ($agent_ids)";
}

if($search_date_options != ""){
  if($search_date_options == "Range"){
    $sch_params[':from_date'] = date("Y-m-d",strtotime($from_date));
    $sch_params[':end_date'] = date("Y-m-d",strtotime($end_date));
    $incr.=" AND DATE(pc.created_at) >= :from_date AND DATE(pc.created_at) <= :end_date";
  }else if($search_date_options == "Exactly"){
    $sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
    $incr.=" AND DATE(pc.created_at) = :added_date";
  }else if($search_date_options == "Before"){
    $sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
    $incr.=" AND DATE(pc.created_at) < :added_date";
  }else if($search_date_options == "After"){
    $sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
    $incr.=" AND DATE(pc.created_at) > :added_date";
  }
}

if($search_effective_date_options != ""){
  if($search_effective_date_options == "Range"){
    $sch_params[':effective_from_date'] = date("Y-m-d",strtotime($effective_from_date));
    $sch_params[':effective_end_date'] = date("Y-m-d",strtotime($effective_end_date));
    $incr.=" AND DATE(pcr.effective_date) >= :effective_from_date AND DATE(pcr.effective_date) <= :effective_end_date";
  }else if($search_effective_date_options == "Exactly"){
    $sch_params[':effective_added_date'] = date("Y-m-d",strtotime($effective_date));
    $incr.=" AND DATE(pcr.effective_date) = :effective_added_date";
  }else if($search_effective_date_options == "Before"){
    $sch_params[':effective_date'] = date("Y-m-d",strtotime($effective_date));
    $incr.=" AND DATE(pcr.effective_date) < :effective_date";
  }else if($search_effective_date_options == "After"){
    $sch_params[':effective_date'] = date("Y-m-d",strtotime($effective_date));
    $incr.=" AND DATE(pcr.effective_date) > :effective_date";
  }
}

if ($status != "") {
  $sch_params[':status'] = makeSafe($status);
  $incr .= " AND pc.status = :status";
}

if(!empty($product)){
  $incr .= " AND pcrap.product_id in (".$product.")";
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
  'url' => 'pmpm_commission.php?' . $query_string,
  'db_handle' => $pdo->dbh,
  'named_params' => $sch_params,
);

$page = (!empty($_GET['page']) && $_GET['page'] > 0) ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);

if (isset($_GET['fees_status']) && isset($_GET['fee_id'])) {

  $change_type = $_GET['fees_status'];
  $fee_id = $_GET['fee_id'];

  if ($fee_id != "" && $change_type != "") {

    $query = "SELECT pc.id,c.rep_id,pc.status FROM customer c join pmpm_commission pc on (pc.agent_id = c.id) WHERE md5(pc.id) = :id";
    $srow = $pdo->selectOne($query,array(':id' => $fee_id));


    $updateSql = array('status' => makeSafe($change_type));
    $where = array("clause" => 'id=:id', 'params' => array(':id' => makeSafe($srow['id'])));
    $pdo->update("pmpm_commission", $updateSql, $where);

    $description['ac_message'] =array(
        'ac_red_1'=>array(
          'href'=>$ADMIN_HOST.'/admin_profile.php?id='.$_SESSION['admin']['id'],
          'title'=>$_SESSION['admin']['display_id'],
        ),
        'ac_message_1' =>' changed PMPM commission status ',
        'ac_red_2'=>array(
            'title'=>$srow['rep_id'],
        ),
      ); 

      $description['Status']= 'From '.$srow['status'].' To '. $change_type;

      activity_feed(3, $_SESSION['admin']['id'], 'Admin',$srow['id'], 'pmpm_commission','PMPM commission status changed', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
    
    setNotifySuccess("PMPM Commission status changed successfully");

    redirect("pmpm_commission.php");
  }
}

if (isset($_GET['delete']) && isset($_GET['fee_id'])) {
  $fee_id = $_GET['fee_id'];

  if ($fee_id != "" && $fee_id > 0) {
    
    $query = "SELECT c.rep_id FROM customer c join pmpm_commission pc on (pc.agent_id = c.id) WHERE pc.id = :id";
    $srow = $pdo->selectOne($query,array(':id' => $fee_id));

    $updateSql = array('is_deleted' => 'Y');
    $where = array("clause" => 'id=:id', 'params' => array(':id' => makeSafe($fee_id)));
    $pdo->update("pmpm_commission", $updateSql, $where);

    $updateSql = array('is_deleted' => 'Y');
	$where = array("clause" => 'commission_id=:id', 'params' => array(':id' => makeSafe($fee_id)));
	$pdo->update("pmpm_commission_rule", $updateSql, $where);
	$pdo->update("pmpm_commission_rule_plan_type", $updateSql, $where);
	$pdo->update("pmpm_commission_rule_assign_product", $updateSql, $where);
	$pdo->update("pmpm_commission_rule_assign_agent", $updateSql, $where);

    $description['ac_message'] =array(
      'ac_red_1'=>array(
        'href'=>$ADMIN_HOST.'/admin_profile.php?id='.$_SESSION['admin']['id'],
        'title'=>$_SESSION['admin']['display_id'],
      ),
      'ac_message_1' =>' deleted PMPM Commission ',
      'ac_red_2'=>array(
          // 'href'=>$ADMIN_HOST.'/memberships_mange.php?id='.md5($fee_id),
          'title'=>$srow['rep_id'],
      ),
    ); 

    activity_feed(3, $_SESSION['admin']['id'], 'Admin', $fee_id, 'prd_fees','deleted PMPM Commission', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));

    $response['status'] = 'success';
    $response['message'] = "PMPM Commission deleted successfully";

    echo json_encode($response);
    exit();
  }
}
if ($is_ajaxed) {

  try {
  
    $sel_sql = "SELECT pc.id,pc.rule_code,pc.status,pc.created_at,pcr.display_id,c.rep_id,CONCAT(c.fname,' ',c.lname) as agent_name,count(DISTINCT pm.id) as total_products
                FROM pmpm_commission pc
                LEFT JOIN pmpm_commission_rule pcr on (pc.id = pcr.commission_id AND pcr.is_deleted = 'N')
                LEFT JOIN pmpm_commission_rule_assign_product pcrap on (pc.id = pcrap.commission_id AND pcrap.is_deleted = 'N')
                LEFT JOIN prd_main pm on(pm.id = pcrap.product_id AND pm.is_deleted = 'N')
                JOIN customer c on (pc.agent_id = c.id)
                WHERE pc.is_deleted = 'N'" . $incr . " GROUP BY pc.id ORDER BY $SortBy $currSortDirection";
    $paginate = new pagination($page, $sel_sql, $options);
      if ($paginate->success == true) {
      $fetch_rows = $paginate->resultset->fetchAll();
      $total_rows = count($fetch_rows);
    }
  } catch (paginationException $e) {
    echo $e;
    exit();
  }

  include_once 'tmpl/pmpm_commission.inc.php';
  exit;
}

if ($is_export) {
    $csv_line = "\n";
    $csv_seprator = "\t";
    $content = "";

    $content .= "PMPM ID" . $csv_seprator .
                "Added Date" . $csv_seprator .
                "Agent Name" . $csv_seprator .
                "Total Products" . $csv_seprator .
                "Total Agents" . $csv_seprator .
                "Status" . $csv_line;

    $sql = "SELECT pc.id,pc.status,pc.created_at,pcr.display_id,c.rep_id,CONCAT(c.fname,' ',c.lname) as agent_name,count(DISTINCT pcrap.product_id) as total_products,count(DISTINCT pcraa.agent_id) as total_agents
                FROM pmpm_commission pc
                JOIN pmpm_commission_rule pcr on (pc.id = pcr.commission_id AND pcr.is_deleted = 'N')
                JOIN pmpm_commission_rule_assign_product pcrap on (pc.id = pcrap.commission_id AND pcrap.is_deleted = 'N')
                JOIN pmpm_commission_rule_assign_agent pcraa on (pc.id = pcraa.commission_id AND pcraa.is_deleted = 'N')
                JOIN customer c on pc.agent_id = c.id
                WHERE pc.is_deleted = 'N'" . $incr . " GROUP BY pc.id ORDER BY $SortBy $currSortDirection";
    $membership_data = $pdo->select($sql,$sch_params);

    if($membership_data){
      foreach ($membership_data as $key => $value) {
        $content .= $value['display_id'] . $csv_seprator .
                    date('m/d/Y',strtotime($value['created_at'])) . $csv_seprator .
                    $value['rep_id']."(".$value['agent_name']. ")" . $csv_seprator .
                    $value['total_products'] . $csv_seprator .
                    $value['total_agents'] . $csv_seprator .
                    $value['status'] . $csv_line;
      }
      if ($content) {
          $csv_filename = "PMPM_commissions_" . date("Ymd", time()) . ".xls";
          header('Content-type: application/vnd.ms-excel');
          header('Content-disposition: attachment;filename=' . $csv_filename);
          echo $content;
          exit;
      }
    }
}


$exStylesheets = array(
	'thirdparty/multiple-select-master/multiple-select.css',
);
$exJs = array(
	'thirdparty/multiple-select-master/jquery.multiple.select.js',
	'thirdparty/masked_inputs/jquery.maskedinput.min.js'
);

$template = 'pmpm_commission.inc.php';
include_once 'layout/end.inc.php';
?>