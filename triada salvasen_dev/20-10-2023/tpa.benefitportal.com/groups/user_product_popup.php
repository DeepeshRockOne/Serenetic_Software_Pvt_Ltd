<?php
include_once __DIR__ . '/includes/connect.php'; 

$customer_id = $_GET['id'];


$selectProducts = "SELECT ws.purchase_date, ws.website_id,p.name as product_name,p.product_code,ws.status
                FROM website_subscriptions as ws
                JOIN customer c on(ws.customer_id=c.id)
                JOIN prd_main p on(p.id=ws.product_id AND p.is_deleted='N')
                WHERE md5(c.id)=:id  AND p.type!='Fees' ORDER BY FIELD (p.type,'Normal','Fees') ASC,FIELD (ws.status,'Post Payment','Active','Inactive') ASC,p.name ASC";
$whereProducts = array(":id" => $customer_id);

$resProducts = $pdo->select($selectProducts, $whereProducts);

$template = 'user_product_popup.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>
