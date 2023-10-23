<?php

$prd_carrier_row = $pdo->selectOne("SELECT id,name as carrier_name FROM prd_fees WHERE display_id=:id",array(":id"=>$carrier_id));
$carrierAutoId = checkIsset($prd_carrier_row["id"]);

if(empty($prd_carrier_row)) {
	$response = array(
		'success' => $fail_value,
		'message' => 'Extract has 0 members',
	);
	return_response($fail_value,$response);
}

$term_incr = '';
$term_params = array();

$term_params[':carrier_id'] = $carrierAutoId;

if(!empty($from_date)){
	$term_incr .= " AND ws.term_date_set >= :from_date";
	$term_params[':from_date'] = date("Y-m-d",strtotime($from_date));
}

if(!empty($to_date)){
	$term_incr .= " AND ws.term_date_set <= :to_date";
	$term_params[':to_date'] = date("Y-m-d",strtotime($to_date));
}

//New updated
$term_sql = "SELECT 
		c.id as customer_id,c.rep_id,c.fname,c.mname,c.lname,c.birth_date,c.gender,c.email,c.cell_phone,c.address,c.address_2,sc.name as state,sc.short_name,c.city,c.zip,AES_DECRYPT(c.ssn,'" . $CREDIT_CARD_ENC_KEY . "')as ssn,
		
		ws.id as ws_id,ws.prd_plan_type_id as plan_type,ws.product_id,ws.plan_id,ws.issued_state,ws.website_id,
		ws.eligibility_date,ws.termination_date,DATE(ws.created_at) as created_at,ws.status,
		ws.ce_id as ce_id,ws.is_fulfillment,ws.sub_products_code,ws.parent_product_code,ws.issuedStateCode,
		
		s.rep_id as sponsor_rep_id,
		p.product_code,p.carrier_id,p.id as productId,
		ppt.title as plan_type_title,
		pc.title as product_category

		FROM 
		(
			SELECT 
			ws.id,ws.prd_plan_type_id,ws.product_id,ws.plan_id,wis.name as issued_state,ws.website_id,ws.customer_id,
			ws.eligibility_date,ws.termination_date,ws.term_date_set,ws.created_at,ws.status,
			ce.id as ce_id,ce.is_fulfillment,sp.product_code AS sub_products_code,
			pm.product_code AS parent_product_code,wis.short_name as issuedStateCode

			FROM website_subscriptions ws
			JOIN customer_enrollment ce ON(ws.id=ce.website_id)
			JOIN states_c AS wis ON((wis.name=ws.issued_state OR wis.short_name=ws.issued_state) AND wis.country_id='231')
			LEFT JOIN sub_products sp ON (FIND_IN_SET(sp.id,ce.sub_product) AND sp.status = 'Active' AND sp.is_deleted='N' AND sp.carrier_id=:carrier_id)
			LEFT JOIN prd_main pm ON (pm.id = ws.product_id AND pm.is_deleted='N' AND pm.carrier_id=:carrier_id AND sp.id IS NULL)
			WHERE sp.id IS NOT NULL OR pm.id IS NOT NULL
		)AS ws 
		JOIN customer as c ON(c.id=ws.customer_id AND c.is_deleted='N')
		JOIN customer as s ON(s.id=c.sponsor_id AND s.is_deleted='N')
		JOIN states_c AS sc ON((sc.name=c.state OR sc.short_name=c.state) AND sc.country_id='231')
	    JOIN prd_main AS p ON(p.id=ws.product_id AND p.type!='Fees')
	    JOIN prd_category as pc ON(pc.id=p.category_id)
	    JOIN prd_plan_type as ppt ON(ws.prd_plan_type_id=ppt.id)
	    WHERE ws.status IN ('Active','Pending','Inactive') AND (ws.termination_date IS NOT NULL) 
	    AND c.status IN('Active','Inactive') $term_incr
	    GROUP BY ws.id ORDER BY ws.id DESC";

$term_res = $pdo->select($term_sql,$term_params);

