<div class="panel panel-default panel-block ">
  <div class="panel-heading">
    <div class="panel-title ">
      <h4 class="mn">+ Account Managers</h4>
    </div>
  </div>
  <div class="panel-body">
    <form id="create_new_admin_level_frm"   method="POST" action="agents_add_account_managers.php">
      <input type="hidden" name="agent_id" id="agent_id" value="<?=$_GET['agent_id']?>">
      <div class="row">
        <?php if(!empty($sub_agent)){ ?>
          <input type="hidden" name="sub_id" value="<?=$_GET['sa_id']?>">
          <input type="hidden" name="operation" value="edit_sub_agent">
        <?php } ?>
          <div class="col-sm-5">
            <div class="theme-form">
              <div class="form-group">
                <input type="text" class="form-control" name="fname" id="fname" value="<?=checkIsset($sub_agent['fname'])?>" />
                <label>First Name<em>*</em></label>
                <span id="error_fname" class="error"></span>
              </div>
              <div class="form-group">
                <input type="text" class="form-control" name="lname" id="lname" value="<?=checkIsset($sub_agent['lname'])?>" />
                <label>Last Name<em>*</em></label>
                <span id="error_lname" class="error"></span>
              </div>
              <div class="form-group">
                <input type="text" class="form-control no_space" name="email" id="email" value="<?=checkIsset($sub_agent['email'])?>" />
                <label>Email<em>*</em></label>
                <span id="error_email" class="error"></span>
              </div>
              <div class="form-group">
                <input type="password" class="form-control" name="password" id="password" value="<?=checkIsset($password)?>"  />
                <label>Password<em>*</em></label>
                <span id="error_password" class="error"></span>
              </div>
              <div class="form-group">
                <input type="password" class="form-control" name="cpassword" id="cpassword"  value="<?=checkIsset($cpassword)?>" />
                <label>Confirm Password<em>*</em></label>
                <span id="error_cpassword" class="error"></span>
              </div>
              <div class="clearfix">
                <div class="phone-control-wrap">
                  <div class="phone-addon text-left w-42 v-align-top">
                    <input type="checkbox" name="passcode" value="Y" id="passcode" <?=checkIsset($sub_agent['passcode']) == 'Y' ? 'checked="checked"' : '' ?>  /> 
                  </div>
                  <div class="phone-addon text-left">
                    <label for="passcode" class="mn">Check this box if account manager may manage members without requesting a passcode.</label>
                  </div>
                </div>
              </div>
              <div class="clearfix m-t-25">
                <div class="phone-control-wrap">
                  <div class="phone-addon text-left w-42 v-align-top">
                    <input type="checkbox" name="edit_enrollment" value="Y" id="editEnrollment" <?=checkIsset($sub_agent['edit_enrollment']) == 'Y' ? 'checked="checked"' : '' ?>  /> 
                  </div>
                  <div class="phone-addon text-left">
                    <label for="editEnrollment" class="mn">Check this box if account manager is a licensed agent and may edit applications on behalf of this agent and direct LOA agents from this single login.</label>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-sm-7">
              <div class="table-responsive">
                <?php 
                  $account_manager = 'account_manager';
                  include 'agents_access_edit.inc.php'; 
                ?>
                <span class="error"><?=checkIsset($errors['features'])?></span>
              </div>
          </div>
      </div>
      <div class="clearfix m-t-30"></div>
      <?=generate2FactorAuthenticationUI($sub_agent,array('main_class'=>'col-sm-10',
                                                          'offsetClass'=>'col-sm-8 col-sm-offset-2 m-b-25'
                                                          ))?>
      <div class="text-center m-t-30"> 
        <input type="hidden" name="is_ajax_submit" id="is_ajax_submit" value="">
        <button class="btn btn-action" type="submit" name="save">Save</button>
        <a href="agents_account_managers.php?agent_id=<?=$_GET['agent_id']?>" class="btn red-link">Back</a>
        <a href="javascript:void(0);" class="btn red-link" onclick='parent.$.colorbox.close(); return false;'>Cancel</a>
      </div>
    </form>
  </div>
</div>
<?=generateIPAddressUI()?>
<script type="text/javascript">
$(document).ready(function(e){

  checkEmail();

  $("#via_mobile").mask("(999) 999-9999");
  $('#create_new_admin_level_frm').bind('submit',function(e) {
    e.preventDefault();
    $('#ajax_loader').show();
    $(".error").hide();
    $("#is_ajax_submit").val(1);
    var params = $('#create_new_admin_level_frm').serialize();
    var agent_id = $("#agent_id").val();
    $.ajax({
        url: $('#create_new_admin_level_frm').attr('action'),
        data: params,
        method: 'POST',
        dataType: 'json',
        success: function(res) {
          $("#is_ajax_submit").val("");
          $('#ajax_loader').hide();
          if (res.status == "success" ) {
              window.location='agents_account_managers.php?agent_id='+agent_id;
          } else if (res.status == "error") {
              var tmp_flag = true;
              $.each(res.errors, function (key, value) {
                  $('#error_' + key).html(value).show();
                  $('#error_' + key).parent("p.error").show();
                  if(tmp_flag == true && $("#error_" + key).length > 0) {
                      tmp_flag = false;
                      $('html, body').animate({
                          scrollTop: parseInt($("#error_" + key).offset().top) - 100
                      }, 1000);
                  }
              });            
          }
        }
    });
  });
});
<?=generate2FactorAuthenticationJS()?>
</script>