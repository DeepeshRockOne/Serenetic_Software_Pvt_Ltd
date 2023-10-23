<?php
include_once __DIR__ . '/layout/start.inc.php';
include_once dirname(__DIR__) . '/includes/cyberx_payment_class.php';
$payment_master_id = $_GET['pay_id'];

$payment_master_res = $pdo->selectOne("SELECT * FROM payment_master WHERE md5(id) = :id", array(":id" => $payment_master_id));

$month_names = array("January","February","March","April","May","June","July","August","September","October","November","December");

$BROWSER = getBrowser();
$OS = getOS($_SERVER['HTTP_USER_AGENT']);
$REQ_URL = ($_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);

$REAL_IP_ADDRESS = get_real_ipaddress();
if (!empty($_POST)) {
	$response = array();
	$validate = new Validation();

	$payment_mode = checkIsset($_POST['payment_mode']);
	$fname_on_card = checkIsset($_POST['fname_on_card']);
	$lname_on_card = checkIsset($_POST['lname_on_card']);
	$card_type = checkIsset($_POST['card_type']);
	$card_number = checkIsset($_POST['card_number']);
	$bill_month = checkIsset($_POST['bill_month']);
	$bill_year = checkIsset($_POST['bill_year']);
	$card_amount = checkIsset($_POST['card_amount']);
	$cvv_no = checkIsset($_POST['cvv_no']);
	$address1 = checkIsset($_POST['address1']);
	$address2 = checkIsset($_POST['address2']);
	$city = checkIsset($_POST['city']);
	$state = checkIsset($_POST['state']);
	$zip = checkIsset($_POST['zip']);


	//Billing Information Bank Draft
    $ach_name = checkIsset($_POST['ach_name']);
    $bank_name = checkIsset($_POST['bank_name']);
    $account_type = checkIsset($_POST['account_type']);
    $routing_number = checkIsset($_POST['routing_number']);
	$account_number = checkIsset($_POST['account_number']);
	
	if(empty($payment_mode)){
		$validate->setError("payment_mode","Please select any Payment Method");
	}

	if($payment_mode == 'CC'){
		$validate->string(array('required' => true, 'field' => 'fname_on_card', 'value' => $fname_on_card), array('required' => 'First Name on card is required'));
		$validate->string(array('required' => true, 'field' => 'lname_on_card', 'value' => $lname_on_card), array('required' => 'Last Name on card is required'));
		$validate->string(array('required' => true, 'field' => 'card_type', 'value' => $card_type), array('required' => 'Credit card type is required'));
		$validate->digit(array('required' => true, 'field' => 'card_number', 'value' => $card_number,"max"=>$MAX_CARD_NUMBER,"min"=>$MIN_CARD_NUMBER), array('required' => 'Card is required', 'invalid' => "Enter valid Card Number"));
		$validate->string(array('required' => true, 'field' => 'bill_month', 'value' => $bill_month), array('required' => 'Month is required'));
		$validate->string(array('required' => true, 'field' => 'bill_year', 'value' => $bill_year), array('required' => 'Year is required'));

		if(!$validate->getError("card_number") && !is_valid_luhn($card_number,$card_type)){
            $validate->setError("card_number","Enter valid Credit Card Number");
        }

		if (!$validate->getError("fname_on_card") && !ctype_alnum(str_replace(" ","",$fname_on_card))) {
			$validate->setError("fname_on_card","Enter Valid First Name");
		}

		if (!$validate->getError("lname_on_card") && !ctype_alnum(str_replace(" ","",$lname_on_card))) {
			$validate->setError("lname_on_card","Enter Valid Last Name");
		}

		if(checkIsset($payment_master_res["require_cvv"]) == "Y" || $cvv_no!=''){
			$validate->digit(array('required' => true, 'field' => 'cvv_no', 'value' => $cvv_no), array('required' => 'CVV is required', 'invalid' => "Enter valid CVV"));

			if(!$validate->getError("cvv_no") && !cvv_type_pair($cvv_no,$card_type)){
				$validate->setError("cvv_no","Invalid CVV Number");
			} 
		}
	}else{
		$validate->string(array('required' => true, 'field' => 'bank_name', 'value' => $bank_name), array('required' => 'Bank Name is required'));
		$validate->string(array('required' => true, 'field' => 'ach_name', 'value' => $ach_name), array('required' => 'Full Name is required'));
		$validate->string(array('required' => true, 'field' => 'account_type', 'value' => $account_type), array('required' => 'Account Type is required'));
		$validate->digit(array('required' => true, 'field' => 'account_number', 'value' => $account_number,'min'=>$MIN_ACCOUNT_NUMBER,'max'=>$MAX_ACCOUNT_NUMBER), array('required' => 'Account number is required', 'invalid' => "Enter valid Account number"));
		$validate->digit(array('required' => true, 'field' => 'routing_number', 'value' => $routing_number), array('required' => 'Routing number is required', 'invalid' => "Enter valid Routing number"));
		if (!$validate->getError("routing_number")) {
			if (checkRoutingNumber($routing_number) == false) {
				$validate->setError("routing_number", "Enter valid routing number");
			}
		}
		if (!$validate->getError("ach_name") && !ctype_alnum(str_replace(" ","",$ach_name))) {
			$validate->setError("ach_name","Enter Valid Name");
		}
	}

	/*
	$validate->string(array('required' => true, 'field' => 'address1', 'value' => $address1), array('required' => 'Address1 is required'));
	$validate->string(array('required' => true, 'field' => 'city', 'value' => $city), array('required' => 'City is required'));
	$validate->string(array('required' => true, 'field' => 'state', 'value' => $state), array('required' => 'State is required'));
	$validate->string(array('required' => true, 'field' => 'zip', 'value' => $zip), array('required' => 'Zip Code is required'));
	*/
	$validate->string(array('required' => true, 'field' => 'card_amount', 'value' => $card_amount), array('required' => 'Amount are required'));
	
	if ($validate->isValid()) {
		$api = new CyberxPaymentAPI();

		$payment_id = $payment_master_res["id"];
		$payment_error = '';
		
		$cc_params = array();
		$cc_params['order_id'] = rand(1111111,9999999);
		$cc_params['amount'] = $card_amount;
		$cc_params['description'] = "Test Processor";
		$cc_params['firstname'] = checkIsset($fname_on_card);
		$cc_params['lastname'] = checkIsset($lname_on_card);
		$cc_params['ipaddress'] = !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'];
		$cc_params['processor'] = $payment_master_res['gateway_name'];
		$cc_params['email'] = 'pramit@siddhisai.net';
		$cc_params['fname'] = '';
		$cc_params['lname'] = '';
		$cc_params['address1'] = !empty($address1) ? $address1 : '18350 Mt Langley';
		$cc_params['address2'] = !empty($address2) ? $address2 : '';
		$cc_params['city'] = !empty($city) ? $city : 'Fountain Valley';
		$cc_params['state'] = !empty($state) ? $state : 'CA';
		$cc_params['zip'] = !empty($zip) ? $zip : '92708';
		$cc_params['phone'] = '';

		/*$payment_mode = "CC";
		$card_number = "4111111111111114";*/

		$payment_test_insert_param = array(
			'admin_id' => $_SESSION['admin']['id'],
			'payment_mode' => $payment_mode,
			'payment_master_id' => $payment_id,
			'fname' => $fname_on_card,
			'lname' => $lname_on_card,
			'card_type' => $card_type,
			'card_number' => "msqlfunc_AES_ENCRYPT('" . $card_number . "','" . $CREDIT_CARD_ENC_KEY . "')",
			'exp_month' => $bill_month,
			'name_on_account' => $ach_name,
			'bankname' => $bank_name,
			'ach_account_type' => $account_type,
			'ach_routing_number' =>  !empty($routing_number) ? "msqlfunc_AES_ENCRYPT('" . $routing_number . "','" . $CREDIT_CARD_ENC_KEY . "')" : '',
			'ach_account_number' =>  !empty($account_number) ? "msqlfunc_AES_ENCRYPT('" . $account_number . "','" . $CREDIT_CARD_ENC_KEY . "')" : '',
			'exp_year' => $bill_year,
			'cvv_no' => $cvv_no,
			'amount' => $card_amount,
			'ip_address' => !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'],
			'updated_at' => 'msqlfunc_NOW()',
			'created_at' => 'msqlfunc_NOW()',
		);

		if ($payment_mode == "CC") {
			$cc_params['ccnumber'] = !empty($card_number) ? $card_number : $card_number;
			$cc_params['card_type'] = $card_type;
			$cc_params['ccexp'] = str_pad($bill_month, 2, "0", STR_PAD_LEFT) . substr($bill_year, -2);
			if(!empty($cvv_no)){
				$cc_params['cvv'] = $cvv_no;
			}
			if ($cc_params['ccnumber'] == '4111111111111114') {
				$paymentApproved = true;
				$txn_id = 0;
				$payment_res = array("status" => "Success","transaction_id" => 0,"message" => "Manual Approved");
			} else {
				$payment_res = $api->processPayment($cc_params, $payment_id);	
				if ($payment_res['status'] == 'Success') {
					$paymentApproved = true;
					$txn_id = $payment_res['transaction_id'];
				} else {
					$paymentApproved = false;
					$txn_id = $payment_res['transaction_id'];
					$payment_error = $payment_res['message'];
					$cc_params['transaction_id'] = $payment_res['transaction_id'];
					$cc_params['order_type'] = 'Test Processor';
					$cc_params['browser'] = $BROWSER;
					$cc_params['os'] = $OS;
					$cc_params['req_url'] = $REQ_URL;
					$cc_params['err_text'] = $payment_error;
					credit_card_payment_decline_log($_SESSION['admin']['id'], $cc_params, $payment_res,'CC');
				}
			}
		}else{
			$cc_params['firstname'] = 'Test';
			$cc_params['lastname'] = 'Test';
			$cc_params['ach_account_type'] = $account_type;
			$cc_params['ach_routing_number'] = $routing_number;
			$cc_params['ach_account_number'] = $account_number;
			$cc_params['name_on_account'] = $ach_name;
			$cc_params['bankname'] = $bank_name;
			$cc_params['ip_address'] = !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'];
			$payment_res = $api->processPaymentACH($cc_params, $payment_id);

			if ($payment_res['status'] == 'Success') {
				$paymentApproved = true;
				$txn_id = $payment_res['transaction_id'];
			} else {
				$paymentApproved = false;
				$txn_id = $payment_res['transaction_id'];
				$payment_error = $payment_res['message'];
				$cc_params['transaction_id'] = $payment_res['transaction_id'];
				$cc_params['order_type'] = 'Test Processor';
				$cc_params['browser'] = $BROWSER;
				$cc_params['os'] = $OS;
				$cc_params['req_url'] = $REQ_URL;
				$cc_params['err_text'] = $payment_error;
				credit_card_payment_decline_log($_SESSION['admin']['id'], $cc_params, $payment_res,'ACH');
			}
		}

		$payment_test_insert_param['payment_status'] = ($payment_res['status'] == 'Success') ? $payment_res['status'] : 'Fail';
		$payment_test_insert_param['transaction_id'] = $payment_res['transaction_id'];
		$payment_test_insert_param['decline_text'] = $payment_error;
		$payment_test_insert_param['cc_params'] = json_encode($cc_params);
		insert_into_payment_master_test_connection($payment_test_insert_param);
		$response['status'] = 'success';
		$response['payment_error'] = $payment_error;
		$response['payment_status'] = $payment_res['status'];

		$activity_description['ac_message'] =array(
			'ac_red_1'=>array(
			  'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
			  'title'=>$_SESSION['admin']['display_id'],
			),
			'ac_message_1' =>'  Test Merchant Processor ',
			'ac_red_2'=>array(
			  'href'=> $ADMIN_HOST.'/add_merchant_processor.php?type='.$payment_master_res['type'].'&id='.md5($payment_master_res['id']),
			  'title'=> $payment_master_res['name'],
			)
		  );
		$activity_description['description'] = ' using following details :';
		foreach($payment_test_insert_param as $key => $value){
			if(!in_array($key,array('card_number','payment_mode','admin_id','cc_params','updated_at','created_at','amount')))
				$activity_description['key_value']['desc_arr'][$key] = $value;
			elseif($key == 'card_number')
				$activity_description['key_value']['desc_arr']['last_four_digit'] = substr($card_number, -4);
			elseif($key=='amount')
				$activity_description['key_value']['desc_arr'][$key] = displayAmount($value);
		}

		activity_feed(3, $_SESSION['admin']['id'], 'Admin',  $_SESSION['admin']['id'], 'Admin', 'Test Merchant Processor',  $_SESSION['admin']['fname'],  $_SESSION['admin']['lname'], json_encode($activity_description));

	} else {
		$response['status'] = 'fail';
        $response['errors'] = $validate->getErrors();
	}
	echo json_encode($response);
    exit();
}

