<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once dirname(__DIR__) . '/includes/cyberx_payment_class.php';
include_once dirname(__DIR__) . '/includes/enrollment_dates.class.php';
include_once dirname(__DIR__) . '/includes/member_setting.class.php';
include_once dirname(__DIR__) . '/includes/policy_setting.class.php';
$enrollDate = new enrollmentDate();
$memberSetting = new memberSetting();
$policySetting = new policySetting();

/*******************************
 **** Note ****
 *  We have no logner use this file, Now we have using commen ajax_reprocess_order.php file
 * 
 */
$res = array();
$validate = new Validation();
$BROWSER = getBrowser();
$OS = getOS($_SERVER['HTTP_USER_AGENT']);
$REQ_URL = ($_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
$order_id = $_POST['order_id'];
$customer_id = $_POST['customer_id'];
$txn_id = '0'; 
$orderdate = $_POST['post_payment_date'];
$od_pay_type = $_POST['payment_method'];
$REQ_URL = ($_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
$inserted_billing_id='';
$validate->string(array('required' => true, 'field' => 'post_payment_date', 'value' => $orderdate), array('required' => 'Payment Date is required'));
$validate->string(array('required' => true, 'field' => 'payment_type', 'value' => $od_pay_type), array('required' => 'Please Select Payment Method'));

$checkCoverage = $enrollDate->checkLastSuccessfullCoverage($order_id);

$REAL_IP_ADDRESS = get_real_ipaddress();
if ($validate->isValid()){

    include_once dirname(__DIR__) . '/includes/function.class.php';
    $functionLst = new functionsList();

    $inserted_billing_id = $od_pay_type;
    $insert_billing = true;
    $order_res = $pdo->selectOne('SELECT id,display_id,is_renewal,subscription_ids,customer_id,grand_total FROM orders WHERE id = :id AND customer_id=:customer_id', array(':id' => $order_id,":customer_id"=>$customer_id));
    if($order_res['id']){
        $order_detail_res = $pdo->select("SELECT plan_id FROM order_details WHERE order_id = :order_id AND is_deleted='N'", array(":order_id" => $order_res['id']));
        $PlanIdArr = array();
        if(count($order_detail_res) > 0){
            foreach ($order_detail_res as $key => $ord_det_value) {
                if(!in_array($ord_det_value['plan_id'], $PlanIdArr)){
                    array_push($PlanIdArr, $ord_det_value['plan_id']);
                }
            }
        }
        $payment_res = array();
        if(strtotime(date('Y-m-d',strtotime($orderdate))) == strtotime(date('Y-m-d')))
        {
            $isrenewal = $order_res['is_renewal'];
            $order_ws_ids = $order_res['subscription_ids'];
            $order_ws_sql = "SELECT ws.* FROM website_subscriptions ws
                                    WHERE
                                    ws.id IN ($order_ws_ids) AND
                                    ws.status IN('Active','Pending','Post Payment','Inactive')";
            $order_ws_result = $pdo->select($order_ws_sql);
            if (empty($order_ws_result)) {
                setNotifyError("Oops,Data Not Found!!!");
                $res['status'] = "order_not_found";
                header('Content-type: application/json');
                echo json_encode($res);
                exit();
            }
            /*start*/
            if(!empty($order_res['id'])){
                $order_billing_res = $pdo->selectOne('SELECT id,customer_billing_id FROM order_billing_info WHERE order_id = :id', array(':id' => $order_res['id']));
                if(!empty($order_billing_res['customer_billing_id'])){
                    if($order_billing_res['customer_billing_id'] == $inserted_billing_id){
                        $insert_billing = false;
                    }
                }
            }           
            $customer_id = $order_res['customer_id'];
            $customer_sql = "SELECT id,fname,lname,rep_id,sponsor_id,type,email,city,state,zip FROM customer  WHERE id=:customer_id";
            $customer_where = array(":customer_id" => $customer_id);
            $customer_row = $pdo->selectOne($customer_sql, $customer_where);
            
            $sponsor_id = $customer_row['sponsor_id'];
            $sponsor_sql = "SELECT id,type,upline_sponsors,level,payment_master_id,ach_master_id FROM customer WHERE type!='Customer' AND id = :sponsor_id ";
            $sponsor_row = $pdo->selectOne($sponsor_sql, array(':sponsor_id' => $sponsor_id));
            
           $customer_billing_sql = "SELECT *,AES_DECRYPT(ach_account_number,'".$CREDIT_CARD_ENC_KEY."') as decrypt_ach_account_number,
            AES_DECRYPT(card_no_full,'{$CREDIT_CARD_ENC_KEY}')as cc_no,
            AES_DECRYPT(ach_account_number,'{$CREDIT_CARD_ENC_KEY}')as ach_account_number,
            AES_DECRYPT(ach_routing_number,'{$CREDIT_CARD_ENC_KEY}')as ach_routing_number FROM
            customer_billing_profile WHERE customer_id=:customer_id AND id=:id ORDER BY id DESC";
            $customer_billing_where = array(":customer_id" => $customer_id, ":id" => $inserted_billing_id); 
            $customer_billing_row = $pdo->selectOne($customer_billing_sql, $customer_billing_where);

            $order_billing_id = '';
            if($insert_billing) {
                $order_billing_info_params = array(
                    'customer_id' => $order_res['customer_id'],
                    'order_id' => $order_res['id'],
                    'fname' => $customer_billing_row['fname'],
                    'lname' => $customer_billing_row['lname'],
                    'email' => $customer_billing_row['email'],
                    'country' => $customer_billing_row['country'],
                    'country_id' => $customer_billing_row['country_id'],
                    'state' => $customer_billing_row['state'],
                    'city' => $customer_billing_row['city'],
                    'zip' => $customer_billing_row['zip'],
                    'phone' => $customer_billing_row['phone'],
                    'address' => $customer_billing_row['address'],
                    'address2' => $customer_billing_row['address2'],
                    'payment_mode' => $customer_billing_row['payment_mode'],
                    'customer_billing_id' => $customer_billing_row['id'],
                    'last_cc_ach_no' => $customer_billing_row['last_cc_ach_no'],
                );
                if($customer_billing_row['payment_mode'] == 'CC'){
                    $order_billing_info_params['card_no'] = $customer_billing_row['card_no'];
                    $order_billing_info_params['card_no_full'] = $customer_billing_row['card_no_full'];
                    $order_billing_info_params['card_type'] = $customer_billing_row['card_type'];
                    $order_billing_info_params['expiry_month'] = $customer_billing_row['expiry_month'];
                    $order_billing_info_params['expiry_year'] = $customer_billing_row['expiry_year'];
                    $order_billing_info_params['cvv_no'] = $customer_billing_row['cvv_no'];
                    $billing_desc = $customer_billing_row['payment_mode'] . " (" . $customer_billing_row['card_type']. " *".$customer_billing_row['card_no'].")"; 
                } else {
                    $order_billing_info_params['bankname'] = $customer_billing_row['bankname'];
                    $order_billing_info_params['ach_account_number'] = $customer_billing_row['ach_account_number'];
                    $order_billing_info_params['ach_routing_number'] = $customer_billing_row['ach_routing_number'];
                    $order_billing_info_params['ach_account_type'] = $customer_billing_row['ach_account_type'];
                    $billing_desc = $customer_billing_row['payment_mode'] . ' ( *'. substr($customer_billing_row['decrypt_ach_account_number'], -4,4) .")"; 
                }
                $order_billing_id = $pdo->insert("order_billing_info", $order_billing_info_params);
            }            
            $update_param = array(
                'req_url' => $REQ_URL
            );
            $sale_type_params = array();
            $sale_type_params['is_renewal'] = $order_res['is_renewal'];
            $sale_type_params['customer_id'] = $order_res['customer_id'];
            $payment_master_id = $functionLst->get_agent_merchant_detail($PlanIdArr, $sponsor_row['id'], $customer_billing_row['payment_mode'],$sale_type_params);

            if(!empty($payment_master_id)){
                $payment_processor = getname('payment_master',$payment_master_id,'processor_id');
            }

            if($insert_billing){
                $update_param['payment_processor'] = $payment_processor;
                $update_param['payment_master_id'] = $payment_master_id;
                $update_param['payment_type'] = $customer_billing_row['payment_mode'];
            }
            $update_where_param = array(
                "clause" => "id=:id",
                "params" => array(":id" => $order_id),
            );
            
            $pdo->update("orders", $update_param, $update_where_param); 
            $order_res = $pdo->selectOne('SELECT * FROM orders WHERE id = :id', array(':id' => $order_id));
            /*end*/
             
            $fname = $customer_billing_row['fname'];
            $lname = $customer_billing_row['lname'];
            $card_type = $customer_billing_row['card_type'];
            $card_number = $customer_billing_row['cc_no'];
            $expiry_month = $customer_billing_row['expiry_month'];
            $expiry_year = $customer_billing_row['expiry_year'];
            $address = $customer_billing_row['address'];
            $address2 = $customer_billing_row['address2'];
            $city = $customer_billing_row['city'];
            $state = $customer_billing_row['state'];
            $zip = $customer_billing_row['zip'];
            $payment_mode = $customer_billing_row['payment_mode'];
            $last_cc_ach_no = $customer_billing_row['last_cc_ach_no'];

            $bankname = $customer_billing_row['bankname'];
            $ach_account_type = $customer_billing_row['ach_account_type'];
            $ach_bill_fname = $customer_billing_row["fname"];
            $ach_bill_lname = $customer_billing_row["lname"];
            $ach_account_number = $customer_billing_row['ach_account_number'];
            $ach_routing_number = $customer_billing_row['ach_routing_number'];
            
            $cc_params = array();
            $cc_params['order_id'] = $order_res['display_id'];
            $cc_params['amount'] = $order_res['grand_total'];
            $cc_params['description'] = "Reprocess Order";
            $cc_params['email'] = $customer_row['email'];
            $cc_params['ipaddress'] = !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'];
            $cc_params['processor'] = $order_res['payment_processor'];            

          
            if ($payment_mode == "ACH") {
                $cc_params['firstname'] = $ach_bill_fname;
                $cc_params['lastname'] = $ach_bill_lname;
                $cc_params['address1'] = $customer_row["address"];
                $cc_params['city'] = $customer_row["city"];
                $cc_params['state'] = $customer_row["state"];
                $cc_params['zip'] = $customer_row["zip"];
                $cc_params['country'] = 'USA';

                $cc_params['ach_account_type'] = $ach_account_type;
                $cc_params['ach_routing_number'] = $ach_routing_number;
                $cc_params['ach_account_number'] = $ach_account_number;
                $cc_params['name_on_account'] = $ach_bill_fname . ' ' . $ach_bill_lname;
                $cc_params['bankname'] = $bankname;
               
                $api = new CyberxPaymentAPI();
                $payment_res = $api->processPaymentACH($cc_params,$payment_master_id);
            
                if ($payment_res['status'] == 'Success') {
                    $paymentApproved = true;
                    $txn_id = $payment_res['transaction_id'];
                } else {
                    $paymentApproved = false;
                    $txn_id = $payment_res['transaction_id'];
                    $payment_error = $payment_res['message'];
                    $cc_params['order_type'] = 'Reprocess Order';
                    $cc_params['browser'] = $BROWSER;
                    $cc_params['os'] = $OS;
                    $cc_params['req_url'] = $REQ_URL;
                    $cc_params['err_text'] = $payment_error;
                    $functionLst->credit_card_decline_log($customer_id, $cc_params, $payment_res);
                }

            } elseif ($payment_mode == "CC") {
                $cc_params['firstname'] = $fname;
                $cc_params['lastname'] = $lname;
                $cc_params['address1'] = $address;
                $cc_params['address2'] = $address2;
                $cc_params['city'] = $city;
                $cc_params['state'] = $state;
                $cc_params['zip'] = $zip;
                $cc_params['country'] = $customer_billing_row['country_id'];
                $cc_params['ccnumber'] = $card_number;
                $cc_params['card_type'] = $card_type;
                $cc_params['ccexp'] = str_pad($expiry_month, 2, "0", STR_PAD_LEFT) . substr($expiry_year, -2);
                if ($card_number == '4111111111111114') {
                    $paymentApproved = true;
                    $txn_id = 0;

                    $payment_res = array("status" => "Success","transaction_id" => 0,"message" => "Manual Approved");
                    // $payment_res = array("status" => "Fail","transaction_id" => 0,"message" => "Manual Failed");
                    $payment_error = 'Payment Successfully.';
                } else {
                    $api = new CyberxPaymentAPI();
                    $payment_res = $api->processPayment($cc_params,$payment_master_id);
                    if ($payment_res['status'] == 'Success') {
                        $paymentApproved = true;
                        $txn_id = $payment_res['transaction_id'];
                        $payment_response = $payment_res;
                    } else {
                        $paymentApproved = false;
                        $txn_id = $payment_res['transaction_id'];
                        $decline_txt = $payment_res['message'];
                        $payment_error = $payment_res['message'];
                        $cc_params['order_type'] = 'Reprocess Order';
                        $cc_params['browser'] = $BROWSER;
                        $cc_params['os'] = $OS;
                        $cc_params['req_url'] = $REQ_URL;
                        $cc_params['err_text'] = $payment_error;
                        $functionLst->credit_card_decline_log($customer_id, $cc_params, $payment_res);
                    }
                }
            }
            $bill_data = array(
                'customer_id' => $customer_id,
                'fname' => makeSafe($fname),
                'lname' => makeSafe($lname),
                'email' => makeSafe($customer_row['email']),
                'country_id' => '231',
                'country' => 'United States',
                'state' => makeSafe($state),
                'city' => makeSafe($city),
                'zip' => makeSafe($zip),
                'address' => makeSafe($address),
                'payment_mode' => $payment_mode,
            );
            if($payment_mode == "ACH") {
                $bill_data = array_merge($bill_data,array(
                    'ach_account_type' => $ach_account_type,
                    'bankname' => $bankname,
                    'ach_account_number' => "msqlfunc_AES_ENCRYPT('" . $ach_account_number . "','" . $CREDIT_CARD_ENC_KEY . "')",
                    'ach_routing_number' => "msqlfunc_AES_ENCRYPT('" . $ach_routing_number . "','" . $CREDIT_CARD_ENC_KEY . "')",
                ));
            } elseif($payment_mode == "CC") {
                $bill_data = array_merge($bill_data,array(
                    'card_type' => makeSafe($card_type),
                    'expiry_month' => makeSafe($expiry_month),
                    'expiry_year' => makeSafe($expiry_year),
                    'card_no' => makeSafe(substr($card_number, -4)),
                    'card_no_full' => "msqlfunc_AES_ENCRYPT('" . $card_number . "','" . $CREDIT_CARD_ENC_KEY . "')",
                ));
            }
            $bill_where = array("clause" => "order_id = :id", "params" => array(":id" => $order_res['id']));

            $pdo->update("order_billing_info",$bill_data,$bill_where);
            $billing_info = $error_message = $customer_status = '';
            $member_status = $memberSetting->get_status_by_payment($paymentApproved);
            if ($paymentApproved) {
                /*----- code for order --------*/
                $order_params = array(
                    'transaction_id' => makeSafe($txn_id),
                    'status' => ($payment_mode == "ACH" ? "Pending Settlement" : "Payment Approved"),
                    'browser' => $BROWSER,
                    'os' => $OS,
                    'req_url' => $REQ_URL,
                    'payment_master_id' => $payment_master_id,
                    'payment_type' => $payment_mode,
                    'payment_processor' => !empty($payment_res['API_Type']) ? $payment_res['API_Type'] : "Authorize.net",
                    'payment_processor_res' => json_encode($payment_res),
                );

                if(isset($payment_res['review_require']) && $payment_res['review_require'] == 'Y'){
                    $order_params['review_require'] = 'Y';
                }
                
                $order_where = array("clause" => "id=:id", "params" => array(":id" => $order_id));
                $pdo->update("orders", $order_params, $order_where);
                $other_params=array("transaction_id"=>$txn_id,'transaction_response'=>$payment_res);

               
                
                foreach ($order_ws_result as $order_ws_row) {
                    /*----------- Fetch Product Row -----------*/
                    $prd_sql = "SELECT name FROM prd_main WHERE id=:id";
                    $prd_row = $pdo->selectOne($prd_sql, array(":id" => $order_ws_row['product_id']));
                    if ($isrenewal == 'Y'){
                        $msg = 'Renewed Successfully'; 
                    }else{
                        $msg = 'Initial Setup Successfully';
                    }   
                     
                    $website_subscriptions_history_data = array(
                        'customer_id' => $customer_id,
                        'website_id' => $order_ws_row['id'],
                        'product_id' => $order_ws_row['product_id'],
                        'plan_id' => $order_ws_row['plan_id'],
                        'fee_applied_for_product' => $order_ws_row['fee_applied_for_product'],
                        'prd_plan_type_id' =>  $order_ws_row['prd_plan_type_id'],
                        'order_id' => $order_id,
                        'status' => 'Success',
                        'message' => $msg,
                        'admin_id' => $_SESSION['admin']['id'],
                        'authorize_id' => makeSafe($txn_id),
                        'processed_at' => 'msqlfunc_NOW()',
                    );
                    $history_id = $pdo->insert("website_subscriptions_history", $website_subscriptions_history_data);
                     if ($isrenewal == 'Y'){
                        $msg = 'Successful Renewal'; 
                        
                        // activity_feed(3, $customer_row['id'], $customer_row['type'], $history_id,
                        // 'website_subscriptions_history', $msg , $prd_row['name'], "", 0);              
                        $update_ws_data = array(
                            'last_order_id' => $order_id,
                             'fail_order_id' => 0,
                            'last_purchase_date' => 'msqlfunc_NOW()',
                            'status' => $member_status['policy_status'],
                            'renew_count' => 'msqlfunc_renew_count + 1',
                            'updated_at' => 'msqlfunc_NOW()',
                        );
                    }else{
                        $msg = 'Initial Setup Successfully';
                        // activity_feed(3, $customer_row['id'], $customer_row['type'], $history_id,
                        // 'website_subscriptions_history', $msg , $prd_row['name'], "", 0);              
                        $update_ws_data = array(
                            'last_order_id' => $order_id,
                            'last_purchase_date' => 'msqlfunc_NOW()',
                            'status' => $member_status['policy_status'],                           
                            'updated_at' => 'msqlfunc_NOW()',
                        ); 
                    }  

                    if($payment_mode!="ACH"){
                        $update_ws_data['total_attempts'] = 0;
                        $update_ws_data['next_attempt_at'] = NULL;
                    }
                    
                    $update_ws_where = array("clause" => "id=:id", "params" => array(":id" => $wsId));
                    $pdo->update("website_subscriptions", $update_ws_data, $update_ws_where);
 
                     
                    $ws_cd_sql = "SELECT cd.* FROM customer_dependent cd WHERE website_id=:website_id";
                    $ws_cd_where = array(":website_id" => $order_ws_row['id']);
                    $ws_cd_result = $pdo->select($ws_cd_sql, $ws_cd_where);
                    foreach ($ws_cd_result as $ws_cd_row) {                        
                        $update_dependent_where = array(
                            "clause" => "id=:id",
                            "params" => array(
                                ":id" => $ws_cd_row['id'],
                            ),
                        );
                        $pdo->update("customer_dependent", array('status' => $member_status['member_status'], 'terminationDate' =>NULL),$update_dependent_where);
                    }
                }

                $enrollDate->updateNextBillingDateByOrder($order_id);

                if($payment_mode != "ACH"){
                    if ($isrenewal == 'Y'){
                       $transactionInsId = $functionLst->transaction_insert($order_id,'Credit','Renewal Order','Transaction Approved',0,$other_params);  
                    } else {
                        $transactionInsId = $functionLst->transaction_insert($order_id,'Credit','New Order','Transaction Approved',0,$other_params); 
                    }
                }else{
                    $transactionInsId = $functionLst->transaction_insert($order_id,'Credit','Pending','Settlement Transaction',0,$other_params); 
                }
                
                $update_customer_where = array(
                    'clause' => 'id=:id',
                    'params' => array(
                        ':id' => $customer_id,
                    ),
                );
                $update_customer_data = array('status' => $member_status['member_status'], 'updated_at' => 'msqlfunc_NOW()');
                $pdo->update('customer', $update_customer_data, $update_customer_where);
                

                 //********* Payable Insert Code Start ********************
                    $payable_params=array(
                        'payable_type'=>'Vendor',
                        'type'=>'Vendor',
                        'transaction_tbl_id' => $transactionInsId['id'],
                    );
                    $payable = $functionLst->payable_insert($order_id,0,0,0,$payable_params);
                       
                //********* Payable Insert Code End   ******************** 
                $customer_status = $member_status['member_status'];
                $res['status'] = 'success';
            } else {
                /*----- code for order --------*/
                $order_params = array(
                    'transaction_id' => makeSafe($txn_id),
                    'payment_master_id' => $payment_master_id,
                    'payment_type' => $payment_mode,
                    'payment_processor' => !empty($payment_res['API_Type']) ? $payment_res['API_Type'] : "Authorize.net",
                    'payment_processor_res' => json_encode($payment_res),
                );                
                $order_where = array("clause" => "id=:id", "params" => array(":id" => $order_id));
                $pdo->update("orders", $order_params, $order_where);

                $other_params=array("transaction_id"=>$txn_id,'transaction_response'=>$payment_res,'reason'=>checkIsset($payment_error));
                
                    $transactionInsId = $functionLst->transaction_insert($order_id,'Failed','Payment Declined','Transaction Declined',0,$other_params);
                
                foreach ($order_ws_result as $order_ws_row) {
                    
                    $website_subscriptions_history_data = array(
                        'customer_id' => $customer_id,
                        'website_id' => $order_ws_row['id'],
                        'product_id' => $order_ws_row['product_id'],
                        'fee_applied_for_product' => $order_ws_row['fee_applied_for_product'],
                        'prd_plan_type_id' =>  $order_ws_row['prd_plan_type_id'],
                        'plan_id' => $order_ws_row['plan_id'],
                        'order_id' => $order_id,
                        'status' => 'Fail',
                        'message' => $payment_error,
                        'authorize_id' => makeSafe($txn_id),
                        'admin_id' => !empty($_SESSION['admin']['id']) ? $_SESSION['admin']['id'] : 0,
                        'note' => 'menual attempt',  
                        'processed_at' => 'msqlfunc_NOW()',
                    );
                    $history_id = $pdo->insert("website_subscriptions_history", $website_subscriptions_history_data);
                    $updateArr = array(
                    'total_attempts' => 'msqlfunc_total_attempts + 1',
                    'updated_at' => 'msqlfunc_NOW()',
                    );
                    $attemptSql = "SELECT * FROM prd_subscription_attempt
                                           WHERE attempt=:attempt AND is_deleted='N'";
                    $attemptParams = array(":attempt" => ($order_ws_row['total_attempts'] + 1));
                    $attemptRow = $pdo->selectOne($attemptSql, $attemptParams);

                    $member_status = $memberSetting->get_status_by_payment($paymentApproved,"",false,"",array("attempt"=>$order_ws_row['total_attempts'] + 1));

                    if (!empty($attemptRow)) {
                        $atmpt = $attemptRow['attempt'];
                        $fail_trigger_id = $attemptRow['fail_trigger_id'];
                        $updateArr['next_attempt_at'] = date('Y-m-d', strtotime("+" . $attemptRow['attempt_frequency'] . " " . $attemptRow['attempt_frequency_type']));
                        $customer_status = $updateArr['status'] = $member_status['member_status'];
                        
                        $customrt_updateArr = array();
                        $customrt_updateArr['status'] = $member_status['member_status'];
                        $customer_updateWhere = array("clause" => 'id=:id', 'params' => array(":id" => $order_ws_row['customer_id']));
                        $pdo->update("customer", $customrt_updateArr, $customer_updateWhere);
                    }else{                        
                        $termination_date = $enrollDate->getTerminationDate($order_ws_row['id']);

                        $extra_params = array();
                        $extra_params['location'] = "ajax_reprocess_order";
                        $termination_reason = "Failed Billing";
                        $policySetting->setTerminationDate($order_ws_row['id'],$termination_date,$termination_reason,$extra_params);
                    }

                $updateWhere = array("clause" => 'id=:id', 'params' => array(":id" => $order_ws_row['id']));
                $pdo->update("website_subscriptions", $updateArr, $updateWhere);
                }
                setNotifyError(($payment_error ? $payment_error : 'Error in processing payment'));
                
                $pf_act_data = array(
                    'order_id' => $order_id,
                    'order_display_id' => $order_res['display_id'],
                    'order_billing_id' => !empty($order_billing_id) ? $order_billing_id : $inserted_billing_id ,
                    'reason' => $payment_error ? $payment_error : 'Error in processing payment',
                    'billing_info' => ($payment_mode == "CC" ? "$card_type *" . substr($card_number, -4):"ACH *" . substr($ach_routing_number, -4)),
                );
                // $pf_act_data = json_encode($pf_act_data);
                // activity_feed(3,$order_res['customer_id'], "Customer", $order_id, 'orders', 'Billing Failed', $fname, $lname,$pf_act_data);
                $error_message = $pf_act_data['reason'];
                $billing_info = $pf_act_data ['billing_info'];
                $res['msg'] = $payment_error;
                $res['status'] = 'payment_fail';
            }

            $activity_feed_data = array();
            $customer_sql = "SELECT fname,lname,rep_id,id FROM customer  WHERE id=:customer_id";
            $customer_where = array(":customer_id" => $customer_id);
            $customer_row = $pdo->selectOne($customer_sql, $customer_where);
            
            $activity_feed_data['ac_message'] = array(
                'ac_red_1'=>array(
                    'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
                    'title'=>$_SESSION['admin']['display_id'],
                ),
                'ac_message_1' =>' Reprocess Order ',
                'ac_red_2'=>array(
                    // 'href'=>$ADMIN_HOST.'/members_details.php?id='.md5($customer_id),
                    'title'=>$order_res['display_id'],
                ),
                'ac_message_2' =>' for Member ',
                'ac_red_3'=>array(
                    'href'=>$ADMIN_HOST.'/members_details.php?id='.md5($customer_id),
                    'title'=>$customer_row['rep_id'],
                ),
            );

            if(!empty($customer_status)){
                $activity_feed_data['customer_status'] = "Member status : ".$customer_status;
            }
            
            if($paymentApproved){
                $activity_feed_data['success_message_descriptions'] = 'Payment Successfull';
            }else{
                $activity_feed_data['error_message_desc'] = $error_message;
            }
            $bill_info =  $payment_mode == "CC" ? "$card_type *" . substr($card_number, -4):"ACH *" . substr($ach_routing_number, -4);
            $activity_feed_data['billing_info_descriptions'] = "Billing Information : ".$bill_info;

            activity_feed(3, $_SESSION['admin']['id'], 'Admin', $customer_id, 'Customer', 'Reprocess Order','','',json_encode($activity_feed_data));
        }else{
            $member_status = $memberSetting->get_status_by_payment("","",true);
            $customer_id = $order_res['customer_id'];
            $customer_sql = "SELECT fname,lname,rep_id,sponsor_id,id FROM customer WHERE id=:customer_id";
            $customer_where = array(":customer_id" => $customer_id);
            $customer_row = $pdo->selectOne($customer_sql, $customer_where);
            
            $sponsor_id = $customer_row['sponsor_id'];
            $sponsor_sql = "SELECT id,type,upline_sponsors,level,payment_master_id,ach_master_id FROM customer WHERE type!='Customer' AND id = :sponsor_id ";
            $sponsor_row = $pdo->selectOne($sponsor_sql, array(':sponsor_id' => $sponsor_id));
            if(!empty($order_res['id'])){
                $order_billing_res = $pdo->selectOne('SELECT * FROM order_billing_info WHERE order_id = :id', array(':id' => $order_res['id']));
                if(!empty($order_billing_res['customer_billing_id'])){
                    if($order_billing_res['customer_billing_id'] == $inserted_billing_id){
                        $insert_billing = false;
                    }
                }
            }
            $customer_billing_res = $pdo->selectOne("SELECT *,AES_DECRYPT(ach_account_number,'".$CREDIT_CARD_ENC_KEY."') as decrypt_ach_account_number FROM customer_billing_profile WHERE id = :id", array(":id" => $inserted_billing_id));
            $sale_type_params = array();
            $sale_type_params['is_renewal'] = $order_res['is_renewal'];
            $sale_type_params['customer_id'] = $order_res['customer_id'];
            $payment_master_id = $functionLst->get_agent_merchant_detail($PlanIdArr, $sponsor_row['id'], $customer_billing_res['payment_mode'],$sale_type_params);

            if(!empty($payment_master_id)){
                $payment_processor = getname('payment_master',$payment_master_id,'processor_id');
            }
            
            if($insert_billing)
            {
                $order_billing_info_params = array(
                    'customer_id' => $order_res['customer_id'],
                    'order_id' => $order_res['id'],
                    'fname' => $customer_billing_res['fname'],
                    'lname' => $customer_billing_res['lname'],
                    'email' => $customer_billing_res['email'],
                    'country' => $customer_billing_res['country'],
                    'country_id' => $customer_billing_res['country_id'],
                    'state' => $customer_billing_res['state'],
                    'city' => $customer_billing_res['city'],
                    'zip' => $customer_billing_res['zip'],
                    'phone' => $customer_billing_res['phone'],
                    'address' => $customer_billing_res['address'],
                    'address2' => $customer_billing_res['address2'],
                    'payment_mode' => $customer_billing_res['payment_mode'],
                    'customer_billing_id' => $customer_billing_res['id'],
                    'last_cc_ach_no' => $customer_billing_res['last_cc_ach_no'],
                );
                if($customer_billing_res['payment_mode'] == 'CC'){
                    $order_billing_info_params['card_no'] = $customer_billing_res['card_no'];
                    $order_billing_info_params['card_no_full'] = $customer_billing_res['card_no_full'];
                    $order_billing_info_params['card_type'] = $customer_billing_res['card_type'];
                    $order_billing_info_params['expiry_month'] = $customer_billing_res['expiry_month'];
                    $order_billing_info_params['expiry_year'] = $customer_billing_res['expiry_year'];
                    $billing_desc = $customer_billing_res['payment_mode'] . " (" . $customer_billing_res['card_type']. " *".$customer_billing_res['card_no'].")"; 
                } else {
                    $order_billing_info_params['bankname'] = $customer_billing_res['bankname'];
                    $order_billing_info_params['ach_account_number'] = $customer_billing_res['ach_account_number'];
                    $order_billing_info_params['ach_routing_number'] = $customer_billing_res['ach_routing_number'];
                    $order_billing_info_params['ach_account_type'] = $customer_billing_res['ach_account_type'];
                    $billing_desc = $customer_billing_res['payment_mode'] . ' ( *'. substr($customer_billing_res['decrypt_ach_account_number'], -4,4) .")"; 
                }
                $order_billing_id = $pdo->insert("order_billing_info", $order_billing_info_params);
            }
             
            $update_param = array(
                'status' => 'Post Payment',
                'post_date' => date("Y-m-d",strtotime($orderdate)),
                'req_url' => $REQ_URL,
                'updated_at' => 'msqlfunc_NOW()',
                'payment_master_id' => $payment_master_id,
                'payment_type' => $customer_billing_res['payment_mode'],
                'future_payment' => 'Y'
            );

            $update_where_param = array(
                "clause" => "id=:id",
                "params" => array(":id" => $order_id),
            );
            $pdo->update("orders", $update_param, $update_where_param);
             
            $transactionInsId = $functionLst->transaction_insert($order_id,'Credit','Pending','Post Transaction','',array("transaction_id"=>$txn_id,'transaction_response'=>$payment_res));
            
            $activity_feed_arr = array(
                'post_date' => date("Y-m-d",strtotime($orderdate)),
                'admin' => $_SESSION['admin']['id']
            );

            // activity_feed(3, $order_res['customer_id'], 'Customer', $order_id, 'orders', 'Order Re-attempt','','',json_encode($activity_feed_arr));
            
            $ws_res = $pdo->select("SELECT * FROM website_subscriptions WHERE id IN (".$order_res['subscription_ids'].")");
            if(count($ws_res) > 0){
                foreach ($ws_res as $key => $value) {
                    $update_ws_data = array(
                        'last_order_id' => $order_id,
                        'status' => $member_status['policy_status'],
                    );
                    $update_ws_where = array("clause" => 'id=:id', 'params' => array(':id' => $value['id']));
                    $pdo->update("website_subscriptions", $update_ws_data, $update_ws_where);
                     
                    $ws_history_data = array(
                        'customer_id' => $order_res['customer_id'],
                        'website_id' => $value['id'],
                        'admin_id' => $_SESSION['admin']['id'],
                        'product_id' => $value['product_id'],
                        'plan_id' => $value['plan_id'],
                        'prd_plan_type_id' => $value['prd_plan_type_id'],
                        'fee_applied_for_product' => $value['fee_applied_for_product'],
                        'order_id' => $order_id,
                        'authorize_id' => makeSafe($txn_id),
                        'status' => 'Setup',
                        'message' => 'Initial Setup Successful With Post Date' . date("m/d/Y", strtotime($orderdate)),
                        'note' => 'menual attempt',
                        'processed_at' => 'msqlfunc_NOW()',
                    );
                    $pdo->insert("website_subscriptions_history", $ws_history_data);
                    
                    $cust_dep_sql = "SELECT id FROM customer_dependent WHERE customer_id = :customer_id AND product_id = :product_id AND product_plan_id = :product_plan_id AND status != 'Termed'";
                    $cust_dep_where = array(":customer_id" =>$order_res['customer_id'], ":product_id" =>$value['product_id'], ":product_plan_id" =>$value['plan_id']);
                    $cust_res_res = $pdo->select($cust_dep_sql, $cust_dep_where);
                    if(count($cust_res_res)){
                        foreach ($cust_res_res as $ce_row) {
                            $term_cd_data = array(
                                "status" => $member_status['member_status'],
                                "updated_at" => "msqlfunc_NOW()",
                            );                    
                            $term_cd_where = array(
                                'clause' => "id=:id and status!='Termed'",
                                'params' => array(':id' => $ce_row['id']),
                            );
                            $pdo->update("customer_dependent", $term_cd_data, $term_cd_where);
                        }
                    }
                }
            }

            $activity_feed_data = array();
            $customer_sql = "SELECT fname,lname,rep_id,id FROM customer  WHERE id=:customer_id";
            $customer_where = array(":customer_id" => $customer_id);
            $customer_row = $pdo->selectOne($customer_sql, $customer_where);
            
            $activity_feed_data['ac_message'] = array(
                'ac_red_1'=>array(
                    'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
                    'title'=>$_SESSION['admin']['display_id'],
                ),
                'ac_message_1' =>' Reprocess Order ',
                'ac_red_2'=>array(
                    // 'href'=>$ADMIN_HOST.'/members_details.php?id='.md5($customer_id),
                    'title'=>$order_res['display_id'],
                ),
                'ac_message_2' =>' for Member ',
                'ac_red_3'=>array(
                    'href'=>$ADMIN_HOST.'/members_details.php?id='.md5($customer_id),
                    'title'=>$customer_row['rep_id'],
                ),
            );

            $order_bill_id = !empty($order_billing_id) ? $order_billing_id : $inserted_billing_id;

            if(!empty($order_bill_id)){
                $bill_info = $pdo->selectOne("SELECT payment_mode,card_type,last_cc_ach_no from order_billing_info where id=:order_bill_id",array(":order_bill_id"=>$order_bill_id));

                $bill_in = $bill_info['payment_mode'] == "CC" ?$bill_info['card_type'] ." *" .$bill_info['last_cc_ach_no'] : "ACH *" . $bill_info['last_cc_ach_no'];
                $activity_feed_data['billing_info_descriptions'] = "Billing Information : ".$bill_in ;
            }
            
            $activity_feed_data['billing_info_descriptions'] = 'Post Payment : '.getCustomDate($orderdate);
            
            activity_feed(3, $_SESSION['admin']['id'], 'Admin', $customer_id, 'Customer', 'Reprocess Order','','',json_encode($activity_feed_data));

            $res['status'] = "success";
        }   
    }else{
        $res['status'] = "order_not_found";
    }
} else {
    $res['status'] = "fail";
    $res['errors'] = $validate->getErrors();
}
header('Content-Type: application/json');
echo json_encode($res);
dbConnectionClose();
exit();
?>