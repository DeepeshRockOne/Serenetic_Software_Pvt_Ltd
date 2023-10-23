<?php
include_once __DIR__ . '/includes/connect.php'; 

$offering_id =!empty($_GET['offering']) ? $_GET['offering'] : 0;

$selSql="SELECT md5(gco.id) as id,gc.class_name,gco.is_contribution,p.name as product_name FROM group_coverage_period_offering gco 
  JOIN group_classes gc ON (gc.id = gco.class_id)
  JOIN prd_main p ON FIND_IN_SET(p.id,gco.products)
  WHERE md5(gco.id)=:offering_id and gco.is_deleted='N'";
$selRes=$pdo->select($selSql,array(":offering_id"=>$offering_id));

$class_name = "";
if(!empty($selRes)){
	$class_name = $selRes[0]['class_name'];
}

$template = 'coverage_periods_product.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';

?>