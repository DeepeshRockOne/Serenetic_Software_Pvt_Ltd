<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$sch_params = array();
$incr='';
$SortBy = "pmx.create_date";
$SortDirection = "DESC";
$currSortDirection = "ASC";
 
 
$id = checkIsset($_GET['fee_id']); 
$name = checkIsset($_GET['name']); 
$display_id = checkIsset($_GET['display_id']);   

if(!empty($id)){
	$sch_params[":id"] = makeSafe($id);
	$incr .= " AND md5(pmx.product_id) = :id";	
} 

 
$sel_sql="SELECT  pmx.*
		  FROM prd_matrix pmx
          WHERE pmx.is_deleted = 'N' ". $incr ."
          ORDER BY field(pmx.plan_type,1,3,2,5,4) $currSortDirection";
     
 $fetch_rows = $pdo-> select($sel_sql,$sch_params);

$template = 'view_plan_price.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>