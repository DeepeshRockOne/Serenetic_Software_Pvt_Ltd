<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Payment";
$breadcrumbes[2]['title'] = "Billing Files";
$breadcrumbes[2]['link'] = 'billing_files.php';

$sch_params = array();
$SortBy = "bf.created_at";
$SortDirection = "DESC";
$currSortDirection = "ASC";

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
$products_join = "";

$products = isset($_GET['products']) ? $_GET['products'] : array();
$file_name = isset($_GET['file_name']) ? $_GET['file_name'] : "";

$file_name = cleanSearchKeyword($file_name); 
 
if($products){
	$products_join = "JOIN billing_file_prd bfp ON(bfp.file_id = bf.id AND bfp.is_deleted = 'N')
		JOIN prd_main pm ON(bfp.product_id=pm.id AND pm.is_deleted = 'N')";
	$incr .= " AND (pm.id IN('".implode("','", $products)."') OR pm.parent_product_id IN('".implode("','", $products)."'))";
}
if($file_name){
	$incr .= " AND bf.file_name LIKE :file_name";
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
	'url' => 'billing_files.php?' . $query_string,
	'db_handle' => $pdo->dbh,
	'named_params' => $sch_params,
);

$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);

if ($is_ajaxed) {
	try {
		
		$sel_sql = "SELECT bf.*,count(DISTINCT bh.id) as total_files,bfp2.products
		FROM billing_files bf 
		JOIN (
			SELECT COUNT(DISTINCT bfp2.product_id) as products,bfp2.file_id
			FROM billing_file_prd bfp2
			WHERE bfp2.is_deleted = 'N'
			GROUP BY bfp2.file_id
		) bfp2 ON (bfp2.file_id = bf.id)
		$products_join
		LEFT JOIN billing_history bh on(bh.service_group_id = bf.id AND bh.status = 'Processed' AND bh.is_deleted = 'N')
		WHERE bf.is_deleted = 'N' $incr 
		GROUP BY bf.id 
		ORDER BY bf.file_name";
        $paginate = new pagination($page, $sel_sql, $options);
		if ($paginate->success == true) {
			$fetch_rows = $paginate->resultset->fetchAll();
			$total_rows = count($fetch_rows);
		}
	} catch (paginationException $e) {
		echo $e;
		exit();
	}

	include_once 'tmpl/billing_files.inc.php';
	exit;
}

$company_arr = get_active_global_products_for_filter();

$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache);
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache);

$template = 'billing_files.inc.php';
include_once 'layout/end.inc.php';
?>
