<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$page_title = "Product List";
$id=$_GET['id'];

$company_sql = "SELECT id,company_name FROM prd_company WHERE is_deleted='N'";
$company_res = $pdo->select($company_sql);

$sch_params = array();
$SortBy = "p.name";
$SortDirection = "ASC";
$currSortDirection = "ASC";

$sel_params = array();
$incr = "";
if(isset($id)){
  $sch_params[':company_id']=$id;
  $incr.=" AND md5(p.company_id) = :company_id";
}

$sel_sql = "SELECT p.id,p.product_code,p.name,p.company_id 
			FROM prd_main p 
			WHERE p.is_deleted='N' " . $incr . " ORDER BY  $SortBy $currSortDirection";
$fetch_rows = $pdo->select($sel_sql,$sch_params);
$total_rows = count($fetch_rows);
   
$template = 'company_offering_products_change.inc.php';
$layout="iframe.layout.php";
include_once 'layout/end.inc.php';
?>