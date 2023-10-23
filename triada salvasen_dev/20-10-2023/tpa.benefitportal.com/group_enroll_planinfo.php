<?php
include_once __DIR__ . '/includes/connect.php';
include_once __DIR__ . '/includes/Api.class.php';

$ajaxApiCall = new Api();
$productID =  isset($_REQUEST['productId']) ? $_REQUEST['productId'] : '' ;
if(!empty($productID)){
$productData = [
    'productID' => $productID,
    'api_key' => 'productData'
];
$productapiResponse = $ajaxApiCall->ajaxApiCall($productData,true);
$productDetails = $productapiResponse['data'];
}

$template = 'group_enroll_planinfo.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>