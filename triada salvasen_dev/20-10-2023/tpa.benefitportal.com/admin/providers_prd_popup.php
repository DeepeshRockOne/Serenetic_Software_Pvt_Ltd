<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$pros_id = isset($_GET['id']) ? $_GET['id'] : '';

if(!empty($pros_id)) {
	$providers_res = $pdo->selectOne("SELECT p.id, p.name, p.display_id, count(DISTINCT (sp.product_id)) as prd_total 
					FROM providers as p
					JOIN sub_provider as sp ON (p.id = sp.providers_id AND sp.is_deleted = 'N') 
					WHERE md5(p.id) = :providers_id ", array(":providers_id" => $pros_id));

	if(!empty($providers_res)) {
		$provider_name = $providers_res['name'];
	    $display_id = $providers_res['display_id'];

	    $description['ac_message'] =array(
	      'ac_red_1'=>array(
	        'href'=>$ADMIN_HOST.'/admin_profile.php?id='.$_SESSION['admin']['id'],
	        'title'=>$_SESSION['admin']['display_id'],
	      ),
	      'ac_message_1' =>' Read Provider ',
	      'ac_red_2'=>array(
	          'href'=>$ADMIN_HOST.'/add_providers.php?providers_id='.md5($providers_res['id']),
	          'title'=>$providers_res['display_id'],
	      ),
	    ); 

	    activity_feed(3, $_SESSION['admin']['id'], 'Admin', $providers_res['id'], 'provider','Read Provider', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
	}
}

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

$popup_is_ajaxed = isset($_GET['popup_is_ajaxed']) ? $_GET['popup_is_ajaxed'] : '';
$provider_id = isset($_GET['provider_id']) ? $_GET['provider_id'] : '';

if(!empty($provider_id)){
	$sch_params[":provider_id"] = makeSafe($provider_id);
	$incr .= " AND sp.providers_id = :provider_id";	
}

if (isset($_GET["sort_direction"]) && $_GET["sort_direction"] != "") {
	$has_querystring = true;
	$currSortDirection = $_GET["sort_direction"];
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
	'url' => 'providers_prd_popup.php?' . $query_string,
	'db_handle' => $pdo->dbh,
	'named_params' => $sch_params,
);

$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);
 
if ($popup_is_ajaxed) {
	try {
		$sel_sql = "SELECT sp.url, p.name, p.product_code, GROUP_CONCAT(sp.url) as url_str
					FROM sub_provider as sp
					JOIN prd_main as p ON (sp.product_id = p.id AND p.is_deleted='N')
					WHERE sp.is_deleted='N' " . $incr . " 
					GROUP BY p.id ORDER BY $SortBy $currSortDirection";

		$paginate = new pagination($page, $sel_sql, $options);
		if ($paginate->success == true) {
			$fetch_rows = $paginate->resultset->fetchAll();
			$total_rows = count($fetch_rows);
		}

	} catch (paginationException $e) {
		echo $e;
		exit();
	}
	include_once 'tmpl/providers_prd_popup.inc.php';
	exit;
}

$template = 'providers_prd_popup.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>