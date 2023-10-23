<?php if($is_ajaxed) { ?>
<div class="table-responsive">
    <table class="<?=$table_class?>">
        <thead>
        <tr>
            <th>Earning Period</th>
            <th>Status</th>
            <th>Statements</th>
            <th>Credit</th>
            <th>Debit</th>
            <th>Total</th>
        </tr>
        </thead>
        <tbody>
        <?php if($totalRecords > 0 ) {
                $monthlyPayPeriod = $commObj->getMonthlyPayPeriod(date('Y-m-d'));
                
                foreach($fetchRecords as $rows) {
                $startPayPeriod=date('m/01/Y', strtotime($rows['pay_period']));
                $endPayPeriod=date('m/d/Y', strtotime($rows['pay_period']));
             
                $creditAmt = $rows['credit_amount'];
                $debitAmt = $rows['debit_amount'];
                $totalAmt = $creditAmt + $debitAmt;
             
                $payPeriodStatus = $rows['status'];
                if(strtotime($monthlyPayPeriod) == strtotime($rows['pay_period'])){
                    $payPeriodStatus = "In Progress";
                }

            ?>
        <tr>
            <td><?=$startPayPeriod?> - <?=$endPayPeriod?></td>
            <td class="text-success"><?=$payPeriodStatus?></td>
            <td class="icons">
                <a href="javascript:void(0)" data-toggle="tooltip" title="View Statement" data-href="agent_monthly_com_popup.php?pay_period=<?=$rows['pay_period']?>&agent_id=<?=md5($agent_id)?>" class="agent_monthly_com_popup"><i class="fa fa-eye text-blue"></i></a>
                <a href="commission_export_csv.php?commission_duration=monthly&agentIds=<?=$rows['customer_id']?>&pay_period=<?=$rows['pay_period']?>" class="exportCSV" data-toggle="tooltip" id="Export_csv" name="Export_csv" title="" data-original-title="Export CSV"><i class="fa fa-download"></i></a>
            </td>
            <td><?=dispCommAmt($creditAmt)?></td>
            <td><?=dispCommAmt($debitAmt)?></td>
            <td><?=dispCommAmt($totalAmt)?></td>
        </tr>
        <?php } }else{ ?>
            <tr>
                <td colspan="6"> No Rows Found!</td>
            </tr>
        <?php } ?>
        </tbody>
        <tfoot>
            <tr>
            <?php if($totalRecords > 0 && !empty($fetchRecords)) { ?>
                <td colspan="6">
                <?php echo $paginate_records->links_html; ?>
                </td>
            <?php } ?>
            </tr>
        </tfoot>
    </table>
</div>
<?php } ?>