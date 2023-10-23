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

$incr = '';
$sch_params = array();
$sch_params[':carrier_id'] = $carrierAutoId;

$incr .= " AND ws.eligibility_date <= :effectiveDate";
$sch_params[':effectiveDate'] = $effective_date;

if(!empty($run_date)){
	$incr .= " AND (ws.termination_date IS NULL OR ws.termination_date > :runDate)";
	$sch_params[':runDate'] = $run_date;
}else{
	$incr .= " AND (ws.termination_date IS NULL)";
}

//New updated
$eligibility_sql = "SELECT 
		c.id as customer_id,c.rep_id,c.fname,c.mname,c.lname,c.birth_date,c.gender,c.email,c.cell_phone,c.address,c.address_2,c.state,c.city,c.zip,AES_DECRYPT(c.ssn,'" . $CREDIT_CARD_ENC_KEY . "')as ssn,
		
		ws.id as ws_id,ws.prd_plan_type_id as plan_type,ws.product_id,ws.plan_id,ws.issued_state,ws.website_id,
		ws.eligibility_date,ws.termination_date,DATE(ws.created_at) as created_at,ws.status,
		ws.ce_id as ce_id,ws.is_fulfillment,ws.sub_products_code,		
		s.rep_id as sponsor_rep_id,
		p.product_code,p.carrier_id,p.id as productId,
		p.category_id,DATE(t.created_at) AS payment_date,od.start_coverage_period,od.end_coverage_period
		FROM 
		(
			SELECT 
			ws.id,ws.prd_plan_type_id,ws.product_id,ws.plan_id,ws.issued_state,ws.website_id,ws.customer_id,
			ws.eligibility_date,ws.termination_date,ws.created_at,ws.status,
			ce.id as ce_id,ce.is_fulfillment,sp.product_code AS sub_products_code,ws.last_order_id

			FROM website_subscriptions ws
			JOIN customer_enrollment ce ON(ws.id=ce.website_id)
			LEFT JOIN sub_products sp ON (FIND_IN_SET(sp.id,ce.sub_product) AND sp.status = 'Active' AND sp.is_deleted='N' AND sp.carrier_id=:carrier_id)
			WHERE ws.product_type='Normal' AND ws.status IN ('Active','Pending','Inactive') AND sp.id IS NOT NULL
		)AS ws 
		JOIN customer as c ON(c.id=ws.customer_id AND c.type='Customer' AND c.status IN('Active','Inactive'))
		JOIN customer as s ON(s.id=c.sponsor_id AND s.type IN('Agent','Group'))
	    JOIN prd_main AS p ON(p.id=ws.product_id AND p.type!='Fees')
	    LEFT JOIN orders o ON(o.customer_id = c.id AND ws.last_order_id=o.id)
		LEFT JOIN order_details od ON(od.order_id = o.id  AND od.is_deleted='N' AND od.website_id=ws.id)
		LEFT JOIN transactions t ON(o.id=t.order_id)
	    WHERE 1 $incr
	    GROUP BY ws.id ORDER BY ws.id DESC;";

$eligibility_res = $pdo->select($eligibility_sql,$sch_params);

if(count($eligibility_res) > 0) {
	$states_arr=getStatesShortNameRes();	
	$plan_type_arr=getPrdPlanType();	
	$prd_category_arr=getPrdCategory();

	$members = array();
	$carrierName = '';

	//Get product plan code start in one query 
		$productIdArray = array_unique(array_column($eligibility_res, 'productId'));
		$sqlProductCode="SELECT id,code_no,plan_code_value,product_id FROM prd_plan_code WHERE product_id IN(".implode(',',$productIdArray).") AND is_deleted='N' AND plan_code_value!='' ORDER BY code_no ASC";
		$resProductCode=$pdo->selectGroup($sqlProductCode,array(),'product_id');
		$productCodesArray = array();
		foreach($resProductCode as $productId => $productArr){
			$plan_code_counter = 0;
			$data = [];
			foreach($productArr as $value){
	  			$plan_code_label="";
	            if($value['code_no']=="GC"){
	              $plan_code_label = "Group Code";
	            }else{
	            	$plan_code_counter++;
	               $plan_code_label = "Plan Code ".$plan_code_counter;
	            }
	            $data[0][$plan_code_label] = $value["plan_code_value"];
			}
			$productCodesArray[$productId] = $data;
		}
	//Get product plan code start in one query 
	foreach($eligibility_res as $key => $row) {
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
				$productRow = $pdo->selectOne("SELECT product_code as parent_product_code FROM prd_main WHERE carrier_id=:carrier_id AND id=:id AND is_deleted='N'",array(":carrier_id"=>$carrierAutoId,':id'=>$row['product_id']));
				$row['product_code'] = !empty($productRow['parent_product_code'])?$productRow['parent_product_code']:$row['product_code'];
			
			}
		}

		$row['issued_state'] = trim(ucwords($row['issued_state']));
		$row['state'] = trim(ucwords($row['state']));
		$tmp_issued_state = trim(strtoupper($row['issued_state']));
		$tmp_state = trim(strtoupper($row['state']));

		$members[] = array(
			'memberId' => $row['rep_id'],
			'email' => $row['email'],
			'phone' => str_replace(array("(",")","-"," "),array("","","",""),$row['cell_phone']),
			'productId' => $row['product_code'],
			'agentId' => $row['sponsor_rep_id'],
			'policyId' => $row['website_id'],
			'issueStateCode' => (isset($states_arr[$tmp_issued_state])?$states_arr[$tmp_issued_state]:$tmp_issued_state),
			'productBenefit' => (isset($plan_type_arr[$row['plan_type']])?$plan_type_arr[$row['plan_type']]:''),
			'productCategory' => (isset($prd_category_arr[$row['category_id']])?$prd_category_arr[$row['category_id']]:''),
			'enrollmentDate' => $row['created_at'],
			'effectiveDate' => $row['eligibility_date'],
			'terminationDate' => (strtotime($row['termination_date']) > 0?$row['termination_date']:''),
			'paymentDate' => (isset($row['payment_date'])?$row['payment_date']:''),
			'paymentPeriodStartDate' => (isset($row['start_coverage_period'])?$row['start_coverage_period']:''),
			'paymentPeriodEndDate' => (isset($row['end_coverage_period'])?$row['end_coverage_period']:''),
			'fulfillmentStatus' => ($row['is_fulfillment'] == "Y"?"Completed":"New"),
			'issuedState' => $row['issued_state'],
			'productCodes' => isset($productCodesArray[$row['productId']]) ? $productCodesArray[$row['productId']] : array(),
			'residenceAddress' => array(
				'line1' => valid_csv_cell_value($row['address']),
				'line2' => valid_csv_cell_value($row['address_2']),
				'city' => valid_csv_cell_value($row['city']),
				'state' => (isset($states_arr[$tmp_state])?$states_arr[$tmp_state]:$tmp_state),
				'zip' => $row['zip'],
			),
			'participants' => $dependents,
		);	
	}

	$data = array(
		"carrierId" => $carrier_id,
		"carrierName" => $carrierName,
		"effectiveDate" => date("Y-m-d",strtotime($effective_date)),
	);


	if(!empty($run_date)){
		$data["runDate"] = date("Y-m-d",strtotime($run_date));
	}

	$data["members"] = $members;

	$response = array(
		'success' => $success_value,
		'message' => 'Eligibility data fetched',
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