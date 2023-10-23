<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$validate = new Validation();
$response = array();

$is_address_ajaxed = checkIsset($_POST['is_address_ajaxed']) ;
$payment_type = !empty($_POST['payment_type']) ? $_POST['payment_type'] : '';
$payment_mode = !empty($_POST['payment_mode']) ? $_POST['payment_mode'] : '';
if($is_address_ajaxed && $payment_mode=="ACH_CC" && $payment_type == "CC"){
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

$group_id = $_POST['group_id'];
$billing_id = $_POST['billing_id'];


$group_update_activity = array();

$company_id = isset($_POST['company_id']) ? $_POST['company_id'] : '';
$payment_mode = !empty($_POST['payment_mode']) ? $_POST['payment_mode'] : '';
$billing_profile_id = !empty($_POST['billing_profile_id']) ? $_POST['billing_profile_id'] : '';
$is_display_payment = !empty($_POST['is_display_payment']) ? $_POST['is_display_payment'] : 'N';


$payment_type = !empty($_POST['payment_type']) ? $_POST['payment_type'] : '';    
$account_type = !empty($_POST['account_type']) ? $_POST['account_type'] : '';
$bankname = checkIsset($_POST['bankname']);
$bank_rounting_number = checkIsset($_POST['bank_rounting_number']);
$bank_account_number = checkIsset($_POST['bank_account_number']);

$entered_routing_number = checkIsset($_POST['entered_routing_number']);
$entered_account_number = checkIsset($_POST['entered_account_number']);


$ach_name = checkIsset($_POST['ach_name']);
$name_on_card = checkIsset($_POST['name_on_card']);
$expiration = !empty($_POST['expiration']) ? $_POST['expiration'] : '';
$cvv = checkIsset($_POST['cvv']);
$bill_address = checkIsset($_POST['bill_address']);
$bill_address_2 = checkIsset($_POST['bill_address_2']);
$bill_city = checkIsset($_POST['bill_city']);
$bill_state = checkIsset($_POST['bill_state']);
$bill_zip = checkIsset($_POST['bill_zip']);
$is_valid_address = !empty($_POST['is_valid_address']) ? $_POST['is_valid_address'] : '';

$card_type = !empty($_POST['card_type']) ? $_POST['card_type'] : '';
$card_number = checkIsset($_POST['card_number']);
$full_card_number = checkIsset($_POST['full_card_number']);
$expiry_month = '';
$expiry_year = '';
$REAL_IP_ADDRESS = get_real_ipaddress();
if(!empty($expiration)){
    $expirtation_details = explode("/", $expiration);
    $expiry_month = $expirtation_details[0];
    $expiry_year = $expirtation_details[1];
}


$validate->string(array('required' => true, 'field' => 'company_id', 'value' => $company_id), array('required' => 'Select Company'));
$validate->string(array('required' => true, 'field' => 'payment_mode', 'value' => $payment_mode), array('required' => 'Select Any Option'));

if($payment_mode=="ACH_CC"){
    if($is_display_payment == 'N'){
        $validate->setError("billing_profile_id","Select/Add Payment Method");
    }
    $validate->string(array('required' => true, 'field' => 'payment_type', 'value' => $payment_type), array('required' => 'Select Payment Type'));

    if($payment_type=='CC'){
        $validate->string(array('required' => true, 'field' => 'name_on_card', 'value' => $name_on_card), array('required' => 'Name is required'));

        if (!$validate->getError("name_on_card") && !ctype_alnum(str_replace(' ','',$name_on_card))) {
            $validate->setError("name_on_card","Enter Valid Name");
        }
        $validate->string(array('required' => true, 'field' => 'card_type', 'value' => $card_type), array('required' => 'Select any card'));
        
        if(empty($card_number) && !empty($full_card_number)) {
            $card_number = $full_card_number;
        }
        
        $validate->digit(array('required' => true, 'field' => 'card_number', 'value' => $card_number,"max"=>$MAX_CARD_NUMBER,"min"=>$MIN_CARD_NUMBER), array('required' => 'Card Number is required', "invalid" => "Please enter valid card number"));

        if(!$validate->getError("card_number") && !is_valid_luhn($card_number,$card_type)){
            $validate->setError("card_number","Enter valid Credit Card Number");
        }
        
        $validate->string(array('required' => true, 'field' => 'expiration', 'value' => $expiration), array('required' => 'Please select expiration month and year'));
        
        $cvv_required = "N";
        
        $sqlProcessor = "SELECT require_cvv FROM payment_master where is_deleted = 'N' AND type='Global' AND status IN ('Active') AND is_cc_accepted = 'Y' AND is_default_for_cc = 'Y'";
        $resProcessor = $pdo->selectOne($sqlProcessor);
        if(!empty($resProcessor)){
            $cvv_required = $resProcessor['require_cvv'];
        }
        if($cvv_required == 'Y' || !empty($cvv)){
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
        $validate->digit(array('required' => true, 'field' => 'bill_zip', 'value' => $bill_zip,"min"=>5,"max"=>5), array('required' => 'Zip is required'));

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

        if (!$validate->getError("ach_name") && !ctype_alnum(str_replace(' ','',$ach_name))) {
            $validate->setError("ach_name","Enter Valid Name");
        }
        $validate->string(array('required' => true, 'field' => 'account_type', 'value' => $account_type), array('required' => 'Select Account Type'));
        $validate->string(array('required' => true, 'field' => 'bankname', 'value' => $bankname,'max'=>50), array('required' => 'Bank name is required'));
        
        if(empty($entered_account_number) || !empty($bank_account_number)){
            $validate->digit(array('required' => true, 'field' => 'bank_account_number', 'value' => $bank_account_number,'min'=>$MIN_ACCOUNT_NUMBER,'max'=>$MAX_ACCOUNT_NUMBER), array('required' => 'Account number is required', 'invalid' => "Enter valid Account number"));
        }
        if(empty($entered_routing_number) || !empty($bank_rounting_number)){
            $validate->digit(array('required' => true, 'field' => 'bank_rounting_number', 'value' => $bank_rounting_number,"min" => 9,"max" => 9), array('required' => 'Routing number is required', 'invalid' => "Enter valid Routing number"));
            if (!$validate->getError("bank_rounting_number")) {
                if (checkRoutingNumber($bank_rounting_number) == false) {
                    $validate->setError("bank_rounting_number", "Enter valid routing number");
                }
            }
        }
    }
}else{
    $payment_type = $payment_mode;
}

if($validate->isValid()){

    $group_bill_sql = "SELECT * FROM customer_billing_profile where id=:billing_id";
    $group_bill_row = $pdo->selectOne($group_bill_sql, array(":billing_id" => $billing_id));
    
    $bill_params = array(
        'company_id'=>$company_id,
        'customer_id'=>$group_id,
        'country_id' => '231',
        'country' => "United States",
        'listbill_enroll' => 'Y',
        'ip_address' => !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'],
        'is_valid_address'=>$is_valid_address,
    );
    $bill_params['payment_mode'] = $payment_type;
    
    if ($payment_type == "ACH") {
        if(empty($bank_account_number)){
            $bank_account_number = $entered_account_number;
        }
        if(empty($bank_rounting_number)){
            $bank_rounting_number = $entered_routing_number;
        }
        $bill_params['fname'] = $ach_name;
        $bill_params['ach_account_number'] = "msqlfunc_AES_ENCRYPT('" . $bank_account_number . "','" . $CREDIT_CARD_ENC_KEY . "')";
        $bill_params['ach_routing_number'] = "msqlfunc_AES_ENCRYPT('" . $bank_rounting_number . "','" . $CREDIT_CARD_ENC_KEY . "')";
        $bill_params['ach_account_type'] = $account_type;
        $bill_params['bankname'] = $bankname;
        $bill_params['last_cc_ach_no'] = makeSafe(substr($bank_account_number, -4));

        
        $message2 = '<br> IN ACH (*'.$bill_params['last_cc_ach_no'].')';
    } else if ($payment_type == "CC") {
        $bill_params['fname'] = $name_on_card;
        
        $bill_params['state'] = $bill_state;
        $bill_params['city'] = $bill_city;
        $bill_params['zip'] = $bill_zip;
        $bill_params['address'] = $bill_address;
        $bill_params['address2'] = $bill_address_2;
        $bill_params['cvv_no'] = $cvv;

        $bill_params['card_no'] = makeSafe(substr($card_number, -4));
        $bill_params['card_no_full'] = "msqlfunc_AES_ENCRYPT('" . $card_number . "','" . $CREDIT_CARD_ENC_KEY . "')";
        $bill_params['card_type'] = makeSafe($card_type);
        $bill_params['expiry_month'] = makeSafe($expiry_month);
        $bill_params['expiry_year'] = makeSafe($expiry_year);
        $bill_params['last_cc_ach_no'] = makeSafe(substr($card_number, -4));
        $message2 = '<br> IN CC '.$bill_params['card_type'].' (*'.$bill_params['last_cc_ach_no'].')';
    }else if($payment_type=="Check"){
        $message2 = '<br> IN Check';
    }

    $billing_profile_update = array();
    if (!empty($group_bill_row)) {
        $billing_profile_update = $pdo->update("customer_billing_profile", $bill_params, array("clause" => "id=:billing_id", "params" => array(":billing_id" => $billing_id)),true);
        $response['msg']="Billing Profile Updated Successfully";
        $message = ' updated billing profile For Group ';
    }else{
        $bill_params['is_default']='N';
        $pdo->insert("customer_billing_profile", $bill_params);
        $response['msg']="Billing Profile Added Successfully";
        $message = ' Added new billing profile For Group ';
    }
    


    $crep_id = getname('customer',$group_id,'rep_id');
    $activityFeedDesc['ac_message'] =array(
        'ac_red_1'=>array(
            'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
            'title'=>$_SESSION['admin']['display_id'],
        ),
        'ac_message_1' =>$message,
        'ac_red_2'=>array(
            'href'=>$ADMIN_HOST.'/groups_details.php?id='.md5($group_id),
            'title'=>$crep_id,
        ),
        'ac_message_2' =>$message2,
    ); 

    if(!empty($billing_profile_update)){
        foreach($billing_profile_update as $key => $val){
            if($key == "is_default"){
                if(!empty($group_bill_row) && $bill_params[$key] == $group_bill_row['is_default']) {
                    continue;
                }

                $val = $val == 'Y' ? ' Selected ' : " Unselected ";
                $bill_params[$key] = $bill_params[$key] == 'Y' ? ' Selected ' : " Unselected ";
            }
            $activityFeedDesc['key_value']['desc_arr'][$key] = ' From ' . $val . ' to ' . $bill_params[$key];
        }
    }

    activity_feed(3,$_SESSION['admin']['id'], 'Admin', $group_id, 'Group', 'Group Billing Profile',$_SESSION['admin']['name'],"",json_encode($activityFeedDesc));
    $response['status'] = "success";   
}

if(count($validate->getErrors()) > 0){
    $response['status'] = "errors";   
    $response['errors'] = $validate->getErrors();   
}
header('Content-Type: application/json');
echo json_encode($response);
dbConnectionClose();
exit();

?>