<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$page_title = "Pay Option Variations";

$sch_params = array();
$SortBy = "c.id";
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
$search_val = isset($_GET['search_val'])?$_GET['search_val']:'';

$search_val = cleanSearchKeyword($search_val); 
 
if ($search_val != "") {
  $sch_params[':search_val'] = "%" . makeSafe($search_val) . "%";
  $incr .= " AND (c.rep_id LIKE :search_val OR c.fname LIKE :search_val OR c.lname LIKE :search_val OR c.business_name LIKE :search_val)";
}

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
    'url' => 'pay_options_variations.php?is_ajaxed=1&' . $query_string,
    'db_handle' => $pdo->dbh,
    'named_params' => $sch_params
);

$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);

if ($is_ajaxed) {
  try {
    $sel_sql = "SELECT gpo.id,gpo.created_at as added_date,gpo.group_id,c.rep_id,c.fname,c.lname,c.business_name,gpo.is_cc,gpo.is_check,gpo.is_ach
             FROM group_pay_options gpo
             JOIN customer c ON (c.id = gpo.group_id AND c.type='Group')
             WHERE gpo.is_deleted='N' AND gpo.rule_type='Variation' " . $incr . " GROUP BY gpo.id ORDER BY  $SortBy $currSortDirection";
    $paginate = new pagination($page, $sel_sql, $options);
    if ($paginate->success == true) {
      $fetch_rows = $paginate->resultset->fetchAll();
      $total_rows = count($fetch_rows);
     
    }
  } catch (paginationException $e) {
    echo $e;
    exit();
  }
  include_once 'tmpl/pay_options_variations.inc.php';
  exit;
}
?>