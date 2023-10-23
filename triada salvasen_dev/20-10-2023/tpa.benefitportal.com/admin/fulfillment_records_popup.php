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

$member_id = isset($_GET['member_id']) ? $_GET['member_id'] : "";

if($member_id){
	$incr .= " AND c.rep_id = :rep_id";
	$sch_params[':rep_id'] = $member_id;
}
if($file_id){
	$incr .= " AND ff.id = :id";
	$sch_params[':id'] = $file_id;
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
    'url' => 'fulfillment_records_popup.php?' . $query_string,
    'db_handle' => $pdo->dbh,
    'named_params' => $sch_params
);

$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);

if ($is_ajaxed) {
	
	try {
		
		$file_products = $pdo->selectOne("SELECT file_name,products FROM fulfillment_files WHERE id = :id",array(":id" => $file_id));
		$file_name = $file_products['file_name'];
		// $products = $file_products['products'];
  //       $sql = "SELECT pm.name,pm.product_code,pm.status
		// 		FROM prd_main pm
		// 		WHERE pm.id in($products) AND pm.is_deleted = 'N' GROUP BY pm.id";

		$sql = "SELECT c.rep_id,CONCAT(c.fname,' ',c.lname) as member_name,p.name as product_name,p.product_code 
					FROM fulfillment_files ff
					JOIN website_subscriptions w on(FIND_IN_SET(w.product_id,ff.products) AND w.status not in('Pending Declined','Pending Payment'))
					JOIN customer_enrollment ce on(ce.website_id = w.id AND ce.is_fulfillment = 'N')
					JOIN customer c on(c.id = w.customer_id AND c.status NOT IN('Pendind Validation','Customer Abandon','Pending'))
					JOIN prd_main p on(w.product_id = p.id)
					where ff.is_deleted = 'N' $incr GROUP by ce.id order by ff.file_name";


		$paginate = new pagination($page, $sql, $options);
	    if ($paginate->success == true) {
	        $membership_data = $paginate->resultset->fetchAll();
	        $total_rows = count($membership_data);
	        
	    }
	} catch (paginationException $e) {
		echo $e;
		exit();
	}
	include_once 'tmpl/fulfillment_records_popup.inc.php';
  	exit;
}

$template = 'fulfillment_records_popup.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>