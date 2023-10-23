<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$res_id =isset($_GET['res_id']) ? $_GET['res_id'] : '';
if(!empty($res_id)){
    $resource_res = $pdo->selectOne("SELECT md5(r.id) as id, display_id, name, type, count(DISTINCT (product_id)) as prd_total,GROUP_CONCAT(DISTINCT (product_id)) as products,status, r.created_at	FROM resources r LEFT JOIN res_products rp ON(rp.res_id=r.id) WHERE is_deleted='N' and md5(r.id)=:id",array(":id"=>$res_id));
    if(!empty($resource_res)) {
		$resource_name = $resource_res['name'];
	    $display_id = $resource_res['display_id'];

	    $description['ac_message'] =array(
	      'ac_red_1'=>array(
	        'href'=>$ADMIN_HOST.'/admin_profile.php?id='.$_SESSION['admin']['id'],
	        'title'=>$_SESSION['admin']['display_id'],
	      ),
	      'ac_message_1' =>' Read Resources',
	      'ac_red_2'=>array(
	          'href'=>$ADMIN_HOST.'/add_resources.php?resources_id='.md5($resource_res['id']),
	          'title'=>$resource_res['display_id'],
	      ),
	    ); 

	    activity_feed(3, $_SESSION['admin']['id'], 'Admin', $resource_res['id'], 'resource','Read Resources', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
	}
}
$sch_params = array();
$incr='';
$SortBy = "name";
$SortDirection = "DESC";
$currSortDirection = "ASC";
$popup_is_ajaxed = isset($_GET['popup_is_ajaxed']) ? $_GET['popup_is_ajaxed'] : '';
if ($popup_is_ajaxed) {

$has_querystring = false;
if (isset($_GET["sort_by"]) && $_GET["sort_by"] != "") {
	$has_querystring = true;
	$SortBy = $_GET["sort_by"];
}
$resource_id = !empty($_GET['product_id']) ? $_GET['product_id'] : '';
if($resource_id!=''){
    $incr.=" id IN($resource_id)";
}else{
    $incr.=" id IN(0)";
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
	'url' => 'resource_popup.php?' . $query_string,
	'db_handle' => $pdo->dbh,
	'named_params' => $sch_params,
);



$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);
 
	try {
        $sel_sql = "SELECT id,name,product_code,status FROM prd_main WHERE " . $incr . " AND is_deleted = 'N' AND status IN('Active','Suspended') ORDER BY $SortBy $currSortDirection";
		$paginate = new pagination($page, $sel_sql, $options);
		if ($paginate->success == true) {
			$fetch_rows = $paginate->resultset->fetchAll();
			$total_rows = count($fetch_rows);
		}

	} catch (paginationException $e) {
		echo $e;
		exit();
	}
	include_once 'tmpl/resource_popup.inc.php';
	exit;
}
$template = 'resource_popup.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>