<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Fulfillment";
$breadcrumbes[2]['title'] = "Generator";
$breadcrumbes[2]['link'] = 'fulfillment_generator.php';

$tz = new UserTimeZone('m/d/Y g:i A T',$_SESSION['admin']['timezone']);
$current_timzone = explode(' ', $tz->getDate(date('m/d/Y g:i A T')));
$current_timzone = end($current_timzone);

$sch_params = array();
$SortBy = "b.created_at";
$SortDirection = "DESC";
$currSortDirection = "ASC";

$productRes = get_active_global_products_for_filter();

$has_querystring = false;
if (isset($_GET["sort_by"]) && $_GET["sort_by"] != "") {
	$has_querystring = true;
	$SortBy = $_GET["sort_by"];
}

if (isset($_GET["sort_direction"]) && $_GET["sort_direction"] != "") {
	$has_querystring = true;
	$currSortDirection = $_GET["sort_direction"];
}

$is_ajaxed = isset($_GET['is_ajaxed']) ? $_GET['is_ajaxed'] : '';

$incr = "";

$products = isset($_GET['products']) ? $_GET['products'] : array();
$file_name = isset($_GET['file_name']) ? $_GET['file_name'] : "";

$file_name = cleanSearchKeyword($file_name); 
 
if($products){
	foreach($products as $key => $value){
		$incr .= " AND FIND_IN_SET($value , ff.products)";
	}
}
if($file_name){
	$incr .= " AND ff.file_name LIKE :file_name";
	$sch_params[':file_name'] = '%' . makeSafe($file_name) . '%';
}


if (count($sch_params) > 0) {
	$has_querystring = true;
}

if (isset($_GET['pages']) && $_GET['pages'] > 0) {
	$has_querystring = true;
	$per_page = $_GET['pages'];
}

$page = isset($_GET['page']) ? $_GET['page'] : '';
$query_string = $has_querystring ? ($page ? str_replace('page=' . $page, "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';

$options = array(
	'results_per_page' => $per_page,
	'url' => 'fulfillment_generator.php?' . $query_string,
	'db_handle' => $pdo->dbh,
	'named_params' => $sch_params,
);

$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);

if ($is_ajaxed) {
	try {
		
		$sel_sql = "SELECT ff.* 
		FROM fulfillment_files ff
		where ff.is_deleted = 'N' $incr GROUP by ff.id order by ff.file_name";
		$paginate = new pagination($page, $sel_sql, $options);
		if ($paginate->success == true) {
			$fetch_rows = $paginate->resultset->fetchAll();
			$total_rows = count($fetch_rows);
		}
	} catch (paginationException $e) {
		echo $e;
		exit();
	}

	include_once 'tmpl/fulfillment_generator.inc.php';
	exit;
}



$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache);
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache);

$template = 'fulfillment_generator.inc.php';
include_once 'layout/end.inc.php';
?>
