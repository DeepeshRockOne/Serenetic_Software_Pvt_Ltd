<p class="fw500"> Summary</p>
    <table class="table table-borderless table-striped <?=$tblClass?>">
        <thead>
            <tr>
                <?php if(checkIsset($resOrder['is_list_bill_order']) == "Y") { ?>
                    <th>Member ID</th>
                <?php } ?>
                <th>Product</th>
                <th>Plan Period</th>
                <th>Plan</th>
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
                        $stepFeeRefund = $order["is_refund"];
                        continue;
                      }
                      if($order["product_type"] == "ServiceFee" || $order["product_type"] == "ListBillServiceFee"){
                        $serviceFeePrice = $order["price"];
                        $serviceFeeRefund = $order["is_refund"];
                        continue;
                      }
                      if($order["product_type"] == "CobraServiceFee"){
                        $CobraServiceFee = $order["price"];
                        $CobraServiceFeeRefund = $order["is_refund"];
                        continue;
                      }
                      $fee_prd_res[] = $order;
                      continue;
                    }
                    if(!empty($resOrder['billing_type']) &&  in_array($resOrder['billing_type'],array('list_bill','TPA'))) {  
                        $subTotal = ($subTotal+$order["price"]);
                    }
                ?>
        <?php if($orderStatus != 'Payment Approved'){ ?>
            <tr>
        <?php }elseif($orderStatus == 'Payment Approved'){ ?>
            <tr class="<?=$order['is_refund'] == 'Y' ? 'text-danger' : ''?>">
        <?php } ?>
                <?php if($resOrder['is_list_bill_order'] == "Y") { ?>
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
                    <?=$prdPlanTypeArray[$order["planTitle"]]['title']?>
                </td>
                <td class="text-right">
                    <?=displayAmount($order["price"])?>
                </td>
            </tr>
            <?php
                  }
                  foreach ($fee_prd_res as $key => $fee_prd_row) {
                    ?>
        <?php if($orderStatus != 'Payment Approved'){ ?>
            <tr>
        <?php }elseif($orderStatus == 'Payment Approved'){ ?>
            <tr class="<?=$fee_prd_row['is_refund'] == 'Y' ? 'text-danger' : ''?>">
        <?php } ?>
                <?php if($resOrder['is_list_bill_order'] == "Y") { ?>
                    <td><?=$fee_prd_row["rep_id"]?></td>
                <?php } ?>
                <td>
                    <?=$fee_prd_row["product_name"]?>
                </td>
                <td></td>
                <td>Fees</td>
                <td class="text-right">
                    <?=displayAmount($fee_prd_row["price"])?>
                </td>
            </tr>
            <?php
                  }
                }
                ?>
        </tbody>
    </table>
    <table class="table table-borderless receipt_table m-t-10" style="max-width: 250px;" align="right">
        <tbody>
            <?php /*<tr class="<?=$orderStatus == 'Refund' ? 'text-danger' : ''?>">*/?>
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
            <?php if($CobraServiceFee > 0){ ?>
                <?php /*<tr class="<?=$CobraServiceFeeRefund == 'Y' ? 'text-danger' : ''?>">*/?>
                <tr>
                <td>COBRA Service Fee(s)</td>
                <td class="text-right">
                    <?=displayAmount($CobraServiceFee)?>
                </td>
            </tr>
            <?php } ?>
            <?php if(!empty($resOrder['billing_type']) &&  in_array($resOrder['billing_type'],array('list_bill','TPA'))) {  
                $grandTotal = $subTotal + $stepFeePrice + $serviceFeePrice + $CobraServiceFee;
            } ?>
            <?php /*<tr class="<?=$orderStatus == 'Refund' ? 'text-danger' : ''?>">*/?>
            <tr>
                <td class="fw500">Total</td>
                <td class="text-right fw500">
                    <?=displayAmount($grandTotal)?>
                </td>
            </tr>
            <?php if($orderStatus == 'Refund'){ ?>
            <hr>
            <?php /*<tr class="<?=$orderStatus == 'Refund' ? 'text-danger' : ''?>">*/?>
            <tr>
                <td class="fw500">Refund</td>
                <td class="text-right fw500">(<?=displayAmount($grandTotal)?>)</td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    <div class="clearfix"></div>