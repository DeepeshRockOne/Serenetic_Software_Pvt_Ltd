<?php //if($sponsor_billing_method == 'individual'){ ?>
<div id="enrollmentPaymentDiv">
<?php  if($sponsor_billing_method == 'individual') { ?>
<hr>
<h4 class="m-t-0 m-b-25">Payment</h4>
<?php } ?>
<?php if($display_default_billing == 'Y' && $sponsor_billing_method == 'individual') { ?>
  <div class="row theme-form">
      <div class="col-md-6">
          <div class="form-group">
              <select class="form-control" name="billing_profile" id="billing_profile">
                  <option data-hidden="true"></option>   
                  <?php 
                     if(isset($def_bill_row)) {
                        foreach ($def_bill_row as $k => $billing) {
                           if($billing['is_direct_deposit_account'] !='Y'){
                              $def_bill_text = '';
                              if($billing['payment_mode'] == "CC") {
                                 $def_bill_text = $billing['card_type'].' *'.$billing['last_cc_ach_no'];
                              } elseif($billing['payment_mode'] == "ACH") {
                                 $def_bill_text = 'ACH *'.$billing['last_cc_ach_no'];
                              }
                              if($billing['is_default'] == 'Y') {
                                 $def_bill_text .= " (Default)";
                              } ?>
                        <option value="<?=$billing['id']?>"><?=$def_bill_text?></option>   
                     <?php }
                        }
                     }
                  ?>
                  <option value="new_billing">New Payment Method</option>   
              </select>
              <label>Payment Method*</label>
              <p class="error" id="error_billing_profile"></p>
          </div>
      </div>
  </div>
<?php } else { ?>
<input type="hidden" name="billing_profile" value="new_billing" id="billing_profile">
<p class="error" id="error_billing_profile"></p>
<?php } ?>
<div id="new_payment_method" style="<?=$display_default_billing == 'Y'?"display:none;":""?>">
  <?php  if($sponsor_billing_method == 'individual') { ?>
   <div id="select_payment_method">
   <p class="m-b-25">Select Payment Type.</p>
   <div class="m-b-25">
      <?php if($is_cc_accepted && $sponsor_billing_method == 'individual') { ?>
      <label class="radio-inline"><input type="radio" name="payment_mode" id="payment_mode_cc" value="CC" checked="checked">Credit Card</label>
      <?php } ?>
      <?php if($is_ach_accepted && $sponsor_billing_method == 'individual') { ?>
      <label class="radio-inline"><input type="radio" name="payment_mode" id="payment_mode_ach" value="ACH">ACH Bank Draft</label>
      <?php } ?>
      <p class="error" id="error_payment_mode"></p>
   </div>
   </div>
   <?php if($sponsor_billing_method == 'individual' && $is_cc_accepted) { ?>
   <div class="row m-b-5" id="payment_credit_card">
      <div class="col-lg-6 col-md-8">
         <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                  <input type="text" name="name_on_card" id="name_on_card" maxlength="25"
                   value="<?= !empty($billing_data['fname']) ? $billing_data['fname'] : '' ?>"
                   class="form-control">
                   <label>Name On Card*</label>
                  <p class="error" id="error_name_on_card"></p></div>
            </div>
            <div class="col-sm-6">
               <div class="form-group">
                  <input type="text" name="card_number" value="" id="card_number" class="required form-control tblur" oninput="isValidNumber(this)" maxlength="16">
                  <label>Card Number* <span id="cc_billing_detail"><?= !empty($billing_data['card_no']) ? "(".$billing_data['card_type']." *" . $billing_data['card_no'] . ")" : '' ?></span></label>
                  <p class="error" id="error_card_number"></p>
               </div>
               <input type="hidden" name="full_card_number" value="<?=!empty($billing_data['card_no_full']) ? $billing_data['card_no_full'] : ''?>">
            </div>
            <div class="col-sm-6">
                <div class="form-group cc_type_wrapper">
                  <select name="card_type" id="card_type" class="tblur form-control" data-error="Card Type is required">
                     <option value=""> </option>
                     <?php if(in_array('Visa', $acceptable_cc)){ ?>
                      <option value="Visa" <?= !empty($billing_data['card_type']) && $billing_data['card_type'] == 'Visa' ? 'selected="selected"' : '' ?>> Visa </option>
                     <?php } ?>
                     <?php if(in_array('MasterCard', $acceptable_cc)){ ?>
                      <option value="MasterCard" <?= !empty($billing_data['card_type']) && $billing_data['card_type'] == 'MasterCard' ? 'selected="selected"' : '' ?>> MasterCard </option>
                      <?php } ?>
                     <?php if(in_array('Discover', $acceptable_cc)){ ?>
                      <option value="Discover" <?= !empty($billing_data['card_type']) && $billing_data['card_type'] == 'Discover' ? 'selected="selected"' : '' ?>> Discover </option>
                      <?php } ?>
                     <?php if(in_array('Amex', $acceptable_cc)){ ?>
                      <option value="Amex" <?= !empty($billing_data['card_type']) && $billing_data['card_type'] == 'Amex' ? 'selected="selected"' : '' ?>> American Express </option>
                      <?php } ?>
                  </select>
                <label>Card Type*</label>
                <p class="error" id="error_card_type"></p>
             </div>
           </div>
           <div class="col-sm-6">
               <div class="form-group">
                  <?php
                     $date = '';
                     if(!empty($billing_data['expiry_month']) && !empty($billing_data['expiry_year'])){
                       $date = date('m/y',strtotime($billing_data['expiry_month']."/01/".$billing_data['expiry_year']));
                     }
                           
                   ?>
                   <input type="text" name="expiration" id="expiration" value="<?=$date?>" class="form-control">
                   <label>Expiration Date*</label>
                   <p class="error" id="error_expiration"></p>
               </div>
            </div>
            <div class="col-sm-6">
               <div class="form-group">
                  <input name="cvv_no" type="text" value="<?=!empty($billing_data['cvv_no']) ? $billing_data['cvv_no'] : ''?>"  class="form-control " oninput="isValidNumber(this)" maxlength="4" />
                  <label>CVV*</label>
                  <p class="error" id="error_cvv_no"></p>
               </div>
            </div>
         </div>
      </div>
   </div>
   <?php } ?>
  <?php } ?>
   <?php if($sponsor_billing_method == 'individual' && $is_ach_accepted) { ?>
      <div class="row m-b-5" id="payment_bank_draft" style="display:none;">
         <div class="col-lg-6 col-md-8">
            <div class="row">
               <div class="col-sm-6">
                  <div class="form-group">
                    <input type="text" class="form-control" name="ach_bill_fname" id="ach_bill_fname" value="<?= !empty($billing_data['fname']) ? $billing_data['fname'] : '' ?>">
                    <label>First Name*</label>
                    <p class="error" id="error_ach_bill_fname"></p> 
                  </div>  
               </div>  
               <div class="col-sm-6">
                  <div class="form-group">
                    <input type="text" class="form-control" name="ach_bill_lname" id="ach_bill_lname"  value="<?= isset($billing_data['lname']) ? $billing_data['lname'] : '' ?>">
                    <label>Last Name*</label>
                    <p class="error" id="error_ach_bill_lname"></p> 
                  </div>
               </div>
               <div class="clearfix"></div>
               <div class="col-sm-6">
                  <div class="form-group">
                     <input type="text" class="form-control" name="bankname"  value="<?= isset($billing_data['bankname'])?$billing_data['bankname']:"" ?>">
                     <label>Bank Name*</label>
                     <p class="error" id="error_bankname"></p> 
                  </div>
               </div>
               <div class="col-sm-6">
                  <div class="form-group">
                     <select name="ach_account_type" id="ach_account_type" class="form-control" data-error="Account Type is required">
                       <option value=""></option>
                       <option value="checking" <?= isset($billing_data['ach_account_type']) && $billing_data['ach_account_type'] == 'checking' ? 'selected="selected"' : ''?>>Checking</option>
                       <option value="savings" <?= isset($billing_data['ach_account_type']) && $billing_data['ach_account_type'] == 'savings' ? 'selected="selected"' : ''?>>Saving</option>
                     </select>
                     <label>Select Account Type*</label>
                     <p class="error" id="error_ach_account_type"></p>
                  </div>
               </div>
               <div class="clearfix"></div>
               <div class="col-sm-6">
                  <div class="form-group">
                     <input type="text" class="form-control" name="account_number" oninput="isValidNumber(this)" maxlength='17'>
                     <?php if(!empty($billing_data['ach_account_number'])){?>
                        <label style="<?=!empty($billing_data['ach_account_number']) ? '' : 'display:none'?>" class="text-white">Account Number <span id="ach_billing_detail"><?= !empty($billing_data['ach_account_number']) ? "(ACH *" . substr($billing_data['ach_account_number'],-4) . ")" : '' ?></span><span class="req-indicator">*</span></label>
                     <?php } else {?>
                        <label style="<?=!empty($billing_data['ach_account_number']) ? 'display:none;' : ''?>">Account Number*</label>
                     <?php } ?>
                     <p class="error" id="error_account_number"></p>
                     <input type="hidden" name="entered_account_number" id="entered_account_number" value="<?=!empty($billing_data['ach_account_number']) ? $billing_data['ach_account_number'] : ''?>" maxlength='50' class="required form-control tblur" >
                  </div>
               </div>
               <div class="col-sm-6">
                  <div class="form-group">
                     <input type="text" class="form-control" name="confirm_account_number" oninput="isValidNumber(this)" maxlength='17'>
                     <label>Confirm Account Number*</label>
                     <p class="error" id="error_confirm_account_number"></p>
                  </div>
               </div>
               <div class="clearfix"></div>
               <div class="col-sm-6">
                  <div class="form-group">
                     <input type="text" class="form-control" name="routing_number" value="<?=!empty($billing_data['ach_routing_number']) ? $billing_data['ach_routing_number'] : ''?>" oninput="isValidNumber(this)" maxlength='9'>
                     <label>Routing Number*</label>
                     <p class="error" id="error_routing_number"></p>
                     <input type="hidden" name="entered_routing_number" id="entered_routing_number" value="<?=!empty($billing_data['ach_routing_number']) ? $billing_data['ach_routing_number'] : ''?>" maxlength='50' class="required form-control">
                  </div>
               </div>
            </div>
         </div>
      </div>
   <?php } ?>
