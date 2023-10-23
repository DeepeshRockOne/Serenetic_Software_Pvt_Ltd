<p class="fw500"> Summary</p>
<div class="table-responsive">
    <table class="table table-borderless table-striped <?=$tblClass?>">
        <thead>
            <tr>
                <?php if($resTrans['is_list_bill_order'] == "Y") { ?>
                    <th>Member ID</th>
                <?php } ?>
                <th>Product</th>
                <th>Coverage Period</th>
                <th>Coverage</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $stepFeePrice = 0; 
                $serviceFeePrice = 0; 
                $stepFeeRefund = 'N'; 
                $serviceFeeRefund = 'N'; 
                if(!empty($detRes)){
                  $fee_prd_res = array();
                  foreach ($detRes as $key => $order) {
                    if($order["type"] == 'Fees'){
                      if($order["product_type"] == "Healthy Step"){
                        $stepFeePrice = $order["price"];
                        $stepFeeRefund= $order["is_refund"];
                        continue;
                      }
                      if($order["product_type"] == "ServiceFee" || $order["product_type"] == "ListBillServiceFee"){
                        $serviceFeePrice = $order["price"];
                        $serviceFeeRefund = $order["is_refund"];
                        continue;
                      }
                      $fee_prd_res[] = $order;
                      continue;
                    }else{
                      $subTotal += $order["price"];
                    }
                ?>
        <?php if($transStatus != 'Payment Approved'){ ?>
            <tr>
        <?php }elseif($transStatus == 'Payment Approved'){ ?>
            <tr class="<?=$order['is_refund'] == 'Y' ? 'text-danger' : ''?>">
        <?php } ?>
                <?php if($resTrans['is_list_bill_order'] == "Y") { ?>
                <td><?=$order["rep_id"]?></td>
                <?php } ?>
                <td>
                    <?=$order["product_name"]?>
                </td>
                <td>
                    <?=date("m/d/Y",strtotime($order["start_coverage_period"]))?> -
                    <?=date("m/d/Y",strtotime($order["end_coverage_period"]))?>
                </td>
                <td>
                    <?=$order["planTitle"]?>
                </td>
                <td class="text-right">
                    <?=displayAmount(abs($order["price"]))?>
                </td>
            </tr>
            <?php
                  }
                  foreach ($fee_prd_res as $key => $fee_prd_row) {
                    $subTotal += $fee_prd_row["price"];
                    ?>
        <?php if($transStatus != 'Payment Approved'){ ?>
            <tr>
        <?php }elseif($transStatus == 'Payment Approved'){ ?>
            <tr class="<?=$fee_prd_row['is_refund'] == 'Y' ? 'text-danger' : ''?>">
        <?php } ?>
                <?php if($resTrans['is_list_bill_order'] == "Y") { ?>
                 <td><?=$fee_prd_row["rep_id"]?></td>
                <?php } ?>
                <td>
                    <?=$fee_prd_row["product_name"]?>
                </td>
                <td></td>
                <td>Fees</td>
                <td class="text-right">
                    <?=displayAmount(abs($fee_prd_row["price"]))?>
                </td>
            </tr>
            <?php
                  }
                }
                ?>
        </tbody>
    </table>
    <table class="table table-borderless pull-right receipt_table m-t-20" style="max-width: 250px;">
        <tbody>
            <?php /*<tr class="<?=$transStatus == 'Refund' ? 'text-danger' : ''?>">*/?>
            <tr>
                <td>SubTotal(s)</td>
                <td class="text-right">
                    <?=displayAmount($subTotal)?>
                </td>
            </tr>
            <?php /*<tr class="<?=$stepFeeRefund == 'Y' ? 'text-danger' : ''?>">*/?>
            <tr>
                <td>Healthy Step(s)</td>
                <td class="text-right">
                    <?=displayAmount($stepFeePrice)?>
                </td>
            </tr>
            <?php /*<tr class="<?=$serviceFeeRefund == 'Y' ? 'text-danger' : ''?>">*/?>
            <tr>
                <td>Service Fee(s)</td>
                <td class="text-right">
                    <?=displayAmount($serviceFeePrice)?>
                </td>
            </tr>
            <?php /*<tr class="<?=$transStatus == 'Refund' ? 'text-danger' : ''?>">*/?>
            <tr>
                <td class="fw500">Total</td>
                <td class="text-right fw500">
                    <?=displayAmount($transTotal)?>
                </td>
            </tr>
            <?php if($transStatus == 'Refund'){ ?>
            <hr>
            <?php /*<tr class="<?=$transStatus == 'Refund' ? 'text-danger' : ''?>">*/?>
            <tr>
                <td class="fw500">Refund</td>
                <td class="text-right fw500">(<?=displayAmount($transTotal)?>)</td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>