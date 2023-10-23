<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$page_title = "Product List";
$id=$_GET['id'];

$category_sql = "SELECT id,title FROM prd_category WHERE is_deleted='N' AND status ='Active'";
$category_res = $pdo->select($category_sql);

$sch_params = array();
$SortBy = "p.name";
$SortDirection = "ASC";
$currSortDirection = "ASC";

$has_querystring = false;
 

$sel_params = array();
$incr = "";
if(isset($id)){
  $sch_params[':category_id']=$id;
  $incr.=" AND md5(p.category_id) = :category_id";
}

$sel_sql = "SELECT p.id,p.product_code,p.name,p.category_id 
			FROM prd_main p 
			WHERE p.is_deleted='N' " . $incr . " 
			ORDER BY  $SortBy $currSortDirection";
$fetch_rows = $pdo->select($sel_sql,$sch_params);
$total_rows = count($fetch_rows);
   
$template = 'product_categories_change.inc.php';
$layout="iframe.layout.php";
include_once 'layout/end.inc.php';
?>