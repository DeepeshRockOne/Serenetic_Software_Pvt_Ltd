<?php
ini_set('memory_limit', '-1');
ini_set('max_execution_time', '-1');
include_once dirname(__FILE__) . '/layout/start.inc.php';

$res = array();
$res['status'] = 'fail';

$pay_period = checkIsset($_REQUEST["pay_period"]);
$pay_date = checkIsset($_REQUEST['pay_date']);
$hrm_payment_duration = checkIsset($_REQUEST["hrm_payment_duration"]);
$groupId = checkIsset($_REQUEST["groupId"]);
$status = checkIsset($_REQUEST['status']);
$compliant = checkIsset($_REQUEST['compliant']);
$key = checkIsset($_REQUEST['key']);
$nachaid = checkIsset($_REQUEST['nachaid']);

if (!empty($groupId) && !empty($hrm_payment_duration) && !empty($pay_period)) {

    $hrmParams = array(":hrm_payment_duration" => $hrm_payment_duration, ":groupId" => $groupId, ":pay_period" => $pay_period, ':status'=>$status);

    $incr = '';
    $tableIncr = '';
    if(!empty($pay_date)){
        $incr .= ' AND hrmp.pay_date=:pay_date';
        $hrmParams[':pay_date'] = $pay_date;
    }

    if($status == 'Completed' && !empty($nachaid)){
        $tableIncr = "JOIN nacha_file_export as nfe ON(nfe.is_deleted='N' AND nfe.group_id=s.id)
                    JOIN nacha_file_members nfm ON(nfm.is_deleted='N' AND nfm.customer_id=c.id AND nfm.nacha_batch_id=nfe.id)";
        $incr .= ' AND nfe.id=:nachaid';
        $hrmParams[':nachaid'] = $nachaid;
    }
    $hrmSql = "SELECT DISTINCT(hrmp.payer_id) AS payerId,
                    c.rep_id AS memberRepId,hrmp.pay_period,hrmp.pay_date,
                    CONCAT(c.fname,' ',c.lname) AS memberName,
                    SUM(IF(hrmp.sub_type='New' OR hrmp.sub_type='Renewals',hrmp.amount,0)) AS totalAmount
                    FROM hrm_payment hrmp
                    JOIN customer AS s ON(s.id = hrmp.group_id AND s.type='Group')
                    JOIN customer c ON (c.id = hrmp.payer_id AND c.type='Customer')
                    JOIN customer_settings cst ON (c.id = cst.customer_id)
                    $tableIncr
                    WHERE hrmp.hrm_payment_duration = :hrm_payment_duration AND hrmp.is_deleted='N' 
                    AND hrmp.group_id = :groupId AND hrmp.pay_period = :pay_period 
                    AND hrmp.status=:status $incr GROUP BY hrmp.payer_id,hrmp.pay_period ";
    
    $hrmPaymentData = $pdo->select($hrmSql, $hrmParams);
    if (!empty($hrmPaymentData)) {
        ob_start();
        foreach ($hrmPaymentData as $memberData) {
            $class = $status == 'Pending' ? 'payToSpecMemberBox'  : ('nonCompliant'? 'moveComplinatToSpecMemberBox': 'genNachaToSpecMemberBox');
            $id = $status == 'Pending' ? 'cs_approve_to_pay_' : ( 'nonCompliant' ? 'cs_move_to_compliant_' : 'cs_approve_to_complete_'); ?>
            <tr>
                <?php if($status == 'NonCompliant') { ?>
                <td>
                    <div class="checkbox checkbox-table">
                        <input type="checkbox" id="<?= $id ?><?= $memberData['payerId'] ?>" class="js-switch <?= $class ?>" data-pay-date="<?=$memberData['pay_date']?>" value="<?= $memberData['payerId'] ?>" data-key="<?=$key?>" data-id="<?= $groupId ?>" />
                        <label for="<?= $id ?><?= $memberData['payerId'] ?>"></label>
                    </div>
                </td>
                <?php } ?>
                <td><?= $memberData['memberRepId']; ?></td>
                <td><?= $memberData['memberName']; ?></td>
                <td><?= date('m/d/Y', strtotime($memberData['pay_date'])); ?></td>
                <td><?= displayAmount($memberData['totalAmount']); ?></td>
            </tr>
<?php }
        $html = ob_get_clean();
        $response['html'] = $html;
        $response['status'] = 'success';
    }
} else {
    $response['status'] = 'fail';
}
echo json_encode($response);
dbConnectionClose();
exit;
?>