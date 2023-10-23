<?php
include_once __DIR__ . '/includes/connect.php';

$is_address_ajaxed = checkIsset($_POST['is_address_ajaxed']) ;
$payment_mode = checkIsset($_POST['payment_mode']);

$REAL_IP_ADDRESS = get_real_ipaddress();
if($is_address_ajaxed && $payment_mode == 'CC'){

    $response = array("status"=>'success');
    $address = $_POST['bill_address'];
    $address_2 = checkIsset($_POST['bill_address_2']);
    $city = $_POST['bill_city'];
    $state = checkIsset($_POST['bill_state']);
    $bill_zip = $_POST['bill_zip'];
    $old_address = $_POST['old_bill_address'];
    $old_zip = $_POST['old_bill_zip'];

    $validate = new Validation();

    $validate->digit(array('required' => true, 'field' => 'bill_zip', 'value' => $bill_zip,'min'=> 5,'max'=>5 ), array('required' => 'Zip Code is required'));

    $validate->string(array('required' => true, 'field' => 'bill_address', 'value' => $address), array('required' => 'Address is required'));
    if($validate->isValid()){
        include_once __DIR__.'/includes/function.class.php';
        $function_list = new functionsList();
        $zipAddress = $function_list->uspsCityVerification($bill_zip);

        if($old_address != $address || $bill_zip!=$old_zip ||  $getStateNameByShortName[$zipAddress['state']] !=$state){
            
            if($zipAddress['status'] =='success'){
                $response['city'] = $zipAddress['city'];
                $response['state'] = $getStateNameByShortName[$zipAddress['state']];
                $response['zip_response_status']='success';

                $tmpAdd1=$address;
                $tmpAdd2=!empty($address_2) ? $address_2 : '#';
                $address_response = $function_list->uspsAddressVerification($tmpAdd1,$tmpAdd2,$zipAddress['city'],$getStateNameByShortName[$zipAddress['state']],$bill_zip);
                
                if(!empty($address_response)){
                    if($address_response['status']=='success'){
                        $response['address'] = $address_response['address'];
                        $response['address2'] = $address_response['address2'];
                        $response['city'] = $address_response['city'];
                        $response['state'] = $getStateNameByShortName[$address_response['state']];
                        $response['enteredAddress']= $address .' '.$address_2 .'</br>'.$address_response['city'].', '.$address_response['state'] . ' '.$bill_zip;
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

$location = isset($_REQUEST['location'])?$_REQUEST['location']:'admin';
$is_delete = isset($_POST['is_delete']) ? $_POST['is_delete'] : '';
$is_ajaxed_delete = isset($_POST['is_ajaxed_delete']) ? $_POST['is_ajaxed_delete'] : '';
$action = isset($_POST['action']) ? $_POST['action'] : '';
if($is_delete && $is_ajaxed_delete  && $action=='Delete'){
    $bill_id = $_POST['bill_id'];
    $customer_id = $_POST['id'];
    $response = array();
    $bill_rows = $pdo->selectOne("SELECT id,last_cc_ach_no,payment_mode,card_type from customer_billing_profile where md5(id) =:id and md5(customer_id) = :customer_id and is_deleted='N' and is_default='N'",array(":id"=>$bill_id,":customer_id"=>$customer_id));
    if(!empty($bill_rows['id'])){
        
        $upd_where = array(
            "clause" => " id = :id",
            "params" => array(
                ":id" => $bill_rows['id'],
            )
        );
        $pdo->update('customer_billing_profile',array("is_deleted"=>'Y'),$upd_where);
        $response['status'] = 'success';

        $rows = $pdo->selectOne("SELECT id,concat(fname,' ',lname) as name,rep_id from customer where md5(id) =:id ",array(":id"=>$customer_id));

        $bill_pro  = $bill_rows['payment_mode'] == 'ACH' ? 'ACH *'.$bill_rows['last_cc_ach_no'] : $bill_rows['card_type'].'*'.$bill_rows['last_cc_ach_no'];
        if($location == "admin") {
            $ac_desc['ac_message'] =array(
                'ac_red_1'=>array(
                    'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
                    'title'=>$_SESSION['admin']['display_id'],
                ),
                'ac_message_1' =>' Deleted Billing Profile on member '.$rows['name'].'(',
                'ac_red_2'=>array(
                    'href'=> $ADMIN_HOST.'/members_details.php?id='.md5($rows['id']),
                    'title'=> $rows['rep_id'],
                ),
                'ac_message_2'=>') <br> Billing Profile : '.$bill_pro,
            );
            activity_feed(3,$_SESSION['admin']['id'], 'Admin' , $rows['id'], 'customer','Admin Deleted Billing Profile',$_SESSION['admin']['name'],"",json_encode($ac_desc));
        
        } elseif($location == "agent") {
            $ac_desc['ac_message'] =array(
                'ac_red_1'=>array(
                    'href'=> 'agent_detail_v1.php?id='.md5($_SESSION['agents']['id']),
                    'title'=> $_SESSION['agents']['rep_id'],
                ),
                'ac_message_1' =>' Deleted Billing Profile on member '.$rows['name'].'(',
                'ac_red_2'=>array(
                    'href'=> $ADMIN_HOST.'/members_details.php?id='.md5($rows['id']),
                    'title'=> $rows['rep_id'],
                ),
                'ac_message_2'=>') <br> Billing Profile : '.$bill_pro,
            );
            activity_feed(3,$_SESSION['agents']['id'],'Agent',$rows['id'],'customer','Agent Deleted Billing Profile',"","",json_encode($ac_desc));

        } elseif($location == "group") {
            $ac_desc['ac_message'] =array(
                'ac_red_1'=>array(
                    'href'=>$ADMIN_HOST.'/groups_details.php?id='.md5($_SESSION['groups']['id']),
                    'title'=>$_SESSION['groups']['rep_id'],
                ),
                'ac_message_1' =>' Deleted Billing Profile on member '.$rows['name'].'(',
                'ac_red_2'=>array(
                    'href'=> $ADMIN_HOST.'/members_details.php?id='.md5($rows['id']),
                    'title'=> $rows['rep_id'],
                ),
                'ac_message_2'=>') <br> Billing Profile : '.$bill_pro,
            );
            activity_feed(3,$_SESSION['groups']['id'], 'Group' , $rows['id'], 'customer','Group Deleted Billing Profile',"","",json_encode($ac_desc));
        }
    }else{
        $response['status'] = 'fail';
    }
    header("Content-type: application/json");
    echo json_encode($response);
    exit;
}
$is_ajaxed = isset($_POST['is_billing_ajaxed']) ? $_POST['is_billing_ajaxed'] : '';
$customer_id = isset($_POST['customer_id']) ? $_POST['customer_id'] : '' ;
if(!empty($customer_id) && $is_ajaxed){

    $validate = new Validation();

    $action = $_POST['action'];
    $is_default = !empty($_POST['is_default']) && $_POST['is_default'] == 'on'  ? 'Y' : 'N';
    //Billing Information Credit Card
    $name_on_card = checkIsset($_POST['name_on_card']);
    $card_number = checkIsset($_POST['card_number']);
    $full_card_number = checkIsset($_POST['full_card_number']);
    $card_type = checkIsset($_POST['card_type']);
    $expiration = checkIsset($_POST['expiration']);
    $cvv = checkIsset($_POST['cvv']);

    //Billing Information Bank Draft
    $ach_name = checkIsset($_POST['ach_name']);
    $bank_name = checkIsset($_POST['bank_name']);
    $account_type = checkIsset($_POST['account_type']);
    $routing_number = checkIsset($_POST['routing_number']);
    $entered_routing_number = checkIsset($_POST['entered_routing_number']);
    $account_number = checkIsset($_POST['account_number']);
    $entered_account_number = checkIsset($_POST['entered_account_number']);
    // $confirm_account_number = checkIsset($_POST['confirm_account_number']);

    //Billing Address Information
    $bill_address = checkIsset($_POST['bill_address']);
    $bill_address_2 = checkIsset($_POST['bill_address_2']);
    $bill_city = checkIsset($_POST['bill_city']);
    $bill_state = checkIsset($_POST['bill_state']);
    $bill_zip = checkIsset($_POST['bill_zip']);

    $bill_id = checkIsset($_POST['bill_id']);
    if(!empty($bill_id)) {
        $billingInfo = $pdo->selectOne("SELECT * from customer_billing_profile where md5(id)=:id and is_deleted='N'",array(":id"=>$bill_id));    
    }
    

     //payment data validation 
        if(empty($payment_mode)){
            $validate->setError("payment_mode","Please select any Payment Method");
        }

        if($payment_mode == 'CC'){
            $validate->string(array('required' => true, 'field' => 'name_on_card', 'value' => $name_on_card), array('required' => 'Full Name is required'));
            if (!$validate->getError("name_on_card") && !ctype_alnum(str_replace(" ","",$name_on_card))) {
                $validate->setError("name_on_card","Enter Valid Name");
            }
            
            if(empty($card_number) && !empty($full_card_number)) {
                $card_number = $full_card_number;
            }

            $validate->digit(array('required' => true, 'field' => 'card_number', 'value' => $card_number,"max"=>$MAX_CARD_NUMBER,"min"=>$MIN_CARD_NUMBER), array('required' => 'Card number is required', 'invalid' => "Enter valid Card Number"));

            if(!$validate->getError("card_number") && !is_valid_luhn($card_number,$card_type)){
                $validate->setError("card_number","Enter valid Credit Card Number");
            }
            
            $validate->string(array('required' => true, 'field' => 'card_type', 'value' => $card_type), array('required' => 'Please select any card'));
            $validate->string(array('required' => true, 'field' => 'expiration', 'value' => $expiration), array('required' => 'Please select expiration month and year'));
            // if($require_cvv == 'yes'){
                $validate->string(array('required' => true, 'field' => 'cvv', 'value' => str_replace('_','',$cvv)), array('required' => 'CVV is required'));	
            // }  
            
            if(!$validate->getError("cvv") && !cvv_type_pair($cvv,$card_type)){
                $validate->setError("cvv","Invalid CVV Number");
            }

            $validate->string(array('required' => true, 'field' => 'bill_address', 'value' => $bill_address), array('required' => 'Address is required'));
            if(!empty($bill_address_2) && preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/',$bill_address_2)) {
                $validate->setError('bill_address_2','Special character not allowed');
            }
            $validate->string(array('required' => true, 'field' => 'bill_city', 'value' => $bill_city), array('required' => 'City is required'));
            $validate->string(array('required' => true, 'field' => 'bill_state', 'value' => $bill_state), array('required' => 'State is required'));
            $validate->string(array('required' => true, 'field' => 'bill_zip', 'value' => $bill_zip,"min"=>5,"max"=>5), array('required' => 'Zip is required'));
            if (!$validate->getError('bill_zip')){
                include_once __DIR__.'/includes/function.class.php';
                $function_list = new functionsList();
                $zipAddress = $function_list->uspsCityVerification($bill_zip);
                if($zipAddress['status'] !='success'){
                    $validate->setError("bill_zip",$zipAddress['error_message']);
                }
            }
        }else{
            $validate->string(array('required' => true, 'field' => 'bank_name', 'value' => $bank_name), array('required' => 'Bank Name is required'));
            $validate->string(array('required' => true, 'field' => 'ach_name', 'value' => $ach_name), array('required' => 'Full Name is required'));
            if (!$validate->getError("ach_name") && !ctype_alnum(str_replace(" ","",$ach_name))) {
                $validate->setError("ach_name","Enter Valid Name");
            }
            // if(empty($full_card_number)){
                $validate->string(array('required' => true, 'field' => 'account_type', 'value' => $account_type), array('required' => 'Account Type is required'));
            // }
            if(empty($entered_account_number) || !empty($account_number)){

                $validate->digit(array('required' => true, 'field' => 'account_number', 'value' => $account_number,'min'=>$MIN_ACCOUNT_NUMBER,'max'=>$MAX_ACCOUNT_NUMBER), array('required' => 'Account number is required', 'invalid' => "Enter valid Account number"));

                // $validate->string(array('required' => true, 'field' => 'confirm_account_number', 'value' => $confirm_account_number), array('required' => 'Confirm Account number is required'));
            
                // if (!$validate->getError('confirm_account_number')) {
                //     if ($confirm_account_number != $account_number) {
                //         $validate->setError('confirm_account_number', "Enter same Account Number");
                //     }
                // }
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
        
    if($validate->isValid()){
        $customer_id = getname('customer',$customer_id,'id','md5(id)');
        $message2 = '';
        $billParams = array();
        if($payment_mode == 'CC'){
            $expiry_month = substr($expiration,0,2);
            $expiry_year = substr($expiration,-2);
            $billParams = array(
                'customer_id' => $customer_id,
                'fname' => !empty($name_on_card) ? makeSafe($name_on_card) : makeSafe($bill_fname),
                // 'lname' => makeSafe($bill_lname),
                // 'email' => makeSafe($primary_email),
                'country_id' => 231,
                'country' => 'United States',
                'state' => makeSafe($bill_state),
                'city' => makeSafe($bill_city),
                'zip' => makeSafe($bill_zip),
                'address' => makeSafe($bill_address),
                'address2' => makeSafe($bill_address_2),
                'card_type' => makeSafe($card_type),
                'expiry_month' => makeSafe($expiry_month),
                'expiry_year' => makeSafe($expiry_year),
                'payment_mode' => 'CC',
            );

            $billParams['card_no'] = makeSafe(substr($card_number, -4));
            $billParams['last_cc_ach_no'] = makeSafe(substr($card_number, -4));
            $billParams['card_no_full'] = "msqlfunc_AES_ENCRYPT('" . $card_number . "','" . $CREDIT_CARD_ENC_KEY . "')";

            if($cvv!=''){
				$billParams['cvv_no'] = makeSafe($cvv);
			}
			$billParams['ip_address'] = !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'];

            $message2 = '<br> IN CC '.$billParams['card_type'].' (*'.$billParams['last_cc_ach_no'].')';
        }else{
            $billParams = array(
				'customer_id' => $customer_id,
				'fname' => makeSafe($ach_name),
				// 'email' => makeSafe($primary_email),
				'country_id' => 231,
				'country' => 'United States',
				// 'state' => makeSafe($bill_state),
				// 'city' => makeSafe($bill_city),
				// 'zip' => makeSafe($bill_zip),
				// 'address' => makeSafe($bill_address),
				'payment_mode' => 'ACH',
				'ach_account_type' => $account_type,
				'bankname' => $bank_name,
			);

			if ($account_number != "") {
				$billParams['last_cc_ach_no'] = makeSafe(substr($account_number, -4));
				$billParams['ach_account_number'] = "msqlfunc_AES_ENCRYPT('" . $account_number . "','" . $CREDIT_CARD_ENC_KEY . "')";
			}else{
				$billParams['last_cc_ach_no'] = makeSafe(substr($entered_account_number, -4));
				$billParams['ach_account_number'] = "msqlfunc_AES_ENCRYPT('" . $entered_account_number . "','" . $CREDIT_CARD_ENC_KEY . "')";
			}
			if ($routing_number != "") {
				$billParams['ach_routing_number'] = "msqlfunc_AES_ENCRYPT('" . $routing_number . "','" . $CREDIT_CARD_ENC_KEY . "')";
			}else{
				$billParams['ach_routing_number'] = "msqlfunc_AES_ENCRYPT('" . $entered_routing_number . "','" . $CREDIT_CARD_ENC_KEY . "')";
            }

			$billParams['ip_address'] = !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'];

            $message2 = '<br> IN ACH (*'.$billParams['last_cc_ach_no'].')';
        }

        if($is_default == "Y") {
            $billParams['is_default'] = $is_default;
        }
        if($is_default == 'Y' && !empty($customer_id)){
            $pdo->update('customer_billing_profile',array('is_default'=>'N'),array("clause" => "customer_id = :customer_id", "params" => array(":customer_id" => $customer_id)));
        }
        $customer_billing_profile_update = array();
        if($action == 'Edit' && !empty($bill_id)){
            unset($billParams['customer_id']);
            if(!empty($billingInfo['id'])){
                $customer_billing_profile_update = $pdo->update("customer_billing_profile", $billParams, array("clause" => "customer_id = :customer_id and id = :id ", "params" => array(":customer_id" => $customer_id,':id'=>$billingInfo['id'])),true);
            }
            $response['msg'] = 'Billing Profile Updated Successfully!';
        }else{
            $billParams['created_at'] = 'msqlfunc_NOW()';
            $pdo->insert('customer_billing_profile',$billParams);
            $response['msg'] = 'New Billing Profile Addedd Successfully!';
        }

        $message =  $update_ac = '';
        if($action =='Edit'){
            $message = ' updated billing profile For Member ';
        }else{
            $message = ' Added new billing profile For Member ';
        }

        $crep_id = getname('customer',$customer_id,'rep_id');

        if($location == "admin") {
            $activityFeedDesc['ac_message'] =array(
                'ac_red_1'=>array(
                    'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
                    'title'=>$_SESSION['admin']['display_id'],
                ),
                'ac_message_1' =>$message,
                'ac_red_2'=>array(
                    'href'=>$ADMIN_HOST.'/members_details.php?id='.md5($customer_id),
                    'title'=>$crep_id,
                ),
                'ac_message_2' =>$message2,
            ); 

            if(!empty($customer_billing_profile_update)){
                foreach($customer_billing_profile_update as $key => $val){
                    if($key == "is_default"){
                        if(!empty($billingInfo) && $billParams[$key] == $billingInfo['is_default']) {
                            continue;
                        }

                        $val = $val == 'Y' ? ' Selected ' : " Unselected ";
                        $billParams[$key] = $billParams[$key] == 'Y' ? ' Selected ' : " Unselected ";
                    }
                    $activityFeedDesc['key_value']['desc_arr'][$key] = ' From ' . $val . ' to ' . $billParams[$key];
                }
            }

            activity_feed(3,$_SESSION['admin']['id'], 'Admin', $customer_id, 'Customer', 'Member Billing Profile',$_SESSION['admin']['name'],"",json_encode($activityFeedDesc));
        
        } elseif($location == "agent") {
            $activityFeedDesc['ac_message'] =array(
                'ac_red_1'=>array(
                    'href'=> 'agent_detail_v1.php?id='.md5($_SESSION['agents']['id']),
                    'title'=> $_SESSION['agents']['rep_id'],
                ),
                'ac_message_1' =>$message,
                'ac_red_2'=>array(
                    'href'=>$ADMIN_HOST.'/members_details.php?id='.md5($customer_id),
                    'title'=>$crep_id,
                ),
                'ac_message_2' =>$message2,
            );
            if(!empty($customer_billing_profile_update)){
                foreach($customer_billing_profile_update as $key => $val){
                    if($key == "is_default"){
                        if(!empty($billingInfo) && $billParams[$key] == $billingInfo['is_default']) {
                            continue;
                        }
                        $val = $val == 'Y' ? ' Selected ' : " Unselected ";
                        $billParams[$key] = $billParams[$key] == 'Y' ? ' Selected ' : " Unselected ";
                    }
                    $activityFeedDesc['key_value']['desc_arr'][$key] = ' From ' . $val . ' to ' . $billParams[$key];
                }
            }
            activity_feed(3,$_SESSION['agents']['id'], 'Agent', $customer_id, 'Customer', 'Member Billing Profile',"","",json_encode($activityFeedDesc));

        } elseif($location == "group") {
            $activityFeedDesc['ac_message'] =array(
                'ac_red_1'=>array(
                    'href'=>$ADMIN_HOST.'/groups_details.php?id='.md5($_SESSION['groups']['id']),
                    'title'=>$_SESSION['groups']['rep_id'],
                ),
                'ac_message_1' =>$message,
                'ac_red_2'=>array(
                    'href'=>$ADMIN_HOST.'/members_details.php?id='.md5($customer_id),
                    'title'=>$crep_id,
                ),
                'ac_message_2' =>$message2,
            ); 

            if(!empty($customer_billing_profile_update)){
                foreach($customer_billing_profile_update as $key => $val){
                    if($key == "is_default"){
                        if(!empty($billingInfo) && $billParams[$key] == $billingInfo['is_default']) {
                            continue;
                        }

                        $val = $val == 'Y' ? ' Selected ' : " Unselected ";
                        $billParams[$key] = $billParams[$key] == 'Y' ? ' Selected ' : " Unselected ";
                    }
                    $activityFeedDesc['key_value']['desc_arr'][$key] = ' From ' . $val . ' to ' . $billParams[$key];
                }
            }

            activity_feed(3,$_SESSION['groups']['id'], 'Group', $customer_id, 'Customer', 'Member Billing Profile',"","",json_encode($activityFeedDesc));
        }
        $response['status'] = 'success';
    }else{
        $errors = $validate->getErrors();
        $response['status'] = 'fail';
        $response['errors'] = $errors;
    }

    header('Content-type: application/json');
    echo json_encode($response);
    exit();
}

$customer_id = $_GET['id'];
$bill_id = checkIsset($_GET['bill_id']);
$action = isset($_GET['action']) ? $_GET['action'] : 'Add';

$row = array();
if($action == 'Edit'){
    $row = $pdo->selectOne("SELECT *,AES_DECRYPT(card_no_full,'" . $CREDIT_CARD_ENC_KEY . "') as card_no_full, AES_DECRYPT(ach_account_number,'" . $CREDIT_CARD_ENC_KEY . "') as ach_account_number, AES_DECRYPT(ach_routing_number,'" . $CREDIT_CARD_ENC_KEY . "') as ach_routing_number  from customer_billing_profile where md5(customer_id)=:customer_id and md5(id)=:id and is_deleted='N'",array(":id"=>$bill_id,':customer_id'=>$customer_id));
}

$cust_row = $pdo->selectOne("SELECT id,concat(fname,' ',lname) as name,rep_id,sponsor_id FROM customer WHERE md5(id)=:id ",array(":id"=>$customer_id));
$sponsor_billing_type = $pdo->selectOne("SELECT id,billing_type FROM  customer_group_settings WHERE customer_id=:id and billing_type='individual'",array(":id"=>$cust_row['sponsor_id']));
if(!empty($sponsor_billing_type['id'])){
    $cust_row['sponsor_id'] = getname('customer',$cust_row['sponsor_id'],'sponsor_id');
}
$payment_mode = (!empty($row['payment_mode'])? $row['payment_mode'] : '');
$pyament_methods = get_pyament_methods($cust_row['sponsor_id']);
$acceptable_cc = $pyament_methods['acceptable_cc'];

$template = 'add_billing_profile.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>