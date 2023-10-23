<?php
include_once dirname(__DIR__) . "/includes/functions.php";
require_once dirname(__DIR__) . '/includes/enrollment_dates.class.php';
include_once dirname(__DIR__) . '/includes/trigger.class.php';
require_once dirname(__DIR__) . '/includes/list_bill.class.php';
include_once dirname(__DIR__) . '/includes/function.class.php';
/**
  * Policy Settings
  *
  */
class policySetting {

    public function setTerminationDate($ws_id,$termination_date = '',$reason = '',$extra_params = array())
    { 
        global $pdo,$ADMIN_HOST;
        $enrollDate = new enrollmentDate();
        $TriggerMailSms = new TriggerMailSms();
        $functionClass = new functionsList();
        $ListBill = new ListBill();

        $BROWSER = getBrowser();
        $OS = getOS($_SERVER['HTTP_USER_AGENT']);
        $REQ_URL = (isset($_SERVER["REQUEST_URI"])?($_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]):'system');
        $today_date = date("Y-m-d");

        $is_encrypted = (isset($extra_params['is_encrypted'])?$extra_params['is_encrypted']:'N');
        $location = (isset($extra_params['location'])?$extra_params['location']:'system');
        $activity_feed_flag = (isset($extra_params['activity_feed_flag'])?$extra_params['activity_feed_flag']:'');

        if($termination_date == "") {
            $termination_date = $enrollDate->getTerminationDate($ws_id);
        }
        $termination_date = date('Y-m-d',strtotime($termination_date));

        $org_reason = $reason;
        if($reason == "Failed Billing") {
            $reason = "Non Payment";
        }

        $incr = " AND ws.id=:id";
        if($is_encrypted=='Y'){
            $incr = " AND md5(ws.id)=:id";
        }
        $ws_sql = "SELECT ws.*,c.sponsor_id,c.rep_id 
                    FROM website_subscriptions ws 
                    JOIN customer c ON(c.id = ws.customer_id) 
                    WHERE 1 $incr";
        $ws_row = $pdo->selectOne($ws_sql,array(':id' => $ws_id));

