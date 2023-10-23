<?php
include_once (__DIR__) . '/layout/start.inc.php';
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
$REQ_URL = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

$step = isset($_POST['dataStep'])?$_POST['dataStep']:"";
$is_draft = !empty($_POST["is_draft"]) ? $_POST["is_draft"] : 0;

$is_address_ajaxed = checkIsset($_POST['is_address_ajaxed']) ;
$is_agency_address_ajaxed = checkIsset($_POST['is_agency_address_ajaxed']) ;
$REAL_IP_ADDRESS = get_real_ipaddress();
if($is_address_ajaxed && !$is_draft && $step >= 2){

    $response = array("status"=>'success');
    $address = $_POST['address'];
    $address_2 = checkIsset($_POST['address_2']);
    $city = $_POST['city'];
    $state = checkIsset($_POST['state']);
    $zip = $_POST['zipcode'];
    $old_address = $_POST['old_address'];
    $old_zip = $_POST['old_zipcode'];

    $validate = new Validation();

    $validate->digit(array('required' => true, 'field' => 'zipcode', 'value' => $zip,'min'=> 5,'max'=>5 ), array('required' => 'Zip Code is required'));

    $validate->string(array('required' => true, 'field' => 'address', 'value' => $address), array('required' => 'Address is required'));
    if($validate->isValid()){
    	$response['agencyApi'] = "";
        if(!empty($is_agency_address_ajaxed)){
            $response['agencyApi'] = 'success';
        }

        include_once '../includes/function.class.php';
        $function_list = new functionsList();
        $zipAddress = $function_list->uspsCityVerification($zip);

        if($old_address != $address || $zip!=$old_zip ||  $getStateNameByShortName[$zipAddress['state']] !=$state){
            
            if($zipAddress['status'] =='success'){
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

if($is_agency_address_ajaxed && !$is_draft && $step >= 2){

    $response = array("status"=>'success');
    $address = $_POST['business_address'];
    $address_2 = checkIsset($_POST['business_address2']);
    $city = $_POST['business_city'];
    $state = checkIsset($_POST['business_state']);
    $zipcode = $_POST['business_zipcode'];
    $old_address = $_POST['old_business_address'];
    $old_zip = $_POST['old_business_zipcode'];

    $validate = new Validation();

    $validate->digit(array('required' => true, 'field' => 'business_zipcode', 'value' => $zipcode,'min'=> 5,'max'=>5 ), array('required' => 'Zip Code is required'));

    $validate->string(array('required' => true, 'field' => 'business_address', 'value' => $address), array('required' => 'Address is required'));
    if($validate->isValid()){
        $response['agencyApi'] = 'done';
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
                $response['errors'] = array("business_zipcode"=>$zipAddress['error_message']);
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

if(empty($_SESSION['agents']['rep_id'])){
	$response['status'] = 'session_fail';
	echo json_encode($response);
	exit;
}
$checkLink_query = "SELECT c.id as id ,email,cell_phone,rep_id,sponsor_id,public_name,public_email,public_phone,user_name,
					display_in_member,is_branding,brand_icon,status,account_type,company_name,company_address,company_address_2,
					company_city,company_state,company_zip,w9_pdf,address,address_2,fname,tax_id,lname,city,state,zip,birth_date,
					type,npn,is_contract_approved,TIMESTAMPDIFF(HOUR,invite_at,now()) as difference,
					AES_DECRYPT(ssn,'" . $CREDIT_CARD_ENC_KEY . "') as dssn 
					FROM customer c 
					LEFT JOIN customer_settings cs on(cs.customer_id=c.id) 
					WHERE rep_id=:invite_key AND (status in ('Pending Approval','Pending Contract','Pending Documentation'))";
$Linkwhere = array(':invite_key' => $_SESSION['agents']['rep_id']);
$agent_res = $pdo->selectOne($checkLink_query, $Linkwhere);
$isRecontract = "No";

if(empty($agent_res['id'])){
	$response['status'] = 'session_fail';
	echo json_encode($response);
	exit;
}
$agentId = $hdn_cust_id = $agent_res["id"];
$sponsor_id = $agent_res['sponsor_id'];
$user_name = $agent_res['user_name'];

$ajax_delete = !empty($_POST['ajax_delete']) ? $_POST['ajax_delete'] : '' ;
if ($ajax_delete) {
	$result = array();
	$lid = !empty($_POST['lid']) ? $_POST['lid'] : '' ;
	if($lid != ''){
		delete_license($lid);
		$result['status'] = "success";
	}
	header('Content-type: application/json');
	echo json_encode($result); 
	exit;
}

$is_ajax_license = !empty($_POST['is_ajax_license']) ? $_POST['is_ajax_license'] : '' ;
if ($is_ajax_license) {
	$result = array();
	$license_expiry = $_POST["license_expiry"];
	$license_not_exp = !empty($_POST['license_not_expire']) ? $_POST['license_not_expire'] : 'N';
	$license_number = $_POST['license_number'];
	$license_active = $_POST["license_active_date"];
	$license_state = !empty($_POST['license_state']) ? $_POST['license_state'] : '';
	$license_type = !empty($_POST["license_type"]) ? $_POST["license_type"] : array();
	$license_auth = !empty($_POST["licsense_authority"]) ? $_POST["licsense_authority"] : array() ;
	$lid = !empty($_POST["lid"]) ? $_POST["lid"] : array() ;
	$hdn_license = $_POST["hdn_license"];
	$edit = !empty($_POST['edit']) ? $_POST['edit'] : '';

	$hdn_license = array_flip($hdn_license);
	foreach($hdn_license as $key => $value){
		$license_staten[$key] = $license_state;
		$license_numbern[$key] = $license_number;
		$license_activen[$key] = $license_active;
		$license_typen[$key] = $license_type;
		$license_authn[$key] = $license_auth;
		$license_expiryn[$key] = $license_expiry;
		$license_not_expn[$key] = $license_not_exp;
		$hdn_license[$key] = $lid;
		$editn[$key] = $edit;
	}

	$ajax = 1;
	check_agent_license_validation($_SESSION['agents']['id'],$validate,$hdn_license,$license_staten,$license_numbern,$license_activen,$license_type,$license_auth,$license_expiryn,$license_not_expn,$editn,$ajax);
	if($validate->isValid()){
		$doc_id = add_update_license($hdn_license,$license_staten,$license_numbern,$license_activen,$license_typen,$license_authn,$license_expiryn,$license_not_expn,$ajax);
		$result['status'] = "success";
		$result['doc_id'] = $doc_id;
	}else{
		$errors = $validate->getErrors();
		$result['errors'] = $errors;
		$result['status'] = "fail";
	}
	header('Content-type: application/json');
	echo json_encode($result); 
	exit;
}

$submit_type = isset($_POST['submit_type'])?$_POST['submit_type']:"";
$action = isset($_POST['action'])?$_POST['action']:"";

$response['is_draft'] = $is_draft;
$response['dataStep'] = $step;
$response['submit_type'] = $submit_type;
$response['action'] = $action;


//********** step1 varible intialization code start **********************
	$admin_name = checkIsset($_POST["admin_name"]);
	$admin_phone = !empty($_POST["admin_phone"]) ? phoneReplaceMain($_POST["admin_phone"]) : '';
	$admin_email = checkIsset($_POST["admin_email"]);
	$username = checkIsset($_POST['username']);
	$display_in_member = !empty($_POST['display_in_member']) ? 'Y' : '';
//********** step1 varible intialization code start **********************

//********** step2 varible intialization code start **********************
	$fname = checkIsset($_POST['fname']);
	$lname = checkIsset($_POST['lname']);
	$address = checkIsset($_POST['address']);
	$address_2 = checkIsset($_POST['address_2']);
	$city = checkIsset($_POST['city']);
	$state = checkIsset($_POST['state']);
	$zipcode = checkIsset($_POST['zipcode']);
	$dob = !empty($_POST['dob']) ? str_replace('_','',$_POST['dob']) : '';
	$ssn = !empty($_POST['ssn']) ? phoneReplaceMain($_POST['ssn']) : '';
	$is_ssn_edit = checkIsset($_POST['is_ssn_edit']);

	$account_type = !empty($_POST["account_type"]) ? $_POST["account_type"] : '';
	if ($account_type == "Business") {
		$business_name = $_POST['business_name'];
		$business_address = $_POST['business_address'];
		$business_address2 = checkIsset($_POST['business_address2']);
		$business_city = $_POST['business_city'];
		$business_state = $_POST['business_state'];
		$business_zipcode = $_POST['business_zipcode'];
		$business_taxid = !empty($_POST['business_taxid']) ? str_replace('_','',$_POST['business_taxid']) : '';
	}
	
	$license_number = !empty($_POST['license_number']) ? $_POST['license_number'] : '';;
	$license_state = !empty($_POST['license_state']) ? $_POST['license_state'] : '';
	$license_expiry = !empty($_POST["license_expiry"]) ? $_POST["license_expiry"] : ''  ;
	$license_not_exp = !empty($_POST['license_not_expire']) ? $_POST['license_not_expire'] : 'N';
	$license_active = !empty($_POST["license_active_date"]) ? $_POST["license_active_date"] : '' ;
	$license_type = !empty($_POST["license_type"]) ? $_POST["license_type"] : array();
	$license_auth = !empty($_POST["licsense_authority"]) ? $_POST["licsense_authority"] : array() ;
	$hdn_license = !empty($_POST["hdn_license"]) ? $_POST["hdn_license"] : array();
	$edit = !empty($_POST['edit']) ? $_POST['edit'] : array();
	$npn_no = !empty($_POST['npn_number']) ? $_POST['npn_number'] : '';
	$w9_form_business = checkIsset($_FILES["w9_form_business"],'arr');
	$e_o_coverage = checkIsset($_POST['e_o_coverage']);
	$e_o_by_parent = isset($_POST['e_o_by_parent']) ? $_POST['e_o_by_parent']:'N';
	if ($e_o_coverage == "Y") {
		$e_o_amount = str_replace(array("$", ","), array("", ""), $_POST['e_o_amount']);
		$e_o_expiration = $_POST['e_o_expiration'];
		$e_o_document = checkIsset($_FILES['e_o_document'],'arr');
	}
	
	$bankname = checkIsset($_POST["bankname"]);
	$bank_rounting_number = checkIsset($_POST['bank_rounting_number']);
	$bank_account_number = checkIsset($_POST['bank_account_number']);
	$entered_account_number = checkIsset($_POST['entered_account_number']);
	$bank_number_confirm = checkIsset($_POST['bank_number_confirm']);		
	$bnk_account_type = checkIsset($_POST['bank_account_type']);	
	
	$license_not_expn = array();
//********** step2 varible intialization code start **********************

//********** step3 varible intialization code start **********************
	$check_agree = isset($_POST['check_agree']) && $_POST['check_agree'] ? 'Y' : 'N';
	$signature_data = isset($_POST['signature_data'])?$_POST['signature_data']:"";
//********** step3 varible intialization code start **********************

//********* step1 validation code Start   ********************
 if (!$is_draft && $step >= 1) {
	if($display_in_member != 'Y'){
		$validate->string(array('required' => true, 'field' => 'admin_name', 'value' => $admin_name), array('required' => 'Name is required'));
		$validate->email(array('required' => true, 'field' => 'admin_email', 'value' => $admin_email), array('required' => 'Email is required.', 'invalid' => 'Please enter valid email'));
		$validate->digit(array('required' => true, 'field' => 'admin_phone', 'value' => $admin_phone, 'min' => 10, 'max' => 10), array('required' => 'Phone Number is required', 'invalid' => 'Valid Phone Number is required'));
	}
	$validate->regex(array('required' => true, 'pattern' => '/^[A-Za-z0-9]+$/', 'field' => 'username', 'value' => $username, 'min' => 4, 'max' => 20), array('required' => 'Username is required', 'invalid' => 'Valid Username is required'));
	if (!$validate->getError('username')) {
		if (!isValidUserName($username, $agentId)) {
			$validate->setError("username", "Username already exist");
		}
	}
 }
//********* step1 validation code end   ********************
if(!empty($hdn_license)){
	foreach($hdn_license as $key => $hdn){
		$license_not_expn[$key] = isset($license_not_exp[$key]) ? $license_not_exp[$key] : 'N' ;
	}
}

//********* step2 validation code Start   ********************
 if (!$is_draft && $step >= 2) {
	
	$validate->string(array('required' => true, 'field' => 'fname', 'value' => $fname), array('required' => 'Firstname is required'));
	$validate->string(array('required' => true, 'field' => 'lname', 'value' => $lname), array('required' => 'Lastname is required'));
	$validate->string(array('required' => true, 'field' => 'address', 'value' => $address), array('required' => 'Address is required'));
	if(!empty($address_2) && preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/',$address_2)) {
		$validate->setError('address_2','Special character not allowed');
	}
	$validate->string(array('required' => true, 'field' => 'city', 'value' => $city), array('required' => 'City is required'));
	$validate->string(array('required' => true, 'field' => 'state', 'value' => $state), array('required' => 'State is required'));
	// $validate->string(array('required' => true, 'field' => 'zipcode', 'value' => $zipcode), array('required' => 'Zip Code is required'));
	$validate->string(array('required' => true, 'field' => 'zipcode', 'value' => str_replace('_','',$zipcode) ,'min'=>5), array('required' => 'Zip Code required.'));
	if (!$validate->getError('zipcode')){
        include_once '../includes/function.class.php';
        $function_list = new functionsList();
        $zipAddress = $function_list->uspsCityVerification($zipcode);
        if($zipAddress['status'] !='success'){
            $validate->setError("zipcode",$zipAddress['error_message']);
        }
    }

	$validate->string(array('required' => true, 'field' => 'dob', 'value' => $dob), array('required' => 'Date of Birth is required'));
	if (!$validate->getError('dob') && !empty($dob)) {
		list($mm, $dd, $yyyy) = explode('/', $dob);
		if(empty($mm) || empty($dd) || empty($yyyy)){
			$validate->setError('dob', 'Valid Date of Birth is required');
		}else if(!empty($mm) && !empty($dd) && !empty($yyyy) && !checkdate($mm, $dd, $yyyy)){
			$validate->setError('dob', 'Valid Date of Birth is required');
		}
		if (!$validate->getError('dob')) {
			$age_y = dateDifference($dob, '%y');
			if ($age_y < 18) {
				$validate->setError('dob', 'You must be 18 years of age');
			} else if ($age_y > 90) {
				$validate->setError('dob', 'You must be younger then 90 years of age');
			}
		}
	}

	if ($is_ssn_edit == '') {
		$validate->digit(array('required' => true, 'field' => 'ssn', 'value' => $ssn, 'min' => 9, 'max' => 9), array('required' => 'SSN required', 'invalid' => 'Valid Social Security Number is required'));
	}

	if ($is_ssn_edit == "Y") {
		$validate->digit(array('required' => true, 'field' => 'ssn', 'value' => $ssn, 'min' => 9, 'max' => 9), array('required' => 'SSN required', 'invalid' => 'Valid Social Security Number is required'));
	}
	
	$validate->string(array('required' => true, 'field' => 'account_type', 'value' => $account_type), array('required' => 'Account type is required'));

	if (!empty($license_number)) {
		foreach ($license_number as $lnkey => $lNum) {
			$validate->string(array('required' => true, 'field' => 'license_number_' . $lnkey, 'value' => $lNum), array('required' => 'License number is required', 'invalid' => 'Valid license Number is required'));
		}
	}

	if(!empty($license_expiry)){
		$tempArr =array_keys($license_expiry);
		$tempId = end($tempArr);
		$temp_l_type = checkIsset($license_type[$tempId]);
		$temp_license_auth = checkIsset($license_auth[$tempId]);
		$templ_state = checkIsset($license_state[$tempId]);

		foreach ($license_expiry as $lekey => $lexpiry) {
			
			$temp_license_typeArr = $license_type;
			$temp_license_authArr = $license_auth;
			$temp_license_state = $license_state;

			if(isset($temp_license_typeArr[$tempId]))
				unset($temp_license_typeArr[$tempId]);
			if(isset($temp_license_state[$tempId]))
				unset($temp_license_state[$tempId]);
			if(isset($temp_license_authArr[$tempId]))
				unset($temp_license_authArr[$tempId]);
			$validate->string(array('required' => true, 'field' => 'license_state_' . $tempId, 'value' => $templ_state), array('required' => 'License state is required'));
			
			if($tempId != $lekey){
				if ($templ_state == $temp_license_state[$lekey] && $temp_l_type == checkIsset($temp_license_typeArr[$lekey]) &&$temp_license_auth == checkIsset($temp_license_authArr[$lekey])) {
					$validate->setError("license_state_" . $tempId, "Please select different license state");
				}
			}
		}
	}
	if (!empty($license_expiry)) {
		check_agent_license_validation($_SESSION['agents']['id'],$validate,$hdn_license,$license_state,$license_number,$license_active,$license_type,$license_auth,$license_expiry,$license_not_expn,$edit);
	}

	$validate->digit(array('required' => true, 'field' => 'npn_number', 'value' => $npn_no), array('required' => 'NPN number is required', 'invalid' => 'Valid NPN number is required'));

	$validate->string(array('required' => true, 'field' => 'e_o_coverage', 'value' => $e_o_coverage), array('required' => 'Select any option'));
	if ($e_o_coverage == 'Y' && $e_o_by_parent=="N") {
		// if (!$is_draft || $e_o_amount != "") {
		// 	$validate->string(array('required' => true, 'field' => 'e_o_amount', 'value' => $e_o_amount), array('required' => 'Amount is required'));
		// 	if ($e_o_amount < 1000000) {
		// 		$validate->setError("e_o_amount", "Minimum E&O amount is $1,000,000");
		// 	}
		// }
		$validate->string(array('required' => true, 'field' => 'e_o_expiration', 'value' => $e_o_expiration), array('required' => 'Expiration Date is required'));
		if ($e_o_expiration != "") {
			if (validateDate($e_o_expiration,'m/d/Y')) {
				if (!isFutureDateMain($e_o_expiration,'m/d/Y')) {
					$validate->setError("e_o_expiration", "Please Add Future Expiration Date is required");
				}
			} else {
				$validate->setError("e_o_expiration", "Valid Expiration Date is required");
			}
		}
	}
	if ($e_o_coverage == "Y" && $e_o_by_parent == 'N') {
		$selADoc = "SELECT e_o_document FROM agent_document WHERE agent_id=:agent_id";
		$whrADoc = array(":agent_id" => $agent_res['id']);
		$resDoc = $pdo->selectOne($selADoc, $whrADoc);
		if (empty($resDoc["e_o_document"])) {
			if(!empty($e_o_document)){
				if (checkIsset($e_o_document['error']) == UPLOAD_ERR_NO_FILE) {
					$validate->setError('e_o_document', "Please add E&O document");
				} else {
					if (!empty($e_o_document["name"]) && !in_array($e_o_document["type"], array("application/pdf", "application/doc"))) {
						$validate->setError('e_o_document', "Please add valid E&O document");
					}
				}
			}else{
				$validate->setError('e_o_document', "Please add E&O document file");
			}
		}
	}

		if ($account_type == "Business") {				
			$validate->string(array('required' => true, 'field' => 'business_name', 'value' => $business_name), array('required' => 'Agency Legal Name is required.'));
			$validate->string(array('required' => true, 'field' => 'business_address', 'value' => $business_address), array('required' => 'Address required.'));
			if(!empty($business_address2) && preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/',$business_address2)) {
				$validate->setError('business_address2','Special character not allowed');
			}
			$validate->string(array('required' => true, 'field' => 'business_city', 'value' => $business_city), array('required' => 'City required.'));
			$validate->string(array('required' => true, 'field' => 'business_state', 'value' => $business_state), array('required' => 'State required.'));
			// $validate->string(array('required' => true, 'field' => 'business_zipcode', 'value' => $business_zipcode), array('required' => 'Zip Code required.'));
			$validate->string(array('required' => true, 'field' => 'business_zipcode', 'value' => str_replace('_','',$business_zipcode) ,'min'=>5), array('required' => 'Zip Code required.'));
			if (!$validate->getError('business_zipcode')){
		        include_once '../includes/function.class.php';
		        $function_list = new functionsList();
		        $zipAddress = $function_list->uspsCityVerification($business_zipcode);
		        if($zipAddress['status'] !='success'){
		            $validate->setError("business_zipcode",$zipAddress['error_message']);
		        }
		    }
		}

		// Commission Tab
		$bankname = checkIsset($_POST["bankname"]);
		$bank_rounting_number = checkIsset($_POST['bank_rounting_number']);
		$bank_account_number = checkIsset($_POST['bank_account_number']);
		$entered_account_number = checkIsset($_POST['entered_account_number']);
		$bank_number_confirm = checkIsset($_POST['bank_number_confirm']);		
		$bnk_account_type = checkIsset($_POST['bank_account_type']);	

		if (!in_array($_SESSION['agents']['agent_coded_level'], array("LOA"))) {
			$validate->string(array('required' => true, 'field' => 'bank_account_type', 'value' => $bnk_account_type), array('required' => 'Please Select Account Type'));
			$validate->string(array('required' => true, 'field' => 'bankname', 'value' => $bankname), array('required' => 'Bank name is required'));
			$validate->digit(array('required' => true, 'field' => 'bank_rounting_number', 'value' => $bank_rounting_number), array('required' => 'Routing number is required', 'invalid' => "Enter valid Routing number"));
				if (!$validate->getError("bank_rounting_number")) {
					if (checkRoutingNumber($bank_rounting_number) == false) {
						$validate->setError("bank_rounting_number", "Enter valid routing number");
					}
				}
			if (empty($entered_account_number)) {
				// $validate->string(array('required' => true, 'field' => 'bank_account_number', 'value' => $bank_account_number), array('required' => 'Account number is required'));
				$validate->digit(array('required' => true, 'field' => 'bank_account_number', 'value' => $bank_account_number,'min'=>$MIN_ACCOUNT_NUMBER,'max'=>$MAX_ACCOUNT_NUMBER), array('required' => 'Account number is required', 'invalid' => "Enter valid Account number"));
			}
			if(!empty($bank_account_number)){
				$validate->digit(array('required' => true, 'field' => 'bank_account_number', 'value' => $bank_account_number,'min'=>$MIN_ACCOUNT_NUMBER,'max'=>$MAX_ACCOUNT_NUMBER), array('required' => 'Account number is required', 'invalid' => "Enter valid Account number"));
			}
			if (empty($entered_account_number)) {
				if($step != 3){
					$validate->string(array('required' => true, 'field' => 'bank_number_confirm', 'value' => $bank_number_confirm), array('required' => 'Confirm Account number is required'));
					if (!$validate->getError('bank_number_confirm')) {
						if ($bank_number_confirm != $bank_account_number) {
							$validate->setError('bank_number_confirm', "Enter same Account Number");
						}
					}
				}
			}
			
			if (empty($agent_res["w9_pdf"])) {
				if(!empty($w9_form_business)) {
					if (!isset($w9_form_business) || $w9_form_business['error'] == UPLOAD_ERR_NO_FILE) {
						$validate->setError('w9_form_business', "Please add w9 file");
					} else {
						if ($w9_form_business["type"] != "application/pdf") {
							$validate->setError('w9_form_business', "Please add valid w9 pdf file");
						}
					}
				}else{
					$validate->setError('w9_form_business', "Please add w9 file");
				}
			}
		}
 }
//********* step2 validation code end   ********************

//********* step3 validation code Start   ********************
 if (!$is_draft && $step >= 3) {
	$validate->string(array('required' => true, 'field' => 'signature_data', 'value' => $signature_data), array('required' => 'Please draw your signature'));
	if (strtoupper($check_agree) != 'Y') {
		$validate->setError('check_agree', 'Please agree to terms and conditions');
	}
 }
//********* step3 validation code end   ********************

if ($validate->isValid()) {

	$new_update_details =array(
		'account_type' => checkIsset($account_type)=='Business' ? 'Agency' : 'Agent',
		'fname' => checkIsset($fname),
		'lname' => checkIsset($lname),
		'address' => checkIsset($address),
		'address_2' => checkIsset($address_2),
		'city' => checkIsset($city),
		'state' => checkIsset($state),
		'zip' => checkIsset($zipcode),
		'public_name' => checkIsset($admin_name),
		'public_email' => checkIsset($admin_email),
		'public_phone' => checkIsset($admin_phone),
		'user_name' => checkIsset($username),
		'company_name' => checkIsset($business_name),
		'company_address' => checkIsset($business_address),
		'company_address_2' => checkIsset($business_address2),
		'company_city' => checkIsset($business_city),
		'company_state' => checkIsset($business_state),
		'company_zip' => checkIsset($business_zipcode),
		'tax_id' => checkIsset($business_taxid),
		'npn' => checkIsset($npn_no),
		'display_in_member' =>  checkIsset($_POST['display_in_member']) == 'Y' ? 'Selected' : 'Unselected' ,
		'is_branding' => !empty($_POST['is_branding']) ? 'Y' : 'N',
		'e_o_coverage' => checkIsset($e_o_coverage) == 'Y' ? 'Selected' : 'Unselected',
		'e_o_amount' => checkIsset($e_o_amount),
		'e_o_expiration' => checkIsset($e_o_expiration),
		'by_parent' => checkIsset($e_o_by_parent) == 'Y' ? 'Selected' : 'Unselected',
		'birth_date' => !empty($dob) ?   date('Y-m-d', strtotime($dob)) : '',
		'last_four_ssn' =>!empty($ssn) ? substr($ssn, -4) : '',
	);
	
	$_SESSION['agent_tmp_member'] = array();
	$contract_activity = array();

	if ($sponsor_id > 0) {
		$selSpo = "SELECT type,email FROM customer WHERE id=:id";
		$whr = array(':id' => $sponsor_id);
		$resSpo = $pdo->selectOne($selSpo, $whr);
		$sponsor_type = $resSpo['type'];
		$sponsor_email = $resSpo['email'];
	} else {
		$sponsor_id = 0;
	}
	$_SESSION['agent_tmp_member']['id'] = $agent_res['id'];
	if ($step >= 1) {
		$_SESSION["agents"]["user_name"] = $username;
		//public email entry
		$cs_param = array();
		$params = array(
			'public_name' => $admin_name,
			'public_email' => $admin_email,
			'public_phone' => $admin_phone,
			'user_name' => makesafe($username),
		);

		$upd_where = array(
			'clause' => 'id = :id',
			'params' => array(
				':id' => $_SESSION['agent_tmp_member']['id'],
			),
		);

		$contract_activity['customer'] = $pdo->update('customer', $params, $upd_where,true);
		$cs_param['display_in_member'] = !empty($_POST['display_in_member']) ? 'Y' : 'N';
		$cs_param['is_branding'] = !empty($_POST['is_branding']) ? 'Y' : 'N';
		$upd_where = array(
			'clause' => 'customer_id = :id',
			'params' => array(
				':id' => $_SESSION['agent_tmp_member']['id'],
			),
		);
		$contract_activity['customer_settings0'] = $pdo->update('customer_settings', $cs_param, $upd_where,true);

		$response['status'] = 'account_approved';
		$response['step'] = 'first';
		if (in_array($agent_res["status"], array("Pending Contract")) && !in_array($agent_res["is_contract_approved"], array("Pending Resubmission"))) {
			$response['next_step'] = 'third';
		}
		unset($_SESSION['site_visit']);
	}
	if($step >=2){
		$type = "Agent";
		$params = array(
			'fname' => $fname,
			'lname' => $lname,
			'address_2' => $address_2,
			'address' => $address,
			'country_id' => '231',
			'country_name' => "United States",
			'city' => $city,
			'state' => $state,
			'zip' => $zipcode,
			'company_id' => $DEFAULT_COMPANY_ID,
			'updated_at' => 'msqlfunc_NOW()',
		);
		if ($dob != "") {
			$params['birth_date'] = date('Y-m-d', strtotime($dob));
		}
		if ($ssn != "") {
			$params['ssn'] = "msqlfunc_AES_ENCRYPT('" . $ssn . "','" . $CREDIT_CARD_ENC_KEY . "')";
			$params['last_four_ssn'] = substr($ssn, -4);
		}
		if (!$is_draft) {
			$params['status'] = 'Pending Approval';
			$new_update_details['status'] = $params['status'];
		}
		$cs_param = array(
			'account_type'	=> $account_type,
			'ip_address'	=> !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'],
			'npn'			=>	$npn_no,
		);

		$upd_where = array(
			'clause' 	=> 'id = :id',
			'params'	=> array(
			':id'		=> $_SESSION['agent_tmp_member']['id'],
			),
		);
		$csupd_where = array(
			'clause'	=> 'customer_id = :id',
			'params'	=> array(
				':id'	=> $_SESSION['agent_tmp_member']['id'],
			),
		);
		$params = array_filter($params, "strlen"); //removes null and blank array fields from array
		if (!$is_draft) {
			$cs_param['is_contract_approved'] = NULL;
		}
		$contract_activity['customer'] = $pdo->update('customer', $params, $upd_where,true);
		$contract_activity['customer_settings1'] = $pdo->update('customer_settings',$cs_param,$csupd_where,true);

		// Link Agent to Their Agency
		$agencyId = $functionsList->getAgencyId($_SESSION['agent_tmp_member']['id']);
		$customer_settings = array("agency_id" => $agencyId);
		$functionsList->addCustomerSettings($customer_settings,$_SESSION['agent_tmp_member']['id']);

		if ($account_type == "Business") {
			$cs_params = array(
				'company_name' => makesafe($business_name),
				'company_address' => makesafe($business_address),
				'company_address_2' => makesafe($business_address2),
				'company_country_id' => makesafe('231'),
				'company_country_name' => makeSafe("United States"),
				'company_city' => makesafe($business_city),
				'company_state' => makesafe($business_state),
				'company_zip' => makesafe($business_zipcode),
				'ip_address' => !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'],
			);
			if (!empty($business_taxid)) {
				$cs_params['tax_id'] = $business_taxid;
			}
			$upd_where = array(
				'clause' => 'customer_id = :id',
				'params' => array(
					':id' => $_SESSION['agent_tmp_member']['id'],
				),
			);
			$cs_params = array_filter($cs_params, "strlen"); //removes null and blank array fields from array
			$contract_activity['customer_settings2'] = $pdo->update('customer_settings', $cs_params, $upd_where,true);
		}
		//w9 pdf start
			$w9_doc = $w9_form_business;
			// if (!in_array($_SESSION['agents']['agent_coded_level'], array("LOA"))) {
				if (!empty($w9_doc["name"])) {
					$w9_pdf_extension_tmp = explode(".", $w9_doc['name']);
					$w9_pdf_extension = end($w9_pdf_extension_tmp);
					$w9_pdf_tmp_name = $w9_doc['tmp_name'];
					$new_w9_pdf_name = 'w9_doc_' . round(microtime(true)) . '.' . $w9_pdf_extension;
					$existingW9FileName = $agent_res["w9_pdf"];
					if (!empty($existingW9FileName)) {
						if (file_exists($AGENT_DOC_DIR . $existingW9FileName)) {
							unlink($AGENT_DOC_DIR . $existingW9FileName);
						}
					}
					move_uploaded_file($w9_pdf_tmp_name, $AGENT_DOC_DIR . $new_w9_pdf_name);
					$response["w9_pdf"] = $AGENT_DOC_WEB . $new_w9_pdf_name;
					$update = array(
						'w9_pdf' => $new_w9_pdf_name,
					);
					$upd_where = array(
						'clause' => 'customer_id = :id',
						'params' => array(
							':id' => $_SESSION['agent_tmp_member']['id'],
						),
					);
					$update = array_filter($update, "strlen"); //removes null and blank array fields from array
					$contract_activity['customer_settings3'] = $pdo->update('customer_settings', $update, $upd_where,true);
				}
			// }
		//w9 pdf start

		//Eo document Start
			$selADoc = "SELECT id FROM agent_document WHERE agent_id=:agent_id";
			$whrADoc = array(":agent_id" => $_SESSION['agent_tmp_member']['id']);
			$resADoc = $pdo->selectOne($selADoc, $whrADoc);
			if ($e_o_coverage == 'Y') {
				if (!empty($resADoc) && count($resADoc) > 0) {
					$updateparams = array(
						'e_o_coverage' => $e_o_coverage,
						'e_o_amount' => $e_o_amount,
						'by_parent'=>$e_o_by_parent,
						'updated_at' => 'msqlfunc_NOW()',
					);
					if ($e_o_expiration != "") {
						$updateparams['e_o_expiration'] = date('Y-m-d', strtotime($e_o_expiration));
					}
					$upd_where = array(
						'clause' => 'agent_id = :id',
						'params' => array(
							':id' => $_SESSION['agent_tmp_member']['id'],
						),
					);
					$updateparams = array_filter($updateparams, "strlen"); //removes null and blank array fields from array
					$contract_activity['agent_document'] = $pdo->update('agent_document', $updateparams, $upd_where,true);
				} else {
					$insparams = array(
						'agent_id' => $_SESSION['agent_tmp_member']['id'],
						'e_o_coverage' => $e_o_coverage,
						'e_o_amount' => $e_o_amount,
						'by_parent'=>$e_o_by_parent,
						'created_at' => 'msqlfunc_NOW()',
						'updated_at' => 'msqlfunc_NOW()',
					);
					if ($e_o_expiration != "") {
						$insparams['e_o_expiration'] = date('Y-m-d', strtotime($e_o_expiration));
					}
					$insparams = array_filter($insparams, "strlen"); //removes null and blank array fields from array
					$agent_doc_id = $pdo->insert('agent_document', $insparams);
					$contract_activity['agent_document'] = 'Agent Error and Ommissions Insurance (E&O) Detail Added.';
				}
			}
		//Eo document End
		//Direct Deposit Account Start
			if (!in_array($_SESSION['agents']['agent_coded_level'], array("LOA"))) {
				$selDirect = "SELECT id from direct_deposit_account WHERE customer_id=:customer_id";
				$whrDirect = array(":customer_id" => $_SESSION['agent_tmp_member']['id']);
				$resDirect = $pdo->selectOne($selDirect, $whrDirect);
				if (!empty($resDirect)) {
					$updateparams = array(
						'bank_name' => $bankname,
						'account_type' => $bnk_account_type,
						'routing_number' => $bank_rounting_number,
						'account_number' => $bank_account_number,
						'updated_at' => 'msqlfunc_NOW()',
					);
					$upd_where = array(
						'clause' => 'customer_id = :customer_id',
						'params' => array(
							':customer_id' => $_SESSION['agent_tmp_member']['id'],
						),
					);
					$updateparams = array_filter($updateparams, "strlen"); //removes null and blank array fields from array
					$getActivity = false;
					if(!$is_draft){
						$getActivity = true;
					}
					$contract_activity['direct_deposit_account'] = $pdo->update('direct_deposit_account', $updateparams, $upd_where,$getActivity);
				} else {
					$insparams = array(
						'customer_id' => $_SESSION['agent_tmp_member']['id'],
						'bank_name' => $bankname,
						'account_type' => $bnk_account_type,
						'routing_number' => $bank_rounting_number,
						'account_number' => $bank_account_number,
						'effective_date' => 'msqlfunc_NOW()',
						'status'		=> 'Active',
						'created_at' => 'msqlfunc_NOW()',
						'updated_at' => 'msqlfunc_NOW()',
					);
					$insparams = array_filter($insparams, "strlen"); //removes null and blank array fields from array
					$pdo->insert('direct_deposit_account', $insparams);
					$contract_activity['direct_deposit_account'] = 'Direct Deposite Account Added.';
				}
			}
		//Direct Deposit Account End

		//e-o document entry Start
			if(!empty($e_o_document)){
				$tmp_v1 = explode(".", $e_o_document['name']);
				$extension = end($tmp_v1);
				$doc_tmp_name = $e_o_document['tmp_name'];
				$e_o_coverage_filename = 'agent_doc_' . round(microtime(true)) . '.' . $extension;
				$selADoc = "SELECT e_o_document FROM agent_document WHERE agent_id=:agent_id";
				$whrADoc = array(":agent_id" => $_SESSION['agent_tmp_member']['id']);
				$resADoc = $pdo->selectOne($selADoc, $whrADoc);
				if ($resADoc) {
					$updateparams = array(
						'e_o_coverage' => $e_o_coverage,
						'updated_at' => 'msqlfunc_NOW()',
					);
					if ($e_o_coverage == 'Y' &&!empty($e_o_document['name'])) {
						$updateparams['e_o_document'] = $e_o_coverage_filename;

						$existingErrorDocument = $resADoc["e_o_document"];
						if ($existingErrorDocument != "") {
							if (file_exists($AGENT_DOC_DIR . $existingErrorDocument)) {
								unlink($AGENT_DOC_DIR . $existingErrorDocument);
							}
						}
						move_uploaded_file($doc_tmp_name, $AGENT_DOC_DIR . $e_o_coverage_filename);
						$response["e_o_document"] = $AGENT_DOC_WEB . $e_o_coverage_filename;
					}
					$upd_where = array(
						'clause' => 'agent_id = :id',
						'params' => array(
							':id' => $_SESSION['agent_tmp_member']['id'],
						),
					);
					$updateparams = array_filter($updateparams, "strlen"); //removes null and blank array fields from array
					$pdo->update('agent_document', $updateparams, $upd_where);
					$contract_activity['agent_document'] = 'Agent Error and Ommissions Insurance (E&O) Detail Document updated.';
				} else {
					$insparams = array(
						'agent_id' => $_SESSION['agent_tmp_member']['id'],
						'e_o_coverage' => $e_o_coverage,
						'created_at' => 'msqlfunc_NOW()',
						'updated_at' => 'msqlfunc_NOW()',
					);
					if ($e_o_coverage == 'Y' && !empty($e_o_document['name'])) {
						$insparams['e_o_document'] = $e_o_coverage_filename;
						move_uploaded_file($doc_tmp_name, $AGENT_DOC_DIR . $e_o_coverage_filename);
						$response["e_o_document"] = $AGENT_DOC_WEB . $e_o_coverage_filename;
					}
					$insparams = array_filter($insparams, "strlen"); //removes null and blank array fields from array
					$agent_doc_id = $pdo->insert('agent_document', $insparams);
					$contract_activity['agent_document'] = 'Agent Error and Ommissions Insurance (E&O) Detail Document Added.';
				}
			}
		//e-o document entry End
		
		//Agent license Code Start
			$contract_activity['agent_license'] = add_update_license($hdn_license,$license_state,$license_number,$license_active,$license_type,$license_auth,$license_expiry,$license_not_expn);
		//Agent license Code End

		//lead update Start
			if ($_SESSION['agent_tmp_member']['id'] > 0 && $account_type == "Business" ) {
				$leads_sql = "SELECT id FROM leads where customer_id = :customer_id";
				$leads_where = array(":customer_id" => makeSafe($_SESSION['agent_tmp_member']['id']));
				$leads_res = $pdo->selectOne($leads_sql, $leads_where);
				if (!empty($leads_res) && count($leads_res) > 0) {
					$leads_update_params = array(
						// 'cell_phone' => makeSafe($phone),
						'company_name' => makeSafe($business_name),
						'company_address' => makeSafe($business_address),
						'company_address2' => makeSafe($business_address2),
						'company_city' => makesafe($business_city),
						'company_state' => makesafe($business_state),
						'company_zip' => makesafe($business_zipcode),
						'status' => "Converted",
						'updated_at' => 'msqlfunc_NOW()',
					);
					$leads_update_where = array(
						'clause' => 'customer_id = :customer_id',
						'params' => array(
							':customer_id' => $_SESSION['agent_tmp_member']['id'],
						),
					);
					$leads_update_params = array_filter($leads_update_params, "strlen"); //removes null and blank array fields from array
					$pdo->update("leads", $leads_update_params, $leads_update_where);
				}
			}
		//lead update End

		if (!$is_draft) {
			addAdminNotification(0, 2, "{HOST}/agent_detail_v1.php?id=" . md5($agent_res["id"]), 0, 'N', $_SESSION['agent_tmp_member']['id']);
			$NOTIFICATION_EMAIL = array("shailesh@cyberxllc.com");
			$rep_params = array();
			$rep_params["fname"] = $_SESSION["agents"]["fname"];
			$rep_params["lname"] = $_SESSION["agents"]["lname"];
			$rep_params["Agent_ID"] = $_SESSION["agents"]["rep_id"];
			$rep_params['link'] = $ADMIN_HOST . "/agent_detail_v1.php?id=" . md5($_SESSION['agents']['id']);
			$rep_params['USER_IDENTITY'] = array('location' => $REQ_URL);
			// Admin - Agent Pending Documentation
			$trigger_id = 7;
			$triggerArr = $pdo->selectOne("SELECT * from triggers where id=:id and is_deleted='N'",array(":id"=>$trigger_id));
			if(!empty($triggerArr['id'])){
				if(!empty($triggerArr['to_email_specific']))
					$NOTIFICATION_EMAIL[] = $triggerArr['to_email_specific'];
				if(!empty($triggerArr['cc_email_specific']))
					$NOTIFICATION_EMAIL[] = $triggerArr['cc_email_specific'];
				if(!empty($triggerArr['bcc_email_specific']))
					$NOTIFICATION_EMAIL[] = $triggerArr['bcc_email_specific'];
			}

			$smart_tags = get_user_smart_tags($_SESSION['agents']['id'],'agent');
			
			if($smart_tags){
				$rep_params = array_merge($rep_params,$smart_tags);
			}

			trigger_mail($trigger_id, $rep_params, $NOTIFICATION_EMAIL);

			//trigger sent to parent agent when document is submit code start
			$agentMailSql = "SELECT cs.company_name,c.fname,c.lname,s.fname as spnsor_fname,s.lname as sponsor_lname,s.email as sponsor_email
						   FROM customer c
						   LEFT JOIN customer s ON (s.id = c.sponsor_id)
						   LEFT JOIN customer_settings cs ON(cs.customer_id=c.id)
						   WHERE c.id=:id";
			$agentMailRes = $pdo->selectOne($agentMailSql, array(":id" => $_SESSION['agents']['id']));

			$trigger_params = array();
			$trigger_params["fname"] = $agentMailRes['spnsor_fname'] . ' ' . $agentMailRes['sponsor_lname'];
			$trigger_params["business_name"] = ($agentMailRes['company_name'] != '') ? $agentMailRes['company_name'] : $agentMailRes['fname'] . ' ' . $agentMailRes['fname'];
			$trigger_params["Agent_ID"] = $_SESSION["agents"]["rep_id"];
			$trigger_params['link'] = $ADMIN_HOST . "/agent_detail_v1.php?id=" . md5($_SESSION['agents']['id']);
			$trigger_param['USER_IDENTITY'] = array('location' => $REQ_URL);

			$sponsor_email = $agentMailRes['sponsor_email'];
			$trigger_id2 = 80;

			$smart_tags = get_user_smart_tags($_SESSION['agents']['id'],'agent');
			
			if($smart_tags){
				$trigger_param = array_merge($trigger_param,$smart_tags);
			}

			trigger_mail($trigger_id2, $trigger_params, $sponsor_email);
			$license_res = $pdo->select("SELECT license_num,license_exp_date,selling_licensed_state FROM agent_license WHERE agent_id=:agent_id AND is_deleted='N'", array(":agent_id" => $_SESSION['agents']['id']));
			$license_arr = array();
			foreach ($license_res as $lkey => $lic) {
				$license_arr[$lkey] = array("license" => $lic['license_num'], 'expiration_date' => $lic['license_exp_date'], "state" => $lic['selling_licensed_state']);
			}

			//audit log entry start
				$user_data = array();
				$user_data['user_id'] = $_SESSION['agent_tmp_member']['id'];
				$user_data['display_id'] = getname("customer", $_SESSION['agent_tmp_member']['id'], "display_id", "id");
				$user_data['full_name'] = makesafe($agent_res["fname"].' '.$agent_res["lname"]);
				$user_data['user_type'] = makesafe($agent_res["type"]);
				$audit_log_id = audit_log($user_data, $_SESSION['agent_tmp_member']['id'], "Agents", "Agent Signed Contract", '', '', 'agent contract update');
			//audit log entry End
		}
		$response['status'] = 'account_approved';
		$response['step'] = 'second';
		unset($_SESSION['site_visit']);
	}
	if (!$is_draft && $step == 3) {

		//signature add and active agent
		if ($_SESSION['agent_tmp_member']['id'] > 0) {
			$signature_file_name = $agent_res["fname"] . time() . '.png';
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

		    /* agent contract save in s3 bucket start (task => EL8-1170) */
				$agentContractFileName = $functionsList->saveAgentContract($_SESSION['agent_tmp_member']['id'],$signature_file_name);
			/* agent contract save in s3 bucket end (task => EL8-1170) */

			$_SESSION["agents"]["status"] = "Active";
			$update_params = array(
				'status' => 'Active',
				'created_at' => 'msqlfunc_NOW()',
				'updated_at' => 'msqlfunc_NOW()',
			);
			$new_update_details['status'] = $update_params['status'];
			$cs_update_params = array(
				'signature_file' => $signature_file_name,
				'ip_address' => !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'],
				'signature_date' => 'msqlfunc_NOW()',
			);
			if(!empty($agentContractFileName)){
				$cs_update_params["agent_contract_file"] = $agentContractFileName;
			}
			if ($isRecontract == "Yes") {
				$cs_update_params["recontract_status"] = "Active";
			}
			$cs_update_params["is_contract_approved"] = NULL;
			$upd_where = array(
				'clause' => 'id = :id',
				'params' => array(
					':id' => $_SESSION['agent_tmp_member']['id'],
				),
			);
			$cs_upd_where = array(
				'clause' => 'customer_id = :id',
				'params' => array(
					':id' => $_SESSION['agent_tmp_member']['id'],
				),
			);
			$pdo->update('customer', $update_params, $upd_where);
			$customer_settings = $pdo->update('customer_settings', $cs_update_params, $cs_upd_where,true);
			// Trigger Action Send Email/SMS
			$TriggerMailSms->trigger_action_mail('agent_onboarding',$_SESSION['agent_tmp_member']['id'],'agent');
			
			if(!empty($customer_settings)){
				$contract_activity['terms'] = 'Terms & Condition';
				$contract_activity['customer_settings'] = 'Agent added signature and accepted Terms & Conditions.';
			}

			if ($sponsor_id > 0) {
				$sponsor_id = $sponsor_id;
				$sponsor_type = $sponsor_type;
			} else {
				$sponsor_id = 0;
				$sponsor_type = 'Agent';
			}
			//audit log entry start
				$user_data = array();
				$user_data['user_id'] = $_SESSION['agent_tmp_member']['id'];
				$user_data['display_id'] = getname("customer", $_SESSION['agent_tmp_member']['id'], "display_id", "id");
				$user_data['full_name'] = makesafe($agent_res["fname"]." ".$agent_res["lname"]);
				// $user_data['lname'] = makesafe($agent_res["lname"]);
				$user_data['user_type'] = makesafe($agent_res["type"]);
				$audit_log_id = audit_log($user_data, $_SESSION['agent_tmp_member']['id'], "Agents", "Agent Terms Agreed", '', '', 'agent contract update');
			//audit log entry end

			$checkNotificationSql = "SELECT * FROM agent_notification_settings WHERE agent_id =:agent_id AND is_deleted='N' AND is_agent_notifications='Y'";
			$checkNotificationRes = $pdo->selectOne($checkNotificationSql, array(":agent_id" => $_SESSION['agents']['sponsor_id']));

			if ($checkNotificationRes) {
					addAgentNotification($_SESSION['agents']['sponsor_id'], 9, "{AGENT}/agent_list.php?agentId=" . $_SESSION['agents']['rep_id'], 0, 'N', $_SESSION['agents']['id'],"","Agent","Agent");
			}
			$response['status'] = 'account_approved';
			$response['step'] = 'third';
			unset($_SESSION['site_visit']);
		} else {
			$response['status'] = 'session_fail';
		}
	}
}else{
	$errors = $validate->getErrors();
	$response['errors'] = $errors;
	$response['status'] = "fail";
}


function delete_license($lid){
	$selADoc = "SELECT id FROM agent_license WHERE agent_id=:agent_id AND id=:id AND is_deleted='N'";
	$whrADoc = array(":agent_id" => $_SESSION['agents']['id'],":id"=>$lid);
	global $pdo;
	$resADoc = $pdo->selectOne($selADoc, $whrADoc);
	if (!empty($resADoc)) {
		//remove license which is not exists when save
			$upd_where = array(
				'clause' => 'id = :id',
				'params' => array(
					':id' => $resADoc['id'],
				),
			);
			$pdo->update('agent_license', array("is_deleted" => 'Y', 'updated_at' => 'msqlfunc_NOW()', 'license_removal_date'=>'msqlfunc_NOW()','license_status'=>'Inactive'), $upd_where);
		// }
	}
}

function add_update_license($hdn_license,$license_state,$license_number,$license_active,$license_type,$license_auth,$license_expiry,$license_not_exp,$ajax=''){
	$agent_doc_id ='';
	global $pdo;
	$license_key_arr = array(
		'selling_licensed_state' => 'Selling License state',
		'license_num' => 'License Number',
		'license_active_date' => 'License Active Date',
		'license_type' => 'License Type',
		'license_not_expire' => 'License Not Expire',
		'license_exp_date' => 'License Expire Date',
		'license_auth' => 'License Auth',
	);

	$agent_licence_activity = array();
	$i=0;
	//insert and update license
	foreach ($hdn_license as $hkey => $h_id) {
		$i++; 
		//check if license id is empty/zero then we need to insert else we need to update
		if (empty($h_id)) {
			$h_id = 0;
		}
		$selADoc = "SELECT id FROM agent_license WHERE agent_id=:agent_id AND id=:id AND is_deleted='N'";
		$whrADoc = array(":agent_id" => $_SESSION['agents']['id'], ":id" => $h_id);
		$resADoc = $pdo->selectOne($selADoc, $whrADoc);
		if (!empty($resADoc)) {
			//update license information
			$updateParams = array(
				'selling_licensed_state' => $license_state[$hkey],
				'license_num' => $license_number[$hkey],
				'license_active_date' => date('Y-m-d', strtotime($license_active[$hkey])),
				'license_type' => isset($license_type[$hkey]) ?  $license_type[$hkey] : '',
				'license_not_expire' => $license_not_exp[$hkey],
				'license_auth' => isset($license_auth[$hkey]) ? $license_auth[$hkey] : '',
				'updated_at' => 'msqlfunc_NOW()',
			);
			if ($license_expiry[$hkey] != "" && $license_not_exp[$hkey] == 'N') {
				$updateParams['license_exp_date'] = date('Y-m-d', strtotime($license_expiry[$hkey]));
			} else {
				$updateParams['license_exp_date'] = '2099-12-31';
			}
			$upd_where = array(
				'clause' => 'agent_id = :agent_id and id = :id',
				'params' => array(
					':id' => $resADoc['id'],
					':agent_id' => $_SESSION['agents']['id'],
				),
			);
			$updateParams = array_filter($updateParams, "strlen"); //removes null and blank array fields from array
			$updated_license_data = $pdo->update('agent_license', $updateParams, $upd_where,true);
			$j=$resADoc['id'];
			if(!empty($updated_license_data)){
				foreach($updated_license_data as $key => $license){
					if(in_array($key,array('license_exp_date','license_active_date'))){
						$license = getCustomDate($license);
						$updateParams[$key] = getCustomDate($updateParams[$key]);
					}
					if(in_array($license,array('Y','N'))){
						$license = $license == 'Y' ? "Selected" : "Unselected";
						$updateParams[$key] = $updateParams[$key] == 'Y' ? "Selected" : "Unselected";
					}
					if(array_key_exists($key,$updateParams)){
						if($resADoc['id']==$j){
							$agent_licence_activity[] = 'In License '.$i.'<br>';
							$j++;
						}
						$agent_licence_activity[] = '&nbsp;&nbsp;'.$license_key_arr[$key] .' Updated : From '.$license.' To '.$updateParams[$key]."<br>";
					}
				}
			}
		} else {
			//ishit
			$insparams = array(
				'agent_id' => $_SESSION['agents']['id'],
				'selling_licensed_state' => $license_state[$hkey],
				'license_num' => $license_number[$hkey],
				'license_added_date'=>'msqlfunc_NOW()',
				'license_active_date'=>!empty($license_active[$hkey]) ? date('Y-m-d', strtotime($license_active[$hkey])) : NULL,
				'license_not_expire' => $license_not_exp[$hkey],
				'license_type' => isset($license_type[$hkey]) ? $license_type[$hkey] : ''  ,
				'license_auth' => isset($license_auth[$hkey]) ?  $license_auth[$hkey] : '',
				'created_at' => 'msqlfunc_NOW()',
				'updated_at' => 'msqlfunc_NOW()',
			);
			if ($license_expiry[$hkey] != "" && $license_not_exp[$hkey] == 'N') {
				$insparams['license_exp_date'] = date('Y-m-d', strtotime($license_expiry[$hkey]));
			} else {
				$insparams['license_exp_date'] = '2099-12-31';
			}
			$insparams = array_filter($insparams, "strlen"); //removes null and blank array fields from array
			$agent_doc_id = $pdo->insert('agent_license', $insparams);
			$agent_licence_activity[] = 'New License Addedd for State : '.$license_state[$hkey].'.<br>';
		}
	}
	return $agent_licence_activity;
}

if(!empty($contract_activity)){
    agent_profile_activity($contract_activity,$is_draft);
}

function agent_profile_activity($contract_activity,$is_draft){
	global $pdo,$ADMIN_HOST,$new_update_details;
	$msg ='';
	$entity_action = 'Agent Submitted Documentation';
	$msg = "Updated Contract Remaining details In Agent ";

	$description = array();
	$flg = "true";
	$agent_name = $pdo->selectOne("SELECT id, CONCAT(fname,' ',lname) as name ,rep_id from customer where id=:id",array(":id"=>$_SESSION['agent_tmp_member']['id']));
	
	$description['ac_message'] = array(
		'ac_message_1' =>$agent_name['name'].' (',
		'ac_red_2'=>array(
			'href'=> $ADMIN_HOST.'/agent_detail_v1.php?id='.md5($agent_name['id']),
			'title'=> $agent_name['rep_id'],
		),
		'ac_message_2' =>')  Updated Contract Remaining details.',
		);
	if($is_draft && empty($contract_activity['terms'])){
		$description['auto_save'] = 'Auto save updated details';
	}
	foreach($contract_activity as $key => $value){
		if(!empty($value) && is_array($value)){
			foreach($value as $key2 => $val){
				if(array_key_exists($key2,$new_update_details)){
					if(in_array($val,array('Y','N'))){
						$val = $val == 'Y' ? "selected" : "unselected";
					}
					if($key2=='account_type'){
						$val = $val =='Business' ? 'Agency' : 'Agent';
					}
					if(!empty($new_update_details[$key2]) && !empty($val)){
						$description['key_value']['desc_arr'][$key2] = ' Updated From '.$val." To ".$new_update_details[$key2].".<br>";
					}else{
						$val1='';
						if(!empty($val)){
							$val1 = "Updated To ".$val."<br>" ;
						}else if(!empty($new_update_details[$key2])) {
							$val1 = "Updated To ".$val."<br>" ;
						}
						
						if(!empty($val)){
							$description['key_value']['desc_arr'][$key2] = $val1;	
						}
					}
					$flg = "false";
				}else{
					$description['description2'][] = ucwords(str_replace('_',' ',$val));
					$flg = "false";
				}
			}    
		}else{
			if(is_array($value) && !empty($value)){
				$description['description'.$key][] = implode('',$value);
				$flg = "false";
			}else if(!empty($value)){
				$description['description'.$key][] = $value;
				$flg = "false";
			}
		}
		
	}
	if($flg == "true"){
		$description['description_novalue'] = 'No updates in agent Contract remaining page.';
	}
	$desc=json_encode($description);
	
	$user_id = $user_type = $user_name = '';
	$user_id = $_SESSION['agents']['id'];
	$user_type ='Agent';
	$user_name = $_SESSION['agents']['fname'].$_SESSION['agents']['lname'];
	if(!empty($user_id)){
	activity_feed(3,$user_id, $user_type , $agent_name['id'], 'Agent', $entity_action,$user_name,"",$desc);
	}
}

$tz = new UserTimeZone('m/d/Y @ g:i:s A T',$_SESSION['agents']['timezone']);
$udate = $pdo->selectOne("SELECT updated_at from customer where id=:id",array(':id'=>$_SESSION['agents']['id']));
//here not used direct $draft variable, we have putted condition on that

$response["updated_at"] = $tz->getDate($udate['updated_at']);
header('Content-Type: application/json');
echo json_encode($response);
dbConnectionClose();
exit();
?>
