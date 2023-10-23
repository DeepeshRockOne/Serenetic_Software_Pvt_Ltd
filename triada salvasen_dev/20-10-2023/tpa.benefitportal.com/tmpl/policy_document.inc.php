<!DOCTYPE html>
<html lang="en">
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-15">
   </head>
   <body>
      <center>
         <div class="policy_doc_wrap">
            <table class="table">
               <tbody>
                  <tr>
                     <td colspan="2" style="text-align: right; padding:15px 0px; border-bottom:16px solid #ebebeb; font-size: 23px; font-weight: 300;">
                        Verification Document
                     </td>
                  </tr>
                  </tbody>
            </table>
            <div class="clearfix">
               <div class="billing_left">
              <div class="policy_holder_wrap">
               <p class="top_title">Plan Holder
               </p>
                  <p class="policy_holder_body">
                     <?=$on_enrollment['fname'] . ' ' .$on_enrollment['lname']?> <br>
                     <strong class="text-action"><?=$on_enrollment['rep_id']?></strong>
                  </p>
                           
              </div>
           </div>
            <?php if(!empty($customer_billing)){ ?>
              <div class="billing_right">
               <div class="billing_info">
                        <table class="table table-borderless" >
                          <thead>
                            <tr>
                              <th class="text-right">
                                Billing Information
                              </th>
                            </tr>
                          </thead>
                           <tbody>
                              <?php 
                                 $payment_mode = "";
                                 $card_number = "";
                                 $expiry_date = "";
                                 if($customer_billing['payment_mode'] == 'CC'){
                                   $payment_mode = "Credit Card";
                                   $card_number = $customer_billing['card_type'] . ' ' .'*'.$customer_billing['last_cc_ach_no'];
                                   $expiry_date = $customer_billing['expiry_month'] .'/'. $customer_billing['expiry_year'];
                                 }else if($customer_billing['payment_mode'] == 'ACH'){
                                   $payment_mode = "ACH";
                                   $card_number = 'ACH ' .'*'.$customer_billing['last_cc_ach_no'];
                                 }
                                 ?>
                              <tr>
                                 <td class="text-right"><?=$payment_mode?></td>
                              </tr>
                              <tr>
                                 <td class="text-right"><?=$customer_billing['fname'] . ' ' . $customer_billing['lname']?></td>
                              </tr>
                              <tr>
                                 <td class="text-right"><?=$card_number?></td>
                              </tr>
                              <tr>
                                 <td class="text-right"><?=$expiry_date?></td>
                              </tr>
                           </tbody>
                        </table>
               </div>
              </div>
            <?php } ?>
           </div>
            <p class="common_title">Product(s)</p>
            <table class="table table-info">
               <thead>
                  <tr>
                     <th >Order No.</th>
                     <th >Plan ID</th>
                     <th >Product</th>
                     <th >Plan</th>
                     <th >Effective Date</th>
                     <th >Termination Date</th>
                     <th >Total Price</th>
                  </tr>
               </thead>
               <tbody>
                  <?php
                     $stepFeePrice = 0;
                     $serviceFeePrice = 0;
                     $i = 1;
                     $row_count=0;
                     foreach ($ws_row as $product) {
                       $i = ($i == 0?1:0);
                       if($product["type"] == 'Fees'){
                           if($product["product_type"] == "Healthy Step"){
                             $stepFeePrice = $product["price"];
                             continue;
                           }
                           if($product["product_type"] == "ServiceFee"){
                             $serviceFeePrice = $product["price"];
                             continue;
                           }
                           if($product["product_type"] == "AdminFee" && $product["payment_type"] == "list_bill" && $product["sponsor_type"] == "Group"){
                              $admin_fee_price += $product['price'];
                              continue;
                            }
                           $fee_prd_res[] = $product;
                           continue;
                       }
                     ?>
                  <tr style="<?= ($row_count % 2 == 0) ? 'background-color: #f4f4f4;' : '' ?>">
                     <td>
                        <?php if($product['last_order_id'] > 0) { ?>
                        <?php if(strtolower($product['payment_type']) == 'list_bill' && $sponsor_row['billing_type'] != 'individual'){ ?>
                           <?=getname('group_orders',$product['last_order_id'],'display_id','id')?>
                        <?php }else{ ?>
                           <?=getname('orders',$product['last_order_id'],'display_id','id')?>
                        <?php }} ?>
                     </td>
                     <td><?=$product['website_id']?></td>
                     <td><?=$product['name']?></td>
                     <td><?=$product['benefit_tier']?></td>
                     <td><?=displayDate($product['eligibility_date'])?></td>
                     <td><?=displayDate($product['termination_date'])?></td>
                     <td class="text-right"><?=displayAmount($product['price'],2)?></td>
                  </tr>
                  <?php $row_count++; ?>
                  <?php } ?>
                  <?php if (!empty($fee_prd_res)){ ?>
                  <?php foreach ($fee_prd_res as $key => $fee_prd_row) {
                     $i = ($i == 0?1:0);
                     ?>
                  <tr style="<?= ($row_count % 2 == 0) ? 'background-color: #f4f4f4;' : '' ?>">
                     <td>
                        <?php if($fee_prd_row['last_order_id'] > 0) { ?>
                        <?php if(strtolower($fee_prd_row['payment_type']) == 'list_bill' && $sponsor_row['billing_type'] != 'individual'){ ?>
                           <?=getname('group_orders',$fee_prd_row['last_order_id'],'display_id','id')?>
                        <?php }else{ ?>
                           <?=getname('orders',$fee_prd_row['last_order_id'],'display_id','id')?>
                        <?php }} ?>
                     </td>
                     <td><?=$fee_prd_row['website_id']?></td>
                     <td><?=$fee_prd_row['name']?></td>
                     <td><?=$fee_prd_row['benefit_tier']?></td>
                     <td><?=displayDate($fee_prd_row['eligibility_date'])?></td>
                     <td><?=displayDate($fee_prd_row['termination_date'])?></td>
                     <td class="text-right"><?=displayAmount($fee_prd_row['price'],2)?></td>
                  </tr>
                  <?php $row_count++; ?>
                  <?php
                     }} ?>
               </tbody>
            </table>
                <table class="table table-borderless product_total_info" align="right">
                  <tbody>
                     <tr>
                        <td class="font-bold">Service Fee:</td>
                        <td class="text-right"><?=displayAmount($serviceFeePrice)?></td>
                     </tr>
                     <tr>
                        <td class="font-bold">Healthy Step:</td>
                        <td class="text-right"><?=displayAmount($stepFeePrice)?></td>
                     </tr>
                     <tr>
                        <td class="font-bold">Total:</td>
                        <td class="text-right"><?=displayAmount($total_premium['total']-$admin_fee_price)?></td>
                     </tr>
                  </tbody>
               </table>
            <?php if(!empty($tax_pay_details)){ ?>
            <table class="table table-borderless breakdown_plan" cellpadding="0" cellspacing="0" border="0" align="center">
               <tbody>
                  <tr>
                     <td colspan="2" class="common_title text-center">Take Home Pay Breakdown</td>
                  </tr>
                  <tr>
                     <td colspan="2" class="text-center">Breakdown of tax benefits.</td>
                  </tr>
                  <tr>
                     <td class="font-bold">Your Gross Income</td>
                     <td class="text-right"><?=displayAmount($tax_pay_details['gross_income'])?></td>
                  </tr>
                  <tr>
                     <td class="font-bold">Estimated Payroll Taxes</td>
                     <td class="text-right text-danger"><?=displayAmount($tax_pay_details['federal_taxes'] + $tax_pay_details['state_taxes'] + $tax_pay_details['fica'] + $tax_pay_details['medicare'] + $tax_pay_details['local_taxes'])?></td>
                  </tr>
                  <tr>
                     <td style="font-style: italic; padding-left: 15px;">Federal Taxes</td>
                     <td class="text-right text-danger"><?=displayAmount($tax_pay_details['federal_taxes'])?></td>
                  </tr>
                  <tr>
                     <td style="font-style: italic; padding-left: 15px;">State Taxes</td>
                     <td class="text-right text-danger"><?=displayAmount($tax_pay_details['state_taxes'])?></td>
                  </tr>
                  <tr>
                     <td style="font-style: italic; padding-left: 15px;">FICA</td>
                     <td class="text-right text-danger"><?=displayAmount($tax_pay_details['fica'])?></td>
                  </tr>
                  <tr>
                     <td style="font-style: italic; padding-left: 15px;">Medicare</td>
                     <td class="text-right text-danger"><?=displayAmount($tax_pay_details['medicare'])?></td>
                  </tr>
                  <tr>
                     <td style="font-style: italic; padding-left: 15px;">Local Taxes</td>
                     <td class="text-right text-danger"><?=displayAmount($tax_pay_details['local_taxes'])?></td>
                  </tr>
                  <?php 
                     $pre_tax_data = json_decode($tax_pay_details['pre_tax_data'],true);
                     $pre_tax_total = 0;
                     if(!empty($pre_tax_data)){
                        foreach($pre_tax_data as $key => $value){
                           $pre_tax_total += $value;
                        }
                     }
                  ?>
                   <tr>
                     <td class="font-bold">PreTax Deductions</td>
                     <td class="text-right text-danger"><?= displayAmount($pre_tax_total) ?></td>
                  </tr>
                  <?php if(!empty($pre_tax_data)){ ?>
                    <?php foreach ($pre_tax_data as $d_name => $d_value) { ?>
                     <tr>
                        <td style="font-style: italic; padding-left: 15px;"><?=$d_name?></td>
                        <td class="text-right text-danger"><?= displayAmount($d_value) ?></td>
                     </tr>
                    <?php } ?>
                  <?php } ?>
                  <?php 
                     $post_tax_data = json_decode($tax_pay_details['post_tax_data'],true);
                     $post_tax_total = 0;
                     if(!empty($post_tax_data)){
                        foreach($post_tax_data as $key => $value){
                           $post_tax_total += $value;
                        }
                     }
                  ?>
                   <tr>
                     <td class="font-bold">PostTax Deductions</td>
                     <td class="text-right text-danger"><?= displayAmount($post_tax_total) ?></td>
                  </tr>
                  <?php if(!empty($post_tax_data)){?>
                    <?php foreach ($post_tax_data as $d_name => $d_value) { ?>
                     <tr>
                        <td style="font-style: italic; padding-left: 15px;"><?= $d_name ?></td>
                        <td class="text-right text-danger"><?= displayAmount($d_value) ?></td>
                     </tr>
                    <?php } ?>
                  <?php } ?>
                  <tr>
                     <td class="font-bold text-success">Claim Payment</td>
                     <td class="text-right text-success"><?= displayAmount($tax_pay_details['claim_payment']) ?></td>
                  </tr>
                  <tr class="br-top">
                     <td class="font-bold">Your Take Home</td>
                     <td class="text-right text-success"><?= displayAmount($tax_pay_details['take_home']) ?></td>
                  </tr>
               </tbody>
            </table>
            <?php } ?>
            <table cellpadding="0" cellspacing="0" width="100%" border="0">
               <tbody>
                  <tr>
                     <td style="vertical-align: top;">
                        <br>
                        <div class="common_title">
                           Dependent Information
                        </div>
                        <table class="table table-borderless  dependent_info">
                           <thead>
                              <tr>
                                 <th>Name</th>
                                 <th>Gender</th>
                                 <th>Birth Date</th>
                                 <th>Relation</th>
                              </tr>
                           </thead>
                           <tbody>
                              <?php 
                                 if($dependents){
                                 $relation = "";
                                 foreach ($dependents as $dependent) {
                                   if(in_array(ucfirst($dependent['relation']), array('Son','Daughter'))){
                                     $relation = "Child";
                                   }else{
                                     $relation = "Spouse";
                                   }
                                 ?>
                              <tr>
                                 <td><?=$dependent['fname'] . ' ' .$dependent['lname']?></td>
                                 <td><?=$dependent['gender']?></td>
                                 <td><?=displayDate($dependent['birth_date'])?></td>
                                 <td><?=$relation?></td>
                              </tr>
                              <?php } ?>
                              <?php }else{ ?>
                              <tr>
                                 <td colspan="4">No Records.</td>
                              </tr>
                              <?php } ?>
                           </tbody>
                        </table>
                     </td>
                     <td style="vertical-align: top;">
                    
                     </td>
                  </tr>
               </tbody>
            </table>
            <table cellspacing="0" cellspacing="0" width="100%" style="margin-top: 25px; margin-bottom: 25px;">
               <tbody>
                  <tr>
                     <td style="width: 50%; padding-right:10px; ">
                        <table class="table table-info table-borderless" style="border: 1px solid #E0E0E0;">
                           <thead>
                              <tr>
                                 <th colspan="2">Plan Holder Information<br><small style="font-style: italic; font-weight: normal;">On Application</small></th>
                              </tr>
                           </thead>
                           <tbody>
                              <tr>
                                 <td class="font-bold">Member ID</td>
                                 <td class="text-right"><?=$on_enrollment['rep_id']?></td>
                              </tr>
                              <tr>
                                 <td class="font-bold">Member Name</td>
                                 <td class="text-right"><?=$on_enrollment['fname'] .' '.$on_enrollment['lname']?></td>
                              </tr>
                              <tr>
                                 <td class="font-bold">Phone</td>
                                 <td class="text-right"><?=format_telephone($on_enrollment['cell_phone'])?></td>
                              </tr>
                              <tr>
                                 <td class="font-bold">Email</td>
                                 <td class="text-right"><?=$on_enrollment['email']?></td>
                              </tr>
                              <tr>
                                 <td class="font-bold">Gender</td>
                                 <td class="text-right"><?=$on_enrollment['gender']?></td>
                              </tr>
                              <tr>
                                 <td class="font-bold">DOB</td>
                                 <td class="text-right"><?= !empty($on_enrollment['birth_date']) ? date("m/d/Y",strtotime($on_enrollment['birth_date'])) : '' ?></td>
                              </tr>
                              <tr>
                                 <td class="font-bold">Address</td>
                                 <td class="text-right"><?=$on_enrollment['address'] .' '. $on_enrollment['address_2']?></td>
                              </tr>
                              <tr>
                                 <td class="font-bold">City</td>
                                 <td class="text-right"><?=$on_enrollment['city']?></td>
                              </tr>
                              <tr>
                                 <td class="font-bold">State</td>
                                 <td class="text-right"><?=$on_enrollment['state']?></td>
                              </tr>
                              <tr>
                                 <td class="font-bold">Zip Code</td>
                                 <td class="text-right"><?=$on_enrollment['zip']?></td>
                              </tr>
                           </tbody>
                        </table>
                     </td>
                     <td style="width: 50%; padding-left:10px;">
                        <table class="table table-danger table-borderless" style=" border: 1px solid #E0E0E0;">
                           <thead>
                              <tr>
                                 <th colspan="2">Plan Holder Information<br><small style="font-style: italic; font-weight: normal;">Current</small></th>
                              </tr>
                           </thead>
                           <tbody>
                              <tr>
                                  <td class="font-bold">Member ID</strong></td>
                                 <td class="text-right"><?=$cust_row['rep_id']?></td>
                              </tr>
                              <tr>
                                  <td class="font-bold">Member Name</td>
                                 <td class="text-right"><?=$cust_row['fname'] .' '.$cust_row['lname']?></td>
                              </tr>
                              <tr>
                                  <td class="font-bold">Phone</td>
                                 <td class="text-right"><?=format_telephone($cust_row['cell_phone'])?></td>
                              </tr>
                              <tr>
                                  <td class="font-bold">Email</td>
                                 <td class="text-right"><?=$cust_row['email']?></td>
                              </tr>
                              <tr>
                                  <td class="font-bold">Gender</td>
                                 <td class="text-right"><?=$cust_row['gender']?></td>
                              </tr>
                              <tr>
                                  <td class="font-bold">DOB</td>
                                 <td class="text-right"><?= !empty($cust_row['birth_date']) ? date("m/d/Y",strtotime($cust_row['birth_date'])) : '' ?></td>
                              </tr>
                              <tr>
                                  <td class="font-bold">Address</td>
                                 <td class="text-right"><?=$cust_row['address'] .' '. $cust_row['address_2']?></td>
                              </tr>
                              <tr>
                                  <td class="font-bold">City</td>
                                 <td class="text-right"><?=$cust_row['city']?></td>
                              </tr>
                              <tr>
                                  <td class="font-bold">State</td>
                                 <td class="text-right"><?=$cust_row['state']?></td>
                              </tr>
                              <tr>
                                  <td class="font-bold">Zip Code</td>
                                 <td class="text-right"><?=$cust_row['zip']?></td>
                              </tr>
                           </tbody>
                        </table>
                     </td>
                  </tr>
               </tbody>
            </table>
            <div class="term_info">
               <p class="common_title">Terms and Conditions</p>
                <div>
                  <?php echo $term_agreements['agreement']; ?>
                </div>
                <div>
                  <?php echo $member_terms['terms']; ?>
                </div>
                <?php if(!empty($customerCustomQueRes)){ 
                     $existChild = array();
                ?>
                   <div>
                      <p class="common_title"><strong>Custom Questions</strong></p>
                      <?php foreach($customerCustomQueRes as $enrollee => $queAnsArr){?>
                        <p><strong><?=ucfirst($enrollee)?></strong>,</p>
                        <?php foreach($queAnsArr as $queAns){?>
                           <?php if(strtolower($enrollee) == 'child' && !in_array($queAns['dependent_id'],$existChild)){ ?>
                              <span><?= getname('customer_dependent',$queAns['dependent_id'],"CONCAT(fname,' ',lname)") ?></span><br>
                           <?php array_push($existChild,$queAns['dependent_id']); } ?>
                           <span> <?=$queAns['display_label']?> - <?=$queAns['answer']?></span><br>
                      <?php } }?>
                   </div>
               <?php } ?>
               <p class="common_title"><strong>Verification Method</strong></p>
               <p>
                  I agree that I have a full and complete understanding of the products for which I am applying. I certify that I am the applicant listed above and I elect to apply for the following products:
               </p>
            </div>
            <table class="table table-info table-striped">
               <thead>
                  <tr>
                     <th><img src="<?= $HOST ?>/images/icons/pdf_gray_checkbox.png" style="width: 18px;"></th>
                     <th>Details</th>
                     <th>Category</th>
                     <th>Name</th>
                     <th>Effective Date</th>
                     <th class="text-center">Terms</th>
                  </tr>
               </thead>
               <tbody>
                  <?php $row_count=0; ?>
                  <?php foreach ($ws_row as $product) {
                      if($product["product_type"] == "AdminFee" && $product["payment_type"] == "list_bill" && $product["sponsor_type"] == "Group"){
                        continue;
                      } ?>
                  <tr style="<?= ($row_count % 2 == 0) ? 'background-color: #f4f4f4;' : '' ?>">
                     <td><img src="<?= $HOST ?>/images/icons/pdf_gray_checkbox.png" style="width: 18px;"></td>
                     <td><img src="<?= $HOST ?>/images/icons/pdf_info_icon.png?_v=1.1" style="width:16px;"></td>
                     <td><?=$product['category']?></td>
                     <td><?=$product['name']?></td>
                     <td><?=displayDate($product['eligibility_date'])?></td>
                     <td class="text-center"><img src="<?= $HOST ?>/images/icons/pdf_terms_icon.png" style="width:16px;"></td>
                  </tr>
                   <?php $row_count++;?>
                  <?php } ?>
               </tbody>
            </table>
            <br>
            <table class="table bottom_info_wrap" width="100%">
               <tbody>
                  <tr>
                     <td width="34%">
                           <p class="common_title">Signature:</p>
                           <table class="table" width="100%" style="margin-top: 5px;">
                           <tbody>
                              <tr>
                                 <td class="bottom_info_box" height="60px" style="vertical-align: middle;">
                                    <div>
                                       <?php if(!empty($signature_data) && !$blank_signature){ ?>
                                          <img src="<?= $signature_data ?>" style="height: 20px;">
                                       <?php } ?>
                                    </div>
                                 </td>
                              </tr>
                           </tbody>
                        </table>
                     </td>
                     <td width="34%">
                        <p class="common_title ">Date:</p>
                        <table class="table" width="100%" style="margin-top: 5px;">
                           <tbody>
                              <tr>
                                 <td  class="bottom_info_box" height="60px" style="vertical-align: middle;">
                                    <div >
                                       <?=!empty($term_agreements['date_of_signature']) ? displayDate($term_agreements['date_of_signature']) : displayDate($cust_row['created_at'])?>
                                    </div>
                                 </td>
                              </tr>
                           </tbody>
                        </table>
                     </td>
                     <td valign="top">
                        <p class="common_title">&nbsp;</p>
                        <table class="table" width="100%" style="margin-top: 5px;">
                           <tbody>
                              <tr>
                                 <td  class="last_child bottom_info_box" height="60px" style="vertical-align: top;">
                                    <div>
                                       <strong>IP Address:</strong> <?=!empty($term_agreements['ip_address']) ? $term_agreements['ip_address'] : $cust_row['ip_address']?><br><strong>Application Date:</strong> <?=!empty($term_agreements['date_of_signature']) ? $term_agreements['date_of_signature'] : $cust_row['created_at']?>
                                    </div>
                                 </td>
                              </tr>
                           </tbody>
                        </table>
                           
                     </td>
                  </tr>
               </tbody>
            </table>
            <?php if(!empty($fetch_rows) && $total_rows > 0) { ?>
               <br><br>
               <h4 class="text-action mt0">Amendment(s):</h4>
               <?php include_once 'tmpl/policy_document_activity.inc.php';?>
            <?php } ?>
         </div>
      </center>
   </body>
</html>