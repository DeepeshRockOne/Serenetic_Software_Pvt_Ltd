<?php
include_once __DIR__ . '/includes/connect.php';

$agentId = checkIsset($_REQUEST["agentId"]);
$payPeriod = checkIsset($_REQUEST["payPeriod"]);
$commissionDuration = checkIsset($_REQUEST["commissionDuration"]);

if($commissionDuration == 'weekly'){
  $startPayPeriod = date('m/d/Y', strtotime('-6 days', strtotime($payPeriod)));;
  $endPayPeriod = date('m/d/Y', strtotime($payPeriod));
}else{
  $startPayPeriod = date('m/01/Y', strtotime($payPeriod));
  $endPayPeriod = date('m/d/Y', strtotime($payPeriod));
}

if(!empty($agentId) && !empty($payPeriod) && !empty($commissionDuration)){
  $sqlPaidComm = "SELECT id,action,pmpm_action,paid_to_debit,paid_to_agent,pmpm_to_debit,pmpm_to_agent 
                FROM commission_credit_balance 
                WHERE agent_id=:agentId AND pay_period=:payPeriod 
                AND commission_duration=:commissionDuration AND status='Paid' ORDER BY id DESC";
  $paramsComm = array(
                ":agentId" => $agentId,
                ":payPeriod" => $payPeriod,
                ":commissionDuration" => $commissionDuration
              );
  $resPaidComm = $pdo->selectOne($sqlPaidComm,$paramsComm);

  $paidToAgent = !empty($resPaidComm["paid_to_agent"]) ? $resPaidComm["paid_to_agent"] : 0;
  $pmpmToAgent = !empty($resPaidComm["pmpm_to_agent"]) ? $resPaidComm["pmpm_to_agent"] : 0;
  
  $paidToDebit = !empty($resPaidComm["paid_to_debit"]) ? $resPaidComm["paid_to_debit"] : 0;
  $pmpmToDebit = !empty($resPaidComm["pmpm_to_debit"]) ? $resPaidComm["pmpm_to_debit"] : 0;

  $paidAgent = $paidToAgent + $pmpmToAgent;
  $paiDebitBalance = $paidToDebit + $pmpmToDebit;
}

?>
<h4 class="m-t-0">Commissions Details</h4>
<p><?php echo ucwords($commissionDuration) .': '. displayDate($startPayPeriod) .' - '.displayDate($endPayPeriod); ?>
<table class="m-b-10" cellpadding="0" cellspacing="0" width="100%" border="0">
  <tbody>
    <tr>
      <td><strong>Paid Agent: </strong><?=dispCommAmt($paidAgent)?>&nbsp;&nbsp;&nbsp;</td>
      <td class="text-right"><strong>Paid Debit Balance:</strong> <?=dispCommAmt($paiDebitBalance) ?></td>
    </tr>
  </tbody>
</table>

<div class="table-responsive">
  <table class="<?=$table_class?> fs12">
      <thead>
          <tr>
              <th>Action</th>
              <th>Amount</th>
          </tr>
      </thead>
      <tbody>
        <?php
          $totalCommissions = $paidAgent + $paiDebitBalance;
        ?>
          <tr>
            <td>Paid To Agent</td>
            <td><?=dispCommAmt($paidToAgent)?></td>
          </tr>
          <tr>
            <td>PMPM To Agent</td>
            <td><?=dispCommAmt($pmpmToAgent)?></td>
          </tr>
          <tr>
            <td>Paid To Debit Balance</td>
            <td><?=dispCommAmt($paidToDebit)?></td>
          </tr>
          <tr>
            <td>PMPM To Debit Balance</td>
            <td><?=dispCommAmt($pmpmToDebit)?></td>
          </tr>
          <tr>
            <td><strong>Total Commissions</strong></td>
            <td><strong><?=dispCommAmt($totalCommissions)?></strong></td>
          </tr>
      </tbody>
  </table>
</div>