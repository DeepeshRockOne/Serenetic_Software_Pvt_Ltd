<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$file_id = $_GET['id'];
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

$sch_params[':file_id'] = $file_id;

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
    'url' => 'billing_product.php?' . $query_string,
    'db_handle' => $pdo->dbh,
    'named_params' => $sch_params
);

$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);

if ($is_ajaxed) {
	
	try {
		$file_products = $pdo->selectOne("SELECT file_name FROM billing_files WHERE id = :id",array(":id" => $file_id));
		$file_name = $file_products['file_name'];
        $sql = "SELECT pm.name,pm.product_code,pm.status
				FROM billing_file_prd bfp
				JOIN prd_main pm ON(bfp.product_id=pm.id AND pm.is_deleted = 'N')
				WHERE bfp.file_id=:file_id AND bfp.is_deleted = 'N' GROUP BY pm.id
				ORDER BY pm.name ASC";

		$paginate = new pagination($page, $sql, $options);
	    if ($paginate->success == true) {
	        $membership_data = $paginate->resultset->fetchAll();
	        $total_rows = count($membership_data);
	        
	    }
	} catch (paginationException $e) {
		echo $e;
		exit();
	}
	include_once 'tmpl/billing_product.inc.php';
  	exit;
}

$template = 'billing_product.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>