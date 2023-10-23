<div class="panel panel-default">
    <div class="panel-heading">
        <h4 class="mn">Manage Payment - <span class="fw300"><?=$row['name'];?></span></h4>
    </div>
    <div class="panel-body">
        <form action="" name="frm_auto_draft_setting" id="frm_auto_draft_setting" method="POST">
            <input type="hidden" name="group_id" id="group_id" value="<?=$group_id_org;?>">
            <input type="hidden" name="company_id" id="company_id" value="<?=$company_id_org;?>">
            <input type="hidden" name="company_name" id="company_name" value="<?=$row['name'];?>">
            <input type="hidden" name="is_address_ajaxed" id="is_address_ajaxed" value="">
            <div class="theme-form">
                <p><strong>Select an option below to setup payments:</strong></p>
                <div class="m-b-25">
                    <?php if($is_ach == "Y") { ?>
                    <div class="m-b-10">
                        <label class="mn">
                            <input type="radio" name="payment_mode" class="chk_payment_mode" value="ACH" <?=!empty($row['payment_mode']) && $row['payment_mode']=="ACH" ? 'checked' : '' ?>> ACH
                        </label>
                    </div>
                    <?php } ?>

                    <?php if($is_cc == "Y") { ?>
                    <div class="m-b-10">
                        <label class="mn">
                            <input type="radio" name="payment_mode" class="chk_payment_mode" value="CC" <?=!empty($row['payment_mode']) && $row['payment_mode']=="CC" ? 'checked' : '' ?>> CC
                        </label>
                    </div>
                    <?php } ?>

                    <?php if($is_check == "Y") { ?>
                    <div class="mn">
                        <label class="mn">
                            <input type="radio" name="payment_mode" class="chk_payment_mode" value="Check" <?=!empty($row['payment_mode']) && $row['payment_mode']=="Check" ? 'checked' : '' ?>> Check
                        </label>
                    </div>
                    <?php } ?>
                    <p class="error" id="error_payment_mode"></p>
                </div>

                <div class="row m-b-20 auto_draft_date_section" style="display: none;">
                    <div class="col-sm-6 billing_profile_section" style="display: none;">
                        <p>Select Payment Method</p>
                        <div class="form-group">
                            <select name="billing_profile" id="billing_profile" class="form-control">
                                <option data-hidden="true"></option>
                            </select>
                            <label>Select Payment Method</label>
                            <p class="error" id="error_billing_profile"></p>
                            <a href="javascript:void(0);" class="btn_add_billing_profile text-action pull-right fw500 fs12 m-t-10">+ Add Payment Method</a>
                        </div>
                    </div>
                </div>

                <div class="ach_payment_section" style="display: none;">
                    <p><strong>Bank Draft Information</strong></p>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <select class="form-control" name="ach_account_type" id="ach_account_type">
                                    <option value=""></option>
                                    <option value="checking">Checking</option>
                                    <option value="savings">Saving</option>
                                </select>
                                <label>Account Type<em>*</em></label>
                                <p class="error" id="error_ach_account_type"></p>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <input type="text" name="ach_name" class="form-control">
                                <label>Full Name<em>*</em></label>
                                <p class="error" id="error_ach_name"></p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <input type="text" name="bankname" class="form-control">
                                <label>Bank Name<em>*</em></label>
                                <p class="error" id="error_bankname"></p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <input type="text" name="routing_number" class="form-control" value="" maxlength='9' oninput="isValidNumber(this)">
                                <label>Bank Routing Number<em>*</em></label>
                                <p class="error" id="error_routing_number"></p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <input type="text" name="account_number" class="form-control" value="" maxlength='17' oninput="isValidNumber(this)">
                                <label>Bank Account Number<em>*</em></label>
                                <p class="error" id="error_account_number"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="cc_payment_section" style="display: none;">
                    <p><strong>Credit Card Information</strong></p>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <input type="text" name="name_on_card" id="name_on_card" class="form-control">
                                <label>Full Name<em>*</em></label>
                                <p class="error" id="error_name_on_card"></p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <select name="card_type" id="card_type" class="form-control" required="" data-error="Card Type is required">
                                    <option value=""></option>
                                    <?php if(in_array('Visa', $acceptable_cc)){ ?>
                                    <option value="Visa"> Visa </option>
                                   <?php } ?>
                                   <?php if(in_array('MasterCard', $acceptable_cc)){ ?>
                                    <option value="MasterCard"> MasterCard </option>
                                    <?php } ?>
                                   <?php if(in_array('Discover', $acceptable_cc)){ ?>
                                    <option value="Discover"> Discover </option>
                                    <?php } ?>
                                   <?php if(in_array('Amex', $acceptable_cc)){ ?>
                                    <option value="Amex"> American Express </option>
                                    <?php } ?>
                                </select>
                                <label>Card Type<em>*</em></label>
                                <p class="error" id="error_card_type"></p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <input name="card_number" id="card_number" type="text" class="form-control" required="" value="" maxlength="16" oninput="isValidNumber(this)">
                                <label>Card Number<em>*</em></label>
                                <p class="error" id="error_card_number"></p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <input type="text" name="expiration" id="expiration" class="form-control">
                                <label>Expiration Date<em>*</em></label>
                                <p class="error" id="error_expiration"></p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group height_auto">
                                <input type="text" name="cvv_no" id="cvv_no" oninput="isValidNumber(this)" minlength="3" maxlength="4" class="form-control">
                                <label>CVV<em>*</em></label>
                                <p class="error" id="error_cvv_no"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="m-b-20 billing_address_section" style="display: none;">
                    <p><strong>Billing Address</strong></p>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <input type="text" name="bill_address" id="bill_address" class="form-control" >
                                <label>Address<em>*</em></label>
                                <p class="error" id="error_bill_address"></p>
                                <input type="hidden" name="old_bill_address" id="old_bill_address" value="">
                            </div>
                        </div>
                        <div class="col-sm-6">
                          <div class="form-group">
                            <input type="text" class="form-control" name="bill_address_2" id="bill_address_2" onkeypress="return block_special_char(event)" />
                            <label>Address 2 (suite, apt)</label>
                            <p class="error" id="error_bill_address_2"></p>
                          </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <input type="text" name="bill_city" class="form-control" id="bill_city" >
                                <label>City<em>*</em></label>
                                <p class="error" id="error_bill_city"></p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <select class="form-control" name="bill_state" id="bill_state">
                                    <option data-hidden="true"></option>
                                    <?php if(!empty($allStateRes)){
                                    foreach($allStateRes as $states){ ?>
                                    <option value="<?=$states['name']?>">
                                        <?=$states['name']?>
                                    </option>
                                    <?php } } ?>
                                </select>
                                <label>State<em>*</em></label>
                                <p class="error" id="error_bill_state"></p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <input type="text" name="bill_zip" maxlength="5" id="bill_zip" class="form-control" oninput="isValidNumber(this)" value="">
                                <label>Zip/Postal Code<em>*</em></label>
                                <p class="error" id="error_bill_zip"></p>
                                <input type="hidden" name="old_bill_zip" id="old_bill_zip" value="">
                            </div>
                        </div>
                    </div>
                    <div class="text-center">
                        <a href="javascript:void(0);" class="btn btn-action btn_save_billing_profile">Save</a>
                        <a href="javascript:void(0);" class="btn red-link btn_cancel_add_billing_profile">Cancel</a>
                    </div>
                </div>
                <div class="row m-b-20 check_payment_section" style="display: none;">
                    <div class="col-sm-4">
                        <p><strong>Please Remit Payment to:</strong></p>
                    </div>
                    <div class="col-sm-8">
                        <p><?=!empty($pay_options['remit_to_address'])?nl2br($pay_options['remit_to_address']):''?></p>
                    </div>
                </div>

                <?php if(!empty($pay_options)) { ?>
                    <?php if($pay_options['cc_additional_charge'] == "Y" && !empty($pay_options['cc_charge'])) { ?>
                    <div class="bg_light_gray cc_charge_section text-center p-20" style="display: none;">
                        <p class="text-gray mn"><?=$pay_options['cc_charge_type'] == "Fixed"?displayAmount($pay_options['cc_charge']):$pay_options['cc_charge']."%"?> service charge will be applied if you pay by CC</p>
                    </div>
                    <?php } ?>

                    <?php if($pay_options['check_additional_charge'] == "Y"){ ?>
                    <div class="bg_light_gray check_charge_section text-center p-20" style="display: none;">
                        <p class="text-gray mn"><?=displayAmount($pay_options['check_charge'])?> service charge will be applied if you pay by Check</p>
                    </div>
                    <?php } ?>
                <?php } ?>

                <hr/>

                <div class="text-center m-t-15">
                    <a href="javascript:void(0);" class="btn btn-info btn_save_auto_draft_setting">Save</a>
                    <a href="group_manage_payment_popup.php" class="btn red-link">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
