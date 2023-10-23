<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
require_once dirname(__DIR__) . '/includes/function.class.php';

if ($SITE_ENV == 'Live') {
    redirect('404.php');
}
$validate = new Validation();

$admin_id = $_SESSION['admin']['id'];
$validate->string(array('required' => true, 'field' => 'group_id', 'value' => $_POST['group_id']), array('required' => 'Group Id is required'));
$validate->string(array('required' => true, 'field' => 'paydate', 'value' => $_POST['paydate']), array('required' => 'Date is required'));

if ($validate->isValid()) {
    $function_list = new functionsList();
    $paydate = !empty($_POST['paydate']) ? date('Y-m-d',strtotime($_POST['paydate'])) : '';
    $group_id = !empty($_POST['group_id']) ? $_POST['group_id'] : '';
    $debug = !empty($_POST['debug']) ? 1 : false;;
    $currentDate = $paydate;

    $publicHolidaysArr = fetch_public_holidays($currentDate);
    if ((date('l', strtotime($currentDate)) == 'Saturday' || date('l', strtotime($currentDate)) == 'Sunday' || in_array($currentDate, $publicHolidaysArr))) {
        if(!empty($group_id)){
            setNotifyError("Invalid date weekend or holiday is selected");
            $response['status'] = "success";
            header('Content-Type: application/json');
            echo json_encode($response);
            dbConnectionClose();
        }
        exit;
    } else {
        $payDateArr = $function_list->getPayDateForHrmPayment($currentDate,$group_id);
        if(empty($payDateArr)){
            setNotifyError("Invalid Paydate or Paydate not found");
            $response['status'] = "success";
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
    }
    $sch_params = array(":rep_id"=>$group_id);

    $payDateSql = "SELECT c.id AS customerId,gc.pay_period,cs.class_id,gcp.paydate AS class_paydate,cgs.billing_type,s.id AS sponsorId,lb.id AS listBillId
                    FROM list_bills lb
                    JOIN list_bill_details lbd ON(lbd.list_bill_id=lb.id)
                    JOIN customer_settings cs ON(lbd.customer_id = cs.customer_id)
                    JOIN customer c ON(c.id=lbd.customer_id AND c.is_deleted='N' AND c.type='Customer')
                    JOIN customer s ON(s.id = c.sponsor_id AND s.is_deleted='N' AND s.type='Group')
                    JOIN group_classes gc ON(gc.is_deleted='N' AND gc.group_id=s.id AND gc.id=cs.class_id)
                    LEFT JOIN group_classes_paydates gcp ON(gcp.is_deleted='N' AND gcp.group_id=s.id AND gcp.class_id = gc.id)
                    LEFT JOIN customer_group_settings cgs ON (cgs.customer_id = s.id)
                    WHERE c.is_deleted='N' AND c.type='Customer' AND gcp.paydate IN("."'".implode("','", $payDateArr)."'".") AND s.rep_id=:rep_id GROUP BY lb.id,IF(cgs.billing_type='individual',c.id,s.id)";
    $payDateRes = $pdo->select($payDateSql,$sch_params);
    $hrmGeneratedForPayDate = [];

    if($debug){
        pre_print($payDateSql,false);
        pre_print($sch_params,false);
        pre_print($payDateRes,false);
    }

    if (!empty($payDateRes)) {
        foreach ($payDateRes as $data) {
            if ($data['billing_type'] == 'list_bill' && $data['listBillId'] != 0) {
                $listBillSql = "SELECT lb.id AS listBillId,lb.status
                                FROM list_bills lb
                                WHERE lb.customer_id = :customer_id AND lb.status IN ('open','paid') AND lb.is_deleted='N'";
                $listBillRes = $pdo->select($listBillSql,array(":customer_id"=>$data['sponsorId']));

                if (!empty($listBillRes)) {
                    if($debug){
                        pre_print($listBillRes,false);
                    }
                    foreach ($listBillRes as $listBill) {
                        if ($listBill['status'] == 'open') {
                            $function_list->add_hrm_payments($listBill['listBillId'], $listBill['status'], $payDateArr);
                        } else if ($listBill['status'] == 'paid') {
                            $orderSql = "SELECT o.id AS orderId,o.status FROM orders o WHERE o.customer_id = :customer_id GROUP BY o.id";
                            $orderRes = $pdo->select($orderSql,array(":customer_id"=>$data['sponsorId']));
                            if (!empty($orderRes)) {
                                if($debug){
                                    pre_print($orderRes,false);
                                }
                                foreach ($orderRes as $order) {
                                    // if(empty($hrmGeneratedForPayDate[$data['sponsorId']])){
                                        $hrmGeneratedForPayDate[$data['sponsorId']][] = $function_list->add_hrm_payments($order['orderId'], $order['status'], $payDateArr);
                                    // }else{
                                    //     $function_list->add_hrm_payments($order['orderId'], $order['status'], $payDate);
                                    // }
                                }
                            }
                        }
                    }
                }
            } else if ($data['billing_type'] == 'individual') {
                $orderSql = "SELECT o.status,o.id AS orderId,o.is_list_bill_order FROM orders o WHERE o.customer_id = :customer_id AND o.status= 'Payment Approved'";
                $orderRes = $pdo->select($orderSql,array(":customer_id"=>$data['customerId']));
                if (!empty($orderRes)) {
                    foreach ($orderRes as $order) {
                        $function_list->add_hrm_payments($order['orderId'], $order['status'], $payDateArr);
                    }
                }
            }
        }

        //Generate NACHA File Start
        if(!empty($hrmGeneratedForPayDate)){
            if($debug){
                pre_print($hrmGeneratedForPayDate,false);
            }
            foreach($hrmGeneratedForPayDate as $groupId => $payDateArr){
                if(!empty($payDateArr)){
                    foreach($payDateArr as $payDates){
                        $payDates = array_unique($payDates);
                        if(!empty($payDates)){
                            foreach($payDates as $payDate1){
                                $function_list->generateNachaFile($groupId,$payDate1,'weekly', '',$debug);
                            }
                        }
                    }
                }
            }
        }
        //Generate NACHA File End
        setNotifySuccess("Test HRM Generated");
        $response['status'] = "success";
    }else{
        setNotifyError("Records not found.");
        $response['status'] = "success";
    }
} else {
    $response['status'] = "fail";
    $response['errors'] = $validate->getErrors();
}
header('Content-Type: application/json');
echo json_encode($response);
dbConnectionClose();
exit;
