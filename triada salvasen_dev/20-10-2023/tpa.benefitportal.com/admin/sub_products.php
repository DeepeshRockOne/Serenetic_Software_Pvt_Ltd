<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$page_title = "Product Catrgories";

$sch_params = array();
$SortBy = "sp.id";
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
$is_ajaxed = $_GET['is_ajaxed'];
$search_val = isset($_GET['search_val'])?$_GET['search_val']:'';

$search_val = cleanSearchKeyword($search_val); 
 
if ($search_val != "") {
  $sch_params[':search_val'] = "%" . makeSafe($search_val) . "%";
  $incr .= " AND (sp.product_code LIKE :search_val OR sp.product_name LIKE :search_val OR pc.name LIKE :search_val) ";
}

if (count($sch_params) > 0) {
  $has_querystring = true;
}
$per_page=5;
if (isset($_GET['pages']) && $_GET['pages'] > 0) {
  $has_querystring = true;
  $per_page = $_GET['pages'];
}
$query_string = $has_querystring ? (isset($_GET["page"]) && $_GET['page'] ? str_replace('page=' . $_GET['page'], "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';

$options = array(
    'results_per_page' => $per_page,
    'url' => 'sub_products.php?is_ajaxed=1&' . $query_string,
    'db_handle' => $pdo->dbh,
    'named_params' => $sch_params
);

$page = isset($_GET["page"]) && $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);

if ($is_ajaxed) {
  try {
    $sel_sql = "SELECT sp.*,md5(sp.id) as id,pc.name as carrier_name,pc.display_id
                FROM sub_products sp
                JOIN prd_fees pc ON(pc.id = sp.carrier_id AND pc.setting_type='Carrier')
                WHERE sp.is_deleted='N' " . $incr . " 
                GROUP BY sp.id 
                ORDER BY  $SortBy $currSortDirection";
    $paginate = new pagination($page, $sel_sql, $options);
    if ($paginate->success == true) {
      $fetch_rows = $paginate->resultset->fetchAll();
      $total_rows = count($fetch_rows);
     
    }
  } catch (paginationException $e) {
    echo $e;
    exit();
  }
  include_once 'tmpl/sub_products.inc.php';
  exit;
}
?>