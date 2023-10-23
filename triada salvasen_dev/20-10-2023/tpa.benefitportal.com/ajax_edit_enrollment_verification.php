<?php
ini_set('memory_limit', '-1');
ini_set('max_execution_time', '-1');
include_once __DIR__ . '/includes/connect.php'; 
include_once __DIR__ . '/includes/cyberx_payment_class.php';
include_once __DIR__ . '/includes/function.class.php';
include_once __DIR__ . '/includes/notification_function.php';
include_once __DIR__ . '/includes/member_enrollment.class.php';
include_once __DIR__ . '/includes/enrollment_dates.class.php';
include_once __DIR__ . '/includes/trigger.class.php';
include_once __DIR__ . '/includes/member_setting.class.php';
require __DIR__ . '/libs/awsSDK/vendor/autoload.php';

use Aws\S3\S3Client;  
use Aws\Exception\AwsException;
use Aws\Credentials\CredentialProvider;

$response = array();

$validate = new Validation();
$enrollDate = new enrollmentDate();
$function_list = new functionsList();
$enrollment = new MemberEnrollment();
$TriggerMailSms = new TriggerMailSms();
$memberSetting = new memberSetting();

$BROWSER = getBrowser();
$OS = getOS($_SERVER['HTTP_USER_AGENT']);
// $REQ_URL = ($_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
$REQ_URL = $_SERVER["HTTP_REFERER"];
$order_id = isset($_POST['order_id']) ? $_POST['order_id'] : 0 ;
$quote_order_id = isset($_POST['order_id']) ? $_POST['order_id'] : 0 ;
$customer_id = isset($_POST['customer_id']) ? $_POST['customer_id'] : 0 ;
$lead_id = isset($_POST['lead_id']) ? $_POST['lead_id'] : 0 ;
$lead_quote_id = isset($_POST['lead_quote_id']) ? $_POST['lead_quote_id'] : 0 ;
$sponsor_id = 0;
$decline_log_id = "";

$lead_track = array(
	'status' => 'Email/SMS Verification',
	'description' => 'Email/SMS Verification Start',
);

lead_tracking($lead_id,$customer_id,$lead_track);

$checkCustomerExistSql = "SELECT id,rep_id,sponsor_id,status FROM customer WHERE id=:id";
$checkCustomerExist = $pdo->selectOne($checkCustomerExistSql, array(':id' => makeSafe($customer_id)));
$customer_id = $checkCustomerExist["id"];
$customer_rep_id = $checkCustomerExist["rep_id"];
$sponsor_id = $checkCustomerExist["sponsor_id"];

$group_billing_method = isset($_POST['group_billing_method'])?$_POST['group_billing_method']:"";
$enrollmentLocation = isset($_POST['enrollmentLocation'])?$_POST['enrollmentLocation']:"";
//Primary member Information
$primary_fname = trim(checkIsset($_POST['primary_fname']));
$primary_lname = trim(checkIsset($_POST['primary_lname']));
$primary_dob = checkIsset($_POST['primary_birthdate']);
$primary_gender = trim(checkIsset($_POST['primary_gender']));
$primary_email = trim(checkIsset($_POST['primary_email']));
$primary_phone = checkIsset($_POST['primary_phone']);
$primary_address_1 = trim(checkIsset($_POST['primary_address1']));
$primary_address_2 = trim(checkIsset($_POST['primary_address2']));
$is_valid_address = checkIsset($_POST['is_valid_address']);
$primary_city = trim(checkIsset($_POST['primary_city']));
$primary_state = trim(checkIsset($_POST['primary_state']));
if(!empty($primary_state) && strlen($primary_state) == 2) {
	$primary_state = strtoupper($primary_state);
	$primary_state = (isset($getStateNameByShortName[$primary_state])?$getStateNameByShortName[$primary_state]:$primary_state);
}
$primary_zip = trim(checkIsset($_POST['primary_zip']));
$primary_ssn = checkIsset($_POST['primary_SSN']);
$primary_benefit_amount = checkIsset($_POST['primary_benefit_amount']);
$primary_in_patient_benefit = checkIsset($_POST['primary_in_patient_benefit']);
$primary_out_patient_benefit = checkIsset($_POST['primary_out_patient_benefit']);
$primary_monthly_income = checkIsset($_POST['primary_monthly_income']);
$required_ssn = checkIsset($_POST['required_SSN']);
//Dependent Information
$dep_ids = checkIsset($_POST['dep_ids'],'arr');
$dep_fname = checkIsset($_POST['dep_fname'],'arr');
$dep_lname = checkIsset($_POST['dep_lname'],'arr');
$dep_ssn = checkIsset($_POST['dep_SSN'],'arr');
$dep_required_ssn = checkIsset($_POST['dep_required_SSN'],'arr');
//Primary member Information
$is_principal_beneficiary = isset($_POST['is_principal_beneficiary'])?  $_POST['is_principal_beneficiary']:'';
$is_contingent_beneficiary = isset($_POST['is_contingent_beneficiary'])? $_POST['is_contingent_beneficiary']:'';
//Billing Information Credit Card

$name_on_card = checkIsset($_POST['name_on_card']);
$card_number = checkIsset($_POST['card_number']);
$full_card_number = checkIsset($_POST['full_card_number']);
$card_type = checkIsset($_POST['card_type']);
$expiration = checkIsset($_POST['expiration']);
$cvv = checkIsset($_POST['cvv']);

$require_cvv = checkIsset($_POST['require_cvv']);
//Billing Information Bank Draft
$ach_name = checkIsset($_POST['ach_name']);
$account_type = checkIsset($_POST['account_type']);
$routing_number = checkIsset($_POST['routing_number']);
$entered_routing_number = checkIsset($_POST['entered_routing_number']);
$account_number = checkIsset($_POST['account_number']);
$entered_account_number = checkIsset($_POST['entered_account_number']);
$confirm_account_number = checkIsset($_POST['confirm_account_number']);

//Billing Address Information
$bill_fname = checkIsset($_POST['bill_fname']);
// $bill_lname = checkIsset($_POST['bill_lname']);
$bill_address = checkIsset($_POST['bill_address']);
$bill_address2 = checkIsset($_POST['bill_address2']);
$bill_city = checkIsset($_POST['bill_city']);
$bill_state = checkIsset($_POST['bill_state']);
$bill_zip = checkIsset($_POST['bill_zip']);
$enroll_with_post_date = checkIsset($_POST['enroll_with_post_date'])!='' ? $_POST['enroll_with_post_date'] : "no" ;

$product_list = checkIsset($_POST['product_list'],'arr');
$child_product_list = checkIsset($_POST['child_product_list']);
$spouse_product_list = checkIsset($_POST['spouse_product_list']);
$plan_list = checkIsset($_POST['sel_plans'],'arr');
$primary_annual_salary = checkIsset($_POST['primary_annual_salary'],'arr');
$primary_monthly_salary_percentage = checkIsset($_POST['primary_monthly_salary_percentage'],'arr');
$bill_country ='231';

//********* Product send email code start ********************
$mi_patria_product_res = $pdo->selectOne("SELECT setting_value FROM app_settings WHERE setting_key='mi_patria_products'");
$mi_patria_product_id = !empty($mi_patria_product_res['setting_value']) ? explode(',',$mi_patria_product_res['setting_value']) : '';

$send_email_productId = array_diff($product_list,$mi_patria_product_id);
//********* Product send email code end ********************

//********* Depedent Detail Variable Initialize code start ********************
$dependent_relation_input =  checkIsset($_POST['dependent_relation_input'],'arr');
$dependent_product_input = checkIsset($_POST['dependent_product_input'],'arr');

$productAndPlanWiseDependent = array();
$productWiseDependentCount = array();
$REAL_IP_ADDRESS = get_real_ipaddress();
//********* Depedent Detail Variable Initialize code end ********************

if(!empty($lead_quote_id)){
	$quoteSql = "SELECT id FROM lead_quote_details WHERE status!='Disabled' AND id = :id";
	$quoteParams = array(":id" => $lead_quote_id);
	$quoteRes = $pdo->selectOne($quoteSql,$quoteParams);
	if(empty($quoteRes["id"])){
		$response['status'] = 'quote_not_found';
		echo json_encode($response);
		exit;
	}
}

$admin_id = checkIsset($_POST['admin_id']);
$payment_mode = checkIsset($_POST['payment_mode']);
$billing_profile_id = checkIsset($_POST['billing_profile_id']);
if($enrollmentLocation!='groupSide' || ($enrollmentLocation=='groupSide' && $group_billing_method == 'individual')){
	if(empty($payment_mode)){
	    $validate->setError("payment_mode","Please select any Payment Method");
	}
}
//primary data validation start
    $validate->string(array('required' => true, 'field' => 'primary_fname', 'value' => $primary_fname), array('required' => 'First Name is required'));
    $validate->string(array('required' => true, 'field' => 'primary_lname', 'value' => $primary_lname), array('required' => 'Last Name is required'));
    if($required_ssn == 'Y'){
		$primary_ssn = phoneReplaceMain($primary_ssn);
        $validate->string(array('required' => true, 'field' => 'primary_SSN', 'value' => $primary_ssn), array('required' => 'SSN is required'));
    }
    /*--- Check Member Already Exist ---*/
    if(in_array($checkCustomerExist["status"],array('Customer Abandon','Pending Quote','Pending Validation'))) {
    	$is_add_product = 0;
    } else {
    	$is_add_product = 1;
    }
	$email_error = $enrollment->validate_existing_email($primary_email,$sponsor_id,$customer_id,$lead_id,array('is_add_product' => $is_add_product));
	if($email_error['status'] == "fail" && in_array($email_error['existing_status'],array("bob_member","none_bob_member"))) {
		$response['existing_email'] = "This email is already associated with another member account.";
		$validate->setError("primary_email","This email is already associated with another member account.");
	}
	/*---/Check Member Already Exist ---*/
//primary data validation end

//Dependent data Validation start
    if(!empty($dep_ids)){

        foreach($dep_ids as $id){
            $validate->string(array('required' => true, 'field' => 'dep_fname_'.$id, 'value' => $dep_fname[$id]), array('required' => 'First Name is required'));
			$validate->string(array('required' => true, 'field' => 'dep_lname_'.$id, 'value' => $dep_lname[$id]), array('required' => 'Last Name is required'));

			$relation = $dependent_relation_input[$id];
			
			$productAndPlanWiseDependent[$id]['relation']=$relation;
			$productAndPlanWiseDependent[$id]['dependent_id']=$id;

			if(!empty($child_product_list) && getRevRelation($relation)=='Child'){
				$child_field = $enrollment->get_child_field(explode(',',$child_product_list));			
				$child_field_asked = array();
				if(!empty($child_field)){
					foreach($child_field as $key => $child)	{
						if($child_field[$key]['asked'] == 'Y'){
							$child_field_asked['asked'][] = $child['label'];
						}
					}
				}
				
				if(count($child_field_asked) > 0){ 
					foreach($child_field_asked['asked'] as $asked){
						$productAndPlanWiseDependent[$id][$asked]=checkIsset($_POST['dep_'.$asked][$id]);
					}
				}
				if(!empty($child_product_list)){
					$child_products1 = explode(',',$child_product_list);
					foreach($child_products1 as $product){
						$product_arr_child  = isset($productWiseDependentCount[$product]['Child']) ?  $productWiseDependentCount[$product]['Child'] :  0;
						$productAndPlanWiseDependent[$id]['dependent_product_list']  = isset($productAndPlanWiseDependent[$id]['dependent_product_list']) ?  $productAndPlanWiseDependent[$id]['dependent_product_list'] :  array(); 
						$productWiseDependentCount[$product]['Child'] = $product_arr_child + 1;
						array_push($productAndPlanWiseDependent[$id]['dependent_product_list'],$product);
					}

				}
			}				
			if(!empty($spouse_product_list) && getRevRelation($relation)=='Spouse'){
				$spouse_field = $enrollment->get_spouse_field(explode(',',$spouse_product_list));
				$spouse_field_asked = array();
				if(!empty($spouse_field)){
					foreach($spouse_field as $key => $spouse)	{
						if($spouse_field[$key]['asked'] == 'Y'){
							$spouse_field_asked['asked'][] = $spouse['label'];
						}
					}
				}

				if(count($spouse_field_asked) > 0){ 
					foreach($spouse_field_asked['asked'] as $asked){
						$productAndPlanWiseDependent[$id][$asked]=checkIsset($_POST['dep_'.$asked][$id]);
					}
				}

					if(!empty($spouse_product_list)){
					$spouse_products_1 = explode(',',$spouse_product_list);
					foreach($spouse_products_1 as $product){
						$product_arr_spouse  = isset($productWiseDependentCount[$product]['Spouse']) ?  $productWiseDependentCount[$product]['Spouse'] :  0;
						$productAndPlanWiseDependent[$id]['dependent_product_list']  = isset($productAndPlanWiseDependent[$id]['dependent_product_list']) ?  $productAndPlanWiseDependent[$id]['dependent_product_list'] :  array(); 
						$productWiseDependentCount[$product]['Spouse'] = $product_arr_spouse + 1;
						array_push($productAndPlanWiseDependent[$id]['dependent_product_list'],$product);
					}
					
				}
			}
			$productAndPlanWiseDependent[$id]['dependent_product_list'] = array_unique($productAndPlanWiseDependent[$id]['dependent_product_list']);

            if(!empty($dep_required_ssn[$id]) && $dep_required_ssn[$id] == "Y"){
				$dep_ssn[$id] = phoneReplaceMain($dep_ssn[$id]);
                $validate->string(array('required' => true, 'field' => 'dep_SSN_'.$id, 'value' => $dep_ssn[$id]), array('required' => 'SSN is required'));
            }
        }
	}
//Dependent data Validatione end

//Beneficiary data validation start
	$principal_beneficiary_percentage = 0;
	if($is_principal_beneficiary == 'displayed'){
		$principal_beneficiary_field = $enrollment->get_principal_beneficiary_field($product_list);
		$tmpPrincipal = !empty($_POST['principal_queBeneficiaryFullName']) ? $_POST['principal_queBeneficiaryFullName'] : array();
		
		if(!empty($tmpPrincipal)){
			foreach ($tmpPrincipal as $principalKey => $childArr) {
				if(!empty($principal_beneficiary_field)){
					foreach($principal_beneficiary_field as $field_key => $row) {
						$is_required = $row['required'];
						$control_name = 'principal_'.$row['label'];
						$label = $row['display_label'];
						$control_value = isset($_POST[$control_name][$principalKey])?$_POST[$control_name][$principalKey]:"";
						${$control_name} = $control_value;
						$control_class = $row['control_class'];
						if($control_name == "principal_queBeneficiaryAllow3" || $control_name == "principal_product"){
							continue;
						}
						if($is_required=='Y'){
							if(is_array(${$control_name})){
								if(empty($control_value)){
									$validate->setError($control_name."_".$principalKey,$label.' is required');
								}
							}else{
								$validate->string(array('required' => true, 'field' => $control_name."_".$principalKey, 'value' => $control_value), array('required' => $label.' is required'));
							}
						}
						if($control_class == "dob" && !empty($control_value)){
							if (!$validate->getError($control_name."_".$principalKey)) {
								list($mm, $dd, $yyyy) = explode('/', $control_value);

								if (!checkdate($mm, $dd, $yyyy)) {
									$validate->setError($control_name."_".$principalKey, 'Valid Date is required');
								}
							}
						}
						if($control_name == "principal_queBeneficiaryEmail" && !empty($control_value)){
							if (!filter_var($control_value, FILTER_VALIDATE_EMAIL)) {
								$validate->setError($control_name."_".$principalKey, 'Valid Email is required');
							}
						}
						if($control_name == "principal_queBeneficiaryPhone" && !empty($control_value)){
							$validate->digit(array('required' => true, 'field' => $control_name."_".$principalKey, 'value' => phoneReplaceMain($control_value), 'min' => 10, 'max' => 10), array('required' => 'Phone Number is required', 'invalid' => 'Valid Phone Number is required'));
						}
						if($control_name == "principal_queBeneficiaryPercentage" && $control_value != ''){
							$principal_beneficiary_percentage = $principal_beneficiary_percentage + $control_value;
						}
					}
				}
			}
		}
		if($principal_beneficiary_percentage!=100){
			$validate->setError('principal_beneficiary_general', 'Sum of all Principal Beneficiary percentages must equal 100%');
		}
	}

	$contingent_beneficiary_percentage = 0;
	if($is_contingent_beneficiary == 'displayed'){
		$contingent_beneficiary_field = $enrollment->get_contingent_beneficiary_field($product_list);
		$tmpContingent = !empty($_POST['contingent_queBeneficiaryFullName']) ? $_POST['contingent_queBeneficiaryFullName'] : array();

		if(!empty($tmpContingent)){
			foreach ($tmpContingent as $contingentKey => $childArr) {
				if(!empty($contingent_beneficiary_field)){
					foreach($contingent_beneficiary_field as $field_key => $row) {
						$is_required = $row['required'];
						$control_name = 'contingent_'.$row['label'];
						$label = $row['display_label'];
						$control_value = isset($_POST[$control_name][$contingentKey])?$_POST[$control_name][$contingentKey]:"";
						${$control_name} = $control_value;
						$control_class = $row['control_class'];
						if($control_name == "contingent_queBeneficiaryAllow3" || $control_name == "contingent_product"){
							continue;
						}
						if($is_required=="Y"){
							if(is_array(${$control_name})){
								if(empty($control_value)){
									$validate->setError($control_name."_".$contingentKey,$label.' is required');
								}
							}else{
								$validate->string(array('required' => true, 'field' => $control_name."_".$contingentKey, 'value' => $control_value), array('required' => $label.' is required'));
							}
						}
						if($control_class == "dob" && !empty($control_value)){
							if (!$validate->getError($control_name."_".$contingentKey)) {
								list($mm, $dd, $yyyy) = explode('/', $control_value);
								if (!checkdate($mm, $dd, $yyyy)) {
									$validate->setError($control_name."_".$contingentKey, 'Valid Date is required');
								}
							}
						}
						if($control_name == "contingent_queBeneficiaryEmail" && !empty($control_value)){
							if (!filter_var($control_value, FILTER_VALIDATE_EMAIL)) {
								$validate->setError($control_name."_".$contingentKey, 'Valid Email is required');
							}
						}
						if($control_name == "contingent_queBeneficiaryPhone" && !empty($control_value)){
							$validate->digit(array('required' => true, 'field' => $control_name."_".$contingentKey, 'value' => phoneReplaceMain($control_value), 'min' => 10, 'max' => 10), array('required' => 'Phone Number is required', 'invalid' => 'Valid Phone Number is required'));
						}
						if($control_name == "contingent_queBeneficiaryPercentage" && $control_value != ''){
							$contingent_beneficiary_percentage = $contingent_beneficiary_percentage + $control_value;
						}
					}
				}
			}
		}
		if($contingent_beneficiary_percentage!=100){
			$validate->setError("contingent_beneficiary_general", 'Sum of all Contingent Beneficiary percentages must equal 100%');
		}
	}
