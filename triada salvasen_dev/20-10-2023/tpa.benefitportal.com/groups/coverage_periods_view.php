<?php
include_once __DIR__ . '/includes/connect.php';

$coverage = !empty($_GET['coverage']) ? $_GET['coverage'] : 0;

$sqlCheck = "SELECT gcp.id,gco.id as gco_id,p.id as product_id,gcp.coverage_period_name,p.name,p.product_code,count(DISTINCT gco.class_id) as total_class,COUNT(DISTINCT ws.customer_id) AS total_member
			FROM group_coverage_period gcp
			JOIN group_coverage_period_offering gco ON (gcp.id = gco.group_coverage_period_id AND gco.is_deleted='N')
			JOIN prd_main p ON FIND_IN_SET(p.id,gco.products)
			JOIN group_classes gc ON (gc.id = gco.class_id)
			LEFT JOIN (
				SELECT ws.customer_id,ws.product_id,cs.group_coverage_period_id,cs.class_id
				FROM website_subscriptions ws
				JOIN customer c ON (c.id = ws.customer_id AND c.is_deleted='N')
				JOIN customer_settings cs ON (cs.customer_id = c.id)
				WHERE c.status NOT IN('Customer Abandon','Pending Quote','Pending Validation','Post Payment','Pending')
			) ws ON(ws.product_id = p.id AND ws.group_coverage_period_id = gco.group_coverage_period_id)
			WHERE md5(gcp.id)=:id 
			GROUP BY p.id
			ORDER BY p.name";
$resCheck = $pdo->select($sqlCheck,array(":id"=>$coverage));


$coverage_period_name = "";

if(!empty($resCheck)){
	$coverage_period_name = $resCheck[0]['coverage_period_name'];
}

$template = 'coverage_periods_view.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>
