<?php
ini_set('memory_limit', '-1');
ini_set('max_execution_time', '-1');
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once dirname(__DIR__) . "/includes/hrm_payment.class.php";
include_once dirname(__DIR__) . "/includes/function.class.php";

$function_list = new functionsList();
$hrmObj = new HRMPayment();
$res = array();
$res['status'] = 'fail';

$debug = !empty($_REQUEST["debug"]) ?  $_REQUEST["debug"] : false;;
$action = checkIsset($_REQUEST["action"]);
$pay_period = checkIsset($_REQUEST["pay_period"]);
$hrm_payment_duration = checkIsset($_REQUEST["hrm_payment_duration"]);
$groupIds = checkIsset($_REQUEST["groupIds"], 'arr');
$memberIds = checkIsset($_REQUEST['memberIds'], 'arr');
$nonCompliant = checkIsset($_REQUEST['nonCompliant']);
$pay_period = date("Y-m-d", strtotime($pay_period));
$payDates = checkIsset($_REQUEST['payDates'], 'arr');
$groupMemberId = array();
$memberIdGroupWise = array();
if (!empty($memberIds)) {
    $memberIds = implode(',', $memberIds);
    $payDatesIncr = !empty($payDates) ? " AND pay_date IN('".implode("','",$payDates)."')" : '';
    $memberArr = array();
    $selMember = "SELECT DISTINCT(payer_id) AS memberId,SUM(amount) AS totalAmount,group_id AS groupId FROM hrm_payment 
    WHERE is_deleted='N' AND payer_id IN(" . $memberIds . ") $payDatesIncr AND pay_period=:pay_period AND hrm_payment_duration = :hrm_payment_duration GROUP BY payer_id";
    $memberParam = array(
        ":pay_period" => $pay_period,
        ":hrm_payment_duration" => $hrm_payment_duration
    );
    $memberData = $pdo->select($selMember, $memberParam);
    foreach ($memberData as $key => $member) {
        $memberArr[$key] = $member;
        $memberIdGroupWise[$member['groupId']] = [];
        $memberIdGroupWise[$member['groupId']][] = $member['memberId'];
    }
    $groupMemberId = explode(',', $memberArr[0]['groupId']);
}
$groupIds = array_merge($groupIds, $groupMemberId);
$sch_params = array();
if($debug){
    echo "memberIdGroupWise";
    pre_print($memberIdGroupWise,false);
}
if (!empty($groupIds) && empty($action)) {
    foreach ($groupIds as $groupId) {
        $incr = '';
        if (!empty($pay_period)) {
            $incr .= " AND hrmp.pay_period=:payPeriod";
            $sch_params[":payPeriod"] = $pay_period;
        }

        if (!empty($groupId)) {
            $incr .= " AND hrmp.group_id=:group_id";
            $sch_params[":group_id"] = $groupId;
        }

        if (!empty($hrm_payment_duration)) {
            $incr .= " AND hrmp.hrm_payment_duration = :hrm_payment_duration";
            $sch_params[":hrm_payment_duration"] = $hrm_payment_duration;
        }
        if (!empty($memberIds) && in_array($groupId, $groupMemberId)) {
            $incr .= " AND hrmp.payer_id IN (" . $memberIds . ")";
        }

        
        if(!empty($nonCompliant) && $nonCompliant == 'Y'){
            // $statusIncr = '';
            // if(!empty($nonCompliant) && $nonCompliant == 'Y'){
            //     $statusIncr = ' OR hrmp.status=:status ';
                // $sch_params[":status"] = 'nonCompliant';
            // }
            // $incr .= " AND (hrmp.status='Pending' $statusIncr)";
            $payDatesIncr = !empty($payDates) ? " AND gcp.paydate IN('".implode("','",$payDates)."')" : '';
            $memberIncr = !empty($memberIds) ? " AND c.id IN('".$memberIds."')" : '';
            $selHRMPayment = "SELECT c.rep_id,gcp.pay_period, hrmp.pay_date AS payDate,l.order_id as order_id,p.annual_hrm_payment,a.id AS sponsor_id,c.id AS customer_id,
                            od.prd_plan_type_id AS planType,od.website_id,od.product_id,od.plan_id,od.id AS order_detail_id,
                            IF(o.is_renewal='L',od.is_renewal,o.is_renewal) AS is_renewal,o.status AS orderstatus,p.is_gap_plus_product,p.type,l.id as list_bill_id, lbd.id as list_bill_detail_id
                            FROM hrm_payment hrmp
                                JOIN customer a ON(hrmp.group_id=a.id AND a.type='Group')
                                JOIN customer c ON(hrmp.payer_id = c.id AND c.type='Customer' AND c.is_compliant='Y')
                                JOIN list_bill_details lbd ON(lbd.ws_id=hrmp.website_id AND lbd.customer_id=hrmp.payer_id AND lbd.id=hrmp.list_bill_detail_id)
                                JOIN list_bills l ON(l.id=lbd.list_bill_id)
                                JOIN orders o ON(l.order_id=o.id)
                                JOIN order_details od ON (od.order_id=o.id AND od.is_deleted='N' AND od.website_id=hrmp.website_id)
                                JOIN customer_settings cs ON(cs.customer_id = c.id)
                                JOIN group_classes gc ON(gc.id=cs.class_id AND gc.is_deleted='N')
                                JOIN group_classes_paydates gcp ON(gcp.class_id = gc.id AND gcp.is_deleted='N')
                                JOIN prd_main p ON (p.id=od.product_id AND p.is_deleted='N' AND p.type!='Fees' AND p.is_gap_plus_product='Y')
                                WHERE hrmp.is_deleted='N' AND hrmp.status='nonCompliant' " . $incr . $payDatesIncr . $memberIncr ."
                                GROUP BY od.product_id,hrmp.payer_id";
            $resHRMPayment = $pdo->select($selHRMPayment, $sch_params);
            if($debug){
                pre_print($selHRMPayment,false);
                pre_print($sch_params,false);
                pre_print($resHRMPayment,false);
            }
            if(!empty($resHRMPayment)){
                $groupArrayByPayDate = array();
                foreach ($resHRMPayment as $order) {
					$weeklyPayPeriod = $hrmObj->getWeeklyPayPeriod($order['payDate']);
					$planType = $order['planType'];
					$selTransactions = "SELECT id,transaction_status FROM transactions WHERE order_id=:odrID AND transaction_status IN('Payment Approved','Pending Settlement') ORDER BY id DESC";
					$resTransactions = $pdo->selectOne($selTransactions, array(":odrID" => $order["order_id"]));
					$order["transaction_tbl_id"] = 0;
					if (!empty($resTransactions["id"])) {
						$order["transaction_tbl_id"] = $resTransactions["id"];
					}
					if (!empty($order['annual_hrm_payment'])) {
                        //create array by group Id and paydate wise
                        $groupArrayByPayDate[$order['sponsor_id']] = [];
                        if(!empty($resTransactions["id"]) && $resTransactions['transaction_status'] == 'Payment Approved' && !in_array($order['payDate'],$groupArrayByPayDate[$order['sponsor_id']])){
                            $groupArrayByPayDate[$order['sponsor_id']][] = $order['payDate'];
                        }
                        if(!empty($resTransactions["id"]) && $resTransactions['transaction_status'] == 'Payment Approved'){
                            $memberIdGroupWise[$order['sponsor_id']][] = $order['customer_id'];
                        }
                        //create array by group Id and paydate wise

						$amount = json_decode($order['annual_hrm_payment']);
						$hrmAmount = $amount->$planType;
						if($order['pay_period'] == "Monthly"){
							$hrmAmount = round(($hrmAmount / 12),2);
						} else if($order['pay_period'] == "Semi-Monthly"){
							$hrmAmount = round(($hrmAmount / 24),2);
						} else if($order['pay_period'] == "Weekly"){
							$hrmAmount = round(($hrmAmount / 52),2);
						} else if($order['pay_period'] == "Bi-Weekly"){
							$hrmAmount = round(($hrmAmount / 26),2);
						}
						$groupId = $order['sponsor_id'];
						$payer_id = $order['customer_id'];

						$hrmSql = "SELECT id FROM hrm_payment WHERE group_id=:group_id AND pay_period=:pay_period AND website_id=:website_id AND payer_id=:payer_id AND is_deleted='N' AND list_bill_detail_id=:list_bill_detail_id";
						$params = array(":group_id" => $groupId, ":pay_period" => $weeklyPayPeriod, ":website_id" => $order['website_id'],":payer_id"=>$payer_id,":list_bill_detail_id"=>$order['list_bill_detail_id']);
						$hrmRes = $pdo->selectOne($hrmSql, $params);
                        $insHrmSql = array(
                            "group_id" => $groupId,
                            "website_id" => $order['website_id'],
                            "product_id" => $order['product_id'],
                            "plan_id" => $order['plan_id'],
                            "prd_plan_type_id" => $order['planType'],
                            "payer_id" => $payer_id,
                            "payer_type" => "Customer",
                            "hrm_unit_price" => $hrmAmount,
                            "amount" => $hrmAmount,
                            "order_id" => $order['order_id'],
                            "order_detail_id" => $order['order_detail_id'],
                            "list_bill_id" => $order['list_bill_id'],
							"list_bill_detail_id" => $order['list_bill_detail_id'],
                            "transaction_id" => $order['transaction_tbl_id'],
                            "status" => ($order['orderstatus'] == 'Payment Approved' ? 'Completed' : 'Pending'),
                            "pay_date" => $order['payDate'],
                            "created_at" => "msqlfunc_NOW()",
                        );

                        $insHrmSql["sub_type"] = $order['is_renewal'] == "Y" ? 'Renewals' : 'New';
                        $insHrmSql["balance_type"] = "addCredit";
                        $insHrmSql["pay_period"] = $weeklyPayPeriod;
                        $insHrmSql["hrm_payment_duration"] = "weekly";

						if(!empty($hrmRes)){
                            $weeklyHRMPaymentId = $hrmRes['id'];
                            $updParams = array(
                                "order_id" => $order["order_id"],
                                "order_detail_id" => $order['order_detail_id'],
                                "transaction_id" => $order["transaction_tbl_id"],
                                "status" => ($order['orderstatus'] == 'Payment Approved' ? 'Completed' : "Pending"),
                                "updated_at" => "msqlfunc_NOW()",
                            );
                            $updWhere = array(
                                'clause' => "id=:id",
                                'params' => array(
                                    ':id' => $weeklyHRMPaymentId,
                                )
                            );
                            $pdo->update("hrm_payment", $updParams, $updWhere);
						}else{
                            $weeklyHRMPaymentId = $pdo->insert("hrm_payment", $insHrmSql);
                            $hrmObj->memberHRMPayment("addCredit", "weekly", $groupId, $payer_id, $weeklyPayPeriod, $hrmAmount, $weeklyHRMPaymentId,$order['payDate'],$order['orderstatus']);
                        }
                        // Activity Feed Code Start
                            $description['ac_message'] = array(
                                'ac_red_1' => array(
                                    'href' => $ADMIN_HOST . '/admin_profile.php?id=' . md5($_SESSION['admin']['id']),
                                    'title' => $_SESSION['admin']['display_id'],
                                ),
                                'ac_message_1' => ' move member ',
                                'ac_red_2' => array(
                                    'href' => $ADMIN_HOST . '/members_details.php?id=' . md5($payer_id),
                                    'title' => $order['rep_id'],
                                ),
                                'ac_message_2' => ' from Non Complaint to '.($order['orderstatus'] == 'Payment Approved' ? 'Completed' : "Pending"),
                            );
                            activity_feed(3, $_SESSION['admin']['id'], 'Admin', $payer_id, 'Customer', "Non Complaint to ".($order['orderstatus'] == 'Payment Approved' ? 'Completed' : "Pending")." HRM Payments", $_SESSION['admin']['fname'], $_SESSION['admin']['lname'], json_encode($description));
                        // Activity Feed Code Ends
					}
				}
                if(!empty($groupArrayByPayDate)){
                    //generate NACHA file group id and paydate wise
                    if($debug){
                        pre_print($groupArrayByPayDate,false);
                        pre_print($memberIdGroupWise,false);
                    }
                    foreach($groupArrayByPayDate as $groupId => $payDateArr){
                        if(!empty($payDateArr)){
                            $payDateArr = array_unique($payDateArr);
                            foreach($payDateArr as $payDate){
                                $memberIds = !empty($memberIdGroupWise[$groupId]) ? implode(',',$memberIdGroupWise[$groupId]) : '';
                                $function_list->generateNachaFile($groupId,$payDate,'weekly',$memberIds,$debug);
                            }
                        }
                    }
                }
            }
        }
        $res['status'] = 'success';
    }

    if ($res['status'] == 'success') {
        setNotifySuccess('Non-compliant HRM Payment: Successful');
        $response['status'] = 'success';
        $response['message'] = "HRM Payments applied successfully";
    } else {
        setNotifyError('Non-compliant HRM Payment: Failed');
        $response['status'] = 'fail';
        $response['message'] = "HRM Payments applied failed";
    }
} else if (!empty($pay_period) && !empty($action) && !empty($groupIds)) {
    foreach ($groupIds as $groupId) {
        $incr = '';
        if (!empty($memberIds) && in_array($groupId, $groupMemberId)) {
            $incr .= " AND hrmp.payer_id IN (" . $memberIds . ")";
        }
        $selHRMPayment = "SELECT hrmp.group_id AS groupId,GROUP_CONCAT(DISTINCT(hrmp.payer_id)) AS payerId,
                    hrmp.pay_period,a.rep_id AS groupDispId,GROUP_CONCAT(DISTINCT(hrmp.credit_balance_id)) AS creditRowIds
                    FROM hrm_payment hrmp 
                    LEFT JOIN customer a ON(hrmp.group_id=a.id)
                    WHERE hrmp.is_deleted='N' AND hrmp.pay_period=:pay_period AND hrmp.status = 'Completed' 
                    AND hrmp.hrm_payment_duration=:hrm_payment_duration AND hrmp.is_deleted='N'
                    AND hrmp.group_id = :group_id " . $incr . " GROUP BY hrmp.group_id";
        $hrmPaymentParams = array(":pay_period" => $pay_period, ":hrm_payment_duration" => $hrm_payment_duration, ":group_id" => $groupId);
        $resHRMPayment = $pdo->select($selHRMPayment, $hrmPaymentParams);
        if (!empty($resHRMPayment)) {
            foreach ($resHRMPayment as $key => $row) {
                $selCredit = "SELECT GROUP_CONCAT(hrmpb.id) AS hrmCreditId,SUM(hrmpb.credit) AS hrmCredit,SUM(hrmpb.paid_to_group) AS groupAmount,hrmpb.group_id,hrmpb.status FROM hrm_payment_credit_balance hrmpb WHERE hrmpb.is_deleted='N' AND hrmpb.id IN(" . $row['creditRowIds'] . ")";
                $resCredit = $pdo->select($selCredit);
                if (!empty($resCredit)) {
                    foreach ($resCredit as $payRow) {
                        if (abs($payRow["groupAmount"]) > 0) {
                            $message = "Reversed HRM Payment For " . ucfirst($hrm_payment_duration) . " HRM Payments";
                            $hrmObj->memberHRMPayment("revCredit", $hrm_payment_duration, $row["groupId"], $row['payerId'], $pay_period, $payRow["groupAmount"], 0, $message, array("transaction_type" => "Reversed_HRM_Payment"));
                        }
                    }
                }
                // Update HRM Payment code start
                $updParams = array("status" => "Pending", "credit_balance_id" => 0, 'paid_at' => 'NULL');
                $updWhere = array(
                    'clause' => "pay_period = :pay_period AND status = 'Completed'
                    AND hrm_payment_duration=:hrm_payment_duration AND is_deleted='N'
                    AND group_id = :groupId AND credit_balance_id IN(" . $row['creditRowIds'] . ") AND payer_id IN (" . $row['payerId'] . ")",
                    'params' => array(
                        ':pay_period' => makeSafe($pay_period),
                        ':groupId' => makeSafe($row["groupId"]),
                        ':hrm_payment_duration' => $hrm_payment_duration
                    )
                );
                $pdo->update("hrm_payment", $updParams, $updWhere);
                // Update HRM Payment code ends

                // Activity Feed Code Start

                $description['ac_message'] = array(
                    'ac_red_1' => array(
                        'href' => $ADMIN_HOST . '/admin_profile.php?id=' . md5($_SESSION['admin']['id']),
                        'title' => $_SESSION['admin']['display_id'],
                    ),
                    'ac_message_1' => ' reversed completed hrm payment in ' . getCustomDate($row['pay_period']) . ' for ',
                    'ac_red_2' => array(
                        'href' => $ADMIN_HOST . '/groups_details.php.php?id=' . md5($row['groupId']),
                        'title' => $row['groupDispId'],
                    ),
                );
                activity_feed(3, $_SESSION['admin']['id'], 'Admin', $row['groupId'], 'Group', "Reversed Completed HRM Payments", $_SESSION['admin']['fname'], $_SESSION['admin']['lname'], json_encode($description));

                // Activity Feed Code Ends
            }
            $res['status'] = 'success';
        }
    }
    if ($res['status'] == 'success') {
        setNotifySuccess('Non-compliant HRM Payment: Successful');
        $response['status'] = 'success';
        $response['message'] = "HRM Payments reversed successfully";
    } else {
        setNotifyError('Non-compliant HRM Payment: Failed');
        $response['status'] = 'fail';
        $response['message'] = "HRM Payments reversed failed";
    }
} else {
    $response['status'] = 'fail';
}
header('Content-Type: application/json');
echo json_encode($response);
dbConnectionClose();
exit;
