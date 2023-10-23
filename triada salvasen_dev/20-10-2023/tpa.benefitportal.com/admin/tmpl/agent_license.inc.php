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
        <td><?=$doc['license_num']?><p class="error"><span class="error_license_number_<?=$key?>"></span></p></td>
        <td class="text-center"><?=date('m/d/Y',strtotime($doc['license_active_date']))?><p class="error"><span class="error_license_active_date_<?=$key?>"></span></p></td>
        <td class="text-center"><?=$doc['license_not_expire'] == 'Y' ? 'Does Not Expire' : date('m/d/Y',strtotime($doc['license_exp_date']))?><p class="error"><span class="error_license_expiry_<?=$key?>"></span></p></td>
        <td class="text-center">
        <?php 
         echo $doc['license_type'] =="Business" ? 'Agency' : '' ;
         echo $doc['license_type'] =="Personal" ? 'Agent' : ''  ?>
        <p class="error"><span class="error_license_type_<?=$key?>"></span></p></td>
        <td class="text-center"><?=$doc['license_auth'] == 'general_lines' ? 'General Lines (Both)' : $doc['license_auth'] ;?><p class="error"><span class="error_licsense_authority<?=$key?>"></span></p></td>
        <td class="text-center"><a href="javascript:void(0);" class="red-link fw500 pn edit_license" 
        data-id="<?=$doc['id']?>">Edit</a></td>
    </tr>
    <tr class="secondary_<?=$doc['id']?> secondary_lc<?=$key?>" style="display:none">
        <td class="license_portion">
            <input type="hidden" name='hdn_license[<?=$key?>]' value="<?=$doc['id']?>" id='hdn_license_<?=$key?>'>
            <?=$doc['license_status']?>
            <input type="hidden" name="edit[<?=$key?>]" value="" id="ed_license__<?=$key?>" class="ed_license__<?=$doc['id']?>">
        </td>
        <td>
            <select name="license_state[<?=$key?>]" id="license_state_<?=$key?>"  class="mw150 select_class_<?=$key?> sel_license_state form-control selected_license_states" data-id="<?=$key?>" >
                <option value=""></option>
                <?php if ($allStateRes) {?>
                <?php foreach ($allStateRes as $state) {
                    $hide_states=(!empty($selectedState)?array_diff($selectedState,$state):array());?>
                <option <?=$state["name"] == $doc['selling_licensed_state'] ? 'selected' : ''?> value="<?=$state["name"];?>" <?=in_array($state,$hide_states)?'disabled':'' ?>><?php echo $state['name']; ?></option>
                <?php }?>
                <?php }?>
            </select>
            <p class="error"><span class="error_license_state_<?=$key?>"></span></p>
        </td>
        <td>
            <input name="license_number[<?=$key?>]" id="license_number_<?=$key?>" type="text" class="form-control license_number"   value="<?=$doc["license_num"]?>" data-id="<?=$key?>">
            <label>License Number<em>*</em></label>
            <p class="error"><span class="error_license_number_<?=$key?>"></span></p>
        </td>
        <td class="text-center">
            <input type="text" name="license_active_date[<?=$key?>]" value="<?=(isset($doc["license_active_date"]) && $doc["license_active_date"] != "" && $doc["license_active_date"] != "0000-00-00") ? date("m/d/Y", strtotime($doc["license_active_date"])) : ""?>" id="license_active_date_<?=$key?>" class="form-control license_active" data-id="<?=$key?>" />
            <p class="error"><span class="error_license_active_date_<?=$key?>"></span></p>
        </td>
        <td class="text-center text-nowrap">
            <input name="license_expiry[<?=$key?>]" id="license_expiry_<?=$key?>" type="text" class="form-control license_expiry"  value="<?=(isset($doc["license_exp_date"]) && $doc["license_exp_date"] != "" && $doc["license_exp_date"] != "0000-00-00") ? date("m/d/Y", strtotime($doc["license_exp_date"])) : "Does Not Expire"?>" <?=checkIsset($doc['license_not_expire']) == 'Y' ? "readonly='readonly'" : '' ?> data-id="<?=$key?>" >
            <p class="error"><span class="error_license_expiry_<?=$key?>"></span></p>
            <div class="clearfix m-t-5">
            <label for="license_not_expire[<?=$key?>]" class="text-red fs12 mn" >
            <input type="checkbox" name="license_not_expire[<?=$key?>]" id="license_not_expire_<?=$key?>" value="Y" <?=checkIsset($doc['license_not_expire']) == 'Y' ? "checked='checked'" : '' ?> class="license_not_expire" data-id="<?=$key?>"> License does not expire</label>
        </td>
        <td class="text-center">
            <select class="form-control sel_license_state license_types" name="license_type[<?=$key?>]" id="license_type_<?=$key?>" data-id="<?=$key?>" >
                <option value="" disabled selected hidden> </option>
                <option value="Business" <?=$doc["license_type"] == 'Business' ? 'selected' : ''?>>Agency</option>
                <option value="Personal" <?=$doc["license_type"] == 'Personal' ? 'selected' : ''?>>Agent</option>
            </select>
            <p class="error"><span class="error_license_type_<?=$key?>"></span></p>
        </td>
        <td class="text-center">
            <select class="form-control sel_license_state licsense_authority" name="licsense_authority[<?=$key?>]" id="licsense_authority_<?=$key?>" data-id="<?=$key?>">
            <option value="" disabled selected hidden> </option>
                <option value="Health" <?=$doc["license_auth"] == 'Health' ? 'selected' : ''?>>Health</option>
                <option value="Life" <?=$doc["license_auth"] == 'Life' ? 'selected' : ''?>>Life</option>
                <option value="general_lines" <?=$doc["license_auth"] == 'general_lines' ? 'selected' : ''?>>General Lines (Both)</option>
            </select>
            <p class="error"><span class="error_licsense_authority<?=$key?>"></span></p>
        </td>
        <td class="text-center text-nowrap">
            <a href="javascript:void(0);" class="text-success ajax_add_license" data-toggle="tooltip" title="Save" data-id="<?=$key?>"><i  class="fa fa-check fa-lg"></i></a>
            <a href="javascript:void(0);" class="text-red" data-toggle="tooltip" title="Delete"  data-id="<?=$key?>" onclick="delete_agent_license(<?=$key?>,<?=$doc['id']?>)" ><i class="fa fa-trash fa-lg"></i></a>
            <a href="javascript:void(0);" data-toggle="tooltip" title="Cancel"  class="cancel_edit" data-id="<?=$key?>"><i class="fa fa-close fa-lg"></i></a>
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
        <!-- <div class="pull-left"><a href="javascript:void(0);" class="btn btn-info">+  License</a></div> -->
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
<script type="text/javascript">

