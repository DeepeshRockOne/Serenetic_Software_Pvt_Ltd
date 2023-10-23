<style type="text/css">
.hidden_class { display: none; }
.paynow_top_wrap {
    background: linear-gradient(180deg, #F4F4F4 0%, rgba(255, 255, 255, 0.35) 100%);
    padding: 18px;
}
</style>
<form method="POST" name="frm_pay_bill" class="form_wrap" id="frm_pay_bill" enctype="multipart/form-data">
<input type="hidden" name="location" value="<?=$location?>"> 
<input type="hidden" name="old_files_deleted_key" id="old_files_deleted_key" value="">
<input type="hidden" name="display_div" id="display_div" value="1"/>
<div class="panel panel-default theme-form">
    <div class="clearfix p-15 br-b">
        <span class="pull-left fs18 fw500 m-t-5">
            Pay Now
        </span>
        <div class="pull-left w-160 p-l-10 pr">
            <div class="form-group height_auto mn ">
                <select name="list_bill_id" id="list_bill_id" class="form-control">
                    <option data-hidden="true"></option>
                    <?php 
                    if(!empty($list_bill_res)) {
                        foreach ($list_bill_res as $list_bill_row) {
                        ?>
                        <option value="<?=$list_bill_row['id'];?>" <?=md5($list_bill_row['id'])==$list_bill_id?"selected":""?>><?=$list_bill_row['list_bill_no']?></option>
                        <?php
                        }
                    }
                    ?>
                </select>
                <label>List Bill</label>
                <p class="error"><span id="err_list_bill_id"></span></p>
            </div>
        </div>
    </div>
    <div class="paynow_top_wrap">
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group height_auto m-b-5">
                    <select name="billing_profile" id="billing_profile" class="form-control">
                        <option></option>
                    </select>
                    <label>Select Payment Method</label>
                    <p class="error"><span id="err_billing_profile"></span></p>
                </div>
            </div>
            <div class="col-sm-6">
                <table cellspacing="0" cellspacing="0" class="fs12 pull-right">
                    <tbody>
                        <tr>
                            <td style="padding: 3px 15px;">Due Date:</td>
                            <td style="padding: 3px 15px;" class="due_date">-</td>
                        </tr>
                        <tr>
                            <td style="padding: 3px 15px;">Current Payment:</td>
                            <td style="padding: 3px 15px; font-weight: bold; font-size: 14px;" class="list_bill_amount">-</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="panel-body ">
        <div class="cc_ach_section">
            <p class="">Amount Being Charged</p>
            <div class="form-group">
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-usd"></i></span>
                    <input type="text" class="form-control list_bill_amount" readonly="readonly">
                </div>
            </div>
        </div>
        <div class="theme-form row check_section m-b-20" style="display: none;">
            <div class="col-sm-6">
                <div class="form-group">
                    <input type="text" class="form-control list_bill_amount" readonly="">
                    <label>Amount Received<em>*</em></label>
                    <p class="error" id="error_ach_name"></p>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <input type="text" name="payment_date" id="payment_date" class="form-control">
                    <label>Payment Date<em>*</em></label>
                    <p class="error" id="err_payment_date"></p>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="col-sm-12">
                <div class="form-group">
                    <input type="text" name="check_number" id="check_number" class="form-control">
                    <label>Check #<em>*</em></label>
                    <p class="error" id="err_check_number"></p>
                </div>
            </div>
            <div class="clearfix"></div>
            <?php for ($i=0; $i < 5; $i++) { ?>
            <div class="col-sm-12">
                <div class="form-group files_input <?=($i == 0) ? '' : 'hidden_class' ?>" id="files_<?=$i?>">
                    <?php if($i != 0) { ?>
                    <a href="javascript:void(0);" class="close_attached pull-right" id="close_attached_<?=$i?>"><i class="fa fa-close fa-lg text-red"></i></a>
                    <?php } ?>
                    <div class="phone-control-wrap">
                        <div class="phone-addon">
                            <div class="custom_drag_control solid_drag_control"> <span class="btn btn-action" style="border-radius:0px;">Upload Attachment</span>
                                <input type="file" class="gui-file" name="file[<?=$i?>]" >
                                <input type="text" class="gui-input" placeholder="Choose File">
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>
                    <p class="error" id="err_file_<?=$i?>"></p>
                    <?php if($i == 0) { ?>
                    <label>You can upload a maximum of 5 files, 5MB each</label>
                    <?php } ?>
                    <div class="clearfix"></div>
                    <input type="hidden" name="is_file_hidden[<?=$i?>]" id="is_file_hidden_<?=$i?>" value="<?=($i == 0) ? 'Y' : 'N' ?>"/>
                </div>
            </div>
            <?php } ?>
            <div class="clearfix"></div>
            <div class="col-sm-12">
                <a href="javascript:void(0);" class="btn red-link pull-right" id="add_attachement"> + Add Attachments</a>
            </div>
            <div class="clearfix"></div>
            <div class="col-sm-12">
                <div class="form-check">
                    <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" name="sent_receipt" id="sent_receipt" value="Y" /> Send an email receipt?
                    </label>
                </div>
            </div>
        </div>
        <div class="text-center">
            <button type="submit" class="btn btn-action" id="btn_pay_bill">Pay Now</button>
            <a href="javascript:void(0);" class="btn red-link" onclick="parent.$.colorbox.close();">Cancel</a>
        </div>
    </div>
</div>
</form>
<script type="text/javascript">
    $(document).ready(function() {
        $(document).off('change','#list_bill_id');
        $(document).on('change','#list_bill_id',function(){
            load_list_bill();
        });

        $(document).off('change','#billing_profile');
        $(document).on('change','#billing_profile',function(){
            var billing_profile = $(this).val();
            if(billing_profile == "record_check_payment") {
                $(".cc_ach_section").hide();
                $(".check_section").show();
            } else {
                $(".check_section").hide();
                $(".cc_ach_section").show();
            }
        });

        $("#payment_date").datepicker({
            changeDay: true,
            changeMonth: true,
            changeYear: true,
            autoclose:true,
        });

        $(document).off('submit', '#frm_pay_bill');
        $(document).on('submit', '#frm_pay_bill', function (e) {
            e.preventDefault();
            $('#ajax_loader').show();
            $.ajax({
                url: 'ajax_pay_bill.php',
                data: new FormData(this),
                method: 'POST',
                cache: false,
                dataType:'json',
                contentType: false,
                processData: false,
                success: function (res) {
                    $("[id^='err_']").html('');

                    if (res.status == "fail") {
                        $('#ajax_loader').hide();
                        var is_error = true;
                        $.each(res.errors, function (index, value) {
                            if (typeof(index) !== "undefined") {
                                $("#err_" + index).html(value);
                                if (is_error) {
                                    var offset = $("#err_" + index).offset();
                                    var offsetTop = offset.top;
                                    var totalScroll = offsetTop - 200;
                                    $('body,html').animate({
                                        scrollTop: totalScroll
                                    }, 1200);
                                    is_error = false;
                                }                            
                            }
                        });
                    } else {
                        if(res.status == 'success'){
                            window.parent.$.colorbox.close()
                            parent.window.location.reload();
                        }
                    }
                }
            });
        });
        if($('select[name="list_bill_id"]').val() != '') {
            load_list_bill();
        }

        $(document).on('click',".deleted_file", function(){
          $("#download_"+$(this).attr('data-key')).hide();
          $("#old_files_deleted_key").val($("#old_files_deleted_key").val() + $(this).attr('data-key') + ',');
          $("#display_div").val($("#display_div").val() - 1);
          $("#add_attachement").show();
        });

        $(document).off('click','#add_attachement');
        $(document).on('click','#add_attachement', function(){
            $display_div_number = parseInt($("#display_div").val());
            $new_div = $display_div_number + 1;
            if($new_div == 5){
                $(this).hide();
            }
            $(".files_input").each(function(index,element){
                if($(this).hasClass('hidden_class')){
                    $("#display_div").val($new_div);
                    $id = $(this).attr('id').replace('files_','');
                    $(this).removeClass('hidden_class');
                    $(this).show();
                    $("#is_file_hidden_"+$id).val('Y');
                    return false;
                }
            });
        });

        $(document).off('click','.close_attached');
        $(document).on('click','.close_attached', function(){
            $display_div_number = parseInt($("#display_div").val());
            $new_div = $display_div_number - 1;
            $id = $(this).attr('id').replace('close_attached_','');
            $("#display_div").val($new_div);
            $("#files_"+$id).addClass('hidden_class');
            $("#files_"+$id).hide();
            $("#is_file_hidden_"+$id).val('N');
            $("#add_attachement").show();
        });

    });
    function load_list_bill() {
        var list_bill_id = $('select[name="list_bill_id"]').val();
        if(list_bill_id > 0) {
            $("#ajax_loader").show();
            $.ajax({
                url: '<?=$HOST?>/ajax_get_list_bill.php?list_bill_id=' + list_bill_id+'&location=<?=$location?>',
                data: null,
                method: 'POST',
                dataType: 'json',
                success: function (res) {
                    $("#ajax_loader").hide();
                    $("input.list_bill_amount").val(res.due_amount);
                    $(".list_bill_amount").html(res.due_amount);
                    $(".due_date").html(res.due_date);
                    $("select#billing_profile").html(res.billing_profile_opt);
                    $("select#billing_profile").selectpicker('refresh');
                    fRefresh();
                    var list_bill_row = res.list_bill_row;
                    if(list_bill_row.due_amount > 0) {
                        $("#btn_pay_bill").show();
                    } else {
                        $("#btn_pay_bill").hide();
                    }
                }
            });
        } else {
            $("input.list_bill_amount").val('-');
            $(".list_bill_amount").html('-');
            $(".due_date").html('-');
            $("select#billing_profile").html('<option data-hidden="true"></option>');
            $("select#billing_profile").selectpicker('refresh');
            $("#btn_pay_bill").show();
        }
    }
</script>