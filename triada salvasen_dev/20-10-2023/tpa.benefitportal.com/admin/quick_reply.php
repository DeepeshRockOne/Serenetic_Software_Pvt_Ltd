<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
has_access(26);

$type = $_GET['type'];

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Support Dashboard';
$breadcrumbes[1]['link'] = 'support_dashboard.php';
$breadcrumbes[2]['title'] = $type . ' Quick Reply';
$breadcrumbes[2]['class'] = 'Active';

$page_title = 'Quick Reply';


$count_sql = "SELECT COUNT(id) as `total` FROM s_canned_messages WHERE type='$type'";
$row = $pdo->selectOne($count_sql);
$total = $row['total'];

$sch_params = array();
$SortBy = "id";
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

$title = $_GET["title"];
$fromdate = $_GET["fromdate"];
$todate = $_GET["todate"];
$custom_date = $_GET['custom_date'];

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

if ($type != "") {
  $sch_params[':type'] = makeSafe($type);
  $incr.=" AND type=:type";
}
if ($title != "") {
  $sch_params[':title'] = "%" . makeSafe($title) . "%";
  $incr.=" AND title LIKE :title";
}

if ($fromdate != "") {
  $sch_params[':fcreated_at'] = date('Y-m-d', strtotime($fromdate));
  $incr.=" AND DATE(created_at) >= :fcreated_at";
}

if ($todate != "") {
  $sch_params[':tcreated_at'] = date('Y-m-d', strtotime($todate));
  $incr.=" AND DATE(created_at) <= :tcreated_at";
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
    'url' => 'quick_reply.php?' . $query_string,
    'db_handle' => $pdo->dbh,
    'named_params' => $sch_params
);

$page = $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);

try {
  $sel_sql = "SELECT * FROM `s_canned_messages` WHERE id > 0 " . $incr . " ORDER BY $SortBy $currSortDirection";
  $paginate = new pagination($page, $sel_sql, $options);
  if ($paginate->success == true) {
    $fetch_rows = $paginate->resultset->fetchAll();
    $total_rows = count($fetch_rows);
  }
} catch (paginationException $e) {
  echo $e;
  exit();
}
$exStylesheets = array('thirdparty/colorbox/colorbox.css');
$exJs = array('thirdparty/colorbox/jquery.colorbox.js');
$template = 'quick_reply.inc.php';
include_once 'layout/end.inc.php';
?>