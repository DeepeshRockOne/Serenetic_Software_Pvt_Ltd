<?php
$product_sql = "SELECT p.id as productId,p.product_code,p.name,p.is_license_require,
				pc.title as product_category,
				pf.name as carrier_name,pf.display_id as carrierDispId
				FROM prd_main p 
				JOIN prd_category pc ON(pc.id = p.category_id AND pc.status = 'Active')
				JOIN prd_fees pf ON(pf.id = p.carrier_id AND pf.setting_type = 'Carrier' AND pf.is_deleted = 'N')
				WHERE p.is_deleted = 'N' AND p.status IN('Active','Suspended')";
$product_res = $pdo->select($product_sql);
if(!empty($product_res)) {
	$data = array();
	foreach ($product_res as $key => $product_row) {
		$data[] = array(
			'productId' => $product_row['product_code'],
			'displayName' => $product_row['name'],
			'productCategory' => $product_row['product_category'],
			'carrierId' => $product_row['carrierDispId'],
			'carrierName' => $product_row['carrier_name'],
			'requireAgentLicense' => ($product_row['is_license_require'] == "Y" ? "Yes" : "No"),
			'productCodes' => get_product_codes($product_row['productId']),
		);
	}
	$response = array(
		'success' => $success_value,
		'message' => 'Products list fetched',
		'data' => $data,
	);
	return_response($success_value,$response);
} else {
	$response = array(
		'success' => $fail_value,
		'message' => 'Extract has 0 products',
	);
	return_response($fail_value,$response);
}

