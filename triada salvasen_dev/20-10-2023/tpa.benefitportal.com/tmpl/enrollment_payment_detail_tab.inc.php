<div class="container">
    <div class="panel panel-default m-t-30 enroll_summary_panel">
        <div class="panel-heading">
            <h3 class="fs20 mn">Application Summary</h3>
        </div>
        <div class="panel-body">
            <div id="enrollment_summary_details_div"></div> 
        </div>
    </div>
</div>

<div class="payment_section">
	<div class="container">
    <div class="row">
      <div class="col-sm-6 col-lg-5">
        <?php if($sponsor_billing_method == 'individual'){ ?>
          <div id="enrollmentPaymentDiv">
            <h4 class="fs16 m-b-20">Payment Method</h4>
            <?php if($display_default_billing == 'Y') { ?>
              <div class="row theme-form">
                  <div class="col-sm-12">
                      <div class="form-group">
                          <select class="form-control" name="billing_profile" id="billing_profile">
                              <option data-hidden="true"></option>   
                              <?php 
                                if(isset($def_bill_row)) {
                                  foreach ($def_bill_row as $k => $billing) {
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
            <input type="hidden" name="payment_mode" id="payment_mode" value="<?= !empty($billing_data['payment_mode']) ? $billing_data['payment_mode'] : 'CC' ?>">
            <p class="error" id="error_payment_mode"></p>
            
            <div class="blue_arrow_tab">
              <ul class="nav nav-tabs nav-noscroll">
                <?php if($is_cc_accepted) { ?>
                  <li class="<?=empty($billing_data['payment_mode']) || (!empty($billing_data['payment_mode']) && $billing_data['payment_mode'] == 'CC')? 'active' : '' ?>"><a href="#credit_card" class="tabs_collapse payment_method" data-toggle="tab" data-payment-method="CC">Credit Card</a></li>
                <?php } ?>
                <?php if($is_ach_accepted) { ?>
                  <li class="<?=!empty($billing_data['payment_mode']) && $billing_data['payment_mode'] == 'ACH' ? 'active' : '' ?>"> <a href="#bank_draft" class="tabs_collapse payment_method" data-toggle="tab" data-payment-method="ACH">ACH Bank Draft</a> </li>
                <?php } ?>
              </ul>
            </div>
            <div class="tab-content left_form_tab">
              <?php if($is_cc_accepted) { ?>
                <div class="tab-pane fade <?=empty($billing_data['payment_mode']) || (!empty($billing_data['payment_mode']) && $billing_data['payment_mode'] == 'CC')? 'in active' : '' ?>" id="credit_card"> 
                	<div class="row  theme-form">
                    <div class="col-sm-12">
                      <div class="form-group">
                        <input type="text" name="name_on_card" id="name_on_card" maxlength="25"
                         value="<?= !empty($billing_data['fname']) ? $billing_data['fname'] : '' ?>"
                         class="form-control">
                         <label>Name On Card*</label>
                        <p class="error" id="error_name_on_card"></p></div>
                    </div>
                    
                    <div class="col-sm-12">
                      <div class="form-group">
                        <input type="text" name="card_number" value="" id="card_number" class="required form-control tblur" oninput="isValidNumber(this)" maxlength="16" *>
                        <label>Card Number* <span id="cc_billing_detail"><?= !empty($billing_data['card_no']) ? "(".$billing_data['card_type']." *" . $billing_data['card_no'] . ")" : '' ?></span></label>
                        <p class="error" id="error_card_number"></p>
                      </div>
                      <input type="hidden" name="full_card_number" value="<?=!empty($billing_data['card_no_full']) ? $billing_data['card_no_full'] : ''?>">
                    </div>
                    <div class="col-sm-12">
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
                      <p class="error" id="error_card_type"></p></div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group height_auto">
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
                      <div class="form-group height_auto">
                        <input name="cvv_no" type="text" value="<?=!empty($billing_data['cvv_no']) ? $billing_data['cvv_no'] : ''?>"  class="form-control " oninput="isValidNumber(this)" maxlength="4" />
                        <label>CVV*</label>
                        <p class="error" id="error_cvv_no"></p> 
                      </div>
                    </div>
                  </div>
                </div>
              <?php } ?>
              <?php if($is_ach_accepted || 1) { ?>
                <div class="tab-pane fade <?=!empty($billing_data['payment_mode']) && $billing_data['payment_mode'] == 'ACH' ? 'in active' : '' ?>" id="bank_draft">
                  	<div class="theme-form"> 
                        <div class="form-group">
                          <input type="text" class="form-control" name="ach_bill_fname" id="ach_bill_fname" value="<?= !empty($billing_data['fname']) ? $billing_data['fname'] : '' ?>">
                          <label>First Name*</label>
                          <p class="error" id="error_ach_bill_fname"></p> 
                        </div>  
                        <div class="form-group">
                          <input type="text" class="form-control" name="ach_bill_lname" id="ach_bill_lname"  value="<?= isset($billing_data['lname']) ? $billing_data['lname'] : '' ?>">
                          <label>Last Name*</label>
                          <p class="error" id="error_ach_bill_lname"></p> 
                        </div>  
                        <div class="form-group">
                          <input type="text" class="form-control" name="bankname"  value="<?= isset($billing_data['bankname'])?$billing_data['bankname']:"" ?>">
                          <label>Bank Name*</label>
                          <p class="error" id="error_bankname"></p> 
                        </div>  
                        <div class="form-group">
                          <select name="ach_account_type" id="ach_account_type" class="form-control" data-error="Account Type is required">
                             <option value=""></option>
                             <option value="checking" <?= isset($billing_data['ach_account_type']) && $billing_data['ach_account_type'] == 'checking' ? 'selected="selected"' : ''?>>Checking</option>
                             <option value="savings" <?= isset($billing_data['ach_account_type']) && $billing_data['ach_account_type'] == 'savings' ? 'selected="selected"' : ''?>>Saving</option>
                          </select>
                          <label>Select Account Type*</label>
                          <p class="error" id="error_ach_account_type"></p>
                        </div>
                        <div class="form-group">
                          <input type="text" class="form-control" name="routing_number" value="<?=!empty($billing_data['ach_routing_number']) ? $billing_data['ach_routing_number'] : ''?>" oninput="isValidNumber(this)" maxlength='9'>
                          <label>Routing Number*</label>
                          <p class="error" id="error_routing_number"></p>
                          <input type="hidden" name="entered_routing_number" id="entered_routing_number" value="<?=!empty($billing_data['ach_routing_number']) ? $billing_data['ach_routing_number'] : ''?>" maxlength='50' class="required form-control">
                        </div> 
                        <div style="<?=!empty($billing_data['ach_account_number']) ? '' : 'display:none'?>">
                          <label class="text-white">Account Number <span id="ach_billing_detail"><?= !empty($billing_data['ach_account_number']) ? "(ACH *" . substr($billing_data['ach_account_number'],-4) . ")" : '' ?></span><span class="req-indicator">*</span></label>
                        </div>
                        <div class="form-group">
                          <input type="text" class="form-control" name="account_number" oninput="isValidNumber(this)" maxlength='17'>
                          <label>Account Number*</label>
                          <p class="error" id="error_account_number"></p>
                          <input type="hidden" name="entered_account_number" id="entered_account_number" value="<?=!empty($billing_data['ach_account_number']) ? $billing_data['ach_account_number'] : ''?>" maxlength='50' class="required form-control tblur" >
                        </div>  
                        <div class="form-group">
                          <input type="text" class="form-control" name="confirm_account_number" oninput="isValidNumber(this)" maxlength='17'>
                          <label>Confirm Account Number*</label>
                          <p class="error" id="error_confirm_account_number"></p>
                        </div>
                    </div>
                </div>
              <?php } ?>
              <div class="billing_info">
                  <div class="pull-left">
                  <p class="fw500 m-b-5">Billing Information:</p>
                  <p class="mn"><span id="display_bill_name"></span></p>
                  <p class="mn">
                      <span id="display_bill_address"></span> <span id="display_bill_address2"></span> <br /> <span id="display_bill_city"></span>, <span id="display_bill_state"></span> <span id="display_bill_zip"></span>
                  </p>
                  </div>
                  <p class="error" id="error_billing_address"></p>
                  <a href="javascript:void(0)" class="edit_btn popup_md" id="edit_billing_address">Edit</a>
                  <div class="clearfix"></div>
              </div>
            </div>
            </div>
            
            <div id="post_date_payment_div" style="display: none">
              <label class="m-b-20 m-t-25 label-input">
                <input type="checkbox" id="enroll_with_post_date" name="enroll_with_post_date" value="yes" class=""  <?=isset($future_payment) && $future_payment=='Y'?'checked':''?>>
                <sapn class=" ">Set payment to occur on a future date?</sapn>
              </label>
              <div class="post_date_div" id="post_date_div" style="<?php echo checkIsset($post_date)!='' ? '' : 'display: none' ; ?>">
                <div class="row">
                  <div class="col-sm-9">
                    <div class="form-group theme-form re">
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
          </div>
        <?php } ?>
      </div>
      <div class="col-sm-6 col-lg-5 col-lg-offset-2">
        <h4 class="fs16 m-b-20 "></h4>
        <div class="pay_summary_right">
        	<table>
              <thead>
                <tr>
                  <td class="fw500">Payment Summary</td>
                  <td class="fw500 contibution_price_td text-right">Member Rate</td>
                  <td class="fw500 contibution_price_td text-right">Group Rate</td>
                  <td class="fw500 contibution_price_td text-right">Full Rate</td>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>SubTotal(s)</td>
                  <td class="contibution_price_td text-right">$<span id="summary_member_rate_sub_total">0.00</span></td>
                  <td class="contibution_price_td text-right">$<span id="summary_group_rate_sub_total">0.00</span></td>
                  <td>$<span id="summary_sub_total">0.00</span></td>
                </tr>
                <tr class="cart_healthy_step_row">
                  <td>Healthy Steps - <span id="summary_healthy_step_name"></span> <a href="javascript:void(0)" id="summary_display_healthy_step"> &nbsp; <i class="fa fa-edit"></i></a></td>
                  <td class="contibution_price_td text-right">$<span id="summary_member_rate_healthy_step_total">0.00</span></td>
                  <td class="contibution_price_td text-right">$<span id="summary_group_rate_healthy_step_total">0.00</span></td>
                  <td>$<span id="summary_healthy_step_total">0.00</span></td>

                </tr>
                <tr class="last">
                  <td>Service fee(s)</td>
                  <td class="contibution_price_td text-right">$<span id="summary_member_rate_service_fee_total">0.00</span></td>
                  <td class="contibution_price_td text-right">$<span id="summary_group_rate_service_fee_total">0.00</span></td>
                  <td>$<span id="summary_service_fee_total">0.00</span></td>
                </tr>
                <tr class="total_tr">
                  <?php if($enrollmentLocation=="groupSide" || $is_group_member == "Y"){ ?>
                    <td>Paycheck Total</td>
                  <?php }else{ ?>
                    <td>Today's Total</td>
                  <?php } ?>
                  <td class="contibution_price_td text-right">$<span id="summary_member_rate_total">0.00</span></td>
                  <td class="contibution_price_td text-right">$<span id="summary_group_rate_total">0.00</span></td>
                  <td>$<span id="summary_total">0.00</span></td>
                </tr>
              </tbody>
            </table>              
        </div>
          
        <div class="box-action">
          <table class="table mn ">
            <tbody class="text-white">
              <tr>
                <?php if($enrollmentLocation=="groupSide" || $is_group_member == "Y"){ ?>
                  <td class="fw500">Paycheck Payment</td>
                <?php }else{ ?>
                  <td class="fw500">Monthly Payment</td>
                <?php } ?>
                <td class="contibution_price_td text-right">$<span id="summary_member_rate_monthly_payment">0.00</span></td>
                <td class="contibution_price_td text-right">$<span id="summary_group_rate_monthly_payment">0.00</span></td>
                <td class="text-right">$<span id="summary_monthly_payment">0.00</span></td>
              </tr>
              <?php if($sponsor_billing_method == 'individual'){ ?>
                <tr>
                  <td class="fw500">Next Billing Date</td>
                  <td class="text-right" id="summary_next_billing_date"></td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="container">	
  <div id="member_verification_div" style="display: none">
    <h4 class="p-title m-b-15" id="verification_option_title">New Member Verification (Select One)</h4>
    <div class="clearfix"></div>
    <div id="verification_option_div"></div>
    <div id="verification_option_html_div">
      <?php 
        include_once __DIR__ . '/enrollment_payment_detail_tab_verification_option.inc.php';
      ?>
    </div>
  </div> 

  <div class="bottom_btn_wrap <?= (isset($_GET["iframe"]) ? 'pull-right' : '') ?>" >
        <div class="pull-right">
          <a href="javascript:void(0);" class="btn btn-action next_tab_button" data-step="4"> Submit</a>
          <a href="javascript:void(0);" class="btn red-link back_tab_button"> Back</a>
          <a href="javascript:void(0);" class="btn red-link exit_enrollment_button" style="display: none;"> Exit Application</a>
        </div>
      </div>
</div>