<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$sch_params = array();
$incr='';
$SortBy = "pa.created_at";
$SortDirection = "DESC";
$currSortDirection = "ASC";

$has_querystring = false;
if (isset($_GET["sort_by"]) && $_GET["sort_by"] != "") {
	$has_querystring = true;
	$SortBy = $_GET["sort_by"];
}

$popup_is_ajaxed = checkIsset($_GET['popup_is_ajaxed']);  
$id = checkIsset($_GET['id']); 
$name = checkIsset($_GET['name']); 
$display_id = checkIsset($_GET['display_id']); 
$total_rows = checkIsset($_GET['count']); 

if(!empty($id)){
	$sch_params[":id"] = makeSafe($id);
	$incr .= " AND md5(pa.fee_id) = :id";	
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
	'url' => 'view_fee_products_details.php?' . $query_string,
	'db_handle' => $pdo->dbh,
	'named_params' => $sch_params,
);

$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);
 
if ($popup_is_ajaxed) {
	try {

		$sel_sql="SELECT pm.id,pm.name,pm.product_code,pm.status,pct.title,pc.company_name,pm.product_type
		          FROM prd_assign_fees pa
		          JOIN prd_main pm on (pm.id=pa.product_id)
		          LEFT JOIN prd_company pc on (pc.id=pm.company_id)
		          LEFT JOIN prd_category pct on (pct.id=pm.category_id)
		          WHERE pm.is_deleted = 'N' AND pa.is_deleted = 'N' ". $incr ."
		          ORDER BY pm.name";
		     
		$paginate = new pagination($page, $sel_sql, $options);
		if ($paginate->success == true) {
			$fetch_rows = $paginate->resultset->fetchAll();
			$total_rows = count($fetch_rows);
		}

	} catch (paginationException $e) {
		echo $e;
		exit();
	}
	include_once 'tmpl/view_fee_products_details.inc.php';
	exit;
}

$template = 'view_fee_products_details.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>