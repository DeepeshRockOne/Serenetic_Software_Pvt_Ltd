<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$pricingModel = checkIsset($_POST['pricing_model']);
$fee_method = checkIsset($_POST['fee_method']);

$priceArr = checkIsset($_POST['pricing_arr']);
$priceArr = !empty($priceArr) ? json_decode($priceArr,true) : array();


if(!empty($pricingModel)){
	if($pricingModel == "FixedPrice"){
		$coverageOptions = checkIsset($_POST['coverage_options']);
		$prdPlansArr = !empty($coverageOptions) ? explode(",", $coverageOptions) : array();
	} else{
		$pricingMatrixKey = !empty($_POST['pricingMatrixKey']) ? json_decode($_POST['pricingMatrixKey'],true) : array();
		
		$price_control = !empty($_POST['price_control']) ? $_POST['price_control'] : array();

		if(!is_array($price_control)){
			$price_control = explode(",", $price_control);	
		}

		if($pricingModel == "VariableEnrollee"){
			$price_control_enrollee = !empty($_POST['price_control_enrollee']) ? $_POST['price_control_enrollee'] : array();
			if(!is_array($price_control_enrollee)){
				$price_control = explode(",", $price_control_enrollee);	
			}


			
		}
		$fetch_rows = $pricingMatrixKey;
		$total_rows = count($fetch_rows);
	}
}

include_once 'tmpl/product_fee_pricing_module.inc.php';
exit;
?>