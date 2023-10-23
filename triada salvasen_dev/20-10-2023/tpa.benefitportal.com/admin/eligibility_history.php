<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Eligibility";
$breadcrumbes[2]['title'] = "History";
$breadcrumbes[2]['link'] = 'eligibility_history.php';

$sch_params = array();
$SortBy = "ef.created_at";
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
	'url' => 'eligibility_history.php?' . $query_string,
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
	  'ac_message_1' =>' read history of eligibility files ',
	  'ac_red_2'=>array(
	    'title'=> "",
	  ),
	); 
	activity_feed(3, $_SESSION['admin']['id'], 'Admin', 0, 'eligibility_files','Admin read history of eligibility files', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
}

if ($is_ajaxed) {
	try {
		
		$sel_sql = "SELECT ef.*,count(eh.id) as total_files 
		FROM eligibility_files ef
		LEFT JOIN eligibility_requests er on(ef.id = er.file_id) 
		LEFT JOIN eligibility_history eh on(eh.req_id = er.id and eh.service_group_id = ef.id AND eh.status = 'Processed' AND eh.is_deleted = 'N')
		where ef.is_deleted = 'N'  GROUP by ef.id order by ef.file_name";
		$paginate = new pagination($page, $sel_sql, $options);
		if ($paginate->success == true) {
			$fetch_rows = $paginate->resultset->fetchAll();
			$total_rows = count($fetch_rows);
		}
	} catch (paginationException $e) {
		echo $e;
		exit();
	}

	include_once 'tmpl/eligibility_history.inc.php';
	exit;
}

$template = 'eligibility_history.inc.php';
include_once 'layout/end.inc.php';
?>
