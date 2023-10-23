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

$approved_status = array("settledSuccessfully","complete");
$wait_status = array("pendingSettlement","capturedPendingSettlement", "pendingsettlement");
$failed_status = array("settlementError","declined","expired","generalError","failedReview","canceled","verifying","failed");
$ord_sql="SELECT o.id,o.display_id,o.subscription_ids,o.customer_id,o.transaction_id,o.payment_master_id,o.is_renewal,o.new_business_total,o.renewal_total,o.new_business_members,o.renewal_members,o.status,c.type as user_type,c.id as user_id,o.created_at,t.created_at as settle_date,t.id as t_id,c.rep_id,c.email,o.is_list_bill_order
            FROM orders o 
            JOIN customer c ON(c.id = o.customer_id) 
            JOIN transactions t ON (t.order_id=o.id AND t.transaction_id = o.transaction_id)
            WHERE 
            ((o.status IN ('Payment Approved') AND t.transaction_status IN ('Payment Approved')) OR (o.status IN ('Refund') AND t.transaction_status IN ('Refund'))) AND 
            o.transaction_id > 0  AND 
            t.created_at >= (NOW() - INTERVAL 90 DAY)
            ORDER BY o.id ASC";
$ord_res = $pdo->select($ord_sql);
if (!empty($ord_res)) {
    foreach ($ord_res as $ord_row) {
        $oldOrderStatus = $ord_row['status'];
        $order_id = $ord_row['id'];
        $t_id=$ord_row['t_id'];
        $transaction_date=$ord_row['settle_date'];
        $order_display_id = $ord_row['display_id'];
        $transaction_id = $ord_row['transaction_id'];
        $payment_master_id = $ord_row['payment_master_id'];

        $new_business_total = $ord_row['new_business_total'];
        $renewal_total = $ord_row['renewal_total'];
        $new_business_members = $ord_row['new_business_members'];
        $renewal_members = $ord_row['renewal_members'];

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

        } else if (in_array($api_status, $wait_status)) {

        } else if (in_array($api_status, $failed_status)) {
            if($oldOrderStatus == "Refund"){
                if($api_status != 'failed'){
                    $trigger_id = 12;
                    $params = array(
                        'OrderID' => $order_display_id,
                    );
                    $sendTo = array($ord_row['email']);
                    trigger_mail($trigger_id,$params,$sendTo);
                }
            } else {
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
                    "req_url" => "cron_scripts/ach_check_late_reurns.php",
                    'transaction_response' => $transaction_res,
                    "reason" => $reason,
                );

                if ($ord_row['is_renewal'] == "L") {
                    $trans_params = array(
                        "transaction_id" => $transaction_id,
                        "req_url" => "cron_scripts/ach_check_late_reurns.php",
                        'transaction_response' => $transaction_res,
                        'new_business_total' => $new_business_total,
                        'renewal_total' => $renewal_total,
                        'new_business_members' => $new_business_members,
                        'renewal_members' => $renewal_members,
                        "reason" => $reason,
                    );
                }
                $transactionInsId = $function_list->transaction_insert($order_id, 'Debit', 'Payment Returned', 'Transaction Returned', 0, $trans_params);

                $payable_params=array(
                    'payable_type'=>'Reverse_Vendor',
                    'type'=>'Vendor',
                    'transaction_tbl_id' => $transactionInsId['id'],
                );
                $function_list->payable_insert($order_id,0,0,0,$payable_params);

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
                                $extra_params['location'] = "ach_check_late_returns";
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
        }
        $insParams = array(
            'order_id' => $order_id,
            'transaction_id' => $t_id,
            'api_status' => $api_status,
            'response' => json_encode($transaction_res),
            'api_date' => "msqlfunc_NOW()",
            'transaction_date' => $transaction_date,
            'req_url' => 'cron_scripts/ach_check_late_returns.php'
        );
        $pdo->insert("ach_api_response", $insParams);
    }
}
$maxdate_Sql = "SELECT MAX(api_date) AS delete_records FROM ach_api_response WHERE (NOW() - INTERVAL 6 MONTH) >= api_date;";
$maxdate_Res= $pdo->selectOne($maxdate_Sql);
if(!empty($maxdate_Res)){   
$delSql = "DELETE FROM ach_api_response WHERE api_date <=:records"; 
$pdo->delete($delSql, array(":records" => $maxdate_Res['delete_records']));
}
echo "Completed";
dbConnectionClose();
?>