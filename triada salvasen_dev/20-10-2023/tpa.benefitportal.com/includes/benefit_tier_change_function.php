<?php
require_once dirname(__DIR__) . '/includes/cyberx_payment_class.php';
require_once dirname(__DIR__) . '/includes/enrollment_dates.class.php';
require_once dirname(__DIR__) . '/includes/function.class.php';
require_once dirname(__DIR__) . '/includes/member_setting.class.php';
function change_subscription($change_date,$ws_id,$new_product_id,$new_prd_plan_type_id,$extra_detail,$requested_by = 'admin')
{
    global $pdo, $CREDIT_CARD_ENC_KEY, $SITE_ENV, $prdPlanTypeArray,$ADMIN_HOST,$TRANSACTION_APPROVED_STATUS;

    $enrollDate = new enrollmentDate();
    $functionClass = new functionsList();
    $memberSetting = new memberSetting();

    $REAL_IP_ADDRESS = get_real_ipaddress();
    $BROWSER = getBrowser();
    $OS = getOS($_SERVER['HTTP_USER_AGENT']);
    $REQ_URL = ($_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
    $extra_params = array();
    $extra_params['action'] = "";
    $is_refund = false;
    $coverage_periods_array = array();
    $decline_log_id = "";
    $adminFeeAmt = $refundAdminFee = 0;
    $adminSubId = $feeOldPrdId = '';

    //policy_change
    //benefit_tier_change
    //benefit_amount_change
    $action = $extra_detail['action'];
    $change_date = date('Y-m-d', strtotime($change_date));
    $today = date('Y-m-d');
    $new_order_id = 0;
    $extra_detail_type = !empty($extra_detail['type']) ? $extra_detail['type'] : "";
    $idAdminFee = !empty($extra_detail['is_adminFee']) ? $extra_detail['is_adminFee'] : false;
    
    $ws_sql = "SELECT * FROM website_subscriptions WHERE id=:id";
    $ws_row = $pdo->selectOne($ws_sql, array(":id" => $ws_id));
    if (empty($ws_row)) {
        return array("status" => false, "error" => "subscription_not_found", "message" => "Subscription not found");
    }

    $customer_sql = "SELECT c.*,
                IFNULL(s.payment_master_id,0) AS payment_master_id,
                IFNULL(s.ach_master_id,0) AS ach_master_id,s.type as sponsor_type,s.id as sponser_id
                FROM customer c
                JOIN customer s ON(s.id=c.sponsor_id)
                WHERE c.id=:customer_id";
    $customer_row = $pdo->selectOne($customer_sql, array(":customer_id" => $ws_row['customer_id']));

    $sponsor_billing_method = "individual";
    $is_group_member = 'N';
    if($customer_row['sponsor_type'] == "Group") {
        $is_group_member = 'Y';

        $sqlBillingType = "SELECT billing_type FROM customer_group_settings where customer_id=:customer_id";
        $resBillingType = $pdo->selectOne($sqlBillingType,array(":customer_id"=>$customer_row['sponsor_id']));
        if(!empty($resBillingType['billing_type'])){
            $sponsor_billing_method = $resBillingType['billing_type'];
        }
    }

    $next_purchase_date = date("Y-m-d", strtotime($ws_row['next_purchase_date']));
    $coverage_start_date = date("Y-m-d", strtotime($ws_row['start_coverage_period']));

    $is_list_bill_enroll = "N";

    //New Plan Row
    $tmp_other_params = array();
    if($new_prd_plan_type_id > 1 && !empty($extra_detail['dependants'])) {
        $tmp_other_params['dep_ids'] = $extra_detail['dependants'];
    }

    if($action == "benefit_amount_change") {
        $tmp_other_params['primary_benefit_amount'] = $extra_detail['primary_benefit_amount'];
        if($new_prd_plan_type_id > 1) {
            $tmp_other_params['dep_benefit_amount'] = $extra_detail['dep_benefit_amount'];
        }
    }
    if(isset($extra_detail['enrollment_date']) && strtotime($extra_detail['enrollment_date']) > 0) {
        $tmp_other_params['enrollment_date'] = $extra_detail['enrollment_date'];
    }

    $new_plan_price_data = get_product_price_detail($ws_row['customer_id'],$new_product_id,$new_prd_plan_type_id,$ws_id,$tmp_other_params);

    if($new_plan_price_data['plan_id'] == 0) {
        return array("status" => false, "error" => "missing_pricing_information", "message" => "Missing information: all pricing criteria must be added for enrollees to complete this change.");
    }

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
    $old_prd_row = get_product_row($ws_row['product_id']);
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

    $description = ("Benefit Tier changed from " . $old_plan_row['plan_type_title'] . " to " . $new_plan_row['plan_type_title']);

    if($action == "policy_change") {
        $description = ("Policy changed from " . $old_plan_row['product_name'] . " to ." . $new_plan_row['product_name']);

    } elseif($action == "benefit_amount_change") {
        $description = ("Benefit Amount changed for " . $old_plan_row['product_name']);
    }

    $member_setting = $memberSetting->get_status_by_change_benefit_tier();

    $new_ws_status = $member_setting['member_status'];
    $new_ce_process_status = "Pending";
    $old_ce_process_status = "Pending";

    $eligibility_date = date("Y-m-d", strtotime($change_date));

    if(strtotime($ws_row['eligibility_date']) == strtotime($eligibility_date)) {
        $termination_date = $ws_row['eligibility_date'];
    } else{
        $termination_date = date('Y-m-d',strtotime('-1 day',strtotime($eligibility_date)));
    }

    $member_setting = $memberSetting->get_status_by_change_benefit_tier($ws_row['eligibility_date'],$change_date,$ws_row['status'],$termination_date);

    $is_proceed_imidiate = false;
    if ((strtotime($ws_row['eligibility_date']) == strtotime($change_date)) || strtotime($change_date) <= strtotime($today)) {
        $is_proceed_imidiate = true;

        // if ($ws_row['status'] == "Pending Payment" || $ws_row['status'] == "Pending") {
        //     $new_ws_status = $member_setting['policy_status'];
        // } else {
        //     $new_ws_status = $member_setting['policy_status'];
        // }
        $new_ws_status = $member_setting['policy_status'];
        $old_ws_status = $member_setting['old_policy_status'];

        $new_ce_process_status = "Active";
        $old_ce_process_status = "Active";
    }

    //inserting in to website subscription for new plan
    {
        $member_payment_type = getname('prd_main', $new_plan_row['product_id'], 'member_payment_type', 'id');
        $product_dates = $enrollDate->getCoveragePeriod($eligibility_date, $member_payment_type);
        $startCoveragePeriod = date('Y-m-d', strtotime($product_dates['startCoveragePeriod']));
        $endCoveragePeriod = date('Y-m-d', strtotime($product_dates['endCoveragePeriod']));

        $is_take_charge = false;
        if ((strtotime($coverage_start_date) > strtotime($today)) && (strtotime($change_date) <= strtotime($coverage_start_date))) {
            $tmp_next_purchase_date = $next_purchase_date;
            $is_take_charge = true;
        } elseif ((strtotime($eligibility_date) > strtotime($today)) && (strtotime($next_purchase_date) > strtotime($today))) {
            $tmp_next_purchase_date = $next_purchase_date;
        } elseif ((strtotime($eligibility_date) <= strtotime($today))) {
            $is_take_charge = true;
            $tmp_next_purchase_date = $next_purchase_date;
        } else {
            $is_take_charge = true;

            $nextBillingDate = $enrollDate->getNextBillingDateFromCoverage($endCoveragePeriod);
            $tmp_next_purchase_date = date('Y-m-d', strtotime($nextBillingDate));
        }

        /*--- Check all transaction are settled or not ---*/
        if ($sponsor_billing_method == "individual" && $is_take_charge == true) {
            $coverage_periods = subscription_coverage_periods_form_date($ws_id,$eligibility_date);
            foreach ($coverage_periods as $key => $coverage_period) {
                
                $tmp_res = subscription_is_paid_for_coverage_period($ws_id, $coverage_period['start_coverage_period']);
                
                if (!empty($tmp_res['order_id']) && $tmp_res['is_post_date_order'] == false && $tmp_res["cc_no"] != "4111111111111114") {
                    
                    if($tmp_res['status'] == "Pending Settlement") {
                        return array("status" => false, "error" => "billing_not_found", "message" => "Payment has not settled, change must be completed upon settled payment.");
                    } else {
                        $api = new CyberxPaymentAPI();
                        $cc_params = array();
                        $cc_params['transaction_id'] = $tmp_res['transaction_id'];
                        $transaction_res = $api->getTransactionDetail($cc_params, $tmp_res['payment_master_id']);
                        $api_status = (!empty($transaction_res['api_status']) ? $transaction_res['api_status'] : '');
                        if (!in_array($api_status,$TRANSACTION_APPROVED_STATUS)) {
                            return array("status" => false, "error" => "billing_not_found", "message" => "Payment has not settled, change must be completed upon settled payment.");
                        }
                    }
                }
            }
        }
        /*---/Check all transaction are settled or not ---*/

        $new_ws_data = array(
            "customer_id" => $ws_row['customer_id'],
            "website_id" => $functionClass->get_website_id(),
            "product_id" => $new_plan_row['product_id'],
            "fee_applied_for_product" => $ws_row['fee_applied_for_product'],
            "product_type" => $new_plan_row['product_type'],
            "plan_id" => $new_plan_row['plan_id'],
            "prd_plan_type_id" => $new_plan_row['prd_plan_type_id'],
            "product_code" => $new_plan_row['product_code'],
            "qty" => ($ws_row['qty'] > 0 ? $ws_row['qty'] : 1),
            "price" => $new_plan_row['price'],
            "next_purchase_date" => $tmp_next_purchase_date,
            "eligibility_date" => $eligibility_date,
            "start_coverage_period" => $startCoveragePeriod,
            "end_coverage_period" => $endCoveragePeriod,
            "last_order_id" => 0,
            "total_attempts" => 0,
            "next_attempt_at" => NULL,
            "renew_count" => $ws_row['renew_count'],
            "termination_date" => NULL,
            "term_date_set" => NULL,
            "status" => $new_ws_status,
            "parent_ws_id" => $ws_row['id'],
            "is_onetime" => $ws_row['is_onetime'],
            "benefit_amount" => $ws_row['benefit_amount'],
            "site_load" => $ws_row['site_load'],
            "payment_type" => $ws_row['payment_type'],
            "application_type" => $ws_row['application_type'],
            "active_date" => strtotime($ws_row['active_date'])>0?$ws_row['active_date']:$ws_row['created_at'],
            "policy_change_reason" => $action,
            "next_purchase_date_changed" => $ws_row['next_purchase_date_changed'],
            "manual_next_purchase_date" => $ws_row['manual_next_purchase_date'],
            "next_purchase_date_retain_rule" => $ws_row['next_purchase_date_retain_rule'],
            'annual_salary' => $ws_row['annual_salary'],
            'monthly_benefit_percentage' => isset($extra_detail['benefit_amount_percentage']) ? $extra_detail['benefit_amount_percentage'] : "",
            'last_purchase_date' => 'msqlfunc_NOW()',
            'purchase_date' => 'msqlfunc_NOW()',
            "updated_at" => "msqlfunc_NOW()",
            "created_at" => "msqlfunc_NOW()",
        );
        if($action == "benefit_amount_change") {
            $new_ws_data['benefit_amount'] = $extra_detail['primary_benefit_amount'];

            /*--- update benefit amount ----*/
            $ba_data = array(
                'customer_id' => $ws_row['customer_id'],
                'product_id' => $new_plan_row['product_id'],
                'type'=>'Primary',
                'amount'=>$extra_detail['primary_benefit_amount'],
            );
            $ba_sql="SELECT id FROM customer_benefit_amount where is_deleted='N' AND customer_id=:customer_id AND product_id=:product_id AND type='Primary'";
            $ba_row = $pdo->selectOne($ba_sql,array(":customer_id"=>$ws_row['customer_id'],":product_id"=>$new_plan_row['product_id']));
            if(!empty($ba_row)) {
                $ba_where = array("clause" => "id=:id", "params" => array(":id" => $ba_row['id']));
                $pdo->update("customer_benefit_amount", $ba_data,$ba_where);
            } else {
                $pdo->insert("customer_benefit_amount", $ba_data);
            }
            /*---/update benefit amount ----*/
        }

        if ($is_group_member == "Y") {
            $new_ws_data['member_price'] = $new_plan_row['member_price'];
            $new_ws_data['group_price'] = $new_plan_row['group_price'];
            $new_ws_data['contribution_type'] = $new_plan_row['contribution_type'];
            $new_ws_data['contribution_value'] = $new_plan_row['contribution_value'];
        }

        if (!empty($ws_row['issued_state'])) {
            $new_ws_data['issued_state'] = $ws_row['issued_state'];
        }
        if(strtotime($new_ws_data['next_purchase_date']) <= strtotime(date('Y-m-d'))) {
            $new_ws_data['next_purchase_date'] = date('Y-m-d',strtotime('+1 day'));
        }
        $new_ws_id = $pdo->insert("website_subscriptions", $new_ws_data);

        $web_history_data = array(
            'customer_id' => $ws_row['customer_id'],
            'fee_applied_for_product' => $ws_row['fee_applied_for_product'],
            'website_id' => $new_ws_id,
            'product_id' => $new_plan_row['product_id'],
            'plan_id' => $new_plan_row['plan_id'],
            'prd_plan_type_id' => $new_plan_row['prd_plan_type_id'],
            'order_id' => 0,
            'status' => 'Update',
            'message' => $description,
            'authorize_id' => '',
            'processed_at' => 'msqlfunc_NOW()',
            'created_at' => 'msqlfunc_NOW()',
        );
        $pdo->insert("website_subscriptions_history", $web_history_data);

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
                "tier_change_date" => $eligibility_date,
                "has_old_coverage" => $old_ce_row['has_old_coverage'],
                "old_coverage_file" => $old_ce_row['old_coverage_file'],
                "parent_coverage_id" => $old_ce_row['id'],
            );
            $pdo->insert("customer_enrollment", $new_ce_data);

            //updating current plan to set term date etc
            $old_cd_data = array(
                'terminationDate' => $termination_date,
            );
            if(strtotime($termination_date) <= strtotime($today)) {
                $old_cd_data['status'] = $member_setting['dependent_status'];
            }
            $old_cd_where = array("clause" => "website_id=:id", "params" => array(":id" => $old_ce_row['website_id']));
            $pdo->update("customer_dependent", $old_cd_data, $old_cd_where);
            
            if(!empty($extra_detail['activity_feed']) && $extra_detail['activity_feed'] == 'true'){
              $dependantsRes = $pdo->select("SELECT display_id FROM customer_dependent where website_id=:id",array(':id'=> $old_ce_row['website_id']));
              if(!empty($dependantsRes)){
                foreach ($dependantsRes as $dep_val) {
                    $af_desc = array();
                    $af_desc['ac_message'] = array(
                        'ac_red_1' => array(
                            'title' => 'System',
                        ),
                        'ac_message_1' => 'set termination date on dependent',
                        'ac_red_2' => array(
                            'title' => $dep_val['display_id'],
                        ),
                        'ac_message_2' => ' <br/>Policy : ' . display_policy($ws_row['id']) . '<br/> Termination Date : ' . displayDate($termination_date),
                    );
                    $af_desc['descriptionReason'] = 'Reason: Dependent Age out';
                    activity_feed(3, 0, 'System', $ws_row['customer_id'], 'customer', 'System ' . ucwords('set termination date for dependent'), '', '', json_encode($af_desc));
                }
              }
            }

            $old_ce_data = array(
                "process_status" => $old_ce_process_status,
                "new_plan_id" => $new_plan_row['id'],
                "tier_change_date" => $eligibility_date,
            );
            $old_ce_where = array("clause" => "id=:id", "params" => array(":id" => $old_ce_row['id']));
            $pdo->update("customer_enrollment", $old_ce_data, $old_ce_where);
        }
        $termination_reason = 'Benefit Tier Change';
        if($action == "policy_change" && $extra_detail_type == 'auto_terminate'){
            $termination_reason = 'Age Out';
        }elseif($action == "policy_change") {
            $termination_reason = 'Policy Change';

        } elseif($action == "benefit_amount_change") {
            $termination_reason = 'Benefit Amount Change';
        }

        //updating current plan to set term date etc
        $old_ws_data = array(
            'termination_date' => $termination_date,
            'term_date_set' => date('Y-m-d'),
            'termination_reason' => $termination_reason,
            "policy_change_reason" => ($extra_detail_type == 'auto_terminate') ? 'Age Out' : $action ,
            "updated_at" => "msqlfunc_NOW()",
        );
        if ($is_proceed_imidiate == true) {
            $old_ws_data['status'] = $old_ws_status;
        }
        $old_ws_where = array("clause" => "id=:id", "params" => array(":id" => $ws_row['id']));
        $pdo->update("website_subscriptions", $old_ws_data, $old_ws_where);


        if ($new_plan_row['plan_type'] > 1 && !empty($extra_detail['dependants'])) {
            $dep_tmp_other_params = array();
            if($action == "benefit_amount_change") {
                $dep_tmp_other_params['dep_benefit_amount'] = $extra_detail['dep_benefit_amount'];
            }
            create_subscription_dependents($ws_id,$new_ws_id,$extra_detail['dependants'],'', '',$dep_tmp_other_params);
        }

        /*--- update fee_applied_for_product -----*/
        $tmp_ws_data = array(
            'fee_applied_for_product' => $new_plan_row['product_id'],
            "updated_at" => "msqlfunc_NOW()",
        );
        $tmp_ws_where = array("clause" => "fee_applied_for_product=:fee_applied_for_product AND customer_id=:customer_id", "params" => array(":fee_applied_for_product" => $old_plan_row['product_id'],":customer_id" => $ws_row['customer_id']));
        $pdo->update("website_subscriptions", $tmp_ws_data, $tmp_ws_where);
        /*--- update fee_applied_for_product -----*/
        
        /*--- Activity Feed ---*/
        $dp_extra_params = array();
        $af_message = 'changed benefit tier';
        if($action == "policy_change") {
            $af_message = 'changed policy';

        } elseif($action == "benefit_amount_change") {
            $af_message = 'changed benefit amount';
            $dp_extra_params['display_benefit_amount'] = 1;
        }

        $life_event_str = "";
        if(!empty($extra_detail['life_event'])) {
            $life_event_str = "<br/> Life Event : ". $functionClass->getLifeEventLabelByKey($extra_detail['life_event']);
        }
        $activity_id = 0;
        if ($requested_by == 'admin') {
            $af_desc = array();
            $af_desc['ac_message'] =array(
                'ac_red_1'=>array(
                    'href'=> 'admin_profile.php?id='.md5($_SESSION['admin']['id']),
                    'title'=> $_SESSION['admin']['display_id'],
                ),
                'ac_message_1' => $af_message.' on ',
                'ac_red_2'=>array(
                    'href'=> 'members_details.php?id='.md5($customer_row['id']),
                    'title'=>$customer_row['rep_id'],
                ),
                'ac_message_2' =>' <br/> Old Policy : '.display_policy($ws_id,$dp_extra_params).' <br/> New Policy : '. display_policy($new_ws_id,$dp_extra_params) . '<br/> Effective From : '.displayDate($eligibility_date). $life_event_str,
            );
            $activity_id = activity_feed(3, $_SESSION['admin']['id'], 'Admin',$customer_row['id'], 'customer', 'Admin '. ucwords($af_message),'','',json_encode($af_desc));

        } elseif ($requested_by == 'agent') {
            $af_desc = array();
            $af_desc['ac_message'] =array(
                'ac_red_1'=>array(
                    'href'=> 'agent_detail_v1.php?id='.md5($_SESSION['agents']['id']),
                    'title'=> $_SESSION['agents']['rep_id'],
                ),
                'ac_message_1' => $af_message.' on ',
                'ac_red_2'=>array(
                    'href'=> 'members_details.php?id='.md5($customer_row['id']),
                    'title'=>$customer_row['rep_id'],
                ),
                'ac_message_2' =>' <br/> Old Policy : '.display_policy($ws_id,$dp_extra_params).' <br/> New Policy : '. display_policy($new_ws_id,$dp_extra_params) . '<br/> Effective From : '.displayDate($eligibility_date). $life_event_str,
            );
            $activity_id = activity_feed(3, $_SESSION['agents']['id'], 'Agent',$customer_row['id'], 'customer', 'Agent '. ucwords($af_message),'','',json_encode($af_desc));
        } elseif ($requested_by == 'group') {
            $af_desc = array();
            $af_desc['ac_message'] =array(
                'ac_red_1'=>array(
                    'href'=> $ADMIN_HOST.'/groups_details.php?id='.md5($_SESSION['groups']['id']),
                    'title'=> $_SESSION['groups']['rep_id'],
                ),
                'ac_message_1' => $af_message.' on ',
                'ac_red_2'=>array(
                    'href'=> $ADMIN_HOST.'/members_details.php?id='.md5($customer_row['id']),
                    'title'=>$customer_row['rep_id'],
                ),
                'ac_message_2' =>' <br/> Old Policy : '.display_policy($ws_id,$dp_extra_params).' <br/> New Policy : '. display_policy($new_ws_id,$dp_extra_params) . '<br/> Effective From : '.displayDate($eligibility_date). $life_event_str,
            );
            $activity_id = activity_feed(3, $_SESSION['groups']['id'], 'Group',$customer_row['id'], 'customer', 'Group '. ucwords($af_message),'','',json_encode($af_desc));
        } elseif ($requested_by == 'system') {
            $af_desc = array();
            $af_desc['ac_message'] =array(
                'ac_red_1'=>array(
                    'title'=> 'System',
                ),
                'ac_message_1' => $af_message.' on ',
                'ac_red_2'=>array(
                    'href'=> $ADMIN_HOST.'/members_details.php?id='.md5($customer_row['id']),
                    'title'=>$customer_row['rep_id'],
                ),
                'ac_message_2' =>' <br/> Old Policy : '.display_policy($ws_id,$dp_extra_params).' <br/> New Policy : '. display_policy($new_ws_id,$dp_extra_params) . '<br/> Effective From : '.displayDate($eligibility_date). $life_event_str,
            );
            if (!empty($extra_detail['reason'])) {
                $af_desc['descriptionReason'] = 'Reason: ' . $extra_detail['reason'];
            }
            $activity_id = activity_feed(3, 0, 'System',$customer_row['id'], 'customer', 'System '. ucwords($af_message),'','',json_encode($af_desc));
        }
        /*---/Activity Feed ---*/

        /*------- Admin Fee ------*/
        if($customer_row['sponsor_type'] == "Group" && ($action == "benefit_tier_change" || $action == "policy_change")){
            $checkAdminFee = "SELECT ws.id,ws.product_id,prd.is_benefit_tier,cs.id as sponser_id,ws.renew_count FROM `website_subscriptions` ws
               JOIN `customer` c ON (c.id = ws.customer_id AND c.is_deleted = 'N')
               JOIN `customer` cs ON (cs.id = c.sponsor_id AND cs.type = 'Group' AND cs.is_deleted = 'N')
               JOIN prd_main prd ON (prd.id = ws.product_id AND prd.product_type='AdminFee')
               WHERE ws.fee_applied_for_product=:product_id AND ws.customer_id=:customer_id AND ws.product_type='Fees' AND ws.status IN ('Active','Pending')";
            $checkAdminFeeWhere = array(":product_id"=>$ws_row['product_id'],":customer_id"=>$ws_row['customer_id']);
            $checkAdminRes = $pdo->selectOne($checkAdminFee,$checkAdminFeeWhere);
            
            if(!empty($checkAdminRes)){
                $other_params = array(
                    'mainPrdId' => $new_product_id,
                    'mainPrdPlanId' => $new_plan_row['plan_id'],
                    'is_benefit_tier' => $checkAdminRes['is_benefit_tier'],
                    'change_date' => $change_date,
                    'sponser_id' => $checkAdminRes['sponser_id'],
                    'renew_count' => $checkAdminRes['renew_count'],
                    'plan_new_ws_id' => $new_ws_id,
                    'action' => $action,
                );
                $adminFeeSubcription = getAdminFee($ws_row['customer_id'],$checkAdminRes['id'],$new_prd_plan_type_id,true,$other_params);
            }else{
                $other_params = array(
                    'mainPrdId' => $new_product_id,
                    'mainPrdPlanId' => $new_plan_row['plan_id'],
                    'change_date' => $change_date,
                    'sponser_id' => $customer_row['sponser_id'],
                    'renew_count' => $ws_row['renew_count'],
                    'action' => $action,
                );
                $adminFeeSubcription = getAdminFee($ws_row['customer_id'],$new_ws_id,$new_prd_plan_type_id,false,$other_params);
            }
            if(!empty($adminFeeSubcription)){
                $adminFeeAmt = $adminFeeSubcription['fee_price'];
                $adminSubId = $adminFeeSubcription['feeWebid'];
                $refundAdminFee = $adminFeeSubcription['amdinFeeRefAmt'];
                $fee_applied_for_product = $adminFeeSubcription['fee_applied_for_product'];
                $feePrdId = $adminFeeSubcription['feePrdId'];
                $feePrdName = $adminFeeSubcription['feePrdName'];
                $feePrdCode = $adminFeeSubcription['feePrdCode'];
                $feePlanId = $adminFeeSubcription['feePlanId'];
                $feeOldPrdId = $adminFeeSubcription['old_prd_id'];
            }
        }
        /*------- Admin Fee ------*/

        /*------- Create Policy Document ------*/
        $tmp_order_id = 0;
        $tmp_extra = array(
            'website_id' => $new_ws_id,
            'old_website_id' => $ws_row['id'],
            'activity_ids' => $activity_id,
            'action' => "policy_updated"
        );
        $functionClass->insert_member_terms($customer_row['id'],$tmp_order_id,$tmp_extra);
        /*-------/Create Policy Document ------*/
    }

    if ($is_take_charge) {
        if ($sponsor_billing_method == "individual") {
            if (!empty($extra_detail['billing_id'])) {
                $billing_sql = "SELECT *, 
                        AES_DECRYPT(ach_routing_number,'" . $CREDIT_CARD_ENC_KEY . "')as ach_routing_number, 
                        AES_DECRYPT(ach_account_number,'" . $CREDIT_CARD_ENC_KEY . "')as ach_account_number, 
                        AES_DECRYPT(card_no_full,'" . $CREDIT_CARD_ENC_KEY . "')as cc_no 
                        FROM customer_billing_profile WHERE id=:id";
                $billing_where = array(":id" => $extra_detail['billing_id']);
                $billing_row = $pdo->selectOne($billing_sql, $billing_where);
            } else {
                if (checkIsset($extra_detail['payment_mode']) == "CC") {
                    $billing_row = array(
                        'id' => 0,
                        'customer_id' => $ws_row['customer_id'],
                        'payment_mode' => $extra_detail['payment_mode'],
                        'fname' => $extra_detail['fname'],
                        'lname' => $extra_detail['lname'],
                        'card_type' => $extra_detail['card_type'],
                        'cc_no' => $extra_detail['card_number'],
                        'cvv_no' => $extra_detail['cvv_no'],
                        'expiry_month' => $extra_detail['expiry_month'],
                        'expiry_year' => $extra_detail['expiry_year'],
                        'address' => $extra_detail['address'],
                        'address2' => $extra_detail['address2'],
                        'city' => $extra_detail['city'],
                        'state' => $extra_detail['state'],
                        'zip' => $extra_detail['zip'],
                    );
                } elseif (checkIsset($extra_detail['payment_mode']) == "ACH") {
                    $billing_row = array(
                        'id' => 0,
                        'customer_id' => $ws_row['customer_id'],
                        'payment_mode' => $extra_detail['payment_mode'],
                        'fname' => $extra_detail['fname'],
                        'lname' => $extra_detail['lname'],
                        'bankname' => $extra_detail['bankname'],
                        'ach_account_type' => $extra_detail['ach_account_type'],
                        'ach_routing_number' => $extra_detail['ach_routing_number'],
                        'ach_account_number' => $extra_detail['ach_account_number'],
                        'address' => $customer_row['address'],
                        'address2' => $customer_row['address2'],
                        'city' => $customer_row['city'],
                        'state' => $customer_row['state'],
                        'zip' => $customer_row['zip'],
                    );
                } else {
                    $billing_sql = "SELECT *, 
                        AES_DECRYPT(ach_routing_number,'" . $CREDIT_CARD_ENC_KEY . "')as ach_routing_number, 
                        AES_DECRYPT(ach_account_number,'" . $CREDIT_CARD_ENC_KEY . "')as ach_account_number, 
                        AES_DECRYPT(card_no_full,'" . $CREDIT_CARD_ENC_KEY . "')as cc_no 
                        FROM customer_billing_profile WHERE is_default='Y' AND customer_id=:customer_id";
                    $billing_where = array(":customer_id" => $ws_row['customer_id']);
                    $billing_row = $pdo->selectOne($billing_sql, $billing_where);
                }

                if (empty($billing_row)) {
                    return array("status" => false, "error" => "billing_not_found", "message" => "Billing detail not found");
                }
            }
        }

        $is_billing_process_success = true;
        $affected_ws_ids = array();
        $coverage_periods = subscription_coverage_periods_form_date($ws_id,$eligibility_date);

        $member_setting = $memberSetting->get_status_by_payment($is_billing_process_success);

        foreach ($coverage_periods as $key => $coverage_period) {
            if ($sponsor_billing_method != "individual") {
                /*----- List Bill / TPA Bill -----*/
                {
                    /*------ Entry In List Bill -------*/
                    $tmp_sql = "SELECT lb.id as lb_id,lbd.id as lbd_id,lbd.amount,lb.status 
                        FROM list_bills lb 
                        JOIN list_bill_details lbd ON(lbd.list_bill_id = lb.id)
                        WHERE lb.is_deleted='N' AND lbd.ws_id=:ws_id AND lb.time_period_start_date >= :from_date";
                    $tmp_where = array(":ws_id"=>$ws_row['id'],":from_date"=>$eligibility_date);

                    $tmp_rows = $pdo->select($tmp_sql,$tmp_where);
                    if(!empty($tmp_rows)) {
                        foreach ($tmp_rows as $key => $tmp_row) {
                            /*--- Refund Old Policy ---*/
                            if($tmp_row['status'] == 'Paid'){
                            $refund_data = array(
                                'customer_id' => $ws_row['customer_id'],
                                'ws_id' => $ws_row['id'],
                                'old_ws_id' => 0,
                                'group_id' => $customer_row['sponsor_id'],
                                'transaction_type' => 'refund',
                                'transaction_amount' => $tmp_row['amount'],
                                'payment_received_from' => $tmp_row['lb_id'],
                                'payment_received_details_id' => $tmp_row['lbd_id'],
                                'description' => $description,
                                'ip_address' => !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'],
                                'req_url' => $REQ_URL,
                                "start_coverage_date" => $coverage_period['start_coverage_period'],
                                "end_coverage_date" => $coverage_period['end_coverage_period'],
                                'created_at' => 'msqlfunc_NOW()',
                                'updated_at' => 'msqlfunc_NOW()',
                            );
                            $pdo->insert('group_member_refund_charge',$refund_data);

                            }
                            $charge_data = array(
                                'customer_id' => $ws_row['customer_id'],
                                'ws_id' => $new_ws_id,
                                'old_ws_id' => 0,
                                'group_id' => $customer_row['sponsor_id'],
                                'transaction_type' => 'charged',
                                'transaction_amount' => $new_plan_row['price'],
                                'payment_received_from' => $tmp_row['lb_id'],
                                'payment_received_details_id' => $tmp_row['lbd_id'],
                                'description' => $description,
                                'ip_address' => !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'],
                                'req_url' => $REQ_URL,
                                "start_coverage_date" => $coverage_period['start_coverage_period'],
                                "end_coverage_date" => $coverage_period['end_coverage_period'],
                                'created_at' => 'msqlfunc_NOW()',
                                'updated_at' => 'msqlfunc_NOW()',
                            );
                            $pdo->insert('group_member_refund_charge',$charge_data);
                            /*--- Charge New Policy ---*/
                        }
                    }
                    /*------/Entry In List Bill -------*/

                    $order_products = array();
                    $order_products[] = array(
                        "website_id" => $new_ws_id,
                        "product_id" => $new_plan_row['product_id'],
                        "plan_id" => $new_plan_row['plan_id'],
                        "fee_applied_for_product" => 0,
                        "prd_plan_type_id" => $new_plan_row['prd_plan_type_id'],
                        "product_type" => $new_plan_row['product_type'],
                        "product_name" => $new_plan_row['product_name'],
                        "unit_price" => $new_plan_row['price'],
                        "product_code" => $new_plan_row['product_code'],
                        "family_member" => (!empty($extra_detail['dependants']) ? count($extra_detail['dependants']) : 0),
                        "qty" => 1,
                        "start_coverage_period" => $coverage_period['start_coverage_period'],
                        "end_coverage_period" => $coverage_period['end_coverage_period'],
                    );
                    $renew_count = $coverage_period['renew_count'];
                    $product_total = $new_plan_price;
                    $grand_total = $product_total;
                    $order_display_id = $functionClass->get_order_id();
                
                    $new_order_data = array(
                        'payment_type' => $sponsor_billing_method,
                        'payment_master_id' => 0,
                        'transaction_id' => 0,
                        'payment_processor' => "",
                        'payment_processor_res' => "",
                        'display_id' => $order_display_id,
                        'customer_id' => $ws_row['customer_id'],
                        'product_total' => $product_total,
                        'sub_total' => $product_total,
                        'grand_total' => $grand_total,
                        'post_date' => date("Y-m-d"),
                        'future_payment' => "N",
                        'status' => "Payment Approved",
                        'type' => ($renew_count == 1 ? ",Customer Enrollment," : ",Renewals,"),
                        'is_renewal' => ($renew_count == 1 ? "N" : "Y"),
                        'ip_address' => !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'],
                        'browser' => $BROWSER,
                        'os' => $OS,
                        'req_url' => $REQ_URL,
                        'order_count' => 1,
                        'updated_at' => 'msqlfunc_NOW()',
                        'created_at' => 'msqlfunc_NOW()',
                        'original_order_date' => 'msqlfunc_NOW()',
                    );
                    $new_order_id = $pdo->insert("group_orders",$new_order_data);
               
                    $ws_ids = array();
                    foreach ($order_products as $key => $od_row) {
                        $od_data = array(
                            "website_id" => $od_row['website_id'],
                            "order_id" => $new_order_id,
                            "product_id" => $od_row['product_id'],
                            "plan_id" => $od_row['plan_id'],
                            "fee_applied_for_product" => $new_prd_row['product_type']=='AdminFee' ? $ws_row['fee_applied_for_product'] : $od_row['fee_applied_for_product'],
                            "prd_plan_type_id" => $od_row['prd_plan_type_id'],
                            "product_type" => $od_row['product_type'],
                            "product_name" => $od_row['product_name'],
                            "unit_price" => $od_row['unit_price'],
                            "product_code" => $od_row['product_code'],
                            "family_member" => $od_row['family_member'],
                            "qty" => $od_row['qty'],
                            "start_coverage_period" => $od_row['start_coverage_period'],
                            "end_coverage_period" => $od_row['end_coverage_period'],
                            "renew_count" => $renew_count,
                        );

                        if($is_group_member == 'Y' && $new_plan_row['product_id'] == $od_row['product_id']) {
                            $od_data['member_price'] = $new_plan_row['member_price'];
                            $od_data['group_price'] = $new_plan_row['group_price'];
                            $od_data['contribution_type'] = $new_plan_row['contribution_type'];
                            $od_data['contribution_value'] = $new_plan_row['contribution_value'];
                        }
                        $od_id = $pdo->insert("group_order_details", $od_data);

                        $tmp_ws_row = $pdo->selectOne("SELECT ws.id FROM website_subscriptions ws WHERE ws.product_id=:product_id AND ws.customer_id=:customer_id AND ws.status NOT IN ('Cancel','Inactive')", array(":product_id" => $od_row['product_id'], ":customer_id" => $ws_row['customer_id']));

                        if (!empty($tmp_ws_row)) {
                            $ws_ids[] = $tmp_ws_row['id'];
                            $affected_ws_ids[$tmp_ws_row['id']] = $tmp_ws_row['id'];

                            $ws_history_status = 'Success';
                            $ws_history_message = $description;

                            $ws_history = array(
                                'customer_id' => $ws_row['customer_id'],
                                'website_id' => $tmp_ws_row['id'],
                                'product_id' => $od_row['product_id'],
                                'plan_id' => $od_row['plan_id'],
                                'order_id' => $new_order_id,
                                'status' => $ws_history_status,
                                'message' => $ws_history_message,
                                'attempt' => '',
                                'created_at' => 'msqlfunc_NOW()',
                                'processed_at' => 'msqlfunc_NOW()',
                            );
                            $pdo->insert("website_subscriptions_history", $ws_history);

                            $tmp_ws_data = array(
                                'status' => $member_setting['member_status'],
                                'last_order_id' => $new_order_id,
                                'payment_type' => $sponsor_billing_method,
                                'purchase_date' => 'msqlfunc_NOW()',
                                'last_purchase_date' => 'msqlfunc_NOW()',
                                "start_coverage_period" => $od_row['start_coverage_period'],
                                "end_coverage_period" => $od_row['end_coverage_period'],
                                "fee_applied_for_product" => $new_prd_row['product_type']=='AdminFee' ? $ws_row['fee_applied_for_product'] : $od_row['fee_applied_for_product'],
                                'updated_at' => 'msqlfunc_NOW()',

                            );
                            $pdo->update("website_subscriptions", $tmp_ws_data, array("clause" => "id=:id", "params" => array(":id" => $tmp_ws_row['id'])));
                        }
                    }
                    if (!empty($ws_ids)) {
                        $pdo->update("group_orders", array('subscription_ids' => implode(',', $ws_ids)), array("clause" => "id=:id", "params" => array(":id" => $new_order_id)));
                    }
                }
            } else {
                /*----- Individual -----*/
                {
                    $order_products = array();
                    $order_products[] = array(
                        "website_id" => $new_ws_id,
                        "product_id" => $new_plan_row['product_id'],
                        "plan_id" => $new_plan_row['plan_id'],
                        "fee_applied_for_product" => 0,
                        "prd_plan_type_id" => $new_plan_row['prd_plan_type_id'],
                        "product_type" => $new_plan_row['product_type'],
                        "product_name" => $new_plan_row['product_name'],
                        "unit_price" => $new_plan_row['price'],
                        "product_code" => $new_plan_row['product_code'],
                        "family_member" => (!empty($extra_detail['dependants']) ? count($extra_detail['dependants']) : 0),
                        "qty" => 1,
                        "start_coverage_period" => $coverage_period['start_coverage_period'],
                        "end_coverage_period" => $coverage_period['end_coverage_period'],
                    );

                    $renew_count = $coverage_period['renew_count'];
                    $refunded_order_id = 0;
                    $is_post_date_order = false;
                    $order_row = array();
                    $payment_mode = $billing_row["payment_mode"];
                    $payment_approved = false;
                    $txn_id = 0;
                    $service_fee = 0.0;
                    $product_total = $new_plan_price;
                    $shipping_charge = 0.0;
                    $tax_charge = 0.0;
                    $discount = 0.0;
                    $grand_total = $product_total;

                    $tmp_res = subscription_is_paid_for_coverage_period($ws_id, $coverage_period['start_coverage_period']);

                    if (!empty($tmp_res['order_id'])) {
                        $refunded_order_id = $tmp_res['order_id'];

                        if ($tmp_res['is_post_date_order'] == true) {
                            $order_row = $pdo->selectOne("SELECT * FROM orders WHERE id=:id",array(":id" => $refunded_order_id));

                            $product_total = (($order_row['product_total'] + $new_plan_price) - $old_plan_price);
                            $grand_total = (($order_row['grand_total'] + $new_plan_price) - $old_plan_price);

                            $order_detail_res = $pdo->select("SELECT * FROM order_details WHERE order_id=:order_id AND website_id!=:website_id AND is_deleted='N'",array(":order_id" => $refunded_order_id,":website_id" => $ws_id));
                            
                            foreach ($order_detail_res as $order_detail_row) {
                                $order_products[] = $order_detail_row;
                            }

                            //update old order status
                            $is_post_date_order = true;
                            $old_ord_data = array('status' => 'Cancelled', 'updated_at' => 'msqlfunc_NOW()');
                            $old_ord_where = array("clause" => "id=:id", "params" => array(":id" => $refunded_order_id));
                            $pdo->update("orders", $old_ord_data, $old_ord_where);
                            $transParams = array("reason" => "Order Cancelled When Benefit Tier Update");
                            $transactionInsId = $functionClass->transaction_insert($refunded_order_id, 'Debit', 'Cancelled', 'Transaction Cancelled',0,$transParams);
                        } else {
                            cancel_order($refunded_order_id, array("ws_row" => $ws_row, 'description' => $description, 'requested_by' => $requested_by, 'is_partial_refund' => 'Y',"adminfeeRefundAmt" => $refundAdminFee,'adminFeePrd' => $feeOldPrdId));
                            $is_refund = true;
                        }
                    }

                    
                }

                {
                    $order_display_id = $functionClass->get_order_id();
                    $cc_params = array();
                    $cc_params['order_id'] = $order_display_id;
                    $cc_params['amount'] = $grand_total;
                    if ($payment_mode == "ACH") {
                        $cc_params['ach_account_type'] = $billing_row['ach_account_type'];
                        $cc_params['ach_routing_number'] = $billing_row['ach_routing_number'];
                        $cc_params['ach_account_number'] = $billing_row['ach_account_number'];
                        $cc_params['name_on_account'] = $billing_row['fname'] . ' ' . $billing_row['lname'];
                        $cc_params['bankname'] = $billing_row['bankname'];
                    } else {
                        $cc_params['ccnumber'] = $billing_row['cc_no'];
                        $cc_params['card_type'] = $billing_row['card_type'];
                        $cc_params['ccexp'] = str_pad($billing_row['expiry_month'], 2, "0", STR_PAD_LEFT) . substr($billing_row['expiry_year'], -2);
                    }
                    $cc_params['description'] = $description;
                    $cc_params['firstname'] = $billing_row['fname'];
                    $cc_params['lastname'] = $billing_row['lname'];
                    $cc_params['address1'] = $billing_row['address'];
                    $cc_params['city'] = $billing_row['city'];
                    $cc_params['state'] = $billing_row['state'];
                    $cc_params['zip'] = $billing_row['zip'];
                    $cc_params['country'] = 'USA';
                    $cc_params['phone'] = !empty($billing_row['phone']) ? $billing_row['phone'] : $customer_row['cell_phone'];
                    $cc_params['email'] = $customer_row['email'];
                    

                    $payment_master_id = $functionClass->get_agent_merchant_detail(array($new_plan_row['plan_id']),$customer_row['sponsor_id'],$payment_mode,array('is_renewal' => ($renew_count == 1 ? "N" : "Y"),'customer_id' => $ws_row['customer_id']));
                    $payment_processor = "";
                    if(!empty($payment_master_id)){
                        $payment_processor = getname('payment_master',$payment_master_id,'processor_id');
                    }
                    $cc_params['processor'] = $payment_processor;

                    if ($is_post_date_order == true) {
                        $payment_approved = true;
                        $txn_id = 0;
                        $payment_res = array('status' => 'Success', 'transaction_id' => 0, 'message' => "Post Payment Order");
                    } else {
                        if ($grand_total == 0) {
                            $payment_res = array('status' => 'Success', 'transaction_id' => 0, 'message' => "Bypass payment API due to order have zero amount.");
                        } else {
                            if ($payment_mode == "ACH") {
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
                        }
                    }

                    if ($payment_res['status'] == 'Success') {
                        $payment_approved = true;
                        $txn_id = $payment_res['transaction_id'];
                    } else {
                        $is_billing_process_success = false;
                        $payment_approved = false;
                        $cc_params['order_type'] = $description;
                        $cc_params['browser'] = $BROWSER;
                        $cc_params['os'] = $OS;
                        $cc_params['req_url'] = $REQ_URL;
                        $cc_params['err_text'] = $payment_res['message'];
                        $payment_error = $payment_res['message'];
                        $decline_log_id = $functionClass->credit_card_decline_log($ws_row['customer_id'], $cc_params, $payment_res);
                    }
                }

                $member_setting = $memberSetting->get_status_by_payment($payment_approved,"",$is_post_date_order);

                {
                    if($adminFeeAmt != 0 && ($action == "benefit_tier_change" || $action == "policy_change")){
                        $product_total = $product_total+$adminFeeAmt;
                        $grand_total = $grand_total+$adminFeeAmt;
                    }
                    
                    $new_order_data = array(
                        'payment_type' => $payment_mode,
                        'payment_master_id' => $payment_master_id,
                        'transaction_id' => $txn_id,
                        'payment_processor' => $payment_processor,
                        'payment_processor_res' => json_encode($payment_res),
                        'display_id' => $order_display_id,
                        'customer_id' => $ws_row['customer_id'],
                        'product_total' => $product_total,
                        'sub_total' => $product_total,
                        'grand_total' => $grand_total,
                        'post_date' => date("Y-m-d"),
                        'future_payment' => "N",
                        'status' => ($payment_approved == true ? "Payment Approved" : "Payment Declined"),
                        'type' => ($renew_count == 1 ? ",Customer Enrollment," : ",Renewals,"),
                        'is_renewal' => ($renew_count == 1 ? "N" : "Y"),
                        'ip_address' => !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'],
                        'browser' => $BROWSER,
                        'os' => $OS,
                        'req_url' => $REQ_URL,
                        'order_count' => 1,
                        'updated_at' => 'msqlfunc_NOW()',
                        'created_at' => 'msqlfunc_NOW()',
                        'original_order_date' => 'msqlfunc_NOW()',
                    );

                    if (!empty($is_post_date_order)) {
                        $new_order_data['post_date'] = $order_row['post_date'];
                        $new_order_data['future_payment'] = 'Y';
                        $new_order_data['status'] = 'Post Payment';
                    }
                    if (isset($payment_res['review_require']) && $payment_res['review_require'] == 'Y') {
                        $new_order_data['review_require'] = 'Y';
                    }
                    $new_order_id = $pdo->insert("orders", $new_order_data);

                    $order_billing_info = array(
                        'order_id' => $new_order_id,
                        'customer_id' => $billing_row['customer_id'],
                        'email' => $customer_row['email'],
                        'phone' => !empty($billing_row['phone']) ? $billing_row['phone'] : $customer_row['cell_phone'],
                        'fname' => $billing_row['fname'],
                        'lname' => $billing_row['lname'],
                        'country' => 'United States',
                        'country_id' => 231,
                        'state' => $billing_row['state'],
                        'city' => $billing_row['city'],
                        'zip' => $billing_row['zip'],
                        'address' => $billing_row['address'],
                        'address2' => $billing_row['address2'],
                        'updated_at' => 'msqlfunc_NOW()',
                        'created_at' => 'msqlfunc_NOW()',
                    );

                    if ($payment_mode == "ACH") {
                        $order_billing_info = array_merge($order_billing_info, array(
                                'ach_account_number' => "msqlfunc_AES_ENCRYPT('" . $billing_row['ach_account_number'] . "','" . $CREDIT_CARD_ENC_KEY . "')",
                                'ach_routing_number' => "msqlfunc_AES_ENCRYPT('" . $billing_row['ach_routing_number'] . "','" . $CREDIT_CARD_ENC_KEY . "')",
                                'ach_account_type' => $billing_row['ach_account_type'],
                                'bankname' => $billing_row['bankname'],
                                'payment_mode' => 'ACH',
                                'last_cc_ach_no' => substr($billing_row['ach_account_number'], -4),
                            )
                        );
                    } else {
                        $order_billing_info = array_merge($order_billing_info, array(
                                'card_no' => substr($billing_row['cc_no'], -4),
                                'card_no_full' => "msqlfunc_AES_ENCRYPT('" . $billing_row['cc_no'] . "','" . $CREDIT_CARD_ENC_KEY . "')",
                                'card_type' => $billing_row['card_type'],
                                'expiry_month' => $billing_row['expiry_month'],
                                'expiry_year' => $billing_row['expiry_year'],
                                'payment_mode' => 'CC',
                                'cvv_no' => $billing_row['cvv_no'],
                                'last_cc_ach_no' => substr($billing_row['cc_no'], -4),
                            )
                        );
                    }
                    $pdo->insert('order_billing_info', $order_billing_info);

                    if (empty($billing_row['id'])) {
                        $cb_profile = $order_billing_info;
                        $cb_profile['is_default'] = "Y";
                        $cb_profile['ip_address'] = !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'];
                        $cb_profile['created_at'] = "msqlfunc_NOW()";
                        $billing_row['id'] = $pdo->insert('customer_billing_profile', $cb_profile);
                    }
                }

                {
                    $ws_ids = array();
                    foreach ($order_products as $key => $od_row) {
                        $od_data = array(
                            "website_id" => $od_row['website_id'],
                            "order_id" => $new_order_id,
                            "product_id" => $od_row['product_id'],
                            "plan_id" => $od_row['plan_id'],
                            "fee_applied_for_product" => $od_row['fee_applied_for_product'],
                            "prd_plan_type_id" => $od_row['prd_plan_type_id'],
                            "product_type" => $od_row['product_type'],
                            "product_name" => $od_row['product_name'],
                            "unit_price" => $od_row['unit_price'],
                            "product_code" => $od_row['product_code'],
                            "family_member" => $od_row['family_member'],
                            "qty" => $od_row['qty'],
                            "start_coverage_period" => $od_row['start_coverage_period'],
                            "end_coverage_period" => $od_row['end_coverage_period'],
                            "renew_count" => $renew_count,
                        );

                        if($is_group_member == 'Y' && $new_plan_row['product_id'] == $od_row['product_id']) {
                            $od_data['member_price'] = $new_plan_row['member_price'];
                            $od_data['group_price'] = $new_plan_row['group_price'];
                            $od_data['contribution_type'] = $new_plan_row['contribution_type'];
                            $od_data['contribution_value'] = $new_plan_row['contribution_value'];
                        }
                        $od_id = $pdo->insert("order_details", $od_data);
                        
                        $od_coverage = $od_row['start_coverage_period'] . ' - ' . $od_row['end_coverage_period'];

                        if (!in_array($od_coverage, $coverage_periods_array)) {
                            array_push($coverage_periods_array, $od_coverage);
                        }

                        $tmp_ws_row = $pdo->selectOne("SELECT ws.id FROM website_subscriptions ws WHERE ws.id=:website_id AND ws.customer_id=:customer_id", array(":website_id" => $od_row['website_id'], ":customer_id" => $ws_row['customer_id']));
                        if (!empty($tmp_ws_row)) {
                            $ws_ids[] = $tmp_ws_row['id'];
                            $affected_ws_ids[$tmp_ws_row['id']] = $tmp_ws_row['id'];

                            if ($is_post_date_order == false) {
                                if ($payment_approved == true) {
                                    $ws_history_status = 'Success';
                                    $ws_history_message = $description;
                                } else {
                                    $ws_history_status = 'Fail';
                                    $ws_history_message = ($description . '. Error: ' . $payment_error);
                                }
                            } else {
                                $ws_history_status = 'Success';
                                $ws_history_message = ('Post Payment: ' . $description);
                            }

                            $ws_history = array(
                                'customer_id' => $ws_row['customer_id'],
                                'website_id' => $tmp_ws_row['id'],
                                'product_id' => $od_row['product_id'],
                                'plan_id' => $od_row['plan_id'],
                                'order_id' => $new_order_id,
                                'status' => $ws_history_status,
                                'message' => $ws_history_message,
                                'attempt' => '',
                                'created_at' => 'msqlfunc_NOW()',
                                'processed_at' => 'msqlfunc_NOW()',
                            );
                            $pdo->insert("website_subscriptions_history", $ws_history);

                            
                            if ($is_post_date_order == false) {
                                $tmp_ws_data = array(
                                    'status' => $member_setting['policy_status'],
                                    'last_order_id' => $new_order_id,
                                    'payment_type' => ($payment_mode == "ACH"?"ACH":"CC"),
                                    'purchase_date' => 'msqlfunc_NOW()',
                                    'last_purchase_date' => 'msqlfunc_NOW()',
                                    "start_coverage_period" => $od_row['start_coverage_period'],
                                    "end_coverage_period" => $od_row['end_coverage_period'],
                                    "fee_applied_for_product" => $od_row['fee_applied_for_product'],
                                    'updated_at' => 'msqlfunc_NOW()',
                                );
                            } else {
                                $tmp_ws_data = array(
                                    'last_order_id' => $new_order_id,
                                    'updated_at' => 'msqlfunc_NOW()',
                                );
                            }
                            $pdo->update("website_subscriptions", $tmp_ws_data, array("clause" => "id=:id", "params" => array(":id" => $tmp_ws_row['id'])));
                        }
                    }

                    if(($action == "benefit_tier_change" || $action == "policy_change") && !empty($adminSubId)){
                        foreach ($order_products as $key => $od_row) {
                            if($od_row['product_id'] = $fee_applied_for_product){
                                $od_data = array(
                                    "website_id" => $adminSubId,
                                    "order_id" => $new_order_id,
                                    "product_id" => $feePrdId,
                                    "plan_id" => $feePlanId,
                                    "fee_applied_for_product" => $fee_applied_for_product,
                                    "prd_plan_type_id" => $od_row['prd_plan_type_id'],
                                    "product_type" => 'Fees',
                                    "product_name" => $feePrdName,
                                    "unit_price" => $adminFeeAmt,
                                    "product_code" => $feePrdCode,
                                    "family_member" => $od_row['family_member'],
                                    "qty" => $od_row['qty'],
                                    "start_coverage_period" => $od_row['start_coverage_period'],
                                    "end_coverage_period" => $od_row['end_coverage_period'],
                                    "renew_count" => $renew_count,
                                );
        
                                if($is_group_member == 'Y' && $new_plan_row['product_id'] == $od_row['product_id']) {
                                    $od_data['member_price'] = 0;
                                    $od_data['group_price'] = 0;
                                    $od_data['contribution_type'] = '';
                                    $od_data['contribution_value'] = '';
                                }
                                $od_id = $pdo->insert("order_details", $od_data);
                                $tmp_ws_fee_row = $pdo->selectOne("SELECT ws.id FROM website_subscriptions ws WHERE ws.id=:website_id AND ws.customer_id=:customer_id", array(":website_id" => $adminSubId, ":customer_id" => $ws_row['customer_id']));
                                if (!empty($tmp_ws_fee_row)) {
                                    $ws_ids[] = $tmp_ws_fee_row['id'];
                                    
                                    $affected_ws_ids[$tmp_ws_fee_row['id']] = $tmp_ws_fee_row['id'];
                                    if ($is_post_date_order == false) {
                                        if ($payment_approved == true) {
                                            $ws_history_status = 'Success';
                                            $ws_history_message = $description;
                                        } else {
                                            $ws_history_status = 'Fail';
                                            $ws_history_message = ($description . '. Error: ' . $payment_error);
                                        }
                                    } else {
                                        $ws_history_status = 'Success';
                                        $ws_history_message = ('Post Payment: ' . $description);
                                    }
                                    $ws_history = array(
                                        'customer_id' => $ws_row['customer_id'],
                                        'website_id' => $tmp_ws_fee_row['id'],
                                        'product_id' => $feePrdId,
                                        'plan_id' => $feePlanId,
                                        'order_id' => $new_order_id,
                                        'status' => $ws_history_status,
                                        'message' => $ws_history_message,
                                        'admin_id' => checkIsset($_SESSION['admin']['id']),
                                        'attempt' => '',
                                        'created_at' => 'msqlfunc_NOW()',
                                        'processed_at' => 'msqlfunc_NOW()',
                                    );
                                    $pdo->insert("website_subscriptions_history", $ws_history);
                                    
                                    if ($is_post_date_order == false) {
                                        $tmp_ws_data = array(
                                            'status' => $member_setting['policy_status'],
                                            'last_order_id' => $new_order_id,
                                            'payment_type' => ($payment_mode == "ACH"?"ACH":"CC"),
                                            'purchase_date' => 'msqlfunc_NOW()',
                                            'last_purchase_date' => 'msqlfunc_NOW()',
                                            "start_coverage_period" => $od_row['start_coverage_period'],
                                            "end_coverage_period" => $od_row['end_coverage_period'],
                                            "fee_applied_for_product" => $fee_applied_for_product,
                                            'updated_at' => 'msqlfunc_NOW()',
                                        );
                                    } else {
                                        $tmp_ws_data = array(
                                            'last_order_id' => $new_order_id,
                                            'updated_at' => 'msqlfunc_NOW()',
                                        );
                                    }
                                    $pdo->update("website_subscriptions", $tmp_ws_data, array("clause" => "id=:id", "params" => array(":id" => $tmp_ws_fee_row['id'])));
                                }
                            }
                        }
                    }

                    if ($is_post_date_order == false) {
                        $txn_id = $payment_res['transaction_id'];
                        if ($payment_approved == true) {
                            if ($payment_mode != "ACH") {
                                $transactionInsId = $functionClass->transaction_insert($new_order_id, 'Credit', ($renew_count == 1 ? "New Order" : "Renewal Order"), 'Transaction Approved', '', array("transaction_id" => $txn_id, 'transaction_response' => $payment_res));
                            } else {
                                $transactionInsId = $functionClass->transaction_insert($new_order_id, 'Credit', 'Pending', 'Settlement Transaction', '', array("transaction_id" => $txn_id, 'transaction_response' => $payment_res));
                            }
                        } else {
                            $transactionInsId = $functionClass->transaction_insert($new_order_id, 'Failed', 'Payment Declined', 'Transaction Declined', '', array("transaction_id" => $txn_id, 'transaction_response' => $payment_res,"reason" => checkIsset($payment_error),'cc_decline_log_id'=>checkIsset($decline_log_id)));
                        }
                    } else {
                        // Insert in transactions for post date order
                        $transactionInsId = $functionClass->transaction_insert($new_order_id, 'Credit', 'Pending', 'Post Transaction');
                    }

                    if (!empty($ws_ids)) {
                        $pdo->update("orders", array('subscription_ids' => implode(',', $ws_ids)), array("clause" => "id=:id", "params" => array(":id" => $new_order_id)));
                    }
                }

                /*----- Order -----*/
                //********* Payable Insert Code Start ********************
                if ($payment_approved == true && $is_post_date_order == false) {
                    if ($payment_mode != "ACH") {
                        $payable_params = array(
                            'payable_type' => 'Vendor',
                            'type' => 'Vendor',
                            'transaction_tbl_id' => $transactionInsId['id'],
                        );
                        $payable = $functionClass->payable_insert($new_order_id, 0, 0, 0, $payable_params);
                    }
                }
                //********* Payable Insert Code End   ********************
                //Charge mew order here
            }

        }//Foreach loop closed

        //Set On Hold Billing Failed to customer, ce & ws  
        if ($is_billing_process_success == false) {
            $cust_update_data = array('status' => $member_setting['member_status'], 'updated_at' => 'msqlfunc_NOW()');
            $cust_update_where = array("clause" => 'id=:id', 'params' => array(":id" => $ws_row['customer_id']));
            $pdo->update("customer", $cust_update_data, $cust_update_where);

            if (!empty($affected_ws_ids)) {
                foreach ($affected_ws_ids as $key => $affected_ws_id) {
                    $affected_ws_data = array('status' => $member_setting['policy_status'], 'updated_at' => 'msqlfunc_NOW()');
                    $affected_ws_where = array("clause" => 'id=:id', 'params' => array(":id" => $affected_ws_id));
                    $pdo->update("website_subscriptions", $affected_ws_data, $affected_ws_where);
                }
            }
        }
    }

    //$functionClass->insert_member_terms($ws_row['customer_id'], $new_order_id, $extra_params);
    $functionClass->insert_dpg_agreements($ws_row['customer_id'], $new_order_id, $extra_params);
    $functionClass->insert_joinder_agreements($ws_row['customer_id'], $new_order_id, $ws_row['application_type']);

    if ($action == "policy_change") {
        $success_message = "Policy Changed Successfully";

    } elseif($action == "benefit_amount_change") {
        $success_message = "Policy Benefit Amount Changed Successfully";
    
    } else {
        $success_message = "Policy Benefit Tier Changed Successfully";
    }

    if ($is_refund) {
        $coverage_period_list = !empty($coverage_periods_array) ? implode(",", $coverage_periods_array) : '';
        if (!empty($coverage_period_list)) {
            //$success_message = "There are two payments for coverage period " . $coverage_period_list . ". Consider refunding the appropriate coverage period.";
        }
    }
    return array("status" => true, "message" => $success_message, "is_refund" => $is_refund);
}

