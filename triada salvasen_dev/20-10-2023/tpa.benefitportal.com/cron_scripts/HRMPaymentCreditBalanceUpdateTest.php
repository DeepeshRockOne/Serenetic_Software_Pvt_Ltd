<?php
include_once dirname(__DIR__) . "/cron_scripts/connect.php";
include_once dirname(__DIR__) . "/includes/hrm_payment.class.php";
$hrmObj = new HRMPayment();

$group_id =  'G56118';

$hrmSql="SELECT hp.* FROM hrm_payment hp JOIN customer c ON (c.id = hp.group_id AND c.type='Group') WHERE c.rep_id=:group_id ORDER BY hp.id ASC";
$hrmSch = array(":group_id"=>$group_id);
$hrmRes = $pdo->select($hrmSql,$hrmSch);

if(!empty($hrmRes)){
    /*---------remove-old-records-hrm-credit-balance-and-history-start----------------*/
    $hrmGroupId=$hrmRes[0]['group_id'];
    $pdo->delete("DELETE FROM hrm_payment_credit_balance WHERE group_id = :hrmgroup_id",array(":hrmgroup_id"=>$hrmGroupId));
    $pdo->delete("DELETE FROM hrm_payment_credit_balance_history WHERE group_id = :hrmgroup_id",array(":hrmgroup_id"=>$hrmGroupId));
    /*---------remove-old-records-hrm-credit-balance-and-history-end-------------------*/
    /*---------hrm-payment-credit-balance-and-history--start---------------------------*/
    foreach ($hrmRes as $key => $htmValue) {
        $orderSql = "SELECT status FROM orders WHERE customer_id = :customer_id GROUP BY id";
        $orderRes = $pdo->selectOne($orderSql,array(":customer_id"=>$htmValue['group_id']));
        $orderStatus = !empty($orderRes['status']) ? $orderRes['status'] : '';
        
        $hrmObj->memberHRMPayment("addCredit", "weekly", $htmValue['group_id'], $htmValue['payer_id'], $htmValue['pay_period'], $htmValue['amount'], $htmValue['id'],$htmValue['pay_date'],$orderStatus);

        /*----------update-hrm-payment-credit-balance-start---------*/
        $updateParams = array(
            'created_at' => $htmValue['created_at'],
            'updated_at' => $htmValue['updated_at'],
        );
        $updWhere = array(
            'clause' => 'group_id = :group_id AND pay_period = :pay_period',
            'params' => array(
                ':group_id' => $htmValue['group_id'],
                ':pay_period' => $htmValue['pay_period'],
            ),
        );
        $pdo->update('hrm_payment_credit_balance', $updateParams, $updWhere);
        /*----------update-hrm-payment-credit-balance-end---------*/

        /*----------update-hrm-payment-credit-balance-history-start---------*/
        $updateParamHistory = array(
            'created_at' => $htmValue['created_at'],
            'updated_at' => $htmValue['updated_at'],
        );
        $updWheresHistory = array(
            'clause' => 'group_id = :group_id AND pay_period = :pay_period',
            'params' => array(
                ':group_id' => $htmValue['group_id'],
                ':pay_period' => $htmValue['pay_period'],
            ),
        );

        $pdo->update('hrm_payment_credit_balance_history', $updateParamHistory, $updWheresHistory);
        /*----------update-hrm-payment-credit-balance-history-end---------*/
    }
    /*---------hrm-payment-credit-balance-and-history--end---------------------------*/
}
echo "<br>Completed";
dbConnectionClose();
exit;