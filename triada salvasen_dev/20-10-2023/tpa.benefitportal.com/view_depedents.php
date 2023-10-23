<?php
include_once __DIR__ . '/includes/connect.php'; 
if(!empty($_GET['ws_id'])) {
	$ws_sql = "SELECT ws.*,p.product_code,p.name as product_name,CONCAT(c.fname,' ',c.lname) as customer_name
            FROM website_subscriptions ws
            JOIN customer c on (c.id = ws.customer_id)
            JOIN prd_main p on (p.id=ws.product_id)
            WHERE md5(ws.id)=:id";
	$ws_where = array(":id" => $_GET['ws_id']);
	$ws_row = $pdo->selectOne($ws_sql, $ws_where);

	//pre_print($ws_row);

	$dep_sql = "SELECT *,IF(relation IN ('Husband','Wife'),'Spouse','Child') AS crelation FROM `customer_dependent` WHERE customer_id=:customer_id AND product_id=:product_id AND product_plan_id=:product_plan_id GROUP BY cd_profile_id ORDER BY id DESC";
	$dep_where = array(
		":customer_id" => $ws_row['customer_id'],
		":product_id" => $ws_row['product_id'],
		":product_plan_id" => $ws_row['plan_id']
	);
	$dep_res = $pdo->select($dep_sql,$dep_where);
}
$template = 'view_depedents.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>