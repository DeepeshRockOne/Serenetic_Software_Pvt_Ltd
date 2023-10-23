<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
has_access(54);

$breadcrumbes[0]['title'] = '<i class="icon-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Carriers';
$page_title = "Carriers";
 
$sch_params=array();
$incr=''; 
$SortBy = "pf.created_at";
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

//fees Code
$feeCodeSql = "SELECT GROUP_CONCAT(DISTINCT  pf.display_id ORDER BY pf.id DESC)  as display_id 
            FROM prd_fees pf 
            WHERE  pf.setting_type='Carrier' AND pf.is_deleted='N' ";
$feeCodeRes = $pdo->selectOne($feeCodeSql);
$feeResArr = array(); 
if(!empty($feeCodeRes)){
  $feeResArr = explode(",", $feeCodeRes['display_id']); 
}   

$is_ajaxed = checkIsset($_GET['is_ajaxed']); 
$carrier_id = isset($_GET["carrier_id"])?$_GET["carrier_id"]:"";
$products = isset($_GET["products"])?$_GET["products"]:array();
$carrier_name = checkIsset($_GET['carrier_name']);
$contact_name = checkIsset($_GET['contact_name']);
$products = checkIsset($_GET['products']);
$member_id = checkIsset($_GET['member_id']);
$status = checkIsset($_GET['status']);

$join_range = isset($_GET['join_range'])?$_GET['join_range']:"";
$fromdate = isset($_GET["fromdate"])?$_GET["fromdate"]:"";
$todate = isset($_GET["todate"])?$_GET["todate"]:"";
$added_date = isset($_GET["added_date"])?$_GET["added_date"]:"";

$extra_export_arr = array();
 
$carrier_id= cleanSearchKeyword($carrier_id);
$member_id = cleanSearchKeyword($member_id);
$contact_name = cleanSearchKeyword($contact_name);
$carrier_name = cleanSearchKeyword($carrier_name); 
 
if (!empty($carrier_id)) {
  $carrier_ids = str_replace(" ", "", $carrier_id);
  $carrier_ids = explode(',', $carrier_ids);
  $carrier_ids = "'" . implode("','", $carrier_ids) . "'";
  $incr .= " AND pf.display_id IN ($carrier_ids)";
}

$extra_export_arr['incr'] =" AND pf.setting_type='Carrier' ";
if ($member_id != "") { 
  $sch_params[':member_id'] = "%" .$member_id . "%";
  $incr.=" AND c.rep_id like :member_id";
  $extra_export_arr['tbl_join'] = "
  LEFT JOIN website_subscriptions w ON (paf.product_id = w.product_id AND DATE(w.eligibility_date) < CURDATE())
  LEFT JOIN customer c ON (w.customer_id = c.id AND c.status='Active')";
}

if (!empty($products)) {
  $products_ids = "'" . implode("','", $products) . "'";
  $incr .= " AND (p.id IN ($products_ids) OR p.parent_product_id IN($products_ids)) ";
} 

if ($carrier_name != "") {
  $sch_params[':carrier_name'] = "%" . makeSafe($carrier_name) . "%";
  $incr.=" AND pf.name LIKE :carrier_name";
}

if ($contact_name != "") {
  $sch_params[':contact_name'] = "%" . makeSafe($contact_name) . "%";
  $incr.=" AND (pf.contact_fname LIKE :contact_name OR pf.contact_lname LIKE :contact_name OR CONCAT(pf.contact_fname,' ',pf.contact_lname) LIKE :contact_name OR CONCAT(pf.contact_lname,' ',pf.contact_fname) LIKE :contact_name )";
}

if ($status != "") {
  $sch_params[':status'] = makeSafe($status);
  $incr.=" AND pf.status = :status";
}

