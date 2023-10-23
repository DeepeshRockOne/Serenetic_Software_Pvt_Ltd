<?php
include_once dirname(__DIR__) . "/cron_scripts/connect.php";
require_once dirname(__DIR__) . '/includes/function.class.php';
require_once dirname(__DIR__) . '/includes/cyberx_payment_class.php';
include_once dirname(__DIR__) . "/includes/commission.class.php";
require_once dirname(__DIR__) . '/includes/enrollment_dates.class.php';
require_once dirname(__DIR__) . '/includes/member_setting.class.php';
include_once dirname(__DIR__) . '/includes/policy_setting.class.php';
$policySetting = new policySetting();
$function_list = new functionsList();
$commObj = new Commission();
$enrollDate = new enrollmentDate();
$memberSetting = new memberSetting();

/*---------- System script status code start -----------*/
$cronSql = "SELECT is_running,next_processed,last_processed FROM system_scripts WHERE script_code=:script_code";
$cronWhere = array(":script_code" => "ach_pending_settlement");
$cronRow = $pdo->selectOne($cronSql,$cronWhere);

if(!empty($cronRow)){
    $cronWhere = array(
                      "clause" => "script_code=:script_code", 
                      "params" => array(
                          ":script_code" => 'ach_pending_settlement'
                      )
                  );
    $pdo->update('system_scripts',array("is_running" => "Y","status"=>"Running","last_processed"=>"msqlfunc_NOW()"),$cronWhere);
}
/*---------- System script status code ends -----------*/

$approved_status = array("settledSuccessfully", "complete");
$wait_status = array("pendingSettlement", "capturedPendingSettlement", "pendingsettlement");
$failed_status = array("settlementError", "declined", "expired", "generalError", "failedReview", "canceled", "verifying", "failed");

$ord_sql = "SELECT o.id,o.display_id,o.subscription_ids,o.customer_id,o.transaction_id,o.payment_master_id,o.is_renewal,o.new_business_total,o.renewal_total,o.new_business_members,o.renewal_members,o.status,c.type as user_type,c.id as user_id
        FROM orders o 
        JOIN customer c ON(c.id = o.customer_id) 
        WHERE o.status IN ('Pending Settlement') ORDER BY o.id ASC";
