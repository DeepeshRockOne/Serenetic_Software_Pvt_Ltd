<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$page_title = "Open List Bills";

$sch_params = array();
$SortBy = "lb.id";
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
$search_list_bill_id = isset($_GET['search_list_bill_id'])?$_GET['search_list_bill_id']:'';
$search_group_id = isset($_GET['search_group_id'])?$_GET['search_group_id']:'';
$search_status = isset($_GET['search_status'])?$_GET['search_status']:'';

$search_list_bill_id = cleanSearchKeyword($search_list_bill_id);
$search_group_id = cleanSearchKeyword($search_group_id); 
 
if (!empty($search_list_bill_id)) {
  $sch_params[':search_list_bill_id'] = "%" . makeSafe($search_list_bill_id) . "%";
  $incr .= " AND (lb.list_bill_no LIKE :search_list_bill_id)";
}
if (!empty($search_group_id)) {
  $sch_params[':search_group_id'] = "%" . makeSafe($search_group_id) . "%";
  $incr .= " AND (g.rep_id LIKE :search_group_id)";
}
if (!empty($search_status)) {
  $sch_params[':search_status'] = "%" . makeSafe($search_status) . "%";
  $incr .= " AND (lb.status LIKE :search_status)";
}


if (count($sch_params) > 0) {
  $has_querystring = true;
}
$per_page=25;
if (isset($_GET['pages']) && $_GET['pages'] > 0) {
  $has_querystring = true;
  $per_page = $_GET['pages'];
}
$query_string = $has_querystring ? (isset($_GET['page']) && $_GET['page'] ? str_replace('page=' . $_GET['page'], "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';

$options = array(
    'results_per_page' => $per_page,
    'url' => 'open_list_bills.php?is_ajaxed=1&' . $query_string,
    'db_handle' => $pdo->dbh,
    'named_params' => $sch_params
);

$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);

if ($is_ajaxed) {
  try {
    $sel_sql = "SELECT lb.*,g.rep_id,g.business_name,gc.name as group_company_name,md5(lb.id) as secured
             FROM list_bills lb
             JOIN customer g ON (g.id = lb.customer_id AND g.type='Group')
             LEFT JOIN group_company as gc ON (gc.group_id = lb.customer_id AND lb.company_id = gc.id)
             WHERE lb.is_deleted='N' AND lb.status='open' " . $incr . " GROUP BY lb.id ORDER BY  $SortBy $currSortDirection";
    $paginate = new pagination($page, $sel_sql, $options);
    if ($paginate->success == true) {
      $fetch_rows = $paginate->resultset->fetchAll();
      $total_rows = count($fetch_rows);
     
    }
  } catch (paginationException $e) {
    echo $e;
    exit();
  }
  include_once 'tmpl/open_list_bills.inc.php';
  exit;
}
?>