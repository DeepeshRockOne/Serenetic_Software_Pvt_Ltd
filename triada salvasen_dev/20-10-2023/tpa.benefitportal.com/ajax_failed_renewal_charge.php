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

$BROWSER = getBrowser();
$OS = getOS($_SERVER['HTTP_USER_AGENT']);
$REQ_URL = $_SERVER["HTTP_REFERER"];
$today_date = date("Y-m-d");
$response = array();
$validate = new Validation();
$enrollDate = new enrollmentDate();
$function_list = new functionsList();
$enrollment = new MemberEnrollment();
$memberSetting = new memberSetting();

$order_id = isset($_POST['order_id']) ? $_POST['order_id'] : 0 ;
$cb_profile_id = checkIsset($_POST['cb_profile_id']);
$payment_mode = checkIsset($_POST['payment_mode']);

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
$bill_address = checkIsset($_POST['bill_address']);
$bill_address2 = checkIsset($_POST['bill_address2']);
$bill_city = checkIsset($_POST['bill_city']);
$bill_state = checkIsset($_POST['bill_state']);
$bill_zip = checkIsset($_POST['bill_zip']);
$bill_country ='231';

$REAL_IP_ADDRESS = get_real_ipaddress();
$order_row = $pdo->selectOne("SELECT * FROM orders WHERE md5(id)=:id AND status NOT IN('Pending Settlement','Payment Approved')",array(':id' => $order_id));
if(empty($order_row)) {
    echo json_encode(array('status' => 'order_not_found'));
    exit();
}
$order_id = $order_row['id'];
$customer_id = $order_row['customer_id'];

if(empty($payment_mode)){
    $validate->setError("payment_mode","Please select any Payment Method");
}

$validate->string(array('required' => true, 'field' => 'bill_fname', 'value' => $bill_fname), array('required' => 'First Name is required'));
$validate->string(array('required' => true, 'field' => 'bill_address', 'value' => $bill_address), array('required' => 'Address is required'));
if(!empty($bill_address2) && preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/',$bill_address2)) {
    $validate->setError('bill_address2','Special character not allowed in Address 2');
}
$validate->string(array('required' => true, 'field' => 'bill_city', 'value' => $bill_city), array('required' => 'City is required'));
$validate->string(array('required' => true, 'field' => 'bill_state', 'value' => $bill_state), array('required' => 'State is required'));
$validate->string(array('required' => true, 'field' => 'bill_zip', 'value' => str_replace('_','',$bill_zip)), array('required' => 'Zip is required'));
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

if($payment_mode == 'CC'){
    $validate->string(array('required' => true, 'field' => 'name_on_card', 'value' => $name_on_card), array('required' => 'Name On card is required'));
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
    if($require_cvv == 'yes' || $cvv!=''){
        $validate->string(array('required' => true, 'field' => 'cvv', 'value' => str_replace('_','',$cvv)), array('required' => 'CVV is required'));  
        
        if(!$validate->getError("cvv") && !cvv_type_pair($cvv,$card_type)){
            $validate->setError("cvv","Invalid CVV Number");
        }
    }        
}

