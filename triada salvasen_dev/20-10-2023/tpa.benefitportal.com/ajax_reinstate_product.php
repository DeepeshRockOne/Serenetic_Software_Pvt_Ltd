<?php
include_once __DIR__ . '/includes/connect.php';
include_once __DIR__ . '/includes/cyberx_payment_class.php';
include_once __DIR__ . '/includes/enrollment_dates.class.php';
include_once __DIR__ . '/includes/function.class.php';
include_once __DIR__ . '/includes/member_enrollment.class.php';
include_once __DIR__ . '/includes/list_bill.class.php';
include_once __DIR__ . '/includes/member_setting.class.php';
include_once __DIR__ . '/includes/policy_setting.class.php';
$policySetting = new policySetting();
$enrollDate = new enrollmentDate();
$functionClass = new functionsList();
$MemberEnrollment = new MemberEnrollment();
$ListBill = new ListBill();
$validate = new Validation();
$memberSetting = new memberSetting();
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
$sel_healthystepfee = isset($_POST['healthystepfee']) ? $_POST['healthystepfee'] : array();
$sel_servicefee = isset($_POST['servicefee']) ? $_POST['servicefee'] : array();

$cust_sql = "SELECT c.*,sp.id as customer_sponsor_id,sp.payment_master_id,sp.ach_master_id 
            FROM customer c 
            JOIN customer sp ON sp.id=c.sponsor_id
            WHERE c.id=:id";
$cust_where = array(":id" => $customer_id);
$cust_row = $pdo->selectOne($cust_sql, $cust_where);
$sponsor_id = $cust_row["customer_sponsor_id"];

$is_list_bill_enroll = "N";
$is_group_member = is_group_member($cust_row['id']);

$REAL_IP_ADDRESS = get_real_ipaddress();
if ($is_group_member == true) {
    $group_cb_row = $pdo->selectOne("SELECT billing_type FROM customer_group_settings WHERE customer_id=:customer_id", array(":customer_id" => $sponsor_id));
    $is_list_bill_enroll = (!empty($group_cb_row['billing_type']) && $group_cb_row['billing_type'] == "list_bill") ? "Y" : "N";
}

$cb_sql = "SELECT *,
                AES_DECRYPT(card_no_full,'" . $CREDIT_CARD_ENC_KEY . "')as cc_no,
                AES_DECRYPT(ach_account_number,'" . $CREDIT_CARD_ENC_KEY . "')as ach_account_number,
                AES_DECRYPT(ach_routing_number,'" . $CREDIT_CARD_ENC_KEY . "')as ach_routing_number 
            FROM customer_billing_profile WHERE is_direct_deposit_account='N' AND customer_id=:customer_id AND is_deleted='N'";
$cb_where = array(":customer_id" => $customer_id);
$cb_res = $pdo->select($cb_sql, $cb_where);


$default_cb_sql = "SELECT id FROM customer_billing_profile WHERE customer_id=:customer_id AND is_default='Y'";
$default_cb_where = array(":customer_id" => $customer_id);
$default_cb_row = $pdo->selectOne($default_cb_sql, $default_cb_where);

if ($step >= 1) {
    if (empty($reinstate_subscriptions)) {
        $validate->setError('reinstate_subscriptions', 'Please select product(s) to reinstate.');
    } else {
        if(count($reinstate_subscriptions) > 1) {
            $core_prd_sql = "SELECT count(w.id) as core_prd_count
                  FROM website_subscriptions w
                  JOIN prd_main p ON (p.id=w.product_id)
                  WHERE
                  p.main_product_type='Core Product' AND
                  w.id IN(".implode(',',$reinstate_subscriptions).")";
            $core_prd_row = $pdo->selectOne($core_prd_sql);
            if(!empty($core_prd_row) && $core_prd_row['core_prd_count'] > 1) {
                $validate->setError('reinstate_subscriptions', 'Please select only one core product.');    
            }
        }
        $subscriptions_coverage_periods = get_coverage_periods_for_reinstate($customer_id, $reinstate_subscriptions);
    }
}