$getfromdate = '';
$gettodate = '';
if($join_range != ""){
  if($join_range == "Range" && $fromdate!='' && $todate!=''){
    $sch_params[':fromdate'] = date("Y-m-d",strtotime($fromdate));
    $sch_params[':todate'] = date("Y-m-d",strtotime($todate));
    $incr.=" AND DATE(pf.created_at) >= :fromdate AND DATE(pf.created_at) <= :todate";
    $getfromdate = $fromdate;
    $gettodate = $todate;
  }else if($join_range == "Exactly" && $added_date!=''){
    $sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
    $incr.=" AND DATE(pf.created_at) = :added_date";
    $getfromdate = $added_date;
    $gettodate = $added_date;
  }else if($join_range == "Before" && $added_date!=''){
    $sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
    $incr.=" AND DATE(pf.created_at) < :added_date";
    $getfromdate = $added_date;
    $gettodate = date('Y-m-d');
  }else if($join_range == "After" && $added_date!=''){
    $sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
    $incr.=" AND DATE(pf.created_at) > :added_date";
    $getfromdate = date('Y-m-d');
    $gettodate = $added_date;
  }
}

$export_val = isset($_GET['export_val']) ? $_GET["export_val"] : '';

if(!empty($export_val)){

  $extra_export_arr['setting_type'] = 'Carrier';
  include_once dirname(__DIR__) . '/includes/aws_reporting_api_url.php';

  if($getfromdate!='' && $gettodate != '') {

    $no_days=0;
    if($getfromdate!= '' && $gettodate!='') {
      $date1 = date_create($getfromdate);
      $date2 = date_create($gettodate);
      $diff = date_diff($date1,$date2);
      $no_days=$diff->format("%a");
    }
    
    if($no_days>62) {
      echo json_encode(array("status"=>"fail","message"=>"Please enter proper date range. A maximum date range of 60 days is allowed per request."));
      exit();
    }
  }
  $job_id=add_export_request_api('EXCEL',$_SESSION['admin']['id'],'Admin',"Carrier Overview","carrier_overview",$incr, $sch_params,$extra_export_arr,'carrier_overview');
  echo json_encode(array("status"=>"success","message"=>"Your export request is added")); 
  exit;
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
    'url' => 'carrier.php?' . $query_string,
    'db_handle' => $pdo->dbh,
    'named_params' => $sch_params
);

$page = (checkIsset($_GET['page']) && $_GET['page'] > 0) ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);

if($is_ajaxed){
  try {
      
    $sql="SELECT pf.*,md5(pf.id) as id,IF(w.eligibility_date < CURDATE(),
      GROUP_CONCAT(
                  DISTINCT
                  CASE
                  WHEN c.status='Active' AND w.eligibility_date < CURDATE() THEN (w.customer_id)
                  END
                  ),0) AS total_customer,COUNT(DISTINCT p.id) AS total_products
          FROM prd_fees pf
          LEFT JOIN prd_assign_fees pa ON (pa.prd_fee_id=pf.id AND pa.is_deleted='N')
          LEFT JOIN prd_main p ON(pa.product_id = p.id AND p.carrier_id = pa.prd_fee_id AND p.is_deleted = 'N')
          LEFT JOIN website_subscriptions w ON (pa.product_id = w.product_id AND DATE(w.eligibility_date) < CURDATE())
          LEFT JOIN customer c ON (w.customer_id = c.id AND c.status='Active')
          WHERE pf.setting_type='Carrier' AND pf.is_deleted='N' 
          " . $incr . "
          GROUP BY pf.id 
          ORDER BY $SortBy $currSortDirection";
    $paginate = new pagination($page, $sql, $options);
    if ($paginate->success == true) {
      $fetch_rows = $paginate->resultset->fetchAll();
       
      $total_rows = count($fetch_rows);
    }
  } catch (paginationException $e) {
    echo $e;
    exit();
  }
 
  include_once 'tmpl/carrier.inc.php';
  exit;
}

$productRes = get_active_global_products_for_filter();

$tmpExJs = array('thirdparty/jquery_ui/js/jquery-ui-1.9.2.custom.min.js');

$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache);
$exJs = array(
  'thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache,
  'thirdparty/masked_inputs/jquery.maskedinput.min.js'
);
 
$template = "carrier.inc.php";
$layout = "main.layout.php";
include_once 'layout/end.inc.php';
?>