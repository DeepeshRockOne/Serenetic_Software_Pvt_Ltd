<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
has_access(15);
 
$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Products List';
$breadcrumbes[1]['link'] = 'manage_product.php';
$breadcrumbes[2]['title'] = 'Product Variation';
$page_title = "Product List";

//Product Category 
$sql_cat = "SELECT id,title FROM prd_category WHERE status='Active'";
$res_cat = $pdo->select($sql_cat);

//Product Company
$sql_company = "SELECT id,company_name FROM prd_company";
$res_company = $pdo->select($sql_company);

$sch_params = array();
$incr="";
$SortBy = "p.id";
$SortDirection = "ASC";
$currSortDirection = "DESC";

$SortByStatus = 'p.id';
$SortDirectionStatus = 'ASC';
$currSortDirectionStatus = "ASC";

$has_querystring = false;
if (isset($_GET["sort"]) && $_GET["sort"] != "") {
  $has_querystring = true;
  $SortBy = $_GET["sort"];
}

if (isset($_GET["direction"]) && $_GET["direction"] != "") {
  $has_querystring = true;
  $currSortDirection = $_GET["direction"];
  if ($_GET["direction"] == "ASC") {
    $SortDirection = "DESC";
    $currSortDirectionStatus = "DESC";
    $SortDirectionStatus = 'DESC';
  } else {
    $SortDirection = "ASC";
    $currSortDirectionStatus = "ASC";
    $SortDirectionStatus = 'ASC';
  }
}

$product = isset($_GET['product'])?$_GET['product']:"";
$is_ajaxed = isset($_GET['is_ajaxed'])?$_GET['is_ajaxed']:"";
$product_code = isset($_GET["product_code"])?$_GET["product_code"]:'';
$title = isset($_GET["title"])?$_GET["title"]:"";
$product_category = isset($_GET['product_category'])?$_GET['product_category']:"";
$products_type = isset($_GET['products_type'])?$_GET['products_type']:"";
$s_status = isset($_GET["s_status"])?$_GET["s_status"]:"";
$company_id = isset($_GET["company"])?$_GET["company"]:"";
$join_range = isset($_GET['join_range'])?$_GET['join_range']:"";
$fromdate = isset($_GET["fromdate"])?$_GET["fromdate"]:"";
$todate = isset($_GET["todate"])?$_GET["todate"]:"";
$added_date = isset($_GET["added_date"])?$_GET["added_date"]:"";


if(!empty($product)){
  $sch_params[':parent_product_id'] = $product;
  $incr.=" AND md5(p.parent_product_id) = :parent_product_id";
} 

if($join_range != ""){
  if($join_range == "Range" && $fromdate!='' && $todate!=''){
    $sch_params[':fromdate'] = date("Y-m-d",strtotime($fromdate));
    $sch_params[':todate'] = date("Y-m-d",strtotime($todate));
    $incr.=" AND DATE(p.create_date) >= :fromdate AND DATE(p.create_date) <= :todate";
  }else if($join_range == "Exactly" && $added_date!=''){
    $sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
    $incr.=" AND DATE(p.create_date) = :added_date";
  }else if($join_range == "Before" && $added_date!=''){
    $sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
    $incr.=" AND DATE(p.create_date) < :added_date";
  }else if($join_range == "After" && $added_date!=''){
    $sch_params[':added_date'] = date("Y-m-d",strtotime($added_date));
    $incr.=" AND DATE(p.create_date) > :added_date";
  }
}


if (!empty($company_id)) {
  $company_id = implode(',', $company_id);
  $incr.=" AND (p.company_id in ($company_id))";
}

if (!empty($products_type)) {
  $products_type = implode("','", $products_type);
  $incr.=" AND (p.product_type in ('".$products_type."'))";
}

if (!empty($product_category)) {
  $product_category = implode("','", $product_category);
  $incr.=" AND (p.category_id in ('".$product_category."'))";
}
 
if(!empty($s_status)){
  $s_status = implode("','", $s_status);
  $incr.=" AND (p.status in ('".$s_status."'))";
}

if (!empty($product_code)) {
 $sch_params[':product_code'] = '%'.makeSafe($product_code).'%';
  $incr .= " AND p.product_code LIKE :product_code";
}

