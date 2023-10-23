<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Fulfillment";
$breadcrumbes[2]['title'] = "History";
$breadcrumbes[2]['link'] = 'fulfillment_history.php';

$sch_params = array();
$SortBy = "ff.created_at";
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
	'url' => 'fulfillment_history.php?' . $query_string,
	'db_handle' => $pdo->dbh,
	'named_params' => $sch_params,
);

$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);

if(!$is_ajaxed){
	$description['ac_message'] =array(
	  'ac_red_1'=>array(
	    'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
	    'title'=>$_SESSION['admin']['display_id'],
	  ),
	  'ac_message_1' =>' read history of fulfillment files ',
	  'ac_red_2'=>array(
	    'title'=> "",
	  ),
	); 
	activity_feed(3, $_SESSION['admin']['id'], 'Admin', 0, 'fulfillment_files','Admin read history of fulfillment files', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
}

if ($is_ajaxed) {
	try {
		
		$sel_sql = "SELECT ff.*,count(fh.id) as total_files 
		FROM fulfillment_files ff
		LEFT JOIN fulfillment_requests fr on(ff.id = fr.file_id) 
		LEFT JOIN fulfillment_history fh on(fh.req_id = fr.id and fh.service_group_id = ff.id AND fh.status = 'Processed' AND fh.is_deleted = 'N')
		where ff.is_deleted = 'N'  GROUP by ff.id order by ff.file_name";
		$paginate = new pagination($page, $sel_sql, $options);
		if ($paginate->success == true) {
			$fetch_rows = $paginate->resultset->fetchAll();
			$total_rows = count($fetch_rows);
		}
	} catch (paginationException $e) {
		echo $e;
		exit();
	}

	include_once 'tmpl/fulfillment_history.inc.php';
	exit;
}

$template = 'fulfillment_history.inc.php';
include_once 'layout/end.inc.php';
?>
