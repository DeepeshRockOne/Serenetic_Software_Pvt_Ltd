<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once dirname(__DIR__) . '/includes/member_enrollment.class.php';
include_once dirname(__DIR__) . '/includes/enrollment_dates.class.php';
include_once dirname(__DIR__) . '/includes/member_setting.class.php';
include_once dirname(__DIR__) . '/includes/policy_setting.class.php';
$policySetting = new policySetting();
$MemberEnrollment = new MemberEnrollment();
$enrollDate = new enrollmentDate();
$memberSetting = new memberSetting();
$error = "";
$decline_log_id = "";
/************************/
//WE ARE NOT USE THIS FILE
/************************/
$ws_id = isset($_REQUEST['ws_id']) ? $_REQUEST['ws_id'] : "";
$ws_row = $pdo->selectOne("SELECT * from website_subscriptions where md5(id)=:id",array(':id' => $ws_id));

$cust_sql = "SELECT * FROM customer WHERE id=:id";
$cust_row = $pdo->selectOne($cust_sql, array(":id" => $ws_row['customer_id']));

$customer_id = $ws_row['customer_id'];
$product_id = $ws_row['product_id'];
$plan_id = $ws_row['plan_id'];
$prdMatrixArr = array($ws_row['plan_id']);
$response = array();

