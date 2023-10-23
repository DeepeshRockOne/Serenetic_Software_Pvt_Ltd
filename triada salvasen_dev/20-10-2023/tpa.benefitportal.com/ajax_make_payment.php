<?php
include_once __DIR__ . '/includes/connect.php';
include_once __DIR__ . '/includes/cyberx_payment_class.php';
include_once __DIR__ . '/includes/enrollment_dates.class.php';
include_once __DIR__ . '/includes/function.class.php';
include_once __DIR__ . '/includes/member_enrollment.class.php'; 
include_once __DIR__ . '/includes/member_setting.class.php'; 
include_once __DIR__ . '/includes/policy_setting.class.php';
$policySetting = new policySetting();
$enrollDate = new enrollmentDate();
$functionClass = new functionsList();
$MemberEnrollment = new MemberEnrollment();
$validate = new Validation();
$memberSetting = new memberSetting();
$BROWSER = getBrowser();
$OS = getOS($_SERVER['HTTP_USER_AGENT']);
$REQ_URL = ($_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
$today = date("Y-m-d");

$stepFeeRow = array();
$response = array();
$decline_log_id = "";
$location = isset($_REQUEST['location'])?$_REQUEST['location']:'admin';

$step = $_POST['step'];
$customer_id = $_POST['customer_id'];
$REAL_IP_ADDRESS = get_real_ipaddress();
$payment_products = isset($_POST['payment_products']) ? array_keys($_POST['payment_products']) : "";

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
            FROM customer_billing_profile WHERE is_direct_deposit_account='N' AND customer_id=:customer_id AND is_deleted = 'N'";
$custBillWhere = array(":customer_id" => $customer_id);
$custBillRes = $pdo->select($custBillSql, $custBillWhere);

$defaultBillingSql = "SELECT id FROM customer_billing_profile WHERE customer_id=:customer_id AND is_default='Y'";
$defaultBillingWhere = array(":customer_id" => $customer_id);
$default_cb_row = $pdo->selectOne($defaultBillingSql, $defaultBillingWhere);

$billingDate = $functionClass->getCustomerBillingDate($customer_id);
$stepFeeRow = $functionClass->getMemberHealthyStepFee($customer_id);
$coverage_subscriptions = array();
$attempt_over = false;

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
                $coverage_subscriptions[$row["renew_count"]]["max_payment_date"] = $row["max_payment_date"];
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
        $is_prior_cov_not_selected = false;
        foreach ($coverage_payments as $covPeriod) {
            foreach ($coverage_subscriptions as $coverageKey => $coverageRow) {
                if($coverageRow['is_approved_payment'] == true) {
                    continue;
                }
                
                if($covPeriod > $coverageKey && !in_array($coverageKey,$coverage_payments)) {
                    $is_prior_cov_not_selected = true;
                    break;
                }
            }
        }

        if($is_prior_cov_not_selected == true) {
            $validate->setError('coverage_payments','Please Select Prior Coverage');
        } else {
            foreach ($coverage_payments as $key => $covPeriod) {
                $startCoveragePeriod = $coverage_subscriptions[$covPeriod]["start_coverage_period"];
                $tmp_index = strtotime($startCoveragePeriod);

                $post_payment_dates = date("Y-m-d",strtotime($coverage_periods_data[$tmp_index]['payment_date']));
                $is_post_payment_dates = false;
                foreach ($coverage_periods_data as $key => $coverage_data) {
                    $coverage_number = $coverage_data['coverage_number'];
                    $payment_date_array = date("Y-m-d",strtotime($coverage_data['payment_date']));
                    if($covPeriod > $coverage_number && $payment_date_array > $post_payment_dates){
                        $is_post_payment_dates = true;
                        break;
                    }
                }

                $is_post_payment_selected = false;
                foreach ($coverage_subscriptions as $coverageKey => $coverageRow) {
                    $order_ids = implode(',',array_column($coverageRow['ws_res'], 'order_id'));
                    if(!empty($order_ids)){
                        $orderRow = $pdo->select("SELECT post_date,status FROM orders WHERE id IN (".$order_ids.") ");
                        foreach ($orderRow as $orderArray) {
                            $postDateOrder = ($orderArray['status'] == "Post Payment") ? date("Y-m-d",strtotime($orderArray['post_date'])) : '';
                            $paymentDate = date("Y-m-d",strtotime($coverage_periods_data[$tmp_index]['payment_date']));
                            if(in_array('Post Payment',$orderArray) && $postDateOrder > $paymentDate) {
                                $is_post_payment_selected = true;
                            }
                        }
                    }
                }

                if($is_post_payment_selected == true || $is_post_payment_dates == true){
                    $validate->setError('payment_date_'.$tmp_index,'Unpaid post payment scheduled for a prior coverage period');
                }else{
                    $validate->string(array('required' => true, 'field' => 'payment_date_'.$tmp_index, 'value' => $coverage_periods_data[$tmp_index]['payment_date']), array('required' => 'Please select payment date.'));
                }

                $validate->string(array('required' => true, 'field' => 'billing_profile_'.$tmp_index, 'value' => $coverage_periods_data[$tmp_index]['billing_profile']), array('required' => 'Please select payment method.'));

                if($coverage_periods_data[$tmp_index]['billing_profile'] == "new_payment_method") {
                    $validate->setError('billing_profile_'.$tmp_index,'Please save billing profile.');
                }
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
            $payment_post_date = '';

            foreach ($coverage_subscriptions as $coverageKey => $coverageRow) {
                if($coverageRow['is_approved_payment'] == true) {
                    continue;
                }

                $order_ids = implode(',',array_column($coverageRow['ws_res'], 'order_id'));
                if(!empty($order_ids)){
                    $orderRow = $pdo->select("SELECT post_date,status FROM orders WHERE id IN (".$order_ids.") ");
                    foreach ($orderRow as $orderArray) {
                        if($orderArray['status'] == "Post Payment"){
                            $payment_post_date = date("m/d/Y",strtotime($orderArray['post_date']));
                            break;
                        }
                    }
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
            $selected_post_payment_date = date("m/d/Y");
            if(!empty($payment_post_date) && $payment_post_date > date('m/d/Y')){
                $selected_post_payment_date = date("m/d/Y",strtotime($payment_post_date));
            }else if(!empty($payment_post_date) && $payment_post_date <= date("m/d/Y")){
                $selected_post_payment_date = date("m/d/Y",strtotime(' +1 day'));
            }
            $response['payment_post_date'] = $selected_post_payment_date;
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

        foreach ($coverage_subscriptions as $k => $coverage_period_row) {
            foreach ($coverage_period_row['ws_res'] as $tmp_key => $tmp_ws_row) {
                if($tmp_ws_row['is_approved_payment'] == true){
                    continue;
                }

                $pricing_change = get_renewals_new_price($tmp_ws_row['id']);
                if($pricing_change['pricing_changed'] == 'Y'){  
                    $new_ws_data = $pricing_change['new_ws_row'];
                    $new_ws_data['product_name'] = $tmp_ws_row['product_name'];
                    $new_ws_data['planTitle'] = $tmp_ws_row['planTitle'];
                    $new_ws_data['transaction_id'] = $tmp_ws_row['transaction_id'];
                    $new_ws_data['is_post_date_order'] = $tmp_ws_row['is_post_date_order'];
                    $new_ws_data['is_approved_payment'] = $tmp_ws_row['is_approved_payment'];
                    $new_ws_data['renew_count'] = $tmp_ws_row['renew_count'];
                    $new_ws_data['start_coverage_period'] = $tmp_ws_row['start_coverage_period'];
                    $new_ws_data['end_coverage_period'] = $tmp_ws_row['end_coverage_period'];
                    $new_ws_data['next_purchase_date'] = $tmp_ws_row['next_purchase_date'];
                    $coverage_period_row['ws_res'][$tmp_key] = $new_ws_data;
                    $coverage_subscriptions[$k] = $coverage_period_row;
                }
            }
        }

        
        foreach ($coverage_payments as $key => $covPeriod) {

            $startCoveragePeriod = $coverage_subscriptions[$covPeriod]["start_coverage_period"];
            $endCoveragePeriod = $coverage_subscriptions[$covPeriod]["end_coverage_period"];
            
            $tmp_index = strtotime($startCoveragePeriod);
            
            $wsRes = $coverage_subscriptions[$covPeriod]["ws_res"];
            $serviceFeeRow = $coverage_subscriptions[$covPeriod]["coverage_service_fee"];

            $coverageBillingDate = $coverage_subscriptions[$covPeriod]["coverage_billing_date"];

            $lastFailOrderId = 0;
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
            $cur_sub_ids = array();
            foreach ($wsRes as $key => $wsData) {
                if(isset($wsData['is_approved_payment']) && $wsData["is_approved_payment"] == true){
                }else {
                    $coverage_payment_approved = false;
                    $payment_date = isset($coverage_periods_data[$tmp_index]['payment_date']) ? $coverage_periods_data[$tmp_index]['payment_date'] : "";
                    $payment_date = date("Y-m-d",strtotime($payment_date));
                    $billing_id = isset($coverage_periods_data[$tmp_index]['billing_profile']) ? $coverage_periods_data[$tmp_index]['billing_profile'] : "";
                    $sub_total += $wsData['price'];
                    
                    $PlanIdArr[] = $wsData["plan_id"];

                    if($wsData['fail_order_id'] > 0){
                        $lastFailOrderId =  $wsData['fail_order_id']; 
                        if(in_array($wsData["id"],$payment_products)) {
                           $cur_sub_ids[] = $wsData["id"];
                        }
                    }
                }
                
            }
            $grand_total = $sub_total;

            if(!empty($coverage_periods_data[$tmp_index]['enrollment_fee'])) {
                if(!$is_enrollment_fee_added){                                
                    $is_selected_enrollment_fee = true;
                    $is_enrollment_fee_added = true;
                    $stepFeeRow['renew_count'] = $covPeriod;
                    $stepFeeRow['is_approved_payment'] = false;
                    $wsRes[] = $stepFeeRow;
                    $grand_total += $stepFeeRow["price"];
                    $cur_sub_ids[] = $stepFeeRow["id"];
                }
            }

             $bill_sql = "SELECT *, 
                        AES_DECRYPT(ach_routing_number,'" . $CREDIT_CARD_ENC_KEY . "')as ach_routing_number, 
                        AES_DECRYPT(ach_account_number,'" . $CREDIT_CARD_ENC_KEY . "')as ach_account_number, 
                        AES_DECRYPT(card_no_full,'" . $CREDIT_CARD_ENC_KEY . "')as cc_no 
                        FROM customer_billing_profile WHERE id=:id";
            $bill_where = array(":id" => $billing_id);
            $bill_row = $pdo->selectOne($bill_sql, $bill_where);
            $payment_mode = $bill_row["payment_mode"];


            $serviceFeeParams = array();
            if(!empty($coverage_periods_data[$tmp_index]['service_fee']) && !empty($serviceFeeRow[0])) {

                $grand_total += $serviceFeeRow["total"];

                $selService = "SELECT * FROM website_subscriptions WHERE customer_id=:customer_id AND product_id=:product_id ORDER BY id DESC";
                $resService = $pdo->selectOne($selService,array(":customer_id"=>$customer_id,":product_id"=>$serviceFeeRow[0]["product_id"]));
                
                $serviceFeeRow[0]["customer_id"] = $customer_id;  
                $serviceFeeRow[0]["start_coverage_period"] = $startCoveragePeriod;  
                $serviceFeeRow[0]["end_coverage_period"] = $endCoveragePeriod;  
                $serviceFeeRow[0]["prd_plan_type_id"] = $serviceFeeRow[0]["plan_id"];   
                $serviceFeeRow[0]["plan_price"] = $serviceFeeRow[0]["price"];
                $serviceFeeRow[0]['renew_count'] = $covPeriod;
                $serviceFeeRow[0]['is_approved_payment'] = false;
                $serviceFeeRow[0]['qty'] = 1;
                $serviceFeeRow[0]["product_type"] = "Fees";  
                
                $serviceFeeParams["price"] = $serviceFeeRow[0]["price"];
                $serviceFeeParams["start_coverage_period"] = $startCoveragePeriod;
                $serviceFeeParams["end_coverage_period"] = $endCoveragePeriod;

                if(!empty($resService)){
                    $cur_sub_ids[] = $resService["id"];
                    $serviceFeeRow[0]["id"] = $resService["id"];    
                    $serviceFeeRow[0]["product_id"] = $resService["product_id"];    
                    $serviceFeeRow[0]["plan_id"] = $resService["plan_id"];    
                    $serviceFeeRow[0]["website_id"] = $resService["website_id"];    
                    $serviceFeeRow[0]["customer_id"] = $resService["customer_id"]; 
                    $serviceFeeRow[0]["eligibility_date"] = $resService["eligibility_date"];  
                    $serviceFeeRow[0]["issued_state"] = $resService["issued_state"];   
                    $serviceFeeRow[0]["total_attempts"] = $resService["total_attempts"];   

                    if(!empty($resService['fee_applied_for_product'] !=  $serviceFeeRow[0]["fee_product_id"])){
                        $selAppliedPrd = "SELECT id,eligibility_date,start_coverage_period,end_coverage_period FROM website_subscriptions WHERE customer_id=:customer_id AND product_id=:product_id";
                        $resAppliedPrd = $pdo->selectOne($selAppliedPrd,array(":customer_id" => $customer_id,":product_id" => $serviceFeeRow[0]["fee_product_id"]));

                        if(!empty($resAppliedPrd)){                              
                            $serviceFeeParams["fee_applied_for_product"] = $serviceFeeRow[0]["fee_product_id"];
                            $serviceFeeParams["eligibility_date"] = $resAppliedPrd['eligibility_date'];
                            $serviceFeeParams["termination_date"] = NULL;
                            $serviceFeeParams["term_date_set"] = NULL;
                            $serviceFeeParams["total_attempts"] = 0;                                
                        }
                    }  
                    $serviceFeeUpdWhere = array("clause" => "id=:id", "params" => array(":id" => $resService['id']));
                
                    $pdo->update("website_subscriptions", $serviceFeeParams, $serviceFeeUpdWhere);
                    
                }else{
                    $selService = "SELECT * FROM website_subscriptions WHERE customer_id=:customer_id AND product_id=:product_id ORDER BY id DESC";
                    $resService = $pdo->selectOne($selService,array(":customer_id"=>$customer_id,":product_id"=>$serviceFeeRow[0]["fee_product_id"]));
                    if(!empty($resService)){
                        $serviceFeeParams["eligibility_date"] = $resService["eligibility_date"];
                        $serviceFeeParams["issued_state"] = $resService["issued_state"];

                        $serviceFeeRow[0]["eligibility_date"] = $resService["eligibility_date"];  
                        $serviceFeeRow[0]["issued_state"] = $resService["issued_state"];  
                    }

                    $serviceFeeParams["customer_id"] = $customer_id;
                    $serviceFeeParams["website_id"] = $functionClass->get_website_id();
                    $serviceFeeParams["product_id"] = $serviceFeeRow[0]["product_id"];
                    $serviceFeeParams["plan_id"] = $serviceFeeRow[0]["matrix_id"];
                    $serviceFeeParams["prd_plan_type_id"] = $serviceFeeRow[0]["plan_id"];
                    $serviceFeeParams["product_type"] = $serviceFeeRow[0]["type"];
                    $serviceFeeParams["fee_applied_for_product"] = $serviceFeeRow[0]["fee_product_id"];
                    $serviceFeeParams["qty"] = 1;
                    $serviceFeeParams["total_attempts"] = 0;
                    $serviceFeeParams["termination_date"] = NULL;
                    $serviceFeeParams["term_date_set"] = NULL;
                    $serviceFeeParams["product_code"] = $serviceFeeRow[0]["product_code"];
                    $serviceFeeParams["payment_type"] = $payment_mode;
                    $serviceFeeParams["purchase_date"] = 'msqlfunc_NOW()';

                    $websiteId = $pdo->insert("website_subscriptions",$serviceFeeParams);

                    $enrollParams = array(
                        "website_id" => $websiteId,
                        "sponsor_id" => $custRes["sponsor_id"],
                        "level" => $custRes["level"],
                        "upline_sponsors" => $custRes["upline_sponsors"],
                        "process_status" => "Active",
                    );
                    $custEnrollId = $pdo->insert("customer_enrollment",$enrollParams);

                    $serviceFeeRow[0]["id"] = $websiteId;    
                    $serviceFeeRow[0]["product_id"] = $serviceFeeRow[0]["product_id"];  
                    $serviceFeeRow[0]["plan_id"] = $serviceFeeRow[0]["matrix_id"];  
                    $serviceFeeRow[0]["prd_plan_type_id"] = $serviceFeeRow[0]["plan_id"];  
                    $serviceFeeRow[0]["product_type"] = $serviceFeeRow[0]["type"];  
                    $serviceFeeRow[0]["website_id"] = $serviceFeeParams["website_id"];    
                    $serviceFeeRow[0]['is_approved_payment'] = false;
                    $serviceFeeRow[0]["plan_price"] = $serviceFeeRow[0]["price"];
                    $serviceFeeRow[0]['renew_count'] = $covPeriod;
                    $serviceFeeRow[0]['qty'] = 1;
                    $serviceFeeRow[0]["product_type"] = "Fees";  
                    $serviceFeeRow[0]["total_attempts"] = 0; 
                }
                $wsRes[] = $serviceFeeRow[0];
            }
          
            if($coverage_payment_approved){
                continue;
            }
         
           
            
            /*--- Check If Already Declined Order with same subscription ---*/
            $order_id = 0;
            $order_display_id = $functionClass->get_order_id();
            if($lastFailOrderId > 0) {
                $sel_order = "SELECT id,subscription_ids,display_id FROM orders WHERE id=:o_id AND status='Payment Declined'";
                $order_params = array(":o_id" => $lastFailOrderId);
                $existOrder = $pdo->selectOne($sel_order,$order_params);
                if(!empty($existOrder['id'])){
                    $existing_sub_ids = explode(',', $existOrder['subscription_ids']);
                    if(!array_merge(array_diff($existing_sub_ids,$cur_sub_ids),array_diff($cur_sub_ids,$existing_sub_ids))){
                        $order_id = $existOrder['id'];
                        $order_display_id = $existOrder['display_id'];
                    }
                }            
            }
            /*---/Check If Already Declined Order with same subscription ---*/

            
            $payment_approved = false;
            $txn_id = 0;
            $payment_master_id = 0;
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

            // Take Charge of Coverage Period Code Start
                if(strtotime(date('Y-m-d')) < strtotime($payment_date)) {
                    $payment_approved = true;
                    $is_post_date_order = true;
                } else {
                    $is_post_date_order = false;

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
                            $decline_log_id = $functionClass->credit_card_decline_log($customer_id, $cc_params, $payment_res);
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
                            if($SITE_ENV != 'Live' && $cc_params['ccnumber'] == "4111111111111113") {
                                $payment_res = '{"status":"Fail","transaction_id":"40049416880","message":"This transaction has been declined.","API_Type":"Auhtorize Global","API_Mode":"sandbox","API_response":{"status":"Fail","error_code":"2","error_message":"This transaction has been declined.","txn_id":"40049416880"}}';
                                $payment_res = json_decode($payment_res,true);
                            } else {
                                $payment_res = $api->processPayment($cc_params, $payment_master_id);
                            }
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
                                $decline_log_id = $functionClass->credit_card_decline_log($customer_id, $cc_params, $payment_res);
                            }
                        }
                    }
                }
            // Take Charge of Coverage Period Code Ends

            // member status and policy status
            $member_setting = $memberSetting->get_status_by_payment($payment_approved);    
            
            // Order Table code Start
                $orderParams = array(
                    'display_id' => $order_display_id,
                    'customer_id' => $customer_id,
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
                    'is_reinstate_order' => 'Y',
                    'payment_type' => $payment_mode,
                    'type' => ($is_renewal == true ? ',Renewals,' : ',Customer Enrollment,'),
                    'site_load' => "USA",
                    'post_date' => date("Y-m-d", strtotime($payment_date)),
                );

                $orderParams['status'] = ($payment_mode == "ACH") ? 'Pending Settlement' : 'Payment Approved';
                if (!$payment_approved) {
                    $orderParams['status'] = 'Payment Declined';
                }

                if ($is_post_date_order == true) {
                    $orderParams['status'] = 'Post Payment';
                    $orderParams['future_payment'] = 'Y';
                    $orderParams['payment_master_id'] = $payment_master_id;
                    $orderParams['payment_processor'] = $payment_processor;
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

                $orderBillingId= 0;
                if($order_id > 0) {
                    $orderBillingId = getname('order_billing_info',$order_id,'id','order_id');

                    $order_where = array("clause" => "id=:id", "params" => array(":id" => $order_id));
                    $pdo->update("orders", $orderParams, $order_where);
                } else {
                    $orderParams['original_order_date'] = 'msqlfunc_NOW()';
                    $order_id = $pdo->insert("orders", $orderParams);
                }
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

                // if attempt declined order then updates same order
                if ($orderBillingId > 0) {
                    unset($billParams['created_at']);
                    $pdo->update("order_billing_info", $billParams, array("clause" => "id=:id", "params" => array(":id" => $orderBillingId)));
                } else {
                    $pdo->insert("order_billing_info", $billParams);
                }

                if(empty($default_cb_row)){
                    $billParams['is_default'] = 'Y';
                }

                $billParams['ip_address'] = !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'];
                $billParams['updated_at'] = 'msqlfunc_NOW()';

                unset($billParams['order_id']);
                unset($billParams['customer_billing_id']);
                $incr_billing = "";
                if(!empty($billing_id)){
                    $incr_billing .= " AND id=".$billing_id;
                }
                $isCustomerBillingExists = $pdo->selectOne("SELECT id FROM customer_billing_profile WHERE customer_id = :customer_id and is_deleted='N' ". $incr_billing, array(':customer_id' => $customer_id));
                if (empty($isCustomerBillingExists)) {
                    $pdo->insert("customer_billing_profile", $billParams);
                } else {
                    unset($billParams['created_at']);
                    if(!empty($isCustomerBillingExists['id'])){
                        $pdo->update("customer_billing_profile", $billParams, array("clause" => "customer_id=:customer_id AND id=:id", "params" => array(":customer_id" => $customer_id,':id' => $isCustomerBillingExists['id'])));
                    } else {
                        $pdo->update("customer_billing_profile", $billParams, array("clause" => "customer_id=:customer_id", "params" => array(":customer_id" => $customer_id)));
                    }
                }
            // Billing Profile Table code Ends
          
            // Order Detail Table code Start
                $subscription_ids = array();
                $tmp_policy_ids = array();
                
                foreach ($wsRes as $key => $wsData) {
                    if(isset($wsData['is_approved_payment']) && $wsData["is_approved_payment"] == true){
                        continue;
                    }

                    if($payment_approved == true){
                        $ws_update_data = array(
                            'last_order_id' => $order_id,
                            'total_attempts' => 0,
                            'next_attempt_at' => NULL,
                            'last_purchase_date' => 'msqlfunc_NOW()',
                            'status' => $member_setting['policy_status'],
                            'payment_type' => $wsData['payment_type'],
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

                        $tmp_policy_ids[] = $wsData['id'];

                        $extra_params = array();
                        $extra_params['location'] = "make_payment";
                        $extra_params['member_setting'] = $member_setting;
                        $policySetting->removeTerminationDate($wsData["id"],$extra_params);

                        $coverage_index++;
                    }else{
                        $payment_error = $payment_res['message'];

                        $member_setting = $memberSetting->get_status_by_payment($payment_approved,$wsData['end_coverage_period']);

                        // If coverage period is end on past date or today then inactive immidiate
                        if(strtotime($wsData['end_coverage_period']) <= strtotime($today)) {
                            $termination_date=$enrollDate->getTerminationDate($wsData['id']);
                                    
                            $ws_update_data = array(
                                'status' => $member_setting['policy_status'],
                                'fail_order_id' => $order_id,
                                'updated_at' => 'msqlfunc_NOW()',
                            );
                            if(!empty($wsData['id'])){
                                $updateArr = $ws_update_data;
                                unset($updateArr['status']);
                                $updateWhere = array("clause" => 'id=:id', 'params' => array(":id" => $wsData['id']));
                                $pdo->update("website_subscriptions", $updateArr, $updateWhere);
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

                            $extra_params = array();
                            $extra_params['location'] = "make_payment";
                            $termination_reason = "Failed Billing";
                            $policySetting->setTerminationDate($wsData["id"],$termination_date,$termination_reason,$extra_params);
                        } else {
                            $attempt_sql = "SELECT * FROM prd_subscription_attempt
                                   WHERE attempt=:attempt AND is_deleted='N'";
                            $attempt_where = array(":attempt" => ($wsData["total_attempts"] + 1));
                            $attemptRow = $pdo->selectOne($attempt_sql, $attempt_where);

                            if ($attemptRow) {
                                $extra = array('attempt' => ($wsData['total_attempts'] + 1));
                                $member_setting = $memberSetting->get_status_by_payment($payment_approved,"","","",$extra);

                                $ws_update_data = array(
                                    'status' => $member_setting['policy_status'],
                                    'total_attempts' => 'msqlfunc_total_attempts + 1',
                                    'next_attempt_at' => date('Y-m-d', strtotime("+" . $attemptRow['attempt_frequency'] . " Days")),
                                    'fail_order_id' => $order_id,
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
                                    $ws_update_data["customer_id"] = $customer_id;
                                    $ws_update_data["issued_state"] = $wsData["issued_state"];
                                    $ws_update_data["eligibility_date"] = $wsData["eligibility_date"];
                                    $ws_update_data["product_type"] = $wsData["product_type"];
                                    $ws_update_data["website_id"] = $wsData["website_id"];
                                    $ws_update_data["product_id"] = $wsData["product_id"];
                                    $ws_update_data["product_code"] = $wsData["product_code"];
                                    $ws_update_data["fail_order_id"] = $order_id;
                                    $websiteInsId = $pdo->insert("website_subscriptions", $ws_update_data);
                                    $wsData['id'] = $websiteInsId;
                                }
                            }else{

                                $ws_update_data = array(
                                    'fail_order_id' => $order_id,
                                    'updated_at' => 'msqlfunc_NOW()',
                                );

                                $ws_update_where = array(
                                    "clause" => 'id=:id', 
                                    'params' => array(
                                        ":id" => $wsData['id'],
                                    )
                                );
                                $pdo->update("website_subscriptions", $ws_update_data, $ws_update_where);


                                $termination_date=$enrollDate->getTerminationDate($wsData['id']);
                                $attempt_over = true;
                                $extra_params = array();
                                $extra_params['location'] = "make_payment";
                                $extra_params['cancel_post_payment_order'] = true;
                                $termination_reason = "Failed Billing";
                                $policySetting->setTerminationDate($wsData['id'],$termination_date,$termination_reason,$extra_params);
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
                        'renew_count'=> $wsData['renew_count'],
                        'unit_price'=>$wsData["price"],
                    );
                    
                    $checkOdSql = "SELECT id FROM order_details WHERE order_id=:order_id AND website_id=:website_id AND is_deleted='N'";
                    $checkOdParams = array(":order_id" => $order_id,":website_id"=>$wsData["id"]);
                    $checkOdRow = $pdo->selectOne($checkOdSql,$checkOdParams);
                    if (!$checkOdRow) {
                        $detail_insert_id = $pdo->insert("order_details", $insOrderDetailSql);
                    } else {
                        $detail_insert_id = $checkOdRow["id"];
                        $pdo->update("order_details", $insOrderDetailSql, array("clause" => "id=:id", "params" => array(":id" => $detail_insert_id)));
                    }                    

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

                if (!isset($healthyStepTermDateSet)) {
                    $healthyStepTermDateSet = true;
                    $policySetting->setHeathyStepDefaultTerminationDate($customer_id);
                }
            }

            // generate joinder agreement when order is approved
            $functionClass->checkJoinderAgreement($order_id);

            //************* insert transaction code start ***********************
                if (!$is_post_date_order) {
                    $other_params=array("transaction_id"=>$txn_id,'transaction_response'=>$payment_res,'cc_decline_log_id'=>checkIsset($decline_log_id));
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

            // Update Policy Renew Count Code Start
            if($payment_approved == true && !empty($tmp_policy_ids)){
                $tmp_policy_ids = array_unique($tmp_policy_ids);
                foreach($tmp_policy_ids as $policyId){
                    $wsRenewCount = $policySetting->getPolicyRenewCount($policyId);
                    $ws_update_data = array(
                        'renew_count' => $wsRenewCount,                        
                    );
                    $ws_update_where = array(
                        "clause" => 'id=:id',
                        'params' => array(
                            ":id" => $policyId,
                        )
                    );
                    $pdo->update("website_subscriptions", $ws_update_data, $ws_update_where);
                }
            }
            // Update Policy Renew Count Code Ends

            //************* Activity Code Start *************
                $future_coverage_message = '';
                if($is_post_date_order){
                    $future_coverage_message = " Post date: ".date('m/d/Y',strtotime($payment_date));
                }
                if($location == "admin") {
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
                      $description['descPostDate'] = $future_coverage_message;
                      activity_feed(3, $_SESSION['admin']['id'], 'Admin',$custRes["id"], 'customer',"Admin Make Payment Order", $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
                } elseif($location == "agent") {
                    $description['ac_message'] =array(
                        'ac_red_1'=>array(
                            'href'=> $ADMIN_HOST.'/agent_detail_v1.php?id='.md5($_SESSION['agents']['id']),
                            'title'=> $_SESSION['agents']['rep_id'],
                        ),
                        'ac_message_1' =>' Generated Order ',
                        'ac_red_2'=>array(
                            'href'=> $ADMIN_HOST.'/members_details.php?id='.md5($custRes["id"]),
                            'title'=> $order_display_id,
                        ),
                    );
                    $description['descPostDate'] = $future_coverage_message;
                    activity_feed(3,$_SESSION['agents']['id'],'Agent',$custRes["id"], 'customer',"Agent Make Payment Order",'','',json_encode($description));
                } elseif($location == "group") {
                    $description['ac_message'] =array(
                        'ac_red_1'=>array(
                            'href'=> $ADMIN_HOST.'/groups_details.php?id='.md5($_SESSION['groups']['id']),
                            'title'=> $_SESSION['groups']['rep_id'],
                        ),
                        'ac_message_1' =>' Generated Order ',
                        'ac_red_2'=>array(
                            'href'=> $ADMIN_HOST.'/members_details.php?id='.md5($custRes["id"]),
                            'title'=> $order_display_id,
                        ),
                    );
                    $description['descPostDate'] = $future_coverage_message;
                    activity_feed(3,$_SESSION['groups']['id'],'Group',$custRes["id"], 'customer',"Group Make Payment Order",'','',json_encode($description));
                }

                if($payment_approved == false){
                    if($location == "admin") {
                        $description['ac_message'] =array(
                          'ac_red_1'=>array(
                          'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
                          'title'=>$_SESSION['admin']['display_id'],
                          ),
                          'ac_message_1' =>' attempted order ',
                          'ac_red_2'=>array(
                          'href'=> $ADMIN_HOST.'/members_details.php?id='.md5($custRes["id"]),
                          'title'=> $order_display_id,
                          ),
                          );
                          activity_feed(3, $_SESSION['admin']['id'], 'Admin',$custRes["id"], 'customer',"Payment Failed", $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
                    } elseif($location == "agent") {
                        $description['ac_message'] =array(
                            'ac_red_1'=>array(
                                'href'=> $ADMIN_HOST.'/agent_detail_v1.php?id='.md5($_SESSION['agents']['id']),
                                'title'=> $_SESSION['agents']['rep_id'],
                            ),
                            'ac_message_1' =>' attempted order ',
                            'ac_red_2'=>array(
                                'href'=> $ADMIN_HOST.'/members_details.php?id='.md5($custRes["id"]),
                                'title'=> $order_display_id,
                            ),
                        );
                        activity_feed(3,$_SESSION['agents']['id'],'Agent',$custRes["id"], 'customer',"Payment Failed",'','',json_encode($description));
                    } elseif($location == "group") {
                        $description['ac_message'] =array(
                            'ac_red_1'=>array(
                                'href'=> $ADMIN_HOST.'/groups_details.php?id='.md5($_SESSION['groups']['id']),
                                'title'=> $_SESSION['groups']['rep_id'],
                            ),
                            'ac_message_1' =>' attempted order ',
                            'ac_red_2'=>array(
                                'href'=> $ADMIN_HOST.'/members_details.php?id='.md5($custRes["id"]),
                                'title'=> $order_display_id,
                            ),
                        );
                        activity_feed(3,$_SESSION['groups']['id'],'Group',$custRes["id"], 'customer',"Payment Failed",'','',json_encode($description));
                    }
                }
                  
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
                setNotifyError("Billing failed for some coverage periods.");
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
$response['attempt_over'] = $attempt_over;
$response['current_step'] = $step;
$response['next_step'] = $step + 1;
echo json_encode($response);
dbConnectionClose();
exit;
?>