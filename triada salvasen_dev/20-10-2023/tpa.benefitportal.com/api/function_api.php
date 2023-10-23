<?php
// Agent Detail API functions
function get_license_by_agent_id($agent_id = 0) {
	global $pdo;
	
	$licenses = array();
	$licenseStates = array();
	$getLicenses = $pdo->select("SELECT id,agent_id,selling_licensed_state,license_exp_date,extended_date,license_num FROM agent_license WHERE agent_id=:agent_id and is_deleted='N'", array(":agent_id" => $agent_id));

	if(!empty($getLicenses)) {
		$stateRes = $pdo->select("SELECT * FROM states_c WHERE country_id = 231 ORDER BY name ASC");
		$stateArr = array();
	
		foreach ($stateRes as $key => $value) {
			$stateArr[$value['name']] = $value['short_name'];
		}
	
		foreach ($getLicenses as $getLicense) {
			$state_tmp = !empty($getLicense["selling_licensed_state"]) ? explode(",", $getLicense["selling_licensed_state"]) : array();

			if (!empty($state_tmp)) {
				$license_exp_date = $getLicense["license_exp_date"];
				if(empty($license_exp_date) || $license_exp_date == '0000-00-00' || strtotime($license_exp_date) < strtotime(date("Y-m-d"))) {
					$license_exp_date = $getLicense["extended_date"];
				}
				foreach ($state_tmp as $state) {
						$licenses[] = array(
							'licenseNumber' => $getLicense["license_num"],
							'state' => $stateArr[$state],
							'expirationDate' => $getLicense['license_exp_date'],
						);
				}
			}
		}
	}
	return $licenses;
}
function get_products_by_agent_id($agent_id = 0) {
	global $pdo;
	$products = array();
	$product_sql = "SELECT p.id,p.product_code,p.name,pr.status as contract_status
					FROM prd_main p 
					JOIN agent_product_rule pr ON(pr.product_id=p.id AND pr.agent_id=:agent_id) 
					WHERE p.is_deleted = 'N' AND p.status='Active'";
	$product_res = $pdo->select($product_sql,array(":agent_id"=>$agent_id));
	if(!empty($product_res)) {
		foreach ($product_res as $key => $product_row) {
			$products[] = array(
				'productId' => $product_row['product_code'],
				'displayName' => $product_row['name'],
				'contractStatus' => $product_row['contract_status'],
			);
		}
	}
	return $products;
}

// Product Detail API functions
function get_restricted_states_by_product($product_id = 0) {
	global $pdo;

	$data = array();
	if(!empty($product_id)) {
		$selPrdState = "SELECT product_id,state_name FROM prd_no_sale_states WHERE product_id=:product_id AND is_deleted='N'";
		$resPrdState = $pdo->select($selPrdState,array(":product_id" => $product_id));
		if(!empty($resPrdState)) {
			$stateRes = $pdo->select("SELECT name,short_name FROM states_c WHERE country_id = 231 ORDER BY name ASC");
			$stateArr = array();
			foreach ($stateRes as $key => $value) {
				$stateArr[$value['name']] = $value['short_name'];
			}

			foreach ($resPrdState as $key => $state_row) {
				$data[] = array(
					'state' => $state_row['state_name'],
					'shortName' => $stateArr[$state_row['state_name']],
				);
			}
		}
	}
	return $data;
}
function get_product_plans_by_id($product_id = 0) {
	global $pdo;

	$data = array();
	if(!empty($product_id)) {
		$prd_plan_sql = "SELECT pm.id,pm.price,ppt.title as plan_type_title
						FROM prd_main p
						JOIN prd_matrix pm ON(p.id=pm.product_id AND pm.is_deleted='N')
						LEFT JOIN prd_plan_type ppt ON(ppt.id=pm.plan_type) 
						WHERE p.is_deleted='N' AND p.type!='Fees' AND pm.is_deleted='N' AND pm.product_id=:product_id";
		$prd_plan_res = $pdo->select($prd_plan_sql,array(":product_id"=>$product_id));
		if(!empty($prd_plan_res)) {
			foreach ($prd_plan_res as $key => $prd_plan_row) {
				$data[] = array(
					'planId' => $prd_plan_row['id'],
					'planType' => !empty($prd_plan_row['plan_type_title']) ? $prd_plan_row['plan_type_title'] : "",
					'price' => $prd_plan_row['price'],
				);
			}
		}
	}
	return $data;
}
function get_sub_products($product_id = 0) {
	global $pdo;

	$data = array();
	if(!empty($product_id)) {
		$sub_product_sql = "SELECT sp.product_code,sp.product_name,
						pf.name as carrier_name,pf.display_id as carrierDispId
						FROM prd_sub_products psp 
						JOIN sub_products sp ON(sp.id=psp.sub_product_id AND sp.is_deleted='N') 
						JOIN prd_fees pf ON(pf.id = sp.carrier_id AND pf.setting_type = 'Carrier' AND pf.is_deleted = 'N')
						WHERE psp.is_deleted='N' AND psp.product_id=:product_id";
		$sub_product_res = $pdo->select($sub_product_sql,array(":product_id"=>$product_id));
		
		if(!empty($sub_product_res)) {
			foreach ($sub_product_res as $key => $sub_product_row) {
				$data[] = array(
					'productId' => $sub_product_row['product_code'],
					'displayName' => $sub_product_row['product_name'],
					'carrierId' => $sub_product_row['carrierDispId'],
					'carrierName' => $sub_product_row['carrier_name'],
				);
			}
		}
	}
	return $data;
}
function get_product_codes($product_id = 0) {
	global $pdo;

	$data = array();
	if(!empty($product_id)) {
		$sqlProductCode="SELECT id,code_no,plan_code_value FROM prd_plan_code WHERE product_id=:product_id AND is_deleted='N' AND plan_code_value!='' ORDER BY code_no ASC";
		$resProductCode=$pdo->select($sqlProductCode,array(":product_id"=>$product_id));
		$plan_code_counter = 0;
		
		if(!empty($resProductCode)) {
      		foreach ($resProductCode as $value) {
      			$plan_code_label="";

                if($value['code_no']=="GC"){
                  $plan_code_label = "Group Code";
                }else{
                	$plan_code_counter++;
                   $plan_code_label = "Plan Code ".$plan_code_counter;
                }

                $data[0][$plan_code_label] = $value["plan_code_value"];
			}
		}
	}
	return $data;
}

