<div id="failed_renewal_age">
    <div class="bg_white">
        <div class="section_wrap">
            <div class="container">
                <div class="text-center">
                    <p class="fs32 fw300 mb20"><strong>Hello</strong>
                        <?=$cust_row['member_name']?>,</p>
                    <p class="fs16 m-b-30">Verify your DOB below to fix payment issue(s).</p>
                </div>
                <form name="frm_dob" id="frm_dob" method="POST" action="">
                    <input type="hidden" name="token" value="<?=$order_id?>">
                    <div class="row theme-form">
                        <div class="col-md-4 col-md-offset-4 col-sm-8 col-sm-offset-2 text-center">
                            <div class="form-group height_auto mn">
                                <div class="phone-control-wrap">
                                    <div class="phone-addon">
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <div class="pr">
                                                <input type="text" class="form-control" id="dob" name="dob">
                                                <label>DOB (MM/DD/YYYY)</label>
                                            </div>
                                        </div>
                                        <p class="error text-left" id="err_dob"></p>
                                    </div>
                                    <div class="phone-addon w-70 v-align-top">
                                        <button type="button" id="submit_date" class="btn btn-action btn-block">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="verification_banner" style="background-image: url(<?=$HOST?>/images/failed_renewal_member_verification_bg.jpg?_v=1.00);"></div>
        <div class="smarte_footer mn">
            <div class="container m-b-15">
                <div class="row footer_help">
                    <div class="col-xs-7">
                        <h4 class="text-action m-t-0">NEED HELP?</h4>
                        <p class="mn need_help"><span>
                                <?=$user_name?></span> <span>
                                <?=format_telephone($display_phone_number);?> </span> <span>
                                <?=$display_email?> </span></p>
                    </div>
                    <div class="col-xs-5 mn text-right">
                        <div class="powered_by_logo">
                            <img src="<?=$image_url?>" height="43px" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="bottom_footer ">
                <div class="container">
                    <ul>
                        <li>
                            <?= $DEFAULT_SITE_NAME ?> &copy;
                            <?php echo date('Y')?>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="bg_white">
    <div id="failed_renewal_dashboard" style="display:none">
        <div class="verification_header">
        </div>
        <div class="verification_sub_header">
            <div class="container">
                <div class="row">
                    <div class="col-xs-7">
                        <h4 class="mn fs20">Welcome <span class="fw300 text-action">
                                <?=$cust_row['member_name']?></span></h4>
                    </div>
                    <div class="col-xs-5 text-right">
                    </div>
                </div>
            </div>
        </div>
        <div class="section_wrap enrollment_receipt">
            <div class="container">
                <div class="transaction_receipt">
                    <div class="row bg_white">
                        <div class="col-sm-3 receipt_left">
                            <div class="bg_dark_primary">
                                <div class="panel-body">
                                    <h4 class="text-uppercase m-t-0 fs14 m-b-5" style="color: rgba(255,255,255,0.39);">MEMBER</h4>
                                    <h4 class="text-white mn">
                                        <?=checkIsset($resOrder["mbrName"])?>
                                    </h4>
                                    <p class="text-white mn">
                                        <?=checkIsset($resOrder["mbrDispId"])?><br>
                                        <?=format_telephone(checkIsset($resOrder["mbrPhone"]))?><br>
                                        <?=checkIsset($resOrder["mbrEmail"])?>
                                    </p>
                                </div>
                            </div>
                            <div class="panel-body">
                                <h4 class="text-uppercase m-t-0 fs14 m-b-5" style="color: rgba(255,255,255,0.39);">BILLING ADDRESS</h4>
                                <p class="text-white mn">
                                    <?=checkIsset($resOrder["billAdd"])?>
                                    <br />
                                    <?php if(!empty($resOrder["billAdd2"])){
                                 echo $resOrder["billAdd2"]; ?>
                                    <br />
                                    <?php } ?>
                                    <?=checkIsset($resOrder["billCity"])?>,
                                    <?=checkIsset($allStateShortName[$resOrder["billState"]])?>
                                    <br />
                                    <?=checkIsset($resOrder["billZip"])?>
                                </p>
                            </div>
                            <div class="panel-body">
                                <h4 class="text-uppercase m-t-0 fs14 m-b-5" style="color: rgba(255,255,255,0.39);">Payment</h4>
                                <p class="text-white mn">
                                    <?=displayAmount($grandTotal)?> (
                                    <?=checkIsset($billType)?> *
                                    <?=checkIsset($resOrder["lastPayNo"])?>)</p>
                            </div>
                            <div class="panel-body">
                                <h4 class="text-uppercase m-t-0 fs14 m-b-5" style="color: rgba(255,255,255,0.39);">TRANSACTION INFO</h4>
                                <p class="text-white mn">
                                    <?=checkIsset($transactionId)?><br>
                                    <?=checkIsset($transactionDate)?>
                                </p>
                            </div>
                            <div class="panel-body">
                                <h4 class="text-uppercase m-t-0 fs14 m-b-5" style="color: rgba(255,255,255,0.39);">ORDER ID</h4>
                                <p class="text-white mn">
                                    <?=$orderDispId?>
                                </p>
                            </div>
                        </div>
                        <div class="col-sm-9 bg_white receipt_right">
                            <div class="p-10">
                                <div class="clearfix m-b-15 m-t-15">
                                    <div class="pull-left">
                                        <p class="fw500 mn text-action">
                                            <?=$orderStatus?> <i class="fa fa-times-circle" aria-hidden="true"></i></p>
                                        <p class="text-gray fs10">
                                            <?=$tz->getDate($resOrder["odrDate"])?>
                                        </p>
                                    </div>
                                    <div class="pull-right">
                                        <h4 class="text-action">RECEIPT</h4>
                                    </div>
                                </div>
                                <p class="m-b-15"><strong class="text-action">Reason : </strong> Card Declined.</p>
                                <p class="clearfix m-b-15 fw600">Summary</p>
                                <div class="table-responsive">
                                    <table class="table table-borderless table-striped table-action">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Coverage Period</th>
                                                <th>Coverage</th>
                                                <th class="text-right">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                    if(!empty($detRes)){
                                      $fee_prd_res = array();
                                      foreach ($detRes as $key => $order) { 
                                         if($order["type"] == 'Fees'){
                                             if($order["product_type"] == "Healthy Step"){
                                               $stepFeePrice = $order["price"];
                                               $stepFeeRefund= $order["is_refund"];
                                               continue;
                                             }
                                             if($order["product_type"] == "ServiceFee"){
                                               $serviceFeePrice = $order["price"];
                                               $serviceFeeRefund = $order["is_refund"];
                                               continue;
                                             }
                                             $fee_prd_res[] = $order;
                                             continue;
                                           }
                                      ?>
                                            <tr>
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
                                                    <?=displayAmount($order["price"])?>
                                                </td>
                                            </tr>
                                            <?php }
                                    foreach ($fee_prd_res as $key => $fee_prd_row) {
                                      ?>
                                            <tr>
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
                                    } ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="pull-right receipt_total">
                                    <table class="table table-borderless receipt_table">
                                        <tbody>
                                            <tr>
                                                <td>SubTotal(s)</td>
                                                <td class="text-right">
                                                    <?=displayAmount($subTotal)?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Healthy Step(s)</td>
                                                <td class="text-right">
                                                    <?=displayAmount($stepFeePrice)?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Service Fee(s)</td>
                                                <td class="text-right">
                                                    <?=displayAmount($serviceFeePrice)?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="fw500">Total</td>
                                                <td class="text-right fw500">
                                                    <?=displayAmount($grandTotal)?>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="m-t-30 m-b-30 theme-form">
            <form id="frm_billing" name="frm_billing" method="POST">
                <input type="hidden" name="order_id" value="<?=$order_id?>">
                <input type="hidden" name="cb_profile_id" id="cb_profile_id" value="<?=checkIsset($billing_data['id'])?>">
                <div class="container">
                    <div class="payment_section row">
                        <div class="col-md-4">
                            <h4 class="fs16 m-b-20">Select Payment Method</h4>
                            <p class="error" id="error_payment_mode"></p>
                            <div class="blue_arrow_tab">
                                <ul class="nav nav-tabs nav-noscroll">
                                    <?php if($is_cc_accepted == true || $billing_data['payment_mode'] == "CC") { ?>
                                    <li class="<?=$billing_data['payment_mode'] == 'CC' ? 'active' : '' ?>"><a href="#credit_card" class="tabs_collapse" data-mode="CC" data-toggle="tab">Credit Card</a></li>
                                    <?php } ?>
                                    <?php if($is_ach_accepted == true || $billing_data['payment_mode'] == "ACH") { ?>
                                    <li class="<?=$billing_data['payment_mode'] == 'ACH' ? 'active' : '' ?>"> <a href="#bank_draft" class="tabs_collapse" data-mode="ACH" data-toggle="tab">ACH Bank Draft</a> </li>
                                    <?php } ?>
                                </ul>
                            </div>
                            <div class="tab-content left_form_tab">
                                <input type="hidden" name="payment_mode" id="payment_mode" value="<?=checkIsset($billing_data['payment_mode'])?>">
                                <?php if($is_cc_accepted == true || $billing_data['payment_mode'] == "CC") { ?>
                                <div class="tab-pane fade <?=$billing_data['payment_mode'] == 'CC' ? 'in active' : '' ?>" id="credit_card">
                                    <div class="row  theme-form">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <input type="text" class="form-control" name="name_on_card" id="name_on_card" value="<?=trim(checkIsset($billing_data['fname']).' '.checkIsset($billing_data['lname']))?>">
                                                <label>Name On Card*</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <input type="text" class=" form-control" name="card_number" id="card_number" maxlength="16" value="" oninput="isValidNumber(this)">
                                                <label>Card Number <span id="cc_billing_detail">
                                                        <?= !empty($billing_data['card_no']) ? "(".$billing_data['card_type']." *" . $billing_data['card_no'] . ")" : '' ?></span></label>
                                                <input type="hidden" name="full_card_number" value="<?=$billing_data['card_no_full']?>">
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <select name="card_type" class="form-control" id="card_type">
                                                    <?php if(!empty($payment_res['acceptable_cc'])) {
                                                  $payment_master_res = explode(',',$payment_res['acceptable_cc']);
                                                ?>
                                                    <?php foreach($payment_master_res as $ctype) : ?>
                                                    <option value="<?=$ctype?>" <?=!empty($billing_data['card_type']) && $billing_data['card_type']==$ctype ? 'selected="selected"' : '' ?> >
                                                        <?= ucwords(str_replace('_',' ',$ctype))?>
                                                    </option>
                                                    <?php endforeach;?>
                                                    <?php }else{ ?>
                                                    <option value="Visa" <?=!empty($billing_data['card_type']) && $billing_data['card_type']=='Visa' ? 'selected="selected"' : '' ?>> Visa</option>
                                                    <option value="MasterCard" <?=!empty($billing_data['card_type']) && $billing_data['card_type']=='MasterCard' ? 'selected="selected"' : '' ?>> MasterCard </option>
                                                    <option value="Discover" <?=!empty($billing_data['card_type']) && $billing_data['card_type']=='Discover' ? 'selected="selected"' : '' ?>> Discover </option>
                                                    <option value="Amex" <?=!empty($billing_data['card_type']) && $billing_data['card_type']=='Amex' ? 'selected="selected"' : '' ?>> American Express </option>
                                                    <?php } ?>
                                                </select>
                                                <label>Card Type*</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group height_auto m-b-15">
                                                        <?php
                                                     $date = '';
                                                     if(!empty($billing_data['expiry_month']) && !empty($billing_data['expiry_year'])){
                                                        $date = date('m/y',strtotime($billing_data['expiry_month']."/01/".$billing_data['expiry_year']));
                                                     }
                                                     ?>
                                                        <input type="text" name="expiration" id="expiration" value="<?=$date?>" class="form-control">
                                                        <label>Expiration Date*</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group height_auto m-b-15">
                                                        <input type="text" name="cvv" id="cvv" oninput="isValidNumber(this)" minlength="3" maxlength="4" value="<?=checkIsset($billing_data['cvv_no'])?>" class="form-control">
                                                        <label>CVV*</label>
                                                    </div>
                                                    <input type="hidden" name="require_cvv" value="<?=$payment_res['require_cvv'] == 'Y' ? 'yes' : 'no'?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
                                <?php if($is_ach_accepted == true || $billing_data['payment_mode'] == "ACH") { ?>
                                <div class="tab-pane fade <?=$billing_data['payment_mode'] == 'ACH' ? 'in active' : '' ?>" id="bank_draft">
                                    <div class="theme-form">
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="ach_name" id="ach_name" value="<?=checkIsset($billing_data['bankname'])?>">
                                            <label>Bank Name*</label>
                                        </div>
                                        <div class="form-group">
                                            <select class="form-control <?=!empty($billing_data['ach_account_type']) ? 'has-value' : ''; ?>" name="account_type" id="account_type">
                                                <option value="checking" <?=(!empty($billing_data['ach_account_type']) && $billing_data['ach_account_type']=='checking' ) ? 'selected="selected"' : '' ?>>Checking</option>
                                                <option value="savings" <?=(!empty($billing_data['ach_account_type']) && $billing_data['ach_account_type']=='savings' ) ? 'selected="selected"' : '' ?>>Saving</option>
                                            </select>
                                            <label>Account Type*</label>
                                        </div>
                                        <div style="<?=!empty($billing_data['ach_routing_number']) ? '' : 'display:none'?>">
                                            <label class="text-white" id="routing_number_detail">Routing Number
                                                <?= !empty($billing_data['ach_routing_number']) ? "(***" . substr($billing_data['ach_routing_number'],-4) . ")" : '' ?>
                                                <span class="req-indicator">*</span></label>
                                        </div>
                                        <div class="form-group">
                                            <input type="text" class="form-control" value="" id="routing_number" name="routing_number" maxlength='9' oninput="isValidNumber(this)">
                                            <label>Routing Number*</label>
                                            <input type="hidden" name="entered_routing_number" id="entered_routing_number" value="<?=checkIsset($billing_data['ach_routing_number'])?>" maxlength='50' class="required form-control tblur">
                                        </div>
                                        <div style="<?=!empty($billing_data['ach_account_number']) ? '' : 'display:none'?>">
                                            <label class="text-white">Account Number <span id="ach_billing_detail">
                                                    <?= !empty($billing_data['ach_account_number']) ? "(ACH *" . substr($billing_data['ach_account_number'],-4) . ")" : '' ?></span><span class="req-indicator">*</span></label>
                                        </div>
                                        <div class="form-group">
                                            <input type="text" class="form-control" id="account_number" name="account_number" value="" oninput="isValidNumber(this)" maxlength="17">
                                            <label>Account Number*</label>
                                            <input type="hidden" name="entered_account_number" id="entered_account_number" value="<?=checkIsset($billing_data['ach_account_number'])?>" maxlength='50' class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="confirm_account_number" id="confirm_account_number" oninput="isValidNumber(this)" maxlength="17">
                                            <label>Confirm Account Number*</label>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
                                <div class=" billing_info hidden">
                                    <h5 class="m-t-0 text-white mn">Billing Address</h5>
                                    <p class="text-white mn">
                                        <span>
                                            <?=checkIsset($billing_data['fname'])?>
                                            <?=checkIsset($billing_data['lname'])?></span><br>
                                        <span>
                                            <?=checkIsset($billing_data['address'])?></span><span>
                                            <?=checkIsset($billing_data['address2'])?></span><br>
                                        <span>
                                            <?=checkIsset($billing_data['city'])?></span>,
                                        <span>
                                            <?=checkIsset($billing_data['state'])?></span>
                                        <span>
                                            <?=checkIsset($billing_data['zip'])?></span><br>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <h4 class="fs16 m-b-20 ">Billing Address</h4>
                            <div class="m-b-30">
                                <label class="mn"><input type="checkbox" value="" id="same_as_personal" <?php echo $same_as_personal ? 'checked' : '' ; ?>>Same as primary plan holder?</label>
                            </div>
                            <div class="row theme-form bill_info">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <input type="text" name="bill_fname" id="bill_fname" value="<?=checkIsset($billing_data['fname']).' '.checkIsset($billing_data['lname'])?>" class="form-control">
                                        <label>Full Name*</label>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <input type="text" name="bill_address" id="bill_address" value="<?=checkIsset($billing_data['address'])?>" class="form-control">
                                        <label>Address</label>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <input type="text" name="bill_address2" id="bill_address2" value="<?=checkIsset($billing_data['address2'])?>" class="form-control" onkeypress="return block_special_char(event)">
                                        <label>Address 2 (suite, apt)</label>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <input type="text" name="bill_city" id="bill_city" value="<?=checkIsset($billing_data['city'])?>" class="form-control">
                                        <label>City*</label>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <select class="form-control" id="bill_state" name="bill_state" title="&nbsp;">
                                            <option value="" selected disable hidden></option>
                                            <?php if(!empty($allStateRes)){ 
                                                foreach($allStateRes as $state){
                                        ?>
                                            <option value="<?=$state['name']?>" <?=checkIsset($billing_data['state'])==$state['name'] ? 'selected' : '' ;?>>
                                                <?=$state['name']?>
                                            </option>
                                            <?php } } ?>
                                        </select>
                                        <?php ?>
                                        <label>State*</label>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <input type="text" name="bill_zip" id="bill_zip" class="form-control" value="<?=checkIsset($billing_data['zip'])?>">
                                        <label>Zip Code*</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-info" id="btn_charge">Charge</button>
                    <a href="<?=$HOST?>" class="btn red-link">Cancel</a>
                </div>
            </form>
        </div>
        <div class="smarte_footer mn">
            <div class="container m-b-15">
                <div class="row footer_help">
                    <div class="col-xs-7">
                        <h4 class="text-action m-t-0">NEED HELP?</h4>
                        <p class="mn need_help"><span>
                                <?=$user_name?></span> <span>
                                <?=format_telephone($display_phone_number);?> </span> <span>
                                <?=$display_email?> </span></p>
                    </div>
                    <div class="col-xs-5 mn text-right">
                        <div class="powered_by_logo">
                            <img src="<?=$image_url?>" height="43px" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="bottom_footer ">
                <div class="container">
                    <ul>
                        <li>
                            <?= $DEFAULT_SITE_NAME ?> &copy;
                            <?php echo date('Y')?>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<div style='display:none'>
    <div id='enroll_popup' class="panel panel-default mn panel-shadowless">
        <div class="panel-body login-alert-modal">
            <div class="media br-n pn mn">
                <div class="media-left"> <img src="<?=$ADMIN_HOST?>/images/<?= $DEFAULT_LOGO_IMAGE ?>" align="left"> </div>
                <div class="media-body theme-form">
                    <h3 class="text-action m-t-n fw600">Uh Oh!</h3>
                    <p id="error_text" style="display:none">Incomplete payment Information, please update your payment details to process payment.</p>
                    <p id="error_response"></p>
                </div>
                <div class="text-center">
                    <a href="javascript:void(0)" class="btn btn-action" id="update_billing" style="display:none">Update Billing</a>
                    <a href="javascript:void(0);" onclick='parent.$.colorbox.close(); return false;' class="btn red-link">Close</a>
                </div>
            </div>
        </div>
    </div>
    <div class="panel-body login-alert-modal" id="successWindow">
        <div class="media br-n pn mn">
            <div class="media-left"> <img src="<?= $AGENT_HOST ?>/images/<?= $DEFAULT_LOGO_IMAGE ?>" align="left"> </div>
            <div class="media-body theme-form">
                <h3 class="blue-link m-t-n fw600 fs24 m-b-10">Success!</h3>
                <p class="m-b-20">Thank you for your payment.<br>Click the login button below to visit your portal.</p>
            </div>
            <div class="text-center">
                <a href="<?= $CUSTOMER_HOST ?>" class="btn btn-info confirm">Login</a>
                <a href="<?= $HOST ?>" class="btn red-link pn">Exit</a>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
$(document).ready(function() {
    $(window).keydown(function(event) {
        if (event.keyCode == 13) {
            event.preventDefault();
            return false;
        }
    });

    /*--- DOB Validations ---*/
    $('#dob').mask('99/99/9999');
    $(document).off('click', '#submit_date');
    $(document).on('click', '#submit_date', function(e) {
        e.stopImmediatePropagation();
        $('#ajax_loader').show();
        var params = $('#frm_dob').serialize();
        $.ajax({
            url: '<?=$HOST?>/failed_renewal.php',
            type: 'POST',
            data: params,
            dataType: 'JSON',
            success: function(res) {
                if (res.status == 'success') {
                    $('#is_ajax').val('');
                    $('#ajax_loader').hide();
                    $("#failed_renewal_dashboard").show();
                    $("#failed_renewal_age").hide();

                } else {
                    $('#ajax_loader').hide();
                    var is_error = true;
                    $.each(res.errors, function(index, error) {
                        console.log('#err_' + index);
                        $('#err_' + index).html(error);
                        if (is_error) {
                            var offset = $('#err_' + index).offset();
                            if (typeof(offset) === "undefined") {
                                console.log("Not found : " + index);
                            } else {
                                var offsetTop = offset.top;
                                var totalScroll = offsetTop - 195;
                                $('body,html').animate({
                                    scrollTop: totalScroll
                                }, 1200);
                                is_error = false;
                            }
                        }
                    });
                }
            }
        });
    });
    /*---/DOB Validations ---*/

    if($("#bill_state").val() !== '' && $("#bill_state").val() !== undefined){
        $("#bill_state").addClass('has-value');
    }
    $("#bill_zip").mask("99999");
    $('#expiration').datepicker({
        format: 'mm/yy',
        startView : 1,
        minViewMode: 1,
        autoclose: true,  
        startDate:new Date(),
        endDate : '+15y'
    });

    $(document).off('click', '#same_as_personal');
    $(document).on('click', '#same_as_personal', function () {
        if ($(this).is(":checked")) {
          $("#bill_fname").val("<?=checkIsset($cust_row['fname']).' '.checkIsset($cust_row['lname'])?>");
          $("#bill_address").val("<?=checkIsset($cust_row['address'])?>");
          $("#bill_address2").val("<?=checkIsset($cust_row['address_2'])?>");
          $("#bill_city").val("<?=checkIsset($cust_row['city'])?>");
          $("#bill_state").val("<?=checkIsset($cust_row['state'])?>").change();
          $("#bill_zip").val("<?=checkIsset($cust_row['zip'])?>");
          $(".bill_info input").addClass('has-value');
        } else {
          $(".bill_info input").removeClass('has-value');
          $("#bill_fname").val('');
          $("#bill_address").val('');
          $("#bill_address2").val('');
          $("#bill_city").val('');
          $("#bill_state").val('').change();
          $("#bill_zip").val('');
        }
    });

    $(document).off('click','.tabs_collapse');
    $(document).on('click','.tabs_collapse',function(){
        var $mode = $(this).attr('data-mode');
        $("#payment_mode").val($mode);
    });

    $(document).off('click', '#btn_charge');
    $(document).on('click', '#btn_charge', function(e) {
        e.preventDefault();
        $('#ajax_loader').show();
        $(".error").html("");
        var params = $('#frm_billing').serializeArray();
        $.ajax({
            url: '<?=$HOST?>/ajax_failed_renewal_charge.php',
            type: 'POST',
            data: params,
            dataType: 'JSON',
            success: function(res) {
                $('#ajax_loader').hide();
                if (res.status == 'payment_success') {
                    $.colorbox({
                        href: "#successWindow",
                        inline: true,
                        width: "470px;",
                        height: "200px",
                        closeButton: false,
                        fixed: true,
                        overlayClose: false,
                        onComplete: function(e) {
                            if ($(window).width() <= 767) {
                                parent.$.colorbox.resize({
                                    width: "470px",
                                    height: "245px"
                                });
                            }
                        }
                    });
                } else if (res.status == 'payment_fail') {
                    $('#error_response').html(res.payment_error);
                    $.colorbox({
                        href: "#enroll_popup",
                        inline: true,
                        width: "530px;",
                        height: "267px;",
                    });
                } else if (res.status == 'order_not_found') {
                    window.location.reload();

                } else if (res.status == 'application_already_submitted') {
                    window.location.reload();

                } else {
                    $.each(res.errors,function(key,error){
                        $("#"+key).parents('.form-group').append("<span class='error'>"+error+"</span>");
                        $("#error_"+key).html(error).show();
                    });
                }
            }
        });
    });

    $('.receipt_right').matchHeight({
        target: $('.receipt_left')
    });
});
</script>