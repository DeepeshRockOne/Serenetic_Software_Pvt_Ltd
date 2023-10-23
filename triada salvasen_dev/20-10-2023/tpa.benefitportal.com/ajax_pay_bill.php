<?php
ini_set('memory_limit', '-1');
ini_set('max_execution_time',0);
include_once __DIR__ . '/includes/connect.php';
require_once __DIR__ . '/includes/list_bill.class.php';
//error_reporting(E_ALL);
$listBillObj = new ListBill();
$validate = new Validation();
$response = array();

$payment_master_id = 0;
$list_bill_id = isset($_REQUEST['list_bill_id'])?$_REQUEST['list_bill_id']:0;
$billing_profile = isset($_REQUEST['billing_profile'])?$_REQUEST['billing_profile']:'';
$location = isset($_REQUEST['location'])?$_REQUEST['location']:'admin';
$payment_date = isset($_REQUEST['payment_date'])?$_REQUEST['payment_date']:'';
$check_number = isset($_REQUEST['check_number'])?$_REQUEST['check_number']:'';
$sent_receipt = isset($_REQUEST['sent_receipt'])?'Y':'N';

$list_bill_sql = "SELECT lb.id
                  FROM list_bills lb
                  JOIN customer c ON(c.id=lb.customer_id)
                  WHERE lb.id=:list_bill_id";
$list_bill_where = array(':list_bill_id' => $list_bill_id);
$list_bill_row = $pdo->selectOne($list_bill_sql, $list_bill_where);

if(!empty($list_bill_row)) {
    $validate->string(array('required' => true, 'field' => 'billing_profile', 'value' => $billing_profile), array('required' => 'Please select Payment Method'));
    if($billing_profile == "new_billing") {
        $validate->setError('billing_profile','Please save billing profile');
    }
    if (!$validate->getError("billing_profile")) {

        if($billing_profile == "record_check_payment") {
            $validate->string(array('required' => true, 'field' => 'payment_date', 'value' => $payment_date), array('required' => 'Please select Payment Date'));
            $validate->string(array('required' => true, 'field' => 'check_number', 'value' => $check_number), array('required' => 'Check # is required.'));
            if(isset($_FILES['file']['name'])) {
                if (count($_FILES['file']['name']) > 5) {
                    $validate->setError('file_0', 'You can upload a maximum of 5 files');
                } else {
                    for ($i=0; $i < 5; $i++) {
                        if (!empty($_FILES['file']['name'][$i])) {
                            if (!$validate->getError('file_'.$i)) {
                                if ($_FILES['file']['size'][$i] > 5242880) { //5MB
                                    $validate->setError('file_'.$i, 'Maximum allowed 5MB for uploaded files');
                                }
                            }
                        }
                    }
                }
            }

        } else {
            $def_bill_sql = "SELECT id FROM customer_billing_profile WHERE id=:id";
            $def_bill_row = $pdo->selectOne($def_bill_sql,array(':id'=>$billing_profile));

            if(empty($def_bill_row)) {
                $validate->setError("billing_profile","Payment Method is not found");
            }
        }
    }
} else {
    $validate->setError('list_bill_id','Please select List Bill');
}
if ($validate->isValid()) {
    $other_params = array(
        'payment_date' => $payment_date,
        'check_number' => $check_number,
        'sent_receipt' => $sent_receipt,
    );
    $pay_lb_res = $listBillObj->pay_list_bill($list_bill_id,$billing_profile,$location,$other_params);

    if($pay_lb_res['status'] == "success") {
        $response['status'] = 'success';
        $response['message'] = $pay_lb_res['message'];
        setNotifySuccess($pay_lb_res['message']);
    } else {
        if($pay_lb_res['error_code'] == "payment_fail") {
            $response['status'] = 'fail';
            $response['errors']['billing_profile'] = $pay_lb_res['message'];
        } else {
            $response['status'] = 'fail';
            $response['errors']['list_bill_id'] = $pay_lb_res['message'];
        }
    }
} else {
    $errors = $validate->getErrors();
    $response['status'] = 'fail';
    $response['errors'] = $errors;
}
echo json_encode($response);
dbConnectionClose();
exit;
?>