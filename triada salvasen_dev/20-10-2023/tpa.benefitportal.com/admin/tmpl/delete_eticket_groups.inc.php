<form id="assigne_new_group_eticket"  name="assigne_new_group_eticket" method="POST">
  <input type="hidden" name="categoryId" id="action" value="<?=$categoryId?>">
  <input type="hidden" name="is_deleted" id="is_deleted" value="Y">
  <input type="hidden" name="is_confirm_delete" id="is_confirm_delete" value="Y">
    <div class="panel panel-default panel-block ">
      <div class="panel-heading">
        <div class="panel-title">
        <h4 class="mn"><img src="images/icons/add_level_icon.svg" height="20px" />&nbsp;&nbsp;
        Delete E-Ticket Group - <span class="fw300"><?=$oldcategory['title']?></span></h4>
      </div>
      </div>
      <div class="panel-body">
        <div class="theme-form">
          <p class="m-b-15">Before you are able to delete this eTicket group level, you must first reassign these etickets to another group. Please select group and admin below:</p>
        <div class="table-responsive">
            <table class="<?=$table_class?> ">
                <thead>
                <tr class="data-head">
                    <th >User ID/Name - Tickets</th>
                    <th >Group</th>
                    <th >Assignee</th>
                </tr>
                </thead>
                <tbody>
                    <?php if ( count($user_res) > 0) {
                        foreach ($user_res as $rows) { ?>
                            <tr>
                                <td><?= $rows['name'].' - <span class="text-red">'.$rows['total_tickets'].' eticket(s)</span>' ?></td>
                                <td>
                                    <input type="hidden" name="user_ids[<?=$rows['user_id']?>]" value="<?=$rows['user_id']?>">
                                    <input type="hidden" name="s_ticket_ids[<?=$rows['user_id']?>]" value="<?=$rows['id']?>">
                                    <div class="form-group height_auto mn w-160">
                                        <select name="group_name[<?= $rows['user_id'] ?>]" class="form-control changecategory" data-id="<?= $rows['id'] ?>" data-user_id="<?= $rows['user_id'] ?>">
                                            <option hidden></option>
                                            <?php if(!empty($groups)) { ?>
                                                <?php foreach ($groups as $grp) {?>
                                                    <option value="<?php echo $grp["id"]; ?>"><?php echo $grp["title"]; ?>
                                                    </option>
                                            <?php } }?>
                                        </select>
                                        <label for="">Group</label>  
                                        <p class="error" id="error_group_name_<?= $rows['user_id'] ?>"></p>
                                    </div>   
                                </td>
                                <td>
                                    <div class="from-group height_auto mn w-160" id="assignee_<?=$rows['user_id']?>">
                                    </div>                               
                                </td>
                            </tr>
                        <?php }?>
                    <?php } else {?>
                        <tr>
                            <td colspan="3" class="text-center">No record(s) found</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
          </div>
        </div>
        <div class="text-center m-t-20">
          <button class="btn btn-action" type="button" name="save_btn" id="save_btn" tabindex="6" >Delete</button>
          <a href="javascript:void(0);" onclick='parent.$.colorbox.close();' class="btn red-link">Cancel</a>
        </div>
      </div>
   
  </div>
</form>

<script type="text/javascript">
  $(document).off('click', '#save_btn');
  $(document).on("click", "#save_btn", function() {
    $("#ajax_loader").show();
    $(".error").html('');
    var $id = $("#id").val();
    $.ajax({
      url: 'ajax_add_etickets_groups.php',
      dataType: 'json',
      data: $("#assigne_new_group_eticket").serialize(),
      type: 'POST',
      success: function(res) {
        $("#ajax_loader").hide();
        if (res.status == "success") {
            parent.$.colorbox.close();
            parent.get_eticket_groups();
            parent.setNotifySuccess("Group deleted successfully.");
        }else if(res.status == "fail"){
            parent.$.colorbox.close();
            parent.get_eticket_groups();
            parent.setNotifyError("Something went wrong.");
        } else {
            $.each(res.errors,function(index,error){
                $('#error_' + index).html(error).show();
            });
        }
      }
    });
  });

$(document).off('change','.changecategory');
$(document).on('change','.changecategory',function(e){
  e.stopImmediatePropagation();
  var $cid = $(this).attr('data-id');
  var $user_id = $(this).attr('data-user_id');
  $("#assignee_"+$cid).removeClass('form-group');
  $("#assignee_"+$cid).html("");
  $.ajax({
    url:"ajax_add_etickets_groups.php",
    data:{
      is_ajaxed_get_assignee :1,
      categoryId : $(this).val(),
      ticket_id : $cid,
      user_id : $user_id,
    },
    dataType:'json',
    type:'post',
    beforeSend :function(e){
      $("#ajax_loader").show();
    },
    success:function(res){
      $("#ajax_loader").hide();
      if(res.status == 'success'){
          $("#assignee_"+$user_id).html(res.data_html);
      }else{
        $("#assignee_"+$user_id).html(res.data_html);
      }
      $("#assignee_"+$user_id).addClass('form-group');
      common_select();
    }
  });
});
</script>