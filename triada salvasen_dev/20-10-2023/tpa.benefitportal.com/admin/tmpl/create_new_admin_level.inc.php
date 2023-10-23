<?php if((!isset($_GET['action'])) || (isset($_GET['action']) &&  $_GET['action'] =='edit')) { ?>
<form id="create_new_admin_level_frm"  name="create_new_admin_level_frm" method="POST">
  <input type="hidden" name="action" id="action" value="<?= checkIsset($_GET['action']) ?>">
  <input type="hidden" name="id" id="id" value="<?=checkIsset($_GET['id']) ?>">
    <div class="panel panel-default panel-block theme-form">
      <div class="panel-heading">
        <h4 class="mn fw500"><img src="images/icons/add_level_icon.svg" height="20px" />&nbsp;&nbsp;<?=isset($_GET['action']) ? 'Update ' : 'Admin';?>
        Level</h4>
      </div>
      <div class="panel-body ">
        <div class="form-group ">
          <input type="text" name="access_name" class="form-control" value="<?=$selected_name;?>" />
          <label>Access Level Name</label>
          <p class="error" id="error_access_name"></p>
        </div>
        <div class="form-group ">
          <select name="dashboard[]" class="form-control">
            <option value="Executive Dashboard"  <?php echo $selected_dashboard == 'Executive Dashboard' ? 'selected' : '' ;?>>Executive Dashboard</option>
            <option value="Support Dashboard" <?php echo $selected_dashboard == 'Support Dashboard' ? 'selected' : '';?>>Support Dashboard</option>
          </select>
          <p class="error" id="error_dashboard"></p>
        </div>
        <?php include('acl.inc.php');?>
        <div class="text-center m-t-30">
          <?php $button_label = isset($_GET['action']) && $_GET['action'] == 'delete' ? 'Delete' : 'Save';?>
          <?php if(isset($_GET['action']) ? $_GET['action'] : ''  != "show") { ?>
          <button class="btn btn-action " type="button" name="save_btn" id="save_btn" tabindex="6" ><?=$button_label;?> </button>
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
    $.ajax({
      url: 'ajax_create_new_admin_level.php',
      dataType: 'JSON',
      data: $("#create_new_admin_level_frm").serialize(),
      type: 'POST',
      success: function(res) {
        $("#ajax_loader").hide();
        if (res.status == "success") {
          window.parent.location.href = "add_access_level.php";
        } else {
          var is_error = true;
          $.each(res.errors, function(index, error) {
            $('#error_' + index).html(error);
            if (is_error) {
              var offset = $('#error_' + index).offset();
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
</script>
<?php }else{
  include_once __DIR__.'/change_admin_access_level.inc.php';
} ?>