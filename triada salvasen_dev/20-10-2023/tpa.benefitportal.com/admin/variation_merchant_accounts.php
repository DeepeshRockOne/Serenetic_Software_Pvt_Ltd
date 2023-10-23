<?php 
	include_once dirname(__FILE__) . '/layout/start.inc.php';

$v_incr = '';
$v_sch_params = array();
$VaritaionSortBy = "order_by";
$VaritaionSortDirection = "ASC";
$currSortDirection = "ASC";
$tz = new UserTimeZone('m/d/Y g:i A T', $_SESSION['admin']['timezone']);
$has_querystring = false;
if (isset($_GET["sort_variation_by"]) && $_GET["sort_variation_by"] != "") {
	$has_querystring = true;
	$VaritaionSortBy = $_GET["sort_variation_by"];
}
if (isset($_GET["sort_variation_direction"]) && $_GET["sort_variation_direction"] != "") {
	$has_querystring = true;
	$currSortDirection = $_GET["sort_variation_direction"];
}

$is_variation_ajaxed = checkIsset($_GET['is_variation_ajaxed']);
$processor_mid = checkIsset($_GET['processor_mid']);

$payment_method_type = checkIsset($_GET['payment_method_type']);
$payment_type = checkIsset($_GET['payment_type']);

$join_range = checkIsset($_GET['join_range']);
$fromdate = checkIsset($_GET["fromdate"]);
$todate = checkIsset($_GET["todate"]);
$added_date = checkIsset($_GET["added_date"]);

$processor_names = checkIsset($_GET['processor_names']);
$processor_names = (!empty($processor_names) && is_array($processor_names) ? implode(",", $processor_names) : $processor_names);

$status = checkIsset($_GET['status']);
$status = (!empty($status) && is_array($status) ? "'".implode("','", $status) . "'" : $status);

$agent_ids = checkIsset($_GET['agent_ids']);
$agent_ids = (!empty($agent_ids) && is_array($agent_ids) ? implode(",", $agent_ids) : $agent_ids);

if($join_range != ""){
	if($join_range == "Range" && $fromdate!='' && $todate!=''){
	  $v_sch_params[':fromdate'] = date("Y-m-d",strtotime($fromdate));
	  $v_sch_params[':todate'] = date("Y-m-d",strtotime($todate));
	  $v_incr.=" AND DATE(p.created_at) >= :fromdate AND DATE(created_at) <= :todate";
	}else if($join_range == "Exactly" && $added_date!=''){
	  $v_sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
	  $v_incr.=" AND DATE(p.created_at) = :added_date";
	}else if($join_range == "Before" && $added_date!=''){
	  $v_sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
	  $v_incr.=" AND DATE(p.created_at) < :added_date";
	}else if($join_range == "After" && $added_date!=''){
	  $v_sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
	  $v_incr.=" AND DATE(p.created_at) > :added_date";
	}
  }

$processor_mid = cleanSearchKeyword($processor_mid); 
 
$table_incr = '';
if(!empty($agent_ids)){
	$v_incr .= " AND (p.is_assigned_to_all_agent = 'Y' OR pmaa.agent_id IN ($agent_ids))";
	$table_incr.=' LEFT JOIN payment_master_assigned_agent pmaa ON(pmaa.payment_master_id = p.id and pmaa.is_deleted="N" and pmaa.status!="Deleted") ';
}

if(!empty($status)){
	$v_incr .= " AND p.status IN($status) ";
}

if(!empty($processor_names)){
	$v_incr .= " AND p.id IN(".$processor_names.") ";
}

if(!empty($processor_mid)){
	$v_sch_params[':processor_mid'] = "%" . trim($processor_mid) . "%";
	$v_incr .= " AND p.merchant_id LIKE :processor_mid";
}

if(!empty($payment_method_type)){
	if($payment_method_type == 'CC'){
		$v_sch_params[':payment_method_type'] = 'Y';
		$v_incr .= " AND p.is_cc_accepted = :payment_method_type";
	} else if($payment_method_type == 'ACH'){
		$v_sch_params[':payment_method_type'] = 'Y';
		$v_incr .= " AND p.is_ach_accepted = :payment_method_type";
	} else if($payment_method_type == 'ACH&CC'){
		$v_sch_params[':payment_method_type'] = 'Y';
		$v_incr .= " AND p.is_ach_accepted = :payment_method_type AND p.is_cc_accepted = :payment_method_type";
	}
}

if(!empty($payment_type)){
	$v_sch_params[':payment_type'] = trim($payment_type);
	$v_incr .= " AND p.type = :payment_type";
}

if (count($v_sch_params) > 0) {
	$has_querystring = true;
}
if (isset($_GET['pages']) && $_GET['pages'] > 0) {
	$has_querystring = true;
	$per_page = $_GET['pages'];
}
$page = isset($_GET['page']) ? $_GET['page'] : '';
$query_string = $has_querystring ? (!empty($page) ? str_replace('page=' . $page, "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';

$options = array(
	'results_per_page' => $per_page,
	'url' => 'variation_merchant_accounts.php?' . $query_string,
	'db_handle' => $pdo->dbh,
	'named_params' => $v_sch_params,
);

$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);

if ($is_variation_ajaxed) {
	try {
		$sel_sql = "SELECT p.* 
				FROM payment_master p 
				$table_incr 
				WHERE p.is_deleted = 'N' AND p.type = 'Variation' " . $v_incr . "
	         GROUP BY id ORDER BY  FIELD(p.STATUS,'Active','Inactive','Closed') ASC, $VaritaionSortBy  $currSortDirection , created_at ASC";
		$paginate = new pagination($page, $sel_sql, $options);
		if ($paginate->success == true) {
			$fetch_rows = $paginate->resultset->fetchAll();
			$total_rows = count($fetch_rows);
		}
	} catch (paginationException $e) {
		echo $e;
		exit();
	}

	include_once 'tmpl/variation_merchant_accounts.inc.php';
	exit;
}
include_once 'tmpl/variation_merchant_accounts.inc.php';
?>