<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once dirname(__DIR__) . '/includes/enrollment_dates.class.php';
include_once dirname(__DIR__) . '/includes/function.class.php';
$enrollDate = new enrollmentDate();
$functionClass = new functionsList();
$error = "";
/************************/
//WE ARE NOT USE THIS FILE
/************************/
$ws_id = isset($_REQUEST['ws_id']) ? $_REQUEST['ws_id'] : "";
$ws_row = $pdo->selectOne("SELECT * from website_subscriptions where md5(id)=:id",array(':id' => $ws_id));

$customer_id = $ws_row['customer_id'];
$product_id = $ws_row['product_id'];
$plan_id = $ws_row['plan_id'];

$customer_sql = "SELECT c.* FROM customer c WHERE c.id=:customer_id";
$customer_row = $pdo->selectOne($customer_sql, array(":customer_id" => $ws_row['customer_id']));
if(isset($_POST['is_submit']) && $_POST['is_submit'] == 'Y'){

    $termination_date = isset($_POST['termination_date']) ? $_POST['termination_date'] : "";
    $reason = isset($_POST['reason']) ? $_POST['reason'] : "";

    
    if(empty($termination_date)){
        $error = "Please select termination date";
        $response['error'] = $error;
        $response['status'] = 'fail';
    }else{
        if (!empty(strtotime($termination_date))) {

            if (strtotime(date('Y-m-d', strtotime($termination_date))) != strtotime(date('Y-m-d', strtotime($ws_row['termination_date'])))) {
                if($ws_row['status'] == "Post Payment" && strtotime($termination_date) == strtotime($ws_row['eligibility_date'])) {

                    $ord_sql = "SELECT o.* 
                                FROM orders o
                                JOIN order_details od ON(od.order_id = o.id AND od.is_deleted='N')
                                WHERE 
                                o.customer_id=:customer_id AND
                                o.status IN('Pending Payment','Post Payment','Payment Declined') AND
                                od.product_id=:product_id";
                    $ord_where = array(
                        ":customer_id" => $ws_row['customer_id'],
                        ":product_id" => $ws_row['product_id'],
                    );
                    $ord_row = $pdo->selectOne($ord_sql,$ord_where);
                    
                    $is_need_to_generate_new_ord = false;
                    if(!empty($ord_row['id'])) {
                        
                        $ord_prd_res = $pdo->select("SELECT od.* FROM order_details od WHERE od.order_id=:order_id AND od.is_deleted='N' AND od.is_refund='N'",array(":order_id" => $ord_row['id']));
                        foreach ($ord_prd_res as $key => $ord_prd_row) {
                            if($ord_prd_row['product_id'] != $ws_row['product_id']) {
                                $is_need_to_generate_new_ord = true;
                            }
                        }

                        if($is_need_to_generate_new_ord == true) {
                            $ord_data = $ord_row;
                            unset($ord_data['id']);

                            $tmp_subscription_ids = explode(',', $ord_data['subscription_ids']);
                            foreach ($tmp_subscription_ids as $sub_id_key => $tmp_subscription_id) {
                                if($ws_row['id'] == $tmp_subscription_id) {
                                    unset($tmp_subscription_ids[$sub_id_key]);
                                }
                            }

                            $new_ord_product_total = 0.0;
                            $new_ord_prd_ids = array();
                            $new_ord_detail_res = array();
                            foreach ($ord_prd_res as $key => $ord_prd_row) {
                                if($ord_prd_row['product_id'] != $ws_row['product_id']) {

                                    $new_ord_prd_ids[] = $ord_prd_row['product_id'];
                                    $new_ord_detail_res[] = $ord_prd_row;
                                    $new_ord_product_total += ($ord_prd_row['unit_price'] - $ord_prd_row['total_discount']);
                                }
                            }

                            $ord_data['display_id'] = $functionClass->get_order_id();
                            $ord_data['subscription_ids'] = implode(',',$tmp_subscription_ids);
                            $ord_data['product_total'] = $new_ord_product_total;
                            $ord_data['service_fee'] = $service_fee;
                            $ord_data['service_fee_rule_log_id'] = isset($advance_commission_log_id) ? $advance_commission_log_id : 0;
                            $ord_data['sub_total'] = ($new_ord_product_total + $service_fee);
                            $ord_data['grand_total'] = ($new_ord_product_total + $service_fee);
                            $ord_data['updated_at'] = 'msqlfunc_NOW()';
                            $ord_data['created_at'] = 'msqlfunc_NOW()';
                            $new_ord_id = $pdo->insert('orders',$ord_data);

                            foreach ($new_ord_detail_res as $key => $new_ord_detail_row) {
                                unset($new_ord_detail_row['id']);
                                $new_ord_detail_row['order_id'] = $new_ord_id;
                                $new_ord_detail_row['updated_at'] = 'msqlfunc_NOW()';
                                $pdo->insert('order_details',$new_ord_detail_row);
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

                        //Cancel old order
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
                        $transParams = array("reason" => checkIsset("Order Cancelled When Set Termination date"));
                        $functionClass->transaction_insert($ord_row['id'],'Debit','Cancelled','Transaction Cancelled',0,$transParams);
                    }
                }

                $upd_term_date_data = array(
                    'process_status' => "Pending",
                );

                $upd_term_date_where = array(
                    "clause" => "website_id=:subscription_id",
                    "params" => array(":subscription_id" => $ws_row['id'])
                );

                $upd_ws_term_date_data = array(
                    'termination_date' => date('Y-m-d', strtotime($termination_date)),
                    "term_date_set" => date('Y-m-d'),
                    'termination_reason' => $reason,
                    "updated_at" => "msqlfunc_NOW()",
                );

                $upd_ws_term_date_where = array(
                    "clause" => "id=:subscription_id",
                    "params" => array(":subscription_id" => $ws_row['id'])
                );

                /* term dependent start */
                $term_cd_data = array(
                    "terminationDate" => date('Y-m-d', strtotime($termination_date)),
                    "updated_at" => "msqlfunc_NOW()"
                );
                $term_cd_where = array(
                    'clause' => "product_id=:product_id and customer_id=:customer_id",
                    'params' => array(':customer_id' => $customer_id, ":product_id" => $ws_row['product_id'])
                );

                if((strtotime(date('Y-m-d', strtotime($termination_date))) <= strtotime(date('Y-m-d'))) || strtotime(date("Y-m-d",strtotime($termination_date))) == strtotime($ws_row['eligibility_date'])){
                    
                    $upd_term_date_data['process_status'] = 'Active';
                    $term_cd_data["status"]="Termed";

                    $update_ws_data = array(
                        'status' => 'Inactive Member Request',
                        'termination_reason' => $reason,
                        'updated_at' => 'msqlfunc_NOW()',
                    );
                    $update_ws_where = array("clause" => 'id=:id', 'params' => array(':id' => $ws_row['id']));
                    $pdo->update("website_subscriptions", $update_ws_data, $update_ws_where);

                    $insert_s_his_prm = array(
                        'customer_id' => $ws_row['customer_id'],
                        'website_id' => $ws_row['id'],
                        'product_id' => $ws_row['product_id'],
                        'plan_id' => $ws_row['plan_id'],
                        'order_id' => $ws_row['last_order_id'],
                        'status' => 'Cancel',
                        'message' => 'Subscription Plan Cancelled',
                        'admin_id' => $_SESSION['admin']['id'],
                        'created_at' => 'msqlfunc_NOW()',
                    );
                    $pdo->insert("website_subscriptions_history", $insert_s_his_prm);

                    $spon_sql = "SELECT * FROM customer WHERE id=:customer_id";
                    $where_s_id = array(':customer_id' => $ws_row['customer_id']);
                    $spon_res = $pdo->selectOne($spon_sql, $where_s_id);

                    

                    // activity_feed(3, $spon_res['sponsor_id'], $spon_res['type'], $customer_id, 'customer', 'Member has cancelled plan',json_encode($af_data));

                    /*$activity_feed_data = array(
                        'termination_date' => date('m/d/Y', strtotime($termination_date)),
                        'product_id' => $ws_row['product_id'],
                        'customer_id' => $ws_row['customer_id'],
                        'dependent_id' => 0,
                        'admin_id' => $_SESSION['admin']['id']
                    );*/
                    //activity_feed(3, $customer_id, 'Customer', $customer_id, 'customer', 'Term Date Set', '', '', json_encode($activity_feed_data));
                    // send_policy_cancellation_mail_to_sponsor($ws_row['customer_id']);
                }

                $pdo->update("customer_enrollment", $upd_term_date_data, $upd_term_date_where);
                $pdo->update("website_subscriptions", $upd_ws_term_date_data, $upd_ws_term_date_where);
                $pdo->update("customer_dependent", $term_cd_data, $term_cd_where);

                $insert_s_his_prm = array(
                        'customer_id' => $ws_row['customer_id'],
                        'website_id' => $ws_row['id'],
                        'product_id' => $ws_row['product_id'],
                        'plan_id' => $ws_row['plan_id'],
                        'order_id' => $ws_row['last_order_id'],
                        'status' => 'Cancel',
                        'message' => $reason,
                        'admin_id' => $_SESSION['admin']['id'],
                        'created_at' => 'msqlfunc_NOW()',
                    );
                $pdo->insert("website_subscriptions_history", $insert_s_his_prm);

                if (!empty(strtotime($ws_row['termination_date']))) {

                    /*$activity_feed_data = array(
                        'old_termination_date' => date('m/d/Y', strtotime($ws_row['termination_date'])),
                        'new_termination_date' => date('m/d/Y', strtotime($termination_date)),
                        'product_id' => $ws_row['product_id'],
                        'customer_id' => $ws_row['customer_id'],
                        'dependent_id' => 0,
                        'admin_id' => $_SESSION['admin']['id']
                    );
                    activity_feed(3, $customer_id, 'Customer', $customer_id, 'customer', 'Term Date Changed', '', '', json_encode($activity_feed_data));*/

                    $af_message = 'changed termination date';
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
                        'ac_message_2' =>' <br/> Policy : '.display_policy($ws_row['id']).' <br/> Termination date changed from : '.displayDate($ws_row['termination_date']).' to : '.displayDate($termination_date).' <br/>Termination Reason : '. $reason,
                    );
                    activity_feed(3, $_SESSION['admin']['id'], 'Admin',$customer_row['id'], 'customer', 'Admin '. ucwords($af_message),'','',json_encode($af_desc));


                } else {
                    /*$activity_feed_data = array(
                        'termination_date' => date('m/d/Y', strtotime($termination_date)),
                        'product_id' => $ws_row['product_id'],
                        'customer_id' => $ws_row['customer_id'],
                        'dependent_id' => 0,
                        'admin_id' => $_SESSION['admin']['id']
                    );
                    activity_feed(3, $customer_id, 'Customer', $customer_id, 'customer', 'Term Date Set', '', '', json_encode($activity_feed_data));*/

                    $af_message = 'set termination date';

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
                        'ac_message_2' =>' <br/> Policy : '.display_policy($ws_row['id']).' <br/> Termination date : '.displayDate($termination_date).' <br/>Termination Reason : '. $reason,
                    );
                    activity_feed(3, $_SESSION['admin']['id'], 'Admin',$customer_row['id'], 'customer', 'Admin '. ucwords($af_message),'','',json_encode($af_desc));
                }
            }
        
        }
        
        $response['status'] = 'success';
        $response['termination_date'] = date('m/d/Y',strtotime($termination_date));
        $response['message'] ='The termination date has been set.';

    }
    echo json_encode($response);
    exit();

}
$reasons = get_policy_termination_reasons();
/************************/
//WE ARE NOT USE THIS FILE
/************************/
$template = 'add_term_date.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>
