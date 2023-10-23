<div class="panel panel-default panel-block">
  <div class="panel-heading">
    <div class="panel-title">
        <p class="fs18">
            <strong class="fw500"><?=$processor_res['name']?> (<?=$payment_type?>) - </strong> <span class="fw300">Transactions</span>
        </p>
    </div>
  </div>
  <div class="panel-body">
    <div class="thumbnail bg_light_gray clearfix">
        <div class="phone-control-wrap">
            <div class="phone-addon text-left">
                <h4 class="m-t-0 m-b-5">Monthly Threshold</h4>
                <p class="mn"><?=displayAmount($processor_res['monthly_threshold_sale'],2);?></p>
            </div>
            <div class="phone-addon">
                <p class="mn fs12 text-right fw500"><?=date("F Y")?></p>
            </div>
        </div>
    </div>
    <div class="table-responsive br-n">
      <table class="<?=$table_class?> merchant_table">
        <thead>
          <tr>
            <th>Type</th>
            <th class="text-center">Transactions</th>
            <th class="text-center">Threshold Percentage</th>
          </tr>
        </thead>
        <tbody>
          <tr class="table_dark_primary">
            <?php $all_transcation = get_total_transaction('All',$processor_id,$processor_res['monthly_threshold_sale'],array('from_date' => date("Y-m-01"),"to_date" => date("Y-m-t"))); ?>
            <td>Sales (total)</td>
            <td class="text-center"><span class="<?=$all_transcation['lable_class']?>"><?=$all_transcation['total_count']?></span>/<span class="<?=$all_transcation['lable_class']?>"><?=displayAmount($all_transcation['total_amount'],2);?></span></td>
            <td class="text-center"><?=$all_transcation['parcentage'];?></td>
          </tr>
          <tr class="table_light_primary">
            <?php $new_transcation = get_total_transaction('New Business',$processor_id,$processor_res['monthly_threshold_sale'],array('from_date' => date("Y-m-01"),"to_date" => date("Y-m-t"))); ?>
            <td class="text-right">New Business</td>
            <td class="text-center"><span class="<?=$new_transcation['lable_class']?>"><?=$new_transcation['total_count']?></span>/<span class="<?=$new_transcation['lable_class']?>"><?=displayAmount($new_transcation['total_amount'],2);?></span></td>
            <td class="text-center"><?=$new_transcation['parcentage'];?></td>
          </tr>
          <tr class="table_dark_primary">
            <?php $renewal_transcation = get_total_transaction('Renewals',$processor_id,$processor_res['monthly_threshold_sale'],array('from_date' => date("Y-m-01"),"to_date" => date("Y-m-t"))); ?>
            <td class="text-right">Renewals</td>
            <td class="text-center"><span class="<?=$renewal_transcation['lable_class']?>"><?=$renewal_transcation['total_count']?></span>/<span class="<?=$renewal_transcation['lable_class']?>"><?=displayAmount($renewal_transcation['total_amount'],2);?> </span></td>
            <td class="text-center"><?=$renewal_transcation['parcentage'];?></td>
          </tr>
          <tr>
            <?php $chargeback_transcation = get_total_transaction('Chargebacks',$processor_id,$processor_res['monthly_threshold_sale'],array('from_date' => date("Y-m-01"),"to_date" => date("Y-m-t"))); ?>
            <td>Chargebacks</td>
            <td class="text-center"><span class="<?=$chargeback_transcation['lable_class']?>"><?=$chargeback_transcation['total_count']?></span>/<span class="<?=$chargeback_transcation['lable_class']?>">(<?=displayAmount($chargeback_transcation['total_amount'],2);?></span>)</td>
            <td class="text-center"><?=$chargeback_transcation['parcentage'];?></td>
          </tr>
          <tr>
            <?php $refund_transcation = get_total_transaction('Refunds',$processor_id,$processor_res['monthly_threshold_sale'],array('from_date' => date("Y-m-01"),"to_date" => date("Y-m-t"))); ?>
            <td >Refunds</td>
            <td class="text-center"><span class="<?=$refund_transcation['lable_class']?>"><?=$refund_transcation['total_count']?></span>/<span class="<?=$refund_transcation['lable_class']?>">(<?=displayAmount($refund_transcation['total_amount'],2);?>)</span></td>
            <td class="text-center"><?=$refund_transcation['parcentage'];?></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
  <div class="m-t-15 text-center m-b-30">
    <a href="javascript:void(0);" class="btn red-link" onclick='parent.$.colorbox.close(); return false;'>Cancel</a>
  </div>
</div>