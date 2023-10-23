<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$validate = new Validation();
$is_address_ajaxed = checkIsset($_POST['is_address_ajaxed']);
if($is_address_ajaxed){
	$response = array("status"=>'success');
    $address = $_POST['bill_address'];
    $address_2 = checkIsset($_POST['bill_address_2']);
    $city = $_POST['bill_city'];
    $state = checkIsset($_POST['bill_state']);
    $zipcode = $_POST['bill_zip'];
    $old_address = $_POST['old_bill_address'];
    $old_zip = $_POST['old_bill_zip'];

    $validate->digit(array('required' => true, 'field' => 'bill_zip', 'value' => $zipcode,'min'=> 5,'max'=>5 ), array('required' => 'Zip Code is required'));
    $validate->string(array('required' => true, 'field' => 'bill_address', 'value' => $address), array('required' => 'Address is required'));
    if($validate->isValid()){
    	include_once '../includes/function.class.php';
      $function_list = new functionsList();
      $zipAddress = $function_list->uspsCityVerification($zipcode);

      if($old_address != $address || $zipcode!=$old_zip ||  $getStateNameByShortName[$zipAddress['state']] !=$state){
          if($zipAddress['status'] =='success'){
              $response['city'] = $zipAddress['city'];
              $response['state'] = $getStateNameByShortName[$zipAddress['state']];
              $response['zip_response_status']='success';

              $tmpAdd1=$address;
              $tmpAdd2=!empty($address_2) ? $address_2 : '#';
              $address_response = $function_list->uspsAddressVerification($tmpAdd1,$tmpAdd2,$zipAddress['city'],$getStateNameByShortName[$zipAddress['state']],$zipcode);

              if(!empty($address_response)){
                  if($address_response['status']=='success'){
                      $response['address'] = $address_response['address'];
                      $response['address2'] = $address_response['address2'];
                      $response['city'] = $address_response['city'];
                      $response['state'] = $getStateNameByShortName[$address_response['state']];
                      $response['enteredAddress']= $address .' '.$address_2 .'</br>'.$address_response['city'].', '.$address_response['state'] . ' '.$zipcode;
                      $response['suggestedAddress']=$address_response['address'] .' '.$address_response['address2'] .'</br>'.$address_response['city'].', '.$address_response['state'] . ' '.$address_response['zip'];
                      $response['zip_response_status']='';
                      $response['address_response_status']='success';
                  }
              }
          }else if($zipAddress['status'] =='fail'){
              $response['status'] = 'fail';
              $response['errors'] = array("bill_zip"=>$zipAddress['error_message']);
          }
      }
    }else{
    	$errors = $validate->getErrors();
      	$response['status'] = 'fail';
      	$response['errors'] = $errors;

    }
    header('Content-type: application/json');
    echo json_encode($response);
    exit();
}

$response = array();
$customer_id = $_POST['group_id'];
$company_id = $_POST['company_id'];
$company_name = $_POST['company_name'];
$payment_mode = isset($_POST['payment_mode'])?$_POST['payment_mode']:"";

$REAL_IP_ADDRESS = get_real_ipaddress();
if($payment_mode=='CC'){
	$name_on_card = checkIsset($_POST['name_on_card']);
	$card_number = checkIsset($_POST['card_number']);
	$card_type = isset($_POST['card_type'])?$_POST['card_type']:"";
	$expiration = isset($_POST['expiration'])?$_POST['expiration']:"";
	$expiry_month = '';
	$expiry_year = '';
	if(!empty($expiration)){
		$expirtation_details = explode("/", $expiration);
		$expiry_month = $expirtation_details[0];
		$expiry_year = $expirtation_details[1];
	}
	$cvv_no = isset($_POST['cvv_no'])?$_POST['cvv_no']:"";

} elseif($payment_mode=='ACH') {
	$ach_name = checkIsset($_POST['ach_name']);
	$ach_bill_fname = checkIsset($_POST['ach_bill_fname']);
	$ach_bill_lname = checkIsset($_POST['ach_bill_lname']);
	$bankname = checkIsset($_POST['bankname']);
	$ach_account_type = isset($_POST['ach_account_type'])?$_POST['ach_account_type']:"";
	$routing_number = checkIsset($_POST['routing_number']);
	$account_number = checkIsset($_POST['account_number']);
}

