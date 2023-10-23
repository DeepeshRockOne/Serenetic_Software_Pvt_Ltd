<div class="panel panel-default">
     <div class="panel-heading">
        <h4 class="mn">License Approval Request</h4>
    </div>
  <div class="panel-body">
    <div class="theme-form">
      <form method="POST" name="frm_category" class="form_wrap " id="frm_license" enctype="multipart/form-data">
        <input type="hidden" name="agent_id" value="<?=$agent_id?>">
        <input type="hidden" name="approve_reject" value="" id="approve_reject">
        <div id="approve_license_section">
        <?php 
        if(!empty($license_res)) {
            $count = 1;
            foreach($license_res as $key => $doc) { 
                ?>
                <div class="row">
                    <div class="col-xs-12">
                    <p class="fw500">License State : <?=$doc["new_selling_licensed_state"]?></p>
                    <input type="hidden" name='pending_hdn_license[<?=$key?>]' value="<?=$doc['id']?>" id='pending_hdn_license_<?=$key?>'>
                    <input type="hidden" name='pending_license_state[<?=$key?>]' value="<?=$doc['new_selling_licensed_state']?>" id='pending_license_state_<?=$key?>'>
                    </div>
                    <div class="col-xs-4">
                        <div class="form-group">
                        <input name="pending_license_number[<?=$key?>]" id="pending_license_number_<?=$key?>" type="text" class="form-control license_number" value="<?=$doc["new_license_num"]?>" data-id="<?=$key?>">
                        <label>License Number<em>*</em></label>
                        <p class="error"><span class="error_pending_license_number_<?=$key?>"></span></p>
                        </div>
                    </div>
                    <div class="col-xs-4">
                        <div class="form-group">
                        <input type="text" name="pending_license_active_date[<?=$key?>]" value="<?=(isset($doc["new_license_active_date"]) && $doc["new_license_active_date"] != "" && $doc["new_license_active_date"] != "0000-00-00") ? date("m/d/Y", strtotime($doc["new_license_active_date"])) : ""?>" id="pending_license_active_date_<?=$key?>" class="form-control pending_license_active" data-id="<?=$key?>" />
                        <label>License Active Date<em>*</em></label>
                        <p class="error"><span class="error_pending_license_active_date_<?=$key?>"></span></p>
                        </div>
                    </div>
                    <div class="col-xs-4">
                        <div class="form-group">
                        <input name="pending_license_expiry[<?=$key?>]" id="pending_license_expiry_<?=$key?>" type="text" class="form-control pending_license_expiry"  value="<?=(isset($doc["new_license_exp_date"]) && $doc["new_license_exp_date"] != "" && $doc["new_license_exp_date"] != "0000-00-00") ? date("m/d/Y", strtotime($doc["new_license_exp_date"])) : "Does Not Expire"?>" <?=checkIsset($doc['new_license_not_expire']) == 'Y' ? "readonly='readonly'" : '' ?> data-id="<?=$key?>" >
                        <label>License Expiration<em>*</em></label>
                        <p class="error"><span class="error_pending_license_expiry_<?=$key?>"></span></p>
                        <div class="clearfix m-t-5">
                        <label for="pending_license_not_expire_<?=$key?>" class="text-red label-input fs12 mn" >
                        <input type="checkbox" name="pending_license_not_expire[<?=$key?>]" id="pending_license_not_expire_<?=$key?>" value="Y" <?=checkIsset($doc['new_license_not_expire']) == 'Y' ? "checked='checked'" : '' ?> class="pending_license_not_expire" data-id="<?=$key?>">
                        &nbsp;&nbsp;License does not expire</label>
                        </div>
                        </div>
                    </div>
                    <div class="col-xs-4">
                        <div class="form-group">
                        <select class="form-control sel_pending_license_state" name="pending_license_type[<?=$key?>]" id="pending_license_type_<?=$key?>" data-id="<?=$key?>" >
                            <option value="" disabled selected hidden> </option>
                            <option value="Business" <?=$doc["new_license_type"] == 'Business' ? 'selected' : ''?>>Agency</option>
                            <option value="Personal" <?=$doc["new_license_type"] == 'Personal' ? 'selected' : ''?>>Agent</option>
                        </select>
                        <label>License Type<em>*</em></label>
                        <p class="error"><span class="error_pending_license_type_<?=$key?>"></span></p>
                        </div>
                    </div>
                    <div class="col-xs-4">
                        <div class="form-group">
                        <select class="form-control sel_pending_license_state" name="pending_licsense_authority[<?=$key?>]" id="pending_licsense_authority_<?=$key?>" data-id="<?=$key?>">
                            <option value="" disabled selected hidden> </option>
                            <option value="Health" <?=$doc["new_license_auth"] == 'Health' ? 'selected' : ''?>>Health</option>
                            <option value="Life" <?=$doc["new_license_auth"] == 'Life' ? 'selected' : ''?>>Life</option>
                            <option value="general_lines" <?=$doc["new_license_auth"] == 'general_lines' ? 'selected' : ''?>>General Lines (Both)</option>
                        </select>
                        <label>License of Authority<em>*</em></label>
                        <p class="error"><span class="error_pending_licsense_authority<?=$key?>"></span></p>
                        </div>
                    </div>
                </div>
                <?php
            }
        }
        ?>
            <div class="text-center">
                <button type="button" class="btn btn-action" id="btn_approve" name="final_save" type="button">Approve</button>
                <a href="javascript:void(0);" id="btn_reject" class="btn red-link">Reject</a>
            </div>
        </div>
        <div id="reject_license_section" style="display: none;">
            <div class="row">
                <div class="col-xs-12">
                    <div class="form-group height_auto">
                        <label for="reject_text">Enter reason for submission being license rejected</label>
                        <textarea name="reject_text" id="reject_text" class="summernote" rows="3"><p>There was an issue(s) with the documentation submitted to add a licensed state to your account. Please update the following:</p>
                            <ul><li></li></ul>
                            <p>Once complete, resubmit for approval so we can get your licensed approved and added.</p>
                            <p>Need help? Contact your parent agent or our agent services team today!</p></textarea>
                        <p class="error"><span class="error_pending_reject_text"></span></p>
                    </div>
                </div>
                <div class="col-xs-12">
                    <div class="form-group m-t-30">
                      <div class="text-center">
                            <button type="button" class="btn btn-action" id="btn_submit_reject" name="final_save" type="button">Send</button>
                            <a href="javascript:void(0);" id="btn_cancel_reject" class="btn red-link">Cancel</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>       
      </form>
    </div>
  </div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        initCKEditor("reject_text",false,"200px");
        $(".pending_license_expiry").datepicker({
            changeDay: true,
            changeMonth: true,
            changeYear: true,
            startDate: new Date()
        });

        $(".pending_license_active").datepicker({
            changeDay: true,
            changeMonth: true,
            changeYear: true,
        });

        trigger(".pending_license_not_expire", function ($this, e) {
            var id = $this.attr('data-id');
            if ($('#pending_license_not_expire_' + id).is(":checked")) {
                $("#pending_license_expiry_" + id).attr('readonly', 'readonly');
                $("#pending_license_expiry_" + id).val("12/31/2099");
            } else {
                $("#pending_license_expiry_" + id).removeAttr('readonly');
                $("#pending_license_expiry_" + id).val("");
            }
        });

        $(document).off('click','#btn_approve');
        $(document).on('click','#btn_approve',function(){
            $("#approve_reject").val('approve');
            submit_form();
        });
        $(document).off('click','#btn_reject');
        $(document).on('click','#btn_reject',function(){
            $("#approve_license_section").hide();
            $("#reject_license_section").show();
        });

        $(document).off('click','#btn_submit_reject');
        $(document).on('click','#btn_submit_reject',function(){
            $("#approve_reject").val('reject');
            submit_form();
        });
        
        $(document).off('click','#btn_cancel_reject');
        $(document).on('click','#btn_cancel_reject',function(){
            $("#reject_license_section").hide();
            $("#approve_license_section").show();
        });
    });

    function submit_form()
    {
        $("#reject_text").val(CKEDITOR.instances.reject_text.getData());
        $.ajax({
            url: 'ajax_contract_license.php',
            data: $("#frm_license").serialize(),
            type: 'POST',
            dataType: 'json',
            beforeSend: function() {
                parent.$("#ajax_loader").show();
            },
            success: function(res) {
                parent.$("#ajax_loader").hide();
                if (res.status == 'success') {
                    window.parent.location = 'agent_detail_v1.php?id=<?=$agent_id?>';
                } else {
                    $(".error").hide();
                    var is_error = true;
                    $.each(res.errors, function(key, value) {
                        $('.error_pending_' + key).parent("p.error").show();
                        $('.error_pending_' + key).html(value).show();

                        $('#error_pending_' + key).parent("p.error").show();
                        $('#error_pending_' + key).html(value).show();
                        if (is_error == true && $("[name='pending_" + key + "']").length > 0) {
                            is_error =false;
                            $('html, body').animate({
                                scrollTop: parseInt($("[name='pending_" + key + "']").offset().top) - 100
                            }, 1000);
                        }
                    });
                }
            }
        });
    }
</script>