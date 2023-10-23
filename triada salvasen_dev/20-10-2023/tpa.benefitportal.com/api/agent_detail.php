<?php
$agent_sql = "SELECT c.id,c.rep_id,c.fname,c.lname,c.birth_date,c.status,c.address,c.address_2,c.city,c.zip,
				s.rep_id as sponsor_rep_id,sc.short_name,
				AES_DECRYPT(c.ssn,'".$CREDIT_CARD_ENC_KEY . "') as ssn
				FROM customer c
				JOIN customer s ON(s.id = c.sponsor_id)
				JOIN states_c as sc ON(sc.name=c.state AND sc.country_id = '231')
				WHERE c.rep_id=:id";
$agent_row = $pdo->selectOne($agent_sql,array(":id"=>$agent_id));

if(!empty($agent_row)) {
	$data = array(
			'agentId' => $agent_row['rep_id'],
			'firstName' => $agent_row['fname'],
			'lastName' => $agent_row['lname'],
			'dateOfBirth' => $agent_row['birth_date'],
			'ssn' => $agent_row['ssn'],
			'parentAgentId' => $agent_row['sponsor_rep_id'],
			'status' => $agent_row['status'],
			'Address' => array(
			'line1' => $agent_row['address'],
			'line2' => $agent_row['address_2'],
			'city' => $agent_row['city'],
			'state' => $agent_row['short_name'],
			'zip' => $agent_row['zip'],
			),
			'licenses' => get_license_by_agent_id($agent_row['id']),
			'products' => get_products_by_agent_id($agent_row['id']),
			
	);

	$response = array(
		'success' => $success_value,
		'message' => 'Agent detail fetched',
		'data' => $data,
	);
	return_response($success_value,$response);
} else {
	$response = array(
		'success' => $fail_value,
		'message' => 'Extract has no agent',
	);
	return_response($fail_value,$response);
}