if(typeof($) !== 'undefined') {
    $(document).ready(function () {
        agent_license_ajax_submit();
        dropdown_pagination('agent_license_ajax_data');
    });
}

$(document).on('change',"#doc_main",function(e){
        // $("")
        if($(this).is(":checked")){
            $(".license_chekbox").prop('checked',true);
        }else{
            $(".license_chekbox").prop('checked',false);
        }
        //$("input[type='checkbox']").uniform();
    });

$(document).on('change',".license_chekbox",function(){
    // $("#doc_main").prop('checked',true);
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
    //$("input[type='checkbox']").uniform();
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
            // refreshControl('.sel_license_state');
            fRefresh();
            common_select();
            $('.license_not_expire').uniform();
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
    data : { license_expiry:license_expiry,license_not_expire:license_not_expire,license_number:license_number,license_active_date:license_active_date,license_state:license_state,license_type:license_type,licsense_authority:licsense_authority,hdn_license:hdn_license,is_ajax_license:1,step:'ajax',lid:lid,agent_id:'<?=$id?>',edit:edit},
    method: 'POST',
    dataType: 'json',
    success : function(res){
        if (res.status == 'success') {
        $("#ajax_loader").hide();
        $("#edit_license_"+id).show();
        $("#hidden_btn_"+id).hide();
        setNotifySuccess("license update successful.");
        if(res.doc_id !== '')
            $(".div_license_"+id).remove();
            agent_license_ajax_submit();
        }else if(res.status == 'fail'){
        $("#ajax_loader").hide();
        $("p.error").hide();
        $.each(res.errors, function(key, value) {
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

$(document).off('click', '.cancel_edit');
$(document).on('click', '.cancel_edit', function (e) {
    $("p.error").children('span').html('').hide();
    var $id = $(this).attr('data-id');
    $("#ed_license__"+$id).val("");
    $(".primary_lc"+$id).show();
    $(".secondary_lc"+$id).hide();
    $('.edit_license').show();
});

$(function(){      
    trigger(".add_more_license",function($this,e){
        index = parseInt($(".license_portion").length);
        $display_counter = parseInt($('#license_display_counter').val());
        $number=index+1;
        if($display_counter > index){
        $number = $display_counter + 1;
        }
        pos_number = $number;
        
      $('#add_license_div').before($(".license_template").html().replace(/~i~/g,pos_number));
      $("#license_display_counter").val($number);
      $('.select_class_'+pos_number).addClass('form-control');
      $('.select_class_'+pos_number).selectpicker({ 
          container: 'body', 
          style:'btn-select',
          noneSelectedText: '',
          dropupAuto:false,
        });
      $(".div_license_"+pos_number+" :input").selectpicker('refresh');
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
      $(".license_not_expire").uniform();
    //   if(index >=52)
    //     $(".add_more_license").hide();

    });

    trigger(".remove_license",function($this,e){
      var id = $this.attr('data-id');
      var lid = $("#hdn_license_"+id).val();
    //   index = parseInt($(".license_portion").length);
    //   if(index < 54)
    //     $(".add_more_license").show();
      $this.parents(".license_portion").fadeOut("slow",function(){
        $(this).remove();
      });
    });

    trigger(".license_not_expire",function($this,e){
      var id = $this.attr('data-id');
      if($('#license_not_expire_'+id).is(":checked"))
        {
          $("#license_expiry_"+id).attr('readonly','readonly');
          $("#license_expiry_"+id).val("12/31/2099");
          $("#license_expiry_"+id).addClass("has-value");
        }
      else
        {
          $("#license_expiry_"+id).removeAttr('readonly');
          $("#license_expiry_"+id).val("");
          $("#license_expiry_"+id).removeClass("has-value");
        }
    });

  });

  $(document).off('click',".edit_license");
  $(document).on('click',".edit_license",function(e){
        var $id = $(this).attr('data-id');
        $(".ed_license__"+$id).val($id);
        $(".primary_"+$id).hide();
        $(".secondary_"+$id).show();
        $('.edit_license').hide();
  });

function delete_agent_license($id,license_id) {
    // console.log($id);
    swal({
        text: '<br>Delete Record: Are you sure?',
        showCancelButton: true,
        confirmButtonText: 'Confirm',
        cancelButtonText: 'Cancel',
    }).then(function(e) {
        var agent_id = $("#agent_id").val();
        var lid = license_id;
        $("#ajax_loader").show();
        $.ajax({
        url : 'ajax_update_agent_account_detail.php',
        method: 'POST',
        data:{lid:lid,agent_id:'<?=$id?>',ajax_delete:"1"},
        dataType: 'json',
        success : function(res){
            if(res.status == 'success') {
            $("#ajax_loader").hide();
            // $this.parents(".license_portion").fadeOut("slow",function(){
                $(".primary_lc"+$id).remove();
                $(".secondary_lc"+$id).remove();
            // });
            var total_rec = $("#license_table tr").length;
            if(total_rec < 3){
                $("#license_table tfoot").html("");
                $("#license_table tbody").html("<tr><td colspan='9'>No license found!</td></tr>");
            }
            agent_license_ajax_submit();
            setNotifySuccess("Agent license deleted successfully.");
            /* refreshLicense("#license_state"); */
            }else if(res.status == 'fail'){
            $("#ajax_loader").hide();
            setNotifyError("Agent license not deleted.");
            }
        }
        });
    }, function(dismiss) {})
}

function agent_license_ajax_submit() {
    $('#ajax_loader').show();
    $('#agent_license_ajax_data').hide();
    $('#agent_product_is_ajaxed').val('1');
    var params = $('#agent_license_frm_search').serialize();
    $.ajax({
        url: "agent_license.php?id=<?=$_REQUEST['id']?>&is_ajaxed_license=1&per_pages=10",
        type: 'GET',
        data: params,
        success: function (res) {
            $('#ajax_loader').hide();
            $('#agent_license_ajax_data').html(res).show();
            $(document).find("#license_table .dataTables_info").hide();
            // refreshControl('.sel_license_state');
            fRefresh();
            common_select();
            $('[data-toggle="tooltip"]').tooltip();
            $('.license_not_expire').uniform();
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
            
            // setPopoverAndTooltip();
        }
    });
    return false;
}
</script>
<?php } ?>