$REAL_IP_ADDRESS = get_real_ipaddress();
if(isset($_POST['is_submit']) && $_POST['is_submit'] == 'Y'){

	$paid_date = isset($_POST['paid_date']) ? $_POST['paid_date'] : "";

	
	if(empty($paid_date)){
		$error = "Please select paid through date";
        $response['error'] = $error;
        $response['status'] = 'fail';
	}else{

		if(strtotime($ws_row['end_coverage_period']) != strtotime($paid_date)) {

            $cust_sql = "SELECT c.*,sp.id as customer_sponsor_id,sp.type as sponsor_type,sp.payment_master_id,sp.ach_master_id FROM customer c 
            JOIN customer sp ON sp.id=c.sponsor_id
            WHERE c.id=:id";
            $cust_where = array(":id" => $ws_row['customer_id']);
            $cust_row = $pdo->selectOne($cust_sql, $cust_where);
            $sponsor_id = $cust_row["customer_sponsor_id"];

            $sponsor_billing_method = "individual";
            $is_group_member = 'N';
            
            $sqlBillingType = "SELECT billing_type FROM customer_group_settings where customer_id=:customer_id";
            $resBillingType = $pdo->selectOne($sqlBillingType,array(":customer_id"=>$cust_row['customer_sponsor_id']));
            if(!empty($resBillingType['billing_type'])){
                $sponsor_billing_method = $resBillingType['billing_type'];
            }

            $date_selection_options = get_tier_change_date_selection_options($ws_row['id']);
            
            $final_coverage_periods = array();
            $take_charge = false;
            $take_charge_arr = array();
            $refund_charge_arr = array();
            foreach ($date_selection_options as $key => $value) {
                if(strtotime($ws_row['end_coverage_period']) < strtotime($paid_date)){
                    if(strtotime($value['end_coverage_period']) <= strtotime($ws_row['end_coverage_period']) || strtotime($value['end_coverage_period']) > strtotime($paid_date)){
                        continue;
                    }
                    $take_charge_arr[$key] = array('start_coverage_period' => $value['start_coverage_period'],'end_coverage_period' => $value['end_coverage_period']);
                    $take_charge = true;

                }else if(strtotime($ws_row['end_coverage_period']) > strtotime($paid_date)){
                    if(strtotime($value['end_coverage_period']) > strtotime($ws_row['end_coverage_period']) || strtotime($value['end_coverage_period']) <= strtotime($paid_date)){
                        continue;
                    }
                    $refund_charge_arr[$key] = array('start_coverage_period' => $value['start_coverage_period'],'end_coverage_period' => $value['end_coverage_period']);
                }
            }

            if(!empty($take_charge_arr) && $take_charge){
                $final_coverage_periods = $take_charge_arr;
            }else if(!empty($refund_charge_arr) && !$take_charge){
                $final_coverage_periods = $refund_charge_arr;
            }

            if($final_coverage_periods){
                foreach ($final_coverage_periods as $key => $value) {
                    if($sponsor_billing_method == "individual"){
                        if(!$take_charge){
                            $start_coverage_period = $value['start_coverage_period'];
                            $tmp_res = subscription_is_paid_for_coverage_period($ws_row['id'], $start_coverage_period);

                            if ($tmp_res['is_paid'] && !empty($tmp_res['order_id'])) {
                                $refunded_order_id = $tmp_res['order_id'];

                                $product_total = $new_plan_price;
                                $shipping_charge = 0;
                                $tax_charge = 0;
                                $discount = 0;
                                $is_refund = false;

                                if ($tmp_res['is_post_date_order'] == true) {
                                    //update old order status
                                    $is_post_date_order = true;
                                    $old_ord_data = array('status' => 'Cancelled', 'updated_at' => 'msqlfunc_NOW()');
                                    $old_ord_where = array("clause" => "id=:id", "params" => array(":id" => $refunded_order_id));
                                    $pdo->update("orders", $old_ord_data, $old_ord_where);

                                    $transactionInsId = $functionClass->transaction_insert($refunded_order_id, 'Debit', 'Cancelled', 'Transaction Cancelled');
                                    $is_refund = true;

                                } else {
                                    $res = cancel_order($refunded_order_id, array("ws_row" => $ws_row, 'description' => "Paid Through update", 'requested_by' => $requested_by, 'is_partial_refund' => 'Y'));
                                    
                                    if($res['status']){
                                        $is_refund = true;
                                    }
                                }

                                if($is_refund){
                                    $update_params = array(
                                        'start_coverage_period' => $value['start_coverage_period'],
                                        'end_coverage_period' => $value['end_coverage_period'],
                                        'updated_at' => 'msqlfunc_NOW()'
                                    );
                                    
                                    $update_where = array(
                                        'clause' => 'id = :id',
                                        'params' => array(
                                            ':id' => makeSafe($ws_row['id'])
                                        )
                                    );
                                    $response['status'] = 'success';
                                }else{
                                    $response['status'] = 'fail';
                                    $response['message'] = 'Refund Failed';
                                }
                            }
                        }else{
                            $grand_total = 0.00; //All Products Total (Include membership_Fee and linked_Fee)
                            $sub_total = 0.00;
                            $cobra_service_fee_total = 0.00;
                            $service_fee_total = 0.00;
                            $healthy_step_fee_total = 0.00;
                            $is_renewal = false;
                                
                            foreach ($value as $ws_row) {
                                $cobra_service_fee = get_cobra_service_fee($ws_row['price']);
                                $cobra_service_fee_total += $cobra_service_fee['fee'];
                                $sub_total += $ws_row['price'];
                                if (isset($ws_row['renew_count']) && $ws_row['renew_count'] > 1) {
                                    $is_renewal = true;
                                }
                            }

                            $grand_total = $sub_total + $cobra_service_fee_total;

                            $payment_date = date("Y-m-d");
                            
                            $bill_sql = "SELECT *, 
                                    AES_DECRYPT(ach_routing_number,'" . $CREDIT_CARD_ENC_KEY . "')as ach_routing_number, 
                                    AES_DECRYPT(ach_account_number,'" . $CREDIT_CARD_ENC_KEY . "')as ach_account_number, 
                                    AES_DECRYPT(card_no_full,'" . $CREDIT_CARD_ENC_KEY . "')as cc_no 
                                    FROM customer_billing_profile WHERE customer_id = :customer_id AND is_default = 'Y' and is_deleted = 'N'";
                            $bill_where = array(":customer_id" => $ws_row['customer_id']);
                            $bill_row = $pdo->selectOne($bill_sql, $bill_where);

                            $payment_master_id = 0;
                            // if ($bill_row['payment_mode'] == "ACH") {
                            //     $payment_master_id = $cust_row['ach_master_id'];
                            // } else {
                            //     $payment_master_id = $cust_row['payment_master_id'];
                            // }
                            $payment_master_id = $functionClass->get_agent_merchant_detail($prdMatrixArr,$cust_row['sponsor_id'],$bill_row['payment_mode'],array('is_renewal'=>$is_renewal,'customer_id'=>$customer_id));
                            $order_display_id = $functionClass->get_order_id();
                            $payment_approved = false;
                            $txn_id = 0;
                            $payment_processor = "";
                            if(!empty($payment_master_id)){
                                $payment_processor= getname('payment_master',$payment_master_id,'processor_id');
                            }

                            $payment_res = array();

                            if (strtotime(date('Y-m-d')) < strtotime($payment_date)) {
                                $payment_approved = true;
                                $is_post_date_order = true;
                            } else {
                                $is_post_date_order = false;

                                $cc_params = array();
                                $cc_params['customer_id'] = $cust_row['rep_id'];
                                $cc_params['order_id'] = $order_display_id;
                                $cc_params['amount'] = $grand_total;
                                $cc_params['description'] = "Cobra Reinstate order";
                                $cc_params['firstname'] = $bill_row['fname'];
                                $cc_params['lastname'] = $bill_row['lname'];
                                $cc_params['address1'] = $bill_row['address'];
                                $cc_params['city'] = $bill_row['city'];
                                $cc_params['state'] = $bill_row['state'];
                                $cc_params['zip'] = $bill_row['zip'];
                                $cc_params['country'] = 'USA';
                                $cc_params['phone'] = $cust_row['cell_phone'];
                                $cc_params['email'] = $cust_row['email'];
                                $cc_params['ipaddress'] = !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'];
                                $cc_params['processor'] = $payment_processor;

                                if ($bill_row['payment_mode'] == "ACH") {
                                    // $payment_master_id = $cust_row['ach_master_id'];
                                    $cc_params['ach_account_type'] = $bill_row['ach_account_type'];
                                    $cc_params['ach_routing_number'] = $bill_row['ach_routing_number'];
                                    $cc_params['ach_account_number'] = $bill_row['ach_account_number'];
                                    $cc_params['name_on_account'] = $bill_row['fname'] . ' ' . $bill_row['lname'];
                                    $cc_params['bankname'] = $bill_row['bankname'];
                                } else {
                                    // $payment_master_id = $cust_row['payment_master_id'];
                                    $cc_params['ccnumber'] = $bill_row['cc_no'];
                                    $cc_params['card_type'] = $bill_row['card_type'];
                                    $cc_params['ccexp'] = str_pad($bill_row['expiry_month'], 2, "0", STR_PAD_LEFT) . substr($bill_row['expiry_year'], -2);
                                }
                                
                                /*--- This is for testing----*/
                                $payment_res = array("status" => "Success","transaction_id" => 0,"message" => "Manual Approved");
                                if ($bill_row['payment_mode'] == "ACH") {
                                    $api = new CyberxPaymentAPI();
                                    $payment_res = $api->processPaymentACH($cc_params, $payment_master_id);
                                } else {
                                    if ($SITE_ENV != 'Live') {
                                        $payment_res = array('status' => 'Success', 'transaction_id' => 0, 'message' => "Manually Approved Order");
                                    } else {
                                        if ($cc_params['ccnumber'] == '4111111111111114') {
                                            $payment_res = array('status' => 'Success', 'transaction_id' => 0, 'message' => "Manually Approved Order");
                                        } else {
                                            $api = new CyberxPaymentAPI();
                                            $payment_res = $api->processPayment($cc_params,$payment_master_id);
                                        }
                                    }
                                }
                                /*---/This is for testing----*/

                                if ($payment_res['status'] == 'Success') {
                                    $payment_approved = true;
                                    $txn_id = $payment_res['transaction_id'];

                                    foreach ($value as $key => $ws_row) {
                                        $payment_approved_ws_ids[$ws_row['ws_id']] = array(
                                            'id' => $ws_row['ws_id'],
                                            'start_coverage_period' => $ws_row['start_coverage_period'],
                                            'end_coverage_period' => $ws_row['end_coverage_period'],
                                        );
                                    }
                                } else {
                                    $reinstate_payment_approved = false;
                                    $cc_params['order_type'] = 'Cobra Paid Through date changed';
                                    $cc_params['browser'] = $BROWSER;
                                    $cc_params['os'] = $OS;
                                    $cc_params['req_url'] = $REQ_URL;
                                    $cc_params['err_text'] = $payment_res['message'];
                                    $txn_id = $payment_res['transaction_id'];
                                    $decline_log_id = $functionClass->credit_card_decline_log($customer_id, $cc_params, $payment_res);
                                }
                            }

                            $order_data = array(
                                'display_id' => $order_display_id,
                                'customer_id' => $customer_id,
                                'transaction_id' => $txn_id,
                                'product_total' => $sub_total,
                                'cobra_service_fee' => $cobra_service_fee_total,
                                'sub_total' => $sub_total,
                                'grand_total' => $grand_total,
                                'type' => ($is_renewal == true ? ',Renewals,' : ',Customer Enrollment,'),
                                'payment_type' => $bill_row['payment_mode'],
                                'is_renewal' => 'N',
                                'payment_processor' => !empty($payment_res['API_Type']) ? $payment_res['API_Type'] : "Authorize.net",
                                'payment_processor_res' => json_encode($payment_res),
                                'site_load' => "USA",
                                'payment_master_id' => !empty($payment_master_id) ? $payment_master_id : '',
                                'post_date' => $payment_date,
                                'browser' => $BROWSER,
                                'os' => $OS,
                                'req_url' => $REQ_URL,
                                'ip_address' => !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'],
                                'original_order_date' => 'msqlfunc_NOW()',
                                'created_at' => 'msqlfunc_NOW()',
                                'updated_at' => 'msqlfunc_NOW()',
                                'is_reinstate_order' => 'Y',
                                'subscription_ids' => implode(',', array_column($value, 'ws_id')),
                                'order_count' => isset($value['renew_count']) && $value['renew_count'] > 1 ? $value['renew_count'] : 1,
                            );

                            $order_data['status'] = ($bill_row['payment_mode'] == "ACH") ? 'Pending Settlement' : 'Payment Approved';
                            if (!$payment_approved) {
                                $order_data['status'] = 'Payment Declined';
                            }
                            if ($is_post_date_order == true) {
                                $order_data['status'] = "Post Payment";
                                $order_data['future_payment'] = "Y";
                            }

                            if (isset($payment_res['review_require']) && $payment_res['review_require'] == 'Y') {
                                $order_data['review_require'] = 'Y';
                            }

                            $order_id = $pdo->insert("orders", $order_data);

                            {

                                $other_params = array("transaction_id"=>$txn_id,'transaction_response'=>$payment_res,'cc_decline_log_id'=>checkIsset($decline_log_id));

                                if ($payment_approved == true) {
                                    if ($bill_row['payment_mode'] != "ACH") {
                                        if ($is_renewal == true) {
                                            $transactionInsId = $functionClass->transaction_insert($order_id, 'Credit', 'Renewal Order', 'Renewal Transaction',0,$other_params);

                                        } else {
                                            $transactionInsId = $functionClass->transaction_insert($order_id, 'Credit', 'New Order', 'Transaction Approved',0,$other_params);
                                        }
                                    } else {
                                        $transactionInsId = $functionClass->transaction_insert($order_id, 'Credit', 'Pending', 'Settlement Transaction',0,$other_params);
                                    }

                                    /*---------- Billing Success Activity Feed ----------*/
                                    if($location == "admin") {
                                        $description['ac_message'] =array(
                                            'ac_red_1'=>array(
                                                'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
                                                'title'=>$_SESSION['admin']['display_id'],
                                            ),
                                            'ac_message_1' =>' cobra paid through date changed on ',
                                            'ac_red_2'=>array(
                                                'href'=> $ADMIN_HOST.'/members_details.php?id='.md5($cust_row['id']),
                                                'title'=> $cust_row['rep_id'],
                                            ),
                                            'ac_message_2' =>' transaction ',
                                            'ac_red_3'=>array(
                                                'title'=>$txn_id,
                                            ),
                                            'ac_message_3' => ($bill_row['payment_mode'] =='CC' ? ' Approved on Order ' : ' PENDING SETTLEMENT on Order '),
                                            'ac_red_4'=>array(
                                                'href'=>$ADMIN_HOST.'/all_orders.php?id='.md5($order_id),
                                                'title'=>$order_display_id,
                                            ),
                                        );
                                        activity_feed(3, $_SESSION['admin']['id'], 'Admin',$cust_row["id"], 'customer',"Successful Payment", $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));

                                    } elseif($location == "agent") {

                                        $description['ac_message'] =array(
                                            'ac_red_1'=>array(
                                                'href'=> 'agent_detail_v1.php?id='.md5($_SESSION['agents']['id']),
                                                'title'=> $_SESSION['agents']['rep_id'],
                                            ),
                                            'ac_message_1' =>' cobra paid through date changed on ',
                                            'ac_red_2'=>array(
                                                'href'=> 'members_details.php?id='.md5($cust_row['id']),
                                                'title'=> $cust_row['rep_id'],
                                            ),
                                            'ac_message_2' =>' transaction ',
                                            'ac_red_3'=>array(
                                                'title'=>$txn_id,
                                            ),
                                            'ac_message_3' => ($bill_row['payment_mode'] =='CC' ? ' Approved on Order ' : ' PENDING SETTLEMENT on Order '),
                                            'ac_red_4'=>array(
                                                'href'=>'all_orders.php?id='.md5($order_id),
                                                'title'=>$order_display_id,
                                            ),
                                        );
                                        activity_feed(3, $_SESSION['agents']['id'],'Agent',$cust_row["id"],'customer',"Successful Payment",'','',json_encode($description));
                                    } elseif($location == "group") {

                                        $description['ac_message'] =array(
                                            'ac_red_1'=>array(
                                                'href'=> $ADMIN_HOST.'/groups_details.php?id='.md5($_SESSION['groups']['id']),
                                                'title'=> $_SESSION['groups']['rep_id'],
                                            ),
                                            'ac_message_1' =>' cobra paid through date changed on ',
                                            'ac_red_2'=>array(
                                                'href'=> $ADMIN_HOST.'/members_details.php?id='.md5($cust_row['id']),
                                                'title'=> $cust_row['rep_id'],
                                            ),
                                            'ac_message_2' =>' transaction ',
                                            'ac_red_3'=>array(
                                                'title'=>$txn_id,
                                            ),
                                            'ac_message_3' => ($bill_row['payment_mode'] =='CC' ? ' Approved on Order ' : ' PENDING SETTLEMENT on Order '),
                                            'ac_red_4'=>array(
                                                'href'=>$ADMIN_HOST.'/all_orders.php?id='.md5($order_id),
                                                'title'=>$order_display_id,
                                            ),
                                        );
                                        activity_feed(3, $_SESSION['groups']['id'],'Group',$cust_row["id"],'customer',"Successful Payment",'','',json_encode($description));
                                    }
                                    /*----------/Billing Success Activity Feed ----------*/

                                } else {
                                    $transactionInsId=$functionClass->transaction_insert($order_id,'Failed','Payment Declined','Transaction Declined',0,$other_params);

                                    if($location == "admin") {
                                        $description['ac_message'] =array(
                                            'ac_red_1'=>array(
                                                'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
                                                'title'=>$_SESSION['admin']['display_id'],
                                            ),
                                            'ac_message_1' =>' cobra paid through date changed on ',
                                            'ac_red_2'=>array(
                                                'href'=> $ADMIN_HOST.'/members_details.php?id='.md5($cust_row['id']),
                                                'title'=> $cust_row['rep_id'],
                                            ),
                                            'ac_message_2' =>' transaction ',
                                            'ac_red_3'=>array(
                                                'title'=>$txn_id,
                                            ),
                                            'ac_message_3' => ' Payment Declined on Order ',
                                            'ac_red_4'=>array(
                                                'href'=>$ADMIN_HOST.'/all_orders.php?id='.md5($order_id),
                                                'title'=>$order_display_id,
                                            ),
                                        );
                                        activity_feed(3, $_SESSION['admin']['id'], 'Admin',$cust_row["id"], 'customer',"Billing Failed", $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));

                                    } elseif($location == "agent") {
                                        $description['ac_message'] =array(
                                            'ac_red_1'=>array(
                                                'href'=> 'agent_detail_v1.php?id='.md5($_SESSION['agents']['id']),
                                                'title'=> $_SESSION['agents']['rep_id'],
                                            ),
                                            'ac_message_1' =>' cobra paid through date changed on ',
                                            'ac_red_2'=>array(
                                                'href'=> 'members_details.php?id='.md5($cust_row['id']),
                                                'title'=> $cust_row['rep_id'],
                                            ),
                                            'ac_message_2' =>' transaction ',
                                            'ac_red_3'=>array(
                                                'title'=>$txn_id,
                                            ),
                                            'ac_message_3' => ' Payment Declined on Order ',
                                            'ac_red_4'=>array(
                                                'href'=> 'all_orders.php?id='.md5($order_id),
                                                'title'=> $order_display_id,
                                            ),
                                        );
                                        activity_feed(3, $_SESSION['agents']['id'],'Agent',$cust_row["id"], 'customer',"Billing Failed",'','',json_encode($description));
                                    } elseif($location == "group") {
                                        $description['ac_message'] =array(
                                            'ac_red_1'=>array(
                                                'href'=> $ADMIN_HOST.'/groups_details.php?id='.md5($_SESSION['groups']['id']),
                                                'title'=> $_SESSION['groups']['rep_id'],
                                            ),
                                            'ac_message_1' =>' cobra paid through date changed on ',
                                            'ac_red_2'=>array(
                                                'href'=> $ADMIN_HOST.'/members_details.php?id='.md5($cust_row['id']),
                                                'title'=> $cust_row['rep_id'],
                                            ),
                                            'ac_message_2' =>' transaction ',
                                            'ac_red_3'=>array(
                                                'title'=>$txn_id,
                                            ),
                                            'ac_message_3' => ' Payment Declined on Order ',
                                            'ac_red_4'=>array(
                                                'href'=> $ADMIN_HOST.'/all_orders.php?id='.md5($order_id),
                                                'title'=> $order_display_id,
                                            ),
                                        );
                                        activity_feed(3, $_SESSION['groups']['id'],'Group',$cust_row["id"], 'customer',"Billing Failed",'','',json_encode($description));
                                    }
                                }
                            }

                            $ord_bill_data = array(
                                'order_id' => $order_id,
                                'customer_id' => $cust_row['id'],
                                'fname' => $bill_row['fname'],
                                'lname' => $bill_row['lname'],
                                'email' => $cust_row['email'],
                                'country_id' => $bill_row['country_id'],
                                'country' => $bill_row['country'],
                                'state' => $bill_row['state'],
                                'city' => $bill_row['city'],
                                'zip' => $bill_row['zip'],
                                'phone' => $bill_row['phone'],
                                'address' => $bill_row['address'],
                                'last_cc_ach_no' => $bill_row['last_cc_ach_no'],
                                'created_at' => 'msqlfunc_NOW()',
                                'payment_mode' => $bill_row['payment_mode'],
                                'customer_billing_id' => $bill_row['id'],
                            );

                            if ($bill_row['payment_mode'] == "ACH") {
                                $ord_bill_data = array_merge($ord_bill_data, array(
                                    'ach_account_type' => $bill_row['ach_account_type'],
                                    'bankname' => $bill_row['bankname'],
                                    'ach_account_number' => "msqlfunc_AES_ENCRYPT('" . $bill_row['ach_account_number'] . "','" . $CREDIT_CARD_ENC_KEY . "')",
                                    'ach_routing_number' => "msqlfunc_AES_ENCRYPT('" . $bill_row['ach_routing_number'] . "','" . $CREDIT_CARD_ENC_KEY . "')",
                                ));
                            } else {
                                $ord_bill_data = array_merge($ord_bill_data, array(
                                    'card_no' => makeSafe(substr($bill_row['cc_no'], -4)),
                                    'card_no_full' => "msqlfunc_AES_ENCRYPT('" . $bill_row['cc_no'] . "','" . $CREDIT_CARD_ENC_KEY . "')",
                                    'cvv_no' => makeSafe($bill_row['cvv_no']),
                                    'card_type' => makeSafe($bill_row['card_type']),
                                    'expiry_month' => makeSafe($bill_row['expiry_month']),
                                    'expiry_year' => makeSafe($bill_row['expiry_year']),
                                ));
                            }
                            $pdo->insert("order_billing_info", $ord_bill_data);

                            $renew_count = 0;
                            foreach ($value as $key => $ws_row) {

                                $renew_count++;

                                $member_setting = $memberSetting->get_status_by_payment($payment_approved,"",$is_post_date_order,$cust_row['status']);

                                if($is_post_date_order == true) {
                                    $is_approved_payment = true;
                                } else {
                                    if($payment_approved == true) {
                                        $is_approved_payment = true;
                                    } else {
                                        $is_approved_payment = false;
                                    }
                                }

                                $cobra_service_fee = get_cobra_service_fee($ws_row['price']);

                                $order_detail_data = array(
                                    'order_id' => $order_id,
                                    'website_id' => $ws_row['ws_id'],
                                    'product_id' => $ws_row['product_id'],
                                    'fee_applied_for_product' => "",
                                    'plan_id' => $ws_row['plan_id'],
                                    'prd_plan_type_id' => $ws_row['plan_type'],
                                    'product_type' => $ws_row['product_type'],
                                    'product_name' => $ws_row['product_name'],
                                    'product_code' => $ws_row['product_code'],
                                    'start_coverage_period' => $ws_row['start_coverage_period'],
                                    'end_coverage_period' => $ws_row['end_coverage_period'],
                                    'qty' => 1,
                                    'renew_count' => $renew_count,
                                    'unit_price' => $ws_row['price'],
                                    'cobra_service_fee' => $cobra_service_fee['fee'],
                                    'family_member' => get_ws_family_member_count($ws_row['ws_id']),
                                );
                                $detail_insert_id = $pdo->insert("order_details", $order_detail_data);
                                

                                $ws_history_data = array(
                                    'customer_id' => $customer_id,
                                    'website_id' => $ws_row['ws_id'],
                                    'product_id' => $ws_row['product_id'],
                                    'fee_applied_for_product' => "",
                                    'plan_id' => $ws_row['plan_id'],
                                    'prd_plan_type_id' => $ws_row['plan_type'],
                                    'order_id' => $order_id,
                                    'status' => ($is_approved_payment == true ? "'Success'" : "Fail"),
                                    'message' => 'Cobra Reinstate Order',
                                    'authorize_id' => $txn_id,
                                    'processed_at' => 'msqlfunc_NOW()',
                                    'created_at' => 'msqlfunc_NOW()',
                                );
                                
                                $pdo->insert("website_subscriptions_history", $ws_history_data);

                                if ($is_approved_payment == true) {
                                    $cust_update_data = array(
                                        'status' => $member_setting['member_status'],
                                        'updated_at' => 'msqlfunc_NOW()',
                                    );
                                    $cust_update_where = array(
                                        "clause" => 'id=:id',
                                        'params' => array(
                                            ":id" => $customer_id,
                                        )
                                    );
                                    $pdo->update("customer", $cust_update_data, $cust_update_where);

                                    $ws_update_data = array(
                                        'last_order_id' => $order_id,
                                        'total_attempts' => 0,
                                        'next_attempt_at' => NULL,
                                        'status' => $member_setting['policy_status'],
                                        'payment_type' => $bill_row['payment_mode'],
                                        'renew_count' => $renew_count,
                                        'last_purchase_date' => 'msqlfunc_NOW()',
                                        'updated_at' => 'msqlfunc_NOW()',
                                    );

                                    if ($payment_approved) {
                                        $extra_params = array();
                                        $extra_params['location'] = "edit_paid_through_date";
                                        $policySetting->removeTerminationDate($ws_row['ws_id'],$extra_params);
                                    }

                                    // $coveragePeriod = $enrollDate->checkLastSuccessfullCoverage($order_id);
                                    // if (!empty($coveragePeriod)) {
                                    $start_coverage_period = $ws_row['start_coverage_period'];
                                    $end_coverage_period = $ws_row['end_coverage_period'];
                                    if (!empty($start_coverage_period)) {
                                        $ws_update_data['start_coverage_period'] = $start_coverage_period;
                                    }
                                    if (!empty($end_coverage_period)) {
                                        $ws_update_data['end_coverage_period'] = $end_coverage_period;
                                    }
                                    // }

                                    $ws_update_where = array(
                                        "clause" => 'id=:id',
                                        'params' => array(
                                            ":id" => $ws_row['ws_id'],
                                        )
                                    );
                                    $pdo->update("website_subscriptions", $ws_update_data, $ws_update_where);


                                    /*------- Customer Dependent ----------*/
                                    $cd_update_data = array(
                                        'terminationDate' => NULL,
                                        'status' => $member_setting['policy_status'],
                                    );
                                    $cd_update_where = array(
                                        "clause" => 'website_id=:id',
                                        'params' => array(
                                            ":id" => $ws_row['id'],
                                        )
                                    );
                                    $pdo->update("customer_dependent", $cd_update_data, $cd_update_where);
                                } else {
                                    
                                    $ws_update_data = array(
                                        'last_order_id' => $order_id,
                                        'total_attempts' => 0,
                                        'next_attempt_at' => NULL,
                                        'last_purchase_date' => 'msqlfunc_NOW()',
                                        'payment_type' => $bill_row['payment_mode'],
                                        'updated_at' => 'msqlfunc_NOW()',
                                    );
                                    $ws_update_where = array(
                                        "clause" => 'id=:id',
                                        'params' => array(
                                            ":id" => $ws_row['id'],
                                        )
                                    );
                                    $pdo->update("website_subscriptions", $ws_update_data, $ws_update_where);
                                    
                                }
                            }

                            //********* Payable Insert Code Start ********************
                            if ($payment_approved == true && $is_post_date_order != true && $bill_row['payment_mode'] != "ACH") {
                                $payable_params = array(
                                    'payable_type' => 'Vendor',
                                    'type' => 'Vendor',
                                    'transaction_tbl_id' => $transactionInsId['id'],
                                );
                                $payable = $functionClass->payable_insert($order_id, 0, 0, 0, $payable_params);
                            }

                            if ($payment_approved == false) {
                                $payment_error = $payment_res['message'];

                                //Update termination date as per last approved payment for coverage
                                if (!empty($payment_approved_ws_ids)) {
                                    foreach ($payment_approved_ws_ids as $key => $pay_app_ws) {
                                        $termination_date = $enrollDate->getTerminationDate($pay_app_ws['id']);

                                        // If coverage period is end on past date or today then inactive immidiate
                                        $member_setting = $memberSetting->get_status_by_payment($payment_approved,$pay_app_ws['end_coverage_period']);
                                        if (strtotime($pay_app_ws['end_coverage_period']) <= strtotime($today)) {
                                            $extra_params = array();
                                            $extra_params['location'] = "edit_paid_through_date";
                                            $termination_reason = "Failed Billing";
                                            $policySetting->setTerminationDate($pay_app_ws['id'],$termination_date,$termination_reason,$extra_params);
                                        } else {
                                            $attempt_sql = "SELECT * FROM prd_subscription_attempt
                                                   WHERE attempt=:attempt AND is_deleted='N'";
                                            $attempt_where = array(":attempt" => 1);
                                            $attempt_row = $pdo->selectOne($attempt_sql, $attempt_where);

                                            $ws_update_data = array(
                                                'status' => $member_setting['policy_status'],
                                                'total_attempts' => '1',
                                                'next_attempt_at' => date('Y-m-d', strtotime("+" . $attempt_row['attempt_frequency'] . " " . $attempt_row['attempt_frequency_type'])),
                                                'updated_at' => 'msqlfunc_NOW()',
                                            );

                                            $ws_update_where = array(
                                                "clause" => 'id=:id',
                                                'params' => array(
                                                    ":id" => $pay_app_ws['id'],
                                                )
                                            );
                                            $pdo->update("website_subscriptions", $ws_update_data, $ws_update_where);
                                        }

                                        /*------- Customer Dependent ----------*/
                                        $cd_update_data = array();
                                        $cd_update_data['terminationDate'] = $termination_date;
                                        // If coverage period is end on past date or today then inactive immidiate
                                        if (strtotime($pay_app_ws['end_coverage_period']) <= strtotime($today)) {
                                            $cd_update_data['status'] = $member_setting['policy_status'];
                                        }
                                        $cd_update_where = array(
                                            "clause" => 'website_id=:id',
                                            'params' => array(
                                                ":id" => $pay_app_ws['id'],
                                            )
                                        );
                                        $pdo->update("customer_dependent", $cd_update_data,$cd_update_where);
                                    }
                                }

                                //Stop Reinstate Processing
                                break;
                            }
                        }

                    }else{
                        $start_coverage_period = $value['start_coverage_period'];
                        $end_coverage_period = $value['end_coverage_period'];

                        $tmp_sql = "SELECT lb.id as lb_id,lbd.id as lbd_id,lbd.amount 
                        FROM list_bills lb 
                        JOIN list_bill_details lbd ON(lbd.list_bill_id = lb.id)
                        WHERE lb.is_deleted='N' AND lbd.ws_id=:ws_id AND lbd.start_coverage_period = :start_coverage_period AND lbd.end_coverage_period = :end_coverage_period GROUP BY lb.id";
                        $tmp_where = array(":ws_id"=>$ws_row['id'],":start_coverage_period"=>$start_coverage_period,":end_coverage_period" => $value['end_coverage_period']);

                        $tmp_rows = $pdo->select($tmp_sql,$tmp_where);
                        if(!empty($tmp_rows)) {
                            foreach ($tmp_rows as $key => $tmp_row) {
                                /*--- Refund Old Policy ---*/
                                $refund_data = array(
                                    'customer_id' => $ws_row['customer_id'],
                                    'ws_id' => $ws_row['id'],
                                    'old_ws_id' => $ws_row['id'],
                                    'group_id' => $customer_row['sponsor_id'],
                                    'transaction_type' => 'refund',
                                    'transaction_amount' => $tmp_row['amount'],
                                    'payment_received_from' => $tmp_row['lb_id'],
                                    'payment_received_details_id' => $tmp_row['lbd_id'],
                                    'description' => "Paid Through date changed",
                                    'ip_address' => !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'],
                                    'req_url' => $REQ_URL,
                                    'created_at' => 'msqlfunc_NOW()',
                                    'updated_at' => 'msqlfunc_NOW()',
                                );
                                $pdo->insert('group_member_refund_charge',$refund_data);

                                // $charge_data = array(
                                //     'customer_id' => $ws_row['customer_id'],
                                //     'ws_id' => $new_ws_row['id'],
                                //     'old_ws_id' => $ws_row['id'],
                                //     'group_id' => $customer_row['sponsor_id'],
                                //     'transaction_type' => 'charge',
                                //     'transaction_amount' => $new_plan_row['price'],
                                //     'payment_received_from' => $tmp_row['lb_id'],
                                //     'payment_received_details_id' => $tmp_row['lbd_id'],
                                //     'description' => $description,
                                //     'ip_address' => $_SERVER['REMOTE_ADDR'],
                                //     'req_url' => $REQ_URL,
                                //     'created_at' => 'msqlfunc_NOW()',
                                //     'updated_at' => 'msqlfunc_NOW()',
                                // );
                                // $pdo->insert('group_member_refund_charge',$charge_data);
                                /*--- Charge New Policy ---*/
                            }
                        }
                    }
                }
            }
            



        }
        // setNotifySuccess('');

        $response['status'] = 'success';
        $response['paid_date'] = date('m/d/Y',strtotime($paid_date));
        $response['product_id'] =$product_id;
        $response['message'] ='Paid Through Date has been updated.';

        // echo "<script>setTimeout(function(){ window.parent.$.colorbox.close(); }, 1000);</script>";

	}
    echo json_encode($response);
    exit();
}
/************************/
//WE ARE NOT USE THIS FILE
/************************/
$date_selection_options = get_tier_change_date_selection_options($ws_row['id']);
$extra = array();
// $coverage_period_data = $MemberEnrollment->get_coverage_period(array($product_id),$cust_row['sponsor_id'],$extra);
// $coverage_period_data = $coverage_period_data[$product_id];
$template = 'edit_paid_through_date.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>