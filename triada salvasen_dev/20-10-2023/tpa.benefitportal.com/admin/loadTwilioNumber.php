<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$sch_params = array();
$SortBy = "tn.id";
$SortDirection = "ASC";
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

$sel_params = array();
$incr = "";
$is_ajaxed = isset($_GET['is_ajaxed'])?$_GET['is_ajaxed']:'';

if (count($sch_params) > 0) {
  $has_querystring = true;
}
$per_page=5;
if (isset($_GET['pages']) && $_GET['pages'] > 0) {
  $has_querystring = true;
  $per_page = $_GET['pages'];
}
$query_string = $has_querystring ? (isset($_GET['page']) && $_GET['page'] ? str_replace('page=' . $_GET['page'], "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';

$options = array(
    'results_per_page' => $per_page,
    'url' => 'loadTwilioNumber.php?is_ajaxed=1&' . $query_string,
    'db_handle' => $pdo->dbh,
    'named_params' => $sch_params
);

$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);

if ($is_ajaxed) {
  try {
    $sel_sql = "SELECT md5(tn.id) as id,tn.TwilioNumber,tn.is_active,tn.service
             FROM twilio_numbers tn
             WHERE tn.is_deleted='N' " . $incr . " ORDER BY  $SortBy $currSortDirection";
    $paginate = new pagination($page, $sel_sql, $options);
    if ($paginate->success == true) {
      $fetch_rows = $paginate->resultset->fetchAll();
      $total_rows = count($fetch_rows);
     
    }
  } catch (paginationException $e) {
    echo $e;
    exit();
  }
  include_once 'tmpl/loadTwilioNumber.inc.php';
  exit;
}
?>