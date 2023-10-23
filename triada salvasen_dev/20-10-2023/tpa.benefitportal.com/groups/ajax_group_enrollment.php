<?php
include_once __DIR__ . '/includes/connect.php';
include_once __DIR__ . '/../includes/trigger.class.php';
include_once __DIR__ . '/../includes/function.class.php';
require __DIR__ . '/../libs/awsSDK/vendor/autoload.php';

use Aws\S3\S3Client;  
use Aws\Exception\AwsException;
use Aws\Credentials\CredentialProvider;

$TriggerMailSms = new TriggerMailSms();
$functionsList = new functionsList();

$BROWSER = getBrowser();
$OS = getOS($_SERVER['HTTP_USER_AGENT']);
$REQ_URL = ($_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);

$submit_type = isset($_POST['submit_type'])?$_POST['submit_type']:"";
$action = isset($_POST['action'])?$_POST['action']:"";
$step = isset($_POST['dataStep'])?$_POST['dataStep']:"";
$payment_type = !empty($_POST['payment_type']) ? $_POST['payment_type'] : '';

// USPS API code start
$is_address_ajaxed = checkIsset($_POST['is_address_ajaxed']);
$REAL_IP_ADDRESS = get_real_ipaddress();
if($is_address_ajaxed && $submit_type!='auto_save' && $step >= 1){

    $response = array("status"=>'success');
    $address = $_POST['business_address'];
    $address_2 = checkIsset($_POST['business_address_2']);
    $city = $_POST['city'];
    $state = checkIsset($_POST['state']);
    $zip = $_POST['zipcode'];
    $old_address = $_POST['old_business_address'];
    $old_zip = $_POST['old_zipcode'];

    $validate = new Validation();

    $validate->digit(array('required' => true, 'field' => 'zipcode', 'value' => $zip,'min'=> 5,'max'=>5 ), array('required' => 'Zip Code is required'));

    $validate->string(array('required' => true, 'field' => 'business_address', 'value' => $address), array('required' => 'Address is required'));
    if($validate->isValid()){
        include_once '../includes/function.class.php';
        $function_list = new functionsList();
        $zipAddress = $function_list->uspsCityVerification($zip);

        if($old_address != $address || $zip!=$old_zip ||  $getStateNameByShortName[$zipAddress['state']] !=$state){
            
            if($zipAddress['status'] =='success'){
            	$response['type'] = '';
                $response['city'] = $zipAddress['city'];
                $response['state'] = $getStateNameByShortName[$zipAddress['state']];
                $response['zip_response_status']='success';

                $tmpAdd1=$address;
                $tmpAdd2=!empty($address_2) ? $address_2 : '#';
                $address_response = $function_list->uspsAddressVerification($tmpAdd1,$tmpAdd2,$zipAddress['city'],$getStateNameByShortName[$zipAddress['state']],$zip);
                
                if(!empty($address_response)){
                    if($address_response['status']=='success'){
                        $response['address'] = $address_response['address'];
                        $response['address2'] = $address_response['address2'];
                        $response['city'] = $address_response['city'];
                        $response['state'] = $getStateNameByShortName[$address_response['state']];
                        $response['enteredAddress']= $address .' '.$address_2 .'</br>'.$address_response['city'].', '.$address_response['state'] . ' '.$zip;
                        $response['suggestedAddress']=$address_response['address'] .' '.$address_response['address2'] .'</br>'.$address_response['city'].', '.$address_response['state'] . ' '.$address_response['zip'];
                        $response['zip_response_status']='';
                        $response['address_response_status']='success';
                    }
                }
            }else if($zipAddress['status'] =='fail'){
                $response['status'] = 'fail';
                $response['errors'] = array("zipcode"=>$zipAddress['error_message']);
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
$is_bill_address_ajaxed = checkIsset($_POST['is_bill_address_ajaxed']);
if($is_bill_address_ajaxed && $submit_type!='auto_save' && $step >= 2 && $payment_type=='CC'){

    $response = array("status"=>'success');
    $address = $_POST['bill_address'];
    $address_2 = checkIsset($_POST['bill_address_2']);
    $city = $_POST['bill_city'];
    $state = checkIsset($_POST['bill_state']);
    $zip = $_POST['bill_zip'];
    $old_address = $_POST['old_bill_address'];
    $old_zip = $_POST['old_bill_zip'];

    $validate = new Validation();

    $validate->digit(array('required' => true, 'field' => 'bill_zip', 'value' => $zip,'min'=> 5,'max'=>5 ), array('required' => 'Zip Code is required'));

    $validate->string(array('required' => true, 'field' => 'bill_address', 'value' => $address), array('required' => 'Address is required'));
    if($validate->isValid()){
        include_once '../includes/function.class.php';
        $function_list = new functionsList();
        $zipAddress = $function_list->uspsCityVerification($zip);

        if($old_address != $address || $zip!=$old_zip ||  $getStateNameByShortName[$zipAddress['state']] !=$state){
            
            if($zipAddress['status'] =='success'){
            	$response['type']='billing';
                $response['city'] = $zipAddress['city'];
                $response['state'] = $getStateNameByShortName[$zipAddress['state']];
                $response['zip_response_status']='success';

                $tmpAdd1=$address;
                $tmpAdd2=!empty($address_2) ? $address_2 : '#';
                $address_response = $function_list->uspsAddressVerification($tmpAdd1,$tmpAdd2,$zipAddress['city'],$getStateNameByShortName[$zipAddress['state']],$zip);
                
                if(!empty($address_response)){
                    if($address_response['status']=='success'){
                        $response['address'] = $address_response['address'];
                        $response['address2'] = $address_response['address2'];
                        $response['city'] = $address_response['city'];
                        $response['state'] = $getStateNameByShortName[$address_response['state']];
                        $response['enteredAddress']= $address .' '.$address_2 .'</br>'.$address_response['city'].', '.$address_response['state'] . ' '.$zip;
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
$validate = new Validation();

$today_date=date('Y-m-d');

$sponsor_id = isset($_POST['sponsor_id'])?$_POST['sponsor_id']:"";
$group_id = isset($_POST["group_id"]) ? $_POST["group_id"] : 0;

$response['step']=$step;
$response['submit_type']=$submit_type;
$response['action']=$action;

//********** step1 varible intialization code start **********************
	$display_in_member = !empty($_POST['display_in_member']) ? $_POST['display_in_member'] : 'N';
	
	$group_name = !empty($_POST['group_name']) ? $_POST['group_name'] : '';
	$business_address = !empty($_POST['business_address']) ? $_POST['business_address'] : '';
	$business_address_2 = !empty($_POST['business_address_2']) ? $_POST['business_address_2'] : '';
	$is_valid_address = !empty($_POST['is_valid_address']) ? $_POST['is_valid_address'] : '';
	$city = !empty($_POST['city']) ? $_POST['city'] : '';
	$state = !empty($_POST['state']) ? $_POST['state'] : '';
	$zipcode = !empty($_POST['zipcode']) ? $_POST['zipcode'] : '';
	$business_phone = !empty($_POST['business_phone']) ? $_POST['business_phone'] : '';
	$business_phone = phoneReplaceMain($business_phone);
	$business_email = checkIsset($_POST['business_email']);
	$no_of_employee = !empty($_POST['no_of_employee']) ? $_POST['no_of_employee'] : '';
	$years_in_business = !empty($_POST['years_in_business']) ? $_POST['years_in_business'] : '';
	$ein = !empty($_POST['ein']) ? $_POST['ein'] : '';
	$ein = phoneReplaceMain($ein);
	$nature_of_business = !empty($_POST['nature_of_business']) ? $_POST['nature_of_business'] : '';
	$sic_code = !empty($_POST['sic_code']) ? $_POST['sic_code'] : '';
	$fname = !empty($_POST['fname']) ? $_POST['fname'] : '';
	$lname = !empty($_POST['lname']) ? $_POST['lname'] : '';
	$phone = !empty($_POST['phone']) ? $_POST['phone'] : '';
	$phone = phoneReplaceMain($phone);
	$email = checkIsset($_POST['email']);
	$username = !empty($_POST['username']) ? $_POST['username'] : '';
	$admin_name = !empty($_POST['admin_name']) ? $_POST['admin_name'] : '';
	$admin_email = checkIsset($_POST['admin_email']);
	$admin_phone = !empty($_POST['admin_phone']) ? $_POST['admin_phone'] : '';
	$admin_phone = phoneReplaceMain($admin_phone);
	$found_state_id = 0;
//********** step1 varible intialization code start **********************

//********** step2 varible intialization code start **********************
	$automated_communication = !empty($_POST['automated_communication']) ? $_POST['automated_communication'] : array();
	$company_count = !empty($_POST['company_count']) ? $_POST['company_count'] : '';
	$group_company = !empty($_POST['group_company']) ? $_POST['group_company'] : '';
	$billing_broken = !empty($_POST['billing_broken']) ? $_POST['billing_broken'] : '';
	
	$billing_type = !empty($_POST['billing_type']) ? $_POST['billing_type'] : '';
	$available_payment = !empty($_POST['available_payment']) ? $_POST['available_payment'] : '';

	$account_type = !empty($_POST['account_type']) ? $_POST['account_type'] : '';
	$bankname = checkIsset($_POST['bankname']);
	$bank_rounting_number = !empty($_POST['bank_rounting_number']) ? $_POST['bank_rounting_number'] : '';
	$bank_account_number = !empty($_POST['bank_account_number']) ? $_POST['bank_account_number'] : '';
	$bank_number_confirm = !empty($_POST['bank_number_confirm']) ? $_POST['bank_number_confirm'] : '';

	$ach_name = checkIsset($_POST['ach_name']);
	$name_on_card = checkIsset($_POST['name_on_card']);
	$expiration = !empty($_POST['expiration']) ? $_POST['expiration'] : '';
	$cvv = !empty($_POST['cvv']) ? $_POST['cvv'] : '';
	$bill_address = checkIsset($_POST['bill_address']);
	$bill_address_2 = checkIsset($_POST['bill_address_2']);
	$bill_city = checkIsset($_POST['bill_city']);
	$bill_state = !empty($_POST['bill_state']) ? $_POST['bill_state'] : '';
	$bill_zip = !empty($_POST['bill_zip']) ? $_POST['bill_zip'] : '';
	$is_valid_billing_address = !empty($_POST['is_valid_billing_address']) ? $_POST['is_valid_billing_address'] : '';

	$card_type = !empty($_POST['card_type']) ? $_POST['card_type'] : '';
	$card_number = checkIsset($_POST['card_number']);
	$expiry_month = '';
	$expiry_year = '';
	if(!empty($expiration)){
		$expirtation_details = explode("/", $expiration);
		$expiry_month = $expirtation_details[0];
		$expiry_year = $expirtation_details[1];
	}

//********** step2 varible intialization code start **********************

//********** step3 varible intialization code start **********************
	$check_agree = isset($_POST['check_agree'])?$_POST['check_agree']:"";
	$signature_data = isset($_POST['signature_data'])?$_POST['signature_data']:"";
//********** step3 varible intialization code start **********************

//********* step1 validation code end   ********************
	if ($submit_type!='auto_save' && $step >= 1) {
		$validate->string(array('required' => true, 'field' => 'group_name', 'value' => $group_name), array('required' => 'Group Name is required.'));
		$validate->string(array('required' => true, 'field' => 'business_address', 'value' => $business_address), array('required' => 'Business address is required.'));
		if(!empty($business_address_2) && preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/',$business_address_2)) {
	        $validate->setError('business_address_2','Special character not allowed');
	    }
		$validate->string(array('required' => true, 'field' => 'city', 'value' => $city), array('required' => 'city is required.'));
		$validate->string(array('required' => true, 'field' => 'state', 'value' => $state), array('required' => 'state is required.'));
		$validate->string(array('required' => true, 'field' => 'zipcode', 'value' => $zipcode), array('required' => 'Zip Code is required'));

		$validate->digit(array('required' => true, 'field' => 'business_phone', 'value' => $business_phone, 'min' => 10, 'max' => 10), array('required' => 'Phone Number is required', 'invalid' => 'Valid Phone Number is required'));
		$validate->email(array('required' => true, 'field' => 'business_email', 'value' => $business_email), array('required' => 'Email is required', 'invalid' => 'Valid Email is required'));
		
		$validate->digit(array('required' => true, 'field' => 'no_of_employee', 'value' => $no_of_employee), array('required' => 'Employee is required', 'invalid' => 'Valid Number is required'));

		$validate->digit(array('required' => true, 'field' => 'years_in_business', 'value' => $years_in_business), array('required' => 'Year in business is required', 'invalid' => 'Valid Number is required'));
		
		$validate->digit(array('required' => true, 'field' => 'ein', 'value' => $ein), array('required' => 'EIN/FEIN is required', 'invalid' => 'Valid EIN/FEIN is required'));

		$validate->string(array('required' => true, 'field' => 'fname', 'value' => $fname), array('required' => 'First Name is required'));
		$validate->string(array('required' => true, 'field' => 'lname', 'value' => $lname), array('required' => 'Last Name is required'));
		
		$validate->digit(array('required' => true, 'field' => 'phone', 'value' => $phone, 'min' => 10, 'max' => 10), array('required' => 'Phone Number is required', 'invalid' => 'Valid Phone Number is required'));
		
		$validate->email(array('required' => true, 'field' => 'email', 'value' => $email), array('required' => 'Email is required', 'invalid' => 'Valid Email is required'));

		if($display_in_member == 'N'){

			$validate->string(array('required' => true, 'field' => 'admin_name', 'value' => $admin_name), array('required' => 'Name is required'));
			$validate->email(array('required' => true, 'field' => 'admin_email', 'value' => $admin_email), array('required' => 'Email is required.', 'invalid' => 'Please enter valid email'));
			$validate->digit(array('required' => true, 'field' => 'admin_phone', 'value' => $admin_phone, 'min' => 10, 'max' => 10), array('required' => 'Phone Number is required', 'invalid' => 'Valid Phone Number is required'));
		}

		$validate->regex(array('required' => true, 'pattern' => '/^[A-Za-z0-9]+$/', 'field' => 'username', 'value' => $username, 'min' => 4, 'max' => 20), array('required' => 'Username is required', 'invalid' => 'Valid Username is required'));

		if(!$validate->getError('zipcode')){
			$zipRes=$pdo->selectOne("SELECT id,state_code FROM zip_code WHERE zip_code=:zip_code",array(":zip_code"=>$zipcode));

			if(empty($zipRes)){
				$validate->setError('zipcode', 'Zip code is not valid');
			}else{
				$stateRes=$pdo->selectOne("SELECT id,name FROM states_c WHERE country_id = '231' AND short_name=:short_name",array(":short_name"=>$zipRes['state_code']));

				if(empty($stateRes)){
					$validate->setError('zipcode', 'Zip code is not valid');
				}else{
					$found_state_id = $stateRes['name'];
				}
			}
		}

		if (!$validate->getError('zipcode')){
	        include_once '../includes/function.class.php';
	        $function_list = new functionsList();
	        $zipAddress = $function_list->uspsCityVerification($zipcode);
	        if($zipAddress['status'] !='success'){
	            $validate->setError("zipcode",$zipAddress['error_message']);
	        }
	    }

		if(!$validate->getError('state')){
			if($found_state_id != $state){
				$validate->setError('state', 'Zip code is not valid for this state');
			}
		}
	
		if (!$validate->getError('username')) {
				if (!isValidUserName($username, $group_id)) {
					$validate->setError("username", "Username already exist");
				}
		}

		if(!$validate->getError('email')){
			$where_select_email = array(':email' => $email);
			$incr = "";
			if(!empty($group_id)){
				$incr .= " AND id!=:id";
				$where_select_email[":id"] = $group_id;
			}
			$selectEmail = "SELECT id,email FROM customer WHERE email=:email $incr AND type='Group' AND is_deleted='N' ";
			$resultEmail = $pdo->selectOne($selectEmail, $where_select_email);
			if (!empty($resultEmail)) {
				$validate->setError("email", "This email is already associated with another group account");
			}
		}
		
		if (count($validate->getErrors()) > 0 && empty($div_step_error)) {
			$div_step_error = "details_tab";
		}
	}
//********* step1 validation code end   ********************

//********* step2 validation code end   ********************
	if ($submit_type!='auto_save' && $step >= 2) {
		
		$validate->string(array('required' => true, 'field' => 'group_company', 'value' => $group_company), array('required' => 'Select any option'));
		$validate->string(array('required' => true, 'field' => 'billing_type', 'value' => $billing_type), array('required' => 'Select Billing Type'));
		

		if($group_company == 'Y'){
			$validate->string(array('required' => true, 'field' => 'billing_broken', 'value' => $billing_broken), array('required' => 'Select any option'));
			if(empty($company_count)){
				$validate->setError("company_count","Please Add Location/Company");
			}
		}

		if($billing_type=='list_bill'){
			$available_payment = !empty($available_payment) ? explode(',',$available_payment) : '';

			if(!empty($available_payment) && !in_array($payment_type,$available_payment)){
				$validate->setError('payment_type','Invalid Payment Method.');
			}

			$validate->string(array('required' => true, 'field' => 'payment_type', 'value' => $payment_type), array('required' => 'Select Payment Type'));

			if($payment_type=='CC'){
				$validate->string(array('required' => true, 'field' => 'name_on_card', 'value' => $name_on_card), array('required' => 'Name is required'));

				if (!$validate->getError("name_on_card") && !ctype_alnum(str_replace(" ","",$name_on_card))) {
					$validate->setError("name_on_card","Enter Valid Name");
				}
				$validate->string(array('required' => true, 'field' => 'card_type', 'value' => $card_type), array('required' => 'Select any card'));
				$validate->digit(array('required' => true, 'field' => 'card_number', 'value' => $card_number,"max"=>$MAX_CARD_NUMBER,"min"=>$MIN_CARD_NUMBER), array('required' => 'Card Number is required', "invalid" => "Please enter valid card number"));
				$validate->string(array('required' => true, 'field' => 'expiration', 'value' => $expiration), array('required' => 'Please select expiration month and year'));
				
				$cvv_required = "N";

				if(!$validate->getError("card_number") && !is_valid_luhn($card_number,$card_type)){
					$validate->setError("card_number","Enter valid Credit Card Number");
				}
				
				$sqlProcessor = "SELECT require_cvv FROM payment_master where is_deleted = 'N' AND type='Global' AND status IN ('Active') AND is_cc_accepted = 'Y' AND is_default_for_cc = 'Y'";
				$resProcessor = $pdo->selectOne($sqlProcessor);
				if(!empty($resProcessor)){
					$cvv_required = $resProcessor['require_cvv'];
				}
				if($cvv_required == 'Y' || $cvv!=''){
					$validate->digit(array('required' => true, 'field' => 'cvv', 'value' => $cvv), array('required' => 'CVV is required', 'invalid' => "Enter valid CVV"));

					if(!$validate->getError("cvv") && !cvv_type_pair($cvv,$card_type)){
						$validate->setError("cvv","Invalid CVV Number");
					} 
				}
				$validate->string(array('required' => true, 'field' => 'bill_address', 'value' => $bill_address), array('required' => 'Address is required'));
				if(!empty($bill_address_2) && preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/',$bill_address_2)) {
			        $validate->setError('bill_address_2','Special character not allowed');
			    }
				$validate->string(array('required' => true, 'field' => 'bill_city', 'value' => $bill_city), array('required' => 'City is required'));
				$validate->string(array('required' => true, 'field' => 'bill_state', 'value' => $bill_state), array('required' => 'State is required'));
				$validate->string(array('required' => true, 'field' => 'bill_zip', 'value' => $bill_zip), array('required' => 'Zip is required'));
				if (!$validate->getError('bill_zip')){
			        include_once '../includes/function.class.php';
			        $function_list = new functionsList();
			        $zipAddress = $function_list->uspsCityVerification($bill_zip);
			        if($zipAddress['status'] !='success'){
			            $validate->setError("bill_zip",$zipAddress['error_message']);
			        }
			    }
			}else if($payment_type=='ACH'){
				$validate->string(array('required' => true, 'field' => 'ach_name', 'value' => $ach_name,'max'=>22), array('required' => 'Name is required'));

				if (!$validate->getError("ach_name") && !ctype_alnum(str_replace(" ","",$ach_name))) {
					$validate->setError("ach_name","Enter Valid Name");
				}
				$validate->string(array('required' => true, 'field' => 'account_type', 'value' => $account_type), array('required' => 'Select Account Type'));
				$validate->string(array('required' => true, 'field' => 'bankname', 'value' => $bankname,'max'=>50), array('required' => 'Bank name is required'));
				
				$validate->digit(array('required' => true, 'field' => 'bank_rounting_number', 'value' => $bank_rounting_number,"min" => 9,"max" => 9), array('required' => 'Routing number is required', 'invalid' => "Enter valid Routing number"));
				
				if (!$validate->getError("bank_rounting_number")) {
					if (checkRoutingNumber($bank_rounting_number) == false) {
						$validate->setError("bank_rounting_number", "Enter valid routing number");
					}
				}
				
				$validate->digit(array('required' => true, 'field' => 'bank_account_number', 'value' => $bank_account_number,'min'=>$MIN_ACCOUNT_NUMBER,'max'=>$MAX_ACCOUNT_NUMBER), array('required' => 'Account number is required', 'invalid' => "Enter valid Account number"));
				
			
				$validate->digit(array('required' => true, 'field' => 'bank_number_confirm', 'value' => $bank_number_confirm,'min'=>$MIN_ACCOUNT_NUMBER,'max'=>$MAX_ACCOUNT_NUMBER), array('required' => 'Confirm Account number is required', 'invalid' => "Enter valid Confirm Account number"));
				
				if (!$validate->getError('bank_number_confirm')) {
					if ($bank_number_confirm != $bank_account_number) {
						$validate->setError('bank_number_confirm', "Enter same Account Number");
					}
				}
			}
		}
		if (count($validate->getErrors()) > 0 && empty($div_step_error)) {
			$div_step_error = "billing_tab";
		}
	}
//********* step2 validation code end   ********************

//********* step3 validation code end   ********************
	if ($submit_type!='auto_save' && $step >= 3) {

		if (strtoupper($check_agree) != 'Y') {
			$validate->setError('check_agree', 'Please agree to terms and conditions');
		}
		$validate->string(array('required' => true, 'field' => 'signature_data', 'value' => $signature_data), array('required' => 'Please draw your signature'));
		
		if (count($validate->getErrors()) > 0 && empty($div_step_error)) {
			$div_step_error = "agreement_tab";
		}
	}
//********* step3 validation code end   ********************


if ($validate->isValid()) {
	if ($step >= 1) {
		$params = array(
			'business_name' => $group_name,
			'address' => makesafe($business_address),
			'address_2' => makesafe($business_address_2),
			'city' => makesafe($city),
			'country_id' => '231',
			'country_name' => "United States",
			'state' => makesafe($state),
			'zip' => makesafe($zipcode),
			'business_phone' => $business_phone,
			'business_email' => $business_email,
			'fname' => makesafe($fname),
			'lname' => makesafe($lname),
			'cell_phone' => makesafe($phone),
			'email' => $email,
			'public_name' => $admin_name,
			'public_email' => $admin_email,
			'public_phone' => $admin_phone,
			'user_name' => $username,
		);
		$upd_where = array(
			'clause' => 'id = :id',
			'params' => array(
				':id' => $group_id,
			),
		);
		$pdo->update('customer', $params, $upd_where);

		$groupSettingParams = array(
			'group_size' => $no_of_employee,
			'group_in_year' => $years_in_business,
			'ein' => $ein,
			'business_nature' => $nature_of_business,
			'sic_code' => $sic_code,
		);

		$group_stg_sql = "SELECT * FROM customer_group_settings WHERE customer_id=:customer_id";
		$group_stg_where = array(":customer_id" => $group_id);
		$group_stg_row = $pdo->selectOne($group_stg_sql, $group_stg_where);
		if (!empty($group_stg_row)){
			$upd_where = array(
				'clause' => 'customer_id = :customer_id',
				'params' => array(
					':customer_id' => $group_id,
				),
			);
			$pdo->update('customer_group_settings', $groupSettingParams, $upd_where);
		} else {
			$groupSettingParams['customer_id'] = $group_id;
			$pdo->insert('customer_group_settings', $groupSettingParams);
		}

		$cs_update_params = array(
			'display_in_member'=>$display_in_member,
			'is_valid_address'=>$is_valid_address,
		);
		$cs_upd_where = array(
			'clause' => 'customer_id = :id',
			'params' => array(
				':id' => $group_id,
			),
		);
		$customer_settings = $pdo->update('customer_settings', $cs_update_params, $cs_upd_where);

	  	$response['status']="success";
  	}
  	if($step >=2){
  		$groupSettingParams = array(
			'automated_communication'=>!empty($automated_communication) ? implode(",", $automated_communication) : '',
			'employer_company_common_owner' => $group_company,
			'invoice_broken_locations'=>'N',
			'billing_type'=>$billing_type,
		);
		if($group_company=='Y'){
			$groupSettingParams['invoice_broken_locations']=$billing_broken;
		}

		$group_stg_sql = "SELECT * FROM customer_group_settings WHERE customer_id=:customer_id";
		$group_stg_where = array(":customer_id" => $group_id);
		$group_stg_row = $pdo->selectOne($group_stg_sql, $group_stg_where);
		if ($group_stg_row) {
			$upd_where = array(
				'clause' => 'customer_id = :customer_id',
				'params' => array(
					':customer_id' => $group_id,
				),
			);
			$pdo->update('customer_group_settings', $groupSettingParams, $upd_where);
		} else {
			$groupSettingParams['customer_id'] = $group_id;
			$pdo->insert('customer_group_settings', $groupSettingParams);
		}

		if($billing_type == "list_bill"){
			$listbill="Y";
		}else{
			$listbill = 'N';
		}

		
		
		if($listbill=="Y"){
			$companyArr = array();
			array_push($companyArr, 0);
			$selSql="SELECT id,name,ein,location FROM group_company WHERE group_id=:group_id and is_deleted='N'";
			$selRes=$pdo->select($selSql,array(":group_id"=>$group_id));

			if(!empty($selRes)){
				foreach ($selRes as $key => $companyRow) {
					array_push($companyArr, $companyRow['id']);
				}
			}

			if(!empty($companyArr)){
				foreach ($companyArr as $key => $company_id) {
					$group_bill_sql = "SELECT id FROM customer_billing_profile where customer_id=:customer_id and company_id = :company_id";
					$group_bill_row = $pdo->selectOne($group_bill_sql, array(":customer_id" => $group_id,":company_id"=>$company_id));
					
					$bill_params = array(
						'company_id'=>$company_id,
						'customer_id'=>$group_id,
						"fname" => $fname,
						"lname" => $lname,
						"email" => $email,
						"phone" => $phone,
						'country_id' => '231',
						'country' => "United States",
						'listbill_enroll' => $listbill,
						'is_default' => 'Y',
						'ip_address' => !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'],
						'is_valid_address'=>$is_valid_billing_address,
					);
					$bill_params['payment_mode'] = $payment_type;
					
					if ($payment_type == "ACH") {
						$bill_params['fname'] = $ach_name;
						$bill_params['ach_account_number'] = "msqlfunc_AES_ENCRYPT('" . $bank_account_number . "','" . $CREDIT_CARD_ENC_KEY . "')";
						$bill_params['ach_routing_number'] = "msqlfunc_AES_ENCRYPT('" . $bank_rounting_number . "','" . $CREDIT_CARD_ENC_KEY . "')";
						$bill_params['ach_account_type'] = $account_type;
						$bill_params['bankname'] = $bankname;
						$bill_params['last_cc_ach_no'] = makeSafe(substr($bank_account_number, -4));
					} else if ($payment_type == "CC") {
						$bill_params['fname'] = $name_on_card;
						
						$bill_params['state'] = $bill_state;
						$bill_params['city'] = makeSafe($bill_city);
						$bill_params['zip'] = $bill_zip;
						$bill_params['address'] = makeSafe($bill_address);
						$bill_params['address2'] = makeSafe($bill_address_2);
						$bill_params['cvv_no'] = $cvv;

						$bill_params['card_no'] = makeSafe(substr($card_number, -4));
						$bill_params['card_no_full'] = "msqlfunc_AES_ENCRYPT('" . $card_number . "','" . $CREDIT_CARD_ENC_KEY . "')";
						$bill_params['card_type'] = makeSafe($card_type);
						$bill_params['expiry_month'] = makeSafe($expiry_month);
						$bill_params['expiry_year'] = makeSafe($expiry_year);
						$bill_params['last_cc_ach_no'] = makeSafe(substr($card_number, -4));
					}
					if (!empty($group_bill_row)) {
						$pdo->update("customer_billing_profile", $bill_params, array("clause" => "id=:id", "params" => array(":id" => $group_bill_row['id'])));
					}else{
						$pdo->insert("customer_billing_profile", $bill_params);
					}
				}
			}

			
		}
		
		$response['status']="success";
  	}
  	if ($submit_type!='auto_save' && $step == 3) {
		$group_product_contracted = $pdo->select("SELECT * FROM agent_product_rule WHERE agent_id=:agent_id AND is_deleted='N' AND status='Pending Approval'", array(":agent_id" => $group_id));

		if(count($group_product_contracted) > 0){
			foreach ($group_product_contracted as $value) {
				$updateSql = array("product_billing_type"=>'',"status" => 'Contracted');
				if($billing_type == "list_bill"){
					$updateSql['product_billing_type'] = 'list_bill';
				}
		      	$updateWhere = array("clause" => "id=:id", "params" => array(":id" => $value['id']));
		      	$pdo->update("agent_product_rule", $updateSql, $updateWhere);
			}
		}

		$cs_update_params = array(
			'ip_address' => !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'],
		);
		$cs_upd_where = array(
			'clause' => 'customer_id = :id',
			'params' => array(
				':id' => $group_id,
			),
		);
		$customer_settings = $pdo->update('customer_settings', $cs_update_params, $cs_upd_where,true);
		$signature_file_name = $fname . time() . '.png';
		$signature_file_name = str_replace(' ', "", $signature_file_name);
		
		$s3Client = new S3Client([
	        'version' => 'latest',
	        'region'  => $S3_REGION,
	        'credentials'=>array(
	            'key'=> $S3_KEY,
	            'secret'=> $S3_SECRET
	        )
	    ]);

	    $result = $s3Client->putObject([
	        'Bucket' => $S3_BUCKET_NAME,
	        'Key'    => $SIGNATURE_FILE_PATH.$signature_file_name,
	        'SourceFile' => $signature_data,
	        'ACL' => 'public-read'
	    ]);

	    /* group contract save in s3 bucket start (task => EL8-1220) */
			$groupContractFileName = $functionsList->saveGroupContract($group_id,$signature_file_name);
		/* group contract save in s3 bucket end (task => EL8-1220) */

		$_SESSION["groups"]["status"] = "Active";

		$cs_update_params = array(
			'signature_file' => $signature_file_name,
			'signature_date' => 'msqlfunc_NOW()',
		);
		$cs_update_params["is_contract_approved"] = NULL;
		if(!empty($groupContractFileName)){
			$cs_update_params["agent_contract_file"] = $groupContractFileName;
		}
		$cs_upd_where = array(
			'clause' => 'customer_id = :id',
			'params' => array(
				':id' => $group_id,
			),
		);
		$customer_settings = $pdo->update('customer_settings', $cs_update_params, $cs_upd_where,true);

		$groupFeatures = $pdo->selectOne("SELECT GROUP_CONCAT(id) as featureIds FROM group_feature_access");
		$feature_access = !empty($groupFeatures['featureIds']) ? $groupFeatures['featureIds'] : "";
		$update_params = array(
			'status' => 'Active',
			'feature_access' => $feature_access,
		);
		$upd_where = array(
			'clause' => 'id = :id',
			'params' => array(
				':id' => $group_id,
			),
		);
		$pdo->update('customer', $update_params, $upd_where);

		// Trigger Action Send Email/SMS
		$TriggerMailSms->trigger_action_mail('group_onboarding',$group_id,'group');


		if(!empty($customer_settings)){
			$contract_activity['terms'] = 'Terms & Condition';
			$contract_activity['customer_settings'] = 'Group added signature and accepted Terms & Conditions.';
		}
		if ($sponsor_id > 0) {
			$sponsor_id = $sponsor_id;
			$sponsor_type = 'Agent';
		} else {
			$sponsor_id = 0;
			$sponsor_type = 'Agent';
		}

		$user_data = array();
		$user_data['user_id'] = $group_id;
		$user_data['display_id'] = getname("customer", $group_id, "display_id", "id");
		$user_data['full_name'] = makesafe($fname." ".$lname);
		$user_data['user_type'] = 'Group';
		$audit_log_id = audit_log($user_data, $group_id, "Group", "Group Terms Agreed", '', '', 'Group contract update');

		$description = '';

		activity_feed(3, $group_id, 'Group', $group_id, 'customer', 'Group Signed Contract', $fname, $lname, $description);
		activity_feed(3, $sponsor_id, 'Agent', $group_id, 'customer', 'Contracted New Group', $fname, $lname, $description);

		$aparams = array();
		$aparams['fname'] = $fname;
		$aparams['lname'] = $lname;
		$aparams['GroupName'] = $group_name;
		$aparams['link'] = $GROUP_HOST;
		$_SESSION['groups']['access'] = $feature_access != "" ? explode(",",$feature_access) : array();
		$smart_tags = get_user_smart_tags($group_id,'group');
                
        if($smart_tags){
            $aparams = array_merge($aparams,$smart_tags);
        }
		// Group - Enrollment Approved
		$trigger_id = 98;
		trigger_mail($trigger_id, $aparams,  $email);
  		$response['status'] = 'account_approved';
  	}
}else{
	$response['status'] = "fail";
	$response['errors'] = $validate->getErrors();
	$response['div_step_error'] = $div_step_error;
}

header('Content-type: application/json');
echo json_encode($response);
dbConnectionClose();
exit;
?>