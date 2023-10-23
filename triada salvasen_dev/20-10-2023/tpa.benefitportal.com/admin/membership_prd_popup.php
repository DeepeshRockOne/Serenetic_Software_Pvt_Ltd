<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$membership_id = $_GET['id'];
$fee_id = $_GET['fee_id'];
if (isset($_GET['pages']) && $_GET['pages'] > 0) {
  $has_querystring = true;
  $per_page = $_GET['pages'];
} else {
  $per_page = 10;
}

$sch_params = array();
$incr = "";
$SortBy = "v.id";
$SortDirection = "DESC";
$currSortDirection = "ASC";
$has_querystring = false;

if($membership_id > 0){
	$incr .= " AND pa.prd_fee_id = :id";
	$sch_params[':id'] = $membership_id;
}
if($fee_id > 0){
	$incr .= " AND pa.fee_id = :fee_id";
	$sch_params[':fee_id'] = $fee_id;
}
if (isset($_GET["sort"]) && $_GET["sort"] != "") {
	$has_querystring = true;
	$SortBy = $_GET["sort"];
}

if (isset($_GET["direction"]) && $_GET["direction"] != "") {
	$has_querystring = true;
	$currSortDirection = $_GET["direction"];
}

$is_ajaxed = isset($_GET['is_ajaxed']) ? $_GET['is_ajaxed'] : "";
if (count($sch_params) > 0) {
  $has_querystring = true;
}
$page = "";
if(isset($_GET['page'])){
	$page = $_GET['page'];
}
$query_string = $has_querystring ? ($page ? str_replace('page=' . $page, "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';

$options = array(
    'results_per_page' => $per_page,
    'url' => 'membership_prd_popup.php?' . $query_string,
    'db_handle' => $pdo->dbh,
    'named_params' => $sch_params
);

$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);

if ($is_ajaxed) {
	$membership_name = "";
	if($membership_id){
		$membership_name = getname('prd_fees',$membership_id,'name','id');
	}
	try {
		
        $sql = "SELECT pm.name,pm.product_code,pm.status
				FROM prd_assign_fees pa
				JOIN prd_main pm on (pm.id=pa.product_id AND FIND_IN_SET(pa.prd_fee_id,pm.membership_ids))
				LEFT JOIN prd_company pc on (pc.id=pm.company_id)
				LEFT JOIN prd_category pct on (pct.id=pm.category_id)
				WHERE pm.is_deleted = 'N' AND pa.is_deleted = 'N' ". $incr . " GROUP BY pm.id";

		$paginate = new pagination($page, $sql, $options);
	    if ($paginate->success == true) {
	        $membership_data = $paginate->resultset->fetchAll();
	        $total_rows = count($membership_data);
	        
	    }
	} catch (paginationException $e) {
		echo $e;
		exit();
	}
	include_once 'tmpl/membership_prd_popup.inc.php';
  	exit;
}

$template = 'membership_prd_popup.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>