// Member Detail API functions
function get_member_subscriptions($customer_id = 0) {
	global $pdo,$CREDIT_CARD_ENC_KEY;

	$subscription_sql = "SELECT 
				ws.id as ws_id,DATE(ws.created_at) as enrollmentDate,ws.prd_plan_type_id as plan_type,ws.eligibility_date,
				ws.termination_date,
				c.rep_id,c.fname,c.mname,c.lname,c.birth_date,c.gender,
				AES_DECRYPT(c.ssn,'" . $CREDIT_CARD_ENC_KEY . "')as ssn,
				a.rep_id as agent_rep_id,p.product_code,sc.short_name,ppt.title as plan_type_title,
				pc.title as product_category,ce.fulfillment_date

				FROM website_subscriptions ws
				JOIN customer_enrollment ce ON(ws.id=ce.website_id)
			    JOIN customer c ON(c.id=ws.customer_id)
			    JOIN states_c sc ON((sc.name=c.state OR sc.short_name=c.state) AND sc.country_id = '231')
			    JOIN customer a ON(a.id=c.sponsor_id)
			    JOIN prd_main p ON(p.id=ws.product_id)
			    JOIN prd_category pc ON(pc.id=p.category_id)
			    JOIN prd_plan_type ppt ON(ws.prd_plan_type_id=ppt.id)
			    WHERE c.id=:customer_id GROUP BY ws.id ORDER BY ws.id DESC";
	$subscription_res = $pdo->select($subscription_sql,array(':customer_id'=>$customer_id));

	$subscriptions = array();
	foreach ($subscription_res as $key => $row) {
		$dependents = array();
		$dependents[] = array(
			'memberId' => $row['rep_id'],
			'relationship' => "Employee",
			'firstName' => $row['fname'],
			'middleName' => $row['mname'],
			'lastName' => $row['lname'],
			'dateOfBirth' => $row['birth_date'],
			'ssn' => str_replace('_','',$row['ssn']),
			'gender' => $row['gender'],
		);

		/*-------- Fetch Dependent Detail ---------*/
		if($row['plan_type'] > 1) {
			$dependent_sql = "SELECT 
                cp.id,cp.display_id,cp.relation,cp.fname,cp.mname,cp.lname,cp.birth_date,cp.ssn,cp.gender
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
						'ssn' => str_replace(array('_','-'),'',$value['ssn']),
						'gender' => $value['gender'], 
					);
				}
			}
		}

		$subscriptions[] = array(
			'productId' => $row['product_code'],
			'agentId' => $row['agent_rep_id'],
			'issueStateCode' =>$row['short_name'],
			'productBenefit' => $row['plan_type_title'],
			'productCategory' => $row['product_category'],
			'enrollmentDate' => $row['enrollmentDate'],
			'effectiveDate' => $row['eligibility_date'],
			'terminationDate' => (strtotime($row['termination_date']) > 0?$row['termination_date']:''),
			'fulfillmentDate' => (strtotime($row['fulfillment_date']) > 0?$row['fulfillment_date']:''),
			'participants' => $dependents,
		);
	}
	return $subscriptions;
}

function getPrdPlanType() {
	global $pdo;
	$plan_type_arr = array();
	$planTypeRes = $pdo->select("SELECT id,title FROM prd_plan_type");
	if(!empty($planTypeRes)) {
		foreach ($planTypeRes as $value) {
			$plan_type_arr[$value['id']] = $value['title'];
		}
	}
	return $plan_type_arr;
}
function getPrdCategory() {
	global $pdo;
	$prd_category_arr = array();
	$prdCategoryRes = $pdo->select("SELECT id,title FROM prd_category");
	if(!empty($prdCategoryRes)) {
		foreach ($prdCategoryRes as $value) {
			$prd_category_arr[$value['id']] = $value['title'];
		}
	}
	return $prd_category_arr;
}
function getStatesShortNameRes() {
	global $pdo;
	$states_arr = array();
	$stateRes = $pdo->select("SELECT name,short_name FROM states_c WHERE country_id='231'");
	if(!empty($stateRes)) {
		foreach ($stateRes as $value) {
			$state_name = trim(strtoupper($value['name']));
			$states_arr[$state_name] = $value['short_name'];
		}
	}
	return $states_arr;
}
?>