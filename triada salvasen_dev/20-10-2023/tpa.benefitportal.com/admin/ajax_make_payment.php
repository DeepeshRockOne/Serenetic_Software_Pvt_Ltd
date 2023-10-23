<?php
/*------------------ 

We are not using this file now......
Please check below one
F:\xampp\htdocs\operation29.com\ajax_make_payment.php
------------------*/ 


include_once 'layout/start.inc.php';
include_once dirname(__DIR__) . '/includes/cyberx_payment_class.php';
include_once dirname(__DIR__) . '/includes/enrollment_dates.class.php';
include_once dirname(__DIR__) . '/includes/function.class.php';
include_once dirname(__DIR__) . '/includes/member_enrollment.class.php';
include_once dirname(__DIR__) . '/includes/member_setting.class.php';
$enrollDate = new enrollmentDate();
$functionClass = new functionsList();
$MemberEnrollment = new MemberEnrollment();
$memberSetting = new memberSetting();

$validate = new Validation();
$BROWSER = getBrowser();
$OS = getOS($_SERVER['HTTP_USER_AGENT']);
$REQ_URL = ($_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
$today = date("Y-m-d");

$stepFeeRow = array();
$response = array();
$admin_id = $_SESSION['admin']['id'];

$step = $_POST['step'];
$customer_id = $_POST['customer_id'];
$payment_products = isset($_POST['payment_products']) ? array_keys($_POST['payment_products']) : "";

$REAL_IP_ADDRESS = get_real_ipaddress();
$custSql = "SELECT c.*,sp.id as customer_sponsor_id,sp.payment_master_id,sp.ach_master_id 
            FROM customer c 
            JOIN customer sp ON sp.id=c.sponsor_id
            WHERE c.id=:id";
$custWhere = array(":id"=>$customer_id);
$custRes = $pdo->selectOne($custSql,$custWhere);
$sponsor_id = $custRes["customer_sponsor_id"];

$custBillSql = "SELECT *,
                AES_DECRYPT(card_no_full,'".$CREDIT_CARD_ENC_KEY."')as cc_no,
                AES_DECRYPT(ach_account_number,'".$CREDIT_CARD_ENC_KEY."')as ach_account_number,
                AES_DECRYPT(ach_routing_number,'".$CREDIT_CARD_ENC_KEY."')as ach_routing_number 
            FROM customer_billing_profile WHERE customer_id=:customer_id";
$custBillWhere = array(":customer_id" => $customer_id);
$custBillRes = $pdo->select($custBillSql, $custBillWhere);

$defaultBillingSql = "SELECT id FROM customer_billing_profile WHERE customer_id=:customer_id AND is_default='Y'";
$defaultBillingWhere = array(":customer_id" => $customer_id);
$default_cb_row = $pdo->selectOne($defaultBillingSql, $defaultBillingWhere);

$billingDate = $functionClass->getCustomerBillingDate($customer_id);
$stepFeeRow = $functionClass->getMemberHealthyStepFee($customer_id);
$coverage_subscriptions = array();

if($step >= 1) {
    if(empty($payment_products)) {
        $validate->setError('payment_products','Please select product(s).');
    } else {
        $subscriptions_coverage_periods = $functionClass->getCoveragePeriodsForPayments($customer_id,$payment_products);
        if(!empty($subscriptions_coverage_periods)){
            foreach ($subscriptions_coverage_periods as $coverageKey => $row) {
                $coverage_subscriptions[$row["renew_count"]]["start_coverage_period"] = $row["start_coverage_period"];
                $coverage_subscriptions[$row["renew_count"]]["end_coverage_period"] = $row["end_coverage_period"];
                $coverage_subscriptions[$row["renew_count"]]["coverage_billing_date"] = $row["coverage_billing_date"];
                $coverage_subscriptions[$row["renew_count"]]["renew_count"] = $row["renew_count"];
                $coverage_subscriptions[$row["renew_count"]]["is_approved_payment"] = $row["is_approved_payment"];
                $coverage_subscriptions[$row["renew_count"]]["coverage_service_fee"] = $row["coverage_service_fee"];
                foreach ($row["ws_res"] as $key => $wsRow) {
                    $coverage_subscriptions[$row["renew_count"]]["ws_res"][] = $wsRow;
                }
            }
        }
    }
}

if($step >= 2) {
    $coverage_payments = isset($_POST['coverage_payments']) ? $_POST['coverage_payments'] : array();
    $coverage_periods_data = isset($_POST['coverage_periods']) ? $_POST['coverage_periods'] : array();
    if(empty($coverage_payments)){
         $validate->setError('coverage_payments','Please Select Coverage');
    }else{
        foreach ($coverage_payments as $key => $covPeriod) {
            $startCoveragePeriod = $coverage_subscriptions[$covPeriod]["start_coverage_period"];
            $tmp_index = strtotime($startCoveragePeriod);

            $validate->string(array('required' => true, 'field' => 'payment_date_'.$tmp_index, 'value' => $coverage_periods_data[$tmp_index]['payment_date']), array('required' => 'Please select payment date.'));

            $validate->string(array('required' => true, 'field' => 'billing_profile_'.$tmp_index, 'value' => $coverage_periods_data[$tmp_index]['billing_profile']), array('required' => 'Please select payment method.'));

            if($coverage_periods_data[$tmp_index]['billing_profile'] == "new_payment_method") {
                $validate->setError('billing_profile_'.$tmp_index,'Please save billing profile.');
            }
        }
    }
}

if ($validate->isValid()) {

    /*------ Load Coverage Periods Code Start --------*/
    if($step == 1) {
        if($_POST['is_subscription_changed'] == "Y") {
            $optionVal = array();
            $optionHtml = '';
            $coverage_periods_html = '';
            $coverage_cnt = 1;
            $last_coverage_end_date = date("m/d/Y");

            foreach ($coverage_subscriptions as $coverageKey => $coverageRow) {
                if($coverageRow['is_approved_payment'] == true) {
                    continue;
                }

                ob_start();
                include "tmpl/make_payment_coverage_period.inc.php";
                $coverage_periods_html .= ob_get_clean();
                $last_coverage_end_date = date("m/d/Y",strtotime($coverageRow['end_coverage_period']));
                $coverage_cnt++;

                array_push($optionVal,$coverageKey);

                
            }
            if(!empty($optionVal)){
                $optionVal = array_unique($optionVal);
                foreach ($optionVal as $key => $coverage) {
                    $optionHtml .= "<option value='".$coverage."'>P".$coverage."</option>";
                }
            }
           
            $response['options_html'] = $optionHtml;
            $response['coverage_periods_html'] = $coverage_periods_html;
            $response['last_coverage_end_date'] = $last_coverage_end_date;
        }
    }
    /*------ Load Coverage Periods Code Ends --------*/

    /*------ Load Payment Summary Code Start --------*/
    if($step == 2) {
        $payment_billing_summary = "";
        $payment_next_billing_summary = "";

        ob_start();
        include "tmpl/payment_billing_summary.inc.php";
        $payment_billing_summary .= ob_get_clean();
        $response['payment_billing_summary'] = $payment_billing_summary;
     
        ob_start();
        include "tmpl/payment_next_billing_summary.inc.php";
        $payment_next_billing_summary .= ob_get_clean();

        $response['payment_next_billing_summary'] = $payment_next_billing_summary;
    }
    /*------ Load Payment Summary Code Ends --------*/

    if($step == 3) {
    
        $coverage_index = 0;
        $reinstate_payment_approved = true;
        $payment_error = "";

        $payment_approved_ws_ids = array();
        $temp_coverage = array();
        foreach ($coverage_payments as $key => $covPeriod) {

            $startCoveragePeriod = $coverage_subscriptions[$covPeriod]["start_coverage_period"];
            $endCoveragePeriod = $coverage_subscriptions[$covPeriod]["end_coverage_period"];
            $renewalCounter = $covPeriod - 1;
            
            $tmp_index = strtotime($startCoveragePeriod);
            
            $wsRes = $coverage_subscriptions[$covPeriod]["ws_res"];
            $serviceFeeRow = $coverage_subscriptions[$covPeriod]["coverage_service_fee"];

            $coverageBillingDate = $coverage_subscriptions[$covPeriod]["coverage_billing_date"];

            $sub_total = 0.00;
            $service_fee = 0.00;
            $is_renewal = false;
            $is_selected_enrollment_fee = false;
            $is_enrollment_fee_added = false;
            $coverage_payment_approved = true;

            if($covPeriod > 1){
                $is_renewal = true;
            }
     
            $PlanIdArr = array();
            foreach ($wsRes as $key => $wsData) {
                if(isset($wsData['is_approved_payment']) && $wsData["is_approved_payment"] == true){
                }else {
                    $coverage_payment_approved = false;
                    $payment_date = isset($coverage_periods_data[$tmp_index]['payment_date']) ? $coverage_periods_data[$tmp_index]['payment_date'] : "";
                    $payment_date = date("Y-m-d",strtotime($payment_date));
                    $billing_id = isset($coverage_periods_data[$tmp_index]['billing_profile']) ? $coverage_periods_data[$tmp_index]['billing_profile'] : "";
                    $sub_total += $wsData['price'];
                    
                    $PlanIdArr[] = $wsData["plan_id"];
                }
            }
            $grand_total = $sub_total;

            if(!empty($coverage_periods_data[$tmp_index]['enrollment_fee'])) {
                if(!$is_enrollment_fee_added){                                
                    $is_selected_enrollment_fee = true;
                    $is_enrollment_fee_added = true;
                    $stepFeeRow['renew_count'] = $renewalCounter;
                    $stepFeeRow['is_approved_payment'] = false;
                    $wsRes[] = $stepFeeRow;
                    $grand_total += $stepFeeRow["price"];
                }
            }

            if(!empty($coverage_periods_data[$tmp_index]['service_fee']) && !empty($serviceFeeRow[0])) {

                $grand_total += $serviceFeeRow["total"];

                $selService = "SELECT * FROM website_subscriptions WHERE customer_id=:customer_id AND product_id=:product_id ORDER BY id DESC";
                $resService = $pdo->selectOne($selService,array(":customer_id"=>$customer_id,":product_id"=>$serviceFeeRow[0]["product_id"]));
                
                if(!empty($resService)){
                    $serviceFeeRow[0]["id"] = $resService["id"];    
                    $serviceFeeRow[0]["plan_id"] = $resService["plan_id"];    
                    $serviceFeeRow[0]["website_id"] = $resService["website_id"];    
                    $serviceFeeRow[0]["customer_id"] = $resService["customer_id"]; 
                    $serviceFeeRow[0]["eligibility_date"] = $resService["eligibility_date"];  
                    $serviceFeeRow[0]["issued_state"] = $resService["issued_state"];     
                    
                }else{
                    $selService = "SELECT * FROM website_subscriptions WHERE customer_id=:customer_id AND product_id=:product_id ORDER BY id DESC";
                    $resService = $pdo->selectOne($selService,array(":customer_id"=>$customer_id,":product_id"=>$serviceFeeRow[0]["fee_product_id"]));
                    if(!empty($resService)){
                        $serviceFeeRow[0]["eligibility_date"] = $resService["eligibility_date"];  
                        $serviceFeeRow[0]["issued_state"] = $resService["issued_state"];  
                        $serviceFeeRow[0]["website_id"] = $functionClass->get_website_id();
                    }
                    $serviceFeeRow[0]["plan_id"] = $serviceFeeRow[0]["matrix_id"];  
                }

                $serviceFeeRow[0]["start_coverage_period"] = $startCoveragePeriod;  
                $serviceFeeRow[0]["end_coverage_period"] = $endCoveragePeriod;  
                $serviceFeeRow[0]["prd_plan_type_id"] = 0;    
                $serviceFeeRow[0]["plan_price"] = $serviceFeeRow[0]["price"];
                $serviceFeeRow[0]['renew_count'] = $renewalCounter;
                $serviceFeeRow[0]['is_approved_payment'] = false;
                $serviceFeeRow[0]['qty'] = 1;
                $serviceFeeRow[0]["product_type"] = "Fees";  
                $wsRes[] = $serviceFeeRow[0];
            }
          
            if($coverage_payment_approved){
                continue;
            }
         
            $bill_sql = "SELECT *, 
                        AES_DECRYPT(ach_routing_number,'" . $CREDIT_CARD_ENC_KEY . "')as ach_routing_number, 
                        AES_DECRYPT(ach_account_number,'" . $CREDIT_CARD_ENC_KEY . "')as ach_account_number, 
                        AES_DECRYPT(card_no_full,'" . $CREDIT_CARD_ENC_KEY . "')as cc_no 
                        FROM customer_billing_profile WHERE id=:id";
            $bill_where = array(":id" => $billing_id);
            $bill_row = $pdo->selectOne($bill_sql, $bill_where);
            $payment_mode = $bill_row["payment_mode"];
          
            $order_display_id = $functionClass->get_order_id();
            $payment_approved = false;
            $txn_id = 0;
            $payment_master_id = 0;
           
            // Take Charge of Coverage Period Code Start
                if(strtotime(date('Y-m-d')) < strtotime($payment_date)) {
                    $payment_approved = true;
                    $is_post_date_order = true;
                } else {
                    $is_post_date_order = false;
                    $sale_type_params = array();
                    if($is_renewal == true){
                        $sale_type_params['is_renewal'] = 'Y';
                        $sale_type_params['customer_id'] = $customer_id;
                    }
                    $payment_master_id = $functionClass->get_agent_merchant_detail($PlanIdArr, $sponsor_id, $payment_mode,$sale_type_params);

                    $payment_processor = "";
                    if(!empty($payment_master_id)){
                        $payment_processor= getname('payment_master',$payment_master_id,'processor_id');
                    }

                    $api = new CyberxPaymentAPI();

                    $cc_params = array();
                    $cc_params['customer_id'] = $custRes["rep_id"];
                    $cc_params['order_id'] = $order_display_id;
                    $cc_params['amount'] = $grand_total;
                    $cc_params['description'] = "Make Payment";
                    $cc_params['firstname'] = $bill_row['fname'];
                    $cc_params['lastname'] = $bill_row['lname'];
                    $cc_params['address1'] = $bill_row['address'];
                    $cc_params['city'] = $bill_row['city'];
                    $cc_params['state'] = $bill_row['state'];
                    $cc_params['zip'] = $bill_row['zip'];
                    $cc_params['country'] = $bill_row['country'];
                    $cc_params['phone'] = $custRes['cell_phone'];
                    $cc_params['email'] = $custRes['email'];
                    $cc_params['ipaddress'] = !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'];
                    $cc_params['processor'] = $payment_processor;

                    if ($payment_mode == "ACH") {
                        $cc_params['ach_account_type'] = $bill_row['ach_account_type'];
                        $cc_params['ach_routing_number'] = $bill_row['ach_routing_number'];
                        $cc_params['ach_account_number'] = $bill_row['ach_account_number'];
                        $cc_params['name_on_account'] = $bill_row['fname'].' '.$bill_row['lname'];
                        $cc_params['bankname'] = $bill_row['bankname'];

                        $payment_res = $api->processPaymentACH($cc_params, $payment_master_id);


                        if ($payment_res['status'] == 'Success') {
                            $payment_approved = true;
                            $txn_id = $payment_res['transaction_id'];
                        } else {
                            $payment_approved = false;
                            $txn_id = $payment_res['transaction_id'];
                            $payment_error = $payment_res['message'];
                            $cc_params['order_type'] = 'Quote';
                            $cc_params['browser'] = $BROWSER;
                            $cc_params['os'] = $OS;
                            $cc_params['req_url'] = $REQ_URL;
                            $cc_params['err_text'] = $payment_error;
                            credit_card_decline_log($customer_id, $cc_params, $payment_res);
                        }
                    } elseif ($payment_mode == "CC") {
                        $cc_params['ccnumber'] = $bill_row['cc_no'];
                        $cc_params['card_type'] = $bill_row['card_type'];
                        $cc_params['ccexp'] = str_pad($bill_row['expiry_month'], 2, "0", STR_PAD_LEFT) . substr($bill_row['expiry_year'], -2);
                        if ($cc_params['ccnumber'] == '4111111111111114') {
                            $payment_approved = true;
                            $txn_id = 0;
                            $payment_res = array("status" => "Success","transaction_id" => 0,"message" => "Manual Approved");
                        } else {
                            $payment_res = $api->processPayment($cc_params, $payment_master_id);
                            if ($payment_res['status'] == 'Success') {
                                $payment_approved = true;
                                $txn_id = $payment_res['transaction_id'];
                            } else {
                                $payment_approved = false;
                                $txn_id = $payment_res['transaction_id'];
                                $payment_error = $payment_res['message'];
                                $cc_params['order_type'] = 'Quote';
                                $cc_params['browser'] = $BROWSER;
                                $cc_params['os'] = $OS;
                                $cc_params['req_url'] = $REQ_URL;
                                $cc_params['err_text'] = $payment_error;
                                credit_card_decline_log($customer_id, $cc_params, $payment_res);
                            }
                        }
                    }
                }
            // Take Charge of Coverage Period Code Ends
            
            // Order Table code Start
                $orderParams = array(
                    'display_id' => $order_display_id,
                    'customer_id' => $customer_id,
                    'created_at' => 'msqlfunc_NOW()',
                    'original_order_date' => 'msqlfunc_NOW()',
                    'type' => ",Customer Enrollment,",
                    'ip_address' => !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'],
                    'browser' => $BROWSER,
                    'os' => $OS,
                    'req_url' => $REQ_URL,
                    'updated_at' => 'msqlfunc_NOW()',
                    'created_at' => 'msqlfunc_NOW()',
                    'product_total' => $sub_total,
                    'sub_total' =>$sub_total,
                    'grand_total' => $grand_total,
                    'order_count'=>$covPeriod,
                    'is_renewal'=>($is_renewal == true ? "Y" : "N"),
                );

                $orderParams['status'] = ($payment_mode == "ACH") ? 'Pending Settlement' : 'Payment Approved';
                if (!$payment_approved) {
                    $orderParams['status'] = 'Payment Declined';
                }

                if ($is_post_date_order == true) {
                    $orderParams['status'] = 'Post Payment';
                    $orderParams['post_date'] = date("Y-m-d", strtotime($payment_date));
                    $orderParams['future_payment'] = 'Y';
                }else{
                    $orderParams['transaction_id'] = $txn_id;
                    $orderParams['payment_type'] = $payment_mode;
                    $orderParams['payment_master_id'] = $payment_master_id;
                    $orderParams['payment_processor'] = $payment_processor;
                    $orderParams['payment_processor_res'] = isset($payment_res)?json_encode($payment_res):"";
                }
           
                if(isset($payment_res['review_require']) && $payment_res['review_require'] == 'Y'){
                    $orderParams['review_require'] = 'Y';
                }
                $order_id = $pdo->insert("orders", $orderParams);
               
                
      
            // Order Table code Ends

            // Billing Profile Table code Start
                $billParams = array(
                    'order_id' => $order_id,
                    'customer_id' => $customer_id,
                    'fname' => $bill_row['fname'],
                    'lname' => $bill_row['lname'],
                    'email' => $custRes['email'],
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
                
                if($bill_row['payment_mode'] == "ACH"){
                    $billParams = array_merge($billParams,array(
                        'ach_account_type' => $bill_row['ach_account_type'],
                        'bankname' => $bill_row['bankname'],
                        'ach_account_number' => "msqlfunc_AES_ENCRYPT('" . $bill_row['ach_account_number'] . "','" . $CREDIT_CARD_ENC_KEY . "')",
                        'ach_routing_number' => "msqlfunc_AES_ENCRYPT('" . $bill_row['ach_routing_number'] . "','" . $CREDIT_CARD_ENC_KEY . "')",
                    ));
                } else {
                    $billParams = array_merge($billParams,array(
                        'card_no' => makeSafe(substr($bill_row['cc_no'], -4)),
                        'card_no_full' => "msqlfunc_AES_ENCRYPT('" . $bill_row['cc_no'] . "','" . $CREDIT_CARD_ENC_KEY . "')",
                        'cvv_no' => makeSafe($bill_row['cvv_no']),
                        'card_type' => makeSafe($bill_row['card_type']),
                        'expiry_month' => makeSafe($bill_row['expiry_month']),
                        'expiry_year' => makeSafe($bill_row['expiry_year']),
                    ));
                }
                $orderBillingId = $pdo->insert("order_billing_info", $billParams);


                $billParams['is_default'] = 'Y';
                $billParams['ip_address'] = !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'];
                $billParams['updated_at'] = 'msqlfunc_NOW()';

                unset($billParams['order_id']);
                unset($billParams['customer_billing_id']);
                $isCustomerBillingExists = $pdo->selectOne("SELECT id FROM customer_billing_profile WHERE customer_id = :customer_id and is_deleted='N'", array(':customer_id' => $customer_id));
                if (empty($isCustomerBillingExists)) {
                    $pdo->insert("customer_billing_profile", $billParams);
                } else {
                    $pdo->update("customer_billing_profile", $billParams, array("clause" => "customer_id=:customer_id", "params" => array(":customer_id" => $customer_id)));
                }
            // Billing Profile Table code Ends
          
            // Order Detail Table code Start
                $subscription_ids = array();
                
                foreach ($wsRes as $key => $wsData) {
                    if(isset($wsData['is_approved_payment']) && $wsData["is_approved_payment"] == true){
                        continue;
                    }

                    $statuses = $memberSetting->get_status_by_payment($payment_approved,$wsData['end_coverage_period']);

                    if($payment_approved == true){
                        $cust_update_data = array(
                            'status' => $statuses['member_status'],
                            'updated_at' => 'msqlfunc_NOW()',
                        );
                        $cust_update_where = array(
                            "clause" => 'id=:id', 
                            'params' => array(
                                ":id" => $customer_id,
                            )
                        );
                        $pdo->update("customer",$cust_update_data, $cust_update_where);

                        $ws_update_data = array(
                            'last_order_id' => $order_id,
                            'total_attempts' => 0,
                            'termination_date' => NULL,
                            'term_date_set' => NULL,
                            'termination_reason' => NULL,
                            'next_attempt_at' => NULL,
                            'last_purchase_date' => 'msqlfunc_NOW()',
                            'status' => $statuses['policy_status'],
                            'payment_type' => $wsData['payment_type'],
                            'renew_count' => $wsData['renew_count'],
                            'updated_at' => 'msqlfunc_NOW()',
                        );

                        if(!empty($wsData['id'])){
                            $ws_update_where = array(
                                "clause" => 'id=:id', 
                                'params' => array(
                                    ":id" => $wsData['id'],
                                )
                            );
                            $pdo->update("website_subscriptions", $ws_update_data, $ws_update_where);
                        }else{
                            $ws_update_data["price"] = $wsData["price"];
                            $ws_update_data["qty"] = $wsData["qty"];
                            $ws_update_data["plan_id"] = $wsData["plan_id"];
                            $ws_update_data["issued_state"] = $wsData["issued_state"];
                            $ws_update_data["eligibility_date"] = $wsData["eligibility_date"];
                            $ws_update_data["product_type"] = $wsData["product_type"];
                            $ws_update_data["website_id"] = $wsData["website_id"];
                            $ws_update_data["product_id"] = $wsData["product_id"];
                            $ws_update_data["product_code"] = $wsData["product_code"];
                            $websiteInsId = $pdo->insert("website_subscriptions", $ws_update_data);
                            $wsData["id"] = $websiteInsId;
                        }

                        $cd_update_data = array(
                            'terminationDate' => NULL,
                            'status' => $statuses['member_status'],
                        );
                        $cd_update_where = array(
                            "clause" => 'website_id=:id', 
                            'params' => array(
                                ":id" => $wsData['id'],
                            )
                        );
                        $pdo->update("customer_dependent", $cd_update_data, $cd_update_where);
                        $coverage_index++;
                    }else{
                        $payment_error = $payment_res['message'];

                        // If coverage period is end on past date or today then inactive immidiate
                        if(strtotime($wsData['end_coverage_period']) <= strtotime($today)) {
                            $cust_update_data = array(
                                'status' => $statuses['member_status'],
                                'updated_at' => 'msqlfunc_NOW()',
                            );
                            $cust_update_where = array(
                                "clause" => 'id=:id', 
                                'params' => array(
                                    ":id" => $customer_id,
                                )
                            );
                            $pdo->update("customer",$cust_update_data, $cust_update_where);

                            $termination_date=$enrollDate->getTerminationDate($wsData['id']);
                                    
                            $ws_update_data = array(
                                'status' => $statuses['policy_status'],
                                'updated_at' => 'msqlfunc_NOW()',
                            );
                            $ws_update_data['termination_date'] = $termination_date;
                            $ws_update_data['term_date_set'] = date('Y-m-d');
                            if(!empty($wsData['id'])){
                                $ws_update_where = array(
                                    "clause" => 'id=:id', 
                                    'params' => array(
                                        ":id" => $wsData['id'],
                                    )
                                );
                                $pdo->update("website_subscriptions", $ws_update_data, $ws_update_where);
                            }else{
                                $ws_update_data["price"] = $wsData["price"];
                                $ws_update_data["qty"] = $wsData["qty"];
                                $ws_update_data["plan_id"] = $wsData["plan_id"];
                                $ws_update_data["issued_state"] = $wsData["issued_state"];
                                $ws_update_data["eligibility_date"] = $wsData["eligibility_date"];
                                $ws_update_data["product_type"] = $wsData["product_type"];
                                $ws_update_data["website_id"] = $wsData["website_id"];
                                $ws_update_data["product_id"] = $wsData["product_id"];
                                $ws_update_data["product_code"] = $wsData["product_code"];
                                $ws_update_data["start_coverage_period"] = $wsData["start_coverage_period"];
                                $ws_update_data["end_coverage_period"] = $wsData["end_coverage_period"];
                                $ws_update_data["customer_id"] = $wsData["customer_id"];
                                $websiteInsId = $pdo->insert("website_subscriptions", $ws_update_data);
                                $wsData["id"] = $websiteInsId;
                            }

                            $cd_update_data = array();
                            $cd_update_data['terminationDate'] = $termination_date;
                            $cd_update_data['status'] = $statuses['member_status'];

                            $cd_update_where = array(
                                        "clause" => 'website_id=:id', 
                                        'params' => array(
                                            ":id" => $wsData['id'],
                                        )
                                    );
                            $pdo->update("customer_dependent", $cd_update_data, $cd_update_where);
                        } else {
                            $attempt_sql = "SELECT * FROM prd_subscription_attempt
                                   WHERE attempt=:attempt AND is_deleted='N'";
                            $attempt_where = array(":attempt" =>1);
                            $attempt_row = $pdo->selectOne($attempt_sql, $attempt_where);

                            $ws_update_data = array(
                                'status' => $statuses['policy_status'],
                                'total_attempts' => '1',
                                'next_attempt_at' => date('Y-m-d', strtotime("+" . $attempt_row['attempt_frequency'])),
                                'updated_at' => 'msqlfunc_NOW()',
                            );

                             if(!empty($wsData['id'])){
                                $ws_update_where = array(
                                    "clause" => 'id=:id', 
                                    'params' => array(
                                        ":id" => $wsData['id'],
                                    )
                                );
                                $pdo->update("website_subscriptions", $ws_update_data, $ws_update_where);

                            }else{
                                $ws_update_data["customer_id"] = $wsData["customer_id"];
                                $ws_update_data["issued_state"] = $wsData["issued_state"];
                                $ws_update_data["eligibility_date"] = $wsData["eligibility_date"];
                                $ws_update_data["product_type"] = $wsData["product_type"];
                                $ws_update_data["website_id"] = $wsData["website_id"];
                                $ws_update_data["product_id"] = $wsData["product_id"];
                                $ws_update_data["product_code"] = $wsData["product_code"];
                                $websiteInsId = $pdo->insert("website_subscriptions", $ws_update_data);
                                $wsData['id'] = $websiteInsId;
                            }
                        }
                    }

                    $insOrderDetailSql = array(
                        'website_id'=>$wsData["id"],
                        'order_id' => $order_id,
                        'product_id' => $wsData['product_id'],
                        'fee_applied_for_product'=>!empty($wsData['fee_applied_for_product']) ? $wsData['fee_applied_for_product'] : 0,
                        'plan_id' => $wsData['plan_id'],
                        'prd_plan_type_id' => $wsData['prd_plan_type_id'],
                        'product_type' => $wsData['product_type'],
                        'product_name' => $wsData['product_name'],
                        'product_code' => $wsData['product_code'],
                        'start_coverage_period' => $wsData["start_coverage_period"],
                        'end_coverage_period' => $wsData["end_coverage_period"],
                        'qty' => $wsData['qty'],
                        'renew_count'=>$covPeriod,
                        'unit_price'=>$wsData["price"],
                    );
                   
                    $detail_insert_id = $pdo->insert("order_details", $insOrderDetailSql);

                    $web_history_data = array(
                        'customer_id' => $customer_id,
                        'website_id' => $wsData["id"],
                        'product_id' => $wsData['product_id'],
                        'fee_applied_for_product'=>!empty($wsData['fee_applied_for_product']) ? $wsData['fee_applied_for_product'] : 0,
                        'plan_id' => $wsData['plan_id'],
                        'prd_plan_type_id' => $wsData['prd_plan_type_id'],
                        'order_id' => $order_id,
                        'status' => ($payment_approved == true?"Success":"Fail"),
                        'message' => "Make Payment Order",
                        'authorize_id' => makeSafe($txn_id),
                        'processed_at' => 'msqlfunc_NOW()',
                        'created_at' => 'msqlfunc_NOW()',
                    );
                    
                    if($is_post_date_order == true) {
                        $web_history_data['message'] = "Make Payment Order Attempt on future .".(date('m/d/Y',strtotime($payment_date)));
                    }
                    $pdo->insert("website_subscriptions_history", $web_history_data);

                    $subscription_ids[] = $wsData["id"];
                }
            //********* Order Detail Table code end   ********************

            //********* Order Table update subscription id code start ********************
                if (!empty($subscription_ids)) {
                    $pdo->update("orders", array('subscription_ids' => implode(',', $subscription_ids)), array("clause" => "id=:id", "params" => array(":id" => $order_id)));
                }
            //********* Order Table update subscription id code end   ********************

            if($payment_approved == true){
                $enrollDate->updateNextBillingDateByOrder($order_id);
            }

            //************* insert transaction code start ***********************
                if (!$is_post_date_order) {
                    $other_params=array("transaction_id"=>$txn_id,'transaction_response'=>$payment_res);
                    if ($payment_approved){
                        if($payment_mode != "ACH"){
                            if($orderParams["is_renewal"] == "Y"){
                               $transactionInsId = $functionClass->transaction_insert($order_id,'Credit','Renewal Order','Renewal Transaction','',$other_params);
                           }else{
                                $transactionInsId=$functionClass->transaction_insert($order_id,'Credit','New Order','Transaction Approved',0,$other_params);
                            }
                        }else{
                            $transactionInsId=$functionClass->transaction_insert($order_id,'Credit','Pending','Settlement Transaction',0,$other_params);
                        }
                    }else{
                        $other_params["reason"] = checkIsset($payment_error);
                        $transactionInsId=$functionClass->transaction_insert($order_id,'Failed','Payment Declined','Transaction Declined',0,$other_params);
                    }
                }else if ($is_post_date_order) {
                    $other_params=array();
                    $transactionInsId=$functionClass->transaction_insert($order_id,'Credit','Post Payment','Post Transaction',0,$other_params);
                }
            //**************** insert transaction code end ***********************

            //************* Activity Code Start *************
              $description['ac_message'] =array(
              'ac_red_1'=>array(
              'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
              'title'=>$_SESSION['admin']['display_id'],
              ),
              'ac_message_1' =>' Generated Order ',
              'ac_red_2'=>array(
              'href'=> $ADMIN_HOST.'/members_details.php?id='.md5($custRes["id"]),
              'title'=> $order_display_id,
              ),
              );
              activity_feed(3, $_SESSION['admin']['id'], 'Admin',$custRes["id"], 'customer',"Admin Make Payment Order", $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
            //************* Activity Code End *************
            
            // Stop Payment Processing Code Start
                if($payment_approved == false) {
                    break;
                }
            // Stop Payment Processing Code Ends

            if($payment_approved == true && $is_post_date_order != true && $bill_row['payment_mode'] != "ACH"){
                $payable_params=array(
                    'payable_type'=>'Vendor',
                    'type'=>'Vendor',
                    'transaction_tbl_id' => $transactionInsId['id'],
                );
                $payable=$functionClass->payable_insert($order_id,0,0,0,$payable_params);
            }  
                  
        }

        /*------- UPDATE => CUST - WS - CE - CUST DEPEN ------*/
        if($coverage_index == 0 && $payment_error != "") {
            $response['status'] = 'payment_error';
            $response['payment_error'] = $payment_error;
        } else {
            if($payment_error != "") {
                setNotifyError("Billing failed for some plan periods.");
            } else {
                setNotifySuccess("Subscriptions Payment successfully.");
            }
            $response['status'] = 'payment_success';
        }
    } else {
        $response['status'] = 'success';    
    }    
} else {
    $response['status'] = 'error';
    $response['errors'] = $validate->getErrors();
}
$response['current_step'] = $step;
$response['next_step'] = $step + 1;
echo json_encode($response);
dbConnectionClose();
exit;
?>