<?php

include_once dirname(__FILE__) . '/layout/start.inc.php';
has_access(26);
$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';

if (in_array($_SESSION['admin']['type'], array('Super Admin', 'Development','Executive','Administrator','Support'))) {
  $breadcrumbes[1]['title'] = 'Chat History';
} else {
  $breadcrumbes[1]['title'] = 'Dashboard';
  $breadcrumbes[1]['link'] = 'support_dashboard.php';
  $breadcrumbes[2]['title'] = 'Chat History';
}

if (isset($_GET["viewall"])) {
  redirect('chat_history.php');
}

$sch_params = array();
$SortBy = "threadid";
$SortDirection = "ASC";
$currSortDirection = "DESC";

$has_querystring = false;
if ($_GET["sort"] != "") {
  $has_querystring = true;
  $SortBy = $_GET["sort"];
}

if ($_GET["direction"] != "") {
  $has_querystring = true;
  $currSortDirection = $_GET["direction"];
  if ($_GET["direction"] == "ASC") {
    $SortDirection = "DESC";
  } else {
    $SortDirection = "ASC";
  }
}

$chat_id = $_GET['chat_id'];
$user_type = $_GET['user_type'];
$name = $_GET['name'];
$operator = $_GET['operator'];
$ip_address = $_GET['ip_address'];

$fromdate = $_GET["fromdate"];
$todate = $_GET["todate"];
$custom_date = $_GET['custom_date'];

if ($chat_id != "") {
  $sch_params[':chat_id'] = makeSafe($chat_id);
  $incr.=" and display_id = :chat_id";
}

if($user_type != "") {
  $sch_params[':user_type'] = makeSafe($user_type);
  $incr.=" and user_type = :user_type"; 
}

if ($name != "") {
  $sch_params[':name'] = "%" . makeSafe($name) . "%";
  $incr.=" and userName like :name";
}

if ($operator != "") {
  $sch_params[':operator'] = makeSafe($operator);
  $incr.=" and agentId = :operator";
}

if ($ip_address != "") {
  $sch_params[':ip_address'] = makeSafe($ip_address) ;
  $incr.=" and remote = :ip_address";
}
switch ($custom_date) {
  case "Today":
    $fromdate = date('m/d/Y');
    $todate = date('m/d/Y');
    break;
  case "Yesterday":
    $fromdate = date("m/d/Y", strtotime("-1 days"));
    $todate = date('m/d/Y', strtotime("-1 days"));
    break;
  case "Last7Days":
    $fromdate = date('m/d/Y', strtotime("-7 day"));
    $todate = date('m/d/Y', strtotime("-1 day"));
    break;
  case "ThisMonth":
    $fromdate = date('m/01/Y');
    $todate = date('m/d/Y');
    break;
  case "LastMonth":
    $fromdate = date('m/d/Y', strtotime(date('Y-m') . " -1 month"));
    $todate = date('m/d/Y', strtotime(date('Y-m') . " last day of -1 month"));
    break;
  case "ThisYear":
    $fromdate = date('01/01/Y');
    $todate = date('m/d/Y');
    break;
}

if ($fromdate != "") {
  $sch_params[':fcreated_at'] = date('Y-m-d', strtotime($fromdate));
  $incr.=" AND DATE(dtmcreated) >= :fcreated_at";
}
if ($todate != "") {
  $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($todate));
  $incr.=" AND DATE(dtmcreated) <= :tcreated_at";
}

if (count($sch_params) > 0) {
  $has_querystring = true;
}

if (isset($_GET['pages']) && $_GET['pages'] > 0) {
  $per_page = $_GET['pages'];
  $has_querystring = true;
}

$query_string = $has_querystring ? ($_GET['page'] ? str_replace('page=' . $_GET['page'], "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';

$options = array(
    'results_per_page' => $per_page,
    'url' => 'chat_history.php?' . $query_string,
    'db_handle' => $pdo->dbh,
    'named_params' => $sch_params
);

$page = $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);

try {
	$state_queue = 0; $state_waiting = 1; $state_chatting = 2; $state_closed = 3; $state_loading = 4; $state_left = 5;
	$thread_to_be_include =  $state_closed . ',' . $state_left ;  
  $sel_sql = "SELECT threadid, display_id, customer_id, user_type, user_sub_type, userName, agentName, dtmcreated, userTyping, dtmmodified, lrevision, istate, remote, nextagent, agentId, userid, shownmessageid, userAgent, (select vclocalname from {$WEBIM_DB}.chatgroup where {$WEBIM_DB}.chatgroup.groupid = {$WEBIM_DB}.chatthread.groupid) as groupname " .
			 				"FROM {$WEBIM_DB}.chatthread " . 
			 				"WHERE istate in ($thread_to_be_include) " . $incr .
			 				" ORDER BY $SortBy $currSortDirection";
			 				
  $paginate = new pagination($page, $sel_sql, $options);
  if ($paginate->success == true) {
    $fetch_rows = $paginate->resultset->fetchAll();
    $total_rows = count($fetch_rows);
  }
} catch (paginationException $e) {
  echo $e;
  exit();
}

$operator_row = "SELECT operatorid,vclocalename FROM {$WEBIM_DB}.chatoperator ORDER BY vclocalename";
$operator_row = $pdo->select($operator_row);
foreach ($operator_row as $key => $value) {
	$operator_data[$value['operatorid']] = $value;
}

$template = 'chat_history.inc.php';
$page_title = 'Chat History';
include_once 'layout/end.inc.php';
?>