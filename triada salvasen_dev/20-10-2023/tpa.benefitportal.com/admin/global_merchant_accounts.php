<?php 
include_once dirname(__FILE__) . '/layout/start.inc.php';
$g_incr = '';
$g_sch_params = array();
$GlobalSortBy = "order_by";
$GlobalSortDirection = "ASC";
$currSortDirection = "ASC";
$tz = new UserTimeZone('m/d/Y g:i A T', $_SESSION['admin']['timezone']);
$has_querystring = false;

if (isset($_GET["sort_global_by"]) && $_GET["sort_global_by"] != "") {
	$has_querystring = true;
	$GlobalSortBy = $_GET["sort_global_by"];
}

if (isset($_GET["sort_global_direction"]) && $_GET["sort_global_direction"] != "") {
	$has_querystring = true;
	$currSortDirection = $_GET["sort_global_direction"];
}

$is_global_ajaxed = checkIsset($_GET['is_global_ajaxed']);


$processor_mid = checkIsset($_GET['processor_mid']);
$processor_date = checkIsset($_GET['processor_date']);
$payment_method_type = checkIsset($_GET['payment_method_type']);
$type = checkIsset($_GET['type']);

$processor_names = checkIsset($_GET['processor_names']);
$processor_names = (!empty($processor_names) && is_array($processor_names) ? implode(",", $processor_names) : $processor_names);

$status = checkIsset($_GET['status']);
$status = (!empty($status) && is_array($status) ? "'".implode("','", $status) . "'" : $status);

$agent_ids = checkIsset($_GET['agent_ids']);
$agent_ids = (!empty($agent_ids) && is_array($agent_ids) ? implode(",", $agent_ids) : $agent_ids);

$join_range = checkIsset($_GET['join_range']);
$fromdate = checkIsset($_GET["fromdate"]);
$todate = checkIsset($_GET["todate"]);
$added_date = checkIsset($_GET["added_date"]);

if($join_range != ""){
	if($join_range == "Range" && $fromdate!='' && $todate!=''){
	  $g_sch_params[':fromdate'] = date("Y-m-d",strtotime($fromdate));
	  $g_sch_params[':todate'] = date("Y-m-d",strtotime($todate));
	  $g_incr.=" AND DATE(p.created_at) >= :fromdate AND DATE(p.created_at) <= :todate";
	}else if($join_range == "Exactly" && $added_date!=''){
	  $g_sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
	  $g_incr.=" AND DATE(p.created_at) = :added_date";
	}else if($join_range == "Before" && $added_date!=''){
	  $g_sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
	  $g_incr.=" AND DATE(p.created_at) < :added_date";
	}else if($join_range == "After" && $added_date!=''){
	  $g_sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
	  $g_incr.=" AND DATE(p.created_at) > :added_date";
	}
  }

$processor_mid = cleanSearchKeyword($processor_mid); 
 
$table_incr = '';
if(!empty($agent_ids)){
	$g_incr .= " AND (is_assigned_to_all_agent = 'Y' OR pmaa.agent_id IN ($agent_ids))";
	$table_incr.=' LEFT JOIN payment_master_assigned_agent pmaa ON(pmaa.payment_master_id = p.id and pmaa.is_deleted="N" and pmaa.status!="Deleted") ';
}

if(!empty($status)){
	$g_incr .= " AND p.status IN($status) ";
}

if(!empty($processor_names)){
	$g_incr .= " AND p.id IN(".$processor_names.") ";
}

if(!empty($processor_mid)){
	$g_sch_params[':processor_mid'] = "%" . trim($processor_mid) . "%";
	$g_incr .= " AND p.merchant_id LIKE :processor_mid";
}

if(!empty($processor_date)){
	$g_sch_params[':processor_date'] = date("Y-m-d",strtotime($processor_date));
	$g_incr .= " AND DATE(p.created_at) = :processor_date";
}

if(!empty($payment_method_type)){
	if($payment_method_type == 'CC'){
		$g_sch_params[':payment_method_type'] = 'Y';
		$g_incr .= " AND p.is_cc_accepted = :payment_method_type";
	} else if($payment_method_type == 'ACH'){
		$g_sch_params[':payment_method_type'] = 'Y';
		$g_incr .= " AND p.is_ach_accepted = :payment_method_type";
	} else if($payment_method_type == 'ACH&CC'){
		$g_sch_params[':payment_method_type'] = 'Y';
		$g_incr .= " AND p.is_ach_accepted = :payment_method_type AND p.is_cc_accepted = :payment_method_type";
	}
}

if(!empty($type)){
	$g_sch_params[':type'] = trim($type);
	$g_incr .= " AND p.type = :type";
}

if (count($g_sch_params) > 0) {
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
	'url' => 'global_merchant_accounts.php?' . $query_string,
	'db_handle' => $pdo->dbh,
	'named_params' => $g_sch_params,
);

$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);

if ($is_global_ajaxed) {
	try {
		$sel_sql = "SELECT p.* 
			FROM payment_master p 
			$table_incr 
			WHERE p.is_deleted = 'N' AND p.type = 'Global' " . $g_incr . "
			 GROUP BY id ORDER BY $GlobalSortBy $currSortDirection,FIELD(p.STATUS,'Active','Inactive','Closed') ASC,created_at ASC";
		$paginate = new pagination($page, $sel_sql, $options);
		if ($paginate->success == true) {
			$fetch_rows = $paginate->resultset->fetchAll();
			$total_rows = count($fetch_rows);
		}		
		$is_ach_default = false;
		$is_cc_default = false;
		if(!empty($fetch_rows) && count($fetch_rows) > 0){
			foreach ($fetch_rows as $key => $value) {
				if($value['is_default_for_ach'] == 'Y'){
					$is_ach_default = true;
				}
				if($value['is_default_for_cc'] == 'Y'){
					$is_cc_default = true;
				}
				if($is_ach_default && $is_cc_default){
					break;
				}
			}
		}
	} catch (paginationException $e) {
		echo $e;
		exit();
	}

	include_once 'tmpl/global_merchant_accounts.inc.php';
	exit;
}

$exStylesheets = array('thirdparty/bootstrap-switch/css/bootstrap3/bootstrap-switch.css');

$tmpExJs = array('thirdparty/jquery_ui/js/jquery-ui-1.9.2.custom.min.js');

$exJs = array('thirdparty/bootstrap-switch/js/bootstrap-switch.js','thirdparty/ajax_form/jquery.form.js','thirdparty/masked_inputs/jquery.maskedinput.min.js');

include_once 'tmpl/global_merchant_accounts.inc.php';
?>