function cancel_tier_change($ws_id,$requested_by = 'admin')
{
    global $pdo,$ADMIN_HOST;
    $enrollDate = new enrollmentDate();
    $functionClass = new functionsList();
    $memberSetting = new memberSetting();

    $extra = array("is_cancel_benefit_tier" => true);
    $member_status = $memberSetting->get_status_by_change_benefit_tier("","","","",$extra);

    $ws_sql = "SELECT w.* FROM website_subscriptions w WHERE w.id=:id";
    $ws_where = array(':id' => $ws_id);
    $ws_row = $pdo->selectOne($ws_sql, $ws_where);

    $customer_sql = "SELECT c.*,
                IFNULL(s.payment_master_id,0) AS payment_master_id,
                IFNULL(s.ach_master_id,0) AS ach_master_id,s.type as sponsor_type
                FROM customer c
                JOIN customer s ON(s.id=c.sponsor_id)
                WHERE c.id=:customer_id";
    $customer_row = $pdo->selectOne($customer_sql, array(":customer_id" => $ws_row['customer_id']));

    $sponsor_billing_method = "individual";
    $is_group_member = 'N';
    $new_ws_row = "";
    if($customer_row['sponsor_type'] == "Group") {
        $is_group_member = 'Y';

        $sqlBillingType = "SELECT billing_type FROM customer_group_settings where customer_id=:customer_id";
        $resBillingType = $pdo->selectOne($sqlBillingType,array(":customer_id"=>$customer_row['sponsor_id']));
        if(!empty($resBillingType['billing_type'])){
            $sponsor_billing_method = $resBillingType['billing_type'];
        }
    }

    $ce_sql = "SELECT ce.*
                FROM customer_enrollment ce
                WHERE ce.website_id=:website_id";
    $ce_row = $pdo->selectOne($ce_sql, array(":website_id" => $ws_row['id']));
    $ce_id = $ce_row['id'];

    $new_ce_sql = "SELECT * FROM customer_enrollment WHERE parent_coverage_id=:id AND process_status='Pending'";
    $new_ce_row = $pdo->selectOne($new_ce_sql, array(":id" => $ce_id));

    if(!empty($new_ce_row)){
        $new_ws_sql = "SELECT w.* FROM website_subscriptions w WHERE w.id=:id";
        $new_ws_where = array(':id' => $new_ce_row['website_id']);
        $new_ws_row = $pdo->selectOne($new_ws_sql, $new_ws_where);
    }

    if ($new_ce_row && $new_ws_row) {
        
        $new_prd_row = get_product_row($new_ws_row['product_id']);
        $new_plan_row = array(
            'id' => $new_ws_row['plan_id'],
            'plan_id' => $new_ws_row['plan_id'],
            'product_id' => $new_ws_row['product_id'],
            'plan_type' => $new_ws_row['prd_plan_type_id'],
            'prd_plan_type_id' => $new_ws_row['prd_plan_type_id'],
            'price' => $new_ws_row['price'],
            'member_price' => $new_ws_row['member_price'],
            'group_price' => $new_ws_row['group_price'],
            'display_member_price' => $new_ws_row['member_price'],
            'display_group_price' => $new_ws_row['group_price'],
            'contribution_type' => $new_ws_row['contribution_type'],
            'contribution_value' => $new_ws_row['contribution_value'],
            'product_code' => $new_prd_row['product_code'],
            'product_name' => $new_prd_row['name'],
            'product_type' => $new_prd_row['type'],
            'plan_type_title' => getPlanName("",$new_ws_row['prd_plan_type_id']),
        );

        //Old Plan Row
        $old_prd_row = get_product_row($ws_row['product_id']);
        $old_plan_row = array(
            'id' => $ws_row['plan_id'],
            'plan_id' => $ws_row['plan_id'],
            'product_id' => $ws_row['product_id'],
            'plan_type' => $ws_row['prd_plan_type_id'],
            'prd_plan_type_id' => $ws_row['prd_plan_type_id'],
            'price' => $ws_row['price'],
            'member_price' => $ws_row['member_price'],
            'group_price' => $ws_row['group_price'],
            'display_member_price' => $ws_row['member_price'],
            'display_group_price' => $ws_row['group_price'],
            'contribution_type' => $ws_row['contribution_type'],
            'contribution_value' => $ws_row['contribution_value'],
            'product_code' => $old_prd_row['product_code'],
            'product_name' => $old_prd_row['name'],
            'product_type' => $old_prd_row['type'],
            'plan_type_title' => getPlanName("",$ws_row['prd_plan_type_id']),
        );

        $policy_change_reason = 'benefit_tier_change';
        if(!empty($new_ws_row['policy_change_reason'])) {
            $policy_change_reason = $new_ws_row['policy_change_reason'];
        }

        $termination_reason = 'Cancelled Benefit Tier Change';

        if($policy_change_reason == "policy_change") {

            $termination_reason = 'Cancelled Policy Change';

        } elseif($policy_change_reason == "benefit_amount_change") {

            $termination_reason = 'Cancelled Benefit Amount Change';
        }

        $ce_update_params = array(
            "process_status" => 'Cancelled',
        );
        $ce_update_where = array(
            "clause" => "website_id=:website_id",
            "params" => array(":website_id" => $new_ce_row['website_id'])
        );
        $pdo->update("customer_enrollment", $ce_update_params, $ce_update_where);

        $new_ws_data = array(
            "status" => $member_status['policy_status'],
            "updated_at" => "msqlfunc_NOW()",
            "termination_date" => $new_ws_row['eligibility_date'],
            "term_date_set" => date('Y-m-d'),
            'termination_reason' => $termination_reason,
        );
        $new_ws_where = array("clause" => "id=:id", "params" => array(":id" => $new_ws_row['id']));
        $pdo->update("website_subscriptions", $new_ws_data, $new_ws_where);

        $web_history_data = array(
            'customer_id' => $new_ws_row['customer_id'],
            'website_id' => $new_ws_row['id'],
            'product_id' => $new_ws_row['product_id'],
            'plan_id' => $new_ws_row['plan_id'],
            'order_id' => 0,
            'status' => 'Update',
            'message' => $termination_reason,
            'authorize_id' => '',
            'processed_at' => 'msqlfunc_NOW()',
            'created_at' => 'msqlfunc_NOW()',
        );
        $pdo->insert("website_subscriptions_history", $web_history_data);

        /*-------- update new dependents --------*/
        $tmp_update_cd_data = array(
            'terminationDate' => $new_ws_row['eligibility_date'],
            'status' => $member_status['dependent_status'],
            'updated_at' => 'msqlfunc_NOW()'
        );
        $tmp_update_cd_where = array(
            "clause" => "website_id=:website_id",
            "params" => array(":website_id" => $new_ws_row['id'])
        );
        $pdo->update('customer_dependent', $tmp_update_cd_data, $tmp_update_cd_where);
        

         /*--- update fee_applied_for_product -----*/
        $tmp_ws_data = array(
            'fee_applied_for_product' => $ws_row['product_id'],
            "updated_at" => "msqlfunc_NOW()",
        );
        $tmp_ws_where = array("clause" => "fee_applied_for_product=:fee_applied_for_product AND customer_id=:customer_id", "params" => array(":fee_applied_for_product" => $new_ws_row['product_id'],":customer_id" => $ws_row['customer_id']));
        $pdo->update("website_subscriptions", $tmp_ws_data, $tmp_ws_where);
        /*--- update fee_applied_for_product -----*/


        /*-------- update old dependents --------*/
        $tmp_update_cd_data = array(
            'terminationDate' => NULL,
            'status' => $member_status['old_dependent_status'],
            'updated_at' => 'msqlfunc_NOW()'
        );
        $tmp_update_cd_where = array(
            "clause" => "website_id=:website_id AND terminationDate=:termination_date",
            "params" => array(":website_id" => $ws_row['id'],"termination_date"=>$ws_row['termination_date'])
        );
        $pdo->update('customer_dependent', $tmp_update_cd_data, $tmp_update_cd_where);
        
        $ce_update_params = array(
            "new_plan_id" => '0',
            "process_status" => '',
            "tier_change_date" => NULL,
        );
        $ce_update_where = array(
            "clause" => "website_id=:website_id",
            "params" => array(":website_id" => $ce_row['website_id'])
        );
        $pdo->update("customer_enrollment", $ce_update_params, $ce_update_where);
        /*-------/Update Customer Enrollment -------*/

        /*------ Refund order if payment received //Need to review and enable this ------------*/
        //refund_new_plan_order_when_cancel_update($ws_row['id'],$new_ws_row['id']);
        /*------/Refund order if payment received ------------*/

        /*---------- Update Subscription to Cancelled for updated plan -----------*/
        $old_ws_data = array(
            "total_attempts" => 0,
            "next_attempt_at" => NULL,
            "renew_count" => $ws_row['renew_count'],
            "updated_at" => "msqlfunc_NOW()",
            "termination_date" => NULL,
            "term_date_set" => NULL,
            "termination_reason" => '',
            "policy_change_reason" => '',
        );
        if(strtotime($ws_row['next_purchase_date']) <= strtotime(date('Y-m-d'))) {
            $old_ws_data['next_purchase_date'] = date('Y-m-d',strtotime('+1 day'));
        }
        $old_ws_where = array("clause" => "id=:id", "params" => array(":id" => $ws_row['id']));
        $pdo->update("website_subscriptions", $old_ws_data, $old_ws_where);       

        if($policy_change_reason == "benefit_amount_change") {
            /*--- update benefit amount ----*/
            $ba_data = array(
                'customer_id' => $ws_row['customer_id'],
                'product_id' => $ws_row['product_id'],
                'type'=>'Primary',
                'amount'=>$ws_row['benefit_amount'],
            );
            $ba_sql="SELECT id FROM customer_benefit_amount where is_deleted='N' AND customer_id=:customer_id AND product_id=:product_id AND type='Primary'";
            $ba_row = $pdo->selectOne($ba_sql,array(":customer_id"=>$ws_row['customer_id'],":product_id"=>$ws_row['product_id']));
            if(!empty($ba_row)) {
                $ba_where = array("clause" => "id=:id", "params" => array(":id" => $ba_row['id']));
                $pdo->update("customer_benefit_amount",$ba_data,$ba_where);
            } else {
                $pdo->insert("customer_benefit_amount",$ba_data);
            }

            $old_cd_sql = "SELECT cd.* FROM customer_dependent cd WHERE cd.website_id=:website_id";
            $old_cd_where = array(':website_id' => $ws_row['id']);
            $old_cd_res = $pdo->select($old_cd_sql, $old_cd_where);
            if(!empty($old_cd_res)) {
                foreach ($old_cd_res as $key => $old_cd_row) {
                    if((!empty($old_cd_row['benefit_amount']) && $old_cd_row['benefit_amount'] > 0) || !empty($old_cd_row['in_patient_benefit']) || !empty($old_cd_row['out_patient_benefit']) || !empty($old_cd_row['monthly_income']) || !empty($old_cd_row['benefit_percentage'])) {
                        $dep_benefit_param = array(
                            "benefit_amount" => checkIsset($old_cd_row['benefit_amount']),
                            "in_patient_benefit" => checkIsset($old_cd_row['in_patient_benefit']),
                            "out_patient_benefit" => checkIsset($old_cd_row['out_patient_benefit']),
                            "monthly_income" => checkIsset($old_cd_row['monthly_income']),
                            "benefit_percentage" => checkIsset($old_cd_row['benefit_percentage']),
                        );
                        save_customer_dependent_profile_benefit_amount($old_cd_row['cd_profile_id'],$old_cd_row['product_id'],$dep_benefit_param);
                    }                    
                }
            }
            /*---/update benefit amount ----*/
        }        

        $dp_extra_params = array();
        $af_message = 'cancelled benefit tier change';

        if($policy_change_reason == "policy_change") {
            $af_message = 'cancelled policy change';

        } elseif($policy_change_reason == "benefit_amount_change") {
            $af_message = 'cancelled benefit amount change';
            $dp_extra_params['display_benefit_amount'] = 1;
        }
        $activity_id = 0;
        if ($requested_by == 'admin') {
            $af_desc = array();
            $af_desc['ac_message'] =array(
                'ac_red_1'=>array(
                    'href'=> 'admin_profile.php?id='.md5($_SESSION['admin']['id']),
                    'title'=> $_SESSION['admin']['display_id'],
                ),
                'ac_message_1' => $af_message.' on ',
                'ac_red_2'=>array(
                    'href'=> 'members_details.php?id='.md5($customer_row['id']),
                    'title'=>$customer_row['rep_id'],
                ),
                'ac_message_2' =>' <br/> Old Policy : '.display_policy($ws_row['id'],$dp_extra_params).' <br/> New Policy : '. display_policy($new_ws_row['id'],$dp_extra_params) . '<br/> Effective From : '.displayDate($new_ws_row['eligibility_date']),
            );
            $activity_id = activity_feed(3, $_SESSION['admin']['id'], 'Admin',$customer_row['id'], 'customer', 'Admin '. ucwords($af_message),'','',json_encode($af_desc));

        } elseif ($requested_by == 'agent') {
            $af_desc = array();
            $af_desc['ac_message'] =array(
                'ac_red_1'=>array(
                    'href'=> 'agent_detail_v1.php?id='.md5($_SESSION['agents']['id']),
                    'title'=> $_SESSION['agents']['rep_id'],
                ),
                'ac_message_1' => $af_message.' on ',
                'ac_red_2'=>array(
                    'href'=> 'members_details.php?id='.md5($customer_row['id']),
                    'title'=>$customer_row['rep_id'],
                ),
                'ac_message_2' =>' <br/> Old Policy : '.display_policy($ws_row['id'],$dp_extra_params).' <br/> New Policy : '. display_policy($new_ws_row['id'],$dp_extra_params) . '<br/> Effective From : '.displayDate($new_ws_row['eligibility_date']),
            );
            $activity_id = activity_feed(3, $_SESSION['agents']['id'], 'Agent',$customer_row['id'], 'customer', 'Agent '. ucwords($af_message),'','',json_encode($af_desc));
        } elseif ($requested_by == 'group') {
            $af_desc = array();
            $af_desc['ac_message'] =array(
                'ac_red_1'=>array(
                    'href'=> $ADMIN_HOST.'/groups_details.php?id='.md5($_SESSION['groups']['id']),
                    'title'=> $_SESSION['groups']['rep_id'],
                ),
                'ac_message_1' => $af_message.' on ',
                'ac_red_2'=>array(
                    'href'=> $ADMIN_HOST.'/members_details.php?id='.md5($customer_row['id']),
                    'title'=>$customer_row['rep_id'],
                ),
                'ac_message_2' =>' <br/> Old Policy : '.display_policy($ws_row['id'],$dp_extra_params).' <br/> New Policy : '. display_policy($new_ws_row['id'],$dp_extra_params) . '<br/> Effective From : '.displayDate($new_ws_row['eligibility_date']),
            );
            $activity_id = activity_feed(3, $_SESSION['groups']['id'], 'Group',$customer_row['id'], 'customer', 'Group '. ucwords($af_message),'','',json_encode($af_desc));
        } elseif ($requested_by == 'system') {
            $af_desc = array();
            $af_desc['ac_message'] =array(
                'ac_red_1'=>array(
                    'title'=> 'System',
                ),
                'ac_message_1' => $af_message.' on ',
                'ac_red_2'=>array(
                    'href'=> $ADMIN_HOST.'/members_details.php?id='.md5($customer_row['id']),
                    'title'=>$customer_row['rep_id'],
                ),
                'ac_message_2' =>' <br/> Old Policy : '.display_policy($ws_row['id'],$dp_extra_params).' <br/> New Policy : '. display_policy($new_ws_row['id'],$dp_extra_params) . '<br/> Effective From : '.displayDate($new_ws_row['eligibility_date']),
            );
            $activity_id = activity_feed(3, 0, 'System',$customer_row['id'], 'customer', 'System '. ucwords($af_message),'','',json_encode($af_desc));
        }

        /*------- Update Policy Document ------*/
        if(!empty($new_ws_row['agreement_id'])) {
            $tmp_extra = array(
                'website_id' => $new_ws_row['id'],
                'old_website_id' => $ws_row['id'],
                'activity_id' => $activity_id,
                'action' => "policy_updated"
            );
            $functionClass->update_member_terms($customer_row['id'],$new_ws_row['id'],$new_ws_row['agreement_id'],$tmp_extra);
        }
        /*-------/Update Policy Document ------*/
    }else if($ws_row['product_type'] == 'Fees'){

        /*-------- update old dependents --------*/
        $tmp_update_cd_data = array(
            'terminationDate' => NULL,
            'status' => $member_status['old_dependent_status'],
            'updated_at' => 'msqlfunc_NOW()'
        );
        $tmp_update_cd_where = array(
            "clause" => "website_id=:website_id AND terminationDate=:termination_date",
            "params" => array(":website_id" => $ws_row['id'],"termination_date"=>$ws_row['termination_date'])
        );
        $pdo->update('customer_dependent', $tmp_update_cd_data, $tmp_update_cd_where);
        
        $ce_update_params = array(
            "new_plan_id" => '0',
            "process_status" => '',
            "tier_change_date" => NULL,
        );
        $ce_update_where = array(
            "clause" => "website_id=:website_id",
            "params" => array(":website_id" => $ce_row['website_id'])
        );
        $pdo->update("customer_enrollment", $ce_update_params, $ce_update_where);
        /*-------/Update Customer Enrollment -------*/

        /*---------- Update Subscription to Cancelled for updated plan -----------*/
        $old_ws_data = array(
            "total_attempts" => 0,
            "next_attempt_at" => NULL,
            "renew_count" => $ws_row['renew_count'],
            "updated_at" => "msqlfunc_NOW()",
            "termination_date" => NULL,
            "term_date_set" => NULL,
            "termination_reason_id" => NULL,
            "policy_change_reason" => '',
        );
        if(strtotime($ws_row['next_purchase_date']) <= strtotime(date('Y-m-d'))) {
            $old_ws_data['next_purchase_date'] = date('Y-m-d',strtotime('+1 day'));
        }
        $old_ws_where = array("clause" => "id=:id", "params" => array(":id" => $ws_row['id']));
        $pdo->update("website_subscriptions", $old_ws_data, $old_ws_where); 
    }
    return true;
}

