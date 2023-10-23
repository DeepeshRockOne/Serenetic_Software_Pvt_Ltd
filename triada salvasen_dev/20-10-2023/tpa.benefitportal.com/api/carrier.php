<?php

$prdFeesSql = "SELECT display_id,name FROM prd_fees WHERE status='Active' AND is_deleted='N' AND setting_type in ('Carrier')";
$prdFeesRes = $pdo->select($prdFeesSql);

if(!empty($prdFeesRes)) {
	$data = array();
	foreach ($prdFeesRes as $key => $carrier_row) {
		$data[] = array(
			'carrierId' => $carrier_row['display_id'],
			'carrierName' => $carrier_row['name'],
		);
	}
	$response = array(
		'success' => $success_value,
		'message' => 'Carriers list fetched',
		'data' => $data,
	);
	return_response($success_value,$response);
} else {
	$response = array(
		'success' => $fail_value,
		'message' => 'Extract has 0 carriers',
	);
	return_response($fail_value,$response);
}