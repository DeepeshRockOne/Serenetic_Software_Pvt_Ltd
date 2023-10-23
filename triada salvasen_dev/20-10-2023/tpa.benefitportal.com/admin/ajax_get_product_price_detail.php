<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$res = array();
$customer_id = $_REQUEST['customer_id'];
$new_prd_id = $_REQUEST['new_prd_id'];
$new_plan_type = $_REQUEST['new_plan_type'];
$ws_id = $_REQUEST['ws_id'];
$dependant = isset($_POST['dependant']) ? $_POST['dependant'] : array();
$dep_benefit_amount = isset($_POST['dep_benefit_amount']) ? $_POST['dep_benefit_amount'] : array();
$primary_benefit_amount = isset($_POST['primary_benefit_amount']) ? $_POST['primary_benefit_amount'] : '';
$other_params = array();

if($new_plan_type > 1 && !empty($dependant)) {
	$other_params['dep_ids'] = array_values($dependant);
	if(!empty($dep_benefit_amount)) {
		$other_params['dep_benefit_amount'] = $dep_benefit_amount;
	}	
}

if(!empty($primary_benefit_amount)) {
	$other_params['primary_benefit_amount'] = $primary_benefit_amount;
}
$price_detail = get_product_price_detail($customer_id,$new_prd_id,$new_plan_type,$ws_id,$other_params);
if(!empty($price_detail['plan_id'])) {
	$ws_sql = "SELECT ws.*
            FROM website_subscriptions ws
            WHERE ws.id=:id";
	$ws_row = $pdo->selectOne($ws_sql, array(":id" => $ws_id));
	$old_plan_price = $ws_row['price'];
	$new_plan_price = $price_detail['price'];

	$res['plan_id'] = $price_detail['plan_id'];
	$res['price'] = $price_detail['price'];
	$res['new_plan_price'] = displayAmount($new_plan_price);
	$res['old_plan_price'] = displayAmount($old_plan_price);
	$res['plan_price_diff'] = displayAmount(abs($new_plan_price - $old_plan_price));
	$res['plan_price_diff_org'] = abs($new_plan_price - $old_plan_price);
	$res['transaction_label'] = ($old_plan_price >= $new_plan_price?"Savings":"Increase");
	$res['status'] = 'success';
	$res['price_detail'] = $price_detail;
} else {
	$error = 'Missing information: all pricing criteria must be added for enrollees to complete this change.';
	if(!empty($price_detail['missing_pricing_criteria'])) {
		$error .= '<br/>The missing criteria is:';
		foreach ($price_detail['missing_pricing_criteria'] as $key1 => $value1) {
			foreach ($value1 as $key2 => $value2) {
				if($key1 == "Child" || $key1 == "Spouse") {
					$error .= '<br/><strong>'.$key2.' ('.$key1.') :</strong> '.implode(', ', $value2);	
				} else {
					$error .= '<br/><strong>Primary:</strong> '.implode(', ', $value2);	
				}
			}
		}
	}
	if(!empty($primary_benefit_amount)) {
		if(!empty($price_detail['error_display'])) {
			$res['benefit_amount_error'] = $price_detail['error_display'];
		}
	} else {
		if(!empty($price_detail['error_display'])) {
			$error = $price_detail['error_display'];
		}	
	}
	
	$res['error'] = $error;
	$res['status'] = 'fail';
	$res['price_detail'] = $price_detail;
}
echo json_encode($res);
dbConnectionClose();