<?php 
   if(isset($_REQUEST['export'])) {
      ?>
<div style="max-width: 865px; margin: 0px auto; font-size: 12px;">
   <div class="invoice_top_wrap" >
      <div class="blue_bar_invoice" style="background-color: #050606; height: 25px; width: 100%;"></div>
      <div class="invoice_head" style="font-size: 33px; line-height: 33px; margin: -30px 45px 0px 0px; position: relative; font-weight: normal; float:right; background-color: #fff; padding:2px 7px; display: inline-block; color: #5694cc; width: 160px; text-align:center;">RECEIPT</div>
      <div class="table-responsive" style="width: 100%">
         <div class="clearfix">
            <div class="billing_left">
               <div style="font-size: 16px;" class="mn font-bold">
                  <?= $group_name ?>
               </div>
               <div>
                  <?php 
                      if(!empty($group_company_name)) {
                          echo "<p><strong>".$group_company_name."</strong></p>";
                      }
                  ?>
                  <div>
                  <?= $address .'<br>'.$city .', '.$state .' '. $zip ?>
                  </div>
               </div>
            </div>
            <div class="billing_right">
               <div class="billing_info">
                  <table class="table table-borderless" >
                     <tbody>
                        <tr>
                           <td class="font-bold">Invoice #</td>
                           <td class="text-right"><?= $invoice_no ?></td>
                        </tr>
                        <tr>
                           <td class="font-bold">Invoice Date</td>
                           <td class="text-right"><?= $invoice_date ?></td>
                        </tr>
                        <tr>
                           <td class="font-bold">Due Date</td>
                           <td class="text-right"><?=$due_date?></td>
                        </tr>
                        <tr>
                           <td class="font-bold">Total</td>
                           <td class="text-right"><?= $invoice_total ?></td>
                        </tr>
                     </tbody>
                  </table>
               </div>
            </div>
         </div>
         <div class="receipt_seprater"></div>
         <p class="common_title pt15 pb15 br-b"> Payment Receipt</p>
         <div class="col-two">
            <div class="col-two-left">
               <table class="table table-borderless">
                  <tbody>
                     <tr>
                        <td class="font-bold">Payment Date</td>
                        <td><?= date('F d, Y',strtotime($order_row['created_at'])) ?></td>
                     </tr>
                     <tr>
                        <td class="font-bold">Transaction Number</td>
                        <td><?= $order_row['transaction_id'] ?></td>
                     </tr>
                     <tr>
                        <td class="font-bold">Payment Method</td>
                        <td>
                           <?php
                             if($order_row['payment_mode'] == "CC") {
                                 echo $order_row['card_type']." *".$order_row['last_cc_ach_no'];

                             } else if($order_row['payment_mode'] == "ACH") {
                                 echo "ACH *".$order_row['last_cc_ach_no'];

                             } else if($order_row['payment_mode'] == "Check") {
                                 echo "Check";
                             }
                         ?>
                        </td>
                     </tr>
                  </tbody>
               </table>
            </div>
            <div class="col-two-right">
               <table class="table table-borderless">
                  <tbody>
                     <tr>
                        <td class="font-bold">Amendment(s)</td>
                        <td class="text-right text-action">(<?= $amendment ?>)</td>
                     </tr>
                     <tr>
                        <td class="font-bold">Payment Amount</td>
                        <td class="text-right"><?= $received_amount ?></td>
                     </tr>
                     <tr>
                        <td class="font-bold">Balance Forward</td>
                        <td class="text-right"><?= $forward_amount ?></td>
                     </tr>
                  </tbody>
               </table>
            </div>
         </div>
      </div>
   </div>
</div>
<?php
   } else {
       ?>
<div class="container">
   <div class="section-padding ">
      <div class="panel panel-default list-bill-panel">
         <div class="panel-body">
            <div style="max-width: 865px; margin: 0px auto; font-size: 12px;">
               <div class="invoice_top_wrap" >
                  <div class="blue_bar_invoice" style="background-color: #050606; height: 25px; width: 100%;"></div>
                  <div class="invoice_head" style="font-size: 33px; line-height: 33px; margin: -30px 45px 0px 0px; position: relative; font-weight: normal; float:right; background-color: #fff; padding:2px 7px; display: inline-block; color: #5694cc; width: 160px; text-align:center;">RECEIPT</div>
                  <div class="table-responsive" style="width: 100%">
                     <div class="clearfix">
                        <div class="billing_left">
                           <div style="font-size: 16px;" class="mn font-bold">
                             <?= $group_name ?>
                           </div>
                           <div>
                              <?php 
                                 if(!empty($group_company_name)) {
                                     echo "<p><strong>".$group_company_name."</strong></p>";
                                 }
                             ?>
                             <div>
                                 <?= $address .'<br>'.$city .', '.$state .' '. $zip ?>
                             </div>
                           </div>
                        </div>
                        <div class="billing_right">
                           <div class="billing_info">
                              <table class="table table-borderless" >
                                 <tbody>
                                    <tr>
                                       <td class="font-bold">Invoice #</td>
                                       <td class="text-right"><?= $invoice_no ?></td>
                                    </tr>
                                    <tr>
                                       <td class="font-bold">Invoice Date</td>
                                       <td class="text-right"><?= $invoice_date ?></td>
                                    </tr>
                                    <tr>
                                       <td class="font-bold">Due Date</td>
                                       <td class="text-right"><?= $due_date ?></td>
                                    </tr>
                                    <tr>
                                       <td class="font-bold">Total</td>
                                       <td class="text-right"><?= $invoice_total ?></td>
                                    </tr>
                                 </tbody>
                              </table>
                           </div>
                        </div>
                     </div>
                     <div class="receipt_seprater"></div>
                     <p class="common_title pt15 pb15 br-b"> Payment Receipt</p>
                     <div class="col-two">
                        <div class="col-two-left">
                           <table class="table table-borderless">
                              <tbody>
                                 <tr>
                                    <td class="font-bold">Payment Date</td>
                                    <td><?= date('F d, Y',strtotime($order_row['created_at'])) ?></td>
                                 </tr>
                                 <tr>
                                    <td class="font-bold">Transaction Number</td>
                                    <td><?= $order_row['transaction_id'] ?></td>
                                 </tr>
                                 <tr>
                                    <td class="font-bold">Payment Method</td>
                                    <td>
                                       <?php
                                         if($order_row['payment_mode'] == "CC") {
                                            echo $order_row['card_type']." *".$order_row['last_cc_ach_no'];

                                         } else if($order_row['payment_mode'] == "ACH") {
                                            echo "ACH *".$order_row['last_cc_ach_no'];

                                         } else if($order_row['payment_mode'] == "Check") {
                                            echo "Check";
                                         }
                                    ?>
                                    </td>
                                 </tr>
                              </tbody>
                           </table>
                        </div>
                        <div class="col-two-right">
                           <table class="table table-borderless">
                              <tbody>
                                 <tr>
                                    <td class="font-bold">Amendment(s)</td>
                                    <td class="text-right text-action"> (<?= $amendment ?>)</td>
                                 </tr>
                                 <tr>
                                    <td class="font-bold">Payment Amount</td>
                                    <td class="text-right"><?= $received_amount ?></td>
                                 </tr>
                                 <tr>
                                    <td class="font-bold">Balance Forward</td>
                                    <td class="text-right"><?= $forward_amount ?></td>
                                 </tr>
                              </tbody>
                           </table>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="text-center m-t-20">
                <a href="payment_receipt.php?order_id=<?=$order_id?>&export" class="btn text-action"><i class="fa fa-download"></i> Export</a>
            </div>
         </div>
      </div>
   </div>
</div>
<?php
   }
   ?>