if($payment_mode=='CC' || $payment_mode=='ACH'){
	$bill_address = checkIsset($_POST['bill_address']);
	$bill_address_2 = checkIsset($_POST['bill_address_2']);
	$bill_city = checkIsset($_POST['bill_city']);
	$bill_country = 231;
	$bill_state = checkIsset($_POST['bill_state']);
	$bill_zip = checkIsset($_POST['bill_zip']);
}

$validate->string(array('required' => true, 'field' => 'payment_mode', 'value' => $payment_mode), 	array('required' => 'Payment Mode is required'));

if ($payment_mode == "CC") {
	$validate->string(array('required' => true, 'field' => 'name_on_card', 'value' => $name_on_card), array('required' => 'Name is required'));

	$validate->string(array('required' => true, 'field' => 'card_type', 'value' => $card_type), array('required' => 'Select Card Type'));
	$validate->digit(array('required' => true, 'field' => 'card_number', 'value' => $card_number,"max"=>$MAX_CARD_NUMBER,"min"=>$MIN_CARD_NUMBER), array('required' => 'Card is required', 'invalid' => "Enter valid Card Number"));

	$validate->string(array('required' => true, 'field' => 'expiration', 'value' => $expiration), array('required' => 'Please select expiration month and year'));

	$validate->digit(array('required' => true, 'field' => 'cvv_no', 'value' => $cvv_no), array('required' => 'CVV is required', 'invalid' => "Enter valid CVV"));

	if (!$validate->getError("name_on_card") && !ctype_alnum(str_replace(" ","",$name_on_card))) {
		$validate->setError("name_on_card","Enter Valid Name");
	}

	if(!$validate->getError("card_number") && !is_valid_luhn($card_number,$card_type)){
		$validate->setError("card_number","Enter valid Credit Card Number");
	}

	if(!$validate->getError("cvv_no") && !cvv_type_pair($cvv_no,$card_type)){
        $validate->setError("cvv_no","Invalid CVV Number");
    }

	if (!$validate->getError("expiration")) {
		$expirty_date = $expiry_year.'-'.$expiry_month.'-01';

		if(strtotime($expirty_date) <= strtotime(date('Y-m-d'))){
			$validate->setError("expiration","Valid Expiry Date is required");
		}
	}
}

if ($payment_mode == "ACH") {
	$validate->string(array('required' => true, 'field' => 'ach_name', 'value' => $ach_name,'max'=>22), array('required' => 'Name is required'));
	$validate->digit(array('required' => true, 'field' => 'account_number', 'value' => $account_number,'min'=>$MIN_ACCOUNT_NUMBER,'max'=>$MAX_ACCOUNT_NUMBER), array('required' => 'Account number is required', 'invalid' => "Enter valid Account number"));
	$validate->digit(array('required' => true, 'field' => 'routing_number', 'value' => $routing_number), array('required' => 'Routing number is required', 'invalid' => "Enter valid Routing number"));
	if (!$validate->getError("routing_number")) {
		if (checkRoutingNumber($routing_number) == false) {
			$validate->setError("routing_number", "Enter valid routing number");
		}
	}
	$validate->string(array('required' => true, 'field' => 'ach_account_type', 'value' => $ach_account_type), array('required' => 'Account Type is required'));
	$validate->string(array('required' => true, 'field' => 'bankname', 'value' => $bankname,'max'=>50), array('required' => 'Bank Name is required'));
	if (!$validate->getError("ach_name") && !ctype_alnum(str_replace(" ","",$ach_name))) {
		$validate->setError("ach_name","Enter Valid Name");
	}
}

$validate->string(array('required' => true, 'field' => 'bill_address', 'value' => $bill_address), array('required' => 'Address is required'));
if(!empty($bill_address_2) && preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/',$bill_address_2)) {
    $validate->setError('bill_address_2','Special character not allowed');
}
$validate->string(array('required' => true, 'field' => 'bill_city', 'value' => $bill_city), array('required' => 'City is required'));
$validate->string(array('required' => true, 'field' => 'bill_state', 'value' => $bill_state), array('required' => 'State is required'));
$validate->digit(array('required' => true, 'field' => 'bill_zip', 'value' => $bill_zip,"min"=>5,"max"=>5), array('required' => 'Zip is required'));