if($payment_mode == 'ACH'){
    $validate->string(array('required' => true, 'field' => 'ach_name', 'value' => $ach_name), array('required' => 'Name is required'));
    if (!$validate->getError("ach_name") && !ctype_alnum(str_replace(" ","",$ach_name))) {
        $validate->setError("ach_name","Enter Valid Name");
    }
    $validate->string(array('required' => true, 'field' => 'account_type', 'value' => $account_type), array('required' => 'Account Type is required'));
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

if ($validate->isValid()) {
    $cust_row = $pdo->selectOne("SELECT c.id,c.rep_id,c.sponsor_id,c.email,c.fname,c.lname,c.address,c.city,c.state,c.zip,c.status FROM customer c WHERE c.id = :id", array(":id" => $customer_id));
    $lead_row = $pdo->selectOne("SELECT l.id,l.lead_id FROM leads l WHERE customer_id=:customer_id", array(":customer_id" => $cust_row['id']));
    $lead_id = $lead_row['id'];

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
                'description' => 'Enrollment Application Already Submitted - ajax_failed_renewal_charge',
            );
            lead_tracking($lead_id,$customer_id,$lead_track);
            
            setNotifyError("Enrollment Application Already Submitted");
            $response['status'] = 'application_already_submitted';
            header('Content-type: application/json');
            echo json_encode($response);
            exit;
        }
        $ld_desc = array(
            'page' => 'ajax_failed_renewal_charge',
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

    $bill_row = $pdo->selectOne("SELECT payment_mode,last_cc_ach_no,card_type FROM customer_billing_profile WHERE id=:id", array(":id" => $cb_profile_id));

    $activity_update = false;
    $activity_description = array();

    if ($payment_mode == "CC") {
        $expiry_month = substr($expiration,0,2);
        $expiry_year = substr($expiration,-2);
        $billParams = array(
            'customer_id' => $customer_id,
            'fname' => !empty($name_on_card) ? makeSafe($name_on_card) : makeSafe($bill_fname),
            'email' => makeSafe($cust_row['email']),
            'country_id' => 231,
            'country' => 'United States',
            'state' => makeSafe($bill_state),
            'city' => makeSafe($bill_city),
            'zip' => makeSafe($bill_zip),
            'address' => makeSafe($bill_address),
            'address2' => makeSafe($bill_address2),
            'is_address_verified' => "N",
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
    } else {
        $billParams = array(
            'customer_id' => $customer_id,
            'fname' => makeSafe($bill_fname),
            'email' => makeSafe($cust_row['email']),
            'country_id' => 231,
            'country' => 'United States',
            'state' => makeSafe($bill_state),
            'city' => makeSafe($bill_city),
            'zip' => makeSafe($bill_zip),
            'address' => makeSafe($bill_address),
            'address2' => makeSafe($bill_address2),
            'is_address_verified' => "N",
            'created_at' => 'msqlfunc_NOW()',
            'payment_mode' => 'ACH',
            'ach_account_type' => $account_type,
            'bankname' => $ach_name,
        );
        if ($account_number != "") {
            $billParams['last_cc_ach_no'] = makeSafe(substr($account_number, -4));
            $billParams['ach_account_number'] = "msqlfunc_AES_ENCRYPT('" . $account_number . "','" . $CREDIT_CARD_ENC_KEY . "')";
        } else {
            $billParams['last_cc_ach_no'] = makeSafe(substr($entered_account_number, -4));
            $billParams['ach_account_number'] = "msqlfunc_AES_ENCRYPT('" . $entered_account_number . "','" . $CREDIT_CARD_ENC_KEY . "')";
        }

        if ($routing_number != "") {
            $billParams['ach_routing_number'] = "msqlfunc_AES_ENCRYPT('" . $routing_number . "','" . $CREDIT_CARD_ENC_KEY . "')";
        } else {
            $billParams['ach_routing_number'] = "msqlfunc_AES_ENCRYPT('" . $entered_routing_number . "','" . $CREDIT_CARD_ENC_KEY . "')";
        }
    }

    $billParams['is_default'] = 'Y';
    $billParams['ip_address'] = !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'];
    $billParams['updated_at'] = 'msqlfunc_NOW()';
    $pdo->update("customer_billing_profile", $billParams, array("clause" => "id=:id", "params" => array(":id" => $cb_profile_id)));

    $billParams['order_id'] = $order_id;
    $billing_id = getname('order_billing_info',$order_id,'id','order_id');
    $billParams['customer_billing_id'] = $cb_profile_id;

    unset($billParams['created_at']);
    unset($billParams['ip_address']);
    unset($billParams['is_default']);   

    if ($billing_id > 0) {
        $pdo->update("order_billing_info", $billParams, array("clause" => "id=:id", "params" => array(":id" => $billing_id)));
    } else {
        $billing_id = $pdo->insert("order_billing_info", $billParams);
    }

    if ($payment_mode == "CC") {
        $billing_detail = $card_type." *" . $billParams['last_cc_ach_no'];
    } else {
        $billing_detail = "ACH *" . $billParams['last_cc_ach_no'];
    }

    if ($bill_row['payment_mode'] == "CC") {
        $old_billing_detail = $bill_row['card_type']." *" . $bill_row['last_cc_ach_no'];
    } else {
        $old_billing_detail = "ACH *" . $bill_row['last_cc_ach_no'];
    }

    if($old_billing_detail != $billing_detail) {
        $activity_description['key_value']['desc_arr']['Payment Method'] = ' updated from '.$old_billing_detail.' to '.$billing_detail;
        $activity_update = true;
    }

    if(in_array($cust_row['status'],array('Customer Abandon','Pending Quote','Pending Validation','Post Payment','Pending'))) {
        $af_data = array();
        $af_data['ac_message'] =array(
            'ac_red_1'=>array(
                'title'=> $lead_row['lead_id'],
            ),
            'ac_message_1' =>' attempted Order '.$order_row['display_id'].' <br/>',
            'ac_message_2' =>' Billed to '.$billing_detail
        );
        if(!empty($activity_description)) {
            $af_data = array_merge($af_data,$activity_description);
        }
        activity_feed(3,$lead_row['id'],'leads',$lead_row['id'],'Lead','Attempted Order', $cust_row['fname'], $cust_row['lname'], json_encode($af_data), $REQ_URL);
    } else {            
        $af_data = array();
        $af_data['ac_message'] =array(
            'ac_red_1'=>array(
                'title'=> $cust_row['rep_id'],
            ),
            'ac_message_1' =>' attempted Order '.$order_row['display_id'].' <br/>',
            'ac_message_2' =>' Billed to '.$billing_detail
        );
        if(!empty($activity_description)) {
            $af_data = array_merge($af_data,$activity_description);
        }
        activity_feed(3,$cust_row['id'],'Customer',$billing_id,'order_billing_info','Attempted Order', $cust_row['fname'], $cust_row['lname'], json_encode($af_data), $REQ_URL);
    }
    
    //New Business Order
    if($order_row['is_renewal'] == "N") {
        if($order_row['is_reinstate_order'] == "Y") {
            /*--- Update Order To Attempt Immidiate ---*/
            $updOdrParams = array(
                'future_payment'=> 'Y',
                'next_attempt_at' => date("Y-m-d",strtotime($today_date))
            );
            $updOdrWhere = array(
                "clause"=>"id=:id",
                "params"=>array(
                    ":id"=>$order_id,
                )
            );
            $pdo->update("orders",$updOdrParams,$updOdrWhere);
            /*---/Update Order To Attempt Immidiate ---*/
            $function_list->generatePostOrder($order_id,'Y');
        } else {
            /*--- Update Order To Attempt Immidiate ---*/
            $updOdrParams = array(
                'future_payment'=> 'Y',
                'total_attempts'=> 0,
                'post_date' => date("Y-m-d",strtotime($today_date))
            );
            $updOdrWhere = array(
                "clause"=>"id=:id",
                "params"=>array(
                    ":id"=>$order_id,
                )
            );
            $pdo->update("orders",$updOdrParams,$updOdrWhere);
            /*---/Update Order To Attempt Immidiate ---*/

            /*--- Update Effective Dates & Coverage ----*/
            $od_sql="SELECT od.id,od.order_id,website_id,p.id as product_id,od.fee_applied_for_product,od.qty,od.prd_plan_type_id,p.type,od.plan_id,p.name as product_name,p.product_code,od.unit_price,od.start_coverage_period,od.end_coverage_period,p.product_type,p.fee_type,o.created_at,p.payment_type_subscription,p.payment_type,p.company_id,od.member_price,od.group_price,od.contribution_type,od.contribution_value,p.is_member_benefits,p.is_fee_on_renewal,p.fee_renewal_type,p.fee_renewal_count 
                FROM orders o 
                JOIN order_details od on (o.id=od.order_id AND od.is_deleted='N')
                JOIN prd_main p ON(p.id=od.product_id)
                WHERE o.id=:order_id GROUP BY od.id";
            $od_res=$pdo->select($od_sql,array(":order_id"=>$order_id));

            if(!empty($od_res)) {
                $product_list = array();
                foreach ($od_res as $od_row) {
                    array_push($product_list, $od_row['product_id']);
                }

                $coverage_dates_option = $enrollment->get_coverage_period($product_list);
                $coverage_dates = array();
                foreach ($coverage_dates_option as $key => $coverage) {
                    $coverage_dates[$coverage['product_id']]=$coverage['coverage_date'];    
                }

                $endCoverageDateArr= array();
                foreach ($od_res as $key => $od_row) {
                    $member_payment_type = $od_row['payment_type_subscription'];

                    if(strtotime($today_date) >= strtotime($od_row['start_coverage_period'])){
                        $start_coverage_date = checkIsset($coverage_dates[$od_row['product_id']]);
                        if($od_row['type']=='Fees') {
                            $start_coverage_date = checkIsset($coverage_dates[$od_row['fee_applied_for_product']]);
                        }
                    } else {
                        $start_coverage_date = $od_row['start_coverage_period'];
                    }
                    $product_dates = $enrollDate->getCoveragePeriod($start_coverage_date,$member_payment_type);
                    $endCoveragePeriod = date('Y-m-d',strtotime($product_dates['endCoveragePeriod']));
                    array_push($endCoverageDateArr, $endCoveragePeriod);
                }

                foreach ($od_res as $od_row) {
                    $member_payment_type = $od_row['payment_type_subscription'];

                    if(strtotime($today_date) >= strtotime($od_row['start_coverage_period'])){
                        $start_coverage_date = checkIsset($coverage_dates[$od_row['product_id']]);
                        if($od_row['type']=='Fees') {
                            $start_coverage_date = checkIsset($coverage_dates[$od_row['fee_applied_for_product']]);
                        }                       
                    } else {
                        $start_coverage_date = $od_row['start_coverage_period'];
                    }

                    $product_dates = $enrollDate->getCoveragePeriod($start_coverage_date,$member_payment_type);

                    $startCoveragePeriod = date('Y-m-d',strtotime($product_dates['startCoveragePeriod']));
                    $endCoveragePeriod = date('Y-m-d',strtotime($product_dates['endCoveragePeriod']));
                    $eligibility_date = date('Y-m-d',strtotime($product_dates['eligibility_date']));


                    /*--- Order Detail ---*/
                    $od_upd_data = array(
                        'start_coverage_period' => $startCoveragePeriod,
                        'end_coverage_period' => $endCoveragePeriod,
                    );
                    $od_upd_where = array("clause" => "id=:id", "params" => array(":id" => $od_row['id']));
                    $pdo->update("order_details",$od_upd_data,$od_upd_where);
                    /*---/Order Detail ---*/


                    /*--- Website Subscriptions ---*/
                    $ws_data = array(
                        'last_purchase_date' => 'msqlfunc_NOW()',
                        'payment_type' => ($payment_mode == 'ACH' ? 'ACH' : 'CC'),
                        'updated_at' => 'msqlfunc_NOW()',
                        'termination_date'=>NULL,
                        'term_date_set' => NULL,
                        'total_attempts' => 0,
                    );
                    if ($od_row['payment_type'] == 'Recurring') {
                        $next_purchase_date=$enrollDate->getNextBillingDateFromCoverageList($endCoverageDateArr,$startCoveragePeriod,$customer_id);
                        $ws_data['next_purchase_date'] = date('Y-m-d',strtotime($next_purchase_date));
                    }else{
                        $ws_data['is_onetime'] = 'Y';
                        $ws_data['next_purchase_date'] = $today_date;
                    }
                    $ws_data['eligibility_date'] = $eligibility_date;
                    $ws_data['start_coverage_period'] = $startCoveragePeriod;
                    $ws_data['end_coverage_period'] = $endCoveragePeriod;

                    /*------ Set Termination Date for Healthy Step ------*/
                    if($od_row['product_type'] == "Healthy Step") {
                        if($od_row['is_member_benefits'] == "Y" && $od_row['is_fee_on_renewal'] == "Y" && $od_row['fee_renewal_type'] == "Renewals" && $od_row['fee_renewal_count'] > 0) {
                            $tmp_fee_renewal_count = $od_row['fee_renewal_count'];
                            $tmp_start_coverage_date = $startCoveragePeriod;
                            $tmp_termination_date = $endCoveragePeriod;
                            while ($tmp_fee_renewal_count > 0) {
                                $product_dates = $enrollDate->getCoveragePeriod($tmp_start_coverage_date,$member_payment_type);
                                $tmp_start_coverage_date = date("Y-m-d",strtotime('+1 day',strtotime($product_dates['endCoveragePeriod'])));
                                $tmp_termination_date = date("Y-m-d",strtotime($product_dates['endCoveragePeriod']));
                                $tmp_fee_renewal_count--;
                            }
                            $ws_data['termination_date'] = $tmp_termination_date;
                            $ws_data['term_date_set'] = $today_date;
                            $ws_data['termination_reason'] = 'Policy Change';
                        }
                    }
                    /*------/Set Termination Date for Healthy Step ------*/

                    $ws_where = array("clause" => "id=:id", "params" => array(":id" => $od_row['website_id']));
                    $pdo->update("website_subscriptions", $ws_data, $ws_where);
                    /*---/Website Subscriptions ---*/

                    $dep_data = array();
                    $dep_data['eligibility_date'] = $eligibility_date;
                    $dep_data['updated_at'] = 'msqlfunc_NOW()';
                    $dep_where = array(
                        "clause" => "website_id=:website_id",
                        "params" => array(
                            ":website_id" => $od_row['website_id']
                        ),
                    );
                    $pdo->update("customer_dependent", $dep_data,$dep_where);
                }
            }
            /*---/Update Effective Dates & Coverage ----*/
            $function_list->generatePostOrder($order_id);
        }
    } else {
        $od_sql="SELECT od.website_id,ws.total_attempts 
                FROM order_details od 
                JOIN website_subscriptions ws ON(ws.id=od.website_id) 
                WHERE od.order_id=:order_id AND od.is_deleted='N' GROUP BY od.id";
        $od_res=$pdo->select($od_sql,array(":order_id"=>$order_id));
        if(!empty($od_res)) {
            foreach ($od_res as $od_row) {
                /*--- Update Order To Attempt Immidiate ---*/
                $ws_upd_data = array(
                    'next_attempt_at' => $today_date
                );
                if(!($od_row['total_attempts'] > 0)) {
                    $ws_upd_data['total_attempts'] = 1;
                }
                $ws_upd_where = array(
                    "clause"=>"id=:id",
                    "params"=>array(
                        ":id"=>$od_row['website_id'],
                    )
                );
                $pdo->update("website_subscriptions",$ws_upd_data,$ws_upd_where);
                /*---/Update Order To Attempt Immidiate ---*/
            }
        }
        $function_list->generateRenewalOrder($cust_row['id'],$order_id);
    }

    $tmp_order_row = $pdo->selectOne("SELECT id,status,payment_processor_res FROM orders WHERE id=:order_id", array(":order_id" => $order_id));
    $response["status"] = "success";
    if($tmp_order_row['status'] == "Payment Approved") {
        $response["status"] = "payment_success";
    }            
    if($tmp_order_row['status'] == "Payment Declined") {
        $reason = get_declined_reason_from_tran_response($tmp_order_row['payment_processor_res'],false);
        $response["status"] = "payment_fail";
        $response['payment_error'] = $reason;
    }

    $response["msg"] = "Order Attempted Successfully";
    $response["activity_description"] = $activity_description;
    $response["cust_row_status"] = $cust_row['status'];

    if(!empty($lead_tracking_id)) {
        $tracking_where = array(
            'clause' => 'id=:id',
            'params' => array(
                ':id' => $lead_tracking_id,
            ),
        );
        $tracking_data = array(
            'is_request_completed' => 'Y',
            'order_status' => (isset($tmp_order_row['status'])?$tmp_order_row['status']:''),
        );
        $pdo->update('lead_tracking',$tracking_data,$tracking_where);
        $ld_desc = array(
            'page' => 'ajax_failed_renewal_charge',
        );
        $tracking_data = array(
            'status' => 'submit_application_end',
            'order_status' => (isset($tmp_order_row['status'])?$tmp_order_row['status']:''),
            'is_request_completed' => 'Y',
            'customer_id' => $customer_id,
            'lead_id' => $lead_id,
            'order_id' => $order_id,
            'description' => json_encode($ld_desc),
        );
        $pdo->insert('lead_tracking',$tracking_data);
    }
} else {
    $errors = $validate->getErrors();
    $response['status'] = 'fail';
    $response['errors'] = $errors;
}
header('Content-Type: application/json');
echo json_encode($response);
dbConnectionClose();
exit;

?>