if ($validate->isValid() && $step >= 2) {
    $coverage_periods_data = isset($_POST['coverage_periods']) ? $_POST['coverage_periods'] : array();

    foreach ($subscriptions_coverage_periods as $coverageKey => $cpr) {
        if ($cpr['is_approved_payment'] == true) {
            continue;
        }
        
        $tmp_index = $coverageKey;

        $validate->string(array('required' => true, 'field' => 'payment_date_' . $tmp_index, 'value' => $coverage_periods_data[$tmp_index]['payment_date']), array('required' => 'Please select payment date.'));

        $validate->string(array('required' => true, 'field' => 'billing_profile_' . $tmp_index, 'value' => $coverage_periods_data[$tmp_index]['billing_profile']), array('required' => 'Please select payment method.'));

        if ($coverage_periods_data[$tmp_index]['billing_profile'] == "new_payment_method") {
            $validate->setError('billing_profile_' . $tmp_index, 'Please save billing profile.');
        }
    }
}
if ($validate->isValid()) {
    $healthyStepFee = $functionClass->getMemberHealthyStepFee($customer_id);
    $is_all_coverage_payments_received = true;
    
    /*---- check if all coverage have approved payment start -------*/
    if ($step >= 1) {
        foreach ($subscriptions_coverage_periods as $k=>$cpr) {
            foreach ($cpr['ws_res'] as $key => $value) {
                if ($value['is_approved_payment'] == false) {
                    $is_all_coverage_payments_received = false;
                }

                /********  Update price based on pay period  *************/
                if($is_list_bill_enroll=='Y'){
                    $pay_period=$ListBill->get_pay_period_type($value['id']);
                    $subscriptions_coverage_periods[$k]['ws_res'][$key]['price']=$ListBill->get_plan_pay_period_price($value['price'],$pay_period);
                }
            }
        }
    }

    /*------ Load Coverage Periods --------*/
    if ($step == 1) {

        if ($_POST['is_subscription_changed'] == "Y") {

            /*----- Load coverage periods of selected subscriptions -----------*/
            $coverage_periods_html = '';
            $coverage_periods_html .= '<div class="coverage_period_row">';
            $coverage_periods_html .= '<h4 class="m-t-0 m-b-20">Details/Coverage Period(s)</h4>';
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

                /*$linked_Fee = $MemberEnrollment->getLinkedFee($product_matrix, $cust_row['customer_sponsor_id'], 'N', 'Y');
                unset($linked_Fee['total']);

                $membership_Fee = $MemberEnrollment->getMembershipFee($product_matrix, $customer_id, $cust_row['zip'], 'N', 'Y');
                unset($membership_Fee['total']);*/

                //$healthyStepFee = $MemberEnrollment->getHealthyStepFee($product_matrix, $cust_row['customer_sponsor_id']);

                $serviceFee = $MemberEnrollment->getRenewalServiceFee($product_matrix,$customer_id,$cust_row['customer_sponsor_id'], $temp_totals,'Members',$is_new_order,$is_renewal,$renewalCountsArr);
                unset($serviceFee['total']);

                ob_start();
                include "tmpl/reinstate_product_coverage_period.inc.php";
                $coverage_periods_html .= ob_get_clean();
                $last_coverage_end_date = date("m/d/Y", strtotime($coverage_period_row['end_coverage_period']));
                $coverage_cnt++;
            }
            $coverage_periods_html .= '</div>';

            if ($is_all_coverage_payments_received == true) {
                $response['coverage_periods_html'] = "<h3>The selected products are currently paid up. Proceed to next tab to complete reinstatement.</h3>";
            } else {
                $response['coverage_periods_html'] = $coverage_periods_html;
                $response['last_coverage_end_date'] = $last_coverage_end_date;
            }
            /*-----/Load coverage periods of selected subscriptions -----------*/
        }
    }

    /*------ Load Reinstate Summary --------*/
    if ($step == 2) {
        $reinstate_billing_summary = "";
        $reinstate_next_billing_summary = "";
        if (!$is_all_coverage_payments_received) {
            ob_start();
            include "tmpl/reinstate_billing_summary.inc.php";
            $reinstate_billing_summary .= ob_get_clean();
            $response['reinstate_billing_summary'] = $reinstate_billing_summary;
        }
        ob_start();
        include "tmpl/reinstate_next_billing_summary.inc.php";
        $reinstate_next_billing_summary .= ob_get_clean();

        $response['reinstate_next_billing_summary'] = $reinstate_next_billing_summary;
    }

    if ($step == 3) {

        foreach ($subscriptions_coverage_periods as $k => $coverage_period_row) {
            foreach ($coverage_period_row['ws_res'] as $tmp_key => $tmp_ws_row) {
                if($tmp_ws_row['is_approved_payment'] == true){
                    continue;
                }

                $pricing_change = get_renewals_new_price($tmp_ws_row['id']);
                if($pricing_change['pricing_changed'] == 'Y'){  
                    $new_ws_data = $pricing_change['new_ws_row'];
                    $new_ws_data['product_name'] = $tmp_ws_row['product_name'];
                    $new_ws_data['prd_plan_type_title'] = $tmp_ws_row['prd_plan_type_title'];
                    $new_ws_data['transaction_id'] = $tmp_ws_row['transaction_id'];
                    $new_ws_data['is_post_date_order'] = $tmp_ws_row['is_post_date_order'];
                    $new_ws_data['is_approved_payment'] = $tmp_ws_row['is_approved_payment'];
                    $new_ws_data['renew_count'] = $tmp_ws_row['renew_count'];
                    $new_ws_data['start_coverage_period'] = $tmp_ws_row['start_coverage_period'];
                    $new_ws_data['end_coverage_period'] = $tmp_ws_row['end_coverage_period'];
                    $new_ws_data['next_purchase_date'] = $tmp_ws_row['next_purchase_date'];
                    $coverage_period_row['ws_res'][$tmp_key] = $new_ws_data;
                    $subscriptions_coverage_periods[$k] = $coverage_period_row;
                }
            }
        }
        $order_id = 0;
        if ($is_all_coverage_payments_received == true) {
            $member_setting = $memberSetting->get_status_by_payment($is_all_coverage_payments_received);
            foreach ($subscriptions_coverage_periods as $scp_key => $value) {
                $tmp_index = $scp_key;
                foreach ($value['ws_res'] as $key => $ws_row) {
                    $order_id = $ws_row['order_id'];
                    $transaction_id = $ws_row['transaction_id'];

                    $fee_sql = "SELECT ws.*,pm.name as product_name,pm.product_type as fee_type
                                FROM website_subscriptions ws
                                JOIN order_details od ON(od.website_id = ws.id AND od.is_deleted='N')
                                JOIN prd_main pm ON(pm.id = ws.product_id AND pm.product_type IN('Healthy Step','ServiceFee')) 
                                WHERE ws.status IN('Cancel','Inactive') AND od.order_id=:order_id";
                    $fee_where = array(":order_id" => $order_id);
                    $fee_res = $pdo->select($fee_sql,$fee_where);
                    if(!empty($fee_res)) {
                        foreach ($fee_res as $key => $fee_row) {
                            $tmp_ws_row = $fee_row;
                            $tmp_ws_row['start_coverage_period'] = $value['start_coverage_period'];
                            $tmp_ws_row['end_coverage_period'] = $value['end_coverage_period'];
                            $tmp_ws_row['renew_count'] = $value['renew_count'];
                            $tmp_ws_row['order_id'] = $order_id;
                            $tmp_ws_row['transaction_id'] = $transaction_id;
                            $subscriptions_coverage_periods[$scp_key]['ws_res'][]=$tmp_ws_row;
                        }
                    }
                }
            }

            /*--- Next Purchase Date ---*/
            $subscriptions_dates = array();
            foreach ($subscriptions_coverage_periods as $coverage_period_row) {
                foreach ($coverage_period_row['ws_res'] as $tmp_key => $tmp_ws_row) {
                    if(isset($tmp_ws_row['fee_type']) && in_array($tmp_ws_row['fee_type'],array("Healthy Step"))) {
                        continue;
                    }

                    $startDate = date("Y-m-d",strtotime("+1 day",strtotime($tmp_ws_row['end_coverage_period'])));
                    $product_dates = $enrollDate->getCoveragePeriod($startDate);
                    $startCoveragePeriod = date('Y-m-d',strtotime($product_dates['startCoveragePeriod']));
                    $endCoveragePeriod = date('Y-m-d',strtotime($product_dates['endCoveragePeriod']));

                    $subscriptions_dates[$tmp_ws_row['id']] = array(
                        'product_code' => $tmp_ws_row['product_code'],
                        'product_name' => $tmp_ws_row['product_name'],
                        'price' => $tmp_ws_row['price'],
                        'start_coverage_period' => $startCoveragePeriod,
                        'end_coverage_period' => $endCoveragePeriod,
                        'start_coverage_period_org' => $tmp_ws_row['start_coverage_period'],
                        'end_coverage_period_org' => $tmp_ws_row['end_coverage_period'],
                    );
                }
            }

            $end_coverage_period_arr = array_column($subscriptions_dates,'end_coverage_period_org');        
            foreach ($subscriptions_dates as $key => $snb) {
                $next_billing_date = $enrollDate->getNextBillingDateFromCoverageList($end_coverage_period_arr,$snb['start_coverage_period_org'],$customer_id);
                $subscriptions_dates[$key]['next_purchase_date'] = $next_billing_date;
            }
            /*---/Next Purchase Date ---*/

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

            foreach ($subscriptions_coverage_periods as $cpr) {
                $tmp_order_ids = array();
                foreach ($cpr['ws_res'] as $key => $ws_row) {
                    if(!empty($ws_row['order_id'])){
                        $tmp_order_ids[] = $ws_row['order_id'];
                    }
                    $wsRenewCount = $policySetting->getPolicyRenewCount($ws_row['id']);

                    $ws_update_data = array(
                        'last_order_id' => $ws_row['order_id'],
                        'total_attempts' => 0,
                        'next_attempt_at' => NULL,
                        'last_purchase_date' => 'msqlfunc_NOW()',
                        'payment_type' => $is_list_bill_enroll == "Y" ? "list_bill" : $ws_row['payment_type'],
                        'renew_count' => $wsRenewCount,                        
                        'updated_at' => 'msqlfunc_NOW()',
                    );
                    $ws_update_where = array(
                        "clause" => 'id=:id',
                        'params' => array(
                            ":id" => $ws_row['id'],
                        )
                    );
                    $pdo->update("website_subscriptions", $ws_update_data, $ws_update_where);

                    $extra_params = array();
                    $extra_params['location'] = "reinstate_product";
                    $extra_params['member_setting'] = $member_setting;
                    $extra_params['message'] = 'Reinstate Order - Already Paid Order';
                    $extra_params['transaction_id'] = $ws_row['transaction_id'];
                    $policySetting->removeTerminationDate($ws_row["id"],$extra_params);
                }
                if(!empty($tmp_order_ids)) {
                    $tmp_order_ids = array_unique($tmp_order_ids);
                    foreach ($tmp_order_ids as $tmp_order_id) {
                        $enrollDate->updateNextBillingDateByOrder($tmp_order_id);
                    }                    
                }
            }
            if (isset($payment_error) && $payment_error != "") {
                setNotifyError("Billing failed for some coverage periods.");
            } else {
                setNotifySuccess("Subscriptions reinstate successfully.");
            }

            /*--- Activity Feed ---*/
            if(count($reinstate_subscriptions) > 1) {
                $reinstate_af_summary = '<b>Policies :</b>'; 
                foreach ($reinstate_subscriptions as $key => $ws_id) {
                    $reinstate_af_summary .= '<br/>'.display_policy($ws_id); 
                }
            } else {
                foreach ($reinstate_subscriptions as $key => $ws_id) {
                    $reinstate_af_summary = 'Plan : '.display_policy($ws_id);
                }
            }
            if($location == "admin") {
                $description['ac_message'] =array(
                    'ac_red_1'=>array(
                        'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
                        'title'=>$_SESSION['admin']['display_id'],
                    ),
                    'ac_message_1' =>' reinstate on ',
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
                    'ac_message_1' =>' reinstate on ',
                    'ac_red_2'=>array(
                        'href'=> 'members_details.php?id='.md5($cust_row['id']),
                        'title'=> $cust_row['rep_id'],
                    ),
                    'ac_message_2' =>'<br/>'.$reinstate_af_summary,
                );
                activity_feed(3,$_SESSION['agents']['id'],'Agent',$cust_row["id"], 'customer',"Agent Reinstate on Member",'','',json_encode($description));
            } elseif($location == "group") {
                $description['ac_message'] =array(
                    'ac_red_1'=>array(
                        'href'=> $ADMIN_HOST.'/groups_details.php?id='.md5($_SESSION['groups']['id']),
                        'title'=> $_SESSION['groups']['rep_id'],
                    ),
                    'ac_message_1' =>' reinstate on ',
                    'ac_red_2'=>array(
                        'href'=> $ADMIN_HOST.'/members_details.php?id='.md5($cust_row['id']),
                        'title'=> $cust_row['rep_id'],
                    ),
                    'ac_message_2' =>'<br/>'.$reinstate_af_summary,
                );
                activity_feed(3,$_SESSION['groups']['id'],'Group',$cust_row["id"], 'customer',"Group Reinstate on Member",'','',json_encode($description));
            }
            /*---/Activity Feed ---*/

            $response['status'] = 'payment_success';
            echo json_encode($response);
            exit();

        } else {

            foreach ($subscriptions_coverage_periods as $scp_key => $value) {
                $tmp_index = $scp_key;
                if (isset($sel_fees[$tmp_index])) {
                    foreach ($sel_fees[$tmp_index] as $v) {
                        $ws_sql = "SELECT ws.*,p.name as product_name 
                                    FROM website_subscriptions ws 
                                    JOIN prd_main p ON(p.id=ws.product_id) 
                                    WHERE ws.id=:id";
                        $tmp_ws_row = $pdo->selectOne($ws_sql, array(":id" => $v));
                        $tmp_ws_row['start_coverage_period'] = $value['start_coverage_period'];
                        $tmp_ws_row['end_coverage_period'] = $value['end_coverage_period'];
                        $tmp_ws_row['renew_count'] = $value['renew_count'];
                        $subscriptions_coverage_periods[$scp_key]['ws_res'][]=$tmp_ws_row;
                    }
                }

                if (isset($sel_healthystepfee[$tmp_index])) {
                    foreach ($sel_healthystepfee[$tmp_index] as $v) {
                        $ws_sql = "SELECT ws.*,p.name as product_name,p.product_type as fee_type 
                                    FROM website_subscriptions ws 
                                    JOIN prd_main p ON(p.id=ws.product_id) 
                                    WHERE ws.id=:id";
                        $tmp_ws_row = $pdo->selectOne($ws_sql, array(":id" => $v));
                        $tmp_ws_row['start_coverage_period'] = $value['start_coverage_period'];
                        $tmp_ws_row['end_coverage_period'] = $value['end_coverage_period'];
                        $tmp_ws_row['renew_count'] = $value['renew_count'];
                        $tmp_ws_row['is_healthystepfee'] = true;
                        $subscriptions_coverage_periods[$scp_key]['ws_res'][]=$tmp_ws_row;
                    }
                }

                if (isset($sel_servicefee[$tmp_index])) {
                    foreach ($sel_servicefee[$tmp_index] as $v) {
                        $ws_sql = "SELECT ws.*,p.name as product_name 
                                    FROM website_subscriptions ws 
                                    JOIN prd_main p ON(p.id=ws.product_id) 
                                    WHERE ws.id=:id";
                        $tmp_ws_row = $pdo->selectOne($ws_sql, array(":id" => $v));
                        $tmp_ws_row['start_coverage_period'] = $value['start_coverage_period'];
                        $tmp_ws_row['end_coverage_period'] = $value['end_coverage_period'];
                        $tmp_ws_row['renew_count'] = $value['renew_count'];
                        $tmp_ws_row['is_servicefee'] = true;
                        $subscriptions_coverage_periods[$scp_key]['ws_res'][]=$tmp_ws_row;
                    }
                }
            }
            
            /*---Next Purchase Date ---*/
            /*foreach ($subscriptions_coverage_periods as $coverageKey => $value) {
                if (isset($value['is_approved_payment']) && $value['is_approved_payment'] == true) {
                    foreach ($value['ws_res'] as $key => $ws_row) {
                        $order_id = $ws_row['order_id'];
                        $transaction_id = $ws_row['transaction_id'];

                        $fee_sql = "SELECT ws.*,pm.name as product_name,pm.product_type as fee_type
                                    FROM website_subscriptions ws
                                    JOIN order_details od ON(od.website_id = ws.id)
                                    JOIN prd_main pm ON(pm.id = ws.product_id AND pm.product_type IN('Healthy Step','ServiceFee')) 
                                    WHERE ws.status IN('Cancel','Inactive') AND od.order_id=:order_id";
                        $fee_where = array(":order_id" => $order_id);
                        $fee_res = $pdo->select($fee_sql,$fee_where);
                        if(!empty($fee_res)) {
                            foreach ($fee_res as $key => $fee_row) {
                                $tmp_ws_row = $fee_row;
                                $tmp_ws_row['start_coverage_period'] = $value['start_coverage_period'];
                                $tmp_ws_row['end_coverage_period'] = $value['end_coverage_period'];
                                $tmp_ws_row['renew_count'] = $value['renew_count'];
                                $tmp_ws_row['order_id'] = $order_id;
                                $tmp_ws_row['transaction_id'] = $transaction_id;
                                $subscriptions_coverage_periods[$scp_key]['ws_res'][]=$tmp_ws_row;
                            }
                        }
                    }
                }
            }*/
            
            $subscriptions_dates = array();
            foreach ($subscriptions_coverage_periods as $coverage_period_row) {
                foreach ($coverage_period_row['ws_res'] as $tmp_key => $tmp_ws_row) {
                    if(isset($tmp_ws_row['fee_type']) && in_array($tmp_ws_row['fee_type'],array("Healthy Step"))) {
                        continue;
                    }
                    $startDate = date("Y-m-d",strtotime("+1 day",strtotime($tmp_ws_row['end_coverage_period'])));
                    $product_dates = $enrollDate->getCoveragePeriod($startDate);
                    $startCoveragePeriod = date('Y-m-d',strtotime($product_dates['startCoveragePeriod']));
                    $endCoveragePeriod = date('Y-m-d',strtotime($product_dates['endCoveragePeriod']));

                    $subscriptions_dates[$tmp_ws_row['id']] = array(
                        'product_code' => $tmp_ws_row['product_code'],
                        'product_name' => $tmp_ws_row['product_name'],
                        'price' => $tmp_ws_row['price'],
                        'start_coverage_period' => $startCoveragePeriod,
                        'end_coverage_period' => $endCoveragePeriod,
                        'start_coverage_period_org' => $tmp_ws_row['start_coverage_period'],
                        'end_coverage_period_org' => $tmp_ws_row['end_coverage_period'],
                    );
                }
            }

            $end_coverage_period_arr = array_column($subscriptions_dates,'end_coverage_period_org');        
            foreach ($subscriptions_dates as $key => $snb) {
                $next_billing_date = $enrollDate->getNextBillingDateFromCoverageList($end_coverage_period_arr,$snb['start_coverage_period_org'],$customer_id);
                $subscriptions_dates[$key]['next_purchase_date'] = $next_billing_date;
            }
            /*---/Next Purchase Date ---*/

            $coverage_index = 0;
            $payment_error = "";
            $reinstate_payment_approved = true;
            $payment_approved_ws_ids = array();

            foreach ($subscriptions_coverage_periods as $coverageKey => $value) {
                if (isset($value['is_approved_payment']) && $value['is_approved_payment'] == true) {
                    continue;
                    //If Payment Already Received
                }

                $payment_approved = ($is_list_bill_enroll == "Y" ? true : false);
                $is_post_date_order = false;
                $lastFailOrderId = 0;

                if($is_list_bill_enroll == "N"){

                    $grand_total = 0.00; //All Products Total (Include membership_Fee and linked_Fee)
                    $sub_total = 0.00;
                    $service_fee_total = 0.00;
                    $healthy_step_fee_total = 0.00;
                    $is_renewal = false;
                    $prdMatrixArr = array();
                    $cur_sub_ids = array();
                    foreach ($value['ws_res'] as $ws_row) {
                        if (isset($ws_row['is_approved_payment']) && $ws_row['is_approved_payment'] == true) {
                            continue;
                        } else {
                            if(isset($ws_row['is_servicefee']))  {
                                $service_fee_total += $ws_row['price'];
                            } elseif (isset($ws_row['is_healthystepfee'])) {
                                $healthy_step_fee_total += $ws_row['price'];
                            } else {
                                $sub_total += $ws_row['price'];
                            }

                            if ($ws_row['renew_count'] > 1) {
                                $is_renewal = true;
                            }
                        }

                        if($ws_row['product_type'] == 'Normal'){
                            array_push($prdMatrixArr,$ws_row['plan_id']);
                        }
                        if($ws_row['fail_order_id'] > 0){
                            $lastFailOrderId =  $ws_row['fail_order_id'];  
                        }
                        $cur_sub_ids[] = $ws_row['id'];
                    }

                    $grand_total = $sub_total + $service_fee_total + $healthy_step_fee_total;

                    $tmp_index = $coverageKey;
                    $payment_date = $coverage_periods_data[$tmp_index]['payment_date'];
                    $payment_date = date("Y-m-d",strtotime($payment_date));
                    $billing_id = $coverage_periods_data[$tmp_index]['billing_profile'];

                    $bill_sql = "SELECT *, 
                            AES_DECRYPT(ach_routing_number,'" . $CREDIT_CARD_ENC_KEY . "')as ach_routing_number, 
                            AES_DECRYPT(ach_account_number,'" . $CREDIT_CARD_ENC_KEY . "')as ach_account_number, 
                            AES_DECRYPT(card_no_full,'" . $CREDIT_CARD_ENC_KEY . "')as cc_no 
                            FROM customer_billing_profile WHERE id=:id";
                    $bill_where = array(":id" => $billing_id);
                    $bill_row = $pdo->selectOne($bill_sql, $bill_where);

                    $order_display_id = $functionClass->get_order_id();
                    $order_id = 0;
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
                    // $payment_master_id = 0;
                    // if ($bill_row['payment_mode'] == "ACH") {
                    //     $payment_master_id = $cust_row['ach_master_id'];
                    // } else {
                    //     $payment_master_id = $cust_row['payment_master_id'];
                    // }
                    $payment_master_id = $functionClass->get_agent_merchant_detail($prdMatrixArr,$cust_row['sponsor_id'],$bill_row['payment_mode'],array('is_renewal'=>$is_renewal,'customer_id'=>$customer_id));
                    
                    $payment_approved = false;
                    $txn_id = 0;
                    $payment_processor = "";
                    if(!empty($payment_master_id)){
                        $payment_processor= getname('payment_master',$payment_master_id,'processor_id');
                    }

                    $payment_res = array();

                    if (strtotime($today) < strtotime($payment_date)) {
                        $payment_approved = true;
                        $is_post_date_order = true;
                    } else {
                        $is_post_date_order = false;

                        $cc_params = array();
                        $cc_params['customer_id'] = $cust_row['rep_id'];
                        $cc_params['order_id'] = $order_display_id;
                        $cc_params['amount'] = $grand_total;
                        $cc_params['description'] = "Reinstate order";
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
                        

                        if ($grand_total == 0) {
                            $payment_res = array('status' => 'Success', 'transaction_id' => 0, 'message' => "Bypass payment API due to order have zero amount.");
                        } else {
                            if ($bill_row['payment_mode'] == "ACH") {
                                $api = new CyberxPaymentAPI();
                                $payment_res = $api->processPaymentACH($cc_params, $payment_master_id);
                            } else {
                                if ($cc_params['ccnumber'] == "4111111111111114") {
                                    $payment_res = array("status" => "Success","transaction_id" => 0,"message" => "Manual Approved");
                                } else {
                                    $api = new CyberxPaymentAPI();
                                    $payment_res = $api->processPayment($cc_params, $payment_master_id);
                                }
                            }
                        }

                        /*--- This is for testing----*/
                        // $payment_res = array("status" => "Success","transaction_id" => 0,"message" => "Manual Approved");
                        // if($coverage_index > 0) {
                           // $payment_res = array("status" => "Failed","transaction_id" => 0,"message" => "Manual Failed");    
                        // }
                        /*---/This is for testing----*/

                        if ($payment_res['status'] == 'Success') {
                            $payment_approved = true;
                            $txn_id = $payment_res['transaction_id'];

                            foreach ($value['ws_res'] as $key => $ws_row) {
                                $payment_approved_ws_ids[$ws_row['id']] = array(
                                    'id' => $ws_row['id'],
                                    'start_coverage_period' => $ws_row['start_coverage_period'],
                                    'end_coverage_period' => $ws_row['end_coverage_period'],
                                );
                            }
                        } else {
                            $reinstate_payment_approved = false;
                            $cc_params['order_type'] = 'Reinstate order';
                            $cc_params['browser'] = $BROWSER;
                            $cc_params['os'] = $OS;
                            $cc_params['req_url'] = $REQ_URL;
                            $payment_error = $payment_res['message'];
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
                        'subscription_ids' => implode(',', $cur_sub_ids),
                        'order_count' => $value['renew_count'],
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

                    // $order_id = $pdo->insert("orders", $order_data);
                    $orderBillingId= 0;
                    if($order_id > 0) {
                        $orderBillingId = getname('order_billing_info',$order_id,'id','order_id');

                        $order_where = array("clause" => "id=:id", "params" => array(":id" => $order_id));
                        $pdo->update("orders", $order_data, $order_where);
                    } else {
                        $order_data['original_order_date'] = 'msqlfunc_NOW()';
                        $order_id = $pdo->insert("orders", $order_data);
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

                    if ($orderBillingId > 0) {
                        unset($ord_bill_data['created_at']);
                        $pdo->update("order_billing_info", $ord_bill_data, array("clause" => "id=:id", "params" => array(":id" => $orderBillingId)));
                    } else {
                        $pdo->insert("order_billing_info", $ord_bill_data);
                    }
                }

                $tmp_order_ids = array();
                $tmp_policy_ids = array();
                foreach ($value['ws_res'] as $key => $ws_row) {
                    if (isset($ws_row['is_approved_payment']) && $ws_row['is_approved_payment'] == true) {
                        continue;
                    }

                    if($is_post_date_order == true) {
                        $is_approved_payment = true;
                    } else {
                        if($payment_approved == true) {
                            $is_approved_payment = true;
                        } else {
                            $is_approved_payment = false;
                        }
                    }
                    $member_setting = $memberSetting->get_status_by_payment($is_approved_payment);

                    if($is_list_bill_enroll == "N"){
                        $order_detail_data = array(
                            'order_id' => $order_id,
                            'website_id' => $ws_row['id'],
                            'product_id' => $ws_row['product_id'],
                            'fee_applied_for_product' => $ws_row['fee_applied_for_product'],
                            'plan_id' => $ws_row['plan_id'],
                            'prd_plan_type_id' => $ws_row['prd_plan_type_id'],
                            'product_type' => $ws_row['product_type'],
                            'product_name' => $ws_row['product_name'],
                            'product_code' => $ws_row['product_code'],
                            'start_coverage_period' => $ws_row['start_coverage_period'],
                            'end_coverage_period' => $ws_row['end_coverage_period'],
                            'qty' => $ws_row['qty'],
                            'renew_count' => $ws_row['renew_count'],
                            'unit_price' => $ws_row['price'],
                            'family_member' => get_ws_family_member_count($ws_row['id']),
                        );
                        
                        $checkOdSql = "SELECT id FROM order_details WHERE order_id=:order_id AND website_id=:website_id AND is_deleted='N'";
                        $checkOdParams = array(":order_id" => $order_id,":website_id"=>$ws_row['id']);
                        $checkOdRow = $pdo->selectOne($checkOdSql,$checkOdParams);
                        if (!$checkOdRow) {
                            $detail_insert_id = $pdo->insert("order_details", $order_detail_data);
                        } else {
                            $detail_insert_id = $checkOdRow["id"];
                            $pdo->update("order_details", $order_detail_data, array("clause" => "id=:id", "params" => array(":id" => $detail_insert_id)));
                        }
                    }

                    $ws_history_data = array(
                        'customer_id' => $customer_id,
                        'website_id' => $ws_row['id'],
                        'product_id' => $ws_row['product_id'],
                        'fee_applied_for_product' => $ws_row['fee_applied_for_product'],
                        'plan_id' => $ws_row['plan_id'],
                        'prd_plan_type_id' => $ws_row['prd_plan_type_id'],
                        'order_id' => (!empty($order_id) ? $order_id : 0),
                        'status' => ($is_approved_payment == true ? "Success" : "Fail"),
                        'message' => 'Reinstate Order',
                        'authorize_id' => (!empty($txn_id) ? $txn_id : 0),
                        'processed_at' => 'msqlfunc_NOW()',
                        'created_at' => 'msqlfunc_NOW()',
                    );
                    if ($is_post_date_order == true) {
                        $ws_history_data['message'] = "Reinstate Order Attempt on future ." . (date('m/d/Y', strtotime($payment_date)));
                    }
                    $pdo->insert("website_subscriptions_history", $ws_history_data);

                    if ($is_approved_payment == true) {
                        $ws_update_data = array(
                            'last_order_id' => $order_id,
                            'total_attempts' => 0,
                            'next_attempt_at' => NULL,
                            'payment_type' => $is_list_bill_enroll == "Y" ? "list_bill" : $bill_row['payment_mode'],
                            'last_purchase_date' => 'msqlfunc_NOW()',
                            'updated_at' => 'msqlfunc_NOW()',
                        );

                        if($order_id!=0){
                            $tmp_order_ids[] = $order_id;
                        }
                        $tmp_policy_ids[] = $ws_row['id'];

                        $ws_update_where = array(
                            "clause" => 'id=:id',
                            'params' => array(
                                ":id" => $ws_row['id'],
                            )
                        );
                        $pdo->update("website_subscriptions", $ws_update_data, $ws_update_where);

                        $extra_params = array();
                        $extra_params['location'] = "ajax_reinstate_product";
                        $extra_params['member_setting'] = $member_setting;
                        $extra_params['message'] = $ws_history_data['message'];
                        $extra_params['transaction_id'] = $ws_history_data['authorize_id'];
                        $policySetting->removeTerminationDate($ws_row["id"],$extra_params);
                    } else {
                        if ($coverage_index > 0) {
                            $ws_update_data = array(
                                'last_order_id' => $order_id,
                                'fail_order_id' => $order_id,
                                'total_attempts' => 0,
                                'next_attempt_at' => NULL,
                                'last_purchase_date' => 'msqlfunc_NOW()',
                                'payment_type' => $is_list_bill_enroll == "Y" ? "list_bill" : $bill_row['payment_mode'],
                                'updated_at' => 'msqlfunc_NOW()',
                            );
                            $ws_update_where = array(
                                "clause" => 'id=:id',
                                'params' => array(
                                    ":id" => $ws_row['id'],
                                )
                            );
                            $pdo->update("website_subscriptions", $ws_update_data, $ws_update_where);
                        }else{
                            $ws_update_data = array(
                                'fail_order_id' => $order_id,
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
                }

                if(!empty($tmp_order_ids)) {
                    $tmp_order_ids = array_unique($tmp_order_ids);
                    foreach ($tmp_order_ids as $tmp_order_id) {
                        $enrollDate->updateNextBillingDateByOrder($tmp_order_id);
                    }                    
                }
                if($payment_approved == true){
                    if (!isset($healthyStepTermDateSet)) {
                        $healthyStepTermDateSet = true;
                        $policySetting->setHeathyStepDefaultTerminationDate($customer_id);
                    }
                }

                // Activity feed code start
                    if($is_list_bill_enroll == "N"){
                        if ($is_post_date_order == true) {
                            $other_params = array();
                            $transactionInsId=$functionClass->transaction_insert($order_id,'Credit','Post Payment','Post Transaction',0,$other_params);

                            if($location == "admin") {
                                $description['ac_message'] =array(
                                    'ac_red_1'=>array(
                                        'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
                                        'title'=>$_SESSION['admin']['display_id'],
                                    ),
                                    'ac_message_1' =>' reinstate on ',
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
                                    'ac_message_1' =>' reinstate on ',
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
                                    'ac_message_1' =>' reinstate on ',
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
                                        'ac_message_1' =>' reinstate on ',
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
                                        'ac_message_1' =>' reinstate on ',
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
                                        'ac_message_1' =>' reinstate on ',
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
                                $other_params["reason"] = checkIsset($payment_error);
                                $transactionInsId=$functionClass->transaction_insert($order_id,'Failed','Payment Declined','Transaction Declined',0,$other_params);

                                if($location == "admin") {
                                    $description['ac_message'] =array(
                                        'ac_red_1'=>array(
                                            'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
                                            'title'=>$_SESSION['admin']['display_id'],
                                        ),
                                        'ac_message_1' =>' reinstate on ',
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
                                        'ac_message_1' =>' reinstate on ',
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
                                        'ac_message_1' =>' reinstate on ',
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
                    }
                // Activity feed code ends

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
                
                //********* Payable Insert Code Start ********************
                if($is_list_bill_enroll == "N"){
                    if ($payment_approved == true && $is_post_date_order != true && checkIsset($bill_row['payment_mode']) != "ACH") {
                        $payable_params = array(
                            'payable_type' => 'Vendor',
                            'type' => 'Vendor',
                            'transaction_tbl_id' => $transactionInsId['id'],
                        );
                        $payable = $functionClass->payable_insert($order_id, 0, 0, 0, $payable_params);
                    }
                }

                if ($payment_approved == false) {
                    $payment_error = $payment_res['message'];

                    //Update termination date as per last approved payment for coverage
                    if (!empty($payment_approved_ws_ids)) {
                        foreach ($payment_approved_ws_ids as $key => $pay_app_ws) {
                            $termination_date = $enrollDate->getTerminationDate($pay_app_ws['id']);

                            $member_setting = $memberSetting->get_status_by_payment($payment_approved,$pay_app_ws['end_coverage_period']);
                            // If coverage period is end on past date or today then inactive immidiate
                            if (strtotime($pay_app_ws['end_coverage_period']) <= strtotime($today)) {
                                $extra_params = array();
                                $extra_params['location'] = "reinstate_product";
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
                        }
                    }

                    //Stop Reinstate Processing
                    break;
                }

                $coverage_index++;
            }

            if ($coverage_index == 0 && $payment_error != "") {
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
                        $reinstate_af_summary = 'Plan : '.display_policy($ws_id);
                    }
                }

                if($location == "admin") {
                    $description['ac_message'] =array(
                        'ac_red_1'=>array(
                            'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
                            'title'=>$_SESSION['admin']['display_id'],
                        ),
                        'ac_message_1' =>' reinstate on ',
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
                        'ac_message_1' =>' reinstate on ',
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
                        'ac_message_1' =>' reinstate on ',
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