<?php
include_once __DIR__ . '/includes/connect.php';
include_once __DIR__ . '/includes/cyberx_payment_class.php';
include_once __DIR__ . '/includes/enrollment_dates.class.php';
include_once __DIR__ . '/includes/function.class.php';
include_once __DIR__ . '/includes/member_enrollment.class.php';
include_once __DIR__ . '/includes/benefit_tier_change_function.php';
include_once __DIR__ . '/includes/member_setting.class.php';
include_once __DIR__ . '/includes/policy_setting.class.php';
$enrollDate = new enrollmentDate();
$functionClass = new functionsList();
$memberSetting = new memberSetting();
$policySetting = new policySetting();

$validate = new Validation();
$BROWSER = getBrowser();
$OS = getOS($_SERVER['HTTP_USER_AGENT']);
$REQ_URL = ($_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
$today = date("Y-m-d");
$decline_log_id = "";

$response = array();
$location = isset($_REQUEST['location'])?$_REQUEST['location']:'admin';

$step = $_POST['step'];
$customer_id = $_POST['customer_id'];
$reinstate_subscriptions = isset($_POST['reinstate_subscriptions']) ? $_POST['reinstate_subscriptions'] : "";
$sel_fees = isset($_POST['fees']) ? $_POST['fees'] : array();
$effective_date = isset($_POST['effective_date']) ? $_POST['effective_date'] : array();
$paid_through_date = isset($_POST['paid_through_date']) ? $_POST['paid_through_date'] : array();
$prd_plan_type = isset($_POST['prd_plan_type']) ? $_POST['prd_plan_type'] : array();
$dependents = isset($_POST['dependents']) ? $_POST['dependents'] : array();
$sel_healthystepfee = isset($_POST['healthystepfee']) ? $_POST['healthystepfee'] : array();
$sel_servicefee = isset($_POST['servicefee']) ? $_POST['servicefee'] : array();
$is_enroll = isset($_POST['is_enroll']) ? $_POST['is_enroll'] : "N";
$is_reinstate = isset($_POST['is_reinstate']) ? $_POST['is_reinstate'] : "N";
$old_plan_type = array();
$cust_sql = "SELECT c.*,sp.id as customer_sponsor_id,sp.type as sponsor_type,sp.payment_master_id,sp.ach_master_id FROM customer c 
            JOIN customer sp ON sp.id=c.sponsor_id
            WHERE c.id=:id";
$cust_where = array(":id" => $customer_id);
$cust_row = $pdo->selectOne($cust_sql, $cust_where);
$sponsor_id = $cust_row["customer_sponsor_id"];

$sponsor_billing_method = "individual";
$is_group_member = 'N';
$REAL_IP_ADDRESS = get_real_ipaddress();
if($cust_row['sponsor_type'] == "Group") {
    $is_group_member = 'Y';

    $sqlBillingType = "SELECT billing_type FROM customer_group_settings where customer_id=:customer_id";
    $resBillingType = $pdo->selectOne($sqlBillingType,array(":customer_id"=>$cust_row['customer_sponsor_id']));
    if(!empty($resBillingType['billing_type'])){
        $sponsor_billing_method = $resBillingType['billing_type'];
    }
}

$cb_sql = "SELECT *,
                AES_DECRYPT(card_no_full,'" . $CREDIT_CARD_ENC_KEY . "')as cc_no,
                AES_DECRYPT(ach_account_number,'" . $CREDIT_CARD_ENC_KEY . "')as ach_account_number,
                AES_DECRYPT(ach_routing_number,'" . $CREDIT_CARD_ENC_KEY . "')as ach_routing_number 
            FROM customer_billing_profile WHERE customer_id=:customer_id";
$cb_where = array(":customer_id" => $customer_id);
$cb_res = $pdo->select($cb_sql, $cb_where);


$default_cb_sql = "SELECT id FROM customer_billing_profile WHERE customer_id=:customer_id AND is_default='Y'";
$default_cb_where = array(":customer_id" => $customer_id);
$default_cb_row = $pdo->selectOne($default_cb_sql, $default_cb_where);
$coverage_periods_data = array();

if ($step >= 1) {
    if (empty($reinstate_subscriptions)) {
        $validate->setError('reinstate_subscriptions', 'Please select product(s) to reinstate.');
    } else {
        $subscriptions_coverage_periods = get_coverage_periods_for_reinstate($customer_id, $reinstate_subscriptions);
        // $response['subscriptions_coverage_periods'] = $subscriptions_coverage_periods;
    }
}

if ($step >= 2) {

    foreach ($subscriptions_coverage_periods as $coverageKey => $cpr) {
        if ($cpr['is_approved_payment'] == true) {
            continue;
        }
        foreach ($cpr['ws_res'] as $key => $value) {
            if (empty($prd_plan_type[$value['product_id']])) {
                $validate->setError('prd_plan_type_' . $value['product_id'], 'Please select benefit tier.');
            }
            if(!empty($prd_plan_type[$value['product_id']]) && $prd_plan_type[$value['product_id']] > 1 && empty($dependents[$value['product_id']])){
                $validate->setError('dependents_' . $value['product_id'], 'Please select dependents.');
            }
            if (empty($effective_date[$value['product_id']])) {
                $validate->setError('effective_date_' . $value['product_id'], 'Please select effective date.');
            }
            if (empty($paid_through_date[$value['product_id']])) {
                $validate->setError('paid_through_date_' . $value['product_id'], 'Please select paid through date.');
            }    
        }
        
    }
}
if ($validate->isValid()) {
    // $healthyStepFee = $functionClass->getMemberHealthyStepFee($customer_id);
    $is_all_coverage_payments_received = true;

    /*---- check if all coverage have approved payment start -------*/
    if ($step >= 1) {
        foreach ($subscriptions_coverage_periods as $cpr) {
            foreach ($cpr['ws_res'] as $key => $value) {
                if ($value['is_approved_payment'] == false) {
                    $is_all_coverage_payments_received = false;
                }
            }
        }
    }

    /*------ Load Coverage Periods --------*/
    if ($step == 1) {
        // pre_print($subscriptions_coverage_periods);
        if ($_POST['is_subscription_changed'] == "Y") {

            /*----- Load coverage periods of selected subscriptions -----------*/
            $coverage_periods_html = '';
            $coverage_periods_html .= '<div class="coverage_period_row">';
            // $coverage_periods_html .= '<h4 class="m-t-0 m-b-20">Details/Coverage Period(s)</h4>';
            $coverage_cnt = 1;
            $last_coverage_end_date = date("m/d/Y");

            foreach ($subscriptions_coverage_periods as $coverageKey => $coverage_period_row) {
                if ($coverage_period_row['is_approved_payment'] == true) {
                    continue;
                }

                $product_matrix = array();
                $temp_totals = 0;
                $renewalCountsArr = array();
                $is_new_order = "N";
                $is_renewal='N';
                $renew_count = 0;

                foreach ($coverage_period_row['ws_res'] as $value2) {
                    $product_matrix[$value2['product_id']] = $value2['plan_id'];
                    $temp_totals += $value2['price'];

                     if($value2["renew_count"] > 1){
                        $is_renewal='Y';
                        $renew_count = $coverage_period_row["renew_count"]-1;
                      }else{
                        $is_new_order = "Y";
                      }
                    $renewalCountsArr[$value2['product_id']] = $renew_count;

                }
                $count = 0;
                foreach ($coverage_period_row['ws_res'] as $tmp_key => $tmp_ws_row) {
                        $coverage_periods_data[$tmp_ws_row['product_id']]['ws_id'] = $tmp_ws_row['id'];
                }

                $coverage_cnt++;
            }
            if($coverage_periods_data){
                foreach ($coverage_periods_data as $tmp_prd_id => $tmp_cv_data) {
                    ob_start();
                    include "tmpl/cobra_reinstate_product_coverage_period.inc.php";
                    $coverage_periods_html .= ob_get_clean();
                }
            }
            $coverage_periods_html .= '</div>';
            $response['coverage_periods_html'] = $coverage_periods_html;
            $response['last_coverage_end_date'] = $last_coverage_end_date;
            /*-----/Load coverage periods of selected subscriptions -----------*/
        }
    }

    /*------ Load Reinstate Summary --------*/
    if ($step == 2) {
        $reinstate_billing_summary = "";
        $reinstate_next_billing_summary = "";
        if (!$is_all_coverage_payments_received) {
            ob_start();
            include "tmpl/cobra_reinstate_billing_summary.inc.php";
            $reinstate_billing_summary .= ob_get_clean();
            $response['reinstate_billing_summary'] = $reinstate_billing_summary;
        }
        // ob_start();
        // include "tmpl/cobra_reinstate_next_billing_summary.inc.php";
        // $reinstate_next_billing_summary .= ob_get_clean();

        $response['reinstate_next_billing_summary'] = $reinstate_next_billing_summary;
    }

    if ($step == 3) {
        /*--- Next Purchase Date ---*/
        $new_ws_ids = array();
        $new_cobra_ids = array();
        $new_ws_id = 0;

        foreach ($reinstate_subscriptions as $product_id => $value) {
            $only_reinstate = 'Y';
            $new_prd_plan_type_id = $prd_plan_type[$product_id];

            $ws_row = $pdo->selectOne("SELECT * from website_subscriptions WHERE id=:id",array(":id" => $value));

            if($ws_row['prd_plan_type_id'] != $prd_plan_type[$product_id] && $ws_row['is_cobra_coverage'] == 'Y'){
                $only_reinstate = 'N';
            }else if($ws_row['prd_plan_type_id'] == $prd_plan_type[$product_id] && $ws_row['is_cobra_coverage'] == 'N'){
                $only_reinstate = 'N';
            }

            $fee_applied_for_product = 0;

            if($ws_row['is_cobra_coverage'] == 'Y'){
                $check_fee_applied_for_product = $pdo->selectOne("SELECT id,product_id,plan_id from website_subscriptions WHERE fee_applied_for_product=:id AND is_cobra_coverage = 'Y'",array(":id" => $product_id));
                if($check_fee_applied_for_product['id']){
                    $fee_applied_for_product = $check_fee_applied_for_product['id'];
                    $new_cobra_ids[$product_id]['cobra_ws_id'] = $fee_applied_for_product;
                    $new_cobra_ids[$product_id]['product_id'] = $check_fee_applied_for_product['product_id'];
                    $new_cobra_ids[$product_id]['plan_id'] = $check_fee_applied_for_product['plan_id'];
                }
            }
            $new_ws_id = $ws_row['id'];

            $is_list_bill_enroll = "N";

            //New Plan Row
            $tmp_other_params = array();
            if($new_prd_plan_type_id > 1 && !empty($dependents)) {
                $tmp_other_params['dep_ids'] = array_values($dependents[$product_id]);
            }

            $new_plan_price_data = get_product_price_detail($ws_row['customer_id'],$product_id,$new_prd_plan_type_id,$value,$tmp_other_params);

            
            $new_prd_row = get_product_row($new_plan_price_data['product_id']);
            $new_plan_row = array(
                'id' => $new_plan_price_data['plan_id'],
                'plan_id' => $new_plan_price_data['plan_id'],
                'product_id' => $new_plan_price_data['product_id'],
                'plan_type' => $new_plan_price_data['prd_plan_type_id'],
                'prd_plan_type_id' => $new_plan_price_data['prd_plan_type_id'],
                'price' => $new_plan_price_data['price'],
                'member_price' => $new_plan_price_data['member_price'],
                'group_price' => $new_plan_price_data['group_price'],
                'display_member_price' => $new_plan_price_data['display_member_price'],
                'display_group_price' => $new_plan_price_data['display_group_price'],
                'contribution_type' => $new_plan_price_data['contribution_type'],
                'contribution_value' => $new_plan_price_data['contribution_value'],
                'product_code' => $new_prd_row['product_code'],
                'product_name' => $new_prd_row['name'],
                'product_type' => $new_prd_row['type'],
                'plan_type_title' => getPlanName("",$new_plan_price_data['prd_plan_type_id']),
            );
            $new_plan_price = $new_plan_price_data['price'];

            //Old Plan Row
            $old_prd_row = get_product_row($product_id);
            $old_plan_row = array(
                'id' => $ws_row['plan_id'],
                'plan_id' => $ws_row['plan_id'],
                'product_id' => $ws_row['product_id'],
                'plan_type' => $ws_row['prd_plan_type_id'],
                'prd_plan_type_id' => $ws_row['prd_plan_type_id'],
                'price' => $ws_row['price'],
                'member_price' => $ws_row['member_price'],
                'display_member_price' => $ws_row['member_price'],
                'group_price' => $ws_row['group_price'],
                'display_group_price' => $ws_row['group_price'],
                'contribution_type' => $ws_row['contribution_type'],
                'contribution_value' => $ws_row['contribution_value'],
                'product_code' => $old_prd_row['product_code'],
                'product_name' => $old_prd_row['name'],
                'product_type' => $old_prd_row['type'],
                'plan_type_title' => getPlanName("",$ws_row['prd_plan_type_id']),
            );
            $old_plan_price = $ws_row['price'];

            $new_ws_status = "Pending";
            $new_ce_process_status = "Pending";
            $old_ce_process_status = "Pending";

            $new_ws_data = array(
                "customer_id" => $ws_row['customer_id'],
                "website_id" => $functionClass->get_website_id(),
                "product_id" => $new_plan_row['product_id'],
                "fee_applied_for_product" => 0,
                "product_type" => $new_plan_row['product_type'],
                "plan_id" => $new_plan_row['plan_id'],
                "prd_plan_type_id" => $new_plan_row['prd_plan_type_id'],
                "product_code" => $new_plan_row['product_code'],
                "qty" => ($ws_row['qty'] > 0 ? $ws_row['qty'] : 1),
                "price" => $new_plan_row['price'],
                "next_purchase_date" => "",
                "eligibility_date" => date('Y-m-d',strtotime($effective_date[$product_id])),
                "start_coverage_period" => "",
                "end_coverage_period" => "",
                "last_order_id" => 0,
                "total_attempts" => 0,
                "renew_count" => $ws_row['renew_count'],
                "termination_date" => date('Y-m-d',strtotime($paid_through_date[$product_id])),
                "term_date_set" => NULL,
                "status" => $new_ws_status,
                "parent_ws_id" => $ws_row['id'],
                "is_onetime" => $ws_row['is_onetime'],
                "benefit_amount" => $ws_row['benefit_amount'],
                "site_load" => $ws_row['site_load'],
                "payment_type" => $ws_row['payment_type'],
                "application_type" => $ws_row['application_type'],
                "active_date" => strtotime($ws_row['active_date'])>0?$ws_row['active_date']:$ws_row['created_at'],
                "policy_change_reason" => "",
                "is_cobra_coverage" => "Y",
                'last_purchase_date' => 'msqlfunc_NOW()',
                'purchase_date' => 'msqlfunc_NOW()',
                "updated_at" => "msqlfunc_NOW()",
                "created_at" => "msqlfunc_NOW()",
            );

            if ($is_group_member == "Y") {
                $new_ws_data['member_price'] = $new_plan_row['member_price'];
                $new_ws_data['group_price'] = $new_plan_row['group_price'];
                $new_ws_data['contribution_type'] = $new_plan_row['contribution_type'];
                $new_ws_data['contribution_value'] = $new_plan_row['contribution_value'];
            }

            if (!empty($ws_row['issued_state'])) {
                $new_ws_data['issued_state'] = $ws_row['issued_state'];
            }

            if($only_reinstate == 'N'){
                $new_ws_id = $pdo->insert("website_subscriptions", $new_ws_data);
            }
            if($only_reinstate == 'N'){

                $cobra_service_fee = get_cobra_service_fee_product();
                $service_fee_price = get_cobra_service_fee($ws_row['price']);

                $new_cobra_data = array(
                    "customer_id" => $ws_row['customer_id'],
                    "website_id" => $functionClass->get_website_id(),
                    "product_id" => $cobra_service_fee['product_id'],
                    "fee_applied_for_product" => $ws_row['product_id'],
                    "product_type" => $cobra_service_fee['product_type'],
                    "plan_id" => $cobra_service_fee['plan_id'],
                    "prd_plan_type_id" => $cobra_service_fee['plan_type'],
                    "product_code" => $cobra_service_fee['product_code'],
                    "qty" => 1,
                    "price" => $service_fee_price['fee'],
                    "next_purchase_date" => "",
                    "eligibility_date" => date('Y-m-d',strtotime($effective_date[$product_id])),
                    "start_coverage_period" => "",
                    "end_coverage_period" => "",
                    "last_order_id" => 0,
                    "total_attempts" => 0,
                    "renew_count" => $ws_row['renew_count'],
                    "termination_date" => date('Y-m-d',strtotime($paid_through_date[$product_id])),
                    "term_date_set" => NULL,
                    "status" => $new_ws_status,
                    "parent_ws_id" => $new_ws_id,
                    "active_date" => strtotime($ws_row['active_date'])>0?$ws_row['active_date']:$ws_row['created_at'],
                    "policy_change_reason" => "",
                    "is_cobra_coverage" => "Y",
                    'last_purchase_date' => 'msqlfunc_NOW()',
                    'purchase_date' => 'msqlfunc_NOW()',
                    "updated_at" => "msqlfunc_NOW()",
                    "created_at" => "msqlfunc_NOW()",
                );

                $cobra_ws_id = $pdo->insert('website_subscriptions',$new_cobra_data);
                $new_cobra_ids[$product_id]['product_id'] = $cobra_service_fee['product_id'];
                $new_cobra_ids[$product_id]['plan_id'] = $cobra_service_fee['plan_id'];
            }

            if($only_reinstate == 'N'){
                $new_ws_ids[$product_id]['ws_id'] = $new_ws_id > 0 ? $new_ws_id :"";
                $new_cobra_ids[$product_id]['cobra_ws_id'] = $cobra_ws_id > 0 ? $cobra_ws_id :"";
            }
            $new_ws_ids[$product_id]['only_reinstate'] = $only_reinstate;

            $web_history_data = array(
                'customer_id' => $ws_row['customer_id'],
                'fee_applied_for_product' => 0,
                'website_id' => $new_ws_id,
                'product_id' => $new_plan_row['product_id'],
                'plan_id' => $new_plan_row['plan_id'],
                'prd_plan_type_id' => $new_plan_row['prd_plan_type_id'],
                'order_id' => 0,
                'status' => 'Update',
                'message' => "Cobra Reinstate",
                'authorize_id' => '',
                'processed_at' => 'msqlfunc_NOW()',
                'created_at' => 'msqlfunc_NOW()',
            );
            if($only_reinstate == 'N'){
                $pdo->insert("website_subscriptions_history", $web_history_data);
            }

            $old_ce_sql = "SELECT ce.*,w.customer_id 
                        FROM customer_enrollment ce 
                        JOIN website_subscriptions w on(w.id = ce.website_id) 
                        WHERE ce.website_id=:website_id";
            $old_ce_res = $pdo->select($old_ce_sql, array(":website_id" => $ws_row['id']));
            foreach ($old_ce_res as $key => $old_ce_row) {
                $sub_products = $functionClass->get_sub_product($new_plan_row['product_id']);
                $new_ce_data = array(
                    "company_id" => $old_ce_row['company_id'],
                    'sub_product' => $sub_products,
                    "sponsor_id" => $old_ce_row['sponsor_id'],
                    "upline_sponsors" => $old_ce_row['upline_sponsors'],
                    "level" => $old_ce_row['level'],
                    "website_id" => $new_ws_id,
                    "process_status" => $new_ce_process_status,
                    "tier_change_date" => "",
                    "has_old_coverage" => $old_ce_row['has_old_coverage'],
                    "old_coverage_file" => $old_ce_row['old_coverage_file'],
                    "parent_coverage_id" => $old_ce_row['id'],
                );
                if($only_reinstate == 'N'){
                    $pdo->insert("customer_enrollment", $new_ce_data);
                }
            }
            if($only_reinstate == 'N'){
                $old_ce_data = array(
                    "process_status" => $old_ce_process_status,
                    "new_plan_id" => $new_plan_row['id'],
                    "tier_change_date" => $effective_date[$product_id],
                );
                $old_ce_where = array("clause" => "id=:id", "params" => array(":id" => $old_ce_row['id']));
                $pdo->update("customer_enrollment", $old_ce_data, $old_ce_where);

                $cobra_service_fee = get_cobra_service_fee_product();

                $service_fee_price = get_cobra_service_fee($ws_row['price']);

                $new_ce_cobra_data = array(
                    "company_id" => $old_ce_row['company_id'],
                    "sponsor_id" => $old_ce_row['sponsor_id'],
                    "upline_sponsors" => $old_ce_row['upline_sponsors'],
                    "level" => $old_ce_row['level'],
                    "website_id" => $cobra_ws_id,
                    "process_status" => $new_ce_process_status,
                    "tier_change_date" => $effective_date[$product_id],
                );
                $pdo->insert("customer_enrollment", $new_ce_cobra_data);

            }
            if ($new_plan_row['plan_type'] > 1 && !empty($dependents && $only_reinstate == 'N')) {

                create_subscription_dependents($ws_row['id'],$new_ws_id,$dependents[$product_id],'', '',array());
            }

        }




        $subscriptions_dates = array();

        foreach ($subscriptions_coverage_periods as $coverage_period_row) {
            foreach ($coverage_period_row['ws_res'] as $tmp_key => $tmp_ws_row) {
                $old_plan_type[$tmp_ws_row['product_id']] = $tmp_ws_row['prd_plan_type_id'];

                $date_selection_options = get_tier_change_date_selection_options($tmp_ws_row['id']);
                $count = 0;
                foreach ($date_selection_options as $key => $value) {
                    $tmp_effective_date = strtotime($effective_date[$tmp_ws_row['product_id']]);
                    $tmp_paid_through_date = strtotime($paid_through_date[$tmp_ws_row['product_id']]);
                    if(strtotime($value['start_coverage_period']) >= $tmp_effective_date){
                        $new_ws_id = $tmp_ws_row['id'];
                        if($new_ws_ids[$tmp_ws_row['product_id']]['only_reinstate'] == 'N'){
                            $new_ws_id = $new_ws_ids[$tmp_ws_row['product_id']]['ws_id'];
                            $new_ws_row = $pdo->selectOne("SELECT * FROM website_subscriptions where id = :id",array(":id" => $new_ws_id));
                            $coverage_periods_data[$tmp_ws_row['product_id']]['ws_res'] = $new_ws_row;
                        }else{
                            $coverage_periods_data[$tmp_ws_row['product_id']]['ws_res'] = $tmp_ws_row;
                        }

                        $coverage_periods_data[$tmp_ws_row['product_id']]['coverage'][$count]['start_coverage_period'] = $value['start_coverage_period'];
                        $coverage_periods_data[$tmp_ws_row['product_id']]['coverage'][$count]['end_coverage_period'] = $value['end_coverage_period'];
                        $coverage_periods_data[$tmp_ws_row['product_id']]['ws_id'] = $new_ws_id;
                        $coverage_periods_data[$tmp_ws_row['product_id']]['is_approved_payment'] = $tmp_ws_row['is_approved_payment'];
                        $coverage_periods_data[$tmp_ws_row['product_id']]['only_reinstate'] = $new_ws_ids[$tmp_ws_row['product_id']]['only_reinstate'];
                        $count++;
                    }
                    if(strtotime($value['end_coverage_period']) >= $tmp_paid_through_date){
                        break;
                    }    

                }
            }
        }

        $final_coverage_period_array = array();

        // pre_print($coverage_periods_data);

        foreach ($coverage_periods_data as $product_id => $data) {
            foreach ($data['coverage'] as $k => $coverage) {

                if($data['is_approved_payment'] == true && $data['only_reinstate'] == 'Y'){
                    continue;
                }
                $temp_ws_row = $pdo->selectOne("SELECT * FROM website_subscriptions where id = :id",array(":id" => $data['ws_id']));

                $final_coverage_period_array[$coverage['start_coverage_period']][] = array(
                    'product_id' => $product_id,
                    'ws_id' => $data['ws_id'],
                    'start_coverage_period' => $coverage['start_coverage_period'],
                    'end_coverage_period' => $coverage['end_coverage_period'],
                    'is_approved_payment' => $data['is_approved_payment'],
                    'plan_type' => $prd_plan_type[$product_id],
                    "price" => $data['ws_res']['price'],
                    'plan_id' => $data['ws_res']['plan_id'],
                    'product_type' => $data['ws_res']['product_type'],
                    'product_name' => getname('prd_main',$product_id,'name','id'),
                    'product_code' => $data['ws_res']['product_code'],
                );

            }
        }

        // pre_print($final_coverage_period_array);
        if(empty($final_coverage_period_array)){
            $is_all_coverage_payments_received = true;
        }
        if ($is_all_coverage_payments_received == true) {
            setNotifySuccess("Subscriptions are already paid.");
            // $response['message'] = "Subscriptions are already paid";
            $response['status'] = "already_paid";

        } else {
            
            $coverage_index = 0;
            $payment_error = "";
            $reinstate_payment_approved = true;
            $payment_approved_ws_ids = array();

            // pre_print($final_coverage_period_array);
            foreach ($final_coverage_period_array as $coverageKey => $value) {
                if ($sponsor_billing_method != "individual") {
                    /*----- List Bill / TPA Bill -----*/
                
                    /*------/Entry In List Bill -------*/

                    $new_plan_price = 0;
                    $cobra_service_fee_total = 0;
                    foreach ($value as $key => $ws_row) {
                        $order_products = array();
                        $cobra_service_fee = get_cobra_service_fee($ws_row['price']);
                        $cobra_service_fee_total += $cobra_service_fee['fee'];
                        $order_products[] = array(
                            "website_id" => $ws_row['ws_id'],
                            "product_id" => $ws_row['product_id'],
                            "plan_id" => $ws_row['plan_id'],
                            "fee_applied_for_product" => 0,
                            "prd_plan_type_id" => $ws_row['plan_type'],
                            "product_type" => $ws_row['product_type'],
                            "product_name" => $ws_row['product_name'],
                            "unit_price" => $ws_row['price'],
                            "cobra_service_fee" => $cobra_service_fee['fee'],
                            "product_code" => $ws_row['product_code'],
                            "family_member" => (!empty($dependents[$ws_row['product_id']]) ? count($ws_row['product_id']) : 0),
                            "qty" => 1,
                            "start_coverage_period" => $ws_row['start_coverage_period'],
                            "end_coverage_period" => $ws_row['end_coverage_period'],
                        );
                        $new_plan_price += $ws_row['price'];
                    }
                    $renew_count = 1;
                    $product_total = $new_plan_price;
                    $grand_total = $product_total + $cobra_service_fee_total;
                    $order_display_id = $functionClass->get_order_id();
                    
                    // pre_print($order_products,false);

                    $ws_ids = array();
                    foreach ($order_products as $key => $od_row) {
                
                        if($prd_plan_type[$od_row['product_id']] > 1 && !empty($dependents[$od_row['product_id']])) {
                            $tmp_other_params['dep_ids'] = array_values($dependents[$od_row['product_id']]);
                        }

                        $new_plan_price_data = get_product_price_detail($customer_id,$product_id,$new_prd_plan_type_id,$value,$tmp_other_params);

                        $tmp_ws_row = $pdo->selectOne("SELECT ws.id FROM website_subscriptions ws WHERE ws.id=:id AND ws.status NOT IN ('Cancel','Inactive') AND ws.is_cobra_coverage = 'Y'", array(":id" => $od_row['website_id']));

                        if (!empty($tmp_ws_row)) {
                            $ws_ids[] = $tmp_ws_row['id'];
                            $affected_ws_ids[$tmp_ws_row['id']] = $tmp_ws_row['id'];

                            $ws_history_status = 'Success';
                            $ws_history_message = "Cobra Enrollment";

                            $ws_history = array(
                                'customer_id' => $customer_id,
                                'website_id' => $tmp_ws_row['id'],
                                'product_id' => $od_row['product_id'],
                                'plan_id' => $od_row['plan_id'],
                                'order_id' => 0,
                                'status' => $ws_history_status,
                                'message' => $ws_history_message,
                                'attempt' => '',
                                'created_at' => 'msqlfunc_NOW()',
                                'processed_at' => 'msqlfunc_NOW()',
                            );
                            $pdo->insert("website_subscriptions_history", $ws_history);

                            $tmp_ws_data = array(
                                'status' => "Active",
                                'last_order_id' => 0,
                                'payment_type' => $sponsor_billing_method,
                                'purchase_date' => 'msqlfunc_NOW()',
                                'last_purchase_date' => 'msqlfunc_NOW()',
                                "start_coverage_period" => $od_row['start_coverage_period'],
                                "end_coverage_period" => $od_row['end_coverage_period'],
                                "fee_applied_for_product" => $od_row['fee_applied_for_product'],
                                'updated_at' => 'msqlfunc_NOW()',

                            );
                            // pre_print($tmp_ws_data);
                            $pdo->update("website_subscriptions", $tmp_ws_data, array("clause" => "id=:id", "params" => array(":id" => $tmp_ws_row['id'])));

                            $tmp_cobra_ws_data = array(
                                'status' => "Active",
                                'last_order_id' => 0,
                                'payment_type' => $sponsor_billing_method,
                                'purchase_date' => 'msqlfunc_NOW()',
                                'last_purchase_date' => 'msqlfunc_NOW()',
                                "start_coverage_period" => $od_row['start_coverage_period'],
                                "end_coverage_period" => $od_row['end_coverage_period'],
                                "fee_applied_for_product" => $od_row['product_id'],
                                'updated_at' => 'msqlfunc_NOW()',

                            );
                            // pre_print($tmp_ws_data);
                            $pdo->update("website_subscriptions", $tmp_cobra_ws_data, array("clause" => "id=:id", "params" => array(":id" => $new_cobra_ids[$od_row['product_id']]['cobra_ws_id'])));
                        }
                        // exit();
                    }

                    $cust_update_data = array(
                        'status' =>'Active',
                        'updated_at' => 'msqlfunc_NOW()',
                    );
                    $cust_update_where = array(
                        "clause" => 'id=:id',
                        'params' => array(
                            ":id" => $customer_id,
                        )
                    );
                    $pdo->update("customer", $cust_update_data, $cust_update_where);
                
                } else {
                        
                        $grand_total = 0.00; //All Products Total (Include membership_Fee and linked_Fee)
                        $sub_total = 0.00;
                        $cobra_service_fee_total = 0.00;
                        $service_fee_total = 0.00;
                        $healthy_step_fee_total = 0.00;
                        $is_renewal = false;
                        $prdMatrixArr = array();

                        foreach ($value as $ws_row) {
                            if (isset($ws_row['is_approved_payment']) && $ws_row['is_approved_payment'] == true) {
                                continue;
                            } else {
                                $cobra_service_fee = get_cobra_service_fee($ws_row['price']);
                                $cobra_service_fee_total += $cobra_service_fee['fee'];
                                $sub_total += $ws_row['price'];
                                if (isset($ws_row['renew_count']) && $ws_row['renew_count'] > 1) {
                                    $is_renewal = true;
                                }
                            }

                            if($ws_row['product_type'] == 'Normal'){
                                array_push($prdMatrixArr,$ws_row['plan_id']);
                            }
                        }

                        $grand_total = $sub_total + $cobra_service_fee_total;

                        // $tmp_index = $coverageKey;
                        // $payment_date = $coverage_periods_data[$tmp_index]['payment_date'];
                        $payment_date = date("Y-m-d");
                        // $billing_id = $coverage_periods_data[$tmp_index]['billing_profile'];

                        $bill_sql = "SELECT *, 
                                AES_DECRYPT(ach_routing_number,'" . $CREDIT_CARD_ENC_KEY . "')as ach_routing_number, 
                                AES_DECRYPT(ach_account_number,'" . $CREDIT_CARD_ENC_KEY . "')as ach_account_number, 
                                AES_DECRYPT(card_no_full,'" . $CREDIT_CARD_ENC_KEY . "')as cc_no 
                                FROM customer_billing_profile WHERE customer_id = :customer_id AND is_default = 'Y' and is_deleted = 'N'";
                        $bill_where = array(":customer_id" => $customer_id);
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
                                $cc_params['order_type'] = 'Cobra Reinstate order';
                                $cc_params['browser'] = $BROWSER;
                                $cc_params['os'] = $OS;
                                $cc_params['req_url'] = $REQ_URL;
                                $cc_params['err_text'] = $payment_res['message'];
                                $txn_id = $payment_res['transaction_id'];
                                $decline_log_id = $functionClass->credit_card_decline_log($customer_id, $cc_params, $payment_res);
                            }
                        }
                        $all_cobra_ws_ids = array();
                        foreach ($new_cobra_ids as $k => $v) {
                            foreach ($v as $k => $cobra_id) {
                                array_push($all_cobra_ws_ids, $cobra_id);
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
                            'is_renewal' => ($is_renewal == true ? 'Y' : 'N'),
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
                            'subscription_ids' => implode(',', array_column($value, 'ws_id')).','.implode(',', $all_cobra_ws_ids),
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

                        if ($is_post_date_order == true) {
                            $other_params = array();
                            $transactionInsId=$functionClass->transaction_insert($order_id,'Credit','Post Payment','Post Transaction',0,$other_params);

                            if($location == "admin") {
                                $description['ac_message'] =array(
                                    'ac_red_1'=>array(
                                        'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
                                        'title'=>$_SESSION['admin']['display_id'],
                                    ),
                                    'ac_message_1' =>' cobra reinstate on ',
                                    'ac_red_2'=>array(
                                        'href'=> $ADMIN_HOST.'/members_details.php?id='.md5($cust_row['id']),
                                        'title'=> $cust_row['rep_id'],
                                    ),
                                    'ac_message_2' =>' Post Payment on Order ',
                                    'ac_red_3'=>array(
                                        'href'=>$ADMIN_HOST.'/all_orders.php?id='.md5($order_id),
                                        'title'=>$order_display_id,
                                    ),
                                    'ac_message_3' =>' Post Payment Date : '.displayDate($payment_date),
                                );
                                activity_feed(3, $_SESSION['admin']['id'], 'Admin',$cust_row["id"], 'customer',"Post Payment Set", $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
                            
                            } elseif($location == "agent") {
                                $description['ac_message'] =array(
                                    'ac_red_1'=>array(
                                        'href'=> 'agent_detail_v1.php?id='.md5($_SESSION['agents']['id']),
                                        'title'=> $_SESSION['agents']['rep_id'],
                                    ),
                                    'ac_message_1' =>' cobra reinstate on ',
                                    'ac_red_2'=>array(
                                        'href'=>  'members_details.php?id='.md5($cust_row['id']),
                                        'title'=> $cust_row['rep_id'],
                                    ),
                                    'ac_message_2' =>' Post Payment on Order ',
                                    'ac_red_3'=>array(
                                        'href'=> 'all_orders.php?id='.md5($order_id),
                                        'title'=>$order_display_id,
                                    ),
                                    'ac_message_3' =>' Post Payment Date : '.displayDate($payment_date),
                                );
                                activity_feed(3, $_SESSION['agents']['id'],'Agent',$cust_row["id"],'customer',"Post Payment Set",'','',json_encode($description));
                            } elseif($location == "group") {
                                $description['ac_message'] =array(
                                    'ac_red_1'=>array(
                                        'href'=> $ADMIN_HOST.'/groups_details.php?id='.md5($_SESSION['groups']['id']),
                                        'title'=> $_SESSION['groups']['rep_id'],
                                    ),
                                    'ac_message_1' =>' cobra reinstate on ',
                                    'ac_red_2'=>array(
                                        'href'=>  $ADMIN_HOST.'/members_details.php?id='.md5($cust_row['id']),
                                        'title'=> $cust_row['rep_id'],
                                    ),
                                    'ac_message_2' =>' Post Payment on Order ',
                                    'ac_red_3'=>array(
                                        'href'=> $ADMIN_HOST.'/all_orders.php?id='.md5($order_id),
                                        'title'=>$order_display_id,
                                    ),
                                    'ac_message_3' =>' Post Payment Date : '.displayDate($payment_date),
                                );
                                activity_feed(3, $_SESSION['groups']['id'],'Group',$cust_row["id"],'customer',"Post Payment Set",'','',json_encode($description));
                            }
                        } else {

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
                                        'ac_message_1' =>' cobra reinstate on ',
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
                                        'ac_message_1' =>' cobra reinstate on ',
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
                                        'ac_message_1' =>' cobra reinstate on ',
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
                                        'ac_message_1' =>' cobra reinstate on ',
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
                                        'ac_message_1' =>' cobra reinstate on ',
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
                                        'ac_message_1' =>' cobra reinstate on ',
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
                            'customer_id' => $customer_id,
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
                            if (isset($ws_row['is_approved_payment']) && $ws_row['is_approved_payment'] == true) {
                                continue;
                            }

                            $renew_count++;

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

                            if(isset($new_cobra_ids[$ws_row['product_id']]['cobra_ws_id'])){
                                $cobra_service_fee = get_cobra_service_fee_product();
                                $service_fee_price = get_cobra_service_fee($ws_row['price']);

                                $order_detail_data = array(
                                    'order_id' => $order_id,
                                    'website_id' => $new_cobra_ids[$ws_row['product_id']]['cobra_ws_id'],
                                    'product_id' => $cobra_service_fee['product_id'],
                                    'fee_applied_for_product' => $ws_row['product_id'],
                                    'plan_id' => $cobra_service_fee['plan_id'],
                                    'prd_plan_type_id' => $cobra_service_fee['plan_type'],
                                    'product_type' => $cobra_service_fee['product_type'],
                                    'product_name' => $cobra_service_fee['product_name'],
                                    'product_code' => $cobra_service_fee['product_code'],
                                    'start_coverage_period' => $ws_row['start_coverage_period'],
                                    'end_coverage_period' => $ws_row['end_coverage_period'],
                                    'qty' => 1,
                                    'renew_count' => $renew_count,
                                    'unit_price' => $service_fee_price['fee'],
                                    'cobra_service_fee' => $service_fee_price['fee'],
                                );
                                $detail_insert_id = $pdo->insert("order_details", $order_detail_data);
                                

                                $cobra_ws_history_data = array(
                                    'customer_id' => $customer_id,
                                    'website_id' => $new_cobra_ids[$ws_row['product_id']]['cobra_ws_id'],
                                    'product_id' => $new_cobra_ids[$ws_row['product_id']]['product_id'],
                                    'fee_applied_for_product' => $ws_row['product_id'],
                                    'plan_id' => $cobra_service_fee['plan_id'],
                                    'prd_plan_type_id' => $cobra_service_fee['plan_type'],
                                    'order_id' => $order_id,
                                    'status' => ($is_approved_payment == true ? "'Success'" : "Fail"),
                                    'message' => 'Cobra Reinstate Order',
                                    'authorize_id' => $txn_id,
                                    'processed_at' => 'msqlfunc_NOW()',
                                    'created_at' => 'msqlfunc_NOW()',
                                );
                                $pdo->insert("website_subscriptions_history", $cobra_ws_history_data);
                            }

                            if ($is_post_date_order == true) {
                                $ws_history_data['message'] = "Reinstate Order Attempt on future ." . (date('m/d/Y', strtotime($payment_date)));
                            }
                            $pdo->insert("website_subscriptions_history", $ws_history_data);

                            $memberSetting = $memberSetting->get_status_by_payment($is_approved_payment);

                            if ($is_approved_payment == true) {
                                $cust_update_data = array(
                                    'status' => $memberSetting['member_status'],
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
                                    'status' => $memberSetting['policy_status'],
                                    'payment_type' => $bill_row['payment_mode'],
                                    'renew_count' => $renew_count,
                                    'last_purchase_date' => 'msqlfunc_NOW()',
                                    'updated_at' => 'msqlfunc_NOW()',
                                );

                                $start_coverage_period = $ws_row['start_coverage_period'];
                                $end_coverage_period = $ws_row['end_coverage_period'];
                                if (!empty($start_coverage_period)) {
                                    $ws_update_data['start_coverage_period'] = $start_coverage_period;
                                }
                                if (!empty($end_coverage_period)) {
                                    $ws_update_data['end_coverage_period'] = $end_coverage_period;
                                }
                                
                                $ws_update_where = array(
                                    "clause" => 'id=:id',
                                    'params' => array(
                                        ":id" => $ws_row['ws_id'],
                                    )
                                );
                                $pdo->update("website_subscriptions", $ws_update_data, $ws_update_where);

                                if(isset($new_cobra_ids[$ws_row['product_id']]['cobra_ws_id'])){
                                    $ws_update_data = array(
                                        'last_order_id' => $order_id,
                                        'total_attempts' => 0,
                                        'next_attempt_at' => NULL,
                                        'status' => $memberSetting['policy_status'],
                                        'payment_type' => $bill_row['payment_mode'],
                                        'renew_count' => $renew_count,
                                        'last_purchase_date' => 'msqlfunc_NOW()',
                                        'updated_at' => 'msqlfunc_NOW()',
                                    );

                                    $start_coverage_period = $ws_row['start_coverage_period'];
                                    $end_coverage_period = $ws_row['end_coverage_period'];
                                    if (!empty($start_coverage_period)) {
                                        $ws_update_data['start_coverage_period'] = $start_coverage_period;
                                    }
                                    if (!empty($end_coverage_period)) {
                                        $ws_update_data['end_coverage_period'] = $end_coverage_period;
                                    }
                                    
                                    $ws_update_where = array(
                                        "clause" => 'id=:id',
                                        'params' => array(
                                            ":id" => $new_cobra_ids[$ws_row['product_id']]['cobra_ws_id'],
                                        )
                                    );
                                    $pdo->update("website_subscriptions", $ws_update_data, $ws_update_where);
                                }


                                /*------- Customer Dependent ----------*/
                                $cd_update_data = array(
                                    'terminationDate' => $end_coverage_period,
                                    'status' => $memberSetting['member_status'],
                                );
                                $cd_update_where = array(
                                    "clause" => 'website_id=:id',
                                    'params' => array(
                                        ":id" => $ws_row['ws_id'],
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
                                        ":id" => $ws_row['ws_id'],
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
                                    $memberSetting = $memberSetting->get_status_by_payment($payment_approved,$pay_app_ws['end_coverage_period']);
                                    // If coverage period is end on past date or today then inactive immidiate
                                    if (strtotime($pay_app_ws['end_coverage_period']) <= strtotime($today)) {
                                        $termination_date = $enrollDate->getTerminationDate($pay_app_ws['id']);

                                        $extra_params = array();
                                        $extra_params['location'] = "ajax_cobra_reinstate_product";
                                        $termination_reason = "Failed Billing";
                                        $policySetting->setTerminationDate($pay_app_ws['id'],$termination_date,$termination_reason,$extra_params);
                                    } else {
                                        $attempt_sql = "SELECT * FROM prd_subscription_attempt
                                               WHERE attempt=:attempt AND is_deleted='N'";
                                        $attempt_where = array(":attempt" => 1);
                                        $attempt_row = $pdo->selectOne($attempt_sql, $attempt_where);

                                        $ws_update_data = array(
                                            'status' => $memberSetting['policy_status'],
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
                                }
                            }

                            //Stop Reinstate Processing
                            break;
                        }

                    }
            }

            if ($payment_error != "") {
                $response['status'] = 'payment_error';
                $response['payment_error'] = $payment_error;
            } else {
                if(count($reinstate_subscriptions) > 1) {
                    $reinstate_af_summary = '<b>Policies :</b>'; 
                    foreach ($reinstate_subscriptions as $key => $ws_id) {
                        $reinstate_af_summary .= '<br/>'.display_policy($ws_id); 
                    }
                } else {
                    foreach ($reinstate_subscriptions as $key => $ws_id) {
                        $reinstate_af_summary = 'Policy : '.display_policy($ws_id);
                    }
                }

                if($location == "admin") {
                    $description['ac_message'] =array(
                        'ac_red_1'=>array(
                            'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
                            'title'=>$_SESSION['admin']['display_id'],
                        ),
                        'ac_message_1' =>' cobra reinstate on ',
                        'ac_red_2'=>array(
                            'href'=> $ADMIN_HOST.'/members_details.php?id='.md5($cust_row['id']),
                            'title'=> $cust_row['rep_id'],
                        ),
                        'ac_message_2' =>'<br/>'.$reinstate_af_summary,
                    );
                    activity_feed(3, $_SESSION['admin']['id'], 'Admin',$cust_row["id"], 'customer',"Admin Reinstate on Member", $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
                
                } elseif($location == "agent") {
                    $description['ac_message'] =array(
                        'ac_red_1'=>array(
                            'href'=> 'agent_detail_v1.php?id='.md5($_SESSION['agents']['id']),
                            'title'=> $_SESSION['agents']['rep_id'],
                        ),
                        'ac_message_1' =>' cobra reinstate on ',
                        'ac_red_2'=>array(
                            'href'=> 'members_details.php?id='.md5($cust_row['id']),
                            'title'=> $cust_row['rep_id'],
                        ),
                        'ac_message_2' =>'<br/>'.$reinstate_af_summary,
                    );
                    activity_feed(3, $_SESSION['agents']['id'], 'Agent',$cust_row["id"], 'customer',"Agent Reinstate on Member",'','',json_encode($description));
                } elseif($location == "group") {
                    $description['ac_message'] =array(
                        'ac_red_1'=>array(
                            'href'=> $ADMIN_HOST.'/groups_details.php?id='.md5($_SESSION['groups']['id']),
                            'title'=> $_SESSION['groups']['rep_id'],
                        ),
                        'ac_message_1' =>' cobra reinstate on ',
                        'ac_red_2'=>array(
                            'href'=> $ADMIN_HOST.'/members_details.php?id='.md5($cust_row['id']),
                            'title'=> $cust_row['rep_id'],
                        ),
                        'ac_message_2' =>'<br/>'.$reinstate_af_summary,
                    );
                    activity_feed(3, $_SESSION['groups']['id'], 'Group',$cust_row["id"], 'customer',"Group Reinstate on Member",'','',json_encode($description));
                }
                
                if ($payment_error != "") {
                    setNotifyError("Billing failed for some coverage periods.");
                } else {
                    setNotifySuccess("Subscriptions reinstate successfully.");
                }
                $response['status'] = 'payment_success';
            }
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