</div>
   <!----------ACH-info-deposit-account--start-code------->
   <div class="row m-b-5" id="payment_bank_deposit" style="display:none;">
      <hr>
      <div class="col-lg-6 col-md-8">
         <div class="row">
            <input type="hidden" id="is_direct_deposit_account" name="is_direct_deposit_account" value="<?= $is_direct_deposit_account ? 'Y' : 'N' ?>">
            <input type="hidden" id="is_gap_or_hip_plus_product" name="is_gap_or_hip_plus_product" value="<?= $is_gap_or_hip_plus_product ? 'Y' : 'N' ?>">
            <div class="col-sm-12" id="ach_information">
               <h3>ACH Information</h3>
               <p><i class="fa fa-info-circle" title="By completing this form to allow deposits into your bank account, you are only authorizing money to be added to your account. You are not authorizing any money be withdrawn, or taken out, from your account." data-toggle="tooltip" data-trigger="hover"></i> Please complete the fields below to enable deposits into your account. All information is secure and encrypted.</p>
            </div>
            <div class="col-sm-6">
               <div class="form-group">
                  <input type="text" class="form-control" name="deposit_ach_bill_fname" id="deposit_ach_bill_fname" value="">
                  <label>First Name*</label>
                  <p class="error" id="error_deposit_ach_bill_fname"></p> 
               </div>  
            </div>  
            <div class="col-sm-6">
               <div class="form-group">
                  <input type="text" class="form-control" name="deposit_ach_bill_lname" id="deposit_ach_bill_lname"  value="">
                  <label>Last Name*</label>
                  <p class="error" id="error_deposit_ach_bill_lname"></p> 
               </div>
            </div>
            <div class="clearfix"></div>
            <div class="col-sm-6">
               <div class="form-group">
                  <input type="text" class="form-control" name="deposit_bankname"  value="">
                  <label>Bank Name*</label>
                  <p class="error" id="error_deposit_bankname"></p> 
               </div>
            </div>
            <div class="col-sm-6">
               <div class="form-group">
                  <select name="deposit_ach_account_type" id="deposit_ach_account_type" class="form-control" data-error="Account Type is required">
                     <option value=""></option>
                     <option value="checking">Checking</option>
                     <option value="savings">Saving</option>
                  </select>
                  <label>Select Account Type*</label>
                  <p class="error" id="error_deposit_ach_account_type"></p>
               </div>
            </div>
            <div class="clearfix"></div>
            <div class="col-sm-6">
               <div class="form-group">
                  <input type="text" class="form-control" name="deposit_account_number" oninput="isValidNumber(this)" maxlength='17'>
                  <label>Account Number<em>*</em></label>
                  <p class="error" id="error_deposit_account_number"></p>
               </div>
            </div>
            <div class="col-sm-6">
               <div class="form-group">
                  <input type="text" class="form-control" name="deposit_confirm_account_number" oninput="isValidNumber(this)" maxlength='17'>
                  <label>Confirm Account Number<em>*</em></label>
                  <p class="error" id="error_deposit_confirm_account_number"></p>
               </div>
            </div>
            <div class="clearfix"></div>
            <div class="col-sm-6">
               <div class="form-group">
                  <input type="text" class="form-control" name="deposit_routing_number" value="" oninput="isValidNumber(this)" maxlength='9'>
                  <label>Routing Number*</label>
                  <p class="error" id="error_deposit_routing_number"></p>
               </div>
            </div>
            <div class="col-xs-12" id="agree_to_authorize_div">
               <label class="m-b-20 m-t-25 label-input">
                  <div class="checker" id="">
                     <span>
                        <input type="checkbox" id="agree_to_authorize" name="agree_to_authorize" value="yes" class="">
                     </span>
                  </div>
                  <sapn class="p-l-2">I agree to authorize Triada to originate credit transfers to my financial institution account.</sapn>
                  <p class="error" id="error_agree_to_authorize"></p>
               </label>
            </div>
         </div>
      </div>
   </div>
   <!----------ACH-info-deposit-account--start-code------->
