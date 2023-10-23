<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$sch_params = array();
$incr='';
$SortBy = "p.name";
$SortDirection = "DESC";
$currSortDirection = "ASC";

$has_querystring = false;
if (isset($_GET["sort_by"]) && $_GET["sort_by"] != "") {
	$has_querystring = true;
	$SortBy = $_GET["sort_by"];
}

$popup_is_ajaxed = checkIsset($_GET['popup_is_ajaxed']);  
$id = checkIsset($_GET['id']); 
$carrier_id = checkIsset($_GET['carrier_id']);
$membership_id = checkIsset($_GET['membership_id']);  
$vendor_id = checkIsset($_GET['vendor_id']);  
$name = checkIsset($_GET['name']); 
$display_id = checkIsset($_GET['display_id']); 
$total_rows = checkIsset($_GET['count']); 

if(!empty($carrier_id)){
  $sch_params[":carrier_id"] = makeSafe($carrier_id);
  // $incr .= " AND p.carrier_id = :carrier_id";
  $incr .= " AND md5(pf.id) = :carrier_id";
}

if(!empty($membership_id)){
  $sch_params[":membership_id"] = makeSafe($membership_id);
  $incr .= " AND FIND_IN_SET(:membership_id,p.membership_ids) AND pf.id = :membership_id";
  // $incr .= " AND pf.id = :membership_id";
  $sch_params[":membership_id"] = makeSafe($membership_id);
}
if(!empty($vendor_id)){
	$sch_params[":vendor_id"] = makeSafe($vendor_id);
    $incr .= " AND md5(pf.id) = :vendor_id";	
}

if (count($sch_params) > 0) {
	$has_querystring = true;
}

if (isset($_GET['pages']) && $_GET['pages'] > 0) {
	$has_querystring = true;
	$per_page = $_GET['pages'];
}
$query_string = $has_querystring ? (isset($_GET['page']) && $_GET['page'] ? str_replace('page=' . $_GET['page'], "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';
  
$options = array(
	'results_per_page' => $per_page,
	'url' => 'view_fees_details.php?' . $query_string,
	'db_handle' => $pdo->dbh,
	'named_params' => $sch_params,
);

$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);
if ($popup_is_ajaxed) {
	try {
			$sel_sql="SELECT p.name,p.product_code,p.status,p.product_type
				FROM prd_fees pf
	          	JOIN prd_assign_fees paf ON(pf.id = paf.prd_fee_id AND paf.is_deleted = 'N')   
	          	JOIN prd_main p ON(p.id = paf.product_id)
	          	WHERE p.is_deleted='N'
	          	" . $incr . " 
	          	GROUP BY p.id
		        ORDER BY $SortBy $currSortDirection";	
		
		$paginate = new pagination($page, $sel_sql, $options);
		if ($paginate->success == true) {
			$fetch_rows = $paginate->resultset->fetchAll();
			$total_rows = count($fetch_rows);
		}

	} catch (paginationException $e) {
		echo $e;
		exit();
	}
	include_once 'tmpl/view_fees_details.inc.php';
	exit;
}

$template = 'view_fees_details.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>