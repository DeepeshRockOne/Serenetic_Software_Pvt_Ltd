<?php
include_once 'layout/start.inc.php';
$res = array();

$pricingMatrixKey = !empty($_POST['pricingMatrixKey']) ? json_decode($_POST['pricingMatrixKey'],true) : array();

$matrixID = !empty($_POST['matrixID']) ? $_POST['matrixID'] : '';

$cloneKey = rand(0,20);

$pricingMatrixKey[$matrixID.$cloneKey] = $pricingMatrixKey[$matrixID];
$pricingMatrixKey[$matrixID.$cloneKey]['keyID'] = $matrixID.$cloneKey;
	
$res['pricingMatrixKey'] = json_encode($pricingMatrixKey);
$res['status'] = "success";
$res['msg']=" Price Matrix Cloned";
	


header('Content-Type: application/json');
echo json_encode($res);
exit;
?>