<div style="display: none">
  <div id="suggestedAddressPopup">
   <?php include_once '../tmpl/suggested_address.inc.php'; ?>
  </div>
</div>
<script type="text/javascript">
    var ach_billing_opts = "<?=$ach_billing_opts?>";
    var cc_billing_opts = "<?=$cc_billing_opts?>";
    var check_billing_opts = "<?=$check_billing_opts?>";

$(document).ready(function() {
    parent.$.colorbox.resize({
        height: 450
    });
    $("#auto_draft_date").datepicker({
        changeDay: true,
        changeMonth: true,
        changeYear: true,
        autoclose:true,
        startDate:'<?=date("m/d/Y",strtotime('+1 day'))?>',
        endDate:'<?=date("m/d/Y",strtotime('+1 month'))?>'
    });
    $('#expiration').datepicker({
        format: 'mm/yy',
        startView : 1,
        minViewMode: 1,
        autoclose: true,
        startDate:new Date(),
        endDate : '+15y'
    });

    $(document).off('change','.chk_payment_mode');
    $(document).on('change','.chk_payment_mode',function(){
        set_billing_profile_opts();
    });
    
    $(document).off('click','.btn_add_billing_profile');
    $(document).on('click','.btn_add_billing_profile',function(){
        add_new_billing_profile()
    });

    $(document).off('click','.btn_cancel_add_billing_profile');
    $(document).on('click','.btn_cancel_add_billing_profile',function(){
        cancel_add_new_billing_profile()
    });
    
    $(document).off('click','.btn_save_auto_draft_setting');
    $(document).on('click','.btn_save_auto_draft_setting',function(){
        save_auto_draft_setting();
    });
    
    $(document).on('focus','#bill_address,#bill_zip',function(){
        $('#is_address_ajaxed').val(1);
    });

    $(document).off('click','.btn_save_billing_profile');
    $(document).on('click','.btn_save_billing_profile',function(){
        $is_address_ajaxed = $('#is_address_ajaxed').val();
        if($is_address_ajaxed == 1){
            updateAddress();
        }else{
            save_billing_profile();            
        }
    });
    set_billing_profile_opts();

});
function set_billing_profile_opts()
{
    $(".ach_payment_section").hide();
    $(".cc_payment_section").hide();
    $(".billing_address_section").hide();
    $(".check_payment_section").hide();
    $(".cc_charge_section").hide();
    $(".check_charge_section").hide();
    $(".billing_profile_section").hide();
    $("select#billing_profile").html('');

    var payment_mode = $('[name="payment_mode"]:checked').val();
    if(payment_mode == "ACH") {
        $(".auto_draft_date_section").show();
        $(".billing_profile_section").show();
        $(".auto_draft_date").show();
        $("select#billing_profile").html(ach_billing_opts);

    } else if(payment_mode == "CC") {
        $(".auto_draft_date_section").show();
        $(".billing_profile_section").show();
        $(".cc_charge_section").show();
        $(".auto_draft_date").show();
        $("select#billing_profile").html(cc_billing_opts);

    } else if(payment_mode == "Check") {
        $(".auto_draft_date_section").show();
        $(".check_payment_section").show();
        $(".check_charge_section").show();
        $(".auto_draft_date").hide();
        $('#auto_draft_date').val('');
        $("select#billing_profile").html(check_billing_opts);
    }
    setTimeout(function(){
        $("select#billing_profile").selectpicker('refresh');
        common_select();
        fRefresh();
    },100);
}

