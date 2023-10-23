<?php
  if ($is_ajaxed) {
?>
<div class="panel-body">
  <div class="table-responsive">
        <table class="<?=$table_class?>">
          <thead>
            <tr>
              <th>Earning Period</th>
              <th>Status</th>
              <th>Debit</th>
              <th class="text-center">Credit</th>
              <th class="text-center">Total</th>
              <th class="text-right">Overall  Debit Balance</th>
            </tr>
          </thead>
          <tbody>
            <?php 
              if($total_rows > 0){
                $overallDebit = 0;
                $ledgerArr = array();
                foreach ($fetch_rows as $key => $row) {
                  $selPeriod = "SELECT id,status FROM commission_credit_balance WHERE agent_id=:agent_id AND pay_period=:pay_period AND commission_duration=:commission_duration ORDER BY id DESC";
                  $paramsPeriod = array(
                        ":agent_id" => $row["agent_id"],
                        ":pay_period" => $row["pay_period"],
                        ":commission_duration" => $row["commission_duration"],
                      );
                  $resPeriod = $pdo->selectOne($selPeriod,$paramsPeriod);
                  // pre_print($resPeriod);
                  $pay_period = $row["pay_period"];
                  $commission_duration = checkIsset($row["commission_duration"]);

                  if($commission_duration == "weekly"){
                    $startPayPeriod=date('m/d/Y', strtotime('-6 days', strtotime($pay_period)));
                    $endPayPeriod=date('m/d/Y', strtotime($pay_period));
                  }else{
                    $startPayPeriod=date('m/01/Y', strtotime($pay_period));
                    $endPayPeriod=date('m/d/Y', strtotime($pay_period));
                  }

                  $status = "";
                  if(checkIsset($resPeriod["status"]) == "Paid"){
                    $status = "Paid";
                  }else if(($commission_duration == "weekly" && strtotime($weeklyPayPeriod) == strtotime($pay_period)) || ($commission_duration == "monthly" && strtotime($monthlyPayPeriod) == strtotime($pay_period))){
                    $status = "In Progress";
                  }else if(($commission_duration == "weekly" && strtotime($weeklyPayPeriod) > strtotime($pay_period)) || ($commission_duration == "monthly" && strtotime($monthlyPayPeriod) > strtotime($pay_period))){
                    $status = "Pending";
                  }
                  
                  $debit = $row['debitBalance'];
                  $credit = $row['appliedCredit'];

                  $monthlyTotal = $row['debitBalance'] + $row['appliedCredit'];
                  $overallDebit += $monthlyTotal;

                  $tmp = array();
                  $tmp["id"] = $row["id"];
                  $tmp["startPayPeriod"] = $startPayPeriod;
                  $tmp["endPayPeriod"] = $endPayPeriod;
                  $tmp["status"] = $status;
                  $tmp["debit"] = $debit;
                  $tmp["credit"] = $credit;
                  $tmp["monthlyTotal"] = $monthlyTotal;
                  $tmp["overallDebit"] = $overallDebit;


                  $ledgerArr[] = $tmp;
                }
                
                $ledgerArr = array_reverse($ledgerArr);
                
                if(!empty($ledgerArr)){
                  foreach ($ledgerArr as $key => $ledgerRow) {
            ?>  
              <tr>
                <td><?=$ledgerRow['startPayPeriod'] .' - '.$ledgerRow['endPayPeriod']?></td>
                <td><?=$ledgerRow['status']?></td>
                <td><?=dispCommAmt($ledgerRow['debit'])?></td>
                <td class="text-center"><?=dispCommAmt($ledgerRow['credit'])?></td>
                <td class="text-center"><?=dispCommAmt($ledgerRow['monthlyTotal'])?></td>
                <td class="text-center"><?=dispCommAmt($ledgerRow['overallDebit'])?></td>
              </tr>
            <?php      
                  }
                }
              
              }else{
            ?>
              <td class="text-center" colspan="6">No Records Found</td>
            <?php    
              }
            ?>
          </tbody>
        </table>
  </div>
  <div class="clearfix">
  <div class="ledger_debit pull-right">
    <strong>Starting Debit Balance: </strong>$0.00 
  </div>
</div>
  <div class="m-t-20 clearfix text-center">
     <a href="javascript:void(0);" class="btn btn-action" id="btn_export">Export Ledger</a>
     <a href="javascript:void(0);" class="btn red-link" onclick="parent.$.colorbox.close(); return false;">Cancel</a>
  </div>
</div>
<?php 
  }else{
?>
 <form action="monthly_ledger_popup.php" name="ledgerFrm" id="ledgerFrm">
    <input type="hidden" name="agentId" id="agentId" value="<?=$agentId?>" />
    <input type="hidden" name="is_ajaxed" id="is_ajaxed" value="1" />
    <input type="hidden" name="export" id="export" value=""/>
    <div class="panel panel-default panel-block">
      <div class="panel-heading">
          <div class="panel-title">
             <h4 class="mn">Debit Balance Ledger</h4>
          </div>
      </div>
    </div>
    <div class="panel panel-default panel-block">
        <div id="ajax_loader" class="ajex_loader" style="display: none;">
          <div class="loader"></div>
        </div>
        <div id="ajax_data"> </div>
    </div>
</form>

<script type="text/javascript">
  $(document).ready(function(){
    ajax_submit();

    $(document).off('click', '#btn_export');
    $(document).on('click', '#btn_export', function(e) {
        parent.confirm_export_data(function() {
            $('#ajax_loader').show();
            var params = {'action':'agent_debit_ledger'};
            $.ajax({
                url: 'monthly_ledger_popup.php',
                type: 'GET',
                data: params,
                dataType: 'json',
                success: function(res) {
                    $('#ajax_loader').hide();
                    $("#export").val('');
                    if(res.status == "success") {
                        confirm_view_export_request(true,'agent');
                    } else {
                        setNotifyError(res.message);
                    }
                }
            });
        });
    });
  });

  function ajax_submit(){
    $('#ajax_loader').show();
    $('#ajax_data').hide();
    $('#is_ajaxed').val('1');
    var params = $('#ledgerFrm').serialize();
    var cpage = $('#nav_page').val();
    $.ajax({
        url: $('#ledgerFrm').attr('action'),
        type: 'GET',
        data: params,
        success: function (res) {
            $('#ajax_loader').hide();
            $('#ajax_data').html(res).show();
        }
    });
    return false;
  }
</script>

<?php
  }
?>