if (!$validate->getError('bill_zip')){
    include_once '../includes/function.class.php';
    $function_list = new functionsList();
    $zipAddress = $function_list->uspsCityVerification($bill_zip);
    if($zipAddress['status'] !='success'){
        $validate->setError("bill_zip",$zipAddress['error_message']);
    }
}

if ($validate->isValid()) {
	$cust_row = $pdo->selectOne("SELECT id,rep_id,fname,lname,email FROM customer WHERE id=:id",array(":id"=>$customer_id));
	$bill_data = array(
		'customer_id' => $customer_id,
		'company_id'=>$company_id,
		'fname' => $cust_row['fname'],
		'lname' => $cust_row['lname'],
		'email' => makeSafe($cust_row['email']),
		'country_id' => 231,
		'country' => 'United States',
		'state' => makeSafe($bill_state),
		'city' => makeSafe($bill_city),
		'zip' => makeSafe($bill_zip),
		'address' => makeSafe($bill_address),
		'address2' => makeSafe($bill_address_2),
		'created_at' => 'msqlfunc_NOW()',
		'updated_at' => 'msqlfunc_NOW()',
		'payment_mode' => $payment_mode,
		'ip_address' => !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'],
        'listbill_enroll' => 'Y',
	);

	if ($payment_mode == "CC") {
		$bill_data = array_merge($bill_data,array(
			'fname' => makeSafe($name_on_card),
			'lname' => '',
			'cvv_no' => makeSafe($cvv_no),
			'card_no' => makeSafe(substr($card_number, -4)),
			'last_cc_ach_no' => makeSafe(substr($card_number, -4)),
			'card_no_full' => "msqlfunc_AES_ENCRYPT('" . $card_number . "','" . $CREDIT_CARD_ENC_KEY . "')",
			'card_type' => makeSafe($card_type),
			'expiry_month' => makeSafe($expiry_month),
			'expiry_year' => makeSafe($expiry_year),
		));
	} elseif($payment_mode == "ACH") {
		$bill_data = array_merge($bill_data,array(
			'fname' => makeSafe($ach_name),
			'lname' => '',
			'ach_account_type' => $ach_account_type,
			'bankname' => $bankname,
			'last_cc_ach_no' => makeSafe(substr($account_number, -4)),
			'ach_account_number' => "msqlfunc_AES_ENCRYPT('" . $account_number . "','" . $CREDIT_CARD_ENC_KEY . "')",
			'ach_routing_number' => "msqlfunc_AES_ENCRYPT('" . $routing_number . "','" . $CREDIT_CARD_ENC_KEY . "')",
		));
	}
	$billing_id = $pdo->insert("customer_billing_profile", $bill_data);

	$payment_method_text = '';
	if($bill_data['payment_mode'] == "CC") {
        $payment_method_text = $bill_data['card_type']." *".$bill_data['last_cc_ach_no'];

    } else if($bill_data['payment_mode'] == "ACH") {
        $payment_method_text = "ACH *".$bill_data['last_cc_ach_no'];

    } else if($bill_data['payment_mode'] == "Check") {
        $payment_method_text = "Check";
    }

	$af_data = array();
	$af_data['ac_message'] = array(
        'ac_red_1'=> array(
            'href'=> $GROUP_HOST.'/groups_details.php?id='.md5($customer_id),
            'title'=> $cust_row['rep_id'],
        ),
        'ac_message_1' => " added new billing profile ".$payment_method_text,
    ); 
    activity_feed(3,$customer_id,'Group',$customer_id,'Group','Group Billing Profile',"","",json_encode($af_data));

	$response['status'] = 'success';
	$response['msg'] = 'Billing profile saved successfully!';
	$response['option_html'] = '<option value="'.$billing_id.'">'.$payment_method_text.'</option>';
	$response['billing_id'] = $billing_id;
} else {
    $errors = $validate->getErrors();
    $response['status'] = 'fail';
    $response['errors'] = $errors;
}
echo json_encode($response);
dbConnectionClose();
exit();
?>