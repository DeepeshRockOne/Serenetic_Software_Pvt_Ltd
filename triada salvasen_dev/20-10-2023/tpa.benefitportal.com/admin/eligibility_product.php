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
    'url' => 'eligibility_product.php?' . $query_string,
    'db_handle' => $pdo->dbh,
    'named_params' => $sch_params
);

$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);

if ($is_ajaxed) {
	
	try {
		
		$file_products = $pdo->selectOne("SELECT file_name,products,product_type FROM eligibility_files WHERE id = :id",array(":id" => $file_id));
		$file_name = $file_products['file_name'];
		$products = $file_products['products'];
		$product_type = $file_products['product_type'];
	
		if($product_type == "Participants Products"){
			$productsArr = explode(",", $products);
			$sql = "SELECT pp.product_code as name,pp.product_code,'Active' as status
				FROM participants_products pp
				WHERE pp.product_code in('".implode("','",$productsArr)."') AND pp.is_deleted = 'N' GROUP BY pp.product_code ORDER BY pp.product_code ASC";
		}else{
        	$sql = "SELECT pm.name,pm.product_code,pm.status
				FROM prd_main pm
				WHERE pm.id in($products) AND pm.is_deleted = 'N' GROUP BY pm.id ORDER BY pm.name ASC";
		}

		$paginate = new pagination($page, $sql, $options);
	    if ($paginate->success == true) {
	        $membership_data = $paginate->resultset->fetchAll();
	        $total_rows = count($membership_data);
	        // pre_print($paginate->links_html);
	        
	    }
	} catch (paginationException $e) {
		echo $e;
		exit();
	}
	include_once 'tmpl/eligibility_product.inc.php';
  	exit;
}

$template = 'eligibility_product.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>