$exJs = array('thirdparty/ajax_form/jquery.form.js','thirdparty/price_format/jquery.price_format.2.0.js');

$template = 'connect_processor_popup.inc.php';
include_once 'layout/iframe.layout.php';
include_once 'tmpl/' . $template;

function insert_into_payment_master_test_connection($insert_param){
	global $pdo;
	$insert_payment_test_id = 0;
	if(!empty($insert_param)){
		$insert_payment_test_id = $pdo->insert("payment_master_test_connection", $insert_param);
	}

	return $insert_payment_test_id;
}

function credit_card_payment_decline_log($admin_id, $cc_params, $res,$payment_mode) {

	global $pdo;
	global $CREDIT_CARD_ENC_KEY;
	global $CC_DECLINE_EMAIL;
	$response = json_encode($res);
	$decline_text = $cc_params['err_text'];

	$insParams = array(
		'admin_id' => $admin_id,
		'payment_mode' => $payment_mode,
		'last_cc_ach_no' => '',
		'amount' => $cc_params['amount'],
		'transaction_id' => $cc_params['transaction_id'],
		'decline_text' => makeSafe($decline_text),
		'response' => $response,
		'ip_address' => (isset($cc_params['ipaddress']) ? $cc_params['ipaddress'] : ""),
		'browser' => $cc_params['browser'],
		'os' => $cc_params['os'],
		'req_url' => $cc_params['req_url'],
		'created_at' => 'msqlfunc_NOW()',
	);
	if($payment_mode == 'CC'){
		$insParams['name_on_card'] = ($cc_params['firstname'] . ' ' . $cc_params['lastname']);
		$insParams['card_no'] = substr($cc_params['ccnumber'], -4);
		$insParams['cvv_no'] = checkIsset($cc_params['cvv_no']);
		$insParams['last_cc_ach_no'] = substr($cc_params['ccnumber'], -4);
		$insParams['card_no_full'] = "msqlfunc_AES_ENCRYPT('" . $cc_params['ccnumber'] . "','" . $CREDIT_CARD_ENC_KEY . "')";
		$insParams['card_type'] = $cc_params['card_type'];
		$insParams['card_expiry'] = $cc_params['ccexp'];
	}else{

		$insParams['last_cc_ach_no'] = substr($cc_params['ach_account_number'], -4);
		$insParams['name_on_account'] = $cc_params['name_on_account'];
		$insParams['bankname'] = $cc_params['bankname'];
		$insParams['ach_account_type'] = $cc_params['ach_account_type'];
		$insParams['ach_routing_number'] = "msqlfunc_AES_ENCRYPT('" . $cc_params['ach_routing_number'] . "','" . $CREDIT_CARD_ENC_KEY . "')";
		$insParams['ach_account_number'] = "msqlfunc_AES_ENCRYPT('" . $cc_params['ach_account_number'] . "','" . $CREDIT_CARD_ENC_KEY . "')";
	}
	
	$pdo->insert('payment_master_test_connection_decline_log', $insParams);
}

?>