<?php if ($is_ajaxed) { ?>
<div class="agp_attr_tbl table-responsive br-n theme-form" id="pending_license_table">
<table class="table">
    <thead>
    <th>Status</th>
    <th>License State</th>
    <th>License Number</th>
    <th class="text-center">License Active Date</th>
    <th class="text-center">License Expiration</th>
    <th class="text-center">License Type</th>
    <th class="text-center">Lines of Authority</th>
    <th class="text-center"></th>
    </thead>
    <tbody>
    <?php 
    if(!empty($fetchDocs) && $totalDocs > 0) {
        $count = 1;
    ?>
    <?php foreach($fetchDocs as $key => $doc) { ?>
    <tr class="pending_primary_<?=$doc['id']?> primary_lc<?=$key?>" style="">
        <td><?=$doc['new_license_status']?></td>
        <td><?=$doc['new_selling_licensed_state']?></td>
        <td><?=$doc['new_license_num']?></td>
        <td class="text-center"><?=date('m/d/Y',strtotime($doc['new_license_active_date']))?></td>
        <td class="text-center"><?=$doc['new_license_not_expire'] == 'Y' ? 'Does Not Expire' : date('m/d/Y',strtotime($doc['new_license_exp_date']))?></td>
        <td class="text-center">
            <?php
                if($doc['new_license_type'] == "Business")  {
                    echo "Agency";
                } else if($doc['new_license_type'] == "Personal")  {
                    echo "Agent";
                } else {
                    echo "";
                }
            ?>
        </td>
        <td class="text-center"><?=$doc['new_license_auth'] == 'general_lines' ? 'General Lines (Both)' : $doc['new_license_auth'] ;?></td>
        <td class="text-center">
            <a href="javascript:void(0);" class="red-link fw500 pn edit_pending_license" data-id="<?=$doc['id']?>">Edit</a>
        </td>
    </tr>
    <tr class="pending_secondary_<?=$doc['id']?> secondary_lc<?=$key?>" style="display:none">
        <td class="license_portion">
            <input type="hidden" name='pending_hdn_license[<?=$key?>]' value="<?=$doc['id']?>" id='pending_hdn_license_<?=$key?>'>
            <?=$doc['new_license_status']?>
            <input type="hidden" name="edit[<?=$key?>]" value="" id="pded_license__<?=$key?>" class="ed_license__<?=$doc['id']?>">
        </td>
        <td>
            <input type="hidden" name='pending_license_state[<?=$key?>]' value="<?=$doc['new_selling_licensed_state']?>" id='pending_license_state_<?=$key?>'>
            <input type="text" class="form-control" value="<?=$doc["new_selling_licensed_state"]?>" readonly>
            <label>License State<em>*</em></label>
            <p class="error"><span class="error_pending_license_state_<?=$key?>"></span></p>
        </td>
        <td>
            <input name="pending_license_number[<?=$key?>]" id="pending_license_number_<?=$key?>" type="text" class="form-control license_number" value="<?=$doc["new_license_num"]?>" data-id="<?=$key?>">
            <label>License Number<em>*</em></label>
            <p class="error"><span class="error_pending_license_number_<?=$key?>"></span></p>
        </td>
        <td class="text-center">
            <input type="text" name="pending_license_active_date[<?=$key?>]" value="<?=(isset($doc["new_license_active_date"]) && $doc["new_license_active_date"] != "" && $doc["new_license_active_date"] != "0000-00-00") ? date("m/d/Y", strtotime($doc["new_license_active_date"])) : ""?>" id="pending_license_active_date_<?=$key?>" class="form-control pending_license_active" data-id="<?=$key?>" />
            <label>License Active Date<em>*</em></label>
            <p class="error"><span class="error_pending_license_active_date_<?=$key?>"></span></p>
        </td>
        <td class="text-center">
            <input name="pending_license_expiry[<?=$key?>]" id="pending_license_expiry_<?=$key?>" type="text" class="form-control pending_license_expiry"  value="<?=(isset($doc["new_license_exp_date"]) && $doc["new_license_exp_date"] != "" && $doc["new_license_exp_date"] != "0000-00-00") ? date("m/d/Y", strtotime($doc["new_license_exp_date"])) : "Does Not Expire"?>" <?=checkIsset($doc['new_license_not_expire']) == 'Y' ? "readonly='readonly'" : '' ?> data-id="<?=$key?>" >
            <label>License Expiration<em>*</em></label>
            <p class="error"><span class="error_pending_license_expiry_<?=$key?>"></span></p>
            <div class="clearfix m-t-5">
            <label for="pending_license_not_expire_<?=$key?>" class="text-red fs12 mn" >
            <input type="checkbox" name="pending_license_not_expire[<?=$key?>]" id="pending_license_not_expire_<?=$key?>" value="Y" <?=checkIsset($doc['new_license_not_expire']) == 'Y' ? "checked='checked'" : '' ?> class="pending_license_not_expire" data-id="<?=$key?>">
            &nbsp;&nbsp;License does not expire</label>
        </td>
        <td class="text-center">
            <select class="form-control sel_pending_license_state" name="pending_license_type[<?=$key?>]" id="pending_license_type_<?=$key?>" data-id="<?=$key?>" >
                <option value="" disabled selected hidden> </option>
                <option value="Business" <?=$doc["new_license_type"] == 'Business' ? 'selected' : ''?>>Agency</option>
                <option value="Personal" <?=$doc["new_license_type"] == 'Personal' ? 'selected' : ''?>>Agent</option>
            </select>
            <label>License Type<em>*</em></label>
            <p class="error"><span class="error_pending_license_type_<?=$key?>"></span></p>
        </td>
        <td class="text-center">
            <select class="form-control sel_pending_license_state" name="pending_licsense_authority[<?=$key?>]" id="pending_licsense_authority_<?=$key?>" data-id="<?=$key?>">
            <option value="" disabled selected hidden> </option>
                <option value="Health" <?=$doc["new_license_auth"] == 'Health' ? 'selected' : ''?>>Health</option>
                <option value="Life" <?=$doc["new_license_auth"] == 'Life' ? 'selected' : ''?>>Life</option>
                <option value="general_lines" <?=$doc["new_license_auth"] == 'general_lines' ? 'selected' : ''?>>General Lines (Both)</option>
            </select>
            <label>License of Authority<em>*</em></label>
            <p class="error"><span class="error_pending_licsense_authority<?=$key?>"></span></p>
        </td>
        <td class="text-center">
            <a href="javascript:void(0);" class="text-success ajax_add_pending_license" data-id="<?=$key?>"><i title="Save" class="fa fa-check fa-lg"></i></a>
            <a href="javascript:void(0);" class="text-red" data-id="<?=$key?>" onclick="delete_pending_agent_license(<?=$key?>,<?=$doc['id']?>)" ><i title="Delete" class="fa fa-trash fa-lg"></i></a>
            <a href="javascript:void(0);" class="pd_cancel_edit" data-id="<?=$key?>"><i title="Cancel" class="fa fa-close fa-lg"></i></a>
        </td>
    </tr>
<?php $count++; } ?>
    </tbody>
    <tfoot>
        <?php } else {?>
    <tr>
    <td colspan="8">No pending approval license found!</td>
    </tr>
    <?php } ?>
    <tr>
    <?php 
        if(!empty($fetchDocs) && $totalDocs > 0) {
    ?>
        <td colspan="8">
        <?php echo $paginate_license->links_html; ?>
        </td>
    <?php 
        }
    ?>
    </tr>
    </tfoot>
</table>
</div>
<?php } else { ?>
<div id="agent_pending_license_ajax_data"></div>
<script type="text/javascript">
$(document).off('click', '.pd_cancel_edit');
$(document).on('click', '.pd_cancel_edit', function (e) {
    var $id = $(this).attr('data-id');
    $("#pded_license__"+$id).val("");
    $(".primary_lc"+$id).show();
    $(".secondary_lc"+$id).hide();
    $('.edit_pending_license').show();
});
function delete_pending_agent_license($id, license_id) {
    swal({
        text: 'Delete  License: Are you sure?',
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Confirm',
        cancelButtonText: 'Cancel',
    }).then(function (e) {
        var agent_id = $("#agent_id").val();
        var lid = license_id;
        $("#ajax_loader").show();
        $.ajax({
            url: 'ajax_update_agent_account_detail.php',
            method: 'POST',
            data: {lid: lid, agent_id: agent_id, ajax_delete: "1",pending_license:"1"},
            dataType: 'json',
            success: function (res) {
                if (res.status == 'success') {
                    agent_license_ajax_submit();
                    agent_pending_license_ajax_submit();
                    setNotifySuccess("Agent license deleted successfully.");
                } else if (res.status == 'fail') {
                    $("#ajax_loader").hide();
                    setNotifyError("Agent license not deleted.");
                }
            }
        });
    }, function (dismiss) {
    })
}
$(document).ready(function () {
    dropdown_pagination('agent_pending_license_ajax_data');
    $(document).off('click', '#agent_pending_license_ajax_data ul.pagination li a');
    $(document).on('click', '#agent_pending_license_ajax_data ul.pagination li a', function (e) {
        e.preventDefault();
        $('#ajax_loader').show();
        $('#agent_pending_license_ajax_data').hide();
        $.ajax({
            url: $(this).attr('href'),
            type: 'GET',
            success: function (res) {
                $('#ajax_loader').hide();
                $('#agent_pending_license_ajax_data').html(res).show();
                $(document).find("#pending_license_table .dataTables_info").hide();
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
                refreshControl('.sel_pending_license_state');
                common_select();
            }
        });
    });
    $(document).off('click', '.ajax_add_pending_license');
    $(document).on('click', '.ajax_add_pending_license', function (e) {
        $("#ajax_loader").show();
        $("p.error").children('span').html('').hide();
        var id = $(this).attr('data-id');

        var license_state = $("#pending_license_state_"+id).val();
        if($('#pending_license_not_expire_'+id).is(":checked")) {
            var license_not_expire = 'Y';
        } else {
            var license_not_expire = 'N';
        }
        var license_expiry = $('#pending_license_expiry_'+id).val();
        var license_number = $('#pending_license_number_'+id).val();
        var license_active_date = $('#pending_license_active_date_'+id).val();    
        var license_type = $('#pending_license_type_'+id).val();
        var licsense_authority = $('#pending_licsense_authority_'+id).val();
        var lid = $("#pending_hdn_license_"+id).val();
        var edit = $("#pded_license__"+id).val();
        var hdn_license = [id];
        $.ajax({
        url : 'ajax_update_agent_account_detail.php',
        data : { section:'is_add_license',agent_id:'<?= $agent_id ?>',agent_id_org:'<?= $agent_id_org ?>',license_expiry:license_expiry,license_not_expire:license_not_expire,license_number:license_number,license_active_date:license_active_date,license_state:license_state,license_type:license_type,licsense_authority:licsense_authority,hdn_license:hdn_license,is_ajax_license:1,step:'ajax',lid:lid,pending_license:"1",edit:edit},
        method: 'POST',
        dataType: 'json',
        success : function(res){
            if (res.status == 'success') {
            $("#ajax_loader").hide();
            $("#edit_pending_license_"+id).show();
            $("#hidden_btn_"+id).hide();
            if(res.doc_id !== '')
                $(".div_license_"+id).remove();
                setNotifySuccess("Agent license updated successfully.");                
                agent_pending_license_ajax_submit();
            } else if(res.status == 'fail'){
                $("#ajax_loader").hide();
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
    });
});
function agent_pending_license_ajax_submit() {
    $('#ajax_loader').show();
    $('#agent_pending_license_ajax_data').hide();
    $('#agent_product_is_ajaxed').val('1');
    $.ajax({
        url: "agent_pending_license.php?id=<?=$agent_id?>&is_ajaxed_license=1&per_pages=10",
        type: 'GET',
        success: function (res) {
            $('#ajax_loader').hide();
            $('#agent_pending_license_ajax_data').html(res).show();
            $(document).find("#pending_license_table .dataTables_info").hide();
            refreshControl('.sel_pending_license_state');
            $(".pending_license_expiry").datepicker({
                changeDay: true,
                changeMonth: true,
                changeYear: true,
                startDate:new Date()
            });
            $(".pending_license_active").datepicker({
                changeDay: true,
                changeMonth: true,
                changeYear: true,
            });
            common_select();
        }
    });
    return false;
}
</script>
<?php } ?>