function add_new_billing_profile()
{
    var payment_mode = $('[name="payment_mode"]:checked').val();
    if(payment_mode == "ACH") {
        $(".ach_payment_section").show();
        $(".billing_address_section").show();

    } else if(payment_mode == "CC") {
        $(".cc_payment_section").show();
        $(".billing_address_section").show();
    }
}
function cancel_add_new_billing_profile()
{
    $(".ach_payment_section").hide();
    $(".cc_payment_section").hide();
    $(".billing_address_section").hide();
}
function save_auto_draft_setting()
{
    $("#ajax_loader").show();
    $(".error").html('');
    $.ajax({
        url:'ajax_save_auto_draft_setting.php',
        data:$("#frm_auto_draft_setting").serialize(),
        type:'POST',
        dataType:'json',
        success:function(data){
            $("#ajax_loader").hide();
            if (data.status == 'success') {
                window.location.href = "group_manage_payment_popup.php";
                parent.setNotifySuccess(data.msg);
            } else if (data.status == "fail") {
                $.each(data.errors, function(key, value) {
                    $('#error_' + key).html(value).show();
                });
            }
        }
    });
}
function save_billing_profile()
{
    $("#ajax_loader").show();
    $(".error").html('');
    $.ajax({
        url:'ajax_save_billing_profile.php',
        data:$("#frm_auto_draft_setting").serialize(),
        type:'POST',
        dataType:'json',
        success:function(data){
            $("#ajax_loader").hide();
            if (data.status == 'success') {
                parent.setNotifySuccess(data.msg);

                var payment_mode = $('[name="payment_mode"]:checked').val();
                if(payment_mode == "ACH") {
                    ach_billing_opts += data.option_html;
                    $("select#billing_profile").html(ach_billing_opts);
                    $("select#billing_profile").val(data.billing_id);

                } else if(payment_mode == "CC") {
                    cc_billing_opts += data.option_html;
                    $("select#billing_profile").html(cc_billing_opts);
                    $("select#billing_profile").val(data.billing_id);
                }
                $(".ach_payment_section,.cc_payment_section,.billing_address_section").find(':input').val('');
                
                setTimeout(function(){
                    $("select#billing_profile").selectpicker('refresh');
                    common_select();
                    fRefresh();
                    cancel_add_new_billing_profile();
                },300);

                $("#card_type").selectpicker('refresh');
                $("#ach_account_type").selectpicker('refresh');
                $("#bill_state").selectpicker('refresh');
            } else if (data.status == "fail") {
                $.each(data.errors, function(key, value) {
                    $('#error_' + key).html(value).show();
                });
            }
        }
    });
}

