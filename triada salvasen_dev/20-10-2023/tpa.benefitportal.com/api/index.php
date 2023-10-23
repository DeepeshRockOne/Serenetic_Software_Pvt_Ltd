<?php
include_once "config_api.inc.php";

	$request_file = '';

	if($requested_api == 'product') {
		if(!empty($param1)) {
			//URL = https://api-endpointsalvasen.benefitportal.com/api/product/CH_LM_500
			$product_id = $param1;
			$request_file = "product_detail.php";
		} else {
			//URL = https://api-endpointsalvasen.benefitportal.com/api/product
			$request_file = "product.php";			
		}
	
	} elseif($requested_api == 'agent') {	
		if(!empty($param1)) {
			//URL = https://api-endpointsalvasen.benefitportal.com/api/agent/A3728348	
			$agent_id = $param1;
			$request_file = "agent_detail.php";
		} else {
			//URL = https://api-endpointsalvasen.benefitportal.com/api/agent
			$request_file = "agent.php";			
		}

	}  elseif($requested_api == 'member') {
		if(!empty($param1)) {
			//URL = https://api-endpointsalvasen.benefitportal.com/api/member/1	
			$member_id = $param1;
			$request_file = "member_detail.php";
		} else {

		}

	} elseif($requested_api == 'fulfillment') {
		if(!empty($param2) && $param2 == "complete") {
			//URL = https://api-endpointsalvasen.benefitportal.com/api/fulfillment/:id/complete
			$fulfillment_id = $param1;
			$request_file = "complete_fulfillment.php";

		} elseif(!empty($param1) && $param1 == "new") {
			//URL = https://api-endpointsalvasen.benefitportal.com/api/fulfillment/pending
			$request_file = "new_fulfillment.php";

		}
		
	} elseif($requested_api == 'carrier') {

		//URL = https://api-endpointsalvasen.benefitportal.com/api/carrier
		$request_file = "carrier.php";	

	} elseif($requested_api == 'eligibility') {
		if(!empty($param2)) {
			//URL = https://api-endpointsalvasen.benefitportal.com/api/eligibility/:carrierCode/:effectiveDate
			$carrier_id = $param1;
			$effective_date = date('Y-m-d',strtotime($param2));
			$run_date = !empty($param3) ? date('Y-m-d',strtotime($param3)) : "";
			$request_file = "eligibility.php";
		} 

	}elseif($requested_api == 'termination') {
		if(!empty($param1)) {
			//URL = https://api-endpointsalvasen.benefitportal.com/api/termination/:carrierCode/:fromDate/:toDate
			$carrier_id = $param1;
			if(!empty($param2)){
				$from_date = date('Y-m-d',strtotime($param2));
			}
			if(!empty($param3)){
				$to_date = date('Y-m-d',strtotime($param3));
			}
			$request_file = "termination.php";
		} 

	}

	if(!empty($request_file)) {
		include_once $request_file;
	} else {
		$response = array(
			'success' => $fail_value,
			'message' => "Your requested api not found"
		);
		return_response($fail_value,$response,405);	
	}
?>