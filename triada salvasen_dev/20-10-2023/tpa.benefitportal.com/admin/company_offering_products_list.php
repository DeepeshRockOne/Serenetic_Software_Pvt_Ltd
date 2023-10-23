<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$page_title = "Product List";
$id=checkIsset($_GET['id']);
$name=checkIsset($_GET['name']);
 

$sch_params = array();
$SortBy = "p.name";
$SortDirection = "ASC";
$currSortDirection = "ASC";
$has_querystring = false;


$sel_params = array();
$incr = "";
if(isset($id)){
  $sch_params[':company_id']=$id;
  $incr.=" AND md5(p.company_id) = :company_id";
}

if (count($sch_params) > 0) {
  $has_querystring = true;
}
//$per_page=1;
if (isset($_GET['pages']) && $_GET['pages'] > 0) {
  $has_querystring = true;
  $per_page = $_GET['pages'];
}
$query_string = $has_querystring ? (isset($_GET['page']) && $_GET['page'] ? str_replace('page=' . $_GET['page'], "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';

$options = array(
    'results_per_page' => $per_page,
    'url' => 'company_offering_products_list.php?' . $query_string,
    'db_handle' => $pdo->dbh,
    'named_params' => $sch_params
);

$page = (isset($_GET['page']) && $_GET['page']) > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);


try {
  $sel_sql = "SELECT p.id,p.name,p.product_code,p.status 
              FROM prd_main p 
              WHERE p.is_deleted='N' AND p.type !='Fees' " . $incr . " 
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
  

$template = 'company_offering_products_list.inc.php';
$layout="iframe.layout.php";
include_once 'layout/end.inc.php';
?>