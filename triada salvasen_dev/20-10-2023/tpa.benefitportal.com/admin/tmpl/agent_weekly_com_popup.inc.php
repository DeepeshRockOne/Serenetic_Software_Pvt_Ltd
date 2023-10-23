<style type="text/css">
.hiddenrow {padding: 0 !important;}
.table>tbody>tr>td.hiddenrow {border-top:0px;}
.table>tbody>tr>td.hiddenrow > .inner_row{border-top:1px solid #e4e7ea;}
</style>
<?php 
   $startPayPeriod=date('m/d/Y', strtotime('-6 days', strtotime($pay_period)));
   $endPayPeriod=date('m/d/Y', strtotime($pay_period));
?>
<div class="panel panel-default panel-block  ">
   <div class="panel-heading">
      <div class="panel-title ">
         <h4 class="mn">Commission details of Pay Period - <span class="fw300"><?=$startPayPeriod.' - '.$endPayPeriod?></span></h4>
      </div>
   </div>

   <div class="thumbnail mn radius-zero">
      <div class="row">
         <div class="col-md-4">
            <p class="fs16 fw500">Weekly Commission Summary</p>
            <table class="m-b-30 weekly_com_sum">
               <tbody>
                  <tr>
                     <td width="150px"><?=$agentRow['name']?></td>
                     <td><strong>Period:</strong> <?=$startPayPeriod.' - '.$endPayPeriod?></td>
                  </tr>
                  <tr>
                     <td width="150px"><a href="javascript:void(0);" class="red-link"><?=$agentRow['rep_id']?></a></td>
                     <td><strong>Total Commissions:</strong>&nbsp; <?=dispCommAmt($resWeeklyCommission['commTotal'])?>
                  </tr>
               </tbody>
            </table>
         </div>
         <div class="col-md-8">
          <div class="row">
         <div class="col-md-8">
            <div class="table-responsive">
               <table class="table table-striped table-small  com_small_tbl weekly text-right">
                  <thead>
                     <tr>
                        <th class="text-left"></th>
                        <th>Credit</th>
                        <th>Debit</th>
                        <th>Total</th>
                     </tr>
                  </thead>
                  <tbody>
                     <tr>
                        <td class="table_light_danger text-left">Earned</td>
                        <td><?=dispCommAmt($earnedCommWeekly)?></td>
                        <td><?=dispCommAmt($earnedCommRevWeekly)?></td>
                        <td><?=dispCommAmt($earnedNetCommWeekly)?></td>
                     </tr>
                     <tr>
                        <td class="table_dark_danger text-left">Advanced</td>
                        <td><?=dispCommAmt($advanceCommWeekly)?></td>
                        <td><?=dispCommAmt($advanceCommRevWeekly)?></td>
                        <td><?=dispCommAmt($advanceNetCommWeekly)?></td>
                     </tr>
                     <tr>
                        <td class="table_light_danger text-left">PMPM</td>
                        <td><?=dispCommAmt($pmpmCommWeekly)?></td>
                        <td><?=dispCommAmt($pmpmCommRevWeekly)?></td>
                        <td><?=dispCommAmt($pmpmNetCommWeekly)?></td>
                     </tr>
                     <tr>
                        <td class="table_dark_danger text-left"><strong>Total</strong></td>
                        <td><?=dispCommAmt($totalEarnedCommWeekly)?></td>
                        <td><?=dispCommAmt($totalRevCommWeekly)?></td>
                        <td><?=dispCommAmt($totalNetCommWeekly)?></td>
                     </tr>
                  </tbody>
               </table>
            </div>
         </div>
         <div class="col-md-4">
            <div class="table-responsive">
               <table class="table table-striped table-small  com_small_tbl weekly text-right">
                  <thead>
                     <tr>
                        <th class="text-right"></th>
                        <th class="text-right">Total</th>
                     </tr>
                  </thead>
                  <tbody>
                     <tr>
                        <td class="table_light_danger text-left">Past Reversals</td>
                        <td><?=dispCommAmt($pastCommRevMonthly)?></td>
                     </tr>
                     <tr>
                        <td class="table_dark_danger text-left">Fees</td>
                        <td><?=dispCommAmt($feeCommWeekly)?></td>
                     </tr>
                     <tr>
                        <td class="table_light_danger text-left">Adjustments</td>
                        <td><?=dispCommAmt($adjustmentCommWeekly)?></td>
                     </tr>
                     <tr>
                        <td class="table_dark_danger text-left"><strong>Total</strong></td>
                        <td><?=dispCommAmt($otherTotalCommWeekly)?></td>
                     </tr>
                  </tbody>
               </table>
            </div>
         </div>
       </div>
       </div>
      </div>
   </div>
   <div class="panel-body">
   <div class="theme-form">
   <form id="reps_search_form" method="GET">
      <input type="hidden" name="status" value="<?=$status?>">
      <input type="hidden" name="agent_id" value="<?=$agent_id?>">
      <input type="hidden" name="commission_type" value="<?=$commission_type?>">
      <input type="hidden" name="pay_period" value="<?=$pay_period?>">
      <div class="row">
         <div class="col-sm-3">
            <div class="form-group height_auto">
               <input type="text" name="order_id" value="<?=$order_id?>" class="form-control">
               <label>Order ID</label>
            </div>
         </div>
         <div class="col-sm-3">
            <div class="form-group height_auto">
               <input type="text" name="payer_id" value="<?=$payer_id?>" class="form-control">
               <label>Member ID</label>
            </div>
         </div>
         <div class="col-sm-3">
            <div class="form-group height_auto">
               <input type="text" name="payer_name" value="<?=$payer_name?>" class="form-control">
               <label>Member  Name</label>
            </div>
         </div>
         <div class="col-sm-3">
            <div class="form-group height_auto">
               <select id="commission_type" name="commission_type" class="form-control">
                  <option value="">&nbsp;</option>
                  <?php if (count($commission_types) > 0) {?>
                     <?php foreach ($commission_types as $ckey => $ctypes) {?>
                     <option value="<?=$ckey?>" <?php echo $commission_type == $ckey ? 'selected' : '' ?>><?php echo $ctypes; ?></option>
                     <?php }?>
                  <?php }?>
               </select>
               <label>Commission Type</label>
            </div>
         </div>
      </div>
      <div class="form-group height_auto">
         <button class="btn btn-info" id="search" name="search" type="submit" onclick="$('#ajax_loader').show()"><i class="fa fa-search"></i>&nbsp; Search<button>
         <a href="agent_weekly_com_popup.php?pay_period=<?=$pay_period?>&type=weekly_commission&agent_id=<?=$agent_id?>&status=<?=$status?>" class="btn btn-info btn-outline" onclick="$('#ajax_loader').show()">Clear Search</a>
      </div>
   </form>
   </div>
      <div class="table-responsive com_panel_tabel">
         <table class="table" style="table-layout: fixed;">
            <tbody>
            <?php 
               $totalCommission = 0;
               if(count($display_commission) > 0) {
            ?>
               <tr>
                  <td></td>
                  <td><strong class="text-blue">Commission Type</strong></td>
                  <td class="text-right"><strong class="text-blue">Credit</strong></td>
                  <td class="text-right"><strong class="text-blue">Debit</strong></td>
                  <td class="text-right"><strong class="text-blue">Total</strong></td>
               </tr>
               <?php
               $i = 0;
               $total = 0;
               foreach($display_commission as $ckey => $com_row) {
                  $totalBalance = $totalCredit[$ckey] + $totalDebit[$ckey];
                  $totalCommission += $totalBalance;
                  $add_class = $ckey == 'Reverse' ? 'text-action' : '';
                  ?>
                     <tr>
                        <td width="50px" data-toggle="collapse" data-target="#row_<?=$i?>" class="accordion-toggle text-center test">
                           <a href="javascript:void(0);" class="plusicon-bg"><i class="fa fa-plus fa-lg"></i>
                           </a>
                        </td>
                        <td><?=$commission_types[$ckey]?><span class="text-light-gray">&nbsp; <i>(<?=count($com_row)?>)</i></span></td>
                        <td class="text-right"><?=dispCommAmt($totalCredit[$ckey])?></td>
                        <td class="text-right"><?=dispCommAmt($totalDebit[$ckey])?></td>
                        <td class="text-right"><?=dispCommAmt($totalBalance)?></td>
                     </tr>
                     <tr>
                        <td colspan="5" class="hiddenrow">
                           <div class="accordion-body collapse inner_row" id="row_<?=$i++?>">
                           <?php 
                              if(count($com_row) > 0){
                           ?>
                              <div class="table-responsive">
                                 <table class="table table-striped table-small color-table info-table m-b-0 com_tbl" style="table-layout: fixed;">
                                    <thead>
                                       <tr>
                                          <th>Order</th>
                                          <th>Member </th>
                                          <th>Product</th>
                                          <?php if($ckey == 'Reverse'){ ?>
                                          <th>Type</th>
                                          <?php } ?>
                                          <th class="text-right">Amount</th>
                                          <th class="text-right">Payout</th>
                                          <?php if(in_array($ckey,array("Earned","Reverse"))){ ?>
                                          <th class="text-right">Earned</th>
                                          <th class="text-right">Graded</th>
                                          <?php } ?>
                                          <th class="text-right">Credit</th>
                                          <th class="text-right">Debit</th>
                                          <th class="text-right">Total</th>
                                       </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                       foreach ($com_row as $key => $row) {
                                          $creditAmtRow = $row['creditAmt'];
                                          $debitAmtRow = $row['debitAmt'];
                                          $totalAmtRow =  $creditAmtRow + $debitAmtRow;
                                          $orderDate = $row['commDate'];
                                    ?>
                                       <tr class="<?=$add_class?>">
                                          <td><?=$row['ordDisplayId']?><br><?=getCustomDate($orderDate)?></td>
                                          <td><?=$row['mbrName']?><br><?=$row['mbrRepId']?></td>
                                          <td><?=$row['prdName']?><br>(<?=$row['prdCode']?>)</td>
                                          <?php if($ckey == 'Reverse'){ ?>
                                          <td><?=$row['revType']?></td>
                                          <?php } ?>
                                          <td class="text-right"><?=dispCommAmt($row['prdPrice'])?></td>
                                          <td class="text-right"><?=$row['percentage'] . "%"?></td>
                                          <?php if(in_array($ckey,array("Earned","Reverse"))){ ?>
                                             <td class="text-right"><?=dispCommAmt($row['earnedComm'])?></td>
                                             <td class="text-right"><?=$row['gradedPercentage'] . "%"?></td>
                                          <?php } ?>
                                          <td class="text-right"><?=dispCommAmt($creditAmtRow)?></td>
                                          <td class="text-right"><?=dispCommAmt($debitAmtRow)?></td>
                                          <td class="text-right"><?=dispCommAmt($totalAmtRow)?></td>
                                       </tr>
                                    <?php } ?>
                                    </tbody>                               
                                 </table>
                              </div>
                           <?php } else {
                              echo "<p class='text-center m-t-10'>No " . $commission_types[$ckey] . " earned for this commission period</p>";
                           }?>
                           </div>
                        </td>
                     </tr>
               <?php  } ?>
               <?php } ?>
               <tr>
                  <td class="text-right" colspan="2"><strong >Adjustments</strong></td>
                  <td class="text-right" colspan='3'><?=dispCommAmt($totaladjustCommission)?></td>
               </tr>
               <?php $totalCommission = $totalCommission + $totaladjustCommission; ?>
               <tr>
                  <td class="text-right" colspan="2"><strong >Total Commission</strong></td>
                  <td class="text-right" colspan='3'><strong><?=dispCommAmt($totalCommission,"text-blue")?></strong></td>
               </tr>
            </tbody>
         </table>
      </div>
      <div class="text-center m-t-20">
        <a href="javascript:void(0);" class="btn red-link" onclick="parent.$.colorbox.close(); return false;">Close</a>
      </div>
   </div>
</div>
<script type="text/javascript">
$('.plusicon-bg').click(function() {
  $(this).parents('.accordion-toggle').find('i').toggleClass('fa-plus fa-minus');
  $(this).parents('.accordion-toggle').siblings('.accordion-toggle').find('i').removeClass('fa-plus').addClass('fa-minus')
});
</script>