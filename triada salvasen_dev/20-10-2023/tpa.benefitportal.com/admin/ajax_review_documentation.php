<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$validate = new Validation();
$is_address_ajaxed = checkIsset($_POST['is_address_ajaxed']) ;
$is_agency_address_ajaxed = checkIsset($_POST['is_agency_address_ajaxed']) ;

$REAL_IP_ADDRESS = get_real_ipaddress();
if($is_address_ajaxed){
    $response = array("status"=>'success');
    $address = $_POST['address'];
    $address_2 = checkIsset($_POST['address_2']);
    $city = $_POST['city'];
    $state = checkIsset($_POST['state']);
    $zipcode = $_POST['zipcode'];
    $old_address = $_POST['old_address'];
    $old_zip = $_POST['old_zipcode'];

    $validate->digit(array('required' => true, 'field' => 'zipcode', 'value' => $zipcode,'min'=> 5,'max'=>5 ), array('required' => 'Zip Code is required'));
    $validate->string(array('required' => true, 'field' => 'address', 'value' => $address), array('required' => 'Address is required'));
    if($validate->isValid()){
        $response['agencyApi'] = "";
        if(!empty($is_agency_address_ajaxed)){
            $response['agencyApi'] = 'success';
        }
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

if($is_agency_address_ajaxed){
    $response = array("status"=>'success');
    $address = $_POST['business_address'];
    $address_2 = checkIsset($_POST['business_address2']);
    $city = $_POST['business_city'];
    $state = checkIsset($_POST['business_state']);
    $zipcode = $_POST['business_zipcode'];
    $old_address = $_POST['old_business_address'];
    $old_zip = $_POST['old_business_zipcode'];

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
// exit();

$contract_status = checkIsset($_POST['contract_status']);//get status from review_documentation.php file

$agent_id_enc = $_POST['agent_id'];

$checkLink_query = "SELECT c.id as id ,w9_pdf,email,rep_id,cs.agent_coded_level FROM customer c LEFT JOIN customer_settings cs on(cs.customer_id=c.id) WHERE md5(c.id)=:agent_id AND (status in ('Pending Approval','Pending Contract','Pending Documentation'))";
$Linkwhere = array(':agent_id' => $agent_id_enc);
$agent_res = $pdo->selectOne($checkLink_query, $Linkwhere);

$agent_id = $agent_res['id'];
$fname = $_POST['fname'];
$lname = $_POST['lname'];
$address = $_POST['address'];
$address_2 = checkIsset($_POST['address_2']);
$city = $_POST['city'];
$state = $_POST['state'];
$zipcode = $_POST['zipcode'];
$dob = str_replace('_','0',$_POST['dob']);
$ssn = phoneReplaceMain($_POST['ssn']);
$is_ssn_edit = checkIsset($_POST['is_ssn_edit']);

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
        if (!checkdate($mm, $dd, $yyyy)) {
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

    // Account Tab
    $account_type = $_POST["account_type"];
    $license_number = !empty($_POST['license_number']) ? $_POST['license_number'] : '';;
    $license_state = !empty($_POST['license_state']) ? $_POST['license_state'] : '';
    $license_expiry = !empty($_POST["license_expiry"]) ? $_POST["license_expiry"] : ''  ;
    $license_not_exp = !empty($_POST['license_not_expire']) ? $_POST['license_not_expire'] : 'N';
    $license_active = !empty($_POST["license_active_date"]) ? $_POST["license_active_date"] : '' ;
    $license_type = !empty($_POST["license_type"]) ? $_POST["license_type"] : array();
    $license_auth = !empty($_POST["licsense_authority"]) ? $_POST["licsense_authority"] : array() ;
    $hdn_license = !empty($_POST["hdn_license"]) ? $_POST["hdn_license"] : array();
    $edit = !empty($_POST['edit']) ? $_POST['edit'] : array();
    $npn_no = $_POST['npn_number'];
    $w9_form_business = checkIsset($_FILES["w9_form_business"]);
    $e_o_coverage = checkIsset($_POST['e_o_coverage']);
    $e_o_by_parent = isset($_POST['e_o_by_parent']) ? $_POST['e_o_by_parent']:'N';
    if ($e_o_coverage == "Y") {
    $e_o_amount = str_replace(array("$", ","), array("", ""), $_POST['e_o_amount']);
    $e_o_expiration = $_POST['e_o_expiration'];
    $e_o_document = checkIsset($_FILES['e_o_document']);
    }

    if ($account_type == "Business") {
        $business_name = $_POST['business_name'];
        $business_address = $_POST['business_address'];
        $business_address2 = checkIsset($_POST['business_address2']);
        $business_city = $_POST['business_city'];
        $business_state = checkIsset($_POST['business_state']);
        $business_zipcode = $_POST['business_zipcode'];
        $business_taxid = $_POST['business_taxid'];
    } 
    $license_not_expn = array();
    foreach($hdn_license as $key => $hdn){
        $license_not_expn[$key] = isset($license_not_exp[$key]) ? $license_not_exp[$key] : 'N' ;
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
        check_agent_license_validation($agent_id,$validate,$hdn_license,$license_state,$license_number,$license_active,$license_type,$license_auth,$license_expiry,$license_not_expn,$edit);
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

    if (!in_array($agent_res['agent_coded_level'], array("LOA"))) {
        
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
            $validate->string(array('required' => true, 'field' => 'bank_number_confirm', 'value' => $bank_number_confirm), array('required' => 'Confirm Account number is required'));
            if (!$validate->getError('bank_number_confirm')) {
                if ($bank_number_confirm != $bank_account_number) {
                    $validate->setError('bank_number_confirm', "Enter same Account Number");
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

    $new_update_details =array(
		'account_type' => checkIsset($account_type)=='Business' ? 'Agency' : 'Agent',
		'fname' => checkIsset($fname),
		'lname' => checkIsset($lname),
		'address' => checkIsset($address),
		'address_2' => checkIsset($address_2),
		'city' => checkIsset($city),
		'state' => checkIsset($state),
		'zip' => checkIsset($zipcode),
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
		'is_contract_approved' => checkIsset($_POST['contract_status']),
    );

    if($validate->isValid()){
        $s_enroll_sql = "SELECT sponsor_id,rep_id,id FROM customer WHERE status in('Pending Approval','Pending Contract','Pending Documentation') AND id=:id";
		$s_where_cust_id = array(':id' => $agent_id);
		$res_enroll = $pdo->selectOne($s_enroll_sql, $s_where_cust_id);
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
            'company_id' => isset($DEFAULT_COMPANY_ID) ? $DEFAULT_COMPANY_ID : 3 ,
            'updated_at' => 'msqlfunc_NOW()',
        );
        if ($dob != "") {
            $params['birth_date'] = date('Y-m-d', strtotime($dob));
        }
        if ($ssn != "") {
            $params['ssn'] = "msqlfunc_AES_ENCRYPT('" . $ssn . "','" . $CREDIT_CARD_ENC_KEY . "')";
            $params['last_four_ssn'] = substr($ssn, -4);
        }

        $cs_param = array(
            'account_type'	=> $account_type,
            'ip_address'	=> !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'],
            'npn'			=>	$npn_no,
        );

        if (count($res_enroll) > 0) {
            $upd_where = array(
                'clause' 	=> 'id = :id',
                'params'	=> array(
                ':id'		=> $agent_id,
                ),
            );
            $csupd_where = array(
                'clause'	=> 'customer_id = :id',
                'params'	=> array(
                    ':id'	=> $agent_id,
                ),
            );
            $params = array_filter($params, "strlen"); //removes null and blank array fields from array
            $cs_param['is_contract_approved'] = NULL;
            if(!empty($contract_status) && $contract_status=="Approved")
            {
                $cs_param['is_contract_approved'] = $contract_status;
                $params['status'] = 'Pending Contract';
                $new_update_details['status'] = $params['status'];
            }
            $contract_activity['customer'] = $pdo->update('customer', $params, $upd_where,true);
            $contract_activity['customer_settings1'] = $pdo->update('customer_settings',$cs_param,$csupd_where,true);
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
                        ':id' => $agent_id,
                    ),
                );
                $cs_params = array_filter($cs_params, "strlen"); //removes null and blank array fields from array
                $contract_activity['customer_settings2'] = $pdo->update('customer_settings', $cs_params, $upd_where,true);
            }
            //w9 pdf
            $w9_doc = $w9_form_business;
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
                        ':id' => $agent_id,
                    ),
                );
                $update = array_filter($update, "strlen"); //removes null and blank array fields from array
                $contract_activity['customer_settings3'] = $pdo->update('customer_settings', $update, $upd_where,true);
            }

            //Agent document
            {
                $selADoc = "SELECT id FROM agent_document WHERE agent_id=:agent_id";
                $whrADoc = array(":agent_id" => $agent_id);
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
                                ':id' => $agent_id,
                            ),
                        );
                        $updateparams = array_filter($updateparams, "strlen"); //removes null and blank array fields from array
                        $contract_activity['agent_document'] = $pdo->update('agent_document', $updateparams, $upd_where,true);
                    } else {
                        $insparams = array(
                            'agent_id' => $agent_id,
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
            }

            if (!in_array($agent_res['agent_coded_level'], array("LOA"))) {
                $selDirect = "SELECT id from direct_deposit_account WHERE customer_id=:customer_id";
                $whrDirect = array(":customer_id" => $agent_id);
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
                            ':customer_id' => $agent_id,
                        ),
                    );
                    $updateparams = array_filter($updateparams, "strlen"); //removes null and blank array fields from array
                    $getActivity = true;
                    $contract_activity['direct_deposit_account'] = $pdo->update('direct_deposit_account', $updateparams, $upd_where,$getActivity);
                } else {
                    $insparams = array(
                        'customer_id' => $agent_id,
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

            //e-o entry
            if(!empty($e_o_document)){
                $tmp_v1 = explode(".", $e_o_document['name']);
                $extension = end($tmp_v1);
                $doc_tmp_name = $e_o_document['tmp_name'];
                $e_o_coverage_filename = 'agent_doc_' . round(microtime(true)) . '.' . $extension;
                $selADoc = "SELECT e_o_document FROM agent_document WHERE agent_id=:agent_id";
                $whrADoc = array(":agent_id" => $agent_id);
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
                            ':id' => $agent_id,
                        ),
                    );
                    $updateparams = array_filter($updateparams, "strlen"); //removes null and blank array fields from array
                    $pdo->update('agent_document', $updateparams, $upd_where);
                    $contract_activity['agent_document'] = 'Agent Error and Ommissions Insurance (E&O) Detail Document updated.';
                } else {
                    $insparams = array(
                        'agent_id' => $agent_id,
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
            //code for license goes here!

            $contract_activity['agent_license'] = add_update_license($hdn_license,$license_state,$license_number,$license_active,$license_type,$license_auth,$license_expiry,$license_not_expn);
            //lead update
            {
                if ($agent_id > 0 && $account_type == "Business" ) {
                    $leads_sql = "SELECT id FROM leads where customer_id = :customer_id";
                    $leads_where = array(":customer_id" => makeSafe($agent_id));
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
                                ':customer_id' => $agent_id,
                            ),
                        );
                        $leads_update_params = array_filter($leads_update_params, "strlen"); //removes null and blank array fields from array
                        $pdo->update("leads", $leads_update_params, $leads_update_where);
                    }
                }
            }
        }

        if(!empty($contract_status) && $contract_status=="Approved")
        {
            $aparams = array();
            $aparams['fname'] = $fname;
            $aparams['link'] = $AGENT_HOST;

            $smart_tags = get_user_smart_tags($agent_id,'agent');
            
            if($smart_tags){
                $aparams = array_merge($aparams,$smart_tags);
            }

            // Agent - Enrollment Approved
            $trigger_id = 19;
            $description = array();
            if(!empty($_SESSION['admin']['id'])){
                $description['ac_message'] = array(
                    'ac_red_1'=>array(
                        'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
                        'title'=>$_SESSION['admin']['display_id'],
                    ),
                    'ac_message_1' =>'Approved Documentation On Agent '.$fname.' '.$lname.' (',
                    'ac_red_2'=>array(
                        'href'=> $ADMIN_HOST.'/agent_detail_v1.php?id='.md5($agent_id),
                        'title'=> $agent_res['rep_id'],
                    ),
                    'ac_message_2' =>')<br>',
                );
            }
            $description = json_encode($description);

            activity_feed(3, $agent_id, 'Agent', $agent_id, 'customer', 'Agent Documentation Approved', $fname, $lname, $description);
            trigger_mail($trigger_id, $aparams,  $agent_res['email']);
            setNotifySuccess("Documentation Status Changed Successfully");
        }
        if(!empty($contract_activity)){
            agent_profile_activity($contract_activity);
        }

        $response['status'] = 'account_approved';
        $response['step'] = 'second';
    }
    if (count($validate->getErrors()) > 0) {
        $response['status'] = "fail";
        $response['errors'] = $validate->getErrors();
    }

    function add_update_license($hdn_license,$license_state,$license_number,$license_active,$license_type,$license_auth,$license_expiry,$license_not_exp,$ajax=''){
        $agent_doc_id ='';
        global $pdo,$agent_id;
    
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
            $whrADoc = array(":agent_id" => $agent_id, ":id" => $h_id);
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
                        ':agent_id' => $agent_id,
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
                    'agent_id' => $agent_id,
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
        // return $agent_doc_id;
        return $agent_licence_activity;
    }

    function agent_profile_activity($contract_activity){
        global $pdo,$ADMIN_HOST,$new_update_details,$agent_id;
        $msg ="Approved Contract on review Documentation for Agent ";
        $entity_action='Agent Documentation Approved';
    
        $description = array();
        $flg = "true";
        $agent_name = $pdo->selectOne("SELECT id, CONCAT(fname,' ',lname) as name ,rep_id from customer where id=:id",array(":id"=>$agent_id));
        
        if(!empty($_SESSION['admin']['id'])){
            $description['ac_message'] = array(
                'ac_red_1'=>array(
                    'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
                    'title'=>$_SESSION['admin']['display_id'],
                ),
                'ac_message_1' =>$msg.$agent_name['name'].' (',
                'ac_red_2'=>array(
                    'href'=> $ADMIN_HOST.'/agent_detail_v1.php?id='.md5($agent_name['id']),
                    'title'=> $agent_name['rep_id'],
                ),
                'ac_message_2' =>')<br>',
            );
        }else{
            $description['ac_message'] = array(
                'ac_message_1' =>$agent_name['name'].' (',
                'ac_red_2'=>array(
                    'href'=> $ADMIN_HOST.'/agent_detail_v1.php?id='.md5($agent_name['id']),
                    'title'=> $agent_name['rep_id'],
                ),
                'ac_message_2' =>')  Updated Contract Remaining details.',
                );
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
                                $val1='';// !empty($val) ? ' Updated To '.$val."<br>" : !empty($new_update_details[$key2]) ? ' Updated To '.$new_update_details[$key2] : '';
                                if(!empty($val)){
                                    $val1 = "Updated To ".$val."<br>" ;
                                }else if(!empty($new_update_details[$key2]))
                                {
                                    $val1 = "Updated To ".$val."<br>" ;
                                }
                                
                                if(!empty($val)){
                                    $description['key_value']['desc_arr'][$key2] = $val1;	
                                }
                            }
                            
                            $flg = "false";
                    }else{
                        $description['description__2'][] = ucwords(str_replace('_',' ',$val));
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
        $desc=json_encode($description);
        
        $user_id = $user_type = $user_name="";
        if(!empty($_SESSION['admin']['id'])){
            $user_id = $_SESSION['admin']['id'];
            $user_type ='Admin';
            $user_name = $_SESSION['admin']['name'];
        }
        if($flg == "false"){
            activity_feed(3,$user_id, $user_type , $agent_name['id'], 'Agent', $entity_action,$user_name,"",$desc);
        }
        
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    dbConnectionClose();
    exit();

?>