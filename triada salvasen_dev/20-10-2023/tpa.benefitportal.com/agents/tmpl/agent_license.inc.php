<?php if ($is_ajaxed) { ?>
<div class="agp_attr_tbl table-responsive br-n theme-form" id="license_table">
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
    <tr class="primary_<?=$doc['id']?> primary_lc<?=$key?>" style="">
        <td><?=$doc['license_status']?></td>
        <td><?=$doc['selling_licensed_state']?><p class="error"><span class="error_license_state_<?=$key?>"></span></p></td>
        <td><?=$doc['license_num']?><span class="error_license_number_<?=$key?>"></span></p></td>
        <td class="text-center"><?=date('m/d/Y',strtotime($doc['license_active_date']))?><p class="error"><span class="error_license_active_date_<?=$key?>"></span></p></td>
        <td class="text-center"><?=$doc['license_not_expire'] == 'Y' ? 'Does Not Expire' : date('m/d/Y',strtotime($doc['license_exp_date']))?><p class="error"><span class="error_license_expiry_<?=$key?>"></span></p></td>
        <td class="text-center">
        <?php 
         echo $doc['license_type'] =="Business" ? 'Agency' : '' ;
         echo $doc['license_type'] =="Personal" ? 'Agent' : ''  ?>
        <p class="error"><span class="error_license_type_<?=$key?>"></span></p></td>
        <td class="text-center"><?=$doc['license_auth'] == 'general_lines' ? 'General Lines (Both)' : $doc['license_auth'] ;?><p class="error"><span class="error_licsense_authority<?=$key?>"></span></p></td>
        <td class="text-center">
            <?php if($doc['new_request'] == "Y" || $doc['is_rejected'] == "Y") { ?>
                -
            <?php } else { ?>
                <a href="javascript:void(0);" class="red-link fw500 pn edit_license" data-id="<?=$doc['id']?>">Edit</a>
            <?php } ?>
            
        </td>
    </tr>
    <tr class="secondary_<?=$doc['id']?> secondary_lc<?=$key?>" style="display:none">
        <td class="license_portion">
            <input type="hidden" name='hdn_license[<?=$key?>]' value="<?=$doc['id']?>" id='hdn_license_<?=$key?>'>
            <?=$doc['license_status']?>
            <input type="hidden" name="edit[<?=$key?>]" value="" id="ed_license__<?=$key?>" class="ed_license__<?=$doc['id']?>">
        </td>
        <td>
            <input type="hidden" name='license_state[<?=$key?>]' value="<?=$doc['selling_licensed_state']?>" id='license_state_<?=$key?>' class="selected_license_states" data-id="<?=$key?>">
            <input type="text" class="form-control" value="<?=$doc["selling_licensed_state"]?>" readonly>
            <label>License State<em>*</em></label>
            <p class="error"><span class="error_license_state_<?=$key?>"></span></p>
        </td>
        <td>
            <input name="license_number[<?=$key?>]" id="license_number_<?=$key?>" type="text" class="form-control license_number"   value="<?=$doc["license_num"]?>" data-id="<?=$key?>">
            <label>License Number<em>*</em></label>
            <p class="error"><span class="error_license_number_<?=$key?>"></span></p>
        </td>
        <td class="text-center">
            <input type="text" name="license_active_date[<?=$key?>]" value="<?=(isset($doc["license_active_date"]) && $doc["license_active_date"] != "" && $doc["license_active_date"] != "0000-00-00") ? date("m/d/Y", strtotime($doc["license_active_date"])) : ""?>" id="license_active_date_<?=$key?>" class="form-control license_active" data-id="<?=$key?>" />
            <label>License Active Date<em>*</em></label>
            <p class="error"><span class="error_license_active_date_<?=$key?>"></span></p>
        </td>
        <td class="text-center">
            <input name="license_expiry[<?=$key?>]" id="license_expiry_<?=$key?>" type="text" class="form-control license_expiry"  value="<?=(isset($doc["license_exp_date"]) && $doc["license_exp_date"] != "" && $doc["license_exp_date"] != "0000-00-00") ? date("m/d/Y", strtotime($doc["license_exp_date"])) : "Does Not Expire"?>" <?=checkIsset($doc['license_not_expire']) == 'Y' ? "readonly='readonly'" : '' ?> data-id="<?=$key?>" >
            <label>License Expiration<em>*</em></label>
            <p class="error"><span class="error_license_expiry_<?=$key?>"></span></p>
            <div class="clearfix m-t-5">
            <label for="license_not_expire[<?=$key?>]" class="text-red fs12 mn" >
            <input type="checkbox" name="license_not_expire[<?=$key?>]" id="license_not_expire_<?=$key?>" value="Y" <?=checkIsset($doc['license_not_expire']) == 'Y' ? "checked='checked'" : '' ?> class="license_not_expire" data-id="<?=$key?>">
            &nbsp;&nbsp;License does not expire</label>
        </td>
        <td class="text-center">
            <select class="form-control sel_license_state" name="license_type[<?=$key?>]" id="license_type_<?=$key?>" data-id="<?=$key?>" >
                <option value="" disabled selected hidden> </option>
                <option value="Business" <?=$doc["license_type"] == 'Business' ? 'selected' : ''?>>Agency</option>
                <option value="Personal" <?=$doc["license_type"] == 'Personal' ? 'selected' : ''?>>Agent</option>
            </select>
            <label>License Type<em>*</em></label>
            <p class="error"><span class="error_license_type_<?=$key?>"></span></p>
        </td>
        <td class="text-center">
            <select class="form-control sel_license_state" name="licsense_authority[<?=$key?>]" id="licsense_authority_<?=$key?>" data-id="<?=$key?>">
            <option value="" disabled selected hidden> </option>
                <option value="Health" <?=$doc["license_auth"] == 'Health' ? 'selected' : ''?>>Health</option>
                <option value="Life" <?=$doc["license_auth"] == 'Life' ? 'selected' : ''?>>Life</option>
                <option value="general_lines" <?=$doc["license_auth"] == 'general_lines' ? 'selected' : ''?>>General Lines (Both)</option>
            </select>
            <label>License of Authority<em>*</em></label>
            <p class="error"><span class="error_licsense_authority<?=$key?>"></span></p>
        </td>
        <td class="text-center">
            <a href="javascript:void(0);" class="text-success ajax_add_license" data-id="<?=$key?>"><i title="Save" class="fa fa-check fa-lg"></i></a>
            <a href="javascript:void(0);" class="text-red" data-id="<?=$key?>" onclick="delete_agent_license(<?=$key?>,<?=$doc['id']?>)" ><i title="Delete" class="fa fa-trash fa-lg"></i></a>
            <a href="javascript:void(0);" class="cancel_edit" data-id="<?=$key?>"><i title="Cancel" class="fa fa-close fa-lg"></i></a>
        </td>
    </tr>
<?php $count++; } ?>
    </tbody>
    <tfoot>
        <?php } else {?>
    <tr>
    <td colspan="8">No license found!</td>
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
<div id="agent_license_ajax_data"></div>
<input type="hidden" name="license_display_counter" id="license_display_counter" value="0">
<div class="license_template license_tempmdsm  " style="display: none"> 
  <div class="license_portion pr div_license_~i~ "> 
    <div class="row seven-cols">
      <input type="hidden" name='hdn_license[~i~]' value="0" id='hdn_license_~i~'>
      <input type="hidden" name="edit[~i~]" value="~i~" id="ed_license__~i~" class="ed_license__~i~">
      <div class="col-md-1">
        <div class="form-group ">
          <select name="license_state[~i~]" id="license_state_~i~"  class="license_state select_class_~i~">
            <option value="" ></option>
            <?php if (!empty($allStateRes)) {?>
              <?php foreach ($allStateRes as $state) {?>
              <option value="<?=$state["name"];?>"><?php echo $state['name']; ?></option>
              <?php }?>
            <?php }?>
          </select>
          <label>License State<em>*</em></label>
          <p class="error"><span id="error_license_state_~i~" class="error_license_state"></span></p>
        </div>
      </div>
      <div class="col-md-1">
        <div class="form-group  ">        
          <input name="license_number[~i~]" id="license_number_~i~" type="text" class="form-control license_number"   value="">
          <label for="license_number[~i~]">License Number<em>*</em></label>
          <p class="error"><span id="error_license_number_~i~" class="error_license_number"></span></p>
        </div>
      </div>
      <div class="col-md-1">
        <div class="form-group ">
            <input type="text" name="license_active_date[~i~]" id="license_active_date_~i~" class="form-control license_active" />
            <label for="license_active_date_~i~">License Active Date<em>*</em></label>
            <p class="error"><span id="error_license_active_date_~i~" class="error_license_active_date"></span>
        </div>
      </div>
      <div class="col-md-1">
        <div class="form-group height_auto m-b-10" id="mdy_tooltip" data-toggle="tooltip" data-placement="top" title="MM/DD/YYYY">
          <input name="license_expiry[~i~]" id="license_expiry_~i~" type="text" class="form-control license_expiry"  value="">
          <label for="license_expiry[~i~]">License Expiration<em>*</em></label>
          <p class="error"><span id="error_license_expiry_~i~" class="error_license_expiry"></span></p>
           <div class="clearfix m-t-5">
           <label for="license_not_expire[~i~]" class="text-red mn fs12">
            <input type="checkbox" name="license_not_expire[~i~]" id="license_not_expire_~i~" class="license_not_expire" data-id="~i~" value="Y">
          License does not expire</label>
        </div>
        </div>
      </div>
      <div class="col-md-1">
        <div class="form-group ">
            <select name="license_type[~i~]" id="license_type_~i~" class="select_class_~i~">
                <option value="" disabled selected hidden> </option>
                  <option value="Business">Agency</option>
                  <option value="Personal">Agent</option>
              </select>
              <label for="license_type~i~">License Type<em>*</em></label>
              <p class="error"><span id="error_license_type_~i~" class="error_license_type"></span></p>
          </div>
      </div>
      <div class="col-md-1">
        <div class="form-group ">
            <select name="licsense_authority[~i~]" id="licsense_authority_~i~" class="select_class_~i~">
                <option value="" disabled selected hidden> </option>
                  <option value="Health">Health</option>
                  <option value="Life">Life</option>
                  <option value="general_lines">General Lines (Both)</option>
              </select>
              <label for="licsense_authority~i~">License of Authority<em>*</em></label>
              <p class="error"><span id="error_licsense_authority_~i~" class="error_licsense_authority"></span></p>
          </div>
      </div>
      <div class="col-md-1">
        <div class="form-group height_auto">
        <!--<a href="javascript:void(0)" class="edit_license btn red-link" style="display:none" id="edit_license_~i~" data-id="~i~" > Edit </a>
        <div class="form-group " id="hidden_btn_~i~"> -->
          <button type="button" class="btn btn-primary ajax_add_license" data-id="~i~">Save</button>
          <a href="javascript:void(0)" class="remove_license btn red-link"  data-id="~i~"> Delete </a>
        <!--</div>-->
        </div>
      </div>
      <div class="clearfix"></div>
    </div>
  </div>  
  <div class="clearfix"></div>
</div>
<script type="text/javascript">
$(document).off('click', '.cancel_edit');
$(document).on('click', '.cancel_edit', function (e) {
    var $id = $(this).attr('data-id');
    $("#ed_license__"+$id).val("");
    $(".primary_lc"+$id).show();
    $(".secondary_lc"+$id).hide();
    $('.edit_license').show();
});
function delete_agent_license($id, license_id) {
    swal({
        text: '<br>Delete Record: Are you sure?',
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
            data: {lid: lid, agent_id: agent_id, ajax_delete: "1"},
            dataType: 'json',
            success: function (res) {
                if (res.status == 'success') {
                    agent_license_ajax_submit();
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
    dropdown_pagination('agent_license_ajax_data');
    agent_license_ajax_submit();

    $(document).on('change',"#doc_main",function(e){
        // $("")
        if($(this).is(":checked")){
            $(".license_chekbox").prop('checked',true);
        }else{
            $(".license_chekbox").prop('checked',false);
        }
    });

    $(document).on('change',".license_chekbox",function(){
        var dataid = $(this).attr('data-id');
        
        if($(this).is(":checked")){
            $(".same_lc"+dataid).prop('checked',true);
        }else{
            $(".same_lc"+dataid).prop('checked',false);
        }
        
        $('.license_chekbox').each(function(e){
            
            if($(this).is(":checked")){
                $("#doc_main").prop('checked',true);
            }
            if($(".license_chekbox").is(":checked") === false){
                $("#doc_main").prop('checked',false);
            }
        });
    });

    $(document).off('click', '#agent_license_ajax_data ul.pagination li a');
    $(document).on('click', '#agent_license_ajax_data ul.pagination li a', function (e) {
        e.preventDefault();
        $('#ajax_loader').show();
        $('#agent_license_ajax_data').hide();
        $.ajax({
            url: $(this).attr('href'),
            type: 'GET',
            success: function (res) {
                $('#ajax_loader').hide();
                $('#agent_license_ajax_data').html(res).show();
                $(document).find("#license_table .dataTables_info").hide();
                $(".license_expiry").datepicker({
                    changeDay: true,
                    changeMonth: true,
                    changeYear: true,
                    startDate: new Date()
                });

                $(".license_active").datepicker({
                    changeDay: true,
                    changeMonth: true,
                    changeYear: true,
                });
                refreshControl('.sel_license_state');
                common_select()
            }
        });
    });

    $(document).off('click', '.ajax_add_license');
    $(document).on('click', '.ajax_add_license', function (e) {
        $("#ajax_loader").show();
        $("p.error").children('span').html('').hide();
        var id = $(this).attr('data-id');

        var license_state = $("#license_state_"+id).val();
        var licsense_authority1 = $("#licsense_authority_"+id).val();
        var license_type = $('#license_type_'+id).val();

        if($('#license_not_expire_'+id).is(":checked"))
        var license_not_expire = 'Y';
        else
        var license_not_expire = 'N';
        var license_expiry = $('#license_expiry_'+id).val();
        var license_number = $('#license_number_'+id).val();
        var license_active_date = $('#license_active_date_'+id).val();    
        var license_type = $('#license_type_'+id).val();
        var licsense_authority = $('#licsense_authority_'+id).val();
        var lid = $("#hdn_license_"+id).val();
        var edit = $("#ed_license__"+id).val();
        var hdn_license = [id];
        $.ajax({
        url : 'ajax_update_agent_account_detail.php',
        data : { section:'is_add_license',agent_id:'<?= $agent_id ?>',agent_id_org:'<?= $agent_id_org ?>',license_expiry:license_expiry,license_not_expire:license_not_expire,license_number:license_number,license_active_date:license_active_date,license_state:license_state,license_type:license_type,licsense_authority:licsense_authority,hdn_license:hdn_license,is_ajax_license:1,step:'ajax',lid:lid,edit:edit},
        method: 'POST',
        dataType: 'json',
        success : function(res){
            if (res.status == 'success') {
            $("#ajax_loader").hide();
            $("#edit_license_"+id).show();
            $("#hidden_btn_"+id).hide();
            if(res.doc_id !== '')
                $(".div_license_"+id).remove();
                if(lid == 0) {
                    setNotifySuccess("Agent license added successfully.");
                } else {
                    setNotifySuccess("Agent license updated successfully.");
                }
                agent_license_ajax_submit();
            } else if(res.status == 'fail'){
            $("#ajax_loader").hide();
            $(".error").hide();
            $.each(res.errors, function(key, value) {
                console.log(res);
                $('.error_' + key).parent("p.error").show();
                $('.error_' + key).html(value).show();

                $('#error_' + key).parent("p.error").show();
                $('#error_' + key).html(value).show();
                if ($("[name='" + key + "']").length > 0) {
                        $('html, body').animate({
                            scrollTop: parseInt($("[name='" + key + "']").offset().top) - 100
                        }, 1000);
                    }
            });
            }
        }
        });
    });
});
function agent_license_ajax_submit() {
    $('#ajax_loader').show();
    $('#agent_license_ajax_data').hide();
    $('#agent_product_is_ajaxed').val('1');
    var params = $('#agent_license_frm_search').serialize();
    $.ajax({
        url: "agent_license.php?id=<?=$agent_id?>&is_ajaxed_license=1&per_pages=10",
        type: 'GET',
        data: params,
        success: function (res) {
            $('#ajax_loader').hide();
            agent_pending_license_ajax_submit();

            $('#agent_license_ajax_data').html(res).show();
            $(document).find("#license_table .dataTables_info").hide();
            refreshControl('.sel_license_state');
            $(".license_expiry").datepicker({
                changeDay: true,
                changeMonth: true,
                changeYear: true,
                startDate:new Date()
            });

            $(".license_active").datepicker({
                changeDay: true,
                changeMonth: true,
                changeYear: true,
            });
            common_select();
            $('.license_not_expire').uniform();
        }
    });
    return false;
}
</script>
<?php } ?>