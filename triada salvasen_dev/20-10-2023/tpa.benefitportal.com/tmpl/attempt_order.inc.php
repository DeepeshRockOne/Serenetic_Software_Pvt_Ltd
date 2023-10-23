<div class="panel panel-default">
    <div class="panel-heading">
        <h4 class="mn">Billing Information</h4>
    </div>
    <form action="" id="form_attempt_order" name="form_attempt_order">
        <input type="hidden" name="order_id" value="<?=$order_row['id']?>">
        <input type="hidden" name="customer_id" value="<?=$order_row['customer_id']?>">
        <input type="hidden" name="lead_id" value="<?=$lead_id?>">
        <input type="hidden" name="location" id="location" value="<?=$location?>">
        <input type="hidden" name="cb_profile_id" id="cb_profile_id" value="<?=checkIsset($billing_data['id'])?>">

        <div class="panel-body">
            <div class="payment_section mn pn">
                <div class="row">
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
                                        <input type="text" class="form-control" id="account_number" name="account_number" maxlength="17" value="" oninput="isValidNumber(this)">
                                        <label>Account Number*</label>
                                        <input type="hidden" name="entered_account_number" id="entered_account_number" value="<?=checkIsset($billing_data['ach_account_number'])?>" maxlength='50' class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="confirm_account_number" maxlength="17" id="confirm_account_number" oninput="isValidNumber(this)">
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
                            <label class="mn"><input type="checkbox" value="" id="same_as_personal" <?php echo $same_as_personal ? 'checked' : '' ; ?>>Same as primary policy holder?</label>
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
                <?php if(strtotime(date("m/d/Y",strtotime('+1 day'))) < strtotime($coverge_effective_date)) { ?>
                <div class="row theme-form m-t-30">
                    <div class="col-md-4">
                        <input type="hidden" name="enroll_with_post_date" value="yes">
                        <div class="post_date_div">
                            <span class="pull-right m-b-10">Select today’s date to run payment immediately</span>
                            <div class="clearfix"></div>
                            <div class="form-group">
                              <div class="input-group">
                                  <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                  <div class="pr">
                                      <input type="text" name="post_date" id="post_date" value="<?=checkIsset($post_date)?>" class="<?=$class?> form-control" id="" />
                                      <label>Payment Date (MM/DD/YYYY)</label>
                                  </div>
                              </div>
                              <a href="javascript:void(0);" class="btn red-link pull-right" id="cancel_post_date" style="background-color: unset;">Cancel Application</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } else { ?>
                <input type="hidden" name="enroll_with_post_date" value="yes">
                <input type="hidden" name="post_date" value="<?=date('m/d/Y');?>">
                <?php } ?>
            </div>
        </div>
        <div class="panel-footer text-center">
            <button type="button" class="btn btn-action" id="submit_attempt_order">Save</button>
            <a href="javascript:void(0)" onclick="parent.$.colorbox.close(); return false;" class="btn red-link">Cancel</a>
        </div>
    </form>
</div>
<script type="text/javascript">
$(document).ready(function(){
    if($("#bill_state").val() !== '' && $("#bill_state").val() !== undefined){
        $("#bill_state").addClass('has-value');
    }

    $("#post_date").datepicker({
        startDate: "<?=date("m/d/Y")?>",
        endDate: "<?=date("m/d/Y",strtotime('-1 day',strtotime($coverge_effective_date)))?>",
        orientation: "bottom",
        enableOnReadonly: true
    });

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

    $(document).off('click','#submit_attempt_order');
    $(document).on('click','#submit_attempt_order',function(){
        submitForm();
    });

    $(document).off('click','.tabs_collapse');
    $(document).on('click','.tabs_collapse',function(){
        var $mode = $(this).attr('data-mode');
        $("#payment_mode").val($mode);
    });

    $(document).off('click','.btn-select');
    $(document).on('click','.btn-select',function(){
        $('.bootstrap-select.bs-container').css('z-index',999999);
    });

    $(document).off('click', "#cancel_post_date");
    $(document).on('click', "#cancel_post_date", function() {
        parent.swal({
          text: "Cancel Application: Are you sure?",
          showCancelButton: true,
          confirmButtonText: "Confirm",
        }).then(function() {
            $("#ajax_loader").show();
            $.ajax({
                url: '<?=$HOST?>/ajax_cancel_post_date.php?lead_detail_page',
                type: 'Post',
                data: {order_id:'<?=md5($order_id)?>',location:"<?=$location?>",lead_id:"<?=$lead_id?>"},
                dataType: 'JSON',
                success: function(res) {
                    $("#ajax_loader").hide();
                    if(res.status == 'success'){
                        parent.window.location.reload();
                    }else if (res.status == 'not_found') {
                        parent.window.location.reload();
                    }
                }
            });
        }, function(dismiss) {
          
        });
    });
});
function scrollToElement(e) {
    var offset = $(e).offset();
    var offsetTop = offset.top;
    var totalScroll = offsetTop - 200;
    $('body,html').animate({
        scrollTop: totalScroll
    }, 1200);
}
function submitForm(){
    $(".error").html("");
    $("#submit_attempt_order").hide();
    var $data = $("#form_attempt_order").serializeArray();
    $.ajax({
        url:"<?=$HOST?>/ajax_attempt_order.php",
        type:"post",
        data:$data,
        dataType:"json",
        beforeSend :function(){
            $("#ajax_loader").show();
        },
        success:function(res) {
            $("#ajax_loader").hide();
            if(typeof(res.order_not_found) !== "undefined") {
                parent.window.location.reload();
                return false;
            }
            
            if(res.status == 'success'){
                parent.window.location.reload();
            } else if(res.status == 'application_already_submitted'){
                parent.window.location.reload();
                return false;                
            } else {
                $.each(res.errors,function(key,error){
                    $("#"+key).parents('.form-group').append("<span class='error'>"+error+"</span>");
                    $("#error_"+key).html(error).show();
                });
                $("#submit_attempt_order").show();
            }
        }
    });
}
</script>