<style type="text/css" media="screen">
.sub_features .checker {display: inline-block;}
</style>
  <div class="panel panel-default panel-block theme-form">
  <div class="panel-heading">
    <div class="panel-title">
      <h4 class="mn"><img src="images/icons/add_level_icon.svg" height="20px" />&nbsp;&nbsp;<strong class="fw500">Edit Admin Level - </strong> <span class="fw300"><?=$adminsRow['fname']?> <?=$adminsRow['lname']?></span></h4>
    </div>
  </div>
  <div class="panel-body ">
    <div class="form-group">
        <form method="post">
          <select id="status_s_<?php echo $_GET['id']; ?>" class="status_s form-control select2 placeholder has-value" data-old_status="<?=$adminsRow['status']?>"  name="status_s">
            <option value="Pending"   <?php if ($adminsRow['status'] == 'Pending') { echo "selected='selected'"; } ?>>Pending</option>
            <option value="Active"    <?php if ($adminsRow['status'] == 'Active') { echo "selected='selected'"; } ?>>Active</option>
            <option value="Inactive" <?php if ($adminsRow['status'] == 'Inactive') { echo "selected='selected'"; } ?>>Inactive</option>
            <?php 
              if(!empty($adminsRow['status']) && !in_array($adminsRow['status'],array("Pending","Active","Inactive"))) {
                echo '<option value="'.$adminsRow['status'].'" selected="selected">'.$adminsRow['status'].'</option>';
              }
            ?>
          </select>
          <label>Account Status</label>
        </form>
      </div>
      <form name="frm" id="frm" class="feature_submit" method="POST">
    <div class="form-group">
        <select  name="access_level" class="change_type11 form-control select2 placeholder has-value" data-old_type="<?=$adminsRow['type']?>" id="change_type11_<?php echo $_GET['id']; ?>">
          <?php 

          if($type != ''){
             foreach($res_acls as $level) {?>
              <option value="<?=$level['name']?>"  <?php echo $type == $level['name'] ? 'selected="selected"' : ''; ?> ><?=$level['name']?></option>
            <?php }
           } else{  
             foreach($res_acls as $level) {?>
              <option value="<?=$level['name']?>"  <?php echo $level['name'] == $adminsRow['type'] ? 'selected="selected"' : ''; ?> ><?=$level['name']?></option>
            <?php } } ?>
        </select>
         <label>Access Level</label>
      </div>
              <div class="clearfix"></div>
                <?php $count = 1;?>
              <?php include('acl.inc.php');?>
              <div class="clearfix m-t-30"></div>
                    <div class="text-center">
                      <button type="submit" id="submitaccess" name="submitaccess" value="Save" class="btn btn-action ">Submit</button>
                      <a href="javascript:void(0);" onclick="parent.$.colorbox.close(); return false;" class="btn red-link">Cancel</a>
                    </div>
                  <?php foreach ($curAccess as $cf) {?>
                  <input type="hidden" name="new_feature[]" value="<?=$cf?>" />
                  <?php }?>
                </form>
    </div>
</div>
<script type="text/javascript">
  $(document).ready(function() {
      var type = $('.change_type11').val();
      if((type != "Special Administrator") &&  (type != "Customer Support Agent")){
        $(".modules-list").hide();
      } else {
        $(".modules-list").show();
      }

      var status_s = $('.status_s').val();
      if(status_s == "Active"){
        $(".modules-list").show();
      } else {
        $(".modules-list").hide();
      }

     $('.status_s').change(function(){
      var id = $(this).attr('id').replace('status_s_', '');
      var idstr = $(this).attr('id');
      var status_s = $(this).val();
      var old_status = $(this).attr('data-old_status');
      swal({
          text: "Change Status: Are you sure?",
          showCancelButton: true,
          confirmButtonText: "Confirm",
      }).then(function(){
          $.ajax({
            url: 'ajax_update_profile_status.php',
            data: {id: id, status: status_s, old_status:old_status},
            method: 'POST',
            dataType: 'json',
             success: function(res) {
                if (res.status == 'success') {
                  window.parent.$.colorbox.close();
                  setNotifySuccess("res.msg");
                } else {
                  //setNotifyError(res.err);
                }
              }
          });
        },function (dismiss){
        $("#"+idstr).val(old_status);
        $("#"+idstr).selectpicker('render');
      });
    });
    $('.change_type11').change(function(){
       var id = $(this).attr('id').replace('change_type11_', '');
       var type = $(this).val();
       var old_type = $(this).attr('data-old_type');
       swal({
           text: "Change Admin Level: Are you sure?",
           showCancelButton: true,
           confirmButtonText: "Confirm",
        }).then(function(){
          $("#ajax_loader").show();
          window.location = 'admins.php?id=' + id + '&type=' + type + '&old_type='+old_type;
          },function(dismiss){
            $('#change_type11_'+id).val(old_type);
            $('#change_type11_'+id).selectpicker('render');
            return false;
        });
    });

    $('#submitaccess').click(function() {
      $("#submitaccess").append("<i class='icon icon-spinner icon-spin'></i>");
      if ($('input[name*="feature"]:checked').size() <= 0) {
        alert('Please select atleast one');
        $("#submitaccess").find('i').remove();
        return false;
      }
      return true;
    });

  });
</script>