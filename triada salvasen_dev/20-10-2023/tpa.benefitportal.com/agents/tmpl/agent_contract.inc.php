<?php include('notify.inc.php'); ?>
<div class="login_wrap">
  <div class="login-right_panel">
    <form method="post" autocomplete="off" id="agent_contract_form">
        <input type="hidden" name="agent_id" id="agent_id" value="<?= checkIsset($agent_res['id']) ?>">
        <input type="hidden" id="enroll_type" name="enroll_type" value="<?=$enrollType?>" />
        <input type="hidden" id="profile_id" name="profile_id" value="<?=$profile_id?>" />
        <div class="login-form mw350 theme-form">
            <p class="fs20 m-b-20">Setup your account below.</p>
            <div class="form-group">
              <input type="text" class="form-control input-lg <?= !empty($fname) ? 'has-value' : '' ?>" name="fname" id="fname" value="<?= $fname ?>" />
              <label>First Name</label>
                <div id="fname_err" class="mid"><span></span></div>
                <p class="error"><span id="err_fname"></span></p>
            </div>
            <div class="form-group">
              <input type="text" class="form-control input-lg <?= !empty($lname) ? 'has-value' : '' ?>" name="lname" id="lname" value="<?= $lname ?>" />
              <label>Last Name</label>
                <div id="lname_err" class="mid"><span></span></div>
                <p class="error"><span id="err_lname"></span></p>
            </div>
            <div class="form-group">
              <input type="text" class="form-control no_space input-lg <?= !empty($email) ? 'has-value' : '' ?>" name="email" id="email" value="<?= $email ?>" />
              <label>Email</label>
                <div id="email_err" class="mid"><span></span></div>
                <p class="error"><span id="err_email"></span></p>
            </div>
            <div class="form-group">
              <input type="text" class="form-control input-lg <?= !empty($phone) ? 'has-value' : '' ?>" name="cell_phone" id="cell_phone" value="<?= $phone ?>" />
              <label>Phone</label>
                <div id="cell_phone_err" class="mid"><span></span></div>
                <p class="error"><span id="err_cell_phone"></span></p>
            </div>
            <div class="form-group">
                <input type="password" id="password" name="password" value="" class="form-control input-lg"  maxlength="20" 
                onblur="check_password(this, 'password_err', 'error_password', event, 'input_validation');" 
                onkeyup="check_password_Keyup(this, 'password_err', 'error_password', event, 'input_validation');">
              <label>Password</label>
                <div id="password_err" class="mid"><span></span></div>
                <p class="error"><span id="err_password"></span></p>
            
                <div id="pswd_info" class="pswd_popup" style="display: none">
                    <div class="pswd_popup_inner">
                      <h4>Password Requirements</h4>
                      <ul>
                        <li id="pwdLength" class="invalid"><em></em>Minimum 8 Characters</li>
                        <li id="pwdUpperCase" class="invalid"><em></em>At least 1 uppercase letter </li>
                        <li id="pwdLowerCase" class="invalid"><em></em>At least 1 lowercase letter </li>
                        <li id="pwdNumber" class="invalid"><em></em>At least 1 number</li>
                      </ul>
                      <div class="btarrow"></div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <input type="password" id="c_password" name="c_password" class="form-control input-lg"  maxlength="20">
              <label>Confirm Password</label>
                <div id="c_password_err" class="mid"><span></span></div>
                <p class="error"><span id="err_c_password"></span></p>
            </div>
            <div class="form-group height_auto text-right">
              <button type="button" class="btn btn-action" name="agent_contract" id="agent_contract">Submit</button>
            </div>
      </div>
    </form>
    <div class="login-footer">
      <p class="fs12 text-black mn"><?=$POWERED_BY_TEXT;?></p>
    </div>
  </div>
</div>

<div style="display:none">
    <div class="panel-body login-alert-modal" id="successWindow" >
        <div class="media br-n pn mn">
            <div class="media-left"> <img src="<?= $AGENT_HOST ?>/images/<?= $DEFAULT_LOGO_IMAGE ?>" align="left"> </div>
            <div class="media-body theme-form">
                <h3 class="blue-link m-t-n fw600 fs24 m-b-10" >Success!</h3>
                <p class="m-b-20">Your agent account has been created successfully. Please click the button below to sign in to your account.</p>
                <a href="<?= $AGENT_HOST ?>" id="btn_begin_enrollment" class="btn btn-info confirm">Begin Enrollment</a>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        checkEmail();
        $("#cell_phone").inputmask({"mask": "(999) 999-9999",'showMaskOnHover': false});
    });

    $(document).off("click","#agent_contract");
    $(document).on("click","#agent_contract",function(e){
        e.preventDefault();
        $('.error span').html('');
        $('#ajax_loader').show();
        $.ajax({
          url:"<?= $AGENT_HOST ?>/ajax_agent_contract.php",
          data: $("#agent_contract_form").serialize(),
          method: 'POST',
          dataType: 'json',
          success: function(res) {
              $('#ajax_loader').hide();
              
              if (res.status == 'account_approved') {
                 $("#btn_begin_enrollment").attr('href',"<?= $AGENT_HOST ?>/index.php?t=" + res.agent_rep_id);

                 $.colorbox({
                    inline: true, 
                    width: "400px", 
                    height: "225px", 
                    overlayClose: false, 
                    closeButton: false,
                    href: "#successWindow", 
                });    
              } else if (res.status == 'fail') {
                  var is_error = true;
                  $('.error span').html('');
                  $.each(res.errors, function (index, value) {
                      $('#err_' + index).html(value).show();
                      if(is_error){
                          var offset = $('#err_' + index).offset();
                          var offsetTop = offset.top;
                          var totalScroll = offsetTop - 50;
                          $('body,html').animate({scrollTop: totalScroll}, 1200);
                          is_error = false;
                      }
                  });
              }else if(res.status == 'no_agent_found'){
                setNotifyError("No Agent Found");
              }
              return false;
          }
        });
    });
</script>