<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once dirname(__DIR__) .'/includes/Api.class.php';
has_access(6);
$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "User Groups";
$breadcrumbes[2]['title'] = "Groups";
$breadcrumbes[2]['link'] = 'groups_listing.php';
$breadcrumbes[3]['title'] = "Manage Groups";
$breadcrumbes[3]['link'] = 'manage_groups.php';

$ajaxApiCall = new Api();

$cart_id = !empty($_GET['id']) ? $_GET['id'] : '';
$group_id = "";
$effective_date = "";
$termination_date = "";
$pay_calc = "";
$cart_type = "";
if(!empty($cart_id)){
	$data = array(
		'id' => $cart_id,
		'api_key' => 'variationCartSettings'
	);
	$variationDetail = $ajaxApiCall->ajaxApiCall($data,true);
	
	$pay_calc = !empty($variationDetail['data']['take_home_pay_calc']) ? $variationDetail['data']['take_home_pay_calc'] : "";
	$group_id = !empty($variationDetail['data']['group_id']) ? $variationDetail['data']['group_id'] : "";
	$group_repId = !empty($variationDetail['data']['rep_id']) ? $variationDetail['data']['rep_id'] : "";
	$group_name = !empty($variationDetail['data']['business_name']) ? $variationDetail['data']['business_name'] : "";
	$effective_date = !empty($variationDetail['data']['effective_date']) ? $variationDetail['data']['effective_date'] : "";
	$termination_date = !empty($variationDetail['data']['termination_date']) ? $variationDetail['data']['termination_date'] : "";
	$cart_type = !empty($variationDetail['data']['cart_type']) ? $variationDetail['data']['cart_type'] : "";
}

$template = 'manage_cart_variation.inc.php';
include_once 'layout/end.inc.php';
?>
