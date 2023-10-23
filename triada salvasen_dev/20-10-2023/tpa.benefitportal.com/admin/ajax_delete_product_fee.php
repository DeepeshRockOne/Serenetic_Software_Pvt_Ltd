<?php
include_once 'layout/start.inc.php';
$res = array();

$productFees = !empty($_POST['productFees']) ? json_decode($_POST['productFees'],true) : array();

$id = !empty($_GET['id']) ? $_GET['id'] : '';

unset($productFees[$id]);
	
$res['productFees'] = json_encode($productFees);
$res['status'] = "success";
$res['msg']="Product Fee Deleted";
	


header('Content-Type: application/json');
echo json_encode($res);
dbConnectionClose();
exit;
?>