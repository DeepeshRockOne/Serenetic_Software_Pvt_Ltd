<?php
include_once 'layout/start.inc.php';
$res = array();

$pricingMatrixKey = !empty($_POST['pricingMatrixKey']) ? json_decode($_POST['pricingMatrixKey'],true) : array();
$pricingMatrixRowDel = !empty($_POST['pricingMatrixRowDel']) ? $_POST['pricingMatrixRowDel'] : array();

$matrixID = !empty($_POST['matrixID']) ? $_POST['matrixID'] : '';

if(!empty($matrixID)){
	unset($pricingMatrixKey[$matrixID]);
}

if(!empty($pricingMatrixRowDel)){
	foreach ($pricingMatrixRowDel as $matrixKey) {
		unset($pricingMatrixKey[$matrixKey]);		
	}
}
	
$res['pricingMatrixKey'] = json_encode($pricingMatrixKey);
$res['status'] = "success";
$res['msg']="Price Matrix Deleted";

header('Content-Type: application/json');
echo json_encode($res);
exit;
?>