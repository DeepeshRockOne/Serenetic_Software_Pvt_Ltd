<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once dirname(__DIR__) .'/includes/function.class.php';
$function_list = new functionsList();
$LifeEvents = $function_list->getLifeEvents();
$sqlCategory = "SELECT * FROM prd_category WHERE is_deleted='N'";
$resCategory = $pdo->select($sqlCategory);
// pre_print($_REQUEST);

$sqlProduct="SELECT p.category_id,p.id as product_id,p.name,p.product_code FROM prd_main p WHERE p.is_deleted='N' AND p.type != 'Fees' AND p.category_id > 0 AND p.record_type!='Variation' order by p.category_id,p.name ASC";
$resProduct=$pdo->select($sqlProduct);

$incr="";
$schParams = array();

$category_id = isset($_GET['category_id'])?$_GET['category_id']:'';

if (!empty($category_id)) {
  $schParams[':category_id'] = $category_id;
  $incr .= " AND pc.id=:category_id";
}

$sqlConnectedProducts="SELECT pc.title as categoryName,p_conn.*,pcp.* 
FROM prd_connections p_conn 
JOIN prd_connected_products pcp ON(pcp.connection_id=p_conn.id)
JOIN prd_main p ON (p.id=pcp.product_id)
JOIN prd_category pc ON (pc.id=pcp.category_id)
where pcp.is_deleted='N' $incr order by pcp.order_by ASC";
$resConnectedProducts=$pdo->select($sqlConnectedProducts,$schParams);

$connectionArr = array();
$allConnectedProduct = array();

if(!empty($resConnectedProducts)){
	foreach ($resConnectedProducts as $key => $value) {
		if(!array_key_exists($value['connection_id'].'_'.$value['category_id'], $connectionArr)){
			$connectionArr[$value['connection_id'].'_'.$value['category_id']]=array();
		}
		array_push($connectionArr[$value['connection_id'].'_'.$value['category_id']], $value);

		array_push($allConnectedProduct, $value['product_id']);
	}
}

include_once 'tmpl/get_connected_category_products.inc.php';
exit;
?>