//Beneficiary data validation end

// Billing Address Validation
	$coverage_dates = $_POST['coverage_dates'];

	$lowest_coverage_date ='';
	if(!empty($coverage_dates)){
		$lowest_coverage_date=$enrollDate->getLowestCoverageDate($coverage_dates);
	}
	if ($enroll_with_post_date == 'yes') {
		$post_date = $_POST['post_date'];
		if(!empty($post_date) && strtotime($post_date) > 0) {
			$billing_date = $post_date;
		}
	}
	if($enrollmentLocation!='groupSide' || ($enrollmentLocation=='groupSide' && $group_billing_method == 'individual')){
	    $validate->string(array('required' => true, 'field' => 'bill_fname', 'value' => $bill_fname), array('required' => 'First Name is required'));
	    // $validate->string(array('required' => true, 'field' => 'bill_lname', 'value' => $bill_lname), array('required' => 'Last Name is required'));
	    $validate->string(array('required' => true, 'field' => 'bill_address', 'value' => $bill_address), array('required' => 'Address is required'));
	    if(!empty($bill_address2) && preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/',$bill_address2)) {
		    $validate->setError('bill_address2','Special character not allowed in Address 2');
		}
	    $validate->string(array('required' => true, 'field' => 'bill_city', 'value' => $bill_city), array('required' => 'City is required'));
	    $validate->string(array('required' => true, 'field' => 'bill_state', 'value' => $bill_state), array('required' => 'State is required'));
	    $validate->string(array('required' => true, 'field' => 'bill_zip', 'value' => str_replace('_','',$bill_zip)), array('required' => 'Zip is required'));
	    // $validate->string(array('required' => true, 'field' => 'bill_zip', 'value' => $bill_zip), array('required' => 'Zip is required'));
	    if(!$validate->getError('bill_zip')){
	        $getDetailOnPinCode=$pdo->selectOne("SELECT * FROM zip_code WHERE zip_code=:zip_code",array(":zip_code"=>$bill_zip));
	          if(!$getDetailOnPinCode){
	              $validate->setError('bill_zip', 'Validate zip code is required');
	          } else {
	              if(!$validate->getError('bill_state')){
	                  $state_res = $pdo->selectOne("SELECT * FROM `states_c` WHERE name = :name", array(':name' => $bill_state));
	                  if($getDetailOnPinCode['state_code'] != $state_res['short_name']){
	                      $validate->setError('bill_zip', 'Validate zip code is required');
	                  }
	              }
	          }
		}
		
		  
		if ($enroll_with_post_date == 'yes') {
			$validate->string(array('required' => true, 'field' => 'post_date', 'value' => $post_date), array('required' => 'Select Post date'));

			/*---------- Post Date Validation ---------*/
			if (empty($validate->getError('post_date'))) {
				if (strtotime($post_date) >= strtotime($lowest_coverage_date)) {
					$validate->setError('post_date', 'Post date must be less than ' . date('m/d/Y', strtotime($lowest_coverage_date)));
				}
				if (strtotime($post_date) <= strtotime(date("Y-m-d"))) {
					$validate->setError('post_date', 'Post date must future date');
				}
			}
			/*---------- Post Date Validation ---------*/
		}
	}
// Billing Address Validation

//payment data validation 
	if($enrollmentLocation!='groupSide' || ($enrollmentLocation=='groupSide' && $group_billing_method == 'individual')){
	    if($payment_mode == 'CC'){
	        $validate->string(array('required' => true, 'field' => 'name_on_card', 'value' => $name_on_card), array('required' => 'Name On card is required'));
	        
			if(empty($card_number) && !empty($full_card_number)) {
                $card_number = $full_card_number;
            }

			// $validate->string(array('required' => true, 'field' => 'card_number', 'value' => $card_number), array('required' => 'Card Number is required'));
			$validate->digit(array('required' => true, 'field' => 'card_number', 'value' => $card_number,"max"=>$MAX_CARD_NUMBER,"min"=>$MIN_CARD_NUMBER), array('required' => 'Card number is required', 'invalid' => "Enter valid Card Number"));

			if(!$validate->getError("card_number") && !is_valid_luhn($card_number,$card_type)){
                $validate->setError("card_number","Enter valid Credit Card Number");
            }
	        
	        $validate->string(array('required' => true, 'field' => 'card_type', 'value' => $card_type), array('required' => 'Please select any card'));
			$validate->string(array('required' => true, 'field' => 'expiration', 'value' => $expiration), array('required' => 'Please select expiration month and year'));
			if($require_cvv == 'yes' || $cvv!=''){
				$validate->digit(array('required' => true, 'field' => 'cvv', 'value' => $cvv), array('required' => 'CVV is required', 'invalid' => "Enter valid CVV"));	

				if(!$validate->getError("cvv") && !cvv_type_pair($cvv,$card_type)){
					$validate->setError("cvv","Invalid CVV Number");
				}
			}        

			if (!$validate->getError("name_on_card") && !ctype_alnum(str_replace(" ","",$name_on_card))) {
				$validate->setError("name_on_card","Enter Valid Name");
			}
	    }else{
	        $validate->string(array('required' => true, 'field' => 'ach_name', 'value' => $ach_name), array('required' => 'Name is required'));
			if (!$validate->getError("ach_name") && !ctype_alnum(str_replace(" ","",$ach_name))) {
				$validate->setError("ach_name","Enter Valid Name");
			}
	        // if(empty($full_card_number)){
	            $validate->string(array('required' => true, 'field' => 'account_type', 'value' => $account_type), array('required' => 'Account Type is required'));
	        // }
	        if(empty($entered_account_number) || !empty($account_number)){

	            $validate->digit(array('required' => true, 'field' => 'account_number', 'value' => $account_number,'min'=>$MIN_ACCOUNT_NUMBER,'max'=>$MAX_ACCOUNT_NUMBER), array('required' => 'Account number is required', 'invalid' => "Enter valid Account number"));

				$validate->digit(array('required' => true, 'field' => 'confirm_account_number', 'value' => $confirm_account_number,'min'=>$MIN_ACCOUNT_NUMBER,'max'=>$MAX_ACCOUNT_NUMBER), array('required' => 'Confirm Account number is required', 'invalid' => "Enter valid Account number"));
	        
	            if (!$validate->getError('confirm_account_number')) {
	                if ($confirm_account_number != $account_number) {
	                    $validate->setError('confirm_account_number', "Enter same Account Number");
	                }
	            }
	        }

	        if(empty($entered_routing_number) || !empty($routing_number)){
	            $validate->digit(array('required' => true, 'field' => 'routing_number', 'value' => $routing_number), array('required' => 'Routing number is required', 'invalid' => "Enter valid Routing number"));
	            if (!$validate->getError("routing_number")) {
	                if (checkRoutingNumber($routing_number) == false) {
	                    $validate->setError("routing_number", "Enter valid routing number");
	                }
	            }
	        }
	    }
	}
//payment data validation 

//Verification page
	$btn_submit_application = checkIsset($_POST['btn_submit_application']);
	if(!empty($btn_submit_application)){
		if(count($validate->getErrors()) > 0){
			$response['status_popup'] = 'edit_popup';
		}
		$password = checkIsset($_POST['password']);
		$c_password = checkIsset($_POST['c_password']);
		$product_check = checkIsset($_POST['product_check'],'arr');
		$product_term_check = checkIsset($_POST['product_term_check'],'arr');
		$product_term = checkIsset($_POST['product_term'],'arr');
		if(!empty($product_list)){
			foreach($product_list as $id){
				if(empty($product_check[$id])){
					// $validate->setError('product_check_'.$id, 'Please select product');
					$validate->setError('product_check_all', 'Please select all product');
				}
			}
		}
		if(!empty($product_term)){
			foreach($product_term as $id){
				if(empty($product_term_check[$id])){
					$validate->setError('product_term_check_'.$id, 'Please agree to terms and conditions');
				}
			}
		}
		$joinder_agreement = checkIsset($_POST['joinder_agreement']);
		$joinder_agreement_check = checkIsset($_POST['joinder_agreement_check']);

		if($joinder_agreement == "Y" && empty($joinder_agreement_check)){
			$validate->setError('joinder_agreement_check', 'Please agree to Joinder Agreement');
		}

		$signature_data = $_POST['signature_data'];
		$validate->string(array('required' => true, 'field' => 'signature_data', 'value' => $signature_data), array('required' => 'Please draw your signature'));
		if (!$validate->getError("signature_data")) {
			$signature_data_tmp = preg_replace('#^data:image/\w+;base64,#i', '', $signature_data);
			if(check_base64_image($signature_data_tmp) == false) {
				$validate->setError('signature_data', 'Please draw your signature');
			}
		}

		
		/*$validate->string(array('required' => true, 'field' => 'password', 'value' => $password), array('required' => 'Password is required'));
		$validate->string(array('required' => true, 'field' => 'c_password', 'value' => $c_password), array('required' => 'Confirm Password is required'));
		//for strong password
		if (!$validate->getError('password')) {
			if (strlen($password) < 8 || strlen($password) > 20) {
				$validate->setError('password', 'Password must be 8-20 characters');
			} else if ((!preg_match('`[A-Z]`', $password) || !preg_match('`[a-z]`', $password)) // at least one alpha
				|| !preg_match('`[0-9]`', $password)) {
				// at least one digit
				$validate->setError('password', 'Valid Password is required');
			} else if (!ctype_alnum($password)) {
				$validate->setError('password', 'Special character not allowed');
			} else if (preg_match('`[?/$\*+]`', $password)) {
				$validate->setError('password', 'Password not valid');
			} else if (preg_match('`[,"]`', $password)) {
				$validate->setError('password', 'Password not valid');
			} else if (preg_match("[']", $password)) {
				$validate->setError('password', 'Password not valid');
			}
		}
		if (!$validate->getError('c_password') && !$validate->getError('password')) {
			if ($password != $c_password) {
				$validate->setError('c_password', 'Both Password must be same');
			}
		}*/
	}
//Verification page

