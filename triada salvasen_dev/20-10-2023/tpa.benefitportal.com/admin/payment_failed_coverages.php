<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Payment';
$breadcrumbes[2]['title'] = 'Missed Plan Payments';
$breadcrumbes[2]['link'] = 'payment_failed_coverages.php';

$incr = ''; 
$sch_params=array();

$SortBy = "fc.created_at";
$SortDirection = "DESC";
$currSortDirection = "ASC";
$per_page = isset($_GET['pages']) ? $_GET['pages'] : 10;


$has_querystring = false;
if (isset($_GET["sort_by"]) && $_GET["sort_by"] != "") {
  $has_querystring = true;
  $SortBy = $_GET["sort_by"];
}

if (isset($_GET["sort_direction"]) && $_GET["sort_direction"] != "") {
  $has_querystring = true;
  $currSortDirection = $_GET["sort_direction"];
}

$is_ajaxed = checkIsset($_GET['is_ajaxed']);
$member_id = checkIsset($_GET['member_id']);
$policy_ids = checkIsset($_GET['policy_ids']);
$product_ids = checkIsset($_GET['product']);

if (!empty($member_id)) { 
  $incr.=" AND c.id IN($member_id)";
}

if (!empty($policy_ids)) {
    $incr.=" AND w.id IN($policy_ids)";
}

if (!empty($product_ids)) {  
  $product_ids = implode(',', $product_ids);
  $incr.=" AND (p.id IN($product_ids) OR p.parent_product_id IN($product_ids)) ";
}

$join_range = isset($_GET['join_range'])?$_GET['join_range']:"";
$fromdate = isset($_GET["fromdate"])?$_GET["fromdate"]:"";
$todate = isset($_GET["todate"])?$_GET["todate"]:"";
$added_date = isset($_GET["added_date"])?$_GET["added_date"]:"";

if($join_range != ""){
  if($join_range == "Range" && $fromdate!='' && $todate!=''){
    $sch_params[':fromdate'] = date("Y-m-d",strtotime($fromdate));
    $sch_params[':todate'] = date("Y-m-d",strtotime($todate));
    $incr.=" AND DATE(w.created_at) >= :fromdate AND DATE(w.created_at) <= :todate";
  }else if($join_range == "Exactly" && $added_date!=''){
    $sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
    $incr.=" AND DATE(w.created_at) = :added_date";
  }else if($join_range == "Before" && $added_date!=''){
    $sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
    $incr.=" AND DATE(w.created_at) < :added_date";
  }else if($join_range == "After" && $added_date!=''){
    $sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
    $incr.=" AND DATE(w.created_at) > :added_date";
  }
}

if (count($sch_params) > 0) {
  $has_querystring = true;
}
if (isset($_GET['pages']) && $_GET['pages'] > 0) {
  $has_querystring = true;
  $per_page = $_GET['pages'];
}

$query_string = $has_querystring ? (checkIsset($_GET['page']) && $_GET['page'] ? str_replace('page=' . $_GET['page'], "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';

$options = array(
    'results_per_page' => $per_page,
    'url' => 'payment_failed_coverages.php?' . $query_string,
    'db_handle' => $pdo->dbh,
    'named_params' => $sch_params
);

$page = (checkIsset($_GET['page']) && $_GET['page'] > 0) ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);

if($is_ajaxed){
  try {
    $sql="SELECT w.id,w.website_id,fc.created_at,w.customer_id,
        c.rep_id,CONCAT(c.fname,' ',c.lname) as member_name,
        p.product_code,p.name
        FROM payment_failed_coverages fc
        JOIN website_subscriptions w ON(w.id=fc.website_id)
        JOIN customer c ON (c.id = fc.customer_id)
        JOIN prd_main p on(w.product_id = p.id)
        WHERE fc.is_deleted='N' AND fc.is_paid='N'" . $incr . "
        GROUP BY w.id ORDER BY $SortBy $currSortDirection";
    $paginate = new pagination($page, $sql, $options);
    if ($paginate->success == true) {
      $fetch_rows = $paginate->resultset->fetchAll();
      $total_rows = count($fetch_rows);
    }
  } catch (paginationException $e) {
    echo $e;
    exit();
  }
 
  include_once 'tmpl/payment_failed_coverages.inc.php';
  exit;
}

$company_arr = get_active_global_products_for_filter();

$selectize = true;
$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache);
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache);

$template = 'payment_failed_coverages.inc.php';
include_once 'layout/end.inc.php';
?>