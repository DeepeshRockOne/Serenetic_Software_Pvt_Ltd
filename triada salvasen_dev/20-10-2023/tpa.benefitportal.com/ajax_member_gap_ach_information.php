<?php
include_once __DIR__ . '/includes/connect.php';
    
    $REAL_IP_ADDRESS = get_real_ipaddress();
    
    $response = array();
    $location = isset($_REQUEST['location'])?$_REQUEST['location']:'admin';
    $customerId = !empty($_POST['customer_id']) ? $_POST['customer_id'] : '';
    $ach_id = !empty($_POST['ach_id']) ? $_POST['ach_id'] : '';
    $ach_fname = !empty($_POST['ach_fname']) ? $_POST['ach_fname'] : '';
    $ach_lname = !empty($_POST['ach_lname']) ? $_POST['ach_lname'] : '';
    $ach_bankname = !empty($_POST['ach_bankname']) ? $_POST['ach_bankname'] : '';
    $ach_account_type = !empty($_POST['ach_account_type']) ? $_POST['ach_account_type'] : '';
    $ach_account_number = !empty($_POST['ach_account_number']) ? $_POST['ach_account_number'] : '';
    $confirm_ach_account_number = !empty($_POST['confirm_ach_account_number']) ? $_POST['confirm_ach_account_number'] : '';
    $ach_routing_number = !empty($_POST['ach_routing_number']) ? $_POST['ach_routing_number'] : '';

    $validate = new Validation();

    $validate->string(array('required' => true, 'field' => 'ach_fname', 'value' => $ach_fname), array('required' => 'First name is required'));
    if (!$validate->getError("ach_fname") && !ctype_alnum(str_replace(" ","",$ach_fname))) {
        $validate->setError("ach_fname","Enter Valid First name");
    }
    $validate->string(array('required' => true, 'field' => 'ach_lname', 'value' => $ach_lname), array('required' => 'Last name is required'));
    if (!$validate->getError("ach_lname") && !ctype_alnum(str_replace(" ","",$ach_lname))) {
        $validate->setError("ach_lname","Enter Valid Last name");
    }
    $validate->string(array('required' => true, 'field' => 'ach_bankname', 'value' => $ach_bankname), array('required' => 'Bank name is required'));
    $validate->string(array('required' => true, 'field' => 'ach_account_type', 'value' => $ach_account_type), array('required' => 'Select account type'));
    $validate->digit(array('required' => true, 'field' => 'ach_account_number', 'value' => $ach_account_number,'min'=>$MIN_ACCOUNT_NUMBER,'max'=>$MAX_ACCOUNT_NUMBER), array('required' => 'Account number is required', 'invalid' => "Enter valid Account number"));
    $validate->digit(array('required' => true, 'field' => 'confirm_ach_account_number', 'value' => $confirm_ach_account_number,'min'=>$MIN_ACCOUNT_NUMBER,'max'=>$MAX_ACCOUNT_NUMBER), array('required' => 'Confirm account number is required', 'invalid' => "Enter valid Confirm account number"));

    if($ach_account_number != $confirm_ach_account_number) {
        $validate->setError('confirm_ach_account_number', 'account number not matched');
    }

    $validate->digit(array('required' => true, 'field' => 'ach_routing_number', 'value' => $ach_routing_number), array('required' => 'Routing number is required', 'invalid' => "Enter valid Routing number"));
    if (!$validate->getError("ach_routing_number")) {
        if (checkRoutingNumber($ach_routing_number) == false) {
            $validate->setError("ach_routing_number", "Enter valid routing number");
        }
    }

    
    $selCustomer="SELECT id,rep_id,fname,lname FROM customer WHERE md5(id)=:customerId";
    $paramsCustomer = array(":customerId"=>$customerId);
    $resCustomer = $pdo->selectOne($selCustomer,$paramsCustomer);
    if($validate->isValid()){
        $paramsACH = array(
            'fname'=>$ach_fname,
            'lname'=>$ach_lname,
            'bankname'=>$ach_bankname,
            'ach_account_type'=>$ach_account_type,
            'ach_account_number'=>"msqlfunc_AES_ENCRYPT('" . $ach_account_number . "','" . $CREDIT_CARD_ENC_KEY . "')",
            'ach_routing_number'=>"msqlfunc_AES_ENCRYPT('" . $ach_routing_number . "','" . $CREDIT_CARD_ENC_KEY . "')",
            'is_direct_deposit_account'=>'Y',
            'payment_mode' => "ACH",
            'ip_address' => !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'],
        );

        if(!empty($ach_id)){
            $updWhereACH = array(
                "clause"=> 'id = :id',
                "params"=>array(":id"=>$ach_id)
            );
            $pdo->update("customer_billing_profile",$paramsACH,$updWhereACH);
            $response['successfully'] = "ACH application deposit account updated successfully.";

            if($location == 'admin'){
                $ac_desc['ac_message'] =array(
                    'ac_red_1'=>array(
                        'href'=>$ADMIN_HOST.'/admin_profile.php?id='. md5($_SESSION['admin']['id']),
                        'title'=>$_SESSION['admin']['display_id'],
                    ),
                    'ac_message_1' =>' Updated ACH Information Deposit Account',
                    'ac_red_2'=>array(
                        'href'=> $ADMIN_HOST.'/members_details.php?id='.md5($resCustomer['id']),
                        'title'=> $resCustomer['rep_id'],
                    ),
                );
                activity_feed(3,$_SESSION['admin']['id'], 'Admin' , $resCustomer['id'], 'customer', 'ACH information deposit account',$_SESSION['admin']['name'],"",json_encode($ac_desc));
            }else if($location == 'group'){
                $ac_desc['ac_message'] =array(
                    'ac_red_1'=>array(
                        'href'=>$ADMIN_HOST.'/groups_details.php?id='.md5($_SESSION['groups']['id']),
                        'title'=>$_SESSION['groups']['rep_id'],
                    ),
                    'ac_message_1' =>' Updated ACH Information Deposit Account',
                    'ac_red_2'=>array(
                        'href'=> $ADMIN_HOST.'/members_details.php?id='.md5($resCustomer['id']),
                        'title'=> $resCustomer['rep_id'],
                    ),
                );
                activity_feed(3,$_SESSION['groups']['id'], 'Group' , $resCustomer['id'], 'customer', 'ACH information deposit account',$_SESSION['groups']['fname'],$_SESSION['groups']['lname'],json_encode($ac_desc));
            }
        }else{
            $paramsACH["customer_id"] = $resCustomer['id'];
            $ach_id = $pdo->insert("customer_billing_profile",$paramsACH);
            $response['successfully'] = "ACH application deposit account created successfully.";

            if($location == 'admin'){
                $ac_desc['ac_message'] =array(
                    'ac_red_1'=>array(
                        'href'=>$ADMIN_HOST.'/admin_profile.php?id='. md5($_SESSION['admin']['id']),
                        'title'=>$_SESSION['admin']['display_id'],
                    ),
                    'ac_message_1' =>' Created ACH Information Deposit Account',
                    'ac_red_2'=>array(
                        'href'=> $ADMIN_HOST.'/members_details.php?id='.md5($resCustomer['id']),
                        'title'=> $resCustomer['rep_id'],
                    ),
                );
                activity_feed(3,$_SESSION['admin']['id'], 'Admin' , $resCustomer['id'], 'customer', 'ACH information deposit account',$_SESSION['admin']['name'],"",json_encode($ac_desc));
            }else if($location == 'group'){
                $ac_desc['ac_message'] =array(
                    'ac_red_1'=>array(
                        'href'=>$ADMIN_HOST.'/groups_details.php?id='.md5($_SESSION['groups']['id']),
                        'title'=>$_SESSION['groups']['rep_id'],
                    ),
                    'ac_message_1' =>' Created ACH Information Deposit Account',
                    'ac_red_2'=>array(
                        'href'=> $ADMIN_HOST.'/members_details.php?id='.md5($resCustomer['id']),
                        'title'=> $resCustomer['rep_id'],
                    ),
                );
                activity_feed(3,$_SESSION['groups']['id'], 'Group' , $resCustomer['id'], 'customer', 'ACH information deposit account',$_SESSION['groups']['fname'],$_SESSION['groups']['lname'],json_encode($ac_desc));
            }
        }
        $response['ach_id'] = $ach_id;
        $response['status'] = 'success';
    }else{
        $errors = $validate->getErrors();
        $response['status'] = 'fail';
        $response['errors'] = $errors;
    }

    header('Content-type: application/json');
    echo json_encode($response);
    dbConnectionClose();
    exit();

?>