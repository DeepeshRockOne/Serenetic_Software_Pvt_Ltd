<?php
include_once dirname(__DIR__) . "/cron_scripts/connect.php";
require_once dirname(__DIR__) . '/includes/function.class.php';
$function_list = new functionsList();
include_once dirname(__DIR__) . "/includes/hrm_payment.class.php";
$hrmObj = new HRMPayment();

$pay_date =  '2023-06-02';
$group_id =  '2876'; //G56118
$sch_params = array(':pay_date'=>$pay_date,':group_id'=>$group_id);
$hrmSql="SELECT * FROM hrm_payment WHERE group_id=:group_id AND pay_date=:pay_date ORDER BY id DESC";
$hrmRes = $pdo->select($hrmSql,$sch_params);

if(!empty($hrmRes)){
    foreach ($hrmRes as $key => $htmValue) { 
        $hrmPaymentOrdersql = "SELECT payer_id,order_detail_id,order_id,transaction_id FROM hrm_payment WHERE group_id=:group_id AND payer_id=:payer_id AND status='Completed' AND order_id > 0 AND transaction_id > 0 AND order_detail_id > 0";
        $ordSchparam = array(':group_id'=>$group_id,':payer_id'=>$htmValue['payer_id']);
        $hrmPaymentOrderRes = $pdo->selectOne($hrmPaymentOrdersql,$ordSchparam);
        $order_id = !empty($hrmPaymentOrderRes['order_id']) ? $hrmPaymentOrderRes['order_id'] : 0;
        $transaction_id = !empty($hrmPaymentOrderRes['transaction_id']) ? $hrmPaymentOrderRes['transaction_id'] : 0;
        $order_detail_id = !empty($hrmPaymentOrderRes['order_detail_id']) ? $hrmPaymentOrderRes['order_detail_id'] : 0;

        $updateParams = array(
            'status' => 'Completed',
            'order_id' => $order_id,
            'transaction_id' => $transaction_id,
            'order_detail_id' => $order_detail_id
        );
        $updWhere = array(
            'clause' => 'id = :id',
            'params' => array(
                ':id' => $htmValue['id'],
            ),
        );

        $pdo->update('hrm_payment', $updateParams, $updWhere);

        $orderSql = "SELECT status FROM orders WHERE customer_id = :customer_id GROUP BY id";
        $orderRes = $pdo->selectOne($orderSql,array(":customer_id"=>$group_id));
        $orderStatus = !empty($orderRes['status']) ? $orderRes['status'] : '';
        
        $hrmObj->memberHRMPayment("addCredit", "weekly", $group_id, $htmValue['payer_id'], $htmValue['pay_period'], $htmValue['amount'], $htmValue['id'],$pay_date,$orderStatus);
    }

    $hrmCrdSql="SELECT id FROM hrm_payment_credit_balance WHERE group_id=:group_id AND pay_date=:pay_date AND status='Open' ORDER BY id DESC";
    $hrmCrdRes = $pdo->selectOne($hrmCrdSql,$sch_params);
    if(!empty($hrmCrdRes)){
        $updateHrmCrdparams = array(
            'status' => 'Paid'
        );
        $updHrmCrdwhere = array(
            'clause' => 'id = :id',
            'params' => array(
                ':id' => $hrmCrdRes['id'],
            ),
        );
        $pdo->update('hrm_payment_credit_balance', $updateHrmCrdparams, $updHrmCrdwhere);
    }

    //Generate NACHA File Start
    $function_list->generateNachaFile($group_id,$pay_date,'weekly');
}
echo "<br>Completed";
dbConnectionClose();
exit;
