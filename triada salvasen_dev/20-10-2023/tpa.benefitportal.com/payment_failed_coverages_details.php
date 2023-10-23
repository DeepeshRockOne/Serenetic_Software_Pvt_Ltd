<?php
include_once __DIR__ . '/includes/connect.php';
include_once __DIR__ . '/includes/function.class.php';

$function_list = new functionsList();

$customerId = checkIsset($_REQUEST["customerId"]);
$websiteId = checkIsset($_REQUEST["websiteId"]);
$res = array();

if(!empty($customerId) && !empty($websiteId)){
	$res = $function_list->getPaymentFailedCoverages($customerId,$websiteId,array("getFailCov" => "Y")); 	
}

// pre_print($res);
$template = 'payment_failed_coverages_details.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>