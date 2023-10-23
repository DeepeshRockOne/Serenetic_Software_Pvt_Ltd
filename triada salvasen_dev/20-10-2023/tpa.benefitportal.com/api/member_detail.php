<?php
$customer_rep_id = $member_id;
$customer_sql = "SELECT c.id,c.rep_id,c.fname,c.mname,c.lname,c.gender,c.email,c.cell_phone,c.birth_date,
				c.address,c.address_2,c.city,c.zip,
				s.rep_id as sponsor_rep_id,sc.short_name,
				AES_DECRYPT(c.ssn,'".$CREDIT_CARD_ENC_KEY . "') as ssn
				FROM customer c
				JOIN customer s ON(s.id = c.sponsor_id)
				JOIN states_c as sc ON((sc.name=c.state OR sc.short_name=c.state) AND sc.country_id = '231')
				WHERE c.rep_id=:id AND c.is_deleted='N'";
$customer_row = $pdo->selectOne($customer_sql,array(":id"=>$customer_rep_id));

if(!empty($customer_row)) {
	$data = array(
		'memberId' => $customer_row['rep_id'],
		'firstName' => $customer_row['fname'],
		'middleName' => $customer_row['mname'],
		'lastName' => $customer_row['lname'],
		'gender' => $customer_row['gender'],
		'email' => $customer_row['email'],
		'phone' => $customer_row['cell_phone'],
		'dateOfBirth' => $customer_row['birth_date'],
		'ssn' => str_replace('_','',$customer_row['ssn']),
		'residenceAddress' => array(
			'line1' => $customer_row['address'],
			'line2' => $customer_row['address_2'],
			'city' => $customer_row['city'],
			'state' => $customer_row['short_name'],
			'zip' => $customer_row['zip'],
		),
		'subscriptions' => get_member_subscriptions($customer_row['id'])
	);

	$response = array(
		'success' => $success_value,
		'message' => 'Member detail fetched',
		'data' => $data,
	);
	return_response($success_value,$response);
} else {
	$response = array(
		'success' => $fail_value,
		'message' => 'Extract has no member',
	);
	return_response($fail_value,$response);
}