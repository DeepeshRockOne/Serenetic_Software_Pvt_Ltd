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

$is_history_ajaxed = isset($_GET['is_history_ajaxed']) ? $_GET['is_history_ajaxed'] : '';
$search_broadcaster_id = isset($_GET['search_broadcaster_id']) ? $_GET['search_broadcaster_id'] : '';
$search_broadcaster_name = isset($_GET['search_broadcaster_name']) ? $_GET['search_broadcaster_name'] : '';
$join_range = isset($_GET['join_range']) ? $_GET['join_range']:"";
$added_date = isset($_GET["added_date"]) ? $_GET["added_date"]:"";
$fromdate = isset($_GET["fromdate"]) ? $_GET["fromdate"]:"";
$todate = isset($_GET["todate"]) ? $_GET["todate"]:"";

$incr = "";

$search_broadcaster_id = cleanSearchKeyword($search_broadcaster_id);
$search_broadcaster_name = cleanSearchKeyword($search_broadcaster_name); 
 
if(!empty($search_broadcaster_id)){
	$sch_params[":search_broadcaster_id"] = $search_broadcaster_id;
	$incr.=" AND b.display_id = :search_broadcaster_id";
}

if(!empty($search_broadcaster_name)){
	$sch_params[":search_broadcaster_name"] = "%" . makeSafe($search_broadcaster_name) . "%";
	$incr.=" AND b.brodcast_name LIKE :search_broadcaster_name";
}

if(!empty($join_range)){
  if($join_range == "Range" && $fromdate!='' && $todate!=''){
    $sch_params[':fromdate'] = date("Y-m-d",strtotime($fromdate));
    $sch_params[':todate'] = date("Y-m-d",strtotime($todate));
    $incr.=" AND DATE(b.created_at) >= :fromdate AND DATE(b.created_at) <= :todate";
  }else if($join_range == "Exactly" && $added_date!=''){
    $sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
    $incr.=" AND DATE(b.created_at) = :added_date";
  }else if($join_range == "Before" && $added_date!=''){
    $sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
    $incr.=" AND DATE(b.created_at) < :added_date";
  }else if($join_range == "After" && $added_date!=''){
    $sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
    $incr.=" AND DATE(b.created_at) > :added_date";
  }
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
	'url' => 'sms_broadcaster_history_page.php?' . $query_string,
	'db_handle' => $pdo->dbh,
	'named_params' => $sch_params,
);

$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);

if ($is_history_ajaxed) {
	try {
		
		$sel_sql = "SELECT b.id, b.brodcast_name, b.status, b.display_id, b.created_at, CONCAT(a.fname, ' ',a.lname) as admin_name, a.display_id as admin_display_id, a.id as admin_id, b.total_users, b.total_sent
				FROM broadcaster as b 
				JOIN broadcaster_schedule_settings as bss ON (bss.broadcaster_id = b.id AND bss.is_deleted = 'N') 
				JOIN admin as a ON (b.sender_id = a.id)
				WHERE b.sender_type='Admin' AND b.type='sms' AND b.status IN ('Completed','Cancelled') AND b.is_deleted = 'N' " . $incr . "
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

	include_once 'tmpl/sms_broadcaster_history_page.inc.php';
	exit;
}
$exStylesheets = array("thirdparty/jquery_ui/css/front/jquery-ui-1.9.2.custom.css", 'thirdparty/multiple-select-master/multiple-select.css', 'thirdparty/sweetalert/sweetalert.css');
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js', 'thirdparty/clipboard/clipboard.min.js');

$template = 'sms_broadcaster_history_page.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php'; ?>