if ($title != "") {
  $sch_params[':title'] = '%'.makeSafe($title).'%';
  $incr.=" AND p.name LIKE :title";
}


if (count($sch_params) > 0) {
  $has_querystring = true;
}

if (isset($_GET['pages']) && $_GET['pages'] > 0) {
  $has_querystring = true;
  $per_page = $_GET['pages'];
}

$query_string = $has_querystring ? (isset($_GET['page']) && $_GET['page'] ? str_replace('page=' . $_GET['page'], "page=*VAR*", $_SERVER['QUERY_STRING']) : $_SERVER['QUERY_STRING'] . "&page=*VAR*") : 'page=*VAR*';

$options = array(
    'results_per_page' => $per_page,
    'url' => 'manage_variation_list.php?' . $query_string,
    'db_handle' => $pdo->dbh,
    'named_params' => $sch_params
);

$page = isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] : 1;
$options = array_merge($pageinate_html, $options);

if ($is_ajaxed) {
  try {
    $sel_sql = "SELECT p.product_code, p.status, p.type, p.id as pro_id, p.name as product_nm,c.title as cat_name,cl.company_name,p.product_type,p.create_date,md5(p.id) as encrypted_id  
                FROM prd_main p
                LEFT JOIN prd_company cl ON(cl.id = p.company_id)
                LEFT JOIN prd_category c ON(p.category_id=c.id)
                WHERE p.record_type='Variation' AND p.is_deleted='N' AND p.type ='Normal' " . $incr . 
                " GROUP BY p.id 
                ORDER BY $SortByStatus $currSortDirectionStatus, $SortBy $currSortDirection ";
     
    $paginate = new pagination($page, $sel_sql, $options);
    if ($paginate->success == true) {
      $fetch_rows = $paginate->resultset->fetchAll();
      $total_rows = count($fetch_rows);
    }
  } catch (paginationException $e) {
    echo $e;
    exit();
  }
  
  if (isset($_GET["is_export"]) && $_GET["is_export"]) {
    $csv_line = "\n";
    $csv_seprator = "\t";
    $content = "";

    $content .= "ID" . $csv_seprator .
                "Added Date" . $csv_seprator .
                "Product Name" . $csv_seprator .
                "Company" . $csv_seprator .
                "Product Type" . $csv_seprator .
                "Product Category" . $csv_seprator .
                "Status" . $csv_line;
      $sel_sql = "SELECT p.product_code, p.child_product, p.status, p.type, p.id as pro_id, p.name as product_nm,c.title as cat_name,cl.company_name,p.product_type,p.create_date
            FROM prd_main p
            LEFT JOIN prd_company cl ON(cl.id = p.company_id)
            LEFT JOIN prd_category c ON(p.category_id=c.id)
            WHERE p.record_type='Variation' AND p.is_deleted='N' AND p.type ='Normal' " . $incr . 
      " GROUP BY p.id 
      ORDER BY $SortByStatus $currSortDirectionStatus, $SortBy $currSortDirection ";

      $fetch_rows=$pdo->select($sel_sql,$sch_params);

      if($fetch_rows){
        foreach ($fetch_rows as $key => $value) {
          $content .= $value['product_code'] . $csv_seprator .
                      date('m/d/Y',strtotime($value['create_date'])) . $csv_seprator .
                      $value['product_nm'] . $csv_seprator .
                      $value['company_name'] . $csv_seprator .
                      $value['product_type'] . $csv_seprator .
                      $value['cat_name'] . $csv_seprator .
                      $value['status'] . $csv_line;
        }

        if ($content) {
            $csv_filename = "manage_variation_list_" . date("Ymd", time()) . ".xls";
            header('Content-type: application/vnd.ms-excel');
            header('Content-disposition: attachment;filename=' . $csv_filename);
            echo $content;
            exit;
        }
      }
  }

  include_once 'tmpl/manage_variation_list.inc.php';
  exit;
}

$exStylesheets = array(
  'thirdparty/multiple-select-master/multiple-select.css',
  'thirdparty/select2/css/select2.css'
);
$exJs = array(
  'thirdparty/multiple-select-master/jquery.multiple.select.js',
  'thirdparty/select2/js/select2.full.min.js'
);

$template = 'manage_variation_list.inc.php';
include_once 'layout/end.inc.php';
?>