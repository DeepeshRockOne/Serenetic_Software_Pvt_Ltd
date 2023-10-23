<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once dirname(__DIR__) . '/includes/aws_reporting_api_url.php';
has_access(65);

$breadcrumbes[0]['title'] = '<i class="icon-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Vendors';
$page_title = "Vendors";

$sqlVendor = "SELECT pf.id,pf.display_id
            FROM prd_fees pf 
            WHERE pf.setting_type='Vendor' AND pf.is_deleted='N' ";
$resVendor = $pdo->select($sqlVendor);
 
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
            WHERE pf.setting_type='Vendor' AND pf.is_deleted='N' ";
$feeCodeRes = $pdo->selectOne($feeCodeSql);
$resArr = array();
if(!empty($feeCodeRes)){
  $feeResArr = explode(",", $feeCodeRes['display_id']); 
}   

$productRes = get_active_global_products_for_filter();

$is_ajaxed = checkIsset($_GET['is_ajaxed']); 
$vendor_ids = isset($_GET["vendor_ids"]) ?$_GET["vendor_ids"] :array();
$products = isset($_GET["products"])?$_GET["products"]:array();
$vendor_name = checkIsset($_GET['vendor_name']);  
$member_id = checkIsset($_GET['member_id']);
$status = checkIsset($_GET['status']);

$join_range = isset($_GET['join_range'])?$_GET['join_range']:"";
$fromdate = isset($_GET["fromdate"])?$_GET["fromdate"]:"";
$todate = isset($_GET["todate"])?$_GET["todate"]:"";
$added_date = isset($_GET["added_date"])?$_GET["added_date"]:"";

$member_id = cleanSearchKeyword($member_id);
$vendor_name = cleanSearchKeyword($vendor_name); 
  
if (!empty($vendor_ids)) {
  $incr .= " AND pf.id IN (". implode(',', $vendor_ids) .")";
}

if ($member_id != "") {
  $sch_params[':member_id'] = "%" .$member_id . "%";
  $incr.=" AND c.rep_id like :member_id";
}

if (!empty($products)) {
  $products_ids = "'" . implode("','", $products) . "'";
  $incr .= " AND pa.product_id IN ($products_ids)";
} 

if ($vendor_name != "") {
  $sch_params[':vendor_name'] = "%" . makeSafe($vendor_name) . "%";
  $incr.=" AND pf.name LIKE :vendor_name";
}

if ($status != "") {
  $sch_params[':status'] = makeSafe($status);
  $incr.=" AND pf.status = :status";
}

if($join_range != ""){
  if($join_range == "Range" && $fromdate!='' && $todate!=''){
    $sch_params[':fromdate'] = date("Y-m-d",strtotime($fromdate));
    $sch_params[':todate'] = date("Y-m-d",strtotime($todate));
    $incr.=" AND DATE(pf.created_at) >= :fromdate AND DATE(pf.created_at) <= :todate";
  }else if($join_range == "Exactly" && $added_date!=''){
    $sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
    $incr.=" AND DATE(pf.created_at) = :added_date";
  }else if($join_range == "Before" && $added_date!=''){
    $sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
    $incr.=" AND DATE(pf.created_at) < :added_date";
  }else if($join_range == "After" && $added_date!=''){
    $sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
    $incr.=" AND DATE(pf.created_at) > :added_date";
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
    'url' => 'vendors.php?' . $query_string,
    'db_handle' => $pdo->dbh,
    'named_params' => $sch_params
);

$page = (checkIsset($_GET['page']) && $_GET['page'] > 0) ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);

if($is_ajaxed){

  if(isset($_REQUEST['export']) && $_REQUEST['export'] == 'vendor_export'){

    if($fromdate !='' && $todate != '') {
        $no_days=0;
        if($fromdate != '' && $todate != '') {
            $date1 = date_create($fromdate);
            $date2 = date_create($todate);
            $diff = date_diff($date1,$date2);
            $no_days = $diff->format("%a");
        }
        if($no_days > 62) {
            echo json_encode(array("status"=>"fail","message"=>"Please enter proper date range. A maximum date range of 60 days is allowed per request."));
            exit();
        }
    }

    $job_id = add_export_request_api('EXCEL',$_SESSION['admin']['id'],'Admin',"Vendor Export","vendor_export",$incr,$sch_params,'','vendor_export');
    $reportDownloadURL = $AWS_REPORTING_URL['vendor_export']."&job_id=".$job_id;
      echo json_encode(array("status"=>"success","message"=>"Your export request is added","reportDownloadURL" => $reportDownloadURL)); 
      exit;
  }

  try {
      
    $sql="SELECT pf.*,md5(pf.id) as id,COUNT(DISTINCT pa.product_id) as total_products,COUNT(DISTINCT w.customer_id) as total_customer
          FROM prd_fees pf
          LEFT JOIN prd_assign_fees pa ON(pa.prd_fee_id=pf.id AND pa.is_deleted='N')  
          LEFT JOIN website_subscriptions w ON (pa.product_id = w.product_id AND w.status='Active')
          LEFT JOIN customer c ON (w.customer_id = c.id)
          WHERE pf.setting_type='Vendor' AND pf.is_deleted='N'
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
 
  include_once 'tmpl/vendors.inc.php';
  exit;
}
 
$exStylesheets = array(
  'thirdparty/multiple-select-master/multiple-select.css'.$cache,
  'thirdparty/jquery_ui/css/front/jquery-ui-1.9.2.custom.css'.$cache
);

$tmpExJs = array('thirdparty/jquery_ui/js/jquery-ui-1.9.2.custom.min.js');

$exJs = array(
  'thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache,
  'thirdparty/masked_inputs/jquery.maskedinput.min.js'
);
 
$template = "vendors.inc.php";
$layout = "main.layout.php";
include_once 'layout/end.inc.php';
?>