<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$pricingMatrixKey = !empty($_POST['pricingMatrixKey']) ? json_decode($_POST['pricingMatrixKey'],true) : array();
$price_control_enrollee = !empty($_POST['price_control_enrollee']) ? $_POST['price_control_enrollee'] : array();
$price_control = array();

$allowPricingUpdate = !empty($_POST['allowPricingUpdate']) ? $_POST['allowPricingUpdate'] : false;

if(!$allowPricingUpdate){
	$price_control_enrollee = !empty($_POST['allow_price_control_enrollee']) ? $_POST['allow_price_control_enrollee'] : array();	
}

if(!empty($price_control_enrollee)){
	foreach ($price_control_enrollee as $keyArr => $valueArr) {
		foreach ($valueArr as $key => $value) {
			array_push($price_control,$value);
		}
	}	
}



$fetch_rows = $pricingMatrixKey;

$total_rows = count($fetch_rows);



include_once 'tmpl/prd_pricing_matrix_list_enrollee.inc.php';
exit;

?>