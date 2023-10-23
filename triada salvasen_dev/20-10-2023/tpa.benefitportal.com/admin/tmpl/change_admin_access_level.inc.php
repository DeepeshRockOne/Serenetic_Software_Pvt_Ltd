<form id="create_new_admin_level_frm"  name="create_new_admin_level_frm" method="POST">
  <input type="hidden" name="action" id="action" value="">
  <input type="hidden" name="id" id="id" value="<?= $_GET['id'] ?>">
    <div class="panel panel-default panel-block ">
      <div class="panel-heading">
        <h4 class="mn"><img src="images/icons/add_level_icon.svg" height="20px" />&nbsp;&nbsp;
        Delete Access Level</h4>
      </div>
      <div class="panel-body">
        <div class="theme-form1">
          <p class="m-b-15">Before you are able to delete this access level, you must first reassign those admins to another level. Please update access level to the following admins and click save.</p>
        <div class="table-responsive br-n">
            <table class="<?=$table_class?> ">
                <thead>
                <tr class="data-head">
                    <th><a href="javascript:void(0);">Admin ID</a></th>
                    <th><a href="javascript:void(0);">Admin Name</a></th>
                    <th style="width: 185px;"><a href="javascript:void(0);">Access Level</a></th>
                </tr>
                </thead>
                <tbody>
                    <?php if ( count($admin_res) > 0) {
                        foreach ($admin_res as $rows) { ?>
                            <tr>
                                <td><?= $rows['display_id']; ?></td>
                                <td><?= $rows['name']; ?></td>
                                <td>
                                    <div class="theme-form pr">
                                        <input type="hidden" name="admin_ids[]" value="<?=$rows['id']?>">
                                        <select name="level_name[]" class="form-control">
                                            <option value="" disabled selected hidden> </option>
                                            <?php if(!empty($acls)) { ?>
                                                <?php foreach ($acls as $acl) {?>
                                                  <?php 
                                                      if($acl['name'] == $rows['type']) {
                                                          continue;
                                                      }
                                                  ?>
                                                  <option value="<?php echo $acl["name"]; ?>" <?php echo $acl['name'] == $rows['type'] ? 'selected=selected' : ''; ?>><?php echo $acl["name"]; ?></option>
                                                <?php }?>
                                              <?php }?>
                                        </select>
                                        <label>Select Access Level</label>
                                        <p class="error" id="error_level_name_<?=$rows['id'];?>"></p> 
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
          <?php if(isset($_GET['action']) ? $_GET['action'] : ''  != "show") { ?>
          <button class="btn btn-action" type="button" name="save_btn" id="save_btn" tabindex="6" >Reassign & Delete</button>
          <?php } ?>
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
      url: 'ajax_update_admin_level.php',
      dataType: 'JSON',
      data: $("#create_new_admin_level_frm").serialize(),
      type: 'POST',
      success: function(res) {
        $("#ajax_loader").hide();
        if (res.status == "success") {
            $("#ajax_loader").show();
            window.location.href = "create_new_admin_level.php?action=delete&id="+$id;
        } else if (res.status == "error") {
            var is_error = true;
            $.each(res.errors, function(index, error) {
              $('#error_' + index).html(error);
              if (is_error == true && $('#error_' + index).length > 0) {
                var offset = $('#error_' + index).offset();
                var offsetTop = offset.top;
                var totalScroll = offsetTop - 195;
                $('body,html').animate({
                  scrollTop: totalScroll
                }, 1200);
                is_error = false;
              }
            });
        } else {
        
        }
      }
    });
  });
</script>