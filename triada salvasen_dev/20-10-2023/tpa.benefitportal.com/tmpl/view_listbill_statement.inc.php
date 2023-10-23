
<div style="max-width: 865px; margin: 0px auto; font-size: 12px;">
   <div class="clearfix" style="margin-bottom: 20px;">
      <div class="billing_left"> 
         <p class="mn font-bold">Triada,</p>
         <p class="mn">10713 West Sam Houston Parkway N Suite #100</p>
         <p class="mn">Houston, TX 77064</p>
         <p class="mn">1-877-387-4232</p>
         <a class="text-primary" href="https://tpa.benefitportal.com" target="_blank">www.tpa.benefitportal.com</a>
      </div>
      <div class="billing_right text-right">
         <img src="<?= $HOST ?>/images/trida_logo.png" height="70px" style="margin-top: 10px;">
      </div>
   </div>
   <div class="invoice_top_wrap" >
      <div class="blue_bar_invoice" style="background-color: #050606; height: 25px; width: 100%;"></div>
      <div class="invoice_head" style="font-size: 33px; line-height: 33px; margin: -30px 45px 0px 0px; position: relative; font-weight: normal; float:right; background-color: #fff; padding:2px 7px; display: inline-block; color: #5694cc; width: 160px; text-align:center;">INVOICE</div>
      <div class="table-responsive" style="width: 100%">
             <div class="clearfix">
               <div class="billing_left">
               <div style="font-size: 16px;" class="mn font-bold">
                  <?= $group_name ?>
               </div>
               <div>
                  <?= $address .'<br>'.$city .', '.$state .' '. $zip ?>
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
                        <td class="text-right"><?= displayAmount2($global_total,2) ?></td>
                     </tr>
                     <tr>
                        <td class="font-bold">Coverage</td>
                        <td class="text-right"><?= date($DATE_FORMAT,strtotime($startCoverage)).' - '.date($DATE_FORMAT,strtotime($endCoverage)) ?></td>
                     </tr>
                  </tbody>
               </table>
               </div>
            </div>
           </div>
         <?php if(!empty($list_bill_items_res)) { 
            ?>
            <?php foreach ($list_bill_items_res as $is_cobra_coverage => $coverageArray) { 
               ?>

               <?php foreach ($coverageArray as $start_end_coverage_date => $ListBillData) { ?>
                     <?php 
                        $coverageDates=explode(':',$start_end_coverage_date);
                        $start_coverage_date=$coverageDates[0];
                        $end_coverage_date=$coverageDates[1];
                     ?>
                     <p style="font-size: 14px; margin-top: 15px;">Coverage Period: 
                        <span class="text-action">
                           <?= date($DATE_FORMAT,strtotime($start_coverage_date)) .' - '. date($DATE_FORMAT, strtotime($end_coverage_date)) ?>      
                        </span>
                     </p>

                     <table style="margin-top: 15px;" class="table table-info">
                        <thead>
                           <tr>
                              <?php if(!empty($title_arr)) {
                                 foreach ($title_arr as $key => $value) { ?>
                                    <th><?=$value?></th>
                                 <?php } ?>
                              <?php } ?>
                           </tr>
                        </thead>
                        <tbody>
                           <?php if(!empty($ListBillData)) { ?>
                              <?php $row_count=0; ?>
                              <?php foreach ($ListBillData as $customerID => $detail) { ?>
                                 <?php if(!empty($detail) && !empty($detail['cust_id']) && !empty($detail['customer_name'])){ ?> 

                                    <tr style="<?= ($row_count % 2 == 0) ? 'background-color: #f4f4f4;' : '' ?>">
                                       <td><?= $detail['customer_name'] ?></td>
                                       <td><?= $detail['gp_company_name'] ?></td>
                                       <?php if(!empty($product_arr)) { ?>
                                          <?php foreach ($product_arr as $titel_key => $category_id) { ?>
                                             <?php 
                                                $tmpCatTotal = isset($detail[$category_id]['category_total']) ? $detail[$category_id]['category_total'] : 0;
                                                $tmpCatType =  isset($detail[$category_id]['transaction_type']) ? $detail[$category_id]['transaction_type'] : '';

                                             ?>
                                             <td class="<?= $tmpCatTotal < 0 ? 'text-action' : '' ?>" style="width:15%;">
                                                <?php if($tmpCatTotal < 0) { ?>
                                                   (<?= displayAmount(abs($tmpCatTotal),2) ?>)
                                                <?php }else{ ?>
                                                   <?= displayAmount(abs($tmpCatTotal),2) ?>
                                                <?php } ?>
                                             </td>
                                          <?php } ?>
                                       <?php } ?>
                                         
                                       
                                       <?php $tmpEmpTotal = isset($detail['employee_total']) ? $detail['employee_total'] : 0 ?>
                                       <td class="<?= $tmpEmpTotal < 0 ? 'text-action' : '' ?>" >
                                          <?php if($tmpEmpTotal < 0) { ?>
                                             (<?= displayAmount(abs($tmpEmpTotal),2) ?>)
                                          <?php }else{ ?>
                                             <?= displayAmount(abs($tmpEmpTotal),2) ?>
                                          <?php } ?>
                                       </td>

                                       <?php $tmpGrpTotal = isset($detail['group_employer_total']) ? $detail['group_employer_total'] : 0 ?>
                                       <td class="<?= $tmpGrpTotal < 0 ? 'text-action' : '' ?>" >
                                          <?php if($tmpGrpTotal < 0) { ?>
                                             (<?= displayAmount(abs($tmpGrpTotal),2) ?>)
                                          <?php }else{ ?>
                                             <?= displayAmount(abs($tmpGrpTotal),2) ?>
                                          <?php } ?>

                                       </td>
                                       <?php $tmpListTotal = isset($detail['total']) ? $detail['total'] : 0 ?>
                                       <td class="<?= $tmpListTotal < 0 ? 'text-action' : '' ?> font-bold">
                                          <?php if($tmpListTotal < 0) { ?>
                                             (<?= displayAmount(abs($tmpListTotal),2) ?>)
                                          <?php }else{ ?>
                                             <?= displayAmount(abs($tmpListTotal),2) ?>
                                          <?php } ?>
                                       </td>
                                       <?php $row_count++;?>
                                    </tr>
                                 <?php } ?>
                              <?php } ?>
                           <?php }else{ ?>
                           <?php } ?>
                        </tbody>
                     </table>
               <?php } ?>
              
            <?php } ?>
         <?php } ?>
         
         <table style=" margin-top: 30px;"  class="table table-borderless listbill_product_total" align="right">
            <tbody>
               <?php if(!empty($global_coverage_total)) { ?>
                  <?php foreach ($global_coverage_total as $global_start_end_coverage_date => $coverage_total_data) { 
                     $globalCoverageDates=explode(':',$global_start_end_coverage_date);
                     $gstart_coverage_date=$globalCoverageDates[0];
                     $gend_coverage_date=$globalCoverageDates[1];
                  ?>
                     <tr>
                        <td class="pl0">Coverage Period: <?= date($DATE_FORMAT,strtotime($gstart_coverage_date)) .' - '. date($DATE_FORMAT, strtotime($gend_coverage_date)) ?></td>
                        <td class="<?= $coverage_total < 0 ? 'text-action' :'' ?> pr0 font-bold text-right">
                           <?php if($coverage_total_data['total'] < 0){?>
                              (<?= displayAmount(abs($coverage_total_data['total']), 2) ?>)
                           <?php }else { ?>
                              <?= displayAmount(abs($coverage_total_data['total']), 2) ?>
                           <?php } ?>
                        </td>
                     </tr>
                  <?php } ?>
               <?php } ?>
               <tr>
                  <td class="pl0">COBRA Coverage: </td>
                  <td class="text-right pr0"><?= displayAmount($cobra_total, 2) ?></td>
               </tr>
               <tr>
                  <td class="pl0">Processing Fees:  </td>
                  <td class="text-right pr0"><?= displayAmount($admin_fee, 2) ?></td>
               </tr>
               <tr>
                  <td class="pl0">Past Due Amount:</td>
                  <td class="text-right pr0"><?= displayAmount2($past_due_amount, 2) ?></td>
               </tr>
               <tr>
                  <td class="pl0">Adjustment(s):</td>
                  <td class="<?= ($list_bill_adjustment < 0) ? 'text-action' : '' ?> text-right pr0">
                     <?php if($list_bill_adjustment<0){ ?>
                        (<?= displayAmount(abs($list_bill_adjustment), 2) ?>)
                     <?php }else{ ?>
                        <?= displayAmount(abs($list_bill_adjustment), 2) ?>
                     <?php } ?>
                  </td>
               </tr>
               <tr>
                  <td class="font-bold pl0">Total ($):</td>
                  <td class="font-bold text-right pr0"><?= displayAmount2($global_total, 2) ?></td>
               </tr>
            </tbody>
         </table>
      </div>
   </div>

   <?php if(!empty($amendment_items_res)) { ?>
      <div class="invoice_bottom_wrap m-t-30 bg_light_gray" style="padding: 30px; margin-top: 30px;">
         <div class="font-bold">Amendment Summary</div>
         <?php foreach ($amendment_items_res as $amd_start_end_coverage_date => $ListBillData) { 
                     $amdCoverageDates=explode(':',$amd_start_end_coverage_date);
                     $astart_coverage_date=$amdCoverageDates[0];
                     $aend_coverage_date=$amdCoverageDates[1];            
         ?>
               <p style="font-size: 14px; margin-bottom: 15px; margin-top: 15px;">Coverage Period: <span class="text-action"><b><?= date($DATE_FORMAT,strtotime($astart_coverage_date)) .' - '. date($DATE_FORMAT, strtotime(date("Y-m-t",strtotime($aend_coverage_date)))) ?> </b></span></p>

               <div class="table-responsive" style="width: 100%">
                     <table class="table table-info table-striped">
                        <thead>
                           <tr>
                              <?php if(!empty($title_arr)) {
                                    foreach ($title_arr as $key => $value) { ?>
                                       <th class="font-bold">
                                          <?=$value?>    
                                       </th>
                                    <?php } ?>
                              <?php } ?>
                           </tr>
                        </thead>
                        <tbody>
                           <?php if(!empty($ListBillData)) { ?>
                              <?php $row_count=0; ?>
                              <?php foreach ($ListBillData as $customerID => $detail) { ?>
                                 <?php if(!empty($detail) && !empty($detail['cust_id']) && !empty($detail['customer_name'])){ ?>
                                    <?php $totalChecked = array(); ?>
                                    <tr style="<?= ($row_count % 2 == 0) ? 'background-color: #f1f1f1;' : '' ?>">
                                       <td><?= $detail['customer_name'] ?></td>
                                       <td> - </td>
                                       <?php if(!empty($product_arr)) { ?>
                                          <?php foreach ($product_arr as $titel_key => $category_id) { ?>
                                                <?php $tmpTotal = isset($detail[$category_id]['category_total']) ? ($detail[$category_id]['category_total']) : '0'; ?>
                                                <td class="<?= ($tmpTotal <= 0) ? '' : 'text-action' ?>">
                                                   <?php if($tmpTotal <= 0) { ?>
                                                      <?= displayAmount(abs($tmpTotal),2)?>
                                                   <?php }else{ ?>
                                                      (<?= displayAmount(abs($tmpTotal),2)?>)
                                                   <?php } ?>
                                                   
                                                </td>
                                          <?php } ?>
                                       <?php } ?>
                                       <td> - </td>
                                       <td class="<?= ($detail['total'] <= 0) ? '' : 'text-action' ?> font-bold" >
                                          <?php if($detail['total'] < 0) { ?>
                                             <?= displayAmount(abs($detail['total']), 2); ?>
                                          <?php }else{ ?>
                                             (<?= displayAmount(abs($detail['total']), 2); ?>)
                                          <?php } ?>
                                          
                                       </td>
                                    </tr>
                                 <?php } ?>
                              <?php } ?>
                           <?php }else{ ?>
                           <?php } ?>
                        </tbody>
                     </table>
               </div>
         <?php } ?>
         <div class="table-responsive" style="width: 100%">
            <table style=" margin-top: 30px;"  class="table table-borderless listbill_product_total" align="right">
               <tbody>
                  <tr>
                     <td>Original Balance:  </td>
                     <td class="text-right"><?= displayAmount($global_total,2) ?></td>
                  </tr>
                  <tr>
                     <td>Amendment(s) </td>
                     <td class="text-action text-right">(<?= displayAmount(abs($amendment_total),2) ?>)</td>
                  </tr>
                  <tr>
                     <td class="text-success font-bold" style="border-top:1px solid #d3d3d3; border-bottom: 1px solid #d3d3d3;">Current Balance to be Paid</td>
                     <td class="text-success font-bold text-right" style="border-top:1px solid #d3d3d3; border-bottom: 1px solid #d3d3d3";><?= displayAmount($final_balance,2) ?></td>
                  </tr>
               </tbody>
            </table>
         </div>
         <div class="bg_white" style="padding: 15px 10px; text-align: center;   border-radius: 2px;   border: 1px solid #E6E6E6; margin-top: 30px;">
            *Amendment(s) made on this invoice will be visible on current statement only and used solely for payment calculations. All amendment(s) must be made on the members’ record(s) to be reflective on the next invoice as balances will be forwarded to next statement. 
         </div>
      </div>
   <?php } ?>
</div>
