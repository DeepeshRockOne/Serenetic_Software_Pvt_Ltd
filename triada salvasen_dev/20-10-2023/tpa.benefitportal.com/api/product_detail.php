<?php
$product_sql = "SELECT p.id as productId,p.product_code,p.name,p.is_license_require,p.type,
				p.primary_age_restrictions_from as from_age,p.primary_age_restrictions_to as age_to,
				pc.title as product_category,
				pf.name as carrier_name,pf.display_id as carrierDispId
				FROM prd_main p 
				JOIN prd_category pc ON(pc.id = p.category_id AND pc.status = 'Active')
				JOIN prd_fees pf ON(pf.id = p.carrier_id AND pf.setting_type = 'Carrier' AND pf.is_deleted = 'N')
				WHERE p.is_deleted = 'N' AND p.product_code=:id AND p.status IN('Active','Suspended')";
$product_row = $pdo->selectOne($product_sql,array(":id"=>$product_id));

if(!empty($product_row)) {
	$data = array(
		'productId' => $product_row['product_code'],
		'displayName' => $product_row['name'],
		'productCategory' => $product_row['product_category'],
		'carrierId' => $product_row['carrierDispId'],
		'carrierName' => $product_row['carrier_name'],
		'requireAgentLicense' => ($product_row['is_license_require'] == "Y" ? "Yes" : "No"),
		'type' => $product_row['type'],
		'fromAge' => !empty($product_row['from_age'])?$product_row['from_age']:'',
		'toAge' => !empty($product_row['age_to'])?$product_row['age_to']:'',
		'restrictedStates' => get_restricted_states_by_product($product_row['productId']),
		'productPlans' => get_product_plans_by_id($product_row['productId']),
		'subProducts' => get_sub_products($product_row['productId']),
		'productCodes' => get_product_codes($product_row['productId']),
	);

	$response = array(
		'success' => $success_value,
		'message' => 'Product detail fetched',
		'data' => $data,
	);
	return_response($success_value,$response);
} else {
	$response = array(
		'success' => $fail_value,
		'message' => 'Extract has no product',
	);
	return_response($fail_value,$response);
}