$old_status = "";
if($validate->isValid()){

	$lead_track = array(
		'status' => 'Validation Completed',
		'description' => 'Email/SMS Verification Validation Completed',
	);

	lead_tracking($lead_id,$customer_id,$lead_track);

	/*---------- Check Application Already Submitted ----------*/
	$lead_tracking_id = 0;
	if(!empty($customer_id) && !empty($lead_id) && !empty($order_id)) {
		$sql = 'SELECT id
				FROM lead_tracking 
				WHERE 
				status="submit_application_start" AND 
				(is_request_completed="N" OR order_status IN("Payment Approved","Pending Settlement")) AND 
				customer_id=:customer_id AND 
				lead_id=:lead_id AND 
				order_id=:order_id
				ORDER BY id DESC';
		$where = array(
			':customer_id'=>$customer_id,
			':lead_id'=>$lead_id,
			':order_id'=>$order_id
		);
		$already_submitted = $pdo->selectOne($sql,$where);
		if(!empty($already_submitted)) {
			$lead_track = array(
				'status' => 'Enrollment',
				'description' => 'Enrollment Application Already Submitted - ajax_edit_enrollment_verification',
			);
			lead_tracking($lead_id,$customer_id,$lead_track);
				
			setNotifyError("Enrollment Application Already Submitted");
			$response['status'] = 'application_already_submitted';
			header('Content-type: application/json');
			echo json_encode($response);
			exit;
		}
		$ld_desc = array(
			'page' => 'ajax_edit_enrollment_verification',
			'application_type' => 'email_sms_verification',
		);
		$tracking_data = array(
			'status' => 'submit_application_start',
			'is_request_completed' => 'N',
			'order_status' => '',
			'customer_id' => $customer_id,
			'lead_id' => $lead_id,
			'order_id' => $order_id,
			'description' => json_encode($ld_desc),
		);
		$lead_tracking_id = $pdo->insert('lead_tracking',$tracking_data);
	}
	/*----------/Check Application Already Submitted ----------*/

	$heading = array(
		'fname' => 'First Name',
		'lname' => 'Last Name',
		'ssn' => 'SSN',
		'birth_date' => 'Birth Date',
		'city' => 'City',
		'status' => 'Status'
	);

	$activity_update = false;
	$activity_description = array();
    $product_ids = array();
    $order_res = array();
   

    
    $plan_list = $prd_plan_type = array();
    $orderSql="SELECT od.id,od.order_id,website_id,p.id as product_id,od.fee_applied_for_product,od.qty,od.prd_plan_type_id,p.type,od.plan_id,p.name as product_name,p.product_code,od.unit_price,od.start_coverage_period,od.end_coverage_period,p.product_type,p.fee_type,o.created_at,p.payment_type_subscription,p.payment_type,p.company_id,od.member_price,od.group_price,od.contribution_type,od.contribution_value,p.is_member_benefits,p.is_fee_on_renewal,p.fee_renewal_type,p.fee_renewal_count FROM orders o 
    JOIN order_details od on (o.id=od.order_id and od.is_deleted='N')
    LEFT JOIN prd_main p ON(p.id=od.product_id and p.is_deleted='N')
    WHERE o.id=:order_id group by od.id";
    $orderRes=$pdo->select($orderSql,array(":order_id"=>$order_id));

    if(empty($orderRes)){
    	$orderSql="SELECT od.id,od.order_id,website_id,p.id as product_id,od.fee_applied_for_product,od.qty,od.prd_plan_type_id,p.type,od.plan_id,p.name as product_name,p.product_code,od.unit_price,od.start_coverage_period,od.end_coverage_period,p.product_type,p.fee_type,o.created_at,p.payment_type_subscription,p.payment_type,p.company_id,od.member_price,od.group_price,od.contribution_type,od.contribution_value,p.is_member_benefits,p.is_fee_on_renewal,p.fee_renewal_type,p.fee_renewal_count FROM group_orders o 
	    JOIN group_order_details od on (o.id=od.order_id AND od.is_deleted='N')
	    LEFT JOIN prd_main p ON(p.id=od.product_id and p.is_deleted='N')
	    WHERE o.id=:order_id group by od.id";
	    $orderRes=$pdo->select($orderSql,array(":order_id"=>$order_id));
    }
    $admin_fee = 0;
    $products = $all_products = array();
    $healhty_step = array('unit_price'=>0,'id'=>'','name'=>'');
    $sub_total = $service_fee = $grand_total = 0;
	
    if(count($orderRes)){
        foreach ($orderRes as $key => $row) {
				$plan_list[$row['product_id']]=$row['plan_id'];
				$prd_plan_type[$row['product_id']]=$row['prd_plan_type_id'];
                if(!in_array($row['product_type'],array('Healthy Step','ServiceFee'))){
                    $products[$row['product_id']] = $row;
                    $sub_total +=$row['unit_price'];
                }else if($row['product_type'] == 'Healthy Step'){
                    $healhty_step['name'] = $row['product_name'];
                    $healhty_step['id'] = $row['product_id'];
                    $healhty_step['unit_price'] = $row['unit_price'];

                }else if($row['product_type'] == 'ServiceFee'){
                    $service_fee+=$row['unit_price'];
                }else if($row['type']=='Fees' && in_array($row['product_type'],array('Carrier','Product','Vendor')) && $row['fee_type'] =='Charged'){
                    $admin_fee += $row['unit_price'];
				}
				$all_products[$row['product_id']] = $row;
        }
        $grand_total = $healhty_step['unit_price'] + $sub_total + $service_fee + $admin_fee;
    }
    $order_total = array(
		"sub_total" => $sub_total,
		"admin_fee" => $admin_fee,
		"service_fee" => $service_fee,
		"grand_total" => $grand_total,
	);
    if (!empty($btn_submit_application)){
		$order_status_res = '';
		$order_sql = "SELECT * FROM orders WHERE id=:order_id";
		$order_res = $pdo->selectOne($order_sql, array(":order_id" => $order_id));

		if(!empty($order_res)){
			$order_status_res = $order_res['status'];
		}
		if(in_array($group_billing_method,array('TPA','list_bill'))){
			if(!empty($order_res)){
				$pdo->delete("DELETE FROM orders WHERE id=:order_id",array("order_id"=>$order_id));
			}
			$order_sql = "SELECT * FROM group_orders WHERE id=:order_id";
			$order_res = $pdo->selectOne($order_sql, array(":order_id" => $order_id));

			if(!empty($order_res)){
				$order_status_res = $order_res['status'];
			}
			
		}else if(in_array($group_billing_method,array('individual'))){
			$group_order_sql = "SELECT * FROM group_orders WHERE id=:order_id";
			$group_order_res = $pdo->selectOne($group_order_sql, array(":order_id" => $order_id));

			if(!empty($group_order_res)){
				$order_status_res = $group_order_res['status'];
				$pdo->delete("DELETE FROM group_orders WHERE id=:order_id",array("order_id"=>$order_id));
			}
		}

		if(!$order_res){
			$order_id=0;
		}
	}
    /*------------ Update Customer Basic Info -------*/

	$purchase_products_array = array();
    // get product ids and coverage date
		if(!empty($all_products)){
			foreach ($all_products as $key => $row) {
				array_push($product_ids, $row['product_id']);
				
				if(!isset($purchase_products_array[$row['product_id']])){
					$purchase_products_array[$row['product_id']] = $row;
					$purchase_products_array[$row['product_id']]['qty'] = 1;
				}else{
					$purchase_products_array[$row['product_id']]['qty'] = $purchase_products_array[$row['product_id']]['qty'] + 1;
				}
			}
		}		
		$dependent_final_array = array();
		
		if(!empty($product_list)){
			foreach($product_list as $id){
				$product_plan = $prd_plan_type[$id];
				$test = array();
				$child_dependent=!empty($productWiseDependentCount[$id]['Child']) ? $productWiseDependentCount[$id]['Child'] : 0;
				$spouse_dependent=!empty($productWiseDependentCount[$id]['Spouse']) ? $productWiseDependentCount[$id]['Spouse']: 0;
				if(count($dep_ids) > 0){
					foreach ($dep_ids as $key => $dependent) {
						if(!empty($productAndPlanWiseDependent[$dependent]['dependent_product_list']) && in_array($id, $productAndPlanWiseDependent[$dependent]['dependent_product_list'])){
							$test[] = $productAndPlanWiseDependent[$dependent];
						}
					}
				}
				if(!empty($test)){			
					$dependent_final_array[] = array(
						"product_id" => $id,
						"plan_id" => $product_plan,
						"matrix_id" => $purchase_products_array[$id]['plan_id'],
						"child_dependent" => $child_dependent,
						"spouse_dependent" => $spouse_dependent,
						"dependent" => $test,
					);
				}
			}
		}

	$product_wise_dependents = array();
	if(count($dependent_final_array) > 0){
		foreach ($dependent_final_array as $dp) {
			$product_wise_dependents[$dp["product_id"]] = $dp["dependent"];
		}
	}
	// get product ids and coverage date
    /*---------- Sponsor Detail -----------*/
    $sponsor_sql = "SELECT CONCAT(fname,' ',lname) as name,id,type,upline_sponsors,level,payment_master_id,ach_master_id,email,rep_id,sponsor_id 
    FROM customer c WHERE type!='Customer' AND id = :id ";
	$sponsor_row = $pdo->selectOne($sponsor_sql, array(':id' => $sponsor_id));
    /*---------- Sponsor Detail -----------*/

    //********* Customer Table code start ********************
		$customerInfo = array(
			'fname' => $primary_fname,
			'lname' => $primary_lname,
			'updated_at' => 'msqlfunc_NOW()',
		);

		if (!empty($primary_ssn)) {
			$customerInfo['ssn'] = "msqlfunc_AES_ENCRYPT('" . str_replace("_", "", $primary_ssn) . "','" . $CREDIT_CARD_ENC_KEY . "')";
			$customerInfo['last_four_ssn'] = substr(str_replace("-", "", $primary_ssn), -4);
		}

		if ($customer_id > 0) {
			$upd_where = array(
				'clause' => 'id = :id',
				'params' => array(
					':id' => $customer_id,
				),
			);
			$customerInfo = array_filter($customerInfo, "strlen"); //removes null and blank array fields from array		
			$customer_update = $pdo->update('customer', $customerInfo, $upd_where,true);

			$lead_track = array(
				'status' => 'Email/SMS Verification',
				'description' => 'Update customer data',
			);

			lead_tracking($lead_id,$customer_id,$lead_track);

			if(!empty($customer_update)){
				$activity_description['description_customer'] = 'Primary member information updated : <br>';
				foreach($customer_update as $key => $data){
					if(array_key_exists($key,$customerInfo)){
						$activity_description['key_value']['desc_arr'][$key] = 	'updated from '.$data.' to '.$customerInfo[$key];
					}
				}
				$activity_update = true;
			}
			$response['primary_member_details'] = $primary_fname.' '.$primary_lname;
        }

        $cust_old_status = "";
		if (!empty($customer_id)) {
			$cust_old_status = getname('customer',$customer_id,'status');
		}

		$sqlCustomerSetting = "SELECT id,signature_file FROM customer_settings where customer_id=:customer_id";
        $resCustomerSetting = $pdo->selectOne($sqlCustomerSetting,array(":customer_id"=>$customer_id));

		$customerSettingParams=array();
        
        if (!empty($btn_submit_application)) {
			$signature_file_name = $primary_fname . time() . '.png';
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
		    
			if (!empty($signature_file_name)) {
				$customerSettingParams['signature_file'] = $signature_file_name;
                $customerSettingParams['signature_date'] = 'msqlfunc_NOW()';
			}

			$lead_track = array(
				'status' => 'Email/SMS Verification',
				'description' => 'Store signature file',
			);

			lead_tracking($lead_id,$customer_id,$lead_track);

			// if (file_exists($SIGNATURE_DIR . $resCustomerSetting['signature_file'])) {
			// 	unlink($SIGNATURE_DIR . $resCustomerSetting['signature_file']);
			// }

            if(!empty($customerSettingParams)){
                if(!empty($resCustomerSetting['id'])){
                    $upd_where = array(
                        'clause' => 'id = :id',
                        'params' => array(
                            ':id' => $resCustomerSetting['id'],
                        ),
                    );
					$pdo->update('customer_settings', $customerSettingParams, $upd_where);
                }
            }
        }
		
    //********* Customer Table code end   ********************
    
    $primary_phone = phoneReplaceMain($primary_phone);
    
    //********* Lead Table code start ********************
		$leadInfo = array(
			'fname' => $primary_fname,
			'lname' => $primary_lname,
			'updated_at' => 'msqlfunc_NOW()',
		);
		if ($lead_id > 0) {
			$where = array(
				"clause" => "id=:id",
				"params" => array(
					":id" => $lead_id,
				),
			);
			$leadInfo = array_filter($leadInfo, "strlen"); //removes null and blank array fields from array
			$pdo->update("leads", $leadInfo, $where);

			$lead_track = array(
				'status' => 'Email/SMS Verification',
				'description' => 'Update lead data',
			);

			lead_tracking($lead_id,$customer_id,$lead_track);

			$tempDesc = array(
				'agent_id' => $sponsor_id,
				'ip_address' => $_SERVER['SERVER_ADDR'],
				'member' => $primary_fname .' '.$primary_lname,
				'member_id' => $customer_id
			);
		}
	//********* Lead Table code end   ********************	
	
	//********* Beneficiary Update Code Start ********************
		$tmpPrincipal = !empty($_POST['principal_queBeneficiaryFullName']) ? $_POST['principal_queBeneficiaryFullName'] : array();
		if(!empty($tmpPrincipal)){
			foreach ($tmpPrincipal as $key => $value) {
				$principal_beneficiary_id = !empty($_POST['principal_beneficiary_id'][$key]) ? $_POST['principal_beneficiary_id'][$key] : 0;
				if(!empty($principal_beneficiary_id)) {
					$sqlBeneficiery = "SELECT id FROM customer_beneficiary where id=:id";
					$resBeneficiery = $pdo->selectOne($sqlBeneficiery,array(":id"=>$principal_beneficiary_id));
				} else {
					$resBeneficiery = array();
				}

				$benficiaryProduct = !empty($_POST['principal_product'][$key]) ? implode(",", $_POST['principal_product'][$key]) : '';
				$name = !empty($_POST['principal_queBeneficiaryFullName'][$key]) ? $_POST['principal_queBeneficiaryFullName'][$key] : '';
				$address = !empty($_POST['principal_queBeneficiaryAddress'][$key]) ? $_POST['principal_queBeneficiaryAddress'][$key] : '';
				$cell_phone = !empty($_POST['principal_queBeneficiaryPhone'][$key]) ? phoneReplaceMain($_POST['principal_queBeneficiaryPhone'][$key]) : '';
				$email = !empty($_POST['principal_queBeneficiaryEmail'][$key]) ? $_POST['principal_queBeneficiaryEmail'][$key] : '';
				$ssn = !empty($_POST['principal_queBeneficiarySSN'][$key]) ? $_POST['principal_queBeneficiarySSN'][$key] : '';
				$relationship = !empty($_POST['principal_queBeneficiaryRelationship'][$key]) ? $_POST['principal_queBeneficiaryRelationship'][$key] : '';
				$percentage = !empty($_POST['principal_queBeneficiaryPercentage'][$key]) ? $_POST['principal_queBeneficiaryPercentage'][$key] : '';
				$insParams=array(
					'beneficiary_type'=>'Principal',
					// 'product_ids'=> $benficiaryProduct,
					'customer_id'=>$customer_id,
					'name'=>$name,
					'address'=>$address,
					'cell_phone'=>$cell_phone,
					'email'=>$email,
					'relationship'=>$relationship,
					'percentage'=>$percentage,
				);
				if(!empty($ssn)){
					$insParams['ssn'] = "msqlfunc_AES_ENCRYPT('" . str_replace("-", "", $ssn) . "','" . $CREDIT_CARD_ENC_KEY . "')";
					$insParams['last_four_ssn'] = substr(str_replace("-", "", $ssn), -4);
				}

				if(!empty($resBeneficiery)){
					$updWhr = array(
						'clause' => 'id = :id',
						'params' => array(
							':id' => $resBeneficiery['id'],
						),
					);
					$customer_beneficiary_array = $pdo->update("customer_beneficiary",$insParams,$updWhr,true);

					if(!empty($customer_beneficiary_array)){
						$activity_description['key_value']['desc_arr']['Principal Beneficiary '.$key] = "Name : ". $insParams['name'].' :  customer beneficiary Information Updated.';
						foreach($customer_beneficiary_array as $bkey => $cbeneficiary){
							if($cbeneficiary != $insParams[$bkey]){
								$activity_description['key_value']['desc_arr']['Beneficiary '.$bkey.' '.$principal_beneficiary_id] = 'updated from '.$cbeneficiary.' to '.$insParams[$bkey];
								$activity_update = true;
							}
						}
					}
					$lead_track = array(
						'status' => 'Updated',
						'description' => 'Updated customer beneficiary table',
					);
				
					lead_tracking($lead_id,$customer_id,$lead_track);

				}
			}
		}

		$tmpContingent = !empty($_POST['contingent_queBeneficiaryFullName']) ? $_POST['contingent_queBeneficiaryFullName'] : array();
		if(!empty($tmpContingent)){
			foreach ($tmpContingent as $key => $value) {
				$contingent_beneficiary_id = !empty($_POST['contingent_beneficiary_id'][$key]) ? $_POST['contingent_beneficiary_id'][$key] : 0;
				if(!empty($contingent_beneficiary_id)) {
					$sqlBeneficiery = "SELECT id FROM customer_beneficiary where id=:id";
					$resBeneficiery = $pdo->selectOne($sqlBeneficiery,array(":id"=>$contingent_beneficiary_id));
				} else {
					$resBeneficiery = array();
				}

				$benficiaryProduct = !empty($_POST['contingent_product'][$key]) ? implode(",", $_POST['contingent_product'][$key]) : '';
				$name = !empty($_POST['contingent_queBeneficiaryFullName'][$key]) ? $_POST['contingent_queBeneficiaryFullName'][$key] : '';
				$address = !empty($_POST['contingent_queBeneficiaryAddress'][$key]) ? $_POST['contingent_queBeneficiaryAddress'][$key] : '';
				$cell_phone = !empty($_POST['contingent_queBeneficiaryPhone'][$key]) ? phoneReplaceMain($_POST['contingent_queBeneficiaryPhone'][$key]) : '';
				$email = !empty($_POST['contingent_queBeneficiaryEmail'][$key]) ? $_POST['contingent_queBeneficiaryEmail'][$key] : '';
				$ssn = !empty($_POST['contingent_queBeneficiarySSN'][$key]) ? $_POST['contingent_queBeneficiarySSN'][$key] : '';
				$relationship = !empty($_POST['contingent_queBeneficiaryRelationship'][$key]) ? $_POST['contingent_queBeneficiaryRelationship'][$key] : '';
				$percentage = !empty($_POST['contingent_queBeneficiaryPercentage'][$key]) ? $_POST['contingent_queBeneficiaryPercentage'][$key] : '';
				$insParams=array(
					'beneficiary_type'=>'Contingent',
					// 'product_ids'=> $benficiaryProduct,
					'customer_id'=>$customer_id,
					'name'=>$name,
					'address'=>$address,
					'cell_phone'=>$cell_phone,
					'email'=>$email,
					'relationship'=>$relationship,
					'percentage'=>$percentage,
				);
				if(!empty($ssn)){
					$insParams['ssn'] = "msqlfunc_AES_ENCRYPT('" . str_replace("-", "", $ssn) . "','" . $CREDIT_CARD_ENC_KEY . "')";
					$insParams['last_four_ssn'] = substr(str_replace("-", "", $ssn), -4);
				}
				if(!empty($resBeneficiery)){
					$updWhr = array(
						'clause' => 'id = :id',
						'params' => array(
							':id' => $resBeneficiery['id'],
						),
					);
					$customer_beneficiary_array = $pdo->update("customer_beneficiary",$insParams,$updWhr,true);

					if(!empty($customer_beneficiary_array)){
						$activity_description['key_value']['desc_arr']['Contingent Beneficiary '.$key] = "Name : ". $insParams['name'].' :  customer beneficiary Information Updated.';
						foreach($customer_beneficiary_array as $bkey => $cbeneficiary){
							if($cbeneficiary != $insParams[$bkey]){
								$activity_description['key_value']['desc_arr']['Beneficiary '.$bkey.' '.$contingent_beneficiary_id] = 'updated from '.$cbeneficiary.' to '.$insParams[$bkey];
								$activity_update = true;
							}
						}
					}

					$lead_track = array(
						'status' => 'Updated',
						'description' => 'Updated customer beneficiary table',
					);
				
					lead_tracking($lead_id,$customer_id,$lead_track);
				}
			}
		}
	//********* Beneficiery Update Code End ********************

	//********* Billing Profile Table code start ********************
		$orderBillingId = 0;
		$bill_ins_id = 0;
		if($enrollmentLocation!='groupSide' || ($enrollmentLocation=='groupSide' && $group_billing_method == 'individual')){
			$isDefaultCheck = $pdo->selectOne("SELECT id FROM customer_billing_profile WHERE customer_id = :customer_id and is_deleted='N' AND is_default = 'Y'", array(':customer_id' => $customer_id));
			if ($payment_mode == "CC") {
				
				$expiry_month = substr($expiration,0,2);
				$expiry_year = substr($expiration,-2);
				$billParams = array(
					'customer_id' => $customer_id,
					'fname' => !empty($name_on_card) ? makeSafe($name_on_card) : makeSafe($bill_fname),
					// 'lname' => makeSafe($bill_lname),
					'email' => makeSafe($primary_email),
					'country_id' => 231,
					'country' => 'United States',
					'state' => makeSafe($bill_state),
					'city' => makeSafe($bill_city),
					'zip' => makeSafe($bill_zip),
					'address' => makeSafe($bill_address),
					'address2' => makeSafe($bill_address2),
					'is_address_verified' => ($bill_address == $primary_address_1?($is_valid_address == "Y"?"Y":"N"):"N"),
					'card_type' => makeSafe($card_type),
					'expiry_month' => makeSafe($expiry_month),
					'expiry_year' => makeSafe($expiry_year),
					'created_at' => 'msqlfunc_NOW()',
					'payment_mode' => 'CC',
				);
				
				$billParams['card_no'] = makeSafe(substr($card_number, -4));
				$billParams['last_cc_ach_no'] = makeSafe(substr($card_number, -4));
				$billParams['card_no_full'] = "msqlfunc_AES_ENCRYPT('" . $card_number . "','" . $CREDIT_CARD_ENC_KEY . "')";

				if($cvv!=''){
					$billParams['cvv_no'] = makeSafe($cvv);
				}
				if(empty($isDefaultCheck)){
					$billParams['is_default'] = 'Y';
				}
				$billParams['ip_address'] = !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'];
				$billParams['updated_at'] = 'msqlfunc_NOW()';
				if(!empty($billing_profile_id)){
					$isCustomerBillingExists = $pdo->selectOne("SELECT * FROM customer_billing_profile WHERE id = :id and is_deleted='N'", array(':id' => $billing_profile_id));
					if (empty($isCustomerBillingExists)) {
						$bill_ins_id = $pdo->insert("customer_billing_profile", $billParams);

						$lead_track = array(
							'status' => 'Email/SMS Verification',
							'description' => 'Inserted billing profile',
						);

						lead_tracking($lead_id,$customer_id,$lead_track);
					} else {
						unset($billParams['created_at']);
						$bill_ins_id = $isCustomerBillingExists['id'];
						$pdo->update("customer_billing_profile", $billParams, array("clause" => "id=:id", "params" => array(":id" => $isCustomerBillingExists['id'])));

						$lead_track = array(
							'status' => 'Email/SMS Verification',
							'description' => 'Updated billing profile',
						);

						lead_tracking($lead_id,$customer_id,$lead_track);
					}
				} else {
					$bill_ins_id = $pdo->insert("customer_billing_profile", $billParams);

					$lead_track = array(
						'status' => 'Email/SMS Verification',
						'description' => 'Inserted billing profile',
					);

					lead_tracking($lead_id,$customer_id,$lead_track);

				}
				$billParams['order_id'] = $order_id;
				// 'cvv_no'	=> makeSafe($cvv),
				$billing_id = getname('order_billing_info',$order_id,'id','order_id');
				$billParams['customer_billing_id'] = $bill_ins_id;

				unset($billParams['created_at']);
				unset($billParams['ip_address']);
				unset($billParams['is_default']);	

				if ($billing_id > 0) {
					
					$order_billing_info_update =  $pdo->update("order_billing_info", $billParams, array("clause" => "id=:id", "params" => array(":id" => $billing_id)),true);

					$lead_track = array(
						'status' => 'Email/SMS Verification',
						'description' => 'Updated order billing info',
					);

					lead_tracking($lead_id,$customer_id,$lead_track);

					if(!empty($order_billing_info_update)){
						$activity_description['description_billing_info'] = 'Billing information updated. <br>';
						$activity_update = true;
					}

				} else {
					$pdo->insert("order_billing_info", $billParams);

					$lead_track = array(
						'status' => 'Email/SMS Verification',
						'description' => 'Inserted order billing info',
					);

					lead_tracking($lead_id,$customer_id,$lead_track);
				}

				$response['billing_details'] = $bill_fname.'<br> CC *'.$billParams['last_cc_ach_no'];
				$response['cc_billing_detail'] = '('.$card_type.' *'.$billParams['last_cc_ach_no'].')';
				/*----- code for order and customer billing profile --------*/
			} else {
				$billParams = array(
					'order_id' => $order_id,
					'customer_id' => $customer_id,
					'fname' => makeSafe($bill_fname),
					// 'lname' => makeSafe($bill_lname),
					'email' => makeSafe($primary_email),
					'country_id' => 231,
					'country' => 'United States',
					'state' => makeSafe($bill_state),
					'city' => makeSafe($bill_city),
					'zip' => makeSafe($bill_zip),
					'address' => makeSafe($bill_address),
					'address2' => makeSafe($bill_address2),
					'is_address_verified' => ($bill_address == $primary_address_1?($is_valid_address == "Y"?"Y":"N"):"N"),
					'created_at' => 'msqlfunc_NOW()',
					'payment_mode' => 'ACH',
					'ach_account_type' => $account_type,
					'bankname' => $ach_name,
				);

				if ($account_number != "") {
					$billParams['last_cc_ach_no'] = makeSafe(substr($account_number, -4));
					$billParams['ach_account_number'] = "msqlfunc_AES_ENCRYPT('" . $account_number . "','" . $CREDIT_CARD_ENC_KEY . "')";
				}else{
					$billParams['last_cc_ach_no'] = makeSafe(substr($entered_account_number, -4));
					$billParams['ach_account_number'] = "msqlfunc_AES_ENCRYPT('" . $entered_account_number . "','" . $CREDIT_CARD_ENC_KEY . "')";
				}
				$rno = '';
				if ($routing_number != "") {
					$billParams['ach_routing_number'] = "msqlfunc_AES_ENCRYPT('" . $routing_number . "','" . $CREDIT_CARD_ENC_KEY . "')";
					$rno = $routing_number;
				}else{
					$billParams['ach_routing_number'] = "msqlfunc_AES_ENCRYPT('" . $entered_routing_number . "','" . $CREDIT_CARD_ENC_KEY . "')";
					$rno = $entered_routing_number;
				}
				if(empty($isDefaultCheck)){
					$billParams['is_default'] = 'Y';
				}
				$billParams['ip_address'] = !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'];
				$billParams['updated_at'] = 'msqlfunc_NOW()';

				unset($billParams['order_id']);
				if(!empty($billing_profile_id)){
					$isCustomerBillingExists = $pdo->selectOne("SELECT * FROM customer_billing_profile WHERE id = :id and is_deleted='N'", array(":id" => $billing_profile_id));
					if (empty($isCustomerBillingExists)) {
						$bill_ins_id = $pdo->insert("customer_billing_profile", $billParams);

						$lead_track = array(
							'status' => 'Email/SMS Verification',
							'description' => 'Inserted customer billing profile',
						);

						lead_tracking($lead_id,$customer_id,$lead_track);

					} else {
						unset($billParams['created_at']);
						$bill_ins_id = $isCustomerBillingExists['id'];
						$customer_billing_profile_update = $pdo->update("customer_billing_profile", $billParams, array("clause" => "id=:id", "params" => array(":id" => $isCustomerBillingExists['id'])),true);

						$lead_track = array(
							'status' => 'Email/SMS Verification',
							'description' => 'Updated customer billing profile',
						);

						lead_tracking($lead_id,$customer_id,$lead_track);

						if(!empty($customer_billing_profile_update)){
							$activity_description['description_billing_info_1'] = 'Billing information updated. <br>';
							$activity_update = true;
						}
					}
				} else {
					$bill_ins_id = $pdo->insert("customer_billing_profile", $billParams);

					$lead_track = array(
						'status' => 'Email/SMS Verification',
						'description' => 'Inserted customer billing profile',
					);

					lead_tracking($lead_id,$customer_id,$lead_track);
				}
				$billParams['order_id'] = $order_id;
				$billing_id = getname('order_billing_info',$order_id,'id','order_id');

				unset($billParams['created_at']);
				unset($billParams['ip_address']);
				unset($billParams['is_default']);

				$billParams['customer_billing_id'] = $bill_ins_id;
				if ($billing_id > 0) {
					$pdo->update("order_billing_info", $billParams, array("clause" => "id=:id", "params" => array(":id" => $billing_id)));

					$lead_track = array(
						'status' => 'Email/SMS Verification',
						'description' => 'Updated order billing info',
					);

					lead_tracking($lead_id,$customer_id,$lead_track);

				} else {
					$pdo->insert("order_billing_info", $billParams);

					$lead_track = array(
						'status' => 'Email/SMS Verification',
						'description' => 'Inserted order billing info',
					);

					lead_tracking($lead_id,$customer_id,$lead_track);
				}

				$response['billing_details'] = $bill_fname.'<br> ACH *'.$billParams['last_cc_ach_no'];
				$response['routing_number_detail'] = 'Routing Number (***'.substr($rno, -4).')<span class="req-indicator">*</span>';
				$response['ach_billing_detail'] = '(ACH *'.$billParams['last_cc_ach_no'].')<span class="req-indicator">*</span>';
				/*----- code for order and customer billing profile --------*/
			}
		}
	//********* Billing Profile Table code end   ********************

	//********* dependent table code start ********************
		$member_setting = $memberSetting->get_status_by_payment("","",($enroll_with_post_date == 'yes' ? true : false),$cust_old_status,array("is_from_enrollment" => true));
		if(count($dependent_final_array) > 0){
			$exists_dep = array();
			$dependent_details = '';
			$existing_dep = array();
			foreach ($dependent_final_array as $dp) {
					$prd_id = $dp["product_id"];
					$prd_mat_id = $dp["matrix_id"];
					$prd_plan_type_id = $dp['plan_id'];
					$product_wise_dependents[$dp["product_id"]] = $dp["dependent"];
					foreach ($dp["dependent"] as $d) {
						$cust_dep_sql = "SELECT id FROM customer_dependent WHERE product_plan_id=:product_plan_id AND cd_profile_id=:cd_profile_id AND customer_id=:customer_id AND (terminationDate IS NULL OR terminationDate='')";
						$cust_dep_where = array(":product_plan_id" =>$prd_mat_id,":cd_profile_id" =>$d['dependent_id'],":customer_id" =>$customer_id);
						$cd_profile_id = !empty($d['dependent_id']) ? $d['dependent_id'] :0;
						$cust_dep_res = $pdo->selectOne($cust_dep_sql,$cust_dep_where);
						if(count($cust_dep_res) > 0){
							$dependent_params = array(
								'customer_id' => $customer_id,
								'order_id' => (isset($order_id) && $order_id != 0) ? $order_id : 0,
								'product_id' => (isset($prd_id)) ? $prd_id : 0,
								'product_plan_id' => $prd_mat_id,
								'prd_plan_type_id' => (isset($prd_plan_type_id)) ? $prd_plan_type_id : 0,
								'relation' => $d["relation"],
								'fname' => $d["fname"],
								'lname' => $d["lname"],
								'birth_date' => date('Y-m-d', strtotime($d["birthdate"])),
								'gender' => $d["gender"],
								'status' => $member_setting['dependent_status'],
								'is_deleted' => 'N',
								'updated_at' => 'msqlfunc_NOW()',
							);
							if (isset($d["email"]) && $d["email"]!="") {
								$dependent_params['email'] = $d["email"];
							}else{
								$dependent_params['email'] = "";
							}
							if (isset($d["phone"]) && $d["phone"]!="") {
								$dependent_params['phone'] = phoneReplaceMain($d["phone"]);
							}else{
								$dependent_params['phone'] ="";
							}
							if (isset($d["state"]) && $d["state"]!="") {
								$dependent_params['state'] = $d["state"];
							}else{
								$dependent_params['state'] = $primary_state;
							}
							if (isset($d["zip"]) && $d["zip"]!="") {
								$dependent_params['zip_code'] = $d["zip"];
							}else{
								$dependent_params['zip_code'] = $primary_zip;	
							}
							if (isset($d["salary"]) && $d["salary"]!="") {
								$dependent_params['salary']=$d["salary"];
							}else{
								$dependent_params['salary']="";
							}
							if (isset($d["employment_status"]) && $d["employment_status"]!="") {
								$dependent_params['employmentStatus']=$d["employment_status"];
							}else{
								$dependent_params['employmentStatus']="";
							}
							if (isset($d["tobacco_status"]) && $d["tobacco_status"]!="") {
								$dependent_params['tobacco_use']=$d["tobacco_status"];
							}else{
								$dependent_params['tobacco_use']="";
							}
							if (isset($d["smoking_status"]) && $d["smoking_status"]!="") {
								$dependent_params['smoke_use']=$d["smoking_status"];
							}else{
								$dependent_params['smoke_use']="";
							}
							if (isset($d["height"]) && $d["height"]!="") {
								$dependent_height_array = explode(".", $d["height"]);
								$dependent_params['height_feet']=$dependent_height_array[0];
								$dependent_params['height_inches']=$dependent_height_array[1];
							}else{
								$dependent_params['height_feet']='';
								$dependent_params['height_inches']='';
							}
							if (isset($d["weight"]) && $d["weight"]!="") {
								$dependent_params['weight']=$d["weight"];
							}else{
								$dependent_params['weight']="";
							}
							if (isset($d["SSN"]) &&  $d["SSN"]!= '') {
								$dependent_params['ssn'] = str_replace("-", "", $d["SSN"]);
								$dependent_params['last_four_ssn'] = substr(str_replace("-", "", $d["SSN"]), -4);
							}
							if ($enroll_with_post_date == "yes") {
								$dependent_params['status'] = "Post Payment";
							}

							$cdp_param = array(
								'customer_id' => $customer_id,
								'relation' => $dependent_params['relation'],
								'fname' => $dependent_params['fname'],
								'lname' => $dependent_params['lname'],
								'birth_date' => $dependent_params['birth_date'],
								'gender' => $dependent_params['gender'],
								'email' => $dependent_params['email'],
								'phone' => $dependent_params['phone'],
								'state' => $dependent_params['state'],
								'zip_code' => $dependent_params['zip_code'],
								'salary' => $dependent_params['salary'],
								'employmentStatus' => $dependent_params['employmentStatus'],
								'tobacco_use' => $dependent_params['tobacco_use'],
								'smoke_use' => $dependent_params['smoke_use'],
								'height_feet' => $dependent_params['height_feet'],
								'height_inches' => $dependent_params['height_inches'],
								'weight' => $dependent_params['weight'],
							);
							if (isset($d["SSN"]) &&  $d["SSN"]!= '') {
								$cdp_param['ssn'] = str_replace("-", "", $d["SSN"]);
								$cdp_param['last_four_ssn'] = substr(str_replace("-", "", $d["SSN"]), -4);
							}
							$dependent_profile_where = array(
								"clause" => "id = :id",
								"params" => array(
									":id" => $d['dependent_id']
								),
							);
							$dependent_where = array(
								"clause" => "id=:id",
								"params" => array(
									":id" => $cust_dep_res['id']
								),
							);
							$pdo->update("customer_dependent_profile", $cdp_param, $dependent_profile_where);
							$customer_dependent_update = $pdo->update("customer_dependent", $dependent_params, $dependent_where,true);

							$lead_track = array(
								'status' => 'Email/SMS Verification',
								'description' => 'Updated customer dependent and customer dependent profile',
							);

							lead_tracking($lead_id,$customer_id,$lead_track);

							//Store Dependent Profile Benefit Amount
							if(!empty($dependent_params['benefit_amount']) || !empty($dependent_params['in_patient_benefit']) || !empty($dependent_params['out_patient_benefit']) || !empty($dependent_params['monthly_income'])) {
								$dep_benefit_param = array(
									"benefit_amount" => checkIsset($dependent_params['benefit_amount']),
									"in_patient_benefit" => checkIsset($dependent_params['in_patient_benefit']),
									"out_patient_benefit" => checkIsset($dependent_params['out_patient_benefit']),
									"monthly_income" => checkIsset($dependent_params['monthly_income']),
									"benefit_percentage" => checkIsset($dependent_params['benefit_percentage']),
								);
								save_customer_dependent_profile_benefit_amount($d['dependent_id'],$dependent_params['product_id'],$dep_benefit_param);
							}

							if(!empty($customer_dependent_update)){
								
								if(!in_array($d['dependent_id'],$exists_dep)) {
									
									foreach($customer_dependent_update as $key => $data){
										if(array_key_exists($key,$dependent_params) && $key!='ssn' && !empty($data)){
											$activity_description['cust_dependent_desc'] = 'Customer dependent information updated. <br>';
											$heading_1 = $key;
											if(!empty($heading[$key])){
												$heading_1 = $heading[$key];
											}
											$activity_description['description_cs_arr'][] = $heading_1. ' updated from '.$data.' to '.$dependent_params[$key];
											array_push($exists_dep,$d['dependent_id']);
											$activity_update = true;
										}
									}
								}
							}
						}else{
							$dependent_params = array(
								'customer_id' => $customer_id,
								'order_id' => (isset($order_id) && $order_id != 0) ? $order_id : 0,
								'product_id' => (isset($prd_id)) ? $prd_id : 0,
								'product_plan_id' => $prd_mat_id,
								'relation' => $d["relation"],
								'fname' => $d["fname"],
								'lname' => $d["lname"],
								'birth_date' => date('Y-m-d', strtotime($d["birthdate"])),
								'gender' => $d["gender"],
								// 'status' => ($paymentApproved ? 'Active' : 'Pending Declined'),
								'is_deleted' => 'N',
								'updated_at' => 'msqlfunc_NOW()',
							);
							if (!empty($d["email"])) {
								$dependent_params['email'] = $d["email"];
							}
							if (!empty($d["phone"])) {
								$dependent_params['phone'] = phoneReplaceMain($d["phone"]);
							}
							if (!empty($d["state"])) {
								$dependent_params['state'] = $d["state"];
							}
							if (!empty($d["zip"])) {
								$dependent_params['zip_code'] = $d["zip"];
							}
							if (!empty($d["salary"])) {
								$dependent_params['salary']=$d["salary"];
							}
							if (!empty($d["employment_status"])) {
								$dependent_params['employmentStatus']=$d["employment_status"];
							}
							if (!empty($d["tobacco_status"])) {
								$dependent_params['tobacco_use']=$d["tobacco_status"];
							}
							if (!empty($d["smoking_status"])) {
								$dependent_params['smoke_use']=$d["smoking_status"];
							}
							if (!empty($d["height"])) {
								$dependent_height_array = explode(".", $d["height"]);
								$dependent_params['height_feet']=$dependent_height_array[0];
								$dependent_params['height_inches']=$dependent_height_array[1];
							}
							if (!empty($d["weight"])) {
								$dependent_params['weight']=$d["weight"];
							}
							if (!empty($d["SSN"])) {
								$dependent_params['ssn'] = str_replace("-", "", $d["SSN"]);
								$dependent_params['last_four_ssn'] = substr(str_replace("-", "", $d["SSN"]), -4);
							}
							if ($enroll_with_post_date == "yes") {
								$dependent_params['status'] = $member_setting['dependent_status'];
							}

							$function_list->insert_dependent($dependent_params, $prd_mat_id,($admin_id > 0 ? 1:0),$cd_profile_id);

							$lead_track = array(
								'status' => 'Email/SMS Verification',
								'description' => 'Inserted customer dependent and customer dependent profile',
							);

							lead_tracking($lead_id,$customer_id,$lead_track);
						}
						
						if(!in_array($d['dependent_id'],$existing_dep)){
							$dependent_details .= getRevRelation($d['relation'],$d['gender']).' - '.ucfirst($d['fname']).' '.ucfirst($d['lname']).'<br>';
							array_push($existing_dep,$d['dependent_id']);
						}
					
				}
			}
			$response['dependent_details'] = $dependent_details;
		}
	//********* dependent table code end   ********************

	//********* update post payment date code start   ********************
		$orderParams = array();
		if($enrollmentLocation!='groupSide' || ($enrollmentLocation=='groupSide' && $group_billing_method == 'individual')){
			if ($enroll_with_post_date == "yes") {
				$orderParams['post_date'] = date("Y-m-d", strtotime($post_date));
				$orderParams['future_payment'] = 'Y';
				$response['post_payment_details'] = date('m/d/Y',strtotime($orderParams['post_date']));
			} else {
				//if post date is not setup then add coverage date in post_date field
				$orderParams['post_date'] = date("Y-m-d", strtotime($lowest_coverage_date.'-1 days'));
				$orderParams['future_payment'] = 'N';
				$response['post_payment_details'] = 'not_set';
			}
			if ($order_id > 0 && !empty($orderParams)) {
				$order_where1 = array("clause" => "id=:id", "params" => array(":id" => $order_id));
				$post_date_update = $pdo->update("orders", $orderParams, $order_where1,true);
				if(($enroll_with_post_date =='yes' && (!empty($post_date) && !empty($post_date_update['post_date']) && strtotime($post_date_update['post_date']) != strtotime($post_date) ||  !empty($post_date_update['future_payment']))) || $enroll_with_post_date =='no' && !empty($post_date_update['future_payment']) && $post_date_update['future_payment'] =='Y'){
					$od_id = getname('orders',$order_id,'display_id');

					$lead_res = $pdo->selectOne("SELECT lead_id FROM leads WHERE id = :lead_id", array(":lead_id" => $lead_id));
					
					if(!empty($post_date_update['future_payment'])){
						if($post_date_update['future_payment'] == 'N'){
							$ac_description_post_date['ac_message'] = array(
								'ac_red_1'=>array(
									'href'=>$AGENT_HOST.'/lead_details.php?id='.md5($lead_id),
									'title'=>$lead_res['lead_id'],
								),
								'ac_message_2' =>'  set Order  ',
								'ac_red_2'=>array(
									'href'=>$ADMIN_HOST.'/all_orders.php?id='.md5($order_id),
									'title'=>$od_id,
								),
								'ac_message_3' =>' to POST PAYMENT on  '.$post_date,
							);
						}elseif($post_date_update['future_payment'] == 'Y'){
							$ac_description_post_date['ac_message'] = array(
								'ac_red_1'=>array(
									'href'=>$AGENT_HOST.'/lead_details.php?id='.md5($lead_id),
									'title'=>$lead_res['lead_id'],
								),
								'ac_message_2' =>'  unset post date on Order  ',
								'ac_red_2'=>array(
									'href'=>$ADMIN_HOST.'/all_orders.php?id='.md5($order_id),
									'title'=>$od_id,
								),
							);
						}
						
					}else{
						$ac_description_post_date['ac_message'] = array(
							'ac_red_1'=>array(
								'href'=>$AGENT_HOST.'/lead_details.php?id='.md5($lead_id),
								'title'=>$lead_res['lead_id'],
							),
							'ac_message_2' =>' updated post payment date on ',
							'ac_red_2'=>array(
								'href'=>$ADMIN_HOST.'/all_orders.php?id='.md5($order_id),
								'title'=>$od_id,
							),
							'ac_message_3' =>' to '.$post_date,
						);
					}
					
					activity_feed(3, $lead_id,'Lead', $lead_id, 'Lead', 'Enrollment Verification Updates', $primary_fname, $primary_lname,json_encode($ac_description_post_date), $REQ_URL);
				}
			}
		}
	//********* update post payment date code end   ********************
	
	$orderParams = array();
	$total_attempts = 0;
	$check_attempts = getname('website_subscriptions',$order_id,'total_attempts','last_order_id');
	$check_attempts = 0;
	$attempt_date  = getname('website_subscriptions',$order_id,'next_attempt_at','last_order_id');
	$attempt_date = '';
	$today = date('Y-m-d');
	$paymentApproved = false;
    if (!empty($btn_submit_application) && $check_attempts < 5 && ($attempt_date=='' || strtotime($today) ==  strtotime($attempt_date) ) ) {

		$order_display_id = 0;
		if ($order_id > 0) {
			$order_display_id = $order_res['display_id'];
		} else {
			$order_display_id = $function_list->get_order_id();
		}

        //********* Payment code start ********************
        	if($enrollmentLocation!='groupSide' || ($enrollmentLocation=='groupSide' && $group_billing_method == 'individual')){
				$payment_error = '';
				$PlanIdArr = array();
				if(count($product_ids) > 0){
				    foreach ($product_ids as $key => $product) {
				        $product_id = $product;
				        $matrix_id = $plan_list[$product_id];
				        if(!empty($matrix_id) && !in_array($matrix_id, $PlanIdArr)){
				        	array_push($PlanIdArr, $matrix_id);
				        }
				    }
				}
				$sale_type_params = array();
	            $sale_type_params['is_renewal'] = "N";
				if ($sponsor_row['type'] == 'Group') {
					$payment_master_id = $function_list->get_agent_merchant_detail($PlanIdArr, $sponsor_row['sponsor_id'], $payment_mode,$sale_type_params);
				} else {
					$payment_master_id = $function_list->get_agent_merchant_detail($PlanIdArr, $sponsor_row['id'], $payment_mode,$sale_type_params);
				}
				$payment_processor= getname('payment_master',$payment_master_id,'processor_id');
				$payment_res = array();
				if ($enroll_with_post_date == "yes") {
					$paymentApproved = true;
					$txn_id = 0;

					$lead_track = array(
						'status' => 'Email/SMS Verification',
						'description' => 'Post Date - Payment Approved',
					);

					lead_tracking($lead_id,$customer_id,$lead_track);

				} else {
					/*$payment_mode = "CC";
					$card_number = "4111111111111114";*/
					$api = new CyberxPaymentAPI();
					$cc_params = array();
					$cc_params['order_id'] = $order_display_id;
					$cc_params['customer_id'] = $customer_rep_id;
					$cc_params['amount'] = $order_total['grand_total'];
					$cc_params['description'] = "Quote Purchase";
					$cc_params['firstname'] = $bill_fname;
					// $cc_params['lastname'] = $bill_lname;
					$cc_params['address1'] = $bill_address;
					$cc_params['address2'] = $bill_address2;
					$cc_params['city'] = $bill_city;
					$cc_params['state'] = $bill_state;
					$cc_params['zip'] = $bill_zip;
					$cc_params['country'] = $bill_country;
					$cc_params['phone'] = $primary_phone;
					$cc_params['email'] = $primary_email;
					$cc_params['ip_address'] = !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'];
					$cc_params['processor'] = $payment_processor;
					if ($payment_mode == "ACH") {
						$cc_params['firstname'] = $primary_fname;
						$cc_params['lastname'] = $primary_lname;
						$cc_params['address1'] = $primary_address_1;
						$cc_params['address2'] = $primary_address_2;
						$cc_params['city'] = $primary_city;
						$cc_params['state'] = $primary_state;
						$cc_params['zip'] = $primary_zip;
						$cc_params['country'] = 'United States';
						$cc_params['ach_account_type'] = $account_type;
						$cc_params['ach_routing_number'] = !empty($routing_number) ? $routing_number : $entered_routing_number;
						$cc_params['ach_account_number'] = !empty($account_number) ? $account_number : $entered_account_number;
						$cc_params['name_on_account'] = $bill_fname;
						$cc_params['bankname'] = $ach_name;

						$lead_track = array(
							'status' => 'Email/SMS Verification',
							'description' => 'Processor calling - ACH',
						);

						lead_tracking($lead_id,$customer_id,$lead_track);

						$payment_res = $api->processPaymentACH($cc_params, $payment_master_id);

						$lead_track = array(
							'status' => 'Email/SMS Verification',
							'description' => 'Payment Status - ' . $payment_res['status'] ,
						);

						lead_tracking($lead_id,$customer_id,$lead_track);

						if ($payment_res['status'] == 'Success') {
							$paymentApproved = true;
							$txn_id = $payment_res['transaction_id'];
						} else {
							$paymentApproved = false;
							$payment_error = $payment_res['message'];
							$txn_id = $payment_res['transaction_id'];
							$cc_params['order_type'] = 'Quote';
							$cc_params['browser'] = $BROWSER;
							$cc_params['os'] = $OS;
							$cc_params['req_url'] = $REQ_URL;
							$cc_params['err_text'] = $payment_error;
							$decline_log_id = $function_list->credit_card_decline_log($customer_id, $cc_params, $payment_res);
						}			
					} elseif ($payment_mode == "CC") {
						$cc_params['ccnumber'] = !empty($card_number) ? $card_number : $full_card_number;
						$cc_params['card_type'] = $card_type;
						$cc_params['ccexp'] = str_replace('/','',$expiration);
						$cc_params['cvv'] = $cvv;
						if ($cc_params['ccnumber'] == '4111111111111114') {
							$paymentApproved = true;
							$txn_id = 0;
							$payment_res = array("status" => "Success","transaction_id" => 12345646546,"message" => "Manual Approved");
							$payment_error = 'Payment Successfully.';

							$lead_track = array(
								'status' => 'Email/SMS Verification',
								'description' => 'Payment Status - Payment Approved (Fake card)',
							);

							lead_tracking($lead_id,$customer_id,$lead_track);

						} else {
							
							if($SITE_ENV != 'Live' && $cc_params['ccnumber'] == "4111111111111113") {
								$payment_res = '{"status":"Fail","transaction_id":"40049416880","message":"This transaction has been declined.","API_Type":"Auhtorize Global","API_Mode":"sandbox","API_response":{"status":"Fail","error_code":"2","error_message":"This transaction has been declined.","txn_id":"40049416880"}}';
    							$payment_res = json_decode($payment_res,true);

    							$lead_track = array(
									'status' => 'Email/SMS Verification',
									'description' => 'Payment Status - Payment Declined (Fake card)',
								);

								lead_tracking($lead_id,$customer_id,$lead_track);

							} else {
								$lead_track = array(
									'status' => 'Email/SMS Verification',
									'description' => 'Processor Call - CC',
								);

								lead_tracking($lead_id,$customer_id,$lead_track);

								$payment_res = $api->processPayment($cc_params, $payment_master_id);

								$lead_track = array(
									'status' => 'Email/SMS Verification',
									'description' => 'Payment Status - ' . $payment_res['status'],
								);

								lead_tracking($lead_id,$customer_id,$lead_track);
							}
							
							if ($payment_res['status'] == 'Success') {
								$paymentApproved = true;
								$txn_id = $payment_res['transaction_id'];
							} else {
								$paymentApproved = false;
								$payment_error = $payment_res['message'];
								$txn_id = checkIsset($payment_res['transaction_id']);
								$cc_params['order_type'] = 'Quote';
								$cc_params['browser'] = $BROWSER;
								$cc_params['os'] = $OS;
								$cc_params['req_url'] = $REQ_URL;
								$cc_params['err_text'] = $payment_error;
								$decline_log_id = $function_list->credit_card_decline_log($customer_id, $cc_params, $payment_res);
							}
						}
					}
				}				
			}else{
				$paymentApproved = true;
				$txn_id = 0;
				$payment_mode = '';
				$payment_master_id = 0;
				$payment_processor = '';
				$payment_res = array();
			}
			$orderParams = array(
				'transaction_id' => $txn_id,
				'payment_type' => $payment_mode,
				'payment_master_id' => $payment_master_id,
				'payment_processor' => $payment_processor,
				'payment_processor_res' => json_encode($payment_res),
			);
		//********* Payment code end   ********************

		// Memeber and Policy status
			$member_setting = $memberSetting->get_status_by_payment($paymentApproved,"",($enroll_with_post_date == 'yes' ? true : false),$cust_old_status,array("is_from_enrollment" => true));

		//********* Order Table code start ********************
			$orderParams = array_merge($orderParams, array(
				'type' => ",Customer Enrollment,",
				'ip_address' => !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'],
				'browser' => $BROWSER,
				'os' => $OS,
				'req_url' => $REQ_URL,
				'updated_at' => 'msqlfunc_NOW()',
				'created_at' => 'msqlfunc_NOW()',
				'product_total' => $order_total['sub_total'],
				'sub_total' => $order_total['sub_total'],
				'grand_total' => $order_total['grand_total'],
			));

			if ($enroll_with_post_date == "yes") {
				$orderParams['post_date'] = date("Y-m-d", strtotime($post_date));
				$orderParams['future_payment'] = 'Y';
			} else {
				//if post date is not setup then add coverage date in post_date field
				$orderParams['post_date'] = date("Y-m-d", strtotime($lowest_coverage_date));
				$orderParams['future_payment'] = 'N';
			}

			$orderParams['status'] = ($payment_mode == "ACH") ? 'Pending Settlement' : 'Payment Approved';
			if (!$paymentApproved) {
				$orderParams['status'] = 'Payment Declined';
			}
			if ($enroll_with_post_date == "yes") {
				$orderParams['status'] = 'Post Payment';
			}

			if(isset($payment_res['review_require']) && $payment_res['review_require'] == 'Y'){
				$orderParams['review_require'] = 'Y';
			}
			
			if ($order_id > 0) {
				$order_where = array("clause" => "id=:id", "params" => array(":id" => $order_id));
				$pdo->update("orders", $orderParams, $order_where);
				if($enrollmentLocation!='groupSide' || ($enrollmentLocation=='groupSide' && $group_billing_method == 'individual')){
					$pdo->update("orders", $orderParams, $order_where);
				}else if($enrollmentLocation=='groupSide'){
					$pdo->update("group_orders", $orderParams, $order_where);
				}

				$lead_track = array(
					'status' => 'Email/SMS Verification',
					'description' => 'Updated order',
				);

				lead_tracking($lead_id,$customer_id,$lead_track);

			} else {
				//if order is new then create new order
				$orderParams = array_merge($orderParams, array(
					'display_id' => $order_display_id,
					'customer_id' => $customer_id,
					'created_at' => 'msqlfunc_NOW()',
				)
				);
				if($enrollmentLocation!='groupSide' || ($enrollmentLocation=='groupSide' && $group_billing_method == 'individual')){
					$order_id = $pdo->insert("orders", $orderParams);
				}else if($enrollmentLocation=='groupSide'){
					$order_id = $pdo->insert("group_orders", $orderParams);
				}

				$lead_track = array(
					'status' => 'Email/SMS Verification',
					'description' => 'Inserted order',
				);

				lead_tracking($lead_id,$customer_id,$lead_track);
			}

		//********* Order Table code end   ********************

		//********* Check Reenroll Products   ********************
			$reenroll_products=array();
			if (count($purchase_products_array) > 0) {
				foreach ($purchase_products_array as $key => $product) {
						//************** Already Purchase Product will  not display *************************
					$sqlWebsite="SELECT w.id,w.product_id,w.plan_id,p.type as product_type,w.status,p.reenroll_options,p.reenroll_within,p.reenroll_within_type,w.termination_date 
								FROM website_subscriptions w 
								JOIN prd_main p ON (p.id = w.product_id)
								where w.customer_id = :cust_id AND w.status IN('".implode("','",$ALLOWED_SUBSCRIPTION_STATUS)."') AND w.product_id=:prd_id AND w.plan_id=:plan_id";
					$resWebsite=$pdo->selectOne($sqlWebsite,array(":cust_id"=>$customer_id,":prd_id" => $product['product_id'],":plan_id" => $product['plan_id']));

					if(count($resWebsite) > 0){
						if($resWebsite && isset($resWebsite['termination_date']) && !empty($resWebsite['termination_date'])){
							if($resWebsite['reenroll_options']=='Available After Specific Time Frame'){
								$currentDateTime = new DateTime();
								$dateTimeInTheFuture = new DateTime($resWebsite['termination_date']);
								$dateInterval = $dateTimeInTheFuture->diff($currentDateTime);
								
								$is_reenroll_option_within=false;
								if($resWebsite['reenroll_within_type']=='Days'){
									if($dateInterval->days>=$resWebsite['reenroll_within']){
										$is_reenroll_option_within=true;
									}
								}elseif($resWebsite['reenroll_within_type']=='Weeks'){
									if(($dateInterval->days/7)>=$resWebsite['reenroll_within']){
										$is_reenroll_option_within=true;
									}
								}elseif($resWebsite['reenroll_within_type']=='Months'){
									$totalMonths=0;
									if(!empty($dateInterval->y)){
										$totalMonths=$dateInterval->y*12;
									}
									$totalMonths = $totalMonths + $dateInterval->m;
									if($totalMonths>=$resWebsite['reenroll_within']){
										$is_reenroll_option_within=true;
									}
								}elseif($resWebsite['reenroll_within_type']=='Years'){
									if($dateInterval->y>=$resWebsite['reenroll_within']){
										$is_reenroll_option_within=true;
									}
								}
								if($is_reenroll_option_within){
									array_push($reenroll_products, $resWebsite['product_id']);
								}
							}else if($resWebsite['reenroll_options']=='Available Without Restrictions'){
								array_push($reenroll_products, $resWebsite['product_id']);						
							}
						}
					}
				}
			}
		//********* Check Reenroll Products   ********************
		
		//********* Order Detail Table code start ********************
		
		//***** get minimum end coverage ********
			$endCoverageDateArr= array();
			foreach ($purchase_products_array as $key => $product) {
				if($product['type']=='Fees') {
					$member_payment_type=$product['payment_type_subscription'];
					$start_coverage_date =$coverage_dates[$product['fee_applied_for_product']];
				} else {
					$member_payment_type=$product['payment_type_subscription'];
					$start_coverage_date =$coverage_dates[$product['product_id']];
				}
				$product_dates=$enrollDate->getCoveragePeriod($start_coverage_date,$member_payment_type);

				$endCoveragePeriod = date('Y-m-d',strtotime($product_dates['endCoveragePeriod']));
				array_push($endCoverageDateArr, $endCoveragePeriod);
			}
		//***** get minimum end coverage ********
		
			$websiteSubscriptionArr = array();
			$subscription_ids = array();
			foreach ($purchase_products_array as $key => $product) {
				$website_id = 0;
				//********* Website Subcription,Customer enrollment Table code start ********************
				$member_payment_type=$product['payment_type_subscription'];
				if(strtotime(date('Y-m-d')) >= strtotime($product['start_coverage_period'])){
					if($product['type']=='Fees') {
						$start_coverage_date =$coverage_dates[$product['fee_applied_for_product']];
					} else {
						$start_coverage_date =$coverage_dates[$product['product_id']];
					}
						
				}else{
					$start_coverage_date = $product['start_coverage_period'];
				}
					$product_dates=$enrollDate->getCoveragePeriod($start_coverage_date,$member_payment_type);

					$startCoveragePeriod = date('Y-m-d',strtotime($product_dates['startCoveragePeriod']));
					$endCoveragePeriod = date('Y-m-d',strtotime($product_dates['endCoveragePeriod']));
					$eligibility_date = date('Y-m-d',strtotime($product_dates['eligibility_date']));					
					{	
						$incr = "";
						// if($admin_id > 0){
						// 	$incr = " AND termination_date IS NULL";
						// }
						if($product['product_type'] != "Healthy Step"){
							$incr = " AND termination_date IS NULL";
						}
						$web_subscription_sql = "SELECT id,termination_date from website_subscriptions where customer_id=:id AND product_id =:product_id AND plan_id=:plan_id $incr ORDER BY id DESC";
						$web_subscription_params = array(
							":id" => $customer_id,
							":product_id" => $product['product_id'],
							":plan_id" => $product['plan_id'],
						);
						$web_subscription_res = $pdo->selectOne($web_subscription_sql, $web_subscription_params);

						$web_payment_type = ($payment_mode == 'ACH' ? 'ACH' : 'CC');
						if($enrollmentLocation == 'groupSide' && $group_billing_method != 'individual'){
							$web_payment_type = $group_billing_method;
						}
						
						$web_subscription_data = array(
							'product_id' => $product['product_id'],
							'fee_applied_for_product'=>!empty($product['fee_applied_for_product']) ? $product['fee_applied_for_product'] : 0,
							'prd_plan_type_id' => $product['prd_plan_type_id'],
							'plan_id' => $product['plan_id'],
							'product_code' => $product['product_code'],
							'product_type' => makeSafe($product['type']),
							'last_purchase_date' => 'msqlfunc_NOW()',
							'last_order_id' => $order_id,
							// 'total_attempts' => 0,
							'price' => $product['unit_price'],
							'member_price' => 0,
							'group_price' => 0,
							'price' => $product['unit_price'],
							'qty' => $product['qty'],
							'payment_type' => $web_payment_type,
							'updated_at' => 'msqlfunc_NOW()',
							'termination_date'=>NULL,
							'term_date_set' => NULL,
							'admin_id'=>$admin_id,
							'application_type'=>'email_sms_verification',
						);
						$web_subscription_data["status"] = $member_setting['policy_status'];

						if(isset($primary_annual_salary[$product['product_id']])){
							$web_subscription_data["annual_salary"] = $primary_annual_salary[$product['product_id']];
						}

						if(isset($primary_monthly_salary_percentage[$product['product_id']])){
							$web_subscription_data["monthly_benefit_percentage"] = $primary_monthly_salary_percentage[$product['product_id']];
						}

						if($enrollmentLocation=='groupSide'){
							$web_subscription_data['member_price'] = isset($product['member_price']) ? $product['member_price'] : 0;
							$web_subscription_data['group_price'] = isset($product['group_price']) ? $product['group_price'] : 0;
							$web_subscription_data['contribution_type'] = isset($product['contribution_type']) ? $product['contribution_type'] : '';
							$web_subscription_data['contribution_value'] = isset($product['contribution_value']) ? $product['contribution_value'] : '';
						}

						if(!empty($primary_benefit_amount[$product['product_id']])){
							$web_subscription_data['benefit_amount'] = $primary_benefit_amount[$product['product_id']];
						}
						if(!empty($primary_in_patient_benefit[$product['product_id']])){
							$web_subscription_data['in_patient_benefit'] = $primary_in_patient_benefit[$product['product_id']];
						}
						if(!empty($primary_out_patient_benefit[$product['product_id']])){
							$web_subscription_data['out_patient_benefit'] = $primary_out_patient_benefit[$product['product_id']];
						}
						if(!empty($primary_monthly_income[$product['product_id']])){
							$web_subscription_data['monthly_income'] = $primary_monthly_income[$product['product_id']];
						}
						// if(!empty($primary_benefit_percentage) && isset($primary_benefit_percentage[$product['product_id']])){
						// 	$web_subscription_data['benefit_percentage'] = $primary_benefit_percentage[$product['product_id']];
						// }
						// if (!$paymentApproved) {
						// 	$web_subscription_data["status"] = 'Pending Declined';
						// }
						// if ($enroll_with_post_date == "yes") {
						// 	$web_subscription_data['status'] = "Post Payment";
						// }

						if ($product['payment_type'] == 'Recurring') {
							$next_purchase_date=$enrollDate->getNextBillingDateFromCoverageList($endCoverageDateArr,$startCoveragePeriod,$customer_id,$product['payment_type_subscription']);
							$web_subscription_data['next_purchase_date'] = date('Y-m-d',strtotime($next_purchase_date));
						}else{
							$web_subscription_data['is_onetime'] = 'Y';
							$web_subscription_data['next_purchase_date'] = date('Y-m-d');
						}
						$web_subscription_data['eligibility_date'] = $eligibility_date;
						$web_subscription_data['start_coverage_period'] = $startCoveragePeriod;
						$web_subscription_data['end_coverage_period'] = $endCoveragePeriod;

						if(!empty($primary_state)){
							$web_subscription_data['issued_state']=$primary_state;
						}

						/*------ Set Termination Date for Healthy Step ------*/
						$healthyStepUpdate = false;
						if($product['product_type'] == "Healthy Step") {
							if($product['is_member_benefits'] == "Y" && $product['is_fee_on_renewal'] == "Y" && $product['fee_renewal_type'] == "Renewals" && $product['fee_renewal_count'] > 0) {
								$tmp_fee_renewal_count = $product['fee_renewal_count'];
								$tmp_start_coverage_date = $startCoveragePeriod;
								$tmp_termination_date = $endCoveragePeriod;
								while ($tmp_fee_renewal_count > 0) {
									$product_dates = $enrollDate->getCoveragePeriod($tmp_start_coverage_date,$member_payment_type);
									$tmp_start_coverage_date = date("Y-m-d",strtotime('+1 day',strtotime($product_dates['endCoveragePeriod'])));
									$tmp_termination_date = date("Y-m-d",strtotime($product_dates['endCoveragePeriod']));
									$tmp_fee_renewal_count--;
								}
								$web_subscription_data['termination_date'] = $tmp_termination_date;
								$web_subscription_data['term_date_set'] = date('Y-m-d');
								$web_subscription_data['termination_reason'] = 'Policy Change';
							}
							if (!empty($web_subscription_res['id'])){
								if(strtotime($web_subscription_res['termination_date']) >= strtotime('today')){
									$healthyStepUpdate = true;
								}
							}
						}
						/*------/Set Termination Date for Healthy Step ------*/

						if(!empty($reenroll_products) && in_array($product['product_id'], $reenroll_products)){
							if ((!empty($web_subscription_res['id']) && $product['product_type'] != "Healthy Step") || $healthyStepUpdate) {
								$website_id = $web_subscription_res['id'];
								if($paymentApproved){
									$web_subscription_data['total_attempts'] = 0;
								}
								$where = array("clause" => "id=:id", "params" => array(":id" => $website_id));
								$pdo->update("website_subscriptions", $web_subscription_data, $where);

								$lead_track = array(
									'status' => 'Email/SMS Verification',
									'description' => 'Updated website subscription data',
								);

								lead_tracking($lead_id,$customer_id,$lead_track);

							} else {
								$web_subscription_data = array_merge($web_subscription_data, array(
									'website_id' => $function_list->get_website_id(),
									'customer_id' => $customer_id,
									'created_at' => 'msqlfunc_NOW()',
									'purchase_date' => 'msqlfunc_NOW()',
								));
								$website_id = $pdo->insert("website_subscriptions", $web_subscription_data);

								$lead_track = array(
									'status' => 'Email/SMS Verification',
									'description' => 'Inserted website subscription data',
								);

								lead_tracking($lead_id,$customer_id,$lead_track);
							}

						}else{
							if ((!empty($web_subscription_res['id']) && $product['product_type'] != "Healthy Step") || $healthyStepUpdate) {
								$website_id = $web_subscription_res['id'];
								if($paymentApproved){
									$web_subscription_data['total_attempts'] = 0;
								}
								$where = array("clause" => "id=:id", "params" => array(":id" => $website_id));
								$pdo->update("website_subscriptions", $web_subscription_data, $where);

								$lead_track = array(
									'status' => 'Email/SMS Verification',
									'description' => 'Updated website subscription data',
								);

								lead_tracking($lead_id,$customer_id,$lead_track);

							} else {
								$web_subscription_data = array_merge($web_subscription_data, array(
									'website_id' => $function_list->get_website_id(),
									'customer_id' => $customer_id,
									'created_at' => 'msqlfunc_NOW()',
									'purchase_date' => 'msqlfunc_NOW()',
								));
								$website_id = $pdo->insert("website_subscriptions", $web_subscription_data);

								$lead_track = array(
									'status' => 'Email/SMS Verification',
									'description' => 'Inserted website subscription data',
								);

								lead_tracking($lead_id,$customer_id,$lead_track);

							}
						}
						if(!empty($website_id)){
							$website_subscriptions_history_msg = 'Initial Setup Successful' . ($enroll_with_post_date == "yes" ? " With Post Date " . date("m/d/Y", strtotime($post_date)) : "") . (!$paymentApproved ? "(Declined)" : "") ; 
							$subscription_ids[] = $website_id;
							$web_history_data = array(
								'customer_id' => $customer_id,
								'website_id' => $website_id,
								'admin_id' => $admin_id,
								'product_id' => $product['product_id'],
								'fee_applied_for_product'=>!empty($product['fee_applied_for_product']) ? $product['fee_applied_for_product'] : 0,
								'plan_id' => $product['plan_id'],
								'prd_plan_type_id' => $product['prd_plan_type_id'],
								'order_id' => $order_id,
								'status' => 'Setup',
								'message' => $website_subscriptions_history_msg,
								'authorize_id' => makeSafe($txn_id),
								'processed_at' => 'msqlfunc_NOW()',
								'created_at' => 'msqlfunc_NOW()',
							);
							$pdo->insert("website_subscriptions_history", $web_history_data);

							$lead_track = array(
								'status' => 'Email/SMS Verification',
								'description' => 'Inserted website subscription history data',
							);

							lead_tracking($lead_id,$customer_id,$lead_track);
						}
						
							$cust_enroll_res = array();
							if(!empty($website_id)){
								$cust_enroll_res = $pdo->selectOne("SELECT id FROM customer_enrollment ce WHERE ce.website_id = :website_id", array(":website_id" => $website_id));
							}
							$sub_products = $function_list->get_sub_product($product['product_id']);
							$not_reenroll_product = true;
							if(!empty($reenroll_products) && in_array($product['product_id'], $reenroll_products)){
								$not_reenroll_product = false;
							}
							if (!empty($cust_enroll_res['id']) && $not_reenroll_product) {
								$enrollParams = array(
									'company_id' => $product['company_id'],
									'website_id' => $website_id,
									'sub_product' =>$sub_products,
									'sponsor_id' => $sponsor_row['id'],
									'upline_sponsors' => $sponsor_row['upline_sponsors'] . $sponsor_row['id'] . ",",
									'level' => $sponsor_row['level'],
								);
								$update_ce_where = array("clause" => "id=:id", "params" => array(":id" => $cust_enroll_res['id']));

								$pdo->update("customer_enrollment", $enrollParams, $update_ce_where);
								$customer_enrollment_id = $cust_enroll_res['id'];

								$lead_track = array(
									'status' => 'Email/SMS Verification',
									'description' => 'Updated customer enrollment data',
								);

								lead_tracking($lead_id,$customer_id,$lead_track);

							} else {
								$enrollParams = array(
									'company_id' => $product['company_id'],
									'website_id' => $website_id,									
									'sub_product' =>$sub_products,
									'sponsor_id' => $sponsor_row['id'],
									'upline_sponsors' => $sponsor_row['upline_sponsors'] . $sponsor_row['id'] . ",",
									'level' => $sponsor_row['level'],
								);
								$customer_enrollment_id = $pdo->insert("customer_enrollment", $enrollParams);

								$lead_track = array(
									'status' => 'Email/SMS Verification',
									'description' => 'Inserted customer enrollment data',
								);

								lead_tracking($lead_id,$customer_id,$lead_track);

							}
							/*------- Update cust_enrollment_id To customer_dependent -----------*/
							$websiteSubscriptionArr[] = array(
								'eligibility_date' => $eligibility_date,
								'website_id' => $website_id,
								'customer_id' => $customer_id,
								'product_id' => $product['product_id'],
								'plan_id' => $product['plan_id'],
								'prd_plan_type_id' => $product['prd_plan_type_id'],
							);
							/*------- Update cust_enrollment_id To customer_dependent -----------*/
						// }
					}
				//********* Website Subcription,Customer enrollment Table code end   ********************
				$insOrderDetailSql = array(
					'order_id' => $order_id,
					'website_id' => $website_id,
					'product_id' => $product['product_id'],
					'fee_applied_for_product'=>!empty($product['fee_applied_for_product']) ? $product['fee_applied_for_product'] : 0,
					'plan_id' => $product['plan_id'],
					'prd_plan_type_id' => $product['prd_plan_type_id'],
					'product_type' => $product['type'],
					'product_name' => $product['product_name'],
					'unit_price'	=> $product['unit_price'],
					'member_price'	=> 0,
					'group_price'	=> 0,
					'product_code' => $product['product_code'],
					'start_coverage_period' => $startCoveragePeriod,
					'end_coverage_period' => $endCoveragePeriod,
					'qty' => $product['qty'],
					'renew_count'=>1,
				);
				if($enrollmentLocation=='groupSide'){
					$insOrderDetailSql['member_price'] = isset($product['member_price']) ? $product['member_price'] : 0;
					$insOrderDetailSql['group_price'] = isset($product['group_price']) ? $product['group_price'] : 0;
					$insOrderDetailSql['contribution_type'] = isset($product['contribution_type']) ? $product['contribution_type'] : '';
					$insOrderDetailSql['contribution_value'] = isset($product['contribution_value']) ? $product['contribution_value'] : '';
				}
				if (!empty($product_wise_dependents[$product['product_id']]) && count($product_wise_dependents[$product['product_id']]) > 0) {
					$insOrderDetailSql['family_member'] = count($product_wise_dependents[$product['product_id']]);
				}

				if($enrollmentLocation!='groupSide' || ($enrollmentLocation=='groupSide' && $group_billing_method == 'individual')){
					$checkOdSql = "SELECT id FROM order_details WHERE order_id=:order_id AND product_id=:product_id AND plan_id=:plan_id AND is_deleted='N'";
					$checkOdParams = array(":order_id" => $order_id, ":product_id" => $product['product_id'], ":plan_id" => $product['plan_id']);
					$checkOdRow = $pdo->selectOne($checkOdSql, $checkOdParams);
					
					if (!$checkOdRow) {
						$detail_insert_id = $pdo->insert("order_details", $insOrderDetailSql);

						$lead_track = array(
							'status' => 'Email/SMS Verification',
							'description' => 'Inserted order details data',
						);

						lead_tracking($lead_id,$customer_id,$lead_track);

					} else {
						$detail_insert_id = $checkOdRow["id"];
						$pdo->update("order_details", $insOrderDetailSql, array("clause" => "id=:id", "params" => array(":id" => $detail_insert_id)));

						$lead_track = array(
							'status' => 'Email/SMS Verification',
							'description' => 'Updated order details data',
						);

						lead_tracking($lead_id,$customer_id,$lead_track);

					}
				}else if($enrollmentLocation=='groupSide'){
					$checkOdSql = "SELECT id FROM group_order_details WHERE order_id=:order_id AND product_id=:product_id AND plan_id=:plan_id AND is_deleted='N'";
					$checkOdParams = array(":order_id" => $order_id, ":product_id" => $product['product_id'], ":plan_id" => $product['plan_id']);
					$checkOdRow = $pdo->selectOne($checkOdSql, $checkOdParams);
					
					if (!$checkOdRow) {
						$detail_insert_id = $pdo->insert("group_order_details", $insOrderDetailSql);

						$lead_track = array(
							'status' => 'Email/SMS Verification',
							'description' => 'Inserted group order details data',
						);

						lead_tracking($lead_id,$customer_id,$lead_track);

					} else {
						$detail_insert_id = $checkOdRow["id"];
						$pdo->update("group_order_details", $insOrderDetailSql, array("clause" => "id=:id", "params" => array(":id" => $detail_insert_id)));

						$lead_track = array(
							'status' => 'Email/SMS Verification',
							'description' => 'Updated group order details data',
						);

						lead_tracking($lead_id,$customer_id,$lead_track);
					}
				}
			}
			if($enrollmentLocation!='groupSide' || ($enrollmentLocation=='groupSide' && $group_billing_method == 'individual')){
				$pdo->delete("DELETE FROM group_order_details WHERE order_id=" . $quote_order_id);

				$checkOdSql = "SELECT product_id,plan_id,id,website_id FROM order_details WHERE order_id=:order_id AND is_deleted='N'";
				$checkOdParams = array(":order_id" => $order_id);
				$checkOdRow = $pdo->select($checkOdSql, $checkOdParams);
				foreach ($checkOdRow as $checkOd) {
					$found = false;
					foreach ($purchase_products_array as $key => $product) {
						if ($product['product_id'] == $checkOd['product_id'] && $product['plan_id'] == $checkOd['plan_id']) {
							$found = true;
						}
					}
					if (!$found) {
						// $pdo->delete("DELETE FROM order_details WHERE id=" . $checkOd['id']);
						$od_where = array(
							"clause" => "id=:od_id",
							"params" => array(
								":od_id" => $checkOd['id'],
							),
						);
						$pdo->update("order_details", array('is_deleted' => 'Y'), $od_where);

						$pdo->delete("DELETE  FROM customer_enrollment 
		                            WHERE website_id =:website_id", array(":website_id" => $checkOd['website_id']));

						$pdo->delete("DELETE FROM website_subscriptions WHERE id=:website_id", array(":website_id" => $checkOd['website_id']));

						$lead_track = array(
							'status' => 'Email/SMS Verification',
							'description' => 'Remove old products from order',
						);

						lead_tracking($lead_id,$customer_id,$lead_track);
					}
				}
			}else if($enrollmentLocation=='groupSide'){
				$pdo->delete("DELETE FROM order_details WHERE order_id=" . $quote_order_id);

				$checkOdSql = "SELECT product_id,plan_id,id,website_id FROM group_order_details WHERE order_id=:order_id AND is_deleted='N'";
				$checkOdParams = array(":order_id" => $order_id);
				$checkOdRow = $pdo->select($checkOdSql, $checkOdParams);
				foreach ($checkOdRow as $checkOd) {
					$found = false;
					foreach ($purchase_products_array as $key => $product) {
						if ($product['product_id'] == $checkOd['product_id'] && $product['plan_id'] == $checkOd['plan_id']) {
							$found = true;
						}
					}
					if (!$found) {
						// $pdo->delete("DELETE FROM group_order_details WHERE id=" . $checkOd['id']);
						$od_where = array(
							"clause" => "id=:od_id",
							"params" => array(
								":od_id" => $checkOd['id'],
							),
						);
						$pdo->update("group_order_details", array('is_deleted' => 'Y'), $od_where);

						$pdo->delete("DELETE  FROM customer_enrollment 
		                            WHERE website_id =:website_id", array(":website_id" => $checkOd['website_id']));

						$pdo->delete("DELETE FROM website_subscriptions WHERE id=:website_id", array(":website_id" => $checkOd['website_id']));

						$lead_track = array(
							'status' => 'Email/SMS Verification',
							'description' => 'Remove old products from order',
						);

						lead_tracking($lead_id,$customer_id,$lead_track);
					}
				}
			}
		//********* Order Detail Table code end   ********************		
			if($enrollmentLocation!='groupSide' || ($enrollmentLocation=='groupSide' && $group_billing_method == 'individual')){
				$other_params=array("transaction_id"=>$txn_id,'transaction_response'=>$payment_res,'cc_decline_log_id'=>checkIsset($decline_log_id));
				if ($enroll_with_post_date != "yes") {
					if ($paymentApproved){
						if($payment_mode != "ACH"){
							//************* insert transaction code start ***********************
								$transactionInsId=$function_list->transaction_insert($order_id,'Credit','New Order','Transaction Approved',0,$other_params);

								$lead_track = array(
									'status' => 'Email/SMS Verification',
									'description' => 'Inserted transaction data - Transaction Approved',
								);

								lead_tracking($lead_id,$customer_id,$lead_track);
							//**************** insert transaction code end ***********************
						}else{
							$transactionInsId=$function_list->transaction_insert($order_id,'Credit','Pending','Settlement Transaction',0,$other_params);

							$lead_track = array(
								'status' => 'Email/SMS Verification',
								'description' => 'Inserted transaction data - Pending',
							);

							lead_tracking($lead_id,$customer_id,$lead_track);

						}

						if(!empty($bill_ins_id)) {
							//Set Default Billing Profile 
							$function_list->setDefaultBillingProfile($customer_id,$bill_ins_id);
						}
					}else{
						//************************ insert transaction code start ***********************
							$other_params["reason"] = checkIsset($payment_error);
							$transactionInsId=$function_list->transaction_insert($order_id,'Failed','Payment Declined','Transaction Declined',0,$other_params);

							$lead_track = array(
								'status' => 'Email/SMS Verification',
								'description' => 'Inserted transaction data - Transaction Declined',
							);

							lead_tracking($lead_id,$customer_id,$lead_track);
						//************************ insert transaction code end ***********************
					}   
				}else {
					//************************ insert transaction code start ***********************
						$other_params=array();
						$transactionInsId=$function_list->transaction_insert($order_id,'Credit','Post Payment','Post Transaction',0,$other_params);

						$lead_track = array(
							'status' => 'Email/SMS Verification',
							'description' => 'Inserted transaction data - Post Transaction',
						);

						lead_tracking($lead_id,$customer_id,$lead_track);
					//************************ insert transaction code end ***********************
					//********************** Set default billing prfile *******//
					if(!empty($bill_ins_id)) {
						//Set Default Billing Profile 
						$function_list->setDefaultBillingProfile($customer_id,$bill_ins_id);
					}
				}
			}

		//********* Order Table update subscription id code start ********************
			if($enrollmentLocation!='groupSide' || ($enrollmentLocation=='groupSide' && $group_billing_method == 'individual')){
				if (!empty($subscription_ids)) {
					$pdo->update("orders", array('subscription_ids' => implode(',', $subscription_ids)), array("clause" => "id=:id", "params" => array(":id" => $order_id)));
				}
			}
		//********* Order Table update subscription id code end   ********************

		//********* Update dependent status code start ********************
			if(!empty($dependent_final_array)){
				$dependent_params = array();
				$incr = "";
				// if($admin_id > 0){
					$incr = " AND terminationDate IS NULL";
				// }
				foreach($dependent_final_array as $dependets){
					$prd_mat_id = $dependets["matrix_id"];
					if(!empty($dependets)){
						foreach($dependets['dependent'] as $d){
							$cust_dep_sql = "SELECT id FROM customer_dependent WHERE product_plan_id=:product_plan_id AND cd_profile_id=:cd_profile_id AND customer_id=:customer_id $incr";
							$cust_dep_where = array(":product_plan_id" =>$prd_mat_id,":cd_profile_id" =>$d['dependent_id'],":customer_id" =>$customer_id);
							$cust_dep_res = $pdo->selectOne($cust_dep_sql,$cust_dep_where);
							if(!empty($cust_dep_res['id'])){

								$dependent_params['status'] = $member_setting['policy_status'];

								// if ($enroll_with_post_date == "yes") {
								// 	$dependent_params['status'] = "Post Payment";
								// }

								$dependent_params['updated_at'] = 'msqlfunc_NOW()';
								$dependent_where = array(
									"clause" => "product_plan_id=:product_plan_id AND cd_profile_id=:cd_profile_id AND customer_id=:customer_id $incr",
									"params" => array(
										":product_plan_id" => $prd_mat_id,
										":cd_profile_id" => $d['dependent_id'],
										":customer_id" => $customer_id
									),
								);
								$pdo->update("customer_dependent", $dependent_params, $dependent_where);

								$lead_track = array(
									'status' => 'Email/SMS Verification',
									'description' => 'Updated dependent data',
								);

								lead_tracking($lead_id,$customer_id,$lead_track);
							}
							
						}
					}
				}
			}
		//********* Update dependent status code end ********************
		
		//********* Update cust_enrollment_id To customer_dependent code start ********************
			if (!empty($websiteSubscriptionArr)) {
				$incr = "";
				// if($admin_id > 0){
					$incr = " AND terminationDate IS NULL";
				// }
				foreach ($websiteSubscriptionArr as $ws_row) {
					$dependent_where = array(
						"clause" => "customer_id=:customer_id AND product_id=:product_id AND product_plan_id=:plan_id AND prd_plan_type_id=:prd_plan_type_id $incr",
						"params" => array(
							":customer_id" => $ws_row['customer_id'],
							":product_id" => $ws_row['product_id'],
							":plan_id" => $ws_row['plan_id'],
							":prd_plan_type_id" => $ws_row['prd_plan_type_id'],
						),
					);
					 $pdo->update("customer_dependent", array('website_id' => $ws_row['website_id'],'eligibility_date'=>$ws_row['eligibility_date']), $dependent_where);
				}
			}
		//********* Update cust_enrollment_id To customer_dependent code end   ********************

		//********* Delete Unsaved Dependent code start ********************
			$dependent_delete_sql = "DELETE FROM customer_dependent WHERE customer_id =:customer_id AND product_id = 0 AND product_plan_id = 0";
			$dependent_delete_where = array(':customer_id' => makesafe($customer_id));
			$pdo->delete($dependent_delete_sql, $dependent_delete_where);
		//********* Delete Unsaved Dependent code end   ********************

		//********* insert terms and agreement of member code start ********************
			$extraMemberTerms = array(
				'websiteSubscriptionArr' => array_column($websiteSubscriptionArr,'website_id'),
				'action' => 'email_verification'
			);
			$function_list->insert_member_terms($customer_id,$order_id,$extraMemberTerms);
			$function_list->insert_dpg_agreements($customer_id,$order_id,array("action"=>"email_verification"));
			$function_list->insert_joinder_agreements($customer_id,$order_id,'email_sms_verification');

			$lead_track = array(
				'status' => 'Email/SMS Verification',
				'description' => 'Inserted Agreements',
			);

			lead_tracking($lead_id,$customer_id,$lead_track);
		//********* insert terms and agreement of member code end   ********************

		//********* Payable Insert Code Start ********************
			if($enrollmentLocation!='groupSide' || ($enrollmentLocation=='groupSide' && $group_billing_method == 'individual')){
				if ($paymentApproved == true && $enroll_with_post_date != "yes"){
					if($payment_mode != "ACH"){
						$payable_params=array(
							'payable_type'=>'Vendor',
							'type'=>'Vendor',
							'transaction_tbl_id' => $transactionInsId['id'],
						);
						$payable=$function_list->payable_insert($order_id,0,0,0,$payable_params);

						$lead_track = array(
							'status' => 'Email/SMS Verification',
							'description' => 'Inserted payable data',
						);

						lead_tracking($lead_id,$customer_id,$lead_track);
					}
				}
			}
		//********* Payable Insert Code End   ********************
		
		if ($paymentApproved) {

			if ($enroll_with_post_date != "yes") {

				$lead_where = array(
					"clause" => "id=:id",
					"params" => array(
						":id" => $lead_id,
					),
				);
				$pdo->update("leads", array('status' => 'Converted', 'updated_at' => 'msqlfunc_NOW()'), $lead_where);

				$lead_track = array(
					'status' => 'Email/SMS Verification',
					'description' => 'Updated lead status to Converted',
				);

				lead_tracking($lead_id,$customer_id,$lead_track);

				$ac_description ['key_value']['desc_arr']['Agent'] = $sponsor_row['name'].'<br>';
				$ac_description ['key_value']['desc_arr']['member'] = $primary_fname.' '.$primary_lname;

				if($admin_id == 0 || ($old_status == 'Pending Validation')){
					activity_feed(3, $sponsor_id, 'Agent', $lead_id, 'Lead', 'Lead added and converted', $primary_fname, $primary_lname, json_encode($ac_description), $REQ_URL);
				}
				$update_lead_param = array(
					'customer_id' => $customer_id,
					'email' => $primary_email,
					'cell_phone' => $primary_phone
				);
				$function_list->update_leads_and_details($update_lead_param);

				$customerPassword = getname("customer",$customer_id,"password",'id');
				if(empty($customerPassword)){
					$temporaryPassword = generate_chat_password(10);
					$updateCustomerPasswordParams = ['password' => "msqlfunc_AES_ENCRYPT('" . $temporaryPassword . "','" . $CREDIT_CARD_ENC_KEY . "')"];
					$updateCustomerPasswordWhere = ['clause' => 'id=:id','params' => [':id' => $customer_id]];
					$pdo->update('customer',$updateCustomerPasswordParams,$updateCustomerPasswordWhere);
				}
				$TriggerMailSms->trigger_action_mail('member_enrollment',$customer_id,'member','addedEffectiveDate',$coverage_dates);
			}

			$member_setting = $memberSetting->get_status_by_payment($paymentApproved,"",($enroll_with_post_date == "yes" ? true : false),$cust_old_status,array("is_from_enrollment" => true));
			
			$enrollment->unqualified_leads_with_duplicate_email($primary_email,$customer_id);

			if (!empty($customer_id)) {
				$cust_old_status = getname('customer',$customer_id,'status');
				$customer_where = array(
					'clause' => 'id = :id',
					'params' => array(
						':id' => $customer_id,
					),
				);
				$updateCustomer = array(
					'status' => $member_setting['member_status'],
					'updated_at' => 'msqlfunc_NOW()',
				);
				// if (!in_array($cust_old_status,array("Active","Inactive")) AND $enroll_with_post_date == "yes") {
				// 	$updateCustomer['status'] = 'Post Payment';
				// }
				$pdo->update('customer', $updateCustomer, $customer_where);

				$lead_track = array(
					'status' => 'Email/SMS Verification',
					'description' => 'Updated customer status',
				);

				lead_tracking($lead_id,$customer_id,$lead_track);
			}
			/*--------------------- Start Final Script -----------------------------------*/

			/*--------- Send Welcome Mail ---------*/
			$mail_data = array();
			$mail_data['fname'] = $primary_fname;
			$mail_data['lname'] = $primary_lname;
			$mail_data['email'] = $primary_email;
			$mail_data['link'] = $HOST . "/member";
			$mail_data['order_id'] = "#" . $order_display_id;
			$mail_data['order_date'] = date("m/d/Y");

			//********* Confirm summary code start ********************
				$summary = "";
				$summary .= '<table width="100%" cellspacing="0" cellpadding="5" border="0" align="center" style="margin-bottom:15px; text-align:left; font-size:14px; margin-top:10px" >
				<thead>
					<tr style="background-color:#f1f1f1; text-align:left;">
						<th width="5%">No.</th>
						<th width="50%">Description</th>
						<th width="10%">Qty</th>
						<th width="">Unit Price</th>
						<th width="12%" style="text-align:right">Total</th>
					</tr>
				</thead>
				<tbody>';
				$i = 1;

				foreach ($products as $key => $product) {

					$summary_price = 0;
					$summary_price = $product['unit_price'];

					$plan_name = '' ;
					if($product['prd_plan_type_id']!=0){
						$plan_name = $prdPlanTypeArray[$product['prd_plan_type_id']]['title'];
					}
					$product_name = $product['product_name'];
					if($product['type']=='Fees'){ 
						$plan_name = $product['fee_type'];
					}
					$count = $i;
					$included_html="";
					$included_html="<em>*</em>";
					$summary .= '<tr>
						<td>' . $count . '</td>
						<td>' . $product_name . ' (' . $plan_name . ')' . '</td>
						<td>' . $product['qty'] . '</td>
						<td>' . displayAmount($summary_price, 2, 'USA') . '</td>
						<td style="text-align:right">' . displayAmount($summary_price * $product['qty'], 2, 'USA') . $included_html .'</td>
					</tr>';
					$i++;
				}

				$summary .= '</tbody>
				</table>
				<table cellspacing="0" cellpadding="5" border="0" style="float:right; width:290px; font-size:14px;">
				<tbody>
				<tr>
					<td>Sub Total : </td>
					<td style="text-align:right">' . displayAmount($order_total['sub_total'], 2, "USA") . '</td>
				</tr>';
				if(!empty($healhty_step['id'])){
					$summary .='<tr>
					<td>'.$healhty_step['name'].' : </td>
					<td style="text-align:right">' . displayAmount($healhty_step['unit_price'], 2, 'USA') . '</td>
					</tr>
					';
				}
				if ($order_total['service_fee'] > 0) {
					$summary .= '<tr>
						<td>Service Fee</td>
						<td align="right">' . displayAmount($order_total['service_fee'], 2, 'USA') . ' </td>
					</tr>';
				}
				if ($order_total['admin_fee'] > 0) {
					$summary .= '<tr>
						<td>Admin Fee</td>
						<td align="right">' . displayAmount($order_total['admin_fee'], 2, 'USA') . ' </td>
					</tr>';
				}
				$summary .= '<tr style="background-color:#f1f1f1; font-size: 16px;">
					<td><strong>Grand Total</strong></td>
					<td style="text-align:right"><strong>' . displayAmount($order_total['grand_total'], 2, "USA") . '</strong></td>
				</tr>
				</tbody>
				</table>
				<div style="clear:both"></div>';
			//********* Confirm summary code end ********************
			$mail_data['order_summary'] = $summary;
			$mail_data['Email'] = $primary_email;
			if ($payment_mode == "CC") {
				$cd_number = !empty($card_number) ? $card_number : $full_card_number;
				$mail_data['billing_detail'] = "Billed to: $card_type *" . substr($cd_number, -4);
			} else {
				$r_number = !empty($routing_number) ? $routing_number : $entered_routing_number;
				$mail_data['billing_detail'] = "Billed to: ACH *" . substr($r_number, -4);
			}

			if ($SITE_ENV=='Local') {
				$primary_email = "karan@cyberxllc.com";
			}

			if (!empty($customer_id)) {
				// IF new member
				$agent_detail = $function_list->get_sponsor_detail_for_mail($customer_id, $sponsor_row['id']);
				if (!empty($agent_detail)) {
					$mail_data['agent_name'] = !empty($agent_detail['agent_name']) ? $agent_detail['agent_name'] : "";
					$mail_data['agent_email'] = !empty($agent_detail['agent_email']) ? $agent_detail['agent_email'] : "";
					$mail_data['agent_phone'] = !empty($agent_detail['agent_phone']) ? format_telephone($agent_detail['agent_phone']) : "";
					$mail_data['agent_id'] = !empty($agent_detail['agent_id']) ? $agent_detail['agent_id'] : "";
					$mail_data['is_public_info'] = $agent_detail['is_public_info'];
				} else {
					$mail_data['is_public_info'] = 'display:none';
				}
				$trigger_id = 39;
				if($enrollmentLocation=='groupSide'){
					$trigger_id = 109;
				}

				$smart_tags = get_user_smart_tags($customer_id,'member');
                
		        if($smart_tags){
		            $mail_data = array_merge($mail_data,$smart_tags);
		        }
		        if($enroll_with_post_date != "yes" && !empty($send_email_productId)){
					trigger_mail($trigger_id, $mail_data, $primary_email, array(), 3);

					$lead_track = array(
						'status' => 'Email/SMS Verification',
						'description' => 'Email sent - Order summary',
					);

					lead_tracking($lead_id,$customer_id,$lead_track);
		        }
				/*---------/Send Welcome Mail ---------*/
				$trigger_id = 38;
				if($enrollmentLocation=='groupSide'){
					$trigger_id = 110;
				}
				if($enroll_with_post_date != "yes" && !empty($send_email_productId)){
					trigger_mail($trigger_id, $mail_data, $primary_email, array(), 3);

					$lead_track = array(
						'status' => 'Email/SMS Verification',
						'description' => 'Email sent - Welcome email',
					);

					lead_tracking($lead_id,$customer_id,$lead_track);
				}

				/*--------- Activity Feed ---------*/
				$member_rep_id = getname('customer',$customer_id,'rep_id','id');

				$activity_feed_data = array(
					'ac_message' => array(
							'ac_message_1' =>'  Member : '.$primary_fname.' '.$primary_lname.' ',
								'ac_red_1'=>array(
									'href'=>$ADMIN_HOST.'/members_details.php?id='.md5($customer_id),
									'title'=>'('.$member_rep_id.') <br>',
						),
					),
					'key_value'=>array(
						'desc_arr'=>array(
							'url'=>$REQ_URL,
							'email'=>$primary_email,
							'phone' => $primary_phone,
						)
					)
				);
				$message_3 = $payment_mode =='CC' ? ' Approved on Order ' : 'PENDING SETTLEMENT on Order';
				$leadRes = $pdo->selectOne("SELECT lead_id FROM leads WHERE id = :lead_id", array(":lead_id" => $lead_id));
				$membreId = getname('customer',$customer_id,'rep_id');
				$odDisplay = getname('orders',$order_id,'display_id');
				if($enrollmentLocation=='groupSide' && $group_billing_method != 'individual'){
					$activity_feed_data_member['ac_message'] = array(
						'ac_red_1'=>array(
							'href'=>$GROUP_HOST.'/lead_details.php?id='.md5($lead_id),
							'title'=>$leadRes['lead_id'],
						),
						'ac_message_2' =>' became Member ',
						'ac_red_2'=>array(
							'href'=>$ADMIN_HOST.'/members_details.php?id='.md5($customer_id),
							'title'=>$customer_rep_id,
						),
						'ac_message_3' =>'  Member : '.$primary_fname.' '.$primary_lname.' <br>',
					);
				}else{
					$activity_feed_data_member['ac_message'] = array(
						'ac_red_1'=>array(
							'href'=>$AGENT_HOST.'/lead_details.php?id='.md5($lead_id),
							'title'=>$leadRes['lead_id'],
						),
						'ac_message_2' =>'  transaction  ',
						'ac_red_2'=>array(
							// 'href'=>$ADMIN_HOST.'/all_orders.php?id='.md5($order_id),
							'title'=>$txn_id,
						),
						'ac_message_3' =>$message_3,
						'ac_red_3'=>array(
							'href'=>$ADMIN_HOST.'/all_orders.php?id='.md5($order_id),
							'title'=>$odDisplay,
						),
						'ac_message_4' =>' and became Member ',
						'ac_red_4'=>array(
							'href'=>$ADMIN_HOST.'/members_details.php?id='.md5($customer_id),
							'title'=>$membreId,
						),
						'ac_message_5' =>'  Member : '.$primary_fname.' '.$primary_lname.' <br>',
					);
				}
				
				$activity_feed_data_member['key_value'] = array(
							'desc_arr'=>array(
								'url'=>$REQ_URL,
								'email'=>$primary_email,
								'phone' => $primary_phone,
							)
						);
						
				
				$mail_content = $pdo->selectOne("SELECT id,email_content,display_id from triggers where id=:id",array(":id"=>$trigger_id));
				
				$email_activity = array();
				if(!empty($mail_data) && !empty($mail_content['id'])){
					$email_cn = $mail_content['email_content'];
					foreach ($mail_data as $placeholder => $value) {
						$email_cn = str_replace("[[" . $placeholder . "]]", $value, $email_cn);
					}

					$email_activity["ac_description_link"] = array(
						'Trigger :' => array(
							'href'=>'#javascript:void(0)',
							'class'=>'descriptionPopup',
							'title'=>$mail_content['display_id'],
							'data-desc'=>htmlspecialchars($email_cn),
							'data-encode'=>'no'
						),
						'<br>Email :' => array(
							'href' => '#javascript:void(0)',
							'title' => $primary_email,
						)
					);
				}
				if($admin_id > 0) {
					
				} else {
					if($old_status != 'Pending Validation'){

					}else{
						activity_feed(3, $lead_id,'lead', $lead_id, 'lead', 'Enrolled A New Member', $primary_fname, $primary_lname, json_encode($activity_feed_data_member));
						activity_feed(3, $sponsor_row['id'], $sponsor_row['type'], $sponsor_row['id'], $sponsor_row['type'], 'Enrolled A New Member', $primary_fname, $primary_lname, json_encode($activity_feed_data_member) );
						activity_feed(3, $customer_id, "Customer", $order_id, 'orders', 'Joined', $primary_fname, $primary_lname,json_encode($activity_feed_data_member));
					}

					if($enroll_with_post_date != "yes"){
						activity_feed(3, $customer_id, "Customer", $trigger_id, 'triggers', 'Welcome email delivered', $primary_fname,$primary_lname,json_encode($email_activity));
					}
				}
				/*---------/Activity Feed ---------*/

				if($enroll_with_post_date != "yes" && !empty($send_email_productId)){
					$enrollment->send_temporary_password_mail($customer_id);

					$lead_track = array(
						'status' => 'Email/SMS Verification',
						'description' => 'Email sent - temporary password',
					);

					lead_tracking($lead_id,$customer_id,$lead_track);

				}
				
			}

			$activity_feed_data = array();

			$activity_feed_data['key_value']['desc_arr']['url'] = $REQ_URL;

			$activity_feed_data['ac_message'] = array(
				'ac_message_1' =>'  Agent : '.$sponsor_row['name'].' ',
				'ac_red_1'=>array(
					'href'=>$ADMIN_HOST.'/agent_detail_v1.php?id='.md5($sponsor_id),
					'title'=>'('.$sponsor_row['rep_id'].') <br>',
				),
				'ac_message_2' =>'  Order : ',
				'ac_red_2'=>array(
					'href'=>$ADMIN_HOST.'/all_orders.php?id='.md5($order_id),
					'title'=>$odDisplay.' <br>',
				),
				'ac_message_3' =>'  Member : '.$primary_fname.' '.$primary_lname.' ',
				'ac_red_3'=>array(
					'href'=>$ADMIN_HOST.'/members_details.php?id='.md5($customer_id),
					'title'=>'('.$member_rep_id.') <br>',
				),
			);
			if($admin_id > 0) {
				$admin_row = $pdo->selectOne("SELECT * FROM admin WHERE id=:id",array(":id" => $admin_id));
				$activity_feed_data = array();
				$activity_feed_data['ac_message'] = array(
					'ac_red_1'=>array(
						'href' => $ADMIN_HOST.'/admin_profile.php?id='.md5($admin_id),
						'title' => $admin_row['display_id'],
					),
					'ac_message_1' =>' added product on ',
					'ac_red_2'=>array(
						'href'=>$ADMIN_HOST.'/members_details.php?id='.md5($customer_id),
						'title'=>$customer_rep_id,
					),
					'ac_message_2' =>' used Email/SMS eSignature to complete enrollment'
				);
				activity_feed(3,$admin_id,'Admin',$customer_id,'customer','Admin Added Product','','',json_encode($activity_feed_data));

			} else {
				$activity_feed_data = array();
				$activity_feed_data['ac_message'] = array(
					'ac_red_1'=>array(
						'href'=>$ADMIN_HOST.'/members_details.php?id='.md5($customer_id),
						'title'=>$member_rep_id,
					),
					'ac_message_1' =>' used Email/SMS eSignature to complete enrollment'
				);
				activity_feed(3, $customer_id, 'Customer', $customer_id, 'customer', 'Application is Approved by Member', $primary_fname, $primary_lname, json_encode($activity_feed_data));



				$leadRes = $pdo->selectOne("SELECT lead_id FROM leads WHERE id = :lead_id", array(":lead_id" => $lead_id));
				$activity_feed_data = array();
				$activity_feed_data['ac_message'] = array(
					'ac_red_1'=>array(
						'href'=> $ADMIN_HOST.'/lead_details.php?id='.md5($lead_id),
						'title'=>$leadRes['lead_id'],
					),
					'ac_message_1' =>' used Email/SMS eSignature to complete enrollment'
				);
				activity_feed(3, $lead_id, 'Lead', $lead_id, 'leads', 'Application is Approved by Lead', $primary_fname, $primary_lname, json_encode($activity_feed_data));
			}

			$response['order_id'] = md5($order_id);
			$response['customer_id'] = md5($customer_id);
			$response['status'] = 'account_approved';
			$response['payment_type'] = strtolower(getname('website_subscriptions',$customer_id,'payment_type','customer_id'));
			$response['test'] = $customer_rep_id;
		} else {
			$total_attempts = getname('website_subscriptions',$order_id,'total_attempts','last_order_id');
			$total_attempts += 1;
			$sub_param = array('total_attempts'=>$total_attempts);
			$where = array("clause"=>"last_order_id=:id","params"=>array(":id"=>$order_id));
			$pdo->update('website_subscriptions',$sub_param,$where);

			$lead_track = array(
				'status' => 'Email/SMS Verification',
				'description' => 'Updated total attempts',
			);

			lead_tracking($lead_id,$customer_id,$lead_track);

			$response['status'] = 'payment_fail';
			$response['payment_error'] = ($payment_error ? $payment_error : 'Error in processing payment');


			$leadRes = $pdo->selectOne("SELECT lead_id FROM leads WHERE id = :lead_id", array(":lead_id" => $lead_id));
			$odDisplay = getname('orders',$order_id,'display_id');

			$activity_desc_fail['ac_message'] = array(
				'ac_red_1'=>array(
					'href'=>$AGENT_HOST.'/lead_details.php?id='.md5($lead_id),
					'title'=>$leadRes['lead_id'],
				),
				'ac_message_2' =>'  transaction  ',
				'ac_red_2'=>array(
					// 'href'=>$ADMIN_HOST.'/all_orders.php?id='.md5($order_id),
					'title'=>$txn_id,
				),
				'ac_message_3' =>' Failed on Order ',
				'ac_red_3'=>array(
					'href'=>$ADMIN_HOST.'/all_orders.php?id='.md5($order_id),
					'title'=>$odDisplay,
				),
				'ac_message_4' =>' due to '.$response['payment_error'],
			);
			
			activity_feed(3, $sponsor_row['id'], $sponsor_row['type'], $lead_id, 'Lead', 'Enrollment Verification Fail', $primary_fname, $primary_lname, json_encode($activity_desc_fail), $REQ_URL);
			
		}
	}
	
    if(empty($btn_submit_application)){
        $response['status'] = 'save_popup';
	}

	if(!empty($lead_tracking_id)) {
		$tracking_where = array(
			'clause' => 'id=:id',
			'params' => array(
				':id' => $lead_tracking_id,
			),
		);
		$tracking_data = array(
			'is_request_completed' => 'Y',
			'order_status' => (isset($orderParams['status'])?$orderParams['status']:''),
		);
		$pdo->update('lead_tracking',$tracking_data,$tracking_where);
		$ld_desc = array(
			'page' => 'ajax_edit_enrollment_verification',
			'paymentApproved' => ($paymentApproved?'true':'false'),
			'application_type' => 'email_sms_verification',
			'btn_submit_application' => $btn_submit_application,
		);
		$tracking_data = array(
			'status' => 'submit_application_end',
			'order_status' => (isset($orderParams['status'])?$orderParams['status']:''),
			'is_request_completed' => 'Y',
			'customer_id' => $customer_id,
			'lead_id' => $lead_id,
			'order_id' => $order_id,
			'description' => json_encode($ld_desc),
		);
		$pdo->insert('lead_tracking',$tracking_data);
	}
	
	/*if(($attempt_date!='' || strtotime($today) !=  strtotime($attempt_date) ) && (($check_attempts > 3 || $check_attempts==0) && $total_attempts==0) && !$paymentApproved && !empty($btn_submit_application)){
		$response['status'] = 'next_day';
		$response['payment_error'] = 'Maximum attempts is 4 per day, you can more attempt Tommorow.';
	}*/

	if($activity_update){
		activity_feed(3, $sponsor_id, 'Agent', $lead_id, 'Lead', 'Enrollment Verification Updates', $primary_fname, $primary_lname, json_encode($activity_description), $REQ_URL);
	}
}

if(count($validate->getErrors()) > 0){
    $response['status'] = 'fail';
    $response['errors'] = $validate->getErrors();
}
header('Content-Type: application/json');
echo json_encode($response);
dbConnectionClose();
exit;
?>