<?php  if($sponsor_billing_method == 'individual') { ?>
   <h5 class="m-t-0 m-b-15">Billing Information <a href="javascript:void(0);" data-toggle="tooltip" data-trigger="hover" title="Edit" data-placement="top" id="edit_billing_address"><i class="fa fa-pencil fa-lg p-l-10"></i></a></h5>
   <p class="mn"><span id="display_bill_name"></span></p>
   <p class="m-b-15">
       <span id="display_bill_address"></span> <span id="display_bill_address2"></span> <br /> <span id="display_bill_city"></span>, <span id="display_bill_state"></span> <span id="display_bill_zip"></span>
   </p>
<div style="display: none">
   <div id="billing_address_popup">
     <div class="panel panel-default panel-block panel-shadowless mn">
       <div class="panel-heading br-b">
         <h4>Billing Address - <span class="fw300"> Edit</span></h4>
       </div>
       <div class="panel-body "> 
          <div class="row theme-form">
           <div class="col-sm-6">
            <div class="form-group">
             <input type="text" name="bill_name" id="bill_name" value="" class="form-control" *>
             <label>Name*</label>
            </div>
           </div>
           <div class="clearfix"></div>
           <div class="col-sm-6">
            <div class="form-group">
             <input type="text" name="bill_address" id="bill_address" value=""
              class="required form-control tblur" *>
             <label>Address*</label>
             <span class="error" id="error_bill_address"></span></div>
           </div>
           <div class="col-sm-6">
            <div class="form-group">
             <input type="text" name="bill_address2" id="bill_address2" value=""
              class="required form-control tblur" onkeypress="return block_special_char(event)" *>
             <label>Address 2 (suite, apt)</label>
             <span class="error" id="error_bill_address2"></span></div>
           </div>
           <div class="col-sm-4">
            <div class="form-group">
             <input type="text" name="bill_city" id="bill_city"
              value="<?= !empty($billing_data['city']) ? $billing_data['city'] : '' ?>"
              class="required form-control tblur" *>
             <label>City*</label>
             <span class="error" id="error_bill_city"></span></div>
           </div>
           <div class="col-sm-4">
            <div class="form-group">
             <select id="bill_state" name="bill_state" class="tblur form-control">
               <option value=""></option>
               <?php if (count($allStateRes) > 0) { ?>
                <?php foreach ($allStateRes as $key => $value) { ?>
                 <option  value="<?= $value["name"]; ?>"><?php echo $value['name']; ?></option>
                <?php } ?>
               <?php } ?>
             </select>
             <label>State*</label>
             <span class="error" id="error_bill_state"></span></div>
           </div>
           <div class="col-sm-4">
            <div class="form-group ">
             <input type="text" name="bill_zip" id="bill_zip"
              value="<?= !empty($billing_data['zip']) ? $billing_data['zip'] : '' ?>"
              class="required form-control tblur"
              maxlength="<?php echo $bill_country == 231 ? '5' : '7'; ?>" *>
             <label>Zip/Postal Code*</label>
             <span class="error" id="error_bill_zip"></span></div>
           </div>
           <div class="col-sm-12 text-center">
              <input id="billing_save" type="button" class="btn btn-action" value="Save" />
           </div>
          </div> 
       </div>
       
     </div>
   </div>
</div>
<?php } ?>
</div>
<hr>
<?php  if($sponsor_billing_method == 'individual') { ?>
<div id="post_date_payment_div" style="display: none">
  <label class="m-b-20 m-t-25">
    <input type="checkbox" id="enroll_with_post_date" name="enroll_with_post_date" value="yes" class=""  <?=isset($future_payment) && $future_payment=='Y'?'checked':''?>>
    <sapn class="p-l-10">Set payment to occur on a future date?</sapn>
  </label>
  <div class="post_date_div" id="post_date_div" style="<?php echo checkIsset($post_date)!='' ? '' : 'display: none' ; ?>">
    <div class="row">
      <div class="col-lg-4 col-md-6">
        <div class="form-group theme-form">
          <div class="input-group">
            <div class="input-group-addon datePickerIcon" data-applyon="post_date"><i class="material-icons fs16">date_range</i></div>
            <div class="pr">
              <input type="text" class="form-control" name="post_date" value="<?=checkIsset($post_date)?>" id='post_date'>
              <label>Post Payment Date (MM/DD/YYYY)</label>
            </div>
          </div>
          <p class="error" id="error_post_date"></p>
        </div>
      </div>
    </div>
  </div>
</div>
<?php } ?>
<?php //} ?>