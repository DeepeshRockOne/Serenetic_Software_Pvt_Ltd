<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$fee_id = checkIsset($_GET['fee_id']);
$data=!empty($_GET['data']) ? json_decode($_GET['data'],true) : array();

$feePrice = array();
$product_code = '';
if (!empty($_GET['fee_id']) && !empty($data)) {
	$product_code = $data['product_code'];    
    $feePrice = $data['price'];    

}

$template = 'view_product_fee.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