function updateAddress(){
    $.ajax({
      url : "ajax_save_billing_profile.php",
      type : 'POST',
      data:$("#frm_auto_draft_setting").serialize(),
      dataType:'json',
      beforeSend :function(e){
         $("#ajax_loader").show();
         $(".error").html('');
      },success(res){
         $("#is_address_ajaxed").val('');
         $("#ajax_loader").hide();
         $(".suggested_address_box").uniform();
         if(res.zip_response_status =="success"){
            $("#bill_state").val(res.state).addClass('has-value');
            $("#bill_city").val(res.city).addClass('has-value');
            $("#is_address_verified").val('N');
            save_billing_profile();
         }else if(res.address_response_status =="success"){
            $(".suggestedAddressEnteredName").html($("#name_on_card").val());
            $("#bill_state").val(res.state).addClass('has-value');
            $("#bill_city").val(res.city).addClass('has-value');
            $(".suggestedAddressEntered").html(res.enteredAddress);
            $(".suggestedAddressAPI").html(res.suggestedAddress);
            $("#is_valid_address").val('Y');
            $.colorbox({
                  inline:true,
                  href:'#suggestedAddressPopup',
                  height:'500px',
                  width:'650px',
                  escKey:false, 
                  overlayClose:false,
                  closeButton:false,
                  onClosed:function(){
                     $suggestedAddressRadio = $("input[name='suggestedAddressRadio']:checked"). val();
                     
                     if($suggestedAddressRadio=="Suggested"){
                        $("#bill_address").val(res.address).addClass('has-value');
                        $("#bill_address_2").val(res.address2).addClass('has-value');
                        $("#is_address_verified").val('Y');
                     }else{
                        $("#is_address_verified").val('N');
                     }
                     save_billing_profile();
                  },
            });
         }else if(res.status == 'success'){
            $("#is_address_verified").val('N');
            save_billing_profile();
         }else{
            $.each(res.errors,function(index,error){
               $("#error_"+index).html(error).show();
           });
         }
         $("#bill_state").selectpicker('refresh');
      }
   });
}
</script>