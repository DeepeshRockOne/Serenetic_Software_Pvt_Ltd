<?php
include_once 'includes/connect.php';
include_once __DIR__ . '/../includes/function.class.php';
$activity_description = $response = array();
$activity_update = false;
$validate = new Validation();
$BROWSER = getBrowser();
$OS = getOS($_SERVER['HTTP_USER_AGENT']);
$REQ_URL = ($_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
$processor_id = '';
// global variables code start
	$btn_clicked = $_POST['btn_clicked'];
	$payment_master_id = !empty($_POST['payment_master_id']) ? $_POST['payment_master_id'] : '';
	$type = !empty($_POST['type']) ? $_POST['type'] : '';
	$processor_name = !empty($_POST['processor_name']) ? $_POST['processor_name'] : '';
	$merchant_id = !empty($_POST['merchant_id']) ? $_POST['merchant_id'] : '';

	$gateway_id = !empty($_POST['gateway_id']) ? $_POST['gateway_id'] : '';
	$gateway_name = !empty($_POST['gateway_name']) ? $_POST['gateway_name'] : '';
	$api_key = !empty($_POST['api_key']) ? $_POST['api_key'] : '';
	$service_key = !empty($_POST['service_key']) ? $_POST['service_key'] : '';
	$api_pin = !empty($_POST['api_pin']) ? $_POST['api_pin'] : '';
	$transaction_key = !empty($_POST['transaction_key']) ? $_POST['transaction_key'] : '';
	$user_name = !empty($_POST['user_name']) ? $_POST['user_name'] : '';
	$password = !empty($_POST['password']) ? $_POST['password'] : '';
	$login_id = !empty($_POST['login_id']) ? $_POST['login_id'] : '';
	$secret_key = !empty($_POST['secret_key']) ? $_POST['secret_key'] : '';

	$url = !empty($_POST['url']) ? $_POST['url'] : '';
	$new_url = !empty($_POST['new_url']) ? $_POST['new_url'] : '';

	$description = !empty($_POST['description']) ? $_POST['description'] : '';
	$accept_ach_value = !empty($_POST['accept_ach_value']) ? (($_POST['accept_ach_value'] == 'Yes') ? 'Y' : 'N') : 'N';
	$is_accept_ach_default = !empty($_POST['is_accept_ach_default']) ? (($_POST['is_accept_ach_default'] == 'on') ? 'Y' : 'N') : 'N';
	$accept_cc_value = !empty($_POST['accept_cc_value']) ? (($_POST['accept_cc_value'] == 'Yes') ? 'Y' : 'N') : 'N';
	$is_accept_cc_default = !empty($_POST['is_accept_cc_default']) ? (($_POST['is_accept_cc_default'] == 'on') ? 'Y' : 'N') : 'N';
	
	$require_cvv = !empty($_POST['require_cvv']) ? (($_POST['require_cvv'] == 'on') ? 'Y' : 'N') : 'N';
	$monthly_threshold_sale = !empty($_POST['monthly_threshold_sale']) ? (str_replace(array("$",","),array("",""), $_POST['monthly_threshold_sale'])) : '';
	$sale_threshold = !empty($_POST['sale_threshold']) ? $_POST['sale_threshold'] : 'No';
	$sale_threshold_alert = !empty($_POST['sale_threshold_alert']) ? $_POST['sale_threshold_alert'] : '';
	$refund_threshold = !empty($_POST['refund_threshold']) ? $_POST['refund_threshold'] : 'No';
	$refund_threshold_alert = !empty($_POST['refund_threshold_alert']) ? $_POST['refund_threshold_alert'] : '';
	$chargeback_threshold = !empty($_POST['chargeback_threshold']) ? $_POST['chargeback_threshold'] : 'No';
	$chargeback_threshold_alert = !empty($_POST['chargeback_threshold_alert']) ? $_POST['chargeback_threshold_alert'] : '';
	$acceptable_cc = !empty($_POST['acceptable_cc']) ? $_POST['acceptable_cc'] : array();
// global variables code ends

// variation variables code start
	$assinged_to_agent = !empty($_POST['assinged_to_agent']) ? $_POST['assinged_to_agent'] : '';
	$agents = !empty($_POST['agents']) ? $_POST['agents'] : array();
	$agents_downline = !empty($_POST['agents_downline']) ? $_POST['agents_downline'] : array();
	$agents_loa = !empty($_POST['agents_loa']) ? $_POST['agents_loa'] : array();
	$assinged_to_product = !empty($_POST['assinged_to_product']) ? $_POST['assinged_to_product'] : '';
	$products = !empty($_POST['products']) ? $_POST['products'] : array();
	$products_variation = !empty($_POST['products_variation']) ? $_POST['products_variation'] : array();
// variation variables code ends

// validation code starts
	$validate->string(array('required' => true, 'field' => 'processor_name', 'value' => $processor_name), array('required' => 'Processor Name is required'));
	$validate->string(array('required' => true, 'field' => 'merchant_id', 'value' => $merchant_id), array('required' => 'Processor id is required'));
	$validate->string(array('required' => true, 'field' => 'gateway_name', 'value' => $gateway_name), array('required' => 'Gateway Name is required'));
	if(!empty($gateway_id) && !empty($gateway_name)){
		// Authorize
		if($gateway_id == 1){
			$validate->string(array('required' => true, 'field' => 'transaction_key', 'value' => $transaction_key), array('required' => 'Transaction key is required'));
			$validate->string(array('required' => true, 'field' => 'login_id', 'value' => $login_id), array('required' => 'Login Id is required'));
		}else if($gateway_id == 2 || $gateway_id == 3) {
			// NMI or C&H
			$validate->string(array('required' => true, 'field' => 'user_name', 'value' => $user_name), array('required' => 'User Name is required'));
			$validate->string(array('required' => true, 'field' => 'api_key', 'value' => $api_key), array('required' => 'Api key is required'));
			$validate->string(array('required' => true, 'field' => 'password', 'value' => $password), array('required' => 'Password is required'));
		}elseif ($gateway_id == 4) {
			// USAePay
			$validate->string(array('required' => true, 'field' => 'user_name', 'value' => $user_name), array('required' => 'User Name is required'));
			$validate->string(array('required' => true, 'field' => 'api_pin', 'value' => $api_pin), array('required' => 'Api pin is required'));
			$validate->string(array('required' => true, 'field' => 'api_key', 'value' => $api_key), array('required' => 'Api key is required'));
		}elseif ($gateway_id == 5) {
			// PayByCliq
			$validate->string(array('required' => true, 'field' => 'user_name', 'value' => $user_name), array('required' => 'User Name is required'));
			$validate->string(array('required' => true, 'field' => 'service_key', 'value' => $service_key), array('required' => 'Service key is required'));
			$validate->string(array('required' => true, 'field' => 'password', 'value' => $password), array('required' => 'Password is required'));
		
		}elseif ($gateway_id == 6) {
			// CyberSource
			$validate->string(array('required' => true, 'field' => 'api_key', 'value' => $api_key), array('required' => 'Api key is required'));
			$validate->string(array('required' => true, 'field' => 'secret_key', 'value' => $secret_key), array('required' => 'Secret key is required'));
		}
	}

	$validate->string(array('required' => true, 'field' => 'url', 'value' => $url), array('required' => 'URL is required'));
	if(!empty($url) && $url == 'new_url'){
		$validate->string(array('required' => true, 'field' => 'new_url', 'value' => $new_url), array('required' => 'URL is required'));
		if(!empty($new_url)){
			if (!filter_var($new_url, FILTER_VALIDATE_URL)) {
				$validate->setError('new_url', "Please enter Valid URL");
			}
		}
	}

	if(!($monthly_threshold_sale > 0)){
		$validate->setError('monthly_threshold_sale', "Monthly Threshold Sale is required");
	}
	// $validate->string(array('required' => true, 'field' => 'monthly_threshold_sale', 'value' => $monthly_threshold_sale), array('required' => 'Monthly Threshold Sale is required'));
	$validate->string(array('required' => true, 'field' => 'accept_ach_value', 'value' => $accept_ach_value), array('required' => 'Please select anyone option'));
	$validate->string(array('required' => true, 'field' => 'accept_cc_value', 'value' => $accept_cc_value), array('required' => 'Please select anyone option'));
	$validate->string(array('required' => true, 'field' => 'sale_threshold', 'value' => $sale_threshold), array('required' => 'Please select anyone option'));
	$validate->string(array('required' => true, 'field' => 'refund_threshold', 'value' => $refund_threshold), array('required' => 'Please select anyone option'));
	$validate->string(array('required' => true, 'field' => 'chargeback_threshold', 'value' => $chargeback_threshold), array('required' => 'Please select anyone option'));


	// if($accept_cc_value == 'Y' && empty($acceptable_cc) && $type=='Global') {
	// 	$validate->setError("acceptable_cc","Please select ant option.");
	// }	

	if(!empty($sale_threshold) && $sale_threshold == 'Yes'){
		$validate->digit(array('required' => true, 'field' => 'sale_threshold_alert', 'value' => $sale_threshold_alert), array('required' => 'This field is required', 'invalid' => "Please Enter valid Number."));

		if(empty($validate->getError('sale_threshold_alert'))) {
			$message = numberInput($sale_threshold_alert);
			if(!empty($message)){
				$validate->setError("sale_threshold_alert",$message);
			}
		}	
	}

	
	

	if(!empty($refund_threshold) && $refund_threshold == 'Yes'){
		$validate->digit(array('required' => true, 'field' => 'refund_threshold_alert', 'value' => $refund_threshold_alert), array('required' => 'This field is required', 'invalid' => "Please Enter valid Number."));

		if(empty($validate->getError('refund_threshold_alert'))) {
			$message = numberInput($refund_threshold_alert);
			if(!empty($message)){
				$validate->setError("refund_threshold_alert",$message);
			}
		}

	}

	if(!empty($chargeback_threshold) && $chargeback_threshold == 'Yes'){
		$validate->digit(array('required' => true, 'field' => 'chargeback_threshold_alert', 'value' => $chargeback_threshold_alert), array('required' => 'This field is required', 'invalid' => "Please Enter valid Number."));

		if(empty($validate->getError('chargeback_threshold_alert'))) {
			$message = numberInput($chargeback_threshold_alert);
			if(!empty($message)){
				$validate->setError("chargeback_threshold_alert",$message);
			}
		}

	}

	if($type == 'Variation'){
		$validate->string(array('required' => true, 'field' => 'assinged_to_agent', 'value' => $assinged_to_agent), array('required' => 'Please select one option'));
		if(!empty($assinged_to_agent) && $assinged_to_agent == 'selected' && empty($agents)){
			$validate->setError('agents','Please select one option');
		}
		$validate->string(array('required' => true, 'field' => 'assinged_to_product', 'value' => $assinged_to_product), array('required' => 'Please select one option'));
		if(!empty($assinged_to_product) && $assinged_to_product == 'selected' && empty($products)){
			$validate->setError('products','Please select one option');
		}
	}

	$status = 'Inactive';
	if($btn_clicked == 'C'){
		$status = 'Active';
	}
// validation code ends

if ($validate->isValid()) {

	if(!empty($payment_master_id)){
		$edit_res = $pdo->selectOne("SELECT * FROM payment_master WHERE is_deleted = 'N' AND type = :type AND md5(id) = :id",array(":type" => $type,":id" => $payment_master_id));
	}

	$live_details = array();
	if($url != 'new_url'){
		$live_details['url'] = $url;
	} else {
		$live_details['url'] = $new_url;
	}
	// Authorize
	if($gateway_id == 1){
		$live_details["transaction_key"] = $transaction_key;
		$live_details["login_id"] = $login_id;
	}else if($gateway_id == 2 || $gateway_id == 3){
		// NMI or C&H
		$live_details["user_name"] = $user_name;
		$live_details["password"] = $password;
		$live_details["api_key"] = $api_key;
	}elseif ($gateway_id == 4) {
		// USAEPay
		$live_details["user_name"] = $user_name;
		$live_details["api_pin"] = $api_pin;
		$live_details["api_key"] = $api_key;

	}elseif ($gateway_id == 5) {
		// PayByCliq
		$live_details["merchant_id"] = $merchant_id;
		$live_details["user_name"] = $user_name;
		$live_details["password"] = $password;
		$live_details["service_key"] = $service_key;

	}elseif ($gateway_id == 6) {
		// CyberSource
		$live_details["merchant_id"] = $merchant_id;
		$live_details["api_key"] = $api_key;
		$live_details["secret_key"] = $secret_key;
	}

	$sandbox_details = '';
	if(!empty($gateway_id)){
		$gateway_sandbox_details_res = $pdo->selectOne("SELECT processor_id,sandbox_details FROM payment_gateway_details WHERE id = :id", array(":id" => $gateway_id));
		if(!empty($gateway_sandbox_details_res)){
			if($gateway_id == 2 || $gateway_id == 3){
				$merchant_sandbox_details = array();
				$gateway_sandbox_details_tbl = json_decode($gateway_sandbox_details_res['sandbox_details'],true);
				$merchant_sandbox_details['url'] = $live_details['url'];
				$merchant_sandbox_details['user_name'] = $gateway_sandbox_details_tbl['user_name'];
				$merchant_sandbox_details['password'] = $gateway_sandbox_details_tbl['password'];
				$merchant_sandbox_details['api_key'] = $live_details["api_key"];
				$sandbox_details = json_encode($merchant_sandbox_details);
			}else{
				$sandbox_details = $gateway_sandbox_details_res['sandbox_details'];
			}
			$processor_id =  $gateway_sandbox_details_res['processor_id'];
		}
	}
		
	if($accept_ach_value != 'Y'){
		$is_accept_ach_default = 'N';
	}

	if($accept_cc_value != 'Y'){
		$is_accept_cc_default = 'N';
	}

	$insert_param = array(
		'name' => $processor_name,
		'merchant_id' => $merchant_id,
		'gateway_id' => $gateway_id,
		'gateway_name' => $gateway_name,
		'live_details' => json_encode($live_details),
		'is_ach_accepted' => $accept_ach_value,
		'is_default_for_ach' => $is_accept_ach_default,
		'is_cc_accepted' => $accept_cc_value,
		'require_cvv' => $require_cvv,
		'is_default_for_cc' => $is_accept_cc_default,
		'monthly_threshold_sale' => $monthly_threshold_sale,
		'type' => $type,
		'description' => $description,
		'updated_at' => 'msqlfunc_NOW()',
		'created_at' => 'msqlfunc_NOW()',
	);

	if($accept_cc_value == 'Y' && !empty($acceptable_cc)){
		$insert_param['acceptable_cc'] = implode(',',$acceptable_cc);
	}

	if(!empty($user_name)){
		$insert_param['merchant_user_name'] = $user_name;
	}

	if(!empty($processor_id)){
		$insert_param['processor_id'] = $processor_id;
	}

	if(!empty($sandbox_details)){
		$insert_param['sandbox_details'] = $sandbox_details;
	}
	if(empty($payment_master_id)){
		$insert_param['status'] = $status;
	}
	if($sale_threshold == 'Yes'){
		$insert_param['is_sales_threshold'] = 'Y';
		$insert_param['sales_threshold_value'] = $sale_threshold_alert;
	} else {
		$insert_param['is_sales_threshold'] = 'N';
		$insert_param['sales_threshold_value'] = 0;
	}
	if($refund_threshold == 'Yes'){
		$insert_param['is_refund_threshold'] = 'Y';
		$insert_param['refund_threshold_value'] = $refund_threshold_alert;
	} else {
		$insert_param['is_refund_threshold'] = 'N';
		$insert_param['refund_threshold_value'] = 0;
	}
	if($chargeback_threshold == 'Yes'){
		$insert_param['is_chargeback_threshold'] = 'Y';
		$insert_param['chargeback_threshold_value'] = $chargeback_threshold_alert;
	} else {
		$insert_param['is_chargeback_threshold'] = 'N';
		$insert_param['chargeback_threshold_value'] = 0;
	}

	if($type == 'Variation'){
		if($assinged_to_agent == 'all') {
			$insert_param['is_assigned_to_all_agent'] = 'Y';
			// $insert_param['assigned_agent'] = NULL;
			// $insert_param['agents_downline'] = NULL;
			// $insert_param['agents_loa'] = NULL;
		} else {
			$insert_param['is_assigned_to_all_agent'] = 'N';
			$agents_downline_arr = array();
			if(!empty($agents_downline) && count($agents_downline) > 0){
				foreach ($agents_downline as $key => $value) {
					if(!in_array($key, $agents_downline_arr)){
						array_push($agents_downline_arr, $key);
						$downline_res = $pdo->select("SELECT id FROM customer WHERE upline_sponsors LIKE :upline_sponsors AND is_deleted = 'N' AND type = 'Agent'", array(':upline_sponsors' => "%," . $key . ",%"));
						if(count($downline_res) > 0){
							foreach ($downline_res as $index => $ele) {
								if(!in_array($ele['id'], $agents)){
									array_push($agents, $ele['id']);
								}
							}
						}
					}
				}
			}

			$agents_loa_arr = array();
			if(!empty($agents_loa) && count($agents_loa) > 0){
				foreach ($agents_loa as $key => $value) {
					if(!in_array($key, $agents_loa_arr)){
						array_push($agents_loa_arr, $key);
						$loa_res = $pdo->select("SELECT c.id FROM customer c LEFT JOIN customer_settings cs ON(cs.customer_id=c.id) WHERE sponsor_id=:sponsor_id AND agent_coded_level=:type AND is_deleted = 'N' AND type = 'Agent'", array(':sponsor_id' => $key ,':type'=>'LOA'));
						if(count($loa_res) > 0){
							foreach ($loa_res as $index => $ele) {
								if(!in_array($ele['id'], $agents)){
									array_push($agents, $ele['id']);
								}
							}
						}
					}
				}
			}
		}
		if($assinged_to_product == 'all') {
			$insert_param['is_assigned_to_all_product'] = 'Y';
			// $insert_param['assigned_product'] = NULL;
			// $insert_param['products_variation'] = NULL;
		} else {
			$insert_param['is_assigned_to_all_product'] = 'N';
		}
	} else {
		$insert_param['is_assigned_to_all_agent'] = 'Y';
		// $insert_param['assigned_agent'] = NULL;
		// $insert_param['agents_downline'] = NULL;
		$insert_param['is_assigned_to_all_product'] = 'Y';
		// $insert_param['assigned_product'] = NULL;
		// $insert_param['products_variation'] = NULL;
	}

		$newOrderBy = 0;
		$getLastOrderBy = $pdo->selectOne("SELECT order_by FROM payment_master WHERE is_deleted = 'N' AND type = :type AND order_by NOT IN(0,1) ORDER BY order_by DESC",array(":type" => $type));
		if(!empty($getLastOrderBy)){
			$newOrderBy = $getLastOrderBy['order_by'] + 1;
		}else{
			$newOrderBy = 2;
		}

		if(empty($payment_master_id)){
			$insert_param['order_by'] = $newOrderBy;
		}

		if(!empty($edit_res) && $edit_res['is_default_for_cc'] == 'Y' && $is_accept_cc_default == 'N'){
			$insert_param['order_by'] = $newOrderBy;
		}

		if(!empty($edit_res) && $edit_res['is_default_for_ach'] == 'Y' && $is_accept_ach_default == 'N'){
			$insert_param['order_by'] = $newOrderBy;
		}

	if($is_accept_ach_default == 'Y'){
		$perious_res = $pdo->selectOne("SELECT id,is_default_for_cc FROM payment_master WHERE is_deleted = 'N' AND type = :type AND is_default_for_ach = :is_accept_ach_default",array(":type" => $type,":is_accept_ach_default" => $is_accept_ach_default));
		if(!empty($perious_res)){
			$per_upd_where = array(
				'clause' => 'id = :id',
				'params' => array(
					':id' => $perious_res['id'],
				),
			);
			$upd_params = array(
							"is_default_for_ach" => 'N',
							"updated_at" => "msqlfunc_NOW()");
			if($perious_res['is_default_for_cc'] == 'Y'){
					$upd_params['order_by'] = 0;
			}else if(!empty($edit_res['order_by']) && !in_array($edit_res['order_by'],array(0,1))){
				$upd_params['order_by'] = $edit_res['order_by'];
			}else{
				$upd_params['order_by'] = $newOrderBy;
			}
			$pdo->update('payment_master',$upd_params,$per_upd_where);
		}
			$insert_param['order_by'] = 1;
	}

	if($is_accept_cc_default == 'Y'){
		$perious_res = $pdo->selectOne("SELECT id,is_default_for_ach FROM payment_master WHERE is_deleted = 'N' AND type = :type AND is_default_for_cc = :is_accept_cc_default",array(":type" => $type,":is_accept_cc_default" => $is_accept_cc_default));

		if(!empty($perious_res)){
			$per_upd_where = array(
				'clause' => 'id = :id',
				'params' => array(
					':id' => $perious_res['id'],
				),
			);
			$upd_params = array(
							"is_default_for_cc" => 'N',
							"updated_at" => "msqlfunc_NOW()");
			if($perious_res['is_default_for_ach'] == 'Y'){
				$upd_params['order_by'] = 1;
			}else if(!empty($edit_res['order_by']) && !in_array($edit_res['order_by'],array(0,1))){
				$upd_params['order_by'] = $edit_res['order_by'];
			}else{
				$upd_params['order_by'] = $newOrderBy;
			}
			$pdo->update('payment_master',$upd_params,$per_upd_where);
		}
		$insert_param['order_by'] = 0;
	}

	if($is_accept_ach_default == 'Y' && $is_accept_cc_default == 'Y'){
		$insert_param['order_by'] = 0;	
	}

	$function_list = new functionsList();
	if(!empty($payment_master_id)){

		$loa_del = $downline_del = $downline_upline = $insert_products = $inserted_agents = $insert_agents = $delete_agents = $delete_products = array();
		if(!empty($agents) && count($agents) > 0 && $insert_param['is_assigned_to_all_agent'] == 'N'){
			// $insert_param['assigned_agent'] = implode(",", $agents);
			$assigned_agent_param = array();
			$asigned_agents = $pdo->selectOne("SELECT GROUP_CONCAT(DISTINCT(agent_id)) as agent_ids FROM  payment_master_assigned_agent  WHERE payment_master_id=:id and is_deleted='N' AND status!='Deleted'",array(":id" => $edit_res['id']));
			$asigned_agents_loa = $pdo->selectOne("SELECT GROUP_CONCAT(DISTINCT(agent_id)) as agent_ids FROM  payment_master_assigned_agent  WHERE payment_master_id=:id and is_deleted='N' and loa_only='Y' AND status!='Deleted'",array(":id" => $edit_res['id']));
			$asigned_agents_downline = $pdo->selectOne("SELECT GROUP_CONCAT(DISTINCT(agent_id)) as agent_ids FROM  payment_master_assigned_agent  WHERE payment_master_id=:id and is_deleted='N' and include_downline='Y' AND status!='Deleted'",array(":id" => $edit_res['id']));

			$insert_agents = $agents;
			if(!empty($asigned_agents['agent_ids']))
				$delete_agents = array_diff(explode(',',$asigned_agents['agent_ids']),$agents);
			if(!empty($asigned_agents['agent_ids']))
				$inserted_agents = $insert_agents = array_diff($agents,explode(',',$asigned_agents['agent_ids']));

			if(!empty($asigned_agents_loa['agent_ids'])){
				$loa_del = array_diff(explode(',',$asigned_agents_loa['agent_ids']),$agents_loa_arr);
				if(!empty($loa_del)){
					$downline_upline[] = insert_update_loa_downline($loa_del,$agents_loa_arr,$agents_downline_arr,$edit_res['id']);
				}
			}

			if(!empty($asigned_agents_loa['agent_ids']))
				$agents_loa_arr = array_diff($agents_loa_arr,explode(',',$asigned_agents_loa['agent_ids']));

			if(!empty($asigned_agents_downline['agent_ids'])){
				$downline_del = array_diff(explode(',',$asigned_agents_downline['agent_ids']),$agents_downline_arr);
				if(!empty($downline_del)){
					$downline_upline[] = insert_update_loa_downline($downline_del,$agents_loa_arr,$agents_downline_arr,$edit_res['id']);
				}
			}
				
			if(!empty($asigned_agents_downline['agent_ids']))
				$agents_downline_arr = array_diff($agents_downline_arr,explode(',',$asigned_agents_downline['agent_ids']));

			if(!empty($delete_agents)){
				$delete_where = array(
					'clause' => ' agent_id IN('.implode(',',$delete_agents).') and payment_master_id=:id AND is_deleted="N"',
					'params' => array(":id"=>$edit_res['id'])
				);
				$pdo->update('payment_master_assigned_agent',array("is_deleted"=>'Y'),$delete_where);
			}
			if(!empty($agents_loa_arr) && empty($insert_agents)){
				$insert_agents1 = $agents_loa_arr;
				$downline_upline[] = insert_update_loa_downline($insert_agents1,$agents_loa_arr,$agents_downline_arr,$edit_res['id']);
			}
			if(!empty($agents_downline_arr) && empty($insert_agents)){
				$insert_agents1 = $agents_downline_arr;
				$downline_upline[] = insert_update_loa_downline($insert_agents1,$agents_loa_arr,$agents_downline_arr,$edit_res['id']);
			}
			if(!empty($insert_agents)){
				$downline_upline[] = insert_update_loa_downline($insert_agents,$agents_loa_arr,$agents_downline_arr,$edit_res['id']);
			}
		}else if($insert_param['is_assigned_to_all_agent'] == 'Y'){
			$all_agents = $pdo->select("SELECT c.id from customer c 
										WHERE c.is_deleted='N' AND c.id NOT IN(SELECT pmaa.agent_id from payment_master_assigned_agent pmaa JOIN payment_master p ON(p.id=pmaa.payment_master_id AND p.is_deleted='N') WHERE md5(p.id)=:payment_master_id AND pmaa.is_deleted='N') AND c.type='Agent'",array(":payment_master_id"=>$payment_master_id));

			if(!empty($all_agents)){
				$assigned_agent_param['payment_master_id'] = $edit_res['id'];
				foreach($all_agents as $ag){
					$assigned_agent_param['agent_id'] = $ag['id'];
					$pdo->insert('payment_master_assigned_agent',$assigned_agent_param);
				}
			}
		}
		// else{
		// 	$delete_where = array(
		// 		'clause' => ' payment_master_id=:id  AND is_deleted="N" ',
		// 		'params' => array(":id"=>$edit_res['id'])
		// 	);
		// 	$pdo->update('payment_master_assigned_agent',array("is_deleted"=>'Y'),$delete_where);
		// }

		if(!empty($products) && count($products) > 0 && $insert_param['is_assigned_to_all_product'] == 'N'){

			$assigned_product_param = array();
			$asigned_products = $pdo->selectOne("SELECT GROUP_CONCAT(DISTINCT(product_id)) as product_ids FROM  payment_master_assigned_product  WHERE payment_master_id=:id and is_deleted='N'",array(":id" => $edit_res['id']));
			$insert_products = $products;
			if(!empty($asigned_products['product_ids']))
				$delete_products = array_diff(explode(',',$asigned_products['product_ids']),$products);
			if(!empty($asigned_products['product_ids']))
				$insert_products = array_diff($products,explode(',',$asigned_products['product_ids']));

			if(!empty($delete_products)){
				$delete_where = array(
					'clause' => ' product_id IN('.implode(',',$delete_products).') AND payment_master_id=:id  AND is_deleted="N"',
					'params' => array(":id"=>$edit_res['id'])
				);
				$pdo->update('payment_master_assigned_product',array("is_deleted"=>'Y'),$delete_where);
			}

			if(!empty($insert_products)){
				foreach($insert_products as $product){
					$product_id = $pdo->selectOne('SELECT id from payment_master_assigned_product where product_id=:id AND payment_master_id=:payment_id and is_deleted="N"',array(":id"=>$product,':payment_id'=>$edit_res['id']));
	
					$assigned_product_param['product_id'] = $product;
					$assigned_product_param['payment_master_id'] = $edit_res['id'];

					if(!empty($product_id['id'])){
						$pdo->update('payment_master_assigned_product','',$update_where);
					}else{
						$assigned_product_param['product_id'] = $product;
						$pdo->insert('payment_master_assigned_product',$assigned_product_param);
					}
				}
			}
		}else if($insert_param['is_assigned_to_all_product'] == 'Y'){
			$delete_where = array(
				'clause' => ' payment_master_id=:id  AND is_deleted="N" ',
				'params' => array(":id"=>$edit_res['id'])
			);
			$pdo->update('payment_master_assigned_product',array("is_deleted"=>'Y','updated_at'=>'msqlfunc_NOW()'),$delete_where);
		}

		unset($insert_param['created_at']);
		$upd_where = array(
			'clause' => 'md5(id) = :id',
			'params' => array(
				':id' => $payment_master_id,
			),
		);
		$payment_master = $pdo->update('payment_master', $insert_param, $upd_where,true);
		if(!empty($payment_master)){
			$activity_description['ac_message'] =array(
				'ac_red_1'=>array(
				  'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
				  'title'=>$_SESSION['admin']['display_id'],
				),
				'ac_message_1' =>'  Updated Merchant Processor ',
				'ac_red_2'=>array(
				  'href'=> $ADMIN_HOST.'/add_merchant_processor.php?type='.$type.'&id='.$payment_master_id,
				  'title'=> $insert_param['name'],
				)
			  );
			if(!empty($delete_products)){
				$del_products = $pdo->selectOne("SELECT GROUP_CONCAT(CONCAT(name,' (',product_code,')')) as name from prd_main where id in(".implode(',',$delete_products).")");

				$activity_description['key_value']['desc_arr']['assigned_product_unselected'] = $del_products['name'].' Unselected';
				$activity_update = true;
			}
			if(!empty($insert_products)){
				$del_agents = $pdo->selectOne("SELECT GROUP_CONCAT(CONCAT(name,' (',product_code,')')) as name from prd_main where id in(".implode(',',$insert_products).")");

				$activity_description['key_value']['desc_arr']['assigned_product_selected'] = $del_agents['name'].' Selected';
				$activity_update = true;
			}

			if(!empty($delete_agents)){
				$del_agents = $pdo->selectOne("SELECT GROUP_CONCAT(CONCAT(fname,' ',lname,' (',rep_id,')')) as name from customer where type='Agent' and id in(".implode(',',$delete_agents).")");

				$activity_description['key_value']['desc_arr']['assigned_agent_unselected'] = $del_agents['name'].' Unselected';
				$activity_update = true;
			}
			if(!empty($downline_upline)){
				$loa_update_agent_selected =$loa_update_agent_unselected = $downline_update_agent_unselected = $downline_update_agent_selected = array();
				foreach($downline_upline as $downline_upline_arr){
					if(!empty($downline_upline_arr)){
						foreach($downline_upline_arr as $key => $arr){
							if(!empty($arr)){
								foreach($arr as $key1 => $value){
									$value = $value == 'N' ? "selected" : "unselected";
									if((in_array($key,$agents_loa_arr) || in_array($key,$loa_del)) && $key1 == 'loa_only'){
										if($value == "selected")
											$loa_update_agent_selected[] = $key;
										else 
											$loa_update_agent_unselected[] = $key;
									}
									if((in_array($key,$agents_downline_arr)  || in_array($key,$downline_del)) && $key1 == 'include_downline'){
										if($value == "selected")
											$downline_update_agent_selected[] = $key;
										else
											$downline_update_agent_unselected[] = $key;
									}
								}
							}
						}
					}
				}
				if(!empty($loa_update_agent_selected)){
					$loa_update_se = $pdo->selectOne("SELECT GROUP_CONCAT(CONCAT(fname,' ',lname,' (',rep_id,')')) as name from customer where type='Agent' and id in(".implode(',',$loa_update_agent_selected).")");
					$activity_description['key_value']['desc_arr']['agents_loa_selected'] = $loa_update_se['name'].' Selected';
					$activity_update = true;
				}
				if(!empty($loa_update_agent_unselected)){
					$loa_update_un = $pdo->selectOne("SELECT GROUP_CONCAT(CONCAT(fname,' ',lname,' (',rep_id,')')) as name from customer where type='Agent' and id in(".implode(',',$loa_update_agent_unselected).")");
					$activity_description['key_value']['desc_arr']['agents_loa_unselected'] = $loa_update_un['name'].' Unselected';
					$activity_update = true;
				}
				if(!empty($downline_update_agent_selected)){
					$loa_update_se = $pdo->selectOne("SELECT GROUP_CONCAT(CONCAT(fname,' ',lname,' (',rep_id,')')) as name from customer where type='Agent' and id in(".implode(',',$downline_update_agent_selected).")");
					$activity_description['key_value']['desc_arr']['agents_downline_selected'] = $loa_update_se['name'].' Selected';
					$activity_update = true;
				}
				if(!empty($downline_update_agent_unselected)){
					$loa_update_un = $pdo->selectOne("SELECT GROUP_CONCAT(CONCAT(fname,' ',lname,' (',rep_id,')')) as name from customer where type='Agent' and id in(".implode(',',$downline_update_agent_unselected).")");
					$activity_description['key_value']['desc_arr']['agents_downline_unselected'] = $loa_update_un['name'].' Unselected';
					$activity_update = true;
				}
			}

			if(!empty($inserted_agents)){
				$ins_agents = $pdo->selectOne("SELECT GROUP_CONCAT(CONCAT(fname,' ',lname,' (',rep_id,')')) as name from customer where type='Agent' and id in(".implode(',',$inserted_agents).")");
				$activity_description['key_value']['desc_arr']['assigned_agent_selected'] = $ins_agents['name'].' Selected';
				$activity_update = true;
			}
			
			foreach($payment_master as $key => $value){
				if(in_array($key,array('order_by','sandbox_details')) && !empty($value)){
					$activity_description['key_value']['desc_arr'][$key] = 'updated.';
					$activity_update = true;
				}elseif($value == 'Y' || $value == 'N'){

					$value = $value == 'Y' ? "selected" : "unselected";
					$insert_param[$key] = $insert_param[$key] == 'Y' ? "selected" : "unselected";

					$activity_description['key_value']['desc_arr'][$key] = 'updated from '.$value.' to '.$insert_param[$key];
					$activity_update = true;
				}else{
					if(!empty($value) && !in_array($key,array('live_details','gateway_id','processor_id','order_by'))){
						$activity_description['key_value']['desc_arr'][$key] = 'updated from '.$value.' to '.$insert_param[$key];
						$activity_update = true;
					}
				}
			}		
		}
		// Database details code start
		$databaseFields = $edit_res;
		if(!empty($databaseFields['live_details'])){
			$databaseFields_live_details_arr = json_decode($databaseFields['live_details'], true);
			if(!empty($databaseFields_live_details_arr)){
				foreach ($databaseFields_live_details_arr as $key => $value) {
					$databaseFields["live_".$key."_details"] = $value;
				}
				unset($databaseFields['live_details']);
			}
		}
		if(!empty($databaseFields['sandbox_details'])){
			$databaseFields_sandbox_details_arr = json_decode($databaseFields['sandbox_details'], true);
			if(!empty($databaseFields_sandbox_details_arr)){
				foreach ($databaseFields_sandbox_details_arr as $key => $value) {
					$databaseFields["sandbox_".$key."_details"] = $value;
				}
				unset($databaseFields['sandbox_details']);
			}
		}
		// Database details code end

		// Update Param details code start
		$updatedFields = $insert_param;
		$live_details_arr = json_decode($insert_param['live_details'],true);
		$sandbox_details_arr = json_decode($insert_param['sandbox_details'],true);

		if(!empty($live_details_arr)){
			foreach ($live_details_arr as $key => $value) {
				$updatedFields["live_".$key."_details"] = $value;
			}
			unset($updatedFields['live_details']);
		}
		if(!empty($sandbox_details_arr)){
			foreach ($sandbox_details_arr as $key => $value) {
				$updatedFields["sandbox_".$key."_details"] = $value;
			}
			unset($updatedFields['sandbox_details']);
		}

		// Update Param details code end
		$function_list->get_updated_payment_field($edit_res['id'], 'N', $updatedFields, $databaseFields);

		if($activity_update){
			activity_feed(3, $_SESSION['admin']['id'], 'Admin',  $_SESSION['admin']['id'], 'Admin', 'Merchant Processor Updated',  $_SESSION['admin']['fname'],  $_SESSION['admin']['lname'], json_encode($activity_description));
		}
		$insert_payment_id = $payment_master_id;
		$response['status'] = 'Success';
	} else {
		$insert_param['processor_code'] = $function_list->generateMerchantProcessorDisplayID();
		unset($insert_param['updated_at']);
		$insert_payment_id = $pdo->insert("payment_master", $insert_param);
		
		
		if($insert_param['is_assigned_to_all_agent'] == 'Y'){
			$all_agents = $pdo->select("SELECT id from customer where is_deleted='N' AND type='Agent'");
			if(!empty($all_agents)){
				$assigned_agent_param['payment_master_id'] = $insert_payment_id;
				foreach($all_agents as $ag){
					$assigned_agent_param['agent_id'] = $ag['id'];
					$pdo->insert('payment_master_assigned_agent',$assigned_agent_param);
				}
			}
		}else if($insert_param['is_assigned_to_all_agent'] != 'Y' && !empty($agents) && count($agents) > 0 && $insert_param['is_assigned_to_all_agent'] == 'N'){
			foreach($agents as $agent){
				$assigned_agent_param['agent_id'] = $agent;
				$assigned_agent_param['payment_master_id'] = $insert_payment_id;
				if(in_array($agent,$agents_downline_arr)){
					$assigned_agent_param['include_downline'] = 'Y';
				}else{
					$assigned_agent_param['include_downline'] = 'N';
				}
				if(in_array($agent,$agents_loa_arr)){
					$assigned_agent_param['loa_only'] = 'Y';
				}else{
					$assigned_agent_param['loa_only'] = 'N';
				}
				$assigned_agent_param['agent_id'] = $agent;
				$pdo->insert('payment_master_assigned_agent',$assigned_agent_param);
			}
		} 

		if(!empty($products) && count($products) > 0 && $insert_param['is_assigned_to_all_product'] == 'N'){

			$assigned_product_param = array();
			if(!empty($products)){
				foreach($products as $product){
					$product_id = $pdo->selectOne('SELECT id from payment_master_assigned_product where product_id=:id and is_deleted="N"',array(":id"=>$product));
	
					$assigned_product_param['product_id'] = $product;
					$assigned_product_param['payment_master_id'] = $insert_payment_id;

					if(!empty($product_id['id'])){
						$pdo->update('payment_master_assigned_product','',$update_where);
					}else{
						$assigned_product_param['product_id'] = $product;
						$pdo->insert('payment_master_assigned_product',$assigned_product_param);
					}
				}
			}
		}

		// Update Param detials start
		$updatedFields = $insert_param;
		$live_details_arr = json_decode($insert_param['live_details'],true);
		$sandbox_details_arr = json_decode($insert_param['sandbox_details'],true);
		
		if(!empty($live_details_arr)){
			foreach ($live_details_arr as $key => $value) {
				$updatedFields["live_".$key."_details"] = $value;
			}
			unset($updatedFields['live_details']);
		}
		if(!empty($sandbox_details_arr)){
			foreach ($sandbox_details_arr as $key => $value) {
				$updatedFields["sandbox_".$key."_details"] = $value;
			}
			unset($updatedFields['sandbox_details']);
		}
		// Update Param detials end
		$function_list->get_updated_payment_field($insert_payment_id, 'Y', $updatedFields, array());
		
		$response['status'] = 'create';

		$activity_description['ac_message'] =array(
			'ac_red_1'=>array(
			  'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
			  'title'=>$_SESSION['admin']['display_id'],
			),
			'ac_message_1' =>'  Created New Merchant Processor ',
			'ac_red_2'=>array(
			  'href'=> $ADMIN_HOST.'/add_merchant_processor.php?type='.$type.'&id='.md5($insert_payment_id),
			  'title'=> $insert_param['name'],
			)
		  );
		activity_feed(3, $_SESSION['admin']['id'], 'Admin',  $_SESSION['admin']['id'], 'Admin', 'Merchant Processor Created',  $_SESSION['admin']['fname'],  $_SESSION['admin']['lname'], json_encode($activity_description));
		$insert_payment_id = md5($insert_payment_id);
	}
	$response['pay_master_id'] = $insert_payment_id;
} else {
	$response['status'] = 'fail';
}

function numberInput($sale_threshold){
	if($sale_threshold < 0){
		return "Please Enter Positive number.";
	}else if($sale_threshold == 0){
		return "Please Enter Greater then number 0.";
	}else if($sale_threshold >= 100){
		return "Please Enter Less then number 100.";
	}
}

function insert_update_loa_downline($insert_agents,$agents_loa_arr,$agents_downline_arr,$payment_master_id){
	global $pdo;
	$insert_agents1 = $insert_agents;
	$assigned_agent_param = $downline_upline = array();
	foreach($insert_agents1 as $agent){
		$agnet_id = $pdo->selectOne('SELECT id from payment_master_assigned_agent where agent_id=:id and payment_master_id=:payment_id and is_deleted="N"',array(":id"=>$agent,':payment_id'=>$payment_master_id));

		$assigned_agent_param['agent_id'] = $agent;
		$assigned_agent_param['payment_master_id'] = $payment_master_id;
		if(in_array($agent,$agents_downline_arr)){
			$assigned_agent_param['include_downline'] = 'Y';
		}else{
			$assigned_agent_param['include_downline'] = 'N';
		}
		if(in_array($agent,$agents_loa_arr)){
			$assigned_agent_param['loa_only'] = 'Y';
		}else{
			$assigned_agent_param['loa_only'] = 'N';
		}
		$assigned_agent_param['status'] ='Active';
		if(!empty($agnet_id['id'])){
			$update_where = array(
				'clause' => 'id=:id',
				'params' => array(":id"=>$agnet_id['id'])
			);
			$downline_upline[$agent] = $pdo->update('payment_master_assigned_agent',$assigned_agent_param,$update_where,true);
		}else{
			$assigned_agent_param['agent_id'] = $agent;
			$pdo->insert('payment_master_assigned_agent',$assigned_agent_param);
		}
	}
	return $downline_upline;
}

header('Content-type: application/json');
$errors = $validate->getErrors();
$response['errors'] = $errors;
echo json_encode($response);
dbConnectionClose();
exit;
?>