if(!empty($term_res)) {
	$members = array();
	$carrierName = '';
	foreach($term_res as $key => $row) {
		$carrierName = $prd_carrier_row['carrier_name'];
		
		$dependents = array();
		$dependents[] = array(
			'memberId' => $row['rep_id'],
			'relationship' => "Employee",
			'firstName' => valid_csv_cell_value($row['fname']),
			'middleName' => valid_csv_cell_value($row['mname']),
			'lastName' => valid_csv_cell_value($row['lname']),
			'dateOfBirth' => $row['birth_date'],
			'ssn' => str_replace(array('_','-'),'',$row['ssn']),
			'gender' => $row['gender'],
		);

		/*-------- Fetch Dependent Detail ---------*/
		if($row['plan_type'] > 1) {
			$dependent_sql = "SELECT 
                cp.id,cp.display_id,cp.relation,cp.fname,cp.mname,cp.lname,cp.birth_date,cp.ssn,cp.gender,cd.terminationDate
                FROM  customer_dependent_profile cp 
                JOIN customer_dependent cd ON(cd.cd_profile_id = cp.id and cd.is_deleted='N') 
                WHERE cp.is_deleted='N' AND
                cd.website_id=:ws_id GROUP BY cp.display_id";
			$dependent_params = array(':ws_id' => $row['ws_id']);
			$dependent_res = $pdo->select($dependent_sql,$dependent_params);
			if(count($dependent_res) > 0) {
				foreach ($dependent_res as $key => $value) {
					$dependents[] = array(
						'dependentId' => $value['display_id'],
						'relationship' => $value['relation'],
						'firstName' => valid_csv_cell_value($value['fname']),
						'middleName' => valid_csv_cell_value($value['mname']),
						'lastName' => valid_csv_cell_value($value['lname']),
						'dateOfBirth' => $value['birth_date'],
						'terminationDate' => (strtotime($value['terminationDate']) > 0 ? $value['terminationDate']:''),
						'ssn' => str_replace(array('_','-'),'',$value['ssn']),
						'gender' => $value['gender'], 
					);
				}
			}
		}

		if($row['carrier_id'] != $carrierAutoId) {
			$row['product_code'] = !empty($row['sub_products_code'])?$row['sub_products_code']:$row['product_code'];
		
		} else {
			if(!empty($row['sub_products_code'])) {
				$row['product_code'] = $row['sub_products_code'];

			} else {
				$row['product_code'] = !empty($row['parent_product_code'])?$row['parent_product_code']:$row['product_code'];
			
			}
		}

		$ord_sql = "SELECT DATE(o.created_at) as payment_date,od.start_coverage_period,od.end_coverage_period
				FROM orders o
				JOIN order_details od ON(od.order_id = o.id AND od.is_deleted='N')
				WHERE od.website_id=:ws_id
				ORDER BY od.start_coverage_period DESC";
		$ord_where = array(
			":ws_id" => $row['ws_id'],
		);
		$ord_row = $pdo->selectOne($ord_sql,$ord_where);

		$members[] = array(
			'memberId' => $row['rep_id'],
			'email' => $row['email'],
			'phone' => str_replace(array("(",")","-"," "),array("","","",""),$row['cell_phone']),
			'productId' => $row['product_code'],
			'agentId' => $row['sponsor_rep_id'],
			'policyId' => $row['website_id'],
			'productBenefit' => $row['plan_type_title'],
			'productCategory' => $row['product_category'],
			'enrollmentDate' => $row['created_at'],
			'effectiveDate' => $row['eligibility_date'],
			'terminationDate' => (strtotime($row['termination_date']) > 0?$row['termination_date']:''),
			'paymentDate' => $ord_row['payment_date'],
			'paymentPeriodStartDate' => $ord_row['start_coverage_period'],
			'paymentPeriodEndDate' => $ord_row['end_coverage_period'],
			'fulfillmentStatus' => ($row['is_fulfillment'] == "Y"?"Completed":"New"),
			'issueStateCode' => $row['issuedStateCode'],
			'issuedState' => $row['issued_state'],
			'productCodes' => get_product_codes($row['productId']),
			'residenceAddress' => array(
				'line1' => valid_csv_cell_value($row['address']),
				'line2' => valid_csv_cell_value($row['address_2']),
				'city' => valid_csv_cell_value($row['city']),
				'state' => $row['short_name'],
				'zip' => $row['zip'],
			),
			'participants' => $dependents,
		);	
	}

	$data = array(
		"carrierId" => $carrier_id,
		"carrierName" => $carrierName,
	);
	if(!empty($from_date)){
		$data["fromDate"] = date("Y-m-d",strtotime($from_date));
	}
	if(!empty($to_date)){
		$data["toDate"] = date("Y-m-d",strtotime($to_date));
	}
	if(!empty($members)){
		$data["members"] = $members;
	}

	$response = array(
		'success' => $success_value,
		'message' => 'Termination data fetched',
		'data' => $data,
	);
	return_response($success_value,$response);
} else {
	$response = array(
		'success' => $fail_value,
		'message' => 'Extract has 0 members',
	);
	return_response($fail_value,$response);
}
?>