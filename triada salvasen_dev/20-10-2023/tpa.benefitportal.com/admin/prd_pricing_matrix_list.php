<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$pricingMatrixKey = !empty($_POST['pricingMatrixKey']) ? json_decode($_POST['pricingMatrixKey'],true) : array();
$price_control = !empty($_POST['price_control']) ? $_POST['price_control'] : array();

$allowPricingUpdate = !empty($_POST['allowPricingUpdate']) ? $_POST['allowPricingUpdate'] : false;

if(!$allowPricingUpdate){
	$price_control = !empty($_POST['allow_price_control']) ? $_POST['allow_price_control'] : array();	
}

$fetch_rows = $pricingMatrixKey;

$total_rows = count($fetch_rows);

include_once 'tmpl/prd_pricing_matrix_list.inc.php';
exit;

?>