$ord_res = $pdo->select($ord_sql);
if (!empty($ord_res)) {
    foreach ($ord_res as $ord_row) {
        $order_id = $ord_row['id'];
        $order_display_id = $ord_row['display_id'];
        $transaction_id = $ord_row['transaction_id'];
        $payment_master_id = $ord_row['payment_master_id'];

        $new_business_total = $ord_row['new_business_total'];
        $renewal_total = $ord_row['renewal_total'];
        $new_business_members = $ord_row['new_business_members'];
        $renewal_members = $ord_row['renewal_members'];

        $oldOrderStatus = "Pending Settlement";
        //********************* To Get Transaction Detail Code start ***************************
        if ($SITE_ENV != "Live") {
            $transaction_res['api_status'] = 'settledSuccessfully';
        } else {
            $api = new CyberxPaymentAPI();
            $cc_params = array();
            $cc_params['transaction_id'] = $transaction_id;
            $transaction_res = $api->getTransactionDetail($cc_params, $payment_master_id);

        }
        //********************* To Get Transaction Detail Code end ***************************

        $api_status = !empty($transaction_res['api_status']) ? $transaction_res['api_status'] : '';
        $activityFeedDesc = array();

        if (in_array($api_status, $approved_status)) {
            $orderParam = array(
                'status' => 'Payment Approved',
                'updated_at' => 'msqlfunc_NOW()',
            );
            $orderWhere = array(
                'clause' => 'id = :id',
                'params' => array(
                    ':id' => $order_id
                )
            );
            $pdo->update('orders', $orderParam, $orderWhere);

            $newOrderStatus = "Payment Approved";
            $oldOdr = array("status" => $oldOrderStatus);
            $newOdr = array("status" => $newOrderStatus);
            $odrCheckDiff = array_diff_assoc($oldOdr, $newOdr);
            if (!empty($odrCheckDiff)) {
                foreach ($odrCheckDiff as $key1 => $value1) {
                    $activityFeedDesc['key_value']['desc_arr'][$key1] = 'From ' . (!empty($oldOdr[$key1]) ? $oldOdr[$key1] : 'blank') . ' To ' . $newOdr[$key1] . " on Order " . checkIsset($ord_row["display_id"]);
                }
            }
            audit_log(array(), $ord_row['customer_id'], "customer", 'Order Status Changed from ' . $oldOrderStatus . ' to ' . $newOrderStatus, $ord_row['id']);

            if ($ord_row['is_renewal'] == "L") {
                $trans_params = array(
                    "transaction_id" => $transaction_id,
                    "req_url" => "cron_scripts/ach_pending_settlement_transactions.php",
                    'transaction_response' => $transaction_res,
                    'new_business_total' => $new_business_total,
                    'renewal_total' => $renewal_total,
                    'new_business_members' => $new_business_members,
                    'renewal_members' => $renewal_members,
                    'not_generate_commission' => true,
                );
                $transactionInsId = $function_list->transaction_insert($order_id, 'Credit', 'List Bill Order', 'Transaction Approved', 0, $trans_params);
            } else {
                $other_params = array("transaction_id" => $transaction_id, "req_url" => "cron_scripts/ach_pending_settlement_transactions.php", 'transaction_response' => $transaction_res, 'not_generate_commission' => true);

                if ($ord_row['is_renewal'] == 'Y') {
                    $transactionInsId = $function_list->transaction_insert($order_id, 'Credit', 'Renewal Order', 'Transaction Approved', 0, $other_params);
                } else {
                    $transactionInsId = $function_list->transaction_insert($order_id, 'Credit', 'New Order', 'Transaction Approved', 0, $other_params);
                    
                    // generate joinder agreement when order is approved
                    $function_list->checkJoinderAgreement($order_id);
                }
            }

            //make total attempts 0 code start
            $sqlWeb = "SELECT w.id FROM website_subscriptions w 
                        JOIN orders o ON FIND_IN_SET(w.id, o.subscription_ids)
                        WHERE o.id=:id";
            $resWeb = $pdo->select($sqlWeb, array(":id" => $order_id));
            if (!empty($resWeb)) {
                foreach ($resWeb as $key => $row) {
                    $updateArr = array(
                        'total_attempts' => 0,
                        'next_attempt_at' => NULL,
                        'updated_at' => 'msqlfunc_NOW()',
                    );
                    $updateWhere = array("clause" => 'id=:id', 'params' => array(":id" => $row['id']));
                    $pdo->update("website_subscriptions", $updateArr, $updateWhere);
                }
            }
            //make total attempts 0 code end       

            //********* Payable Insert Code Start ********************
            $payable_params=array(
                'payable_type'=>'Vendor',
                'type'=>'Vendor',
                'transaction_tbl_id' => $transactionInsId['id'],
            );
            $function_list->payable_insert($order_id,0,0,0,$payable_params);
            //********* Payable Insert Code End   ********************

            if (!empty($activityFeedDesc)) {
                $activityFeedDesc1 = array();
                $activityFeedDesc1 = array_merge($activityFeedDesc1, $activityFeedDesc);
                activity_feed(3, $ord_row['user_id'], $ord_row['user_type'], $ord_row['user_id'], $ord_row['user_type'], 'Order Status Changed', '', '', json_encode($activityFeedDesc1));
            }
        } else if (in_array($api_status, $wait_status)) {

        } else if (in_array($api_status, $failed_status)) {
            /*
            If an ACH payment is returned, all of those policies on that ACH order must be terminated to successfully paid coverage periods. 
            If all policies are then terminated and termination date is in past, then member status would be changed to Inactive.
            */
            $reason = '';
            if (isset($transaction_res['message'])) {
                $reason = $transaction_res['message'];
            }

            $update_params = array(
                'order_comments' => $reason,
                'status' => 'Payment Returned',
                'updated_at' => 'msqlfunc_NOW()',
            );
            $update_where = array(
                'clause' => 'id = :id',
                'params' => array(
                    ':id' => $order_id
                )
            );
            $pdo->update('orders', $update_params, $update_where);

            $newOrderStatus = "Payment Returned";
            $oldOdr = array("status" => $oldOrderStatus);
            $newOdr = array("status" => $newOrderStatus);
            $odrCheckDiff = array_diff_assoc($oldOdr, $newOdr);
            if (!empty($odrCheckDiff)) {
                foreach ($odrCheckDiff as $key1 => $value1) {
                    $activityFeedDesc['key_value']['desc_arr'][$key1] = 'From ' . (!empty($oldOdr[$key1]) ? $oldOdr[$key1] : 'blank') . ' To ' . $newOdr[$key1] . " on Order " . checkIsset($ord_row["display_id"]);
                }
            }
            audit_log(array(), $ord_row['customer_id'], "customer", 'Order Status Changed from ' . $oldOrderStatus . ' to ' . $newOrderStatus, $ord_row['id']);

            $trans_params = array(
                "transaction_id" => $transaction_id,
                "req_url" => "cron_scripts/ach_pending_settlement_transactions.php",
                'transaction_response' => $transaction_res,
                "reason" => $reason,
            );

            if ($ord_row['is_renewal'] == "L") {
                $trans_params = array(
                    "transaction_id" => $transaction_id,
                    "req_url" => "cron_scripts/ach_pending_settlement_transactions.php",
                    'transaction_response' => $transaction_res,
                    'new_business_total' => $new_business_total,
                    'renewal_total' => $renewal_total,
                    'new_business_members' => $new_business_members,
                    'renewal_members' => $renewal_members,
                    "reason" => $reason,
                );
            }
            $transactionInsId = $function_list->transaction_insert($order_id, 'Debit', 'Payment Returned', 'Transaction Returned', 0, $trans_params);

            $extraParams = array();
            $extraParams['note'] = "Commission reversed when Order status changed to Payment Returned";
            $extraParams["transaction_tbl_id"] = $transactionInsId['id'];
            $commObj->reverseOrderCommissions($order_id, $extraParams);

            if ($ord_row['is_renewal'] != "L") {
                $orderSubscriptionIds = $ord_row['subscription_ids'];
                if(!empty($orderSubscriptionIds)) {
                    $wsSql = "SELECT ws.id FROM website_subscriptions ws WHERE ws.id IN ($orderSubscriptionIds)";
                    $wsRes = $pdo->select($wsSql);
                    if (count($wsRes) > 0) {
                        foreach ($wsRes as $key => $wsRow) {
                            $termination_date = $enrollDate->getTerminationDate($wsRow['id']);

                            $extra_params = array();
                            $extra_params['location'] = "ach_pending_settlement_transactions";
                            $extra_params['message'] = "Subscription Plan Terminated (Payment Returned)";
                            $extra_params['activity_feed_flag'] = "change_order_status";
                            $termination_reason = "Payment Returned";
                            $policySetting->setTerminationDate($wsRow['id'],$termination_date,$termination_reason,$extra_params);
                        }
                    }
                }
            }
            
            if (!empty($activityFeedDesc)) {
                $activityFeedDesc1 = array();
                $activityFeedDesc1 = array_merge($activityFeedDesc1, $activityFeedDesc);
                activity_feed(3, $ord_row['user_id'], $ord_row['user_type'], $ord_row['user_id'], $ord_row['user_type'], 'Order Status Changed', '', '', json_encode($activityFeedDesc1));
            }
        }
        $insParams = array(
            'order_id' => $order_id,
            'transaction_id' => 0,
            'api_status' => $api_status,
            'response' => json_encode($transaction_res),
            'api_date' => "msqlfunc_NOW()",
            'transaction_date' => '',
            'req_url' => 'cron_scripts/ach_pending_settlement_transactions.php'
        );
        $pdo->insert("ach_api_response", $insParams);
    }
}

/*--------- System script status code start ----------*/
if(!empty($cronRow)){
    $cronSql = "SELECT last_processed FROM system_scripts WHERE script_code=:script_code";
    $cronWhere = array(":script_code" => "ach_pending_settlement");
    $cronRow = $pdo->selectOne($cronSql,$cronWhere);  
    $cronUpdParams = array("is_running" => "N","status"=>"Active","next_processed"=>date("Y-m-d H:i:s",strtotime("+1 day", strtotime($cronRow['last_processed']))));
    $cronWhere = array(
        "clause" => "script_code=:script_code", 
        "params" => array(
            ":script_code" => 'ach_pending_settlement'
        )
    );
    $pdo->update('system_scripts',$cronUpdParams,$cronWhere);
}
/*---------- System script status code ends -----------*/
echo "Completed";
dbConnectionClose();
?>