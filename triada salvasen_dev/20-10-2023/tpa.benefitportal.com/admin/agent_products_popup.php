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
$name = checkIsset($_GET['name']); 
$display_id = checkIsset($_GET['display_id']); 
$total_rows = checkIsset($_GET['count']); 
$sch_params['id'] = $id;

$agent_data = $pdo->selectOne("SELECT fname,lname,rep_id,type,business_name FROM customer where md5(id) = :id",array(":id" => $id));

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
	'url' => 'agent_products_popup.php?' . $query_string,
	'db_handle' => $pdo->dbh,
	'named_params' => $sch_params,
);

$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);
if ($popup_is_ajaxed) {
	try {
			$sel_sql="SELECT p.name,p.product_code,apr.status,p.product_type
	          	FROM prd_main p
	          	JOIN agent_product_rule apr on(apr.product_id = p.id)   
	          	WHERE md5(apr.agent_id) = :id and apr.is_deleted = 'N' and p.is_deleted='N' and p.product_type != 'Healthy Step'
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
	include_once 'tmpl/agent_products_popup.inc.php';
	exit;
}

$template = 'agent_products_popup.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>