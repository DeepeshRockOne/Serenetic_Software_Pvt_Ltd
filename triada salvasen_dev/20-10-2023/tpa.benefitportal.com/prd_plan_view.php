<?php
include_once __DIR__ . '/includes/connect.php';
include_once __DIR__ . '/includes/Api.class.php';

$categoryID = !empty($_GET['categoryID']) ? $_GET['categoryID'] : 0;
$agentID = !empty($_GET['agentID']) ? $_GET['agentID'] : 0;
$productIDs = !empty($_GET['productIDs']) ? $_GET['productIDs'] : '';

if(empty($categoryID)){
	redirect('404.php');
}
$apiCall = new Api();
$data = [
	'productIDs' => $productIDs,
    'agentID' => $agentID,
    'categoryID' => $categoryID,
    'api_key' => 'pageBuilderCategoryProducts'
];
$apiResponse = $apiCall->ajaxApiCall($data,true);
// pre_print($apiResponse);
if($apiResponse['status'] == 'Success'){
	$categoryName = $apiResponse['data']['productCategory'];
	$productDetails = $apiResponse['data']['productDetails'];
}


$exStylesheets = array('groups/css/prd_preview.css'.$cache, 'thirdparty/malihu_scroll/css/jquery.mCustomScrollbar.css');
$exJs = array('thirdparty/malihu_scroll/js/jquery.mCustomScrollbar.concat.min.js');

$template = 'prd_plan_view.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>