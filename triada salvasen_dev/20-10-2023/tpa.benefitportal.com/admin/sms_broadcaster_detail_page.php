<?php

include_once dirname(__FILE__) . '/layout/start.inc.php';

$sch_params = array();
$SortBy = "b.created_at";
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
	'url' => 'sms_broadcaster_detail_page.php?' . $query_string,
	'db_handle' => $pdo->dbh,
	'named_params' => $sch_params,
);

$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);

if ($is_ajaxed) {
	try {
		
		$sel_sql = "SELECT b.id, b.brodcast_name, b.status, b.display_id, b.created_at, CONCAT(a.fname, ' ',a.lname) as admin_name, a.display_id as admin_display_id, a.id as admin_id, b.total_sent, GROUP_CONCAT(CONCAT(bss.schedule_date, ' ', bss.schedule_hour, ':00 ')) as schedule_details,bss.time_zone as schedule_time_zone
					FROM broadcaster as b 
					JOIN broadcaster_schedule_settings as bss ON (bss.broadcaster_id = b.id AND bss.is_deleted = 'N') 
					JOIN admin as a ON (b.sender_id = a.id)
					WHERE b.sender_type='Admin' AND b.status NOT IN ('Completed','Cancelled') AND b.type='sms' AND b.is_deleted = 'N' " . $incr . "
	         		GROUP BY b.id ORDER BY $SortBy $currSortDirection";
		$paginate = new pagination($page, $sel_sql, $options);
		if ($paginate->success == true) {
			$fetch_rows = $paginate->resultset->fetchAll();
			$total_rows = count($fetch_rows);
		}
	} catch (paginationException $e) {
		echo $e;
		exit();
	}

	include_once 'tmpl/sms_broadcaster_detail_page.inc.php';
	exit;
}
$exStylesheets = array("thirdparty/jquery_ui/css/front/jquery-ui-1.9.2.custom.css", 'thirdparty/multiple-select-master/multiple-select.css', 'thirdparty/sweetalert/sweetalert.css');
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js', 'thirdparty/clipboard/clipboard.min.js');

$template = 'sms_broadcaster_detail_page.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php'; 
?>