        if(!empty($ws_row) && (empty($ws_row['termination_date']) || (strtotime($termination_date) != strtotime($ws_row['termination_date'])))) {

            $ws_id = $ws_row['id'];

            if($ws_row['status'] == "Post Payment" && strtotime($termination_date) == strtotime($ws_row['eligibility_date'])) {
                $ord_sql = "SELECT o.*,od.unit_price 
                            FROM orders o
                            JOIN order_details od ON(od.order_id = o.id AND od.is_deleted='N')
                            WHERE 
                            o.status IN('Pending Payment','Post Payment','Payment Declined') AND od.website_id=:website_id";
                $ord_where = array(":website_id"=>$ws_id);
                $ord_row = $pdo->selectOne($ord_sql,$ord_where);
                
                if(!empty($ord_row['id'])) {

                    /*---- Cancel old order ----*/
                    $ord_update_data = array(
                        'status' => "Cancelled",
                        'future_payment' => "N",
                        'updated_at' => "msqlfunc_NOW()",
                    );
                    $ord_update_where = array(
                        "clause" => "id=:id",
                        "params" => array(":id" => $ord_row['id'])
                    );
                    $pdo->update('orders',$ord_update_data,$ord_update_where);
                    $transParams = array("reason" => $reason);
                    $functionClass->transaction_insert($ord_row['id'],'Debit','Cancelled','Transaction Cancelled',0,$transParams);
                    /*---- Cancel old order ----*/


                    $od_sql = "SELECT od.* 
                                FROM order_details od 
                                WHERE 
                                od.order_id=:order_id AND 
                                od.is_refund='N' AND 
                                od.website_id != :website_id AND od.is_deleted='N'";
                    $od_where = array(
                        ":order_id" => $ord_row['id'],
                        ":website_id" => $ws_id
                    );
                    $od_res = $pdo->select($od_sql,$od_where);

                    if(!empty($od_res)) {
                        $ord_data = $ord_row;
                        unset($ord_data['id']);
                        unset($ord_data['unit_price']);

                        $tmp_subscription_ids = explode(',', $ord_data['subscription_ids']);
                        foreach ($tmp_subscription_ids as $sub_id_key => $tmp_subscription_id) {
                            if($ws_id == $tmp_subscription_id) {
                                unset($tmp_subscription_ids[$sub_id_key]);
                            }
                        }

                        $ord_data['display_id'] = $functionClass->get_order_id();
                        $ord_data['subscription_ids'] = implode(',',$tmp_subscription_ids);
                        $ord_data['product_total'] = ($ord_row['product_total'] - $ord_row['unit_price']);
                        $ord_data['sub_total'] = ($ord_row['sub_total'] - $ord_row['unit_price']);
                        $ord_data['grand_total'] = ($ord_row['grand_total'] - $ord_row['unit_price']);
                        $new_ord_id = $pdo->insert('orders',$ord_data);

                        foreach ($od_res as $key => $new_od_row) {
                            unset($new_od_row['id']);
                            $new_od_row['order_id'] = $new_ord_id;
                            $pdo->insert('order_details',$new_od_row);
                        }

                        $functionClass->transaction_insert($new_ord_id,'Credit','Pending','Post Transaction','',array("transaction_id"=>0));

                        
                        $ws_update_data = array(
                            'last_order_id' => $new_ord_id,
                            'updated_at' => "msqlfunc_NOW()",
                        );
                        $ws_update_where = array(
                            "clause" => "id IN(:id)",
                            "params" => array(":id" => implode(',',$tmp_subscription_ids))
                        );
                        $pdo->update('website_subscriptions',$ws_update_data,$ws_update_where);
                    }
                }
            }

            $ws_term_data = array(
                'termination_date' => $termination_date,
                'term_date_set' => $today_date,
                'termination_reason' => $reason,
            );
            $ws_term_where = array(
                "clause" => "id=:id",
                "params" => array(":id" => $ws_id)
            );


            $ce_term_data = array(
                'process_status' => "Pending",
            );
            $ce_term_where = array(
                "clause" => "website_id=:website_id",
                "params" => array(":website_id" => $ws_id)
            );


            $term_cd_data = array(
                "terminationDate" => date('Y-m-d', strtotime($termination_date)),
                "updated_at" => "msqlfunc_NOW()"
            );
            $term_cd_where = array(
                'clause' => "website_id=:website_id",
                'params' => array(":website_id" => $ws_id)
            );


            if(strtotime($termination_date) <= strtotime($today_date) || strtotime($termination_date) == strtotime($ws_row['eligibility_date'])) {
                $term_cd_data["status"]="Inactive";
                $ce_term_data['process_status'] = 'Active';
                $ws_term_data['status'] = "Inactive";

                if(empty($ws_row['termination_date'])){
                    $products = array($ws_row["product_id"] => $today_date);
                    $TriggerMailSms->trigger_action_mail('member_cancellation',$ws_row['customer_id'],'member','addedTerminationDate',$products);
                }
            } else {
                if(empty($ws_row['termination_date'])){
                    $products = array($ws_row["product_id"] => $termination_date);
                    $TriggerMailSms->trigger_action_mail('member_cancellation',$ws_row['customer_id'],'member','addedTerminationDate',$products);
                }
            }

            $sponsor_billing_method = get_sponsor_billing_method();

            // Refund/Charge Adjustment for ListBill Member Code Start
            if ($sponsor_billing_method == "list_bill") {
                $ListBill->getSubscriptionRefundChargeCoverage($ws_row["id"]);
                /*$ListBill->listBillMemberAdjustment("refund",$ws_row["id"],$ws_row["customer_id"],$ws_row["sponsor_id"],$termination_date);

                if(!empty($ws_row['termination_date']) && strtotime($ws_row['termination_date']) < strtotime($termination_date)){
                    $ListBill->listBillMemberAdjustment("charged",$ws_row["id"],$ws_row["customer_id"],$ws_row["sponsor_id"]);
                }*/
            }
            // Refund/Charge Adjustment for ListBill Member Code Ends

            $pdo->update("customer_dependent", $term_cd_data, $term_cd_where);
            $pdo->update("customer_enrollment", $ce_term_data, $ce_term_where);
            $pdo->update("website_subscriptions", $ws_term_data, $ws_term_where);

            $ws_history = array(
                'customer_id' => $ws_row['customer_id'],
                'website_id' => $ws_row['id'],
                'product_id' => $ws_row['product_id'],
                'plan_id' => $ws_row['plan_id'],
                'fee_applied_for_product' => $ws_row['fee_applied_for_product'],
                'order_id' => $ws_row['last_order_id'],
                'status' => 'Termed',
                'message' => (isset($extra_params['message'])?$extra_params['message']:$reason),
                'admin_id' => (isset($_SESSION['admin']['id'])?$_SESSION['admin']['id']:''),
                'note' => $reason,
                'processed_at' => 'msqlfunc_NOW()'
            );
            $pdo->insert("website_subscriptions_history", $ws_history);

            /*------------- Terminate Reinstate Post Payment Order -----------*/
            if(isset($extra_params['cancel_post_payment_order'])) {
                $reinstate_ord_sql = "SELECT o.id 
                            FROM orders o 
                            WHERE 
                            o.status IN ('Pending Payment','Post Payment','Payment Declined') AND
                            o.is_reinstate_order='Y' AND
                            o.future_payment='Y'AND 
                            o.customer_id=:customer_id
                            GROUP BY o.id";
                $reinstate_ord_res = $pdo->select($reinstate_ord_sql,array(":customer_id" => $ws_row['customer_id']));
                if(!empty($reinstate_ord_res)) {
                    foreach ($reinstate_ord_res as $reinstate_ord_row) {
                        $re_upd_order_data = array(
                            'status' => 'Cancelled',
                            'future_payment' => 'N',
                            'updated_at' => 'msqlfunc_NOW()',
                        );
                        $re_upd_order_where = array("clause" => "id=:id", "params" => array(":id" => $reinstate_ord_row['id']));
                        $pdo->update("orders",$re_upd_order_data,$re_upd_order_where);
                        
                        $other_params=array("req_url" => "cron_scripts/monthly_subscription_order.php");
                        $functionClass->transaction_insert($reinstate_ord_row['id'],'Debit','Cancelled','Transaction Cancelled','',$other_params);
                    }
                }
            }
            /*-------------/Terminate Reinstate Post Payment Order -----------*/


            $is_all_prd_terminated = false;
            $active_ws_sql = "SELECT ws.id 
                                FROM website_subscriptions ws 
                                JOIN prd_main pm ON (pm.id=ws.product_id)
                                WHERE ws.status NOT IN('Inactive') AND pm.type !='Fees' AND ws.customer_id=:customer_id";
            $active_ws_row = $pdo->selectOne($active_ws_sql,array(":customer_id" => $ws_row['customer_id']));

            if(empty($active_ws_row)) {                                
                $upd_cust_data = array('status' => 'Inactive');
                $upd_cust_where = array(
                    "clause" => "id=:id",
                    "params" => array(":id" => $ws_row['customer_id'])
                );
                $pdo->update("customer", $upd_cust_data, $upd_cust_where);

                $is_all_prd_terminated = true;
            } else {
                /*-- Check All Polices Terminate In Future --*/
                $active_ws_sql = "SELECT ws.id 
                                FROM website_subscriptions ws 
                                JOIN prd_main pm ON (pm.id=ws.product_id)
                                WHERE (ws.termination_date IS NULL OR ws.termination_date='') AND pm.type !='Fees' AND ws.customer_id=:customer_id";
                $active_ws_row = $pdo->selectOne($active_ws_sql,array(":customer_id" => $ws_row['customer_id']));
                if(empty($active_ws_row)) {
                    $is_all_prd_terminated = true;
                }
            }

            if(!isset($extra_params['skip_fees_terminate'])) {
                if($is_all_prd_terminated == true) {
                    $extra_params2 = $extra_params;
                    $extra_params2['skip_fees_terminate'] = true;
                    $active_fees_ws_sql = "SELECT ws.id,ws.termination_date as fee_termination_date,ws.eligibility_date as fee_eligibility_date,pr_ws.termination_date FROM website_subscriptions ws 
                                    JOIN website_subscriptions pr_ws ON(pr_ws.product_id=ws.fee_applied_for_product AND pr_ws.customer_id=ws.customer_id) 
                                    JOIN prd_main pm ON (pm.id=ws.product_id)
                                    WHERE ws.status NOT IN('Inactive') AND pm.type='Fees' AND ws.customer_id=:customer_id
                                    GROUP BY ws.id ORDER BY pr_ws.termination_date DESC";
                    $active_fees_ws_res = $pdo->select($active_fees_ws_sql,array(":customer_id" => $ws_row['customer_id']));
                    foreach ($active_fees_ws_res as $fees_ws_row) {
                        if(strtotime($fees_ws_row['fee_termination_date']) > 0) {
                            if(strtotime($fees_ws_row['fee_termination_date']) < strtotime($fees_ws_row['termination_date'])) {
                                continue;
                            }
                        }
                        if(strtotime($fees_ws_row['termination_date']) < strtotime($fees_ws_row['fee_eligibility_date'])) {
                            $fees_ws_row['termination_date'] = $fees_ws_row['fee_eligibility_date'];
                        }
                        $this->setTerminationDate($fees_ws_row['id'],$fees_ws_row['termination_date'],$org_reason,$extra_params2);
                    }
                } else {
                    /*$extra_params3 = $extra_params;
                    $extra_params3['skip_fees_terminate'] = true;
                    $active_fees_ws_sql = "SELECT ws.id,ws.termination_date as fee_termination_date,ws.eligibility_date as fee_eligibility_date,pr_ws.termination_date 
                    FROM website_subscriptions ws 
                    JOIN website_subscriptions pr_ws ON(pr_ws.product_id=ws.fee_applied_for_product AND pr_ws.customer_id=ws.customer_id) 
                    JOIN prd_main pm ON (pm.id=ws.product_id)
                    WHERE ws.status NOT IN('Inactive') AND pm.type='Fees' AND pm.product_type NOT IN('Healthy Step') AND ws.customer_id=:customer_id AND pr_ws.product_id=:product_id
                    GROUP BY ws.id ORDER BY pr_ws.termination_date DESC";
                    $active_fees_ws_res = $pdo->select($active_fees_ws_sql,array(":customer_id" => $ws_row['customer_id'],":product_id" => $ws_row['product_id']));
                    foreach ($active_fees_ws_res as $fees_ws_row) {
                        $fee_termination_date = $termination_date;
                        if(strtotime($fees_ws_row['fee_termination_date']) > 0) {
                            if(strtotime($fees_ws_row['fee_termination_date']) < strtotime($fee_termination_date)) {
                                continue;
                            }
                        }
                        if(strtotime($fee_termination_date) < strtotime($fees_ws_row['fee_eligibility_date'])) {
                            $fee_termination_date = $fees_ws_row['fee_eligibility_date'];
                        }
                        $this->setTerminationDate($fees_ws_row['id'],$fee_termination_date,$org_reason,$extra_params3);
                    }*/
                }
            }

            if($location == "member_detail") {
                $portal = isset($extra_params['portal'])?$extra_params['portal']:'';
                
                if (!empty(strtotime($ws_row['termination_date']))) {
                    $af_message = 'changed termination date';
                    if($portal == "admin") {
                        $af_desc = array();
                        $af_desc['ac_message'] =array(
                            'ac_red_1'=>array(
                                'href'=> 'admin_profile.php?id='.md5($_SESSION['admin']['id']),
                                'title'=> $_SESSION['admin']['display_id'],
                            ),
                            'ac_message_1' => $af_message.' on ',
                            'ac_red_2'=>array(
                                'href'=> 'members_details.php?id='.md5($ws_row['customer_id']),
                                'title'=>$ws_row['rep_id'],
                            ),
                            'ac_message_2' =>' <br/> Policy : '.display_policy($ws_row['id']).' <br/> Termination date changed from : '.displayDate($ws_row['termination_date']).' to : '.displayDate($termination_date).' <br/>Termination Reason : '. $reason,
                        );
                        activity_feed(3, $_SESSION['admin']['id'], 'Admin',$ws_row['customer_id'], 'customer', 'Admin '. ucwords($af_message),'','',json_encode($af_desc));

                    } elseif($portal == "agent") {
                        $af_desc = array();
                        $af_desc['ac_message'] =array(
                            'ac_red_1'=>array(
                                'href'=> 'agent_detail_v1.php?id='.md5($_SESSION['agents']['id']),
                                'title'=> $_SESSION['agents']['rep_id'],
                            ),
                            'ac_message_1' => $af_message.' on ',
                            'ac_red_2'=>array(
                                'href'=> 'members_details.php?id='.md5($ws_row['customer_id']),
                                'title'=>$ws_row['rep_id'],
                            ),
                            'ac_message_2' =>' <br/> Policy : '.display_policy($ws_row['id']).' <br/> Termination date changed from : '.displayDate($ws_row['termination_date']).' to : '.displayDate($termination_date).' <br/>Termination Reason : '. $reason,
                        );
                        activity_feed(3, $_SESSION['agents']['id'], 'Agent',$ws_row['customer_id'], 'customer', 'Agent '. ucwords($af_message),'','',json_encode($af_desc));
                    }elseif($portal == "group") {
                        $af_desc = array();
                        $af_desc['ac_message'] =array(
                            'ac_red_1'=>array(
                                'href'=> $ADMIN_HOST.'/groups_details.php?id='.md5($_SESSION['groups']['id']),
                                'title'=> $_SESSION['groups']['rep_id'],
                            ),
                            'ac_message_1' => $af_message.' on ',
                            'ac_red_2'=>array(
                                'href'=> $ADMIN_HOST.'/members_details.php?id='.md5($ws_row['customer_id']),
                                'title'=>$ws_row['rep_id'],
                            ),
                            'ac_message_2' =>' <br/> Policy : '.display_policy($ws_row['id']).' <br/> Termination date changed from : '.displayDate($ws_row['termination_date']).' to : '.displayDate($termination_date).' <br/>Termination Reason : '. $reason,
                        );
                        activity_feed(3, $_SESSION['groups']['id'], 'Group',$ws_row['customer_id'], 'customer', 'Group '. ucwords($af_message),'','',json_encode($af_desc));
                    }
                } else {
                    $af_message = 'set termination date';
                    if($portal == "admin") {
                        $af_desc = array();
                        $af_desc['ac_message'] =array(
                            'ac_red_1'=>array(
                                'href'=> 'admin_profile.php?id='.md5($_SESSION['admin']['id']),
                                'title'=> $_SESSION['admin']['display_id'],
                            ),
                            'ac_message_1' => $af_message.' on ',
                            'ac_red_2'=>array(
                                'href'=> 'members_details.php?id='.md5($ws_row['customer_id']),
                                'title'=>$ws_row['rep_id'],
                            ),
                            'ac_message_2' =>' <br/> Policy : '.display_policy($ws_row['id']).' <br/> Termination date : '.displayDate($termination_date).' <br/>Termination Reason : '. $reason,
                        );
                        activity_feed(3, $_SESSION['admin']['id'], 'Admin',$ws_row['customer_id'], 'customer', 'Admin '. ucwords($af_message),'','',json_encode($af_desc));                        
                    
                    } elseif($portal == "agent") {
                        $af_desc = array();
                        $af_desc['ac_message'] =array(
                            'ac_red_1'=>array(
                                'href'=> 'agent_detail_v1.php?id='.md5($_SESSION['agents']['id']),
                                'title'=> $_SESSION['agents']['rep_id'],
                            ),
                            'ac_message_1' => $af_message.' on ',
                            'ac_red_2'=>array(
                                'href'=> 'members_details.php?id='.md5($ws_row['customer_id']),
                                'title'=>$ws_row['rep_id'],
                            ),
                            'ac_message_2' =>' <br/> Policy : '.display_policy($ws_row['id']).' <br/> Termination date : '.displayDate($termination_date).' <br/>Termination Reason : '. $reason,
                        );
                        activity_feed(3,$_SESSION['agents']['id'], 'Agent',$ws_row['customer_id'], 'customer', 'Agent '. ucwords($af_message),'','',json_encode($af_desc));
                    
                    } elseif($portal == "group") {
                        $af_desc = array();
                        $af_desc['ac_message'] =array(
                            'ac_red_1'=>array(
                                'href'=> $ADMIN_HOST.'/groups_details.php?id='.md5($_SESSION['groups']['id']),
                                'title'=> $_SESSION['groups']['rep_id'],
                            ),
                            'ac_message_1' => $af_message.' on ',
                            'ac_red_2'=>array(
                                'href'=> $ADMIN_HOST.'/members_details.php?id='.md5($ws_row['customer_id']),
                                'title'=>$ws_row['rep_id'],
                            ),
                            'ac_message_2' =>' <br/> Policy : '.display_policy($ws_row['id']).' <br/> Termination date : '.displayDate($termination_date).' <br/>Termination Reason : '. $reason,
                        );
                        activity_feed(3,$_SESSION['groups']['id'], 'Group',$ws_row['customer_id'], 'customer', 'Group '. ucwords($af_message),'','',json_encode($af_desc));
                    }
                }
            } elseif($location == "change_product_status") {
                $af_message = 'set termination date';

                $af_desc = array();
                $af_desc['ac_message'] =array(
                    'ac_red_1'=>array(
                        'href'=> 'admin_profile.php?id='.md5($_SESSION['admin']['id']),
                        'title'=> $_SESSION['admin']['display_id'],
                    ),
                    'ac_message_1' => $af_message.' on ',
                    'ac_red_2'=>array(
                        'href'=> 'members_details.php?id='.md5($ws_row['customer_id']),
                        'title'=>$ws_row['rep_id'],
                    ),
                    'ac_message_2' =>' <br/> Policy : '.display_policy($ws_row['id']).' <br/> Termination date : '.displayDate($termination_date).' <br/>Termination Reason : '. $reason,
                );
                activity_feed(3, $_SESSION['admin']['id'], 'Admin',$ws_row['customer_id'], 'customer', 'Admin '. ucwords($af_message),'','',json_encode($af_desc));
            }

            if($org_reason == "Failed Billing") {
                $af_desc = array();
                $af_desc['ac_message'] =array(
                    'ac_message_1' =>'System Terminated Policy '.display_policy($ws_row['id']).' for non-payment <br/> Termination date : '.displayDate($termination_date),
                );
                activity_feed(3,$ws_row['customer_id'],'Customer',$ws_row['customer_id'], 'customer', 'System Terminated Policy','','',json_encode($af_desc));
            }

            if($activity_feed_flag == "change_order_status") {
                $af_desc = array();
                $af_desc['ac_message'] =array(
                    'ac_message_1' =>'Terminated Policy '.display_policy($ws_row['id']).' <br/> Reason : Order status changed to  '.$reason.' <br/> Termination date : '.displayDate($termination_date),
                );
                activity_feed(3,$ws_row['customer_id'],'Customer',$ws_row['customer_id'], 'customer', 'Terminated Policy','','',json_encode($af_desc));

            } else if($activity_feed_flag == "return_order") {
                $af_desc = array();
                $af_desc['ac_message'] =array(
                    'ac_message_1' =>'Terminated Policy '.display_policy($ws_row['id']).' <br/> Reason : '.$reason.' <br/> Termination date : '.displayDate($termination_date),
                );
                activity_feed(3,$ws_row['customer_id'],'Customer',$ws_row['customer_id'], 'customer', 'Terminated Policy','','',json_encode($af_desc));
            }
        }
        return true;
    }

    public function removeTerminationDate($ws_id,$extra_params = array())
    {
        global $pdo,$ADMIN_HOST;
        $enrollDate = new enrollmentDate();
        $ListBill = new ListBill();
        $today_date = date("Y-m-d");
        $BROWSER = getBrowser();
        $OS = getOS($_SERVER['HTTP_USER_AGENT']);
        $REQ_URL = (isset($_SERVER["REQUEST_URI"])?($_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]):'system');
        $today_date = date("Y-m-d");

        $is_encrypted = (isset($extra_params['is_encrypted'])?$extra_params['is_encrypted']:'N');
        $location = (isset($extra_params['location'])?$extra_params['location']:'system');
        $member_setting = array('member_status' => 'Active','policy_status' => 'Active','dependent_status' => 'Active');
        if(!empty($extra_params['member_setting'])) {
            $member_setting = $extra_params['member_setting'];
        }

        $incr = " AND ws.id=:id";
        if($is_encrypted=='Y'){
            $incr = " AND md5(ws.id)=:id";
        }
        $ws_sql = "SELECT ws.id,ws.status,ws.customer_id,ws.product_id,ws.plan_id,ws.fee_applied_for_product,ws.prd_plan_type_id,ws.last_order_id,c.sponsor_id,c.rep_id,p.product_type,p.is_member_benefits,p.is_fee_on_renewal,p.fee_renewal_type,p.fee_renewal_count,p.member_payment_type,ws.eligibility_date 
                    FROM website_subscriptions ws 
                    JOIN customer c ON(c.id = ws.customer_id)
                    JOIN prd_main p ON (p.id=ws.product_id) 
                    WHERE 1 $incr";
        $ws_row = $pdo->selectOne($ws_sql,array(':id' => $ws_id));

        if(!empty($ws_row)) {

            $new_ws_sql = "SELECT ce.id
                FROM customer_enrollment ce_p
                JOIN customer_enrollment ce  ON(ce.parent_coverage_id = ce_p.id)
                WHERE ce_p.website_id=:ws_id AND ce.process_status IN('Pending')";
            $new_ws_row = $pdo->selectOne($new_ws_sql, array(":ws_id" => $ws_row['id']));
            if(!empty($new_ws_row)) {
                return true;
            }

            $ws_upd_data = array(
                'termination_date' => NULL,
                'termination_reason' => NULL,
                "term_date_set" => NULL,
                'status' => $member_setting['policy_status'],
            );
            /*------ Set Termination Date for Healthy Step ------*/
            if($ws_row['product_type'] == "Healthy Step" && !isset($extra_params['remove_healthy_step_term'])) {
                if($ws_row['is_member_benefits'] == "Y" && $ws_row['is_fee_on_renewal'] == "Y" && $ws_row['fee_renewal_type'] == "Renewals" && $ws_row['fee_renewal_count'] > 0) {

                    $product_dates = $enrollDate->getCoveragePeriod($ws_row['eligibility_date'],$ws_row['member_payment_type']);
                    $tmp_fee_renewal_count = $ws_row['fee_renewal_count'];
                    $tmp_start_coverage_date = $product_dates['startCoveragePeriod'];
                    $tmp_termination_date = $product_dates['endCoveragePeriod'];
                    while ($tmp_fee_renewal_count > 0) {
                        $product_dates = $enrollDate->getCoveragePeriod($tmp_start_coverage_date,$ws_row['member_payment_type']);
                        $tmp_start_coverage_date = date("Y-m-d",strtotime('+1 day',strtotime($product_dates['endCoveragePeriod'])));
                        $tmp_termination_date = date("Y-m-d",strtotime($product_dates['endCoveragePeriod']));
                        $tmp_fee_renewal_count--;
                    }
                    $ws_upd_data['termination_date'] = $tmp_termination_date;
                    $ws_upd_data['term_date_set'] = date('Y-m-d');
                    $ws_upd_data['termination_reason'] = 'Policy Change';

                    if(strtotime($tmp_termination_date) <= strtotime($today_date)) {
                        $ws_upd_data['status'] = 'Inactive';
                    }
                }
            }
            /*------/Set Termination Date for Healthy Step ------*/

            $ws_upd_where = array(
                "clause" => "id=:id",
                "params" => array(":id" => $ws_row['id'])
            );
            $pdo->update("website_subscriptions", $ws_upd_data, $ws_upd_where);

            $ws_history_data = array(
                'customer_id' => $ws_row['customer_id'],
                'website_id' => $ws_row['id'],
                'product_id' => $ws_row['product_id'],
                'plan_id' => $ws_row['plan_id'],
                'fee_applied_for_product' => $ws_row['fee_applied_for_product'],
                'prd_plan_type_id' => $ws_row['prd_plan_type_id'],
                'order_id' => (isset($extra_params['order_id'])?$extra_params['order_id']:$ws_row['last_order_id']),
                'status' => "Success",
                'message' => (isset($extra_params['message'])?$extra_params['message']:'Removed Termination Date'),
                'authorize_id' => (isset($extra_params['transaction_id'])?$extra_params['transaction_id']:''),
                'created_at' => 'msqlfunc_NOW()',
                'processed_at' => 'msqlfunc_NOW()',
            );
            $pdo->insert("website_subscriptions_history", $ws_history_data);

            $ce_upd_data = array('process_status' => 'Active');
            if(strtotime($ws_row['eligibility_date']) > strtotime(date('Y-m-d'))){
                $ce_upd_data['process_status'] = 'Pending';
            }
            $ce_upd_where = array(
                "clause" => "website_id=:website_id",
                "params" => array(":website_id" => $ws_row['id'])
            );
            $pdo->update("customer_enrollment", $ce_upd_data, $ce_upd_where);

            $cd_upd_data = array(
                "status" => $member_setting['dependent_status'],
                "terminationDate" => NULL,
                "updated_at" => "msqlfunc_NOW()"
            );
            $cd_upd_where = array(
                'clause' => "website_id=:website_id",
                'params' => array(":website_id" => $ws_row['id'])
            );
            $pdo->update("customer_dependent", $cd_upd_data, $cd_upd_where);


            $upd_cust_data = array('status' => $member_setting['member_status']);
            $upd_cust_where = array(
                "clause" => "id=:id",
                "params" => array(":id" => $ws_row['customer_id'])
            );
            $pdo->update("customer", $upd_cust_data, $upd_cust_where);

            $sponsor_billing_method = get_sponsor_billing_method();

            // Refund/Charge Adjustment for ListBill Member Code Start
            if ($sponsor_billing_method == "list_bill") {
                $ListBill->getSubscriptionRefundChargeCoverage($ws_row["id"]);
                //$ListBill->listBillMemberAdjustment("reinstate",$ws_row["id"],$ws_row["customer_id"],$ws_row["sponsor_id"]);
            }
            // Refund/Charge Adjustment for ListBill Member Code Ends

            if($location == "member_detail") {
                $portal = isset($extra_params['portal'])?$extra_params['portal']:'';
                $af_message = ' removed termination date';
                if($portal == "admin") {
                    $af_desc['ac_message'] =array(
                        'ac_red_1'=>array(
                            'href'=> 'admin_profile.php?id='.md5($_SESSION['admin']['id']),
                            'title'=> $_SESSION['admin']['display_id'],
                        ),
                        'ac_message_1' => $af_message.' on ',
                        'ac_red_2'=>array(
                            'href'=> 'members_details.php?id='.md5($ws_row["customer_id"]),
                            'title'=>$ws_row['rep_id'],
                        ),
                        'ac_message_2' =>' <br/> Policy : '.display_policy($ws_row['id']),
                    );
                    activity_feed(3, $_SESSION['admin']['id'], 'Admin',$ws_row["customer_id"], 'customer', 'Admin '. ucwords($af_message),'','',json_encode($af_desc));

                } elseif($portal == "agent") {
                    $af_desc['ac_message'] =array(
                        'ac_red_1'=>array(
                            'href' => 'agent_detail_v1.php?id='.md5($_SESSION['agents']['id']),
                            'title' => $_SESSION['agents']['rep_id'],
                        ),
                        'ac_message_1' => $af_message.' on ',
                        'ac_red_2'=>array(
                            'href'=> 'members_details.php?id='.md5($ws_row["customer_id"]),
                            'title'=>$ws_row['rep_id'],
                        ),
                        'ac_message_2' =>' <br/> Policy : '.display_policy($ws_row['id']),
                    );
                    activity_feed(3, $_SESSION['agents']['id'], 'Agent',$ws_row["customer_id"], 'customer', 'Agent '. ucwords($af_message),'','',json_encode($af_desc));
                } elseif($portal == "group") {
                    $af_desc = array();
                    $af_desc['ac_message'] =array(
                        'ac_red_1'=>array(
                            'href'=> 'groups_details.php?id='.md5($_SESSION['groups']['id']),
                            'title'=> $_SESSION['groups']['rep_id'],
                        ),
                        'ac_message_1' => $af_message.' on ',
                        'ac_red_2'=>array(
                            'href'=> 'members_details.php?id='.md5($ws_row['customer_id']),
                            'title'=>$ws_row['rep_id'],
                        ),
                        'ac_message_2' =>' <br/> Policy : '.display_policy($ws_row['id']),
                    );
                    activity_feed(3,$_SESSION['groups']['id'], 'Group',$ws_row['customer_id'], 'customer', 'Group '. ucwords($af_message),'','',json_encode($af_desc));
                }
            }
        }
    }

    public function setHeathyStepDefaultTerminationDate($customer_id)
    {
        global $pdo;
        $enrollDate = new enrollmentDate();
        $today_date = date("Y-m-d");

        $ws_sql = "SELECT ws.id,ws.status,ws.customer_id,ws.product_id,ws.plan_id,ws.fee_applied_for_product,ws.prd_plan_type_id,ws.last_order_id,p.product_type,p.is_member_benefits,p.is_fee_on_renewal,p.fee_renewal_type,p.fee_renewal_count,p.member_payment_type,ws.termination_date,ws.eligibility_date,od.order_id
                    FROM website_subscriptions ws 
                    JOIN prd_main p ON (p.id=ws.product_id AND p.product_type='Healthy Step') 
                    JOIN order_details od ON(od.website_id = ws.id AND od.is_deleted='N' AND od.is_refund='N' AND od.is_chargeback='N' AND od.is_payment_return='N')
                    JOIN orders o ON(o.id = od.order_id AND (o.status='Payment Approved' OR o.status='Pending Settlement'))
                    WHERE ws.customer_id=:customer_id
                    ORDER BY ws.id DESC";
        $ws_row = $pdo->selectOne($ws_sql,array(':customer_id' => $customer_id));
        if(!empty($ws_row)) {
            if($ws_row['is_member_benefits'] == "Y" && $ws_row['is_fee_on_renewal'] == "Y" && $ws_row['fee_renewal_type'] == "Renewals" && $ws_row['fee_renewal_count'] > 0) {

                $product_dates = $enrollDate->getCoveragePeriod($ws_row['eligibility_date'],$ws_row['member_payment_type']);
                $tmp_fee_renewal_count = $ws_row['fee_renewal_count'];
                $tmp_start_coverage_date = $product_dates['startCoveragePeriod'];
                $tmp_termination_date = $product_dates['endCoveragePeriod'];
                while ($tmp_fee_renewal_count > 0) {
                    $product_dates = $enrollDate->getCoveragePeriod($tmp_start_coverage_date,$ws_row['member_payment_type']);
                    $tmp_start_coverage_date = date("Y-m-d",strtotime('+1 day',strtotime($product_dates['endCoveragePeriod'])));
                    $tmp_termination_date = date("Y-m-d",strtotime($product_dates['endCoveragePeriod']));
                    $tmp_fee_renewal_count--;
                }
                if(strtotime($tmp_termination_date) != strtotime($ws_row['termination_date'])) {
                    $ws_upd_data = array();
                    $ws_upd_data['termination_date'] = $tmp_termination_date;
                    $ws_upd_data['term_date_set'] = date('Y-m-d');
                    $ws_upd_data['termination_reason'] = 'Policy Change';
                    $ws_upd_data['status'] = 'Active';

                    if(strtotime($tmp_termination_date) <= strtotime($today_date)) {
                        $ws_upd_data['status'] = 'Inactive';
                    }

                    $ws_upd_where = array(
                        "clause" => "id=:id",
                        "params" => array(":id" => $ws_row['id'])
                    );
                    $pdo->update("website_subscriptions", $ws_upd_data, $ws_upd_where);

                    $ws_history_data = array(
                        'customer_id' => $ws_row['customer_id'],
                        'website_id' => $ws_row['id'],
                        'product_id' => $ws_row['product_id'],
                        'plan_id' => $ws_row['plan_id'],
                        'fee_applied_for_product' => $ws_row['fee_applied_for_product'],
                        'prd_plan_type_id' => $ws_row['prd_plan_type_id'],
                        'order_id' => $ws_row['order_id'],
                        'status' => "Success",
                        'message' => (isset($extra_params['message'])?$extra_params['message']:'Removed Termination Date'),
                        'authorize_id' => '',
                        'created_at' => 'msqlfunc_NOW()',
                        'processed_at' => 'msqlfunc_NOW()',
                    );
                    $pdo->insert("website_subscriptions_history", $ws_history_data);

                    $ce_upd_data = array('process_status' => 'Active');
                    $ce_upd_where = array(
                        "clause" => "website_id=:website_id",
                        "params" => array(":website_id" => $ws_row['id'])
                    );
                    $pdo->update("customer_enrollment", $ce_upd_data, $ce_upd_where);
                }
            }
        }
    }

    public function getPolicyRenewCount($ws_id){
        global $pdo;
        $renewCountSql = "SELECT (MAX(od.renew_count) - 1) as wsRenewCount
            FROM order_details od 
            JOIN transactions t ON(t.order_id = od.order_id)    
            WHERE od.is_deleted='N' AND t.transaction_status = 'Payment Approved' 
            AND od.website_id=:website_id";
        $renewCountparams = array(":website_id" => $ws_id);
        $renewCountRes = $pdo->selectOne($renewCountSql,$renewCountparams);
        $wsRenewCount = $renewCountRes["wsRenewCount"] > 0 ? $renewCountRes["wsRenewCount"]  : 0;
        return $wsRenewCount;
    }
}
?>