function update_tier_change_date($ws_id, $change_date, $extra_detail = array(), $requested_by = 'admin')
{
    global $pdo, $CREDIT_CARD_ENC_KEY, $SITE_ENV,$ADMIN_HOST,$TRANSACTION_APPROVED_STATUS;
    $enrollDate = new enrollmentDate();
    $functionClass = new functionsList();
    $memberSetting = new memberSetting();
    
    $REAL_IP_ADDRESS = get_real_ipaddress();
    $enr_prd_ids = get_enrollment_fee_prd_ids();
    $BROWSER = getBrowser();
    $OS = getOS($_SERVER['HTTP_USER_AGENT']);
    $REQ_URL = ($_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
    $coverage_periods_array = array();
    $change_date = date('Y-m-d', strtotime($change_date));
    $today = date('Y-m-d');
    $decline_log_id="";

    $old_ws_sql = "SELECT ws.* FROM website_subscriptions ws WHERE ws.id=:id";
    $ws_row = $pdo->selectOne($old_ws_sql, array(":id" => $ws_id));

    $old_ce_sql = "SELECT * FROM customer_enrollment WHERE website_id=:website_id";
    $old_ce_row = $pdo->selectOne($old_ce_sql, array(":website_id" => $ws_row['id']));
    $ce_id = $old_ce_row['id'];

    $new_ce_sql = "SELECT ce.* 
                    FROM customer_enrollment ce 
                    WHERE ce.parent_coverage_id=:parent_coverage_id AND ce.process_status='Pending'";
    $new_ce_row = $pdo->selectOne($new_ce_sql, array(":parent_coverage_id" => $old_ce_row['id']));

    $new_ws_sql = "SELECT ws.* FROM website_subscriptions ws WHERE ws.id=:id";
    $new_ws_row = $pdo->selectOne($new_ws_sql, array(":id" => $new_ce_row['website_id']));

    if (empty($ws_row) || empty($new_ws_row)) {
        return array("status" => false, "error" => "subscription_not_found", "message" => "Subscription not found");
    }

    $customer_sql = "SELECT c.*,
                IFNULL(s.payment_master_id,0) AS payment_master_id,
                IFNULL(s.ach_master_id,0) AS ach_master_id,s.type as sponsor_type
                FROM customer c
                JOIN customer s ON(s.id=c.sponsor_id)
                WHERE c.id=:customer_id";
    $customer_row = $pdo->selectOne($customer_sql, array(":customer_id" => $ws_row['customer_id']));

    $sponsor_billing_method = "individual";
    $is_group_member = 'N';
    if($customer_row['sponsor_type'] == "Group") {
        $is_group_member = 'Y';

        $sqlBillingType = "SELECT billing_type FROM customer_group_settings where customer_id=:customer_id";
        $resBillingType = $pdo->selectOne($sqlBillingType,array(":customer_id"=>$customer_row['sponsor_id']));
        if(!empty($resBillingType['billing_type'])){
            $sponsor_billing_method = $resBillingType['billing_type'];
        }
    }

    $policy_change_reason = 'benefit_tier_change';
    if(!empty($new_ws_row['policy_change_reason'])) {
        $policy_change_reason = $new_ws_row['policy_change_reason'];        
    }

    $termination_reason = 'Benefit Tier Change';
    
    if($policy_change_reason == "policy_change") {
        $termination_reason = 'Policy Change';    
    
    } elseif($policy_change_reason == "benefit_amount_change") {
        $termination_reason = 'Benefit Amount Change';
    }

    $is_list_bill_enroll = "N";

    //New Plan Row
    $new_prd_row = get_product_row($new_ws_row['product_id']);
    $new_plan_row = array(
        'id' => $new_ws_row['plan_id'],
        'plan_id' => $new_ws_row['plan_id'],
        'product_id' => $new_ws_row['product_id'],
        'plan_type' => $new_ws_row['prd_plan_type_id'],
        'prd_plan_type_id' => $new_ws_row['prd_plan_type_id'],
        'price' => $new_ws_row['price'],
        'member_price' => $new_ws_row['member_price'],
        'group_price' => $new_ws_row['group_price'],
        'display_member_price' => $new_ws_row['member_price'],
        'display_group_price' => $new_ws_row['group_price'],
        'contribution_type' => $new_ws_row['contribution_type'],
        'contribution_value' => $new_ws_row['contribution_value'],
        'product_code' => $new_prd_row['product_code'],
        'product_name' => $new_prd_row['name'],
        'product_type' => $new_prd_row['type'],
        'plan_type_title' => getPlanName("",$new_ws_row['prd_plan_type_id']),
    );
    $new_plan_price = $new_ws_row['price'];

    //Old Plan Row
    $old_prd_row = get_product_row($ws_row['product_id']);
    $old_plan_row = array(
        'id' => $ws_row['plan_id'],
        'plan_id' => $ws_row['plan_id'],
        'product_id' => $ws_row['product_id'],
        'plan_type' => $ws_row['prd_plan_type_id'],
        'prd_plan_type_id' => $ws_row['prd_plan_type_id'],
        'price' => $ws_row['price'],
        'member_price' => $ws_row['member_price'],
        'group_price' => $ws_row['group_price'],
        'display_member_price' => $ws_row['member_price'],
        'display_group_price' => $ws_row['group_price'],
        'contribution_type' => $ws_row['contribution_type'],
        'contribution_value' => $ws_row['contribution_value'],
        'product_code' => $old_prd_row['product_code'],
        'product_name' => $old_prd_row['name'],
        'product_type' => $old_prd_row['type'],
        'plan_type_title' => getPlanName("",$ws_row['prd_plan_type_id']),
    );
    $old_plan_price = $ws_row['price'];

    $next_purchase_date = $ws_row['next_purchase_date'];

    if ($policy_change_reason == "policy_change") {
        $description = ("Policy changed from " . $old_plan_row['product_name'] . " to ." . $new_plan_row['product_name']);
    
    } elseif($policy_change_reason == "benefit_amount_change") {
        $description = 'Benefit Amount Changed for '.$old_plan_row['product_name'] ;
    
    } else {
        $description = ("Plan changed from " . $old_plan_row['plan_type_title'] . " to " . $new_plan_row['plan_type_title']);
    }

    $member_setting = $memberSetting->get_status_by_change_benefit_tier();

    $new_ws_status = $member_setting['policy_status'];
    $new_ce_status = "Pending";
    $new_ce_process_status = "Pending";
    $old_ce_process_status = "Pending";

    $eligibility_date = date("Y-m-d", strtotime($change_date));

    if(strtotime($ws_row['eligibility_date']) == strtotime($eligibility_date)) {
        $termination_date = $ws_row['eligibility_date'];
    } else{
        $termination_date = date('Y-m-d',strtotime('-1 day',strtotime($eligibility_date)));
    }

    $member_setting = $memberSetting->get_status_by_change_benefit_tier($ws_row['eligibility_date'],$change_date,$ws_row['status'],$termination_date);

    $is_proceed_imidiate = false;
    if ((strtotime($ws_row['eligibility_date']) == strtotime($change_date)) || strtotime($change_date) <= strtotime($today)) {
        $is_proceed_imidiate = true;

        $new_ws_status = $member_setting['policy_status'];
        $new_ce_status = $member_setting['policy_status'];

        // if ($ws_row['status'] == "Pending Payment") {
        //     $new_ws_status = $member_setting['policy_status'];
        //     $new_ce_status = $member_setting['policy_status'];
        // } else {
        //     $new_ws_status = $member_setting['policy_status'];
        //     $new_ce_status = $member_setting['policy_status'];
        // }

        $old_ws_status = $member_setting['old_policy_status'];;

        $new_ce_status = "Active";
        $new_ce_process_status = "Active";

        $old_ce_status = "Terminated";
        $old_ce_process_status = "Active";
    }

    /*--- Check all transaction are settled or not ---*/
    if (strtotime($eligibility_date) < strtotime($next_purchase_date) && $sponsor_billing_method == "individual") {
        $coverage_periods = subscription_coverage_periods_form_date($ws_id,$eligibility_date);
        foreach ($coverage_periods as $key => $coverage_period) {
            
            $tmp_res = subscription_is_paid_for_coverage_period($ws_id, $coverage_period['start_coverage_period']);
            
            if (!empty($tmp_res['order_id']) && $tmp_res['is_post_date_order'] == false && $tmp_res["cc_no"] != "4111111111111114") {
                
                if($tmp_res['status'] == "Pending Settlement") {
                    return array("status" => false, "error" => "billing_not_found", "message" => "Payment has not settled, change must be completed upon settled payment.");
                } else {
                    $api = new CyberxPaymentAPI();
                    $cc_params = array();
                    $cc_params['transaction_id'] = $tmp_res['transaction_id'];
                    $transaction_res = $api->getTransactionDetail($cc_params, $tmp_res['payment_master_id']);
                    $api_status = (!empty($transaction_res['api_status']) ? $transaction_res['api_status'] : '');
                    if (!in_array($api_status,$TRANSACTION_APPROVED_STATUS)) {
                        return array("status" => false, "error" => "billing_not_found", "message" => "Payment has not settled, change must be completed upon settled payment.");
                    }
                }
            }
        }
    }
    /*---/Check all transaction are settled or not ---*/

    {
        $member_payment_type = getname('prd_main', $new_ws_row['product_id'], 'member_payment_type', 'id');
        $product_dates = $enrollDate->getCoveragePeriod($eligibility_date, $member_payment_type);

        $startCoveragePeriod = date('Y-m-d', strtotime($product_dates['startCoveragePeriod']));
        $endCoveragePeriod = date('Y-m-d', strtotime($product_dates['endCoveragePeriod']));

        if (strtotime($eligibility_date) < strtotime($next_purchase_date)) {
            $tmp_next_purchase_date = $next_purchase_date;
        } else {
            $nextBillingDate = $enrollDate->getNextBillingDateFromCoverage($endCoveragePeriod);
            $tmp_next_purchase_date = date('Y-m-d', strtotime($nextBillingDate));
        }

        /*----- update new ws ----*/
        $update_new_ws_data = array(
            "total_attempts" => 0,
            "next_attempt_at" => NULL,
            "status" => $new_ws_status,
            "next_purchase_date" => $tmp_next_purchase_date,
            "eligibility_date" => $eligibility_date,
            "start_coverage_period" => $startCoveragePeriod,
            "end_coverage_period" => $endCoveragePeriod,
            "updated_at" => "msqlfunc_NOW()",
        );
        if(strtotime($update_new_ws_data['next_purchase_date']) <= strtotime(date('Y-m-d'))) {
            $update_new_ws_data['next_purchase_date'] = date('Y-m-d',strtotime('+1 day'));
        }
        $update_new_ws_where = array("clause" => "id=:id", "params" => array(":id" => $new_ws_row['id']));
        $pdo->update("website_subscriptions", $update_new_ws_data, $update_new_ws_where);

        /*----- update old ws ----*/
        if ($is_proceed_imidiate == true) {
            $update_old_ws_data = array(
                "status" => $member_setting['policy_status'],
                "updated_at" => "msqlfunc_NOW()",
            );
            $update_old_ws_where = array("clause" => "id=:id", "params" => array(":id" => $ws_row['id']));
            $pdo->update("website_subscriptions", $update_old_ws_data, $update_old_ws_where);
        }
        $old_ws_data = array(
            'termination_date' => $termination_date,
            'term_date_set' => date('Y-m-d'),
            'termination_reason' => $termination_reason,
        );

        $old_ws_where = array("clause" => "id=:id", "params" => array(":id" => $ws_row['id']));
        $pdo->update("website_subscriptions", $old_ws_data, $old_ws_where);

        /*----- update new ce ------*/
        $update_new_ce_data = array(
            "process_status" => $new_ce_process_status,
            "tier_change_date" => $eligibility_date,
        );
        $update_new_ce_where = array("clause" => "website_id=:website_id", "params" => array(":website_id" => $new_ws_row['id']));
        $pdo->update("customer_enrollment", $update_new_ce_data, $update_new_ce_where);

        /*------ update old ce ------*/
        $update_old_ce_data = array(
            "process_status" => $old_ce_process_status,
            "tier_change_date" => $eligibility_date,
        );
        $update_old_ce_where = array("clause" => "website_id=:website_id", "params" => array(":website_id" => $ws_row['id']));
        $pdo->update("customer_enrollment", $update_old_ce_data, $update_old_ce_where);

        /*-------- update new dependents --------*/
        $tmp_update_cd_data = array(
            'eligibility_date' => $eligibility_date,
            'updated_at' => 'msqlfunc_NOW()'
        );

        if ($is_proceed_imidiate == true) {
            $tmp_update_cd_data['status'] = $member_setting['dependent_status'];
        }

        $tmp_update_cd_where = array(
            "clause" => "website_id=:website_id",
            "params" => array(":website_id" => $new_ws_row['id'])
        );
        $pdo->update('customer_dependent', $tmp_update_cd_data, $tmp_update_cd_where);

        /*-------- update old dependents --------*/
         $tmp_update_cd_data = array(
            'terminationDate' => $termination_date,
            'updated_at' => 'msqlfunc_NOW()'
        );

        if ($is_proceed_imidiate == true) {
            $tmp_update_cd_data['status'] = $member_setting['dependent_status'];
        }

        $tmp_update_cd_where = array(
            "clause" => "website_id=:website_id",
            "params" => array(":website_id" => $ws_row['id'])
        );
        $pdo->update('customer_dependent', $tmp_update_cd_data, $tmp_update_cd_where);
    }

    {
        /*--- Activity Feed ---*/
        $dp_extra_params = array();
        $af_message = 'updated future benefit tier change';
        if($policy_change_reason == "policy_change") {
            $af_message = 'updated future policy change';
        
        } elseif($policy_change_reason == "benefit_amount_change") {
            $af_message = 'updated future benefit amount change';
            $dp_extra_params['display_benefit_amount'] = 1;
        }
        $activity_id = 0;
        if ($requested_by == 'admin') {
            $af_desc = array();
            $af_desc['ac_message'] =array(
                'ac_red_1'=>array(
                    'href'=> 'admin_profile.php?id='.md5($_SESSION['admin']['id']),
                    'title'=> $_SESSION['admin']['display_id'],
                ),
                'ac_message_1' => $af_message.' on ',
                'ac_red_2'=>array(
                    'href'=> 'members_details.php?id='.md5($customer_row['id']),
                    'title'=>$customer_row['rep_id'],
                ),
                'ac_message_2' =>' <br/> Old Policy : '.display_policy($ws_row['id'],$dp_extra_params).' <br/> New Policy : '. display_policy($new_ws_row['id'],$dp_extra_params) . '<br/> Effective From : '.displayDate($eligibility_date),
            );
            $activity_id = activity_feed(3, $_SESSION['admin']['id'], 'Admin',$customer_row['id'], 'customer', 'Admin '. ucwords($af_message),'','',json_encode($af_desc));
        } elseif ($requested_by == 'agent') {
            $af_desc = array();
            $af_desc['ac_message'] =array(
                'ac_red_1'=>array(
                    'href'=> 'agent_detail_v1.php?id='.md5($_SESSION['agents']['id']),
                    'title'=> $_SESSION['agents']['rep_id'],
                ),
                'ac_message_1' => $af_message.' on ',
                'ac_red_2'=>array(
                    'href'=> 'members_details.php?id='.md5($customer_row['id']),
                    'title'=>$customer_row['rep_id'],
                ),
                'ac_message_2' =>' <br/> Old Policy : '.display_policy($ws_row['id'],$dp_extra_params).' <br/> New Policy : '. display_policy($new_ws_row['id'],$dp_extra_params) . '<br/> Effective From : '.displayDate($eligibility_date),
            );
            $activity_id = activity_feed(3, $_SESSION['agents']['id'], 'Agent',$customer_row['id'], 'customer', 'Agent '. ucwords($af_message),'','',json_encode($af_desc));
        } elseif ($requested_by == 'group') {
            $af_desc = array();
            $af_desc['ac_message'] =array(
                'ac_red_1'=>array(
                    'href'=> $ADMIN_HOST.'/groups_details.php?id='.md5($_SESSION['groups']['id']),
                    'title'=> $_SESSION['groups']['rep_id'],
                ),
                'ac_message_1' => $af_message.' on ',
                'ac_red_2'=>array(
                    'href'=> $ADMIN_HOST.'/members_details.php?id='.md5($customer_row['id']),
                    'title'=>$customer_row['rep_id'],
                ),
                'ac_message_2' =>' <br/> Old Policy : '.display_policy($ws_row['id'],$dp_extra_params).' <br/> New Policy : '. display_policy($new_ws_row['id'],$dp_extra_params) . '<br/> Effective From : '.displayDate($eligibility_date),
            );
            $activity_id = activity_feed(3, $_SESSION['groups']['id'], 'Group',$customer_row['id'], 'customer', 'Group '. ucwords($af_message),'','',json_encode($af_desc));
        } elseif ($requested_by == 'system') {
            $af_desc = array();
            $af_desc['ac_message'] =array(
                'ac_red_1'=>array(
                    'title'=> 'System',
                ),
                'ac_message_1' => $af_message.' on ',
                'ac_red_2'=>array(
                    'href'=> $ADMIN_HOST.'/members_details.php?id='.md5($customer_row['id']),
                    'title'=>$customer_row['rep_id'],
                ),
                'ac_message_2' =>' <br/> Old Policy : '.display_policy($ws_row['id'],$dp_extra_params).' <br/> New Policy : '. display_policy($new_ws_row['id'],$dp_extra_params) . '<br/> Effective From : '.displayDate($eligibility_date),
            );
            $activity_id = activity_feed(3, 0, 'System',$customer_row['id'], 'customer', 'System '. ucwords($af_message),'','',json_encode($af_desc));
        }
        /*---/Activity Feed ---*/

        /*------- Update Policy Document ------*/
        if(!empty($new_ws_row['agreement_id'])) {
            $tmp_extra = array(
                'website_id' => $new_ws_row['id'],
                'old_website_id' => $ws_row['id'],
                'activity_id' => $activity_id,
                'action' => "policy_updated"
            );
            $functionClass->update_member_terms($customer_row['id'],$new_ws_row['id'],$new_ws_row['agreement_id'],$tmp_extra);
        }
        /*-------/Update Policy Document ------*/
    }

    if (strtotime($eligibility_date) < strtotime($next_purchase_date)) {
        $extra_detail['dependants'] = 0;
        if($new_ws_row['prd_plan_type_id'] > 0) {
            $cd_res = $pdo->select("SELECT id FROM customer_dependent WHERE website_id=:website_id AND is_deleted='N'", array(":website_id" => $new_ws_row['id']));
            if(!empty($cd_res)) {
                $extra_detail['dependants'] = $cd_res;
            }
        }

        if ($sponsor_billing_method == "individual") {
            $billing_sql = "SELECT *, 
                AES_DECRYPT(ach_routing_number,'" . $CREDIT_CARD_ENC_KEY . "')as ach_routing_number, 
                AES_DECRYPT(ach_account_number,'" . $CREDIT_CARD_ENC_KEY . "')as ach_account_number, 
                AES_DECRYPT(card_no_full,'" . $CREDIT_CARD_ENC_KEY . "')as cc_no 
                FROM customer_billing_profile WHERE is_default='Y' AND customer_id=:customer_id";
            $billing_where = array(":customer_id" => $ws_row['customer_id']);
            $billing_row = $pdo->selectOne($billing_sql, $billing_where);

            if (empty($billing_row)) {
                return array("status" => false, "error" => "billing_not_found", "message" => "Billing detail not found");
            }
        }

        $is_billing_process_success = true;
        $affected_ws_ids = array();
        $ws_id = $ws_row['id'];
        $coverage_periods = subscription_coverage_periods_form_date($ws_id, $eligibility_date);
        $is_partial_refund = 'Y';
        foreach ($coverage_periods as $key => $coverage_period) {

            if ($sponsor_billing_method != "individual") {
                /*----- List Bill / TPA Bill -----*/
                {
                    /*------ Entry In List Bill -------*/
                    $tmp_sql = "SELECT lb.id as lb_id,lbd.id as lbd_id,lbd.amount,lb.status 
                        FROM list_bills lb 
                        JOIN list_bill_details lbd ON(lbd.list_bill_id = lb.id)
                        WHERE lb.is_deleted='N' AND lbd.ws_id=:ws_id AND lb.time_period_start_date >= :from_date GROUP BY lb.id";
                    $tmp_where = array(":ws_id"=>$ws_row['id'],":from_date"=>$eligibility_date);

                    $tmp_rows = $pdo->select($tmp_sql,$tmp_where);
                    if(!empty($tmp_rows)) {
                        foreach ($tmp_rows as $key => $tmp_row) {
                            /*--- Refund Old Policy ---*/
                            if($tmp_row['status'] == 'Paid'){
                            $refund_data = array(
                                'customer_id' => $ws_row['customer_id'],
                                'ws_id' => $new_ws_row['id'],
                                'old_ws_id' => $ws_row['id'],
                                'group_id' => $customer_row['sponsor_id'],
                                'transaction_type' => 'refund',
                                'transaction_amount' => $tmp_row['amount'],
                                'payment_received_from' => $tmp_row['lb_id'],
                                'payment_received_details_id' => $tmp_row['lbd_id'],
                                'description' => $description,
                                'ip_address' => !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'],
                                'req_url' => $REQ_URL,
                                'created_at' => 'msqlfunc_NOW()',
                                'updated_at' => 'msqlfunc_NOW()',
                            );
                            $pdo->insert('group_member_refund_charge',$refund_data);

                            }
                            $charge_data = array(
                                'customer_id' => $ws_row['customer_id'],
                                'ws_id' => $new_ws_row['id'],
                                'old_ws_id' => $ws_row['id'],
                                'group_id' => $customer_row['sponsor_id'],
                                'transaction_type' => 'charged',
                                'transaction_amount' => $new_plan_row['price'],
                                'payment_received_from' => $tmp_row['lb_id'],
                                'payment_received_details_id' => $tmp_row['lbd_id'],
                                'description' => $description,
                                'ip_address' => !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'],
                                'req_url' => $REQ_URL,
                                'created_at' => 'msqlfunc_NOW()',
                                'updated_at' => 'msqlfunc_NOW()',
                            );
                            $pdo->insert('group_member_refund_charge',$charge_data);
                            /*--- Charge New Policy ---*/
                        }
                    }
                    /*------/Entry In List Bill -------*/

                    $order_products = array();
                    $order_products[] = array(
                        "website_id" => $new_ws_row['id'],
                        "product_id" => $new_plan_row['product_id'],
                        "plan_id" => $new_plan_row['plan_id'],
                        "fee_applied_for_product" => 0,
                        "prd_plan_type_id" => $new_plan_row['prd_plan_type_id'],
                        "product_type" => $new_plan_row['product_type'],
                        "product_name" => $new_plan_row['product_name'],
                        "unit_price" => $new_plan_row['price'],
                        "product_code" => $new_plan_row['product_code'],
                        "family_member" => (!empty($extra_detail['dependants']) ? count($extra_detail['dependants']) : 0),
                        "qty" => 1,
                        "start_coverage_period" => $coverage_period['start_coverage_period'],
                        "end_coverage_period" => $coverage_period['end_coverage_period'],
                    );
                    $renew_count = $coverage_period['renew_count'];
                    $product_total = $new_plan_price;
                    $grand_total = $product_total;
                    $order_display_id = $functionClass->get_order_id();
                
                    $new_order_data = array(
                        'payment_type' => $sponsor_billing_method,
                        'payment_master_id' => 0,
                        'transaction_id' => 0,
                        'payment_processor' => "",
                        'payment_processor_res' => "",
                        'display_id' => $order_display_id,
                        'customer_id' => $ws_row['customer_id'],
                        'product_total' => $product_total,
                        'sub_total' => $product_total,
                        'grand_total' => $grand_total,
                        'post_date' => date("Y-m-d"),
                        'future_payment' => "N",
                        'status' => "Payment Approved",
                        'type' => ($renew_count == 1 ? ",Customer Enrollment," : ",Renewals,"),
                        'is_renewal' => ($renew_count == 1 ? "N" : "Y"),
                        'ip_address' => !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'],
                        'browser' => $BROWSER,
                        'os' => $OS,
                        'req_url' => $REQ_URL,
                        'order_count' => 1,
                        'updated_at' => 'msqlfunc_NOW()',
                        'created_at' => 'msqlfunc_NOW()',
                        'original_order_date' => 'msqlfunc_NOW()',
                    );
                    $new_order_id = $pdo->insert("group_orders",$new_order_data);
               
                    $ws_ids = array();
                    foreach ($order_products as $key => $od_row) {
                        $od_data = array(
                            "website_id" => $od_row['website_id'],
                            "order_id" => $new_order_id,
                            "product_id" => $od_row['product_id'],
                            "plan_id" => $od_row['plan_id'],
                            "fee_applied_for_product" => $od_row['fee_applied_for_product'],
                            "prd_plan_type_id" => $od_row['prd_plan_type_id'],
                            "product_type" => $od_row['product_type'],
                            "product_name" => $od_row['product_name'],
                            "unit_price" => $od_row['unit_price'],
                            "product_code" => $od_row['product_code'],
                            "family_member" => $od_row['family_member'],
                            "qty" => $od_row['qty'],
                            "start_coverage_period" => $od_row['start_coverage_period'],
                            "end_coverage_period" => $od_row['end_coverage_period'],
                            "renew_count" => $renew_count,
                        );

                        if($is_group_member == 'Y' && $new_plan_row['product_id'] == $od_row['product_id']) {
                            $od_data['member_price'] = $new_plan_row['member_price'];
                            $od_data['group_price'] = $new_plan_row['group_price'];
                            $od_data['contribution_type'] = $new_plan_row['contribution_type'];
                            $od_data['contribution_value'] = $new_plan_row['contribution_value'];
                        }
                        $od_id = $pdo->insert("group_order_details", $od_data);

                        $tmp_ws_row = $pdo->selectOne("SELECT ws.id FROM website_subscriptions ws WHERE ws.product_id=:product_id AND ws.customer_id=:customer_id AND ws.status NOT IN ('Cancel','Inactive')", array(":product_id" => $od_row['product_id'], ":customer_id" => $ws_row['customer_id']));

                        if (!empty($tmp_ws_row)) {
                            $ws_ids[] = $tmp_ws_row['id'];
                            $affected_ws_ids[$tmp_ws_row['id']] = $tmp_ws_row['id'];

                            $ws_history_status = 'Success';
                            $ws_history_message = $description;

                            $ws_history = array(
                                'customer_id' => $ws_row['customer_id'],
                                'website_id' => $tmp_ws_row['id'],
                                'product_id' => $od_row['product_id'],
                                'plan_id' => $od_row['plan_id'],
                                'order_id' => $new_order_id,
                                'status' => $ws_history_status,
                                'message' => $ws_history_message,
                                'attempt' => '',
                                'created_at' => 'msqlfunc_NOW()',
                                'processed_at' => 'msqlfunc_NOW()',
                            );
                            $pdo->insert("website_subscriptions_history", $ws_history);

                            $member_setting = $memberSetting->get_status_by_payment(true);

                            $tmp_ws_data = array(
                                'status' => $member_setting['member_status'],
                                'last_order_id' => $new_order_id,
                                'payment_type' => $sponsor_billing_method,
                                'purchase_date' => 'msqlfunc_NOW()',
                                'last_purchase_date' => 'msqlfunc_NOW()',
                                "start_coverage_period" => $od_row['start_coverage_period'],
                                "end_coverage_period" => $od_row['end_coverage_period'],
                                "fee_applied_for_product" => $od_row['fee_applied_for_product'],
                                'updated_at' => 'msqlfunc_NOW()',

                            );
                            $pdo->update("website_subscriptions", $tmp_ws_data, array("clause" => "id=:id", "params" => array(":id" => $tmp_ws_row['id'])));
                        }
                    }
                    if (!empty($ws_ids)) {
                        $pdo->update("group_orders", array('subscription_ids' => implode(',', $ws_ids)), array("clause" => "id=:id", "params" => array(":id" => $new_order_id)));
                    }
                }
            } else {
                /*----- Individual -----*/
                {
                    $order_products = array();
                    $order_products[] = array(
                        "website_id" => $new_ws_row['id'],
                        "product_id" => $new_plan_row['product_id'],
                        "plan_id" => $new_plan_row['plan_id'],
                        "fee_applied_for_product" => 0,
                        "prd_plan_type_id" => $new_ws_row['prd_plan_type_id'],
                        "product_type" => $new_plan_row['product_type'],
                        "product_name" => $new_plan_row['product_name'],
                        "unit_price" => $new_plan_row['price'],
                        "product_code" => $new_plan_row['product_code'],
                        "family_member" => (!empty($extra_detail['dependants']) ? count($extra_detail['dependants']) : 0),
                        "qty" => 1,
                        "start_coverage_period" => $coverage_period['start_coverage_period'],
                        "end_coverage_period" => $coverage_period['end_coverage_period'],
                    );

                    $renew_count = $coverage_period['renew_count'];
                    $refunded_order_id = 0;
                    $is_post_date_order = false;
                    $order_row = array();
                    $payment_mode = $billing_row["payment_mode"];
                    $payment_approved = false;
                    $txn_id = 0;
                    $service_fee = 0.0;
                    $product_total = $new_plan_price;
                    $shipping_charge = 0.0;
                    $tax_charge = 0.0;
                    $discount = 0.0;
                    $grand_total = $product_total;

                    $tmp_res = subscription_is_paid_for_coverage_period($ws_id, $coverage_period['start_coverage_period']);

                    if (!empty($tmp_res['order_id'])) {
                        $refunded_order_id = $tmp_res['order_id'];

                        if ($tmp_res['is_post_date_order'] == true) {
                            $order_row = $pdo->selectOne("SELECT * FROM orders WHERE id=:id",array(":id" => $refunded_order_id));

                            $product_total = (($order_row['product_total'] + $new_plan_price) - $old_plan_price);
                            $grand_total = (($order_row['grand_total'] + $new_plan_price) - $old_plan_price);

                            $order_detail_res = $pdo->select("SELECT * FROM order_details WHERE order_id=:order_id AND website_id!=:website_id AND is_deleted='N'",array(":order_id" => $refunded_order_id,":website_id" => $ws_id));
                            
                            foreach ($order_detail_res as $order_detail_row) {
                                $order_products[] = $order_detail_row;
                            }

                            //update old order status
                            $is_post_date_order = true;
                            $old_ord_data = array('status' => 'Cancelled', 'updated_at' => 'msqlfunc_NOW()');
                            $old_ord_where = array("clause" => "id=:id", "params" => array(":id" => $refunded_order_id));
                            $pdo->update("orders", $old_ord_data, $old_ord_where);
                            $transParams = array("reason" => "Order Cancelled When Benefit Tier Update");
                            $transactionInsId = $functionClass->transaction_insert($refunded_order_id, 'Debit', 'Cancelled', 'Transaction Cancelled',0,$transParams);

                        } else {
                            cancel_order($refunded_order_id, array("ws_row" => $ws_row, 'description' => $description, 'requested_by' => $requested_by, 'is_partial_refund' => $is_partial_refund));
                        }
                    }                    
                }

                {
                    $order_display_id = $functionClass->get_order_id();
                    $cc_params = array();
                    $cc_params['order_id'] = $order_display_id;
                    $cc_params['amount'] = $grand_total;

                    if ($payment_mode == "ACH") {
                        $payment_master_id = $customer_row['ach_master_id'];
                        $cc_params['ach_account_type'] = $billing_row['ach_account_type'];
                        $cc_params['ach_routing_number'] = $billing_row['ach_routing_number'];
                        $cc_params['ach_account_number'] = $billing_row['ach_account_number'];
                        $cc_params['name_on_account'] = $billing_row['fname'] . ' ' . $billing_row['lname'];
                        $cc_params['bankname'] = $billing_row['bankname'];
                    } else {
                        $payment_master_id = $customer_row['payment_master_id'];
                        $cc_params['ccnumber'] = $billing_row['cc_no'];
                        $cc_params['card_type'] = $billing_row['card_type'];
                        $cc_params['ccexp'] = str_pad($billing_row['expiry_month'], 2, "0", STR_PAD_LEFT) . substr($billing_row['expiry_year'], -2);
                    }

                    $cc_params['description'] = $description;
                    $cc_params['firstname'] = $billing_row['fname'];
                    $cc_params['lastname'] = $billing_row['lname'];
                    $cc_params['address1'] = $billing_row['address'];
                    $cc_params['city'] = $billing_row['city'];
                    $cc_params['state'] = $billing_row['state'];
                    $cc_params['zip'] = $billing_row['zip'];
                    $cc_params['country'] = 'USA';
                    $cc_params['phone'] = !empty($billing_row['phone']) ? $billing_row['phone'] : $customer_row['cell_phone'];
                    $cc_params['email'] = $customer_row['email'];
                    
                    $payment_master_id = $functionClass->get_agent_merchant_detail(array($new_plan_row['plan_id']),$customer_row['sponsor_id'],$payment_mode,array('is_renewal' => ($renew_count == 1 ? "N" : "Y"),'customer_id' => $ws_row['customer_id']));
                    $payment_processor = "";
                    if(!empty($payment_master_id)){
                        $payment_processor = getname('payment_master',$payment_master_id,'processor_id');
                    }
                    $cc_params['processor'] = $payment_processor;

                    if ($is_post_date_order == true) {
                        $payment_approved = true;
                        $txn_id = 0;
                        $payment_res = array('status' => 'Success', 'transaction_id' => 0, 'message' => "Post Payment Order");
                    } else {
                        if ($grand_total == 0) {
                            $payment_res = array('status' => 'Success', 'transaction_id' => 0, 'message' => "Bypass payment API due to order have zero amount.");
                        } else {
                            if ($payment_mode == "ACH") {
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
                        }
                    }

                    if ($payment_res['status'] == 'Success') {
                        $payment_approved = true;
                        $txn_id = $payment_res['transaction_id'];
                    } else {
                        $is_billing_process_success = false;
                        $payment_approved = false;
                        $cc_params['order_type'] = $description;
                        $cc_params['browser'] = $BROWSER;
                        $cc_params['os'] = $OS;
                        $cc_params['req_url'] = $REQ_URL;
                        $cc_params['err_text'] = $payment_res['message'];
                        $payment_error = $payment_res['message'];
                        $decline_log_id = $functionClass->credit_card_decline_log($ws_row['customer_id'], $cc_params, $payment_res);
                    }
                }

                $member_setting = $memberSetting->get_status_by_payment($payment_approved,"",$is_post_date_order);

                {
                    $new_order_data = array(
                        'payment_type' => $payment_mode,
                        'payment_master_id' => $payment_master_id,
                        'transaction_id' => $txn_id,
                        'payment_processor' => $payment_processor,
                        'payment_processor_res' => json_encode($payment_res),
                        'display_id' => $order_display_id,
                        'customer_id' => $ws_row['customer_id'],
                        'product_total' => $product_total,
                        'sub_total' => $product_total,
                        'grand_total' => $grand_total,
                        'post_date' => date("Y-m-d"),
                        'future_payment' => "N",
                        'status' => ($payment_approved == true ? "Payment Approved" : "Payment Declined"),
                        'type' => ($renew_count == 1 ? ",Customer Enrollment," : ",Renewals,"),
                        'is_renewal' => ($renew_count == 1 ? "N" : "Y"),
                        'ip_address' => !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'],
                        'browser' => $BROWSER,
                        'os' => $OS,
                        'req_url' => $REQ_URL,
                        'order_count' => 1,
                        'updated_at' => 'msqlfunc_NOW()',
                        'created_at' => 'msqlfunc_NOW()',
                        'original_order_date' => 'msqlfunc_NOW()',
                    );

                    if (!empty($is_post_date_order)) {
                        $new_order_data['post_date'] = $order_row['post_date'];
                        $new_order_data['future_payment'] = 'Y';
                        $new_order_data['status'] = 'Post Payment';
                    }
                    if (isset($payment_res['review_require']) && $payment_res['review_require'] == 'Y') {
                        $new_order_data['review_require'] = 'Y';
                    }
                    $new_order_id = $pdo->insert("orders", $new_order_data);

                    $order_billing_info = array(
                        'order_id' => $new_order_id,
                        'customer_id' => $billing_row['customer_id'],
                        'email' => $customer_row['email'],
                        'phone' => !empty($billing_row['phone']) ? $billing_row['phone'] : $customer_row['cell_phone'],
                        'fname' => $billing_row['fname'],
                        'lname' => $billing_row['lname'],
                        'country' => 'United States',
                        'country_id' => 231,
                        'state' => $billing_row['state'],
                        'city' => $billing_row['city'],
                        'zip' => $billing_row['zip'],
                        'address' => $billing_row['address'],
                        'address2' => $billing_row['address2'],
                        'updated_at' => 'msqlfunc_NOW()',
                        'created_at' => 'msqlfunc_NOW()',
                    );

                    if ($payment_mode == "ACH") {
                        $order_billing_info = array_merge($order_billing_info, array(
                                'ach_account_number' => "msqlfunc_AES_ENCRYPT('" . $billing_row['ach_account_number'] . "','" . $CREDIT_CARD_ENC_KEY . "')",
                                'ach_routing_number' => "msqlfunc_AES_ENCRYPT('" . $billing_row['ach_routing_number'] . "','" . $CREDIT_CARD_ENC_KEY . "')",
                                'ach_account_type' => $billing_row['ach_account_type'],
                                'bankname' => $billing_row['bankname'],
                                'payment_mode' => 'ACH',
                                'last_cc_ach_no' => substr($billing_row['ach_account_number'], -4),
                            )
                        );
                    } else {
                        $order_billing_info = array_merge($order_billing_info, array(
                                'card_no' => substr($billing_row['cc_no'], -4),
                                'card_no_full' => "msqlfunc_AES_ENCRYPT('" . $billing_row['cc_no'] . "','" . $CREDIT_CARD_ENC_KEY . "')",
                                'card_type' => $billing_row['card_type'],
                                'expiry_month' => $billing_row['expiry_month'],
                                'expiry_year' => $billing_row['expiry_year'],
                                'payment_mode' => 'CC',
                                'cvv_no' => $billing_row['cvv_no'],
                                'last_cc_ach_no' => substr($billing_row['cc_no'], -4),
                            )
                        );
                    }
                    $pdo->insert('order_billing_info', $order_billing_info);
                }

                {
                    $ws_ids = array();
                    foreach ($order_products as $key => $od_row) {
                        $od_data = array(
                            "website_id" => $od_row['website_id'],
                            "order_id" => $new_order_id,
                            "product_id" => $od_row['product_id'],
                            "plan_id" => $od_row['plan_id'],
                            "fee_applied_for_product" => $od_row['fee_applied_for_product'],
                            "prd_plan_type_id" => $od_row['prd_plan_type_id'],
                            "product_type" => $od_row['product_type'],
                            "product_name" => $od_row['product_name'],
                            "unit_price" => $od_row['unit_price'],
                            "product_code" => $od_row['product_code'],
                            "family_member" => $od_row['family_member'],
                            "qty" => $od_row['qty'],
                            "start_coverage_period" => $od_row['start_coverage_period'],
                            "end_coverage_period" => $od_row['end_coverage_period'],
                            "renew_count" => $renew_count,
                        );
                        if($is_group_member == 'Y' && $new_plan_row['product_id'] == $od_row['product_id']) {
                            $od_data['member_price'] = $new_plan_row['member_price'];
                            $od_data['group_price'] = $new_plan_row['group_price'];
                            $od_data['contribution_type'] = $new_plan_row['contribution_type'];
                            $od_data['contribution_value'] = $new_plan_row['contribution_value'];
                        }
                        $od_id = $pdo->insert("order_details", $od_data);
                        
                        $tmp_ws_row = $pdo->selectOne("SELECT ws.id FROM website_subscriptions ws WHERE ws.id=:website_id AND ws.customer_id=:customer_id", array(":website_id" => $od_row['website_id'], ":customer_id" => $ws_row['customer_id']));

                        if (!empty($tmp_ws_row)) {
                            $ws_ids[] = $tmp_ws_row['id'];
                            $affected_ws_ids[$tmp_ws_row['id']] = $tmp_ws_row['id'];

                            if ($is_post_date_order == false) {
                                if ($payment_approved == true) {
                                    $ws_history_status = 'Success';
                                    $ws_history_message = $description;
                                } else {
                                    $ws_history_status = 'Fail';
                                    $ws_history_message = ($description . '. Error: ' . $payment_error);
                                }
                            } else {
                                $ws_history_status = 'Success';
                                $ws_history_message = ('Post Payment: ' . $description);
                            }

                            $ws_history = array(
                                'customer_id' => $ws_row['customer_id'],
                                'website_id' => $tmp_ws_row['id'],
                                'product_id' => $od_row['product_id'],
                                'plan_id' => $od_row['plan_id'],
                                'order_id' => $new_order_id,
                                'status' => $ws_history_status,
                                'message' => $ws_history_message,
                                'attempt' => '',
                                'created_at' => 'msqlfunc_NOW()',
                                'processed_at' => 'msqlfunc_NOW()',
                            );
                            $pdo->insert("website_subscriptions_history", $ws_history);

                            if ($is_post_date_order == false) {
                                $tmp_ws_data = array(
                                    'status' => $member_setting['member_status'],
                                    'last_order_id' => $new_order_id,
                                    'payment_type' => ($payment_mode == "ACH"?"ACH":"CC"),
                                    'purchase_date' => 'msqlfunc_NOW()',
                                    'last_purchase_date' => 'msqlfunc_NOW()',
                                    "start_coverage_period" => $od_row['start_coverage_period'],
                                    "end_coverage_period" => $od_row['end_coverage_period'],
                                    "fee_applied_for_product" => $od_row['fee_applied_for_product'],
                                    'updated_at' => 'msqlfunc_NOW()',
                                );
                            } else {
                                $tmp_ws_data = array(
                                    'last_order_id' => $new_order_id,
                                    'updated_at' => 'msqlfunc_NOW()',
                                );
                            }
                            $pdo->update("website_subscriptions", $tmp_ws_data, array("clause" => "id=:id", "params" => array(":id" => $tmp_ws_row['id'])));
                        }
                        
                    }
                    if ($is_post_date_order == false) {
                        $txn_id = $payment_res['transaction_id'];
                        if ($payment_approved == true) {
                            if ($payment_mode != "ACH") {
                                $transactionInsId = $functionClass->transaction_insert($new_order_id, 'Credit', ($renew_count == 1 ? "New Order" : "Renewal Order"), 'Transaction Approved', '', array("transaction_id" => $txn_id, 'transaction_response' => $payment_res));
                            } else {
                                $transactionInsId = $functionClass->transaction_insert($new_order_id, 'Credit', 'Pending', 'Settlement Transaction', '', array("transaction_id" => $txn_id, 'transaction_response' => $payment_res));
                            }
                        } else {
                            $transactionInsId = $functionClass->transaction_insert($new_order_id, 'Failed', 'Payment Declined', 'Transaction Declined', '', array("transaction_id" => $txn_id, 'transaction_response' => $payment_res,"reason" => checkIsset($payment_error),'cc_decline_log_id'=>checkIsset($decline_log_id)));
                        }
                    } else {
                        // Insert in transactions for post date order
                        $transactionInsId = $functionClass->transaction_insert($new_order_id, 'Credit', 'Pending', 'Post Transaction');
                    }

                    if (!empty($ws_ids)) {
                        $pdo->update("orders", array('subscription_ids' => implode(',', $ws_ids)), array("clause" => "id=:id", "params" => array(":id" => $new_order_id)));
                    }
                }
                /*----- Order -----*/
                //********* Payable Insert Code Start ********************
                if ($payment_approved == true && $is_post_date_order == false) {
                    if ($payment_mode != "ACH") {

                        $payable_params = array(
                            'payable_type' => 'Vendor',
                            'type' => 'Vendor',
                            'transaction_tbl_id' => $transactionInsId['id'],
                        );
                        $payable = $functionClass->payable_insert($new_order_id, 0, 0, 0, $payable_params);
                    }
                }
                //********* Payable Insert Code End   ********************
                //Charge mew order here
            }
        } //Foreach loop closed
        $member_setting = $memberSetting->get_status_by_payment($is_billing_process_success);
        //Set On Hold Billing Failed to customer, ce & ws  
        if ($is_billing_process_success == false) {
            $cust_update_data = array('status' => $member_setting['member_status'], 'updated_at' => 'msqlfunc_NOW()');
            $cust_update_where = array("clause" => 'id=:id', 'params' => array(":id" => $ws_row['customer_id']));
            $pdo->update("customer", $cust_update_data, $cust_update_where);

            if (!empty($affected_ws_ids)) {
                foreach ($affected_ws_ids as $key => $affected_ws_id) {
                    $affected_ws_data = array('status' => $member_setting['policy_status'], 'updated_at' => 'msqlfunc_NOW()');
                    $affected_ws_where = array("clause" => 'id=:id', 'params' => array(":id" => $affected_ws_id));
                    $pdo->update("website_subscriptions", $affected_ws_data, $affected_ws_where);
                }
            }
        }
    }
    return array("status" => true, "message" => "Successfully saved future coverage update");
}

function refund_new_plan_order_when_cancel_update($old_ws_id, $new_ws_id, $extra_detail = array())
{
    global $pdo, $CREDIT_CARD_ENC_KEY, $SITE_ENV;
    return true;

    $enr_prd_ids = get_enrollment_fee_prd_ids();
    $BROWSER = getBrowser();
    $OS = getOS($_SERVER['HTTP_USER_AGENT']);
    $REQ_URL = ($_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
    $memberSetting = new memberSetting();
    $functionClass = new functionsList();
    $decline_log_id="";

    $REAL_IP_ADDRESS = get_real_ipaddress();
    $ws_sql = "SELECT ws.* FROM website_subscriptions ws WHERE ws.id=:id";
    $ws_row = $pdo->selectOne($ws_sql, array(":id" => $new_ws_id));

    $new_ws_sql = "SELECT ws.* FROM website_subscriptions ws WHERE ws.id=:id";
    $new_ws_row = $pdo->selectOne($new_ws_sql, array(":id" => $old_ws_id));

    if (empty($ws_row) || empty($new_ws_row)) {
        return array("status" => false, "error" => "subscription_not_found", "message" => "Subscription not found");
    }

    $eligibility_date = $ws_row['eligibility_date'];

    $is_list_bill_enroll = "N";
    $is_group_member = is_group_member($ws_row['customer_id']);

    //New Plan Row
    $new_plan_sql = "SELECT pm.id,pm.id as plan_id,pm.product_id,pm.plan_type,pm.price,p.product_code,p.name as product_name,p.type as product_type,ppt.title as plan_type_title FROM prd_matrix pm
                    JOIN prd_main p ON p.id=pm.product_id
                    JOIN prd_plan_type ppt ON ppt.id=pm.plan_type
                    WHERE pm.id=:plan_id";
    $new_plan_row = $pdo->selectOne($new_plan_sql, array(":plan_id" => $new_ws_row['plan_id']));
    $new_plan_price = $new_plan_row['price'];


    //Old Plan Row
    $old_plan_sql = "SELECT pm.id,pm.id as plan_id,pm.product_id,pm.plan_type,pm.price,p.product_code,p.name as product_name,p.type as product_type,ppt.title as plan_type_title FROM            prd_matrix pm
                    JOIN prd_main p ON p.id=pm.product_id
                    JOIN prd_plan_type ppt ON ppt.id=pm.plan_type
                    WHERE pm.id=:plan_id";
    $old_plan_row = $pdo->selectOne($old_plan_sql, array(":plan_id" => $ws_row['plan_id']));
    $old_plan_price = $old_plan_row['price'];

    $is_upgrade_downgrade = false;

    $upgrade_downgrade = 'Benefit tier change';
    if (isset($extra_detail['is_upgrade_downgrade'])) {
        $is_upgrade_downgrade = true;
        $upgrade_downgrade = $extra_detail['upgrade_downgrade'];
    }

    if ($is_upgrade_downgrade == true) {
        $description = ("Cancelled Product " . $upgrade_downgrade . "d from " . $old_plan_row['product_name'] . " to ." . $new_plan_row['product_name']);
    } else {
        $description = ("Cancelled plan changed from " . $old_plan_row['plan_type_title'] . " to " . $new_plan_row['plan_type_title']);
    }

    {
        $customer_sql = "SELECT c.email,c.cell_phone,c.id,c.sponsor_id,
                        IFNULL(sp.payment_master_id,0) AS payment_master_id,
                        IFNULL(sp.ach_master_id,0) AS ach_master_id
                        FROM customer c
                        LEFT JOIN customer sp ON sp.id=c.sponsor_id
                        WHERE c.id=:customer_id";
        $customer_row = $pdo->selectOne($customer_sql, array(":customer_id" => $ws_row['customer_id']));

        if ($is_group_member == true) {
            $group_cb_row = $pdo->selectOne("SELECT * FROM customer_billing_profile where customer_id=:customer_id", array(":customer_id" => $customer_row['sponsor_id']));
            if (!empty($group_cb_row['listbill_enroll'])) {
                $is_list_bill_enroll = $group_cb_row["listbill_enroll"];
            }
        }

        if ($is_list_bill_enroll == "N") {
            $billing_sql = "SELECT *, 
                AES_DECRYPT(ach_routing_number,'" . $CREDIT_CARD_ENC_KEY . "')as ach_routing_number, 
                AES_DECRYPT(ach_account_number,'" . $CREDIT_CARD_ENC_KEY . "')as ach_account_number, 
                AES_DECRYPT(card_no_full,'" . $CREDIT_CARD_ENC_KEY . "')as cc_no 
                FROM customer_billing_profile WHERE is_default='Y' AND customer_id=:customer_id";
            $billing_where = array(":customer_id" => $ws_row['customer_id']);
            $billing_row = $pdo->selectOne($billing_sql, $billing_where);

            if (empty($billing_row)) {
                return array("status" => false, "error" => "billing_not_found", "message" => "Billing detail not found");
            }
        }

        $is_billing_process_success = true;
        $affected_ws_ids = array();
        $ws_id = $ws_row['id'];
        $coverage_periods = subscription_coverage_periods_form_date($ws_id, $eligibility_date);
        $is_partial_refund = 'Y';
        foreach ($coverage_periods as $key => $coverage_period) {
            if ($is_list_bill_enroll == "Y") {
                $tmp_sql = "SELECT * FROM group_member_refund_charge WHERE ws_id=:ws_id AND old_ws_id=:old_ws_id";
                $tmp_where = array(":ws_id" => $new_ws_id, ":old_ws_id" => $old_ws_id);
                $tmp_rows = $pdo->select($tmp_sql, $tmp_where);
                if (!empty($tmp_rows)) {
                    foreach ($tmp_rows as $key => $tmp_row) {
                        if ($tmp_row['is_applied_to_list_bill'] == "N") {
                            $pdo->delete("DELETE FROM group_member_refund_charge WHERE id=:id", array(":id" => $tmp_row['id']));
                        } else {
                            $refund_charge_data = array(
                                'customer_id' => $tmp_row['customer_id'],
                                'ws_id' => $old_ws_id,
                                'old_ws_id' => $new_ws_id,
                                'group_id' => $tmp_row['group_id'],
                                'transaction_type' => ($tmp_row['transaction_type'] == "charged" ? "refund" : "charged"),
                                'transaction_amount' => $tmp_row['transaction_amount'],
                                'payment_received_from' => $tmp_row['payment_received_from'],
                                'description' => $description,
                                'ip_address' => !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'],
                                'req_url' => $REQ_URL,
                                'created_at' => 'msqlfunc_NOW()',
                                'updated_at' => 'msqlfunc_NOW()',
                            );
                            $pdo->insert('group_member_refund_charge', $refund_charge_data);
                        }
                    }
                }
            } else {
                {
                    $order_products = array();
                    $order_products[] = array(
                        "product_id" => $new_plan_row['product_id'],
                        "plan_id" => $new_plan_row['plan_id'],
                        "product_type" => $new_plan_row['product_type'],
                        "product_name" => $new_plan_row['product_name'],
                        "unit_price" => $new_plan_row['price'],
                        "product_code" => $new_plan_row['product_code'],
                        "plan_type" => $new_plan_row['plan_type'],
                        "qty" => 1,
                        "start_coverage_period" => $coverage_period['start_coverage_period'],
                        "end_coverage_period" => $coverage_period['end_coverage_period'],
                    );

                    $renew_count = $coverage_period['renew_count'];
                    $refunded_order_id = 0;
                    $is_post_date_order = false;
                    $order_row = array();
                    $payment_mode = $billing_row["payment_mode"];
                    $payment_approved = false;
                    $txn_id = 0;
                    $service_fee = 0.0;
                    $product_total = $new_plan_price;
                    $shipping_charge = 0.0;
                    $tax_charge = 0.0;
                    $discount = 0.0;
                    $tmp_res = subscription_is_paid_for_coverage_period($ws_id, $coverage_period['start_coverage_period']);

                    if (!empty($tmp_res['order_id'])) {
                        $refunded_order_id = $tmp_res['order_id'];

                        $product_total = $new_plan_price;
                        $shipping_charge = 0;
                        $tax_charge = 0;
                        $discount = 0;


                        if ($tmp_res['is_post_date_order'] == true) {
                            //update old order status
                            $is_post_date_order = true;
                            $old_ord_data = array('status' => 'Cancelled', 'updated_at' => 'msqlfunc_NOW()');
                            $old_ord_where = array("clause" => "id=:id", "params" => array(":id" => $refunded_order_id));
                            $pdo->update("orders", $old_ord_data, $old_ord_where);
                            $transParams = array("reason" => "Order Cancelled When Benefit Tier Update");
                            $transactionInsId = $functionClass->transaction_insert($refunded_order_id, 'Debit', 'Cancelled', 'Transaction Cancelled',0,$transParams);

                        } else {
                            cancel_order($refunded_order_id, array("ws_row" => $ws_row, 'description' => $description, 'requested_by' => $requested_by, 'is_partial_refund' => $is_partial_refund));
                        }
                    } else {
                        continue;
                    }

                    foreach ($order_products as $key => $od_row) {

                        /*------- Replace old plan with  new plan detail ------------*/
                        if ($od_row['product_id'] == $old_plan_row['product_id']) {
                            $order_products[$key] = array(
                                "product_id" => $new_plan_row['product_id'],
                                "plan_id" => $new_plan_row['plan_id'],
                                "product_type" => $new_plan_row['product_type'],
                                "product_name" => $new_plan_row['product_name'],
                                "unit_price" => $new_plan_row['price'],
                                "product_code" => $new_plan_row['product_code'],
                                "family_member" => (!empty($extra_detail['dependants']) ? count($extra_detail['dependants']) : 0),
                                "qty" => 1,
                                "start_coverage_period" => $od_row['start_coverage_period'],
                                "end_coverage_period" => $od_row['end_coverage_period'],
                            );
                        }  
                    }

                    $grand_total = ($product_total + $shipping_charge + $tax_charge) - $discount;
                }

                {
                    $order_display_id = $functionClass->get_order_id();
                    $cc_params = array();
                    $cc_params['order_id'] = $order_display_id;
                    $cc_params['amount'] = $grand_total;

                    if ($payment_mode == "ACH") {
                        $payment_master_id = $customer_row['ach_master_id'];
                        $cc_params['ach_account_type'] = $billing_row['ach_account_type'];
                        $cc_params['ach_routing_number'] = $billing_row['ach_routing_number'];
                        $cc_params['ach_account_number'] = $billing_row['ach_account_number'];
                        $cc_params['name_on_account'] = $billing_row['fname'] . ' ' . $billing_row['lname'];
                        $cc_params['bankname'] = $billing_row['bankname'];
                    } else {
                        $payment_master_id = $customer_row['payment_master_id'];
                        $cc_params['ccnumber'] = $billing_row['cc_no'];
                        $cc_params['card_type'] = $billing_row['card_type'];
                        $cc_params['ccexp'] = str_pad($billing_row['expiry_month'], 2, "0", STR_PAD_LEFT) . substr($billing_row['expiry_year'], -2);
                    }

                    $cc_params['description'] = $description;
                    $cc_params['firstname'] = $billing_row['fname'];
                    $cc_params['lastname'] = $billing_row['lname'];
                    $cc_params['address1'] = $billing_row['address'];
                    $cc_params['city'] = $billing_row['city'];
                    $cc_params['state'] = $billing_row['state'];
                    $cc_params['zip'] = $billing_row['zip'];
                    $cc_params['country'] = 'USA';
                    $cc_params['phone'] = !empty($billing_row['phone']) ? $billing_row['phone'] : $customer_row['cell_phone'];
                    $cc_params['email'] = $customer_row['email'];

                    $payment_master_id = $functionClass->get_agent_merchant_detail(array($new_plan_row['plan_id']),$customer_row['sponsor_id'],$payment_mode,array('is_renewal' => ($renew_count == 1 ? "N" : "Y"),'customer_id' => $ws_row['customer_id']));
                    $payment_processor = "";
                    if(!empty($payment_master_id)){
                        $payment_processor = getname('payment_master',$payment_master_id,'processor_id');
                    }
                    $cc_params['processor'] = $payment_processor;

                    if ($is_post_date_order == true) {
                        $payment_approved = true;
                        $txn_id = 0;
                    } else {
                        if ($grand_total == 0) {
                            $payment_res = array('status' => 'Success', 'transaction_id' => 0, 'message' => "Bypass payment API due to order have zero amount.");
                        } else {
                            if ($payment_mode == "ACH") {
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
                        }
                    }

                    if ($payment_res['status'] == 'Success') {
                        $payment_approved = true;
                        $txn_id = $payment_res['transaction_id'];
                    } else {
                        $is_billing_process_success = false;
                        $payment_approved = false;
                        $cc_params['order_type'] = $description;
                        $cc_params['browser'] = $BROWSER;
                        $cc_params['os'] = $OS;
                        $cc_params['req_url'] = $REQ_URL;
                        $cc_params['err_text'] = $payment_res['message'];
                        $payment_error = $payment_res['message'];
                        $decline_log_id = $functionClass->credit_card_decline_log($ws_row['customer_id'], $cc_params, $payment_res);
                    }
                }

                {
                    $new_order_data = array(
                        'transaction_id' => $txn_id,
                        'payment_type' => $payment_mode,
                        'payment_master_id' => $payment_master_id,
                        'payment_processor' => $payment_processor,
                        'payment_processor_res' => json_encode($payment_res),
                    );

                    $order_billing_info = array(
                        'customer_id' => $billing_row['customer_id'],
                        'email' => $customer_row['email'],
                        'phone' => !empty($billing_row['phone']) ? $billing_row['phone'] : $customer_row['cell_phone'],
                        'fname' => $billing_row['fname'],
                        'lname' => $billing_row['lname'],
                        'country' => 'United States',
                        'country_id' => 231,
                        'state' => $billing_row['state'],
                        'city' => $billing_row['city'],
                        'zip' => $billing_row['zip'],
                        'address' => $billing_row['address'],
                        'address2' => $billing_row['address2'],
                        'updated_at' => 'msqlfunc_NOW()',
                    );

                    if ($payment_mode == "ACH") {
                        $order_billing_info = array_merge($order_billing_info, array(
                                'ach_account_number' => "msqlfunc_AES_ENCRYPT('" . $billing_row['ach_account_number'] . "','" . $CREDIT_CARD_ENC_KEY . "')",
                                'ach_routing_number' => "msqlfunc_AES_ENCRYPT('" . $billing_row['ach_routing_number'] . "','" . $CREDIT_CARD_ENC_KEY . "')",
                                'ach_account_type' => $billing_row['ach_account_type'],
                                'bankname' => $billing_row['bankname'],
                                'payment_mode' => 'ACH',
                            )
                        );
                    } else {
                        $order_billing_info = array_merge($order_billing_info, array(
                                'card_no' => substr($billing_row['cc_no'], -4),
                                'card_no_full' => "msqlfunc_AES_ENCRYPT('" . $billing_row['cc_no'] . "','" . $CREDIT_CARD_ENC_KEY . "')",
                                'card_type' => $billing_row['card_type'],
                                'expiry_month' => $billing_row['expiry_month'],
                                'expiry_year' => $billing_row['expiry_year'],
                                'payment_mode' => 'CC',
                            )
                        );
                    }

                    $pdo->insert('order_billing_info', $order_billing_info);

                    if (empty($billing_row['id'])) {
                        $cb_profile = $order_billing_info;
                        $cb_profile['is_default'] = "Y";
                        $cb_profile['created_at'] = "msqlfunc_NOW()";
                        $pdo->insert('customer_billing_profile', $cb_profile);
                    }

                    $new_order_data = array_merge($new_order_data, array(
                        'display_id' => $order_display_id,
                        'customer_id' => $ws_row['customer_id'],
                        'product_total' => $product_total,
                        'sub_total' => ($product_total + $shipping_charge + $tax_charge + $service_fee),
                        'grand_total' => $grand_total,
                        'post_date' => date("Y-m-d"),
                        'future_payment' => "N",
                        'status' => ($payment_approved == true ? "Payment Approved" : "Payment Declined"),
                        'type' => ($renew_count == 1 ? ",Customer Enrollment," : ",Renewals,"),
                        'is_renewal' => ($renew_count == 1 ? "N" : "Y"),
                        'ip_address' => !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'],
                        'browser' => $BROWSER,
                        'os' => $OS,
                        'req_url' => $REQ_URL,
                        'updated_at' => 'msqlfunc_NOW()',
                        'created_at' => 'msqlfunc_NOW()',
                    ));
                    if (!empty($is_post_date_order)) {
                        $new_order_data['post_date'] = $order_row['post_date'];
                        $new_order_data['future_payment'] = $order_row['future_payment'];
                        $new_order_data['status'] = $order_row['status'];
                    }
                    $new_order_id = $pdo->insert("orders", $new_order_data);
                }

                {
                    if ($is_post_date_order == false) {
                        $txn_id = $payment_res['transaction_id'];
                        if ($payment_approved == true) {
                            if ($payment_mode != "ACH") {
                                $transactionInsId = $functionClass->transaction_insert($new_order_id, 'Credit', ($renew_count == 1 ? "New Order" : "Renewal Order"), 'Transaction Approved', '', array("transaction_id" => $txn_id, 'transaction_response' => $payment_res));
                            } else {
                                $transactionInsId = $functionClass->transaction_insert($new_order_id, 'Credit', 'Pending', 'Settlement Transaction', '', array("transaction_id" => $txn_id, 'transaction_response' => $payment_res));
                            }


                            $af_data = array(
                                'order_id' => $new_order_id,
                                'order_display_id' => $new_order_data['display_id'],
                                'customer_id' => $ws_row['customer_id'],
                                'description' => $description,
                                'billing_detail' => ($payment_mode == "CC" ? $billing_row['card_type'] . " *" . substr($billing_row['cc_no'], -4) : "ACH *" . substr($billing_row['ach_routing_number'], -4)),
                            );
                            if ($requested_by == 'admin') {
                                $af_data = array_merge($af_data, array(
                                    'admin_id' => $_SESSION['admin']['id'],
                                    'admin_name' => $_SESSION['admin']['name']
                                ));
                            } elseif ($requested_by == 'agent') {
                                $af_data = array_merge($af_data, array(
                                    'agent_id' => $_SESSION['agents']['id'],
                                    'agent_name' => $_SESSION['agents']['fname'] . ' ' . $_SESSION['agents']['lname']
                                ));
                            } elseif ($requested_by == 'group') {
                                $af_data = array_merge($af_data, array(
                                    'group_id' => $_SESSION['groups']['id'],
                                    'group_name' => $_SESSION['groups']['fname'] . ' ' . $_SESSION['groups']['lname']
                                ));
                            }
                            activity_feed(3, $ws_row['customer_id'], "Customer", $new_order_id, 'orders', 'Successful Payment', $billing_row['fname'], $billing_row['lname'], json_encode($af_data));
                        } else {
                            $transactionInsId = $functionClass->transaction_insert($new_order_id, 'Failed', 'Payment Declined', 'Transaction Declined', '', array("transaction_id" => $txn_id, 'transaction_response' => $payment_res,"reason" => checkIsset($payment_error),'cc_decline_log_id'=>checkIsset($decline_log_id)));

                            $af_data = array(
                                'order_id' => $new_order_id,
                                'order_display_id' => $new_order_data['display_id'],
                                'order_billing_id' => $new_order_data['billing_id'],
                                'reason' => $payment_error ? $payment_error : 'Error in processing payment',
                                'description' => $description,
                                'billing_info' => ($payment_mode == "CC" ? $billing_row['card_type'] . " *" . substr($billing_row['cc_no'], -4) : "ACH *" . substr($billing_row['ach_routing_number'], -4)),
                            );
                            if ($requested_by == 'admin') {
                                $af_data = array_merge($af_data, array(
                                    'admin_id' => $_SESSION['admin']['id'],
                                    'admin_name' => $_SESSION['admin']['name']
                                ));
                            } elseif ($requested_by == 'agent') {
                                $af_data = array_merge($af_data, array(
                                    'agent_id' => $_SESSION['agents']['id'],
                                    'agent_name' => $_SESSION['agents']['fname'] . ' ' . $_SESSION['agents']['lname']
                                ));
                            } elseif ($requested_by == 'group') {
                                $af_data = array_merge($af_data, array(
                                    'group_id' => $_SESSION['groups']['id'],
                                    'group_name' => $_SESSION['groups']['fname'] . ' ' . $_SESSION['groups']['lname']
                                ));
                            }
                            activity_feed(3, $ws_row['customer_id'], "Customer", $new_order_id, 'orders', 'Billing Failed', $billing_row['fname'], $billing_row['lname'], json_encode($af_data));
                        }
                    } else {
                        // Insert in transactions for post date order
                        $transactionInsId = $functionClass->transaction_insert($new_order_id, 'Credit', 'Pending', 'Post Transaction');
                    }
                }

                {
                    $ws_ids = array();
                    foreach ($order_products as $key => $od_row) {

                        $od_data = array(
                            "order_id" => $new_order_id,
                            "product_id" => $od_row['product_id'],
                            "plan_id" => $od_row['plan_id'],
                            "product_type" => $od_row['product_type'],
                            "product_name" => $od_row['product_name'],
                            "unit_price" => $od_row['unit_price'],
                            "product_code" => $od_row['product_code'],
                            "family_member" => $od_row['family_member'],
                            "qty" => $od_row['qty'],
                            "start_coverage_period" => $od_row['start_coverage_period'],
                            "end_coverage_period" => $od_row['end_coverage_period'],
                        );
                        $od_id = $pdo->insert("order_details", $od_data);

                        if (!in_array($od_row['product_id'], $enr_prd_ids)) {
                            $tmp_ws_row = $pdo->selectOne("SELECT ws.id FROM website_subscriptions ws WHERE ws.plan_id=:plan_id AND ws.customer_id=:customer_id AND ws.status NOT IN ('Cancel','Inactive')", array(":plan_id" => $od_row['plan_id'], ":customer_id" => $ws_row['customer_id']));

                            if (!empty($tmp_ws_row)) {
                                $ws_ids[] = $tmp_ws_row['id'];
                                $affected_ws_ids[$tmp_ws_row['id']] = $tmp_ws_row['id'];

                                if ($is_post_date_order == false) {
                                    if ($payment_approved == true) {
                                        $ws_history_status = 'Success';
                                        $ws_history_message = $description;
                                    } else {
                                        $ws_history_status = 'Fail';
                                        $ws_history_message = ($description . '. Error: ' . $payment_error);
                                    }
                                } else {
                                    $ws_history_status = 'Success';
                                    $ws_history_message = ('Post Payment: ' . $description);
                                }

                                $ws_history = array(
                                    'customer_id' => $ws_row['customer_id'],
                                    'website_id' => $tmp_ws_row['id'],
                                    'product_id' => $od_row['product_id'],
                                    'plan_id' => $od_row['plan_id'],
                                    'order_id' => $new_order_id,
                                    'status' => $ws_history_status,
                                    'message' => $ws_history_message,
                                    'attempt' => '',
                                    'created_at' => 'msqlfunc_NOW()',
                                    'processed_at' => 'msqlfunc_NOW()',
                                );
                                $pdo->insert("website_subscriptions_history", $ws_history);

                                $tmp_ws_data = array(
                                    'status' => "Active",
                                    'last_order_id' => $new_order_id,
                                    'purchase_date' => 'msqlfunc_NOW()',
                                    'last_purchase_date' => 'msqlfunc_NOW()',
                                    'next_purchase_date' => $ws_row['next_purchase_date'],
                                    'updated_at' => 'msqlfunc_NOW()',
                                );
                                $pdo->update("website_subscriptions", $tmp_ws_data, array("clause" => "id=:id", "params" => array(":id" => $tmp_ws_row['id'])));
                            }
                        }
                    }

                    if (!empty($ws_ids)) {
                        $pdo->update("orders", array('subscription_ids' => implode(',', $ws_ids)), array("clause" => "id=:id", "params" => array(":id" => $new_order_id)));
                    }
                }
                /*----- Order -----*/
                //********* Payable Insert Code Start ********************
                if ($payment_approved == true && $is_post_date_order == false) {
                    if ($payment_mode != "ACH") {

                        $payable_params = array(
                            'payable_type' => 'Vendor',
                            'type' => 'Vendor',
                            'transaction_tbl_id' => $transactionInsId['id'],
                        );
                        $payable = $functionClass->payable_insert($new_order_id, 0, 0, 0, $payable_params);


                    }
                }
                //********* Payable Insert Code End   ********************
                //Charge mew order here
            }
        }//Foreach loop closed

        //Set On Hold Billing Failed to customer, ce & ws 
        $member_setting = $memberSetting->get_status_by_payment($is_billing_process_success); 
        if ($is_billing_process_success == false) {
            $cust_update_data = array('status' => $member_setting['member_status'], 'updated_at' => 'msqlfunc_NOW()');
            $cust_update_where = array("clause" => 'id=:id', 'params' => array(":id" => $ws_row['customer_id']));
            $pdo->update("customer", $cust_update_data, $cust_update_where);

            if (!empty($affected_ws_ids)) {
                foreach ($affected_ws_ids as $key => $affected_ws_id) {
                    $affected_ws_data = array('status' => $member_setting['policy_status'], 'updated_at' => 'msqlfunc_NOW()');
                    $affected_ws_where = array("clause" => 'id=:id', 'params' => array(":id" => $affected_ws_id));
                    $pdo->update("website_subscriptions", $affected_ws_data, $affected_ws_where);
                }
            }
        }
    }
    return true;
}

function create_subscription_dependents($ws_id,$new_ws_id,$dependants, $quali_event, $other_quali_event = '',$extra_params)
{
    global $pdo;

    $old_ws_sql = "SELECT * FROM website_subscriptions WHERE id=:id";
    $old_ws_row = $pdo->selectOne($old_ws_sql, array(":id" => $ws_id));

    $ws_sql = "SELECT * FROM website_subscriptions WHERE id=:id";
    $ws_row = $pdo->selectOne($ws_sql, array(":id" => $new_ws_id));
    if (!empty($ws_row)) {

        $ce_sql = "SELECT ce.id,ce.website_id,w.plan_id,w.product_id,w.eligibility_date,w.prd_plan_type_id 
                    FROM customer_enrollment ce
                    JOIN website_subscriptions w on(w.id = ce.website_id)
                    WHERE ce.website_id=:website_id";
        $ce_result = $pdo->select($ce_sql, array(":website_id" => $new_ws_id));
        foreach ($ce_result as $key => $ce_row) {
            if ($ce_row['prd_plan_type_id'] > 1) {
                foreach ($dependants as $key => $value) {
                    $cd_profile_sql = "SELECT * FROM customer_dependent_profile WHERE id = :id";
                    $row = $pdo->selectOne($cd_profile_sql, array(":id" => $value));
                    if ($row) {

                        $benefit_amount = '';
                        if(isset($extra_params['dep_benefit_amount']) && isset($extra_params['dep_benefit_amount'][$value]) ||  isset($extra_params['in_patient_benefit']) && isset($extra_params['in_patient_benefit'][$value]) || isset($extra_params['out_patient_benefit']) && isset($extra_params['out_patient_benefit'][$value]) || isset($extra_params['monthly_income']) && isset($extra_params['monthly_income'][$value]) || isset($extra_params['benefit_percentage']) && isset($extra_params['benefit_percentage'][$value])) {
                            $benefit_amount = $extra_params['dep_benefit_amount'][$value];
                            $in_patient_benefit = checkIsset($extra_params['dep_in_patient_benefit'][$value]);
                            $out_patient_benefit = checkIsset($extra_params['dep_out_patient_benefit'][$value]);
                            $monthly_income = checkIsset($extra_params['dep_monthly_income'][$value]);
                            $benefit_percentage = checkIsset($extra_params['dep_benefit_percentage'][$value]);

                            $dep_benefit_param = array(
                                "benefit_amount" => checkIsset($benefit_amount),
                                "in_patient_benefit" => checkIsset($in_patient_benefit),
                                "out_patient_benefit" => checkIsset($out_patient_benefit),
                                "monthly_income" => checkIsset($monthly_income),
                                "benefit_percentage" => checkIsset($benefit_percentage),
                            );
                            save_customer_dependent_profile_benefit_amount($value,$ws_row['product_id'],$dep_benefit_param);
                        } else {
                            if(!empty($ws_row['product_id'])) {
                                $ba_sql ="SELECT amount 
                                        FROM customer_benefit_amount 
                                        WHERE 
                                        is_deleted='N' AND 
                                        customer_dependent_profile_id=:customer_dependent_profile_id AND 
                                        product_id=:product_id
                                        ORDER BY id DESC";
                                $ba_where = array(":customer_dependent_profile_id"=>$row['id'],":product_id"=>$ws_row['product_id']);
                                $ba_row = $pdo->selectOne($ba_sql,$ba_where);
                                if(!empty($ba_row)) {
                                    $benefit_amount = $ba_row['amount'];
                                }
                            }
                        }

                        $ins_data = array(
                            'cd_profile_id' => $row['id'],
                            'customer_id' => $row['customer_id'],
                            'display_id' => $row['display_id'],
                            'product_id' => $ce_row['product_id'],
                            'product_plan_id' => $ce_row['plan_id'],
                            'prd_plan_type_id' => $ce_row['prd_plan_type_id'],
                            'website_id' => $ce_row['website_id'],
                            'relation' => makesafe($row['relation']),
                            'fname' => makesafe($row['fname']),
                            'mname' => makesafe($row['mname']),
                            'lname' => makesafe($row['lname']),
                            'email' => makesafe($row['email']),
                            'phone' => makesafe($row['phone']),
                            'birth_date' => $row['birth_date'],
                            'ssn' => $row['ssn'],
                            'last_four_ssn' => $row['last_four_ssn'],
                            'gender' => $row['gender'],
                            'address' => $row['address'],
                            'city' => $row['city'],
                            'zip_code' => $row['zip_code'],
                            'state' => $row['state'],
                            'height_feet' => $row['height_feet'],
                            'height_inches' => $row['height_inches'],
                            'weight' => $row['weight'],
                            'tobacco_use' => $row['tobacco_use'],
                            'smoke_use' => $row['smoke_use'],
                            'employmentStatus' => $row['employmentStatus'],
                            'salary' => $row['salary'],
                            'benefit_amount' => $benefit_amount,
                            'benefit_level' => $row['benefit_level'],
                            'hire_date' => $row['hire_date'],
                            'hours_per_week' => $row['hours_per_week'],
                            'pay_frequency' => $row['pay_frequency'],
                            'us_citizen' => $row['us_citizen'],
                            'eligibility_date' => $ce_row['eligibility_date'],
                            'enrollment_date' => $ce_row['eligibility_date'],
                            'active_since' => strtotime($old_ws_row['active_date'])>0?$old_ws_row['active_date']:$old_ws_row['created_at'],
                            'is_deleted' => 'N',
                            'qualify_event' => $quali_event,
                            "tier_change_date" => NULL,
                            "status" => 'Active',
                            "terminationDate" => NULL,
                            'created_at' => 'msqlfunc_NOW()',
                            'updated_at' => 'msqlfunc_NOW()'
                        );

                        if ($quali_event == "Other") {
                            $ins_data['other_qualify_event'] = $other_quali_event;
                        } else {
                            $ins_data['other_qualify_event'] = "";
                        }

                        $eligibiltyFile_products = array();
                        //  insert eligiblity file code for dependent
                        $parent_prd_id = array();
                        $parent_prd_id = $pdo->selectOne("select parent_product_id from prd_main where id=:prd_id", array(":prd_id" => $ce_row['product_id']));

                        if (in_array($ce_row['product_id'], $eligibiltyFile_products) || in_array($parent_prd_id['parent_product_id'], $eligibiltyFile_products)) {

                            if (strtolower($row['relation']) == "husband" || strtolower($row['relation']) == "wife") {
                                $ins_data['eligibility_code'] = 2;
                            } else {
                                $sel_dependent = "SELECT eligibility_code FROM `customer_dependent` WHERE customer_id =:customer_id AND product_id=:product_id AND product_plan_id=:product_plan_id AND (LOWER(relation)='son' OR LOWER(relation)='daughter') ORDER BY eligibility_code DESC";
                                $res_eligibilty_code = $pdo->selectOne($sel_dependent, array(":customer_id" => $row['customer_id'], ":product_id" => $ce_row['product_id'], ":product_plan_id" => $ce_row['plan_id']));
                                if (count($res_eligibilty_code) > 0) {
                                    if ($res_eligibilty_code['eligibility_code'] < 3) {
                                        $ins_data['eligibility_code'] = 3;
                                    } else {
                                        $ins_data['eligibility_code'] = $res_eligibilty_code['eligibility_code'] + 1;
                                    }
                                } else {
                                    $ins_data['eligibility_code'] = 3;
                                }
                            }
                        }
                        $pdo->insert('customer_dependent', $ins_data);
                    }
                }
            }
        }
    }
}

function get_prd_matrix($product_id, $plan_type)
{
    global $pdo;
    $pm_sql = "select * FROM prd_matrix WHERE is_deleted = 'N' AND product_id=:product_id AND plan_type=:plan_type";
    $new_pm_row = $pdo->selectOne($pm_sql, array(":product_id" => $product_id, ":plan_type" => $plan_type));
    return $new_pm_row;
}

function get_plan_id($product_id, $plan_type, $customer_id = 0)
{
    global $pdo;
    $res = array();

    if (!empty($customer_id)) {
        $cust_row = $pdo->selectOne("SELECT * FROM customer WHERE id=:id", array(":id" => $customer_id));
        if (!empty($cust_row)) {
            $temp_age = dateDifference($cust_row['birth_date'], '%y');
            $sql = "SELECT id,price FROM prd_matrix WHERE age_from < :age AND age_to > :age AND product_id=:product_id AND plan_type=:plan_type";
            $res = $pdo->selectOne($sql, array(':age' => $temp_age, ":product_id" => $product_id, ":plan_type" => $plan_type));
        }
    }
    if (empty($res)) {
        $sql = "SELECT id,price FROM prd_matrix WHERE product_id=:product_id AND plan_type=:plan_type";
        $res = $pdo->selectOne($sql, array(":product_id" => $product_id, ":plan_type" => $plan_type));
    }
    return $res['id'];
}

function getPlanName($prdMatId, $pid = "")
{
    global $pdo;
    if ($pid == "") {
        $sql = "select pt.title FROM prd_matrix pm
        JOIN prd_plan_type pt ON (pt.id = pm.plan_type)
        WHERE pm.id=:id";
        $res = $pdo->selectOne($sql, array(":id" => $prdMatId));
    } else {
        $sql = "select pt.title FROM prd_plan_type pt
        WHERE pt.id=:id";
        $res = $pdo->selectOne($sql, array(":id" => $pid));
    }

    return checkIsset($res['title']);
}

function addQuotes($string)
{
    return "'" . implode("','", explode(',', $string)) . "'";
}

function inArrayAll($needles, $haystack)
{
    return !array_diff($needles, $haystack);
}

function get_upgrade_downgrade_products($product_id)
{
    global $pdo;
    $products_res = array();
    $products_ids = array();
    $upgrade_products_ids = array();
    $downgrade_products_ids = array();
    $sql = "SELECT pu.* FROM prd_up_down_grade pu WHERE pu.is_deleted='N' AND pu.prd_id=:product_id";
    $row = $pdo->selectOne($sql, array(":product_id" => $product_id));
    if (!empty($row)) {
        if ($row['is_upgrade'] == "Y") {
            $products_ids = explode(',', $row['upgrade_prd_id']);
            $upgrade_products_ids = $products_ids;
        }

        if ($row['is_downgrade'] == "Y") {
            $products_ids = array_merge($products_ids, explode(',', $row['downgrade_prd_id']));
            $downgrade_products_ids = $products_ids;
        }

        if (count($products_ids) > 0) {
            $products_ids[] = $product_id;
            $sql = "SELECT p.id as product_id,p.name as product_name,p.product_code FROM prd_main p WHERE p.is_deleted='N' AND p.id IN('" . implode("','", $products_ids) . "') ORDER BY p.name";
            $res = $pdo->select($sql);
            if (!empty($res)) {
                foreach ($res as $key => $row) {
                    if (in_array($row['product_id'], $upgrade_products_ids)) {
                        $upgrade_downgrade = 'upgrade';
                    } else {
                        $upgrade_downgrade = 'downgrade';
                    }

                    $products_res[] = array(
                        'product_id' => $row['product_id'],
                        'product_code' => $row['product_code'],
                        'product_name' => $row['product_name'],
                        'upgrade_downgrade' => $upgrade_downgrade,
                    );
                }
            }
        }
    }
    return $products_res;
}