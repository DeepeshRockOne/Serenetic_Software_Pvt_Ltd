<?php include 'notify.inc.php'; ?>
<div class="login_wrap">
  <div class="login-left-panel">
    <div class="lf_inner">
      <div class="lf_cont"> <img src="<?php echo $HOST; ?>/images/logo_white.svg<?=$cache;?>" alt="">
      </div>
    </div>
  </div>
  <div class="login-right_panel">
    <form role="form" id="sign_up_form" action="" method="POST" autocomplete="off" >
      <div class="login-form mw350 theme-form">
        <h3 class="mb15 fw600"><?= $DEFAULT_SITE_NAME ?> Admin Portal</h3>
        <p class="fs18 m-b-25">Setup your account below.</p>
        <div class="form-group">
          <input class="form-control input-lg" type="text" value="<?php echo isset($row['fname']) ? $row['fname'] : ''; ?>"  id="fname" name="fname" data-error="First Name is required">
          <label>First Name</label>
          <div id="fname_err" class="mid"><span></span></div>
          <p class="error"><span id="error_fname"></span></p>
        </div>
        <div class="form-group">
          <input class="form-control input-lg" type="text" value="<?php echo isset($row['lname']) ? $row['lname'] : ''; ?>" id="lname" name="lname" data-error="Last Name is required">
          <label>Last Name</label>
          <div id="lname_err" class="mid"><span></span></div>
          <p class="error"><span id="error_lname"></span></p>
        </div>
        <div class="form-group ">
          <input class="form-control input-lg" type="text" value="<?php echo isset($row['email']) ? $row['email'] : ''; ?>" id="email" readonly="" name="email">
          <label>Email</label>
          <div class="mid" id="email_err"><span></span></div>
          <p class="error"><span id="error_email"></span></p>
        </div>
        <div class="form-group ">
          <input type="text" id="phone1" name="phone" maxlength="10"  class="w-50 inputPhone no3 valid_phone1 form-control input-lg" value="<?= $phone1.$phone2.$phone3 ?>" />
          <label>Phone</label>
          <div class="mid" id="phone1_err"><span></span></div>
          <p class="error" id="error_phone1"></p>
        </div>
        <div class="form-group ">
          <input type="password" id="password" name="password" class="form-control input-lg"   data-error="Password is required" maxlength="20" onblur="check_password(this, 'password_err', 'err_password', event, 'input_validation');" onkeyup="check_password_Keyup(this, 'password_err', 'err_password', event, 'input_validation');">
          <label>Password</label>
          <div class="mid" id="password_err"><span></span></div>
          <p class="error"><span id="error_password"><?php echo (isset($errors['password'])) ? $errors['password'] : ''; ?></span></p>
          <div id="pswd_info" class="pswd_popup" style="display: none">
            <div class="pswd_popup_inner">
              <h4>Password Requirements</h4>
              <ul style="list-style:none; padding-left:10px;">
                <li id="pwdLength" class="invalid"><em></em>Minimum 8 characters</li>
                <li id="pwdUpperCase" class="invalid"><em></em>At least 1 uppercase letter</li>
                <li id="pwdLowerCase" class="invalid"><em></em>At least 1 lowecase letter</li>
                <li id="pwdNumber" class="invalid"><em></em>At least 1 number</li>
                <!-- <li id="special_char" class="valid"><em></em>Special Character Allowed: !@#^()-|[]{}<></li> -->
              </ul>
              <div class="btarrow"></div>
            </div>
          </div>
        </div>
        <div class="form-group">
          <input type="password" id="password_chk" name="password_chk" class="form-control input-lg" data-error="Confirm Password is required" maxlength="20">
          <label>Confirm Password</label>
          <div class="mid" id="password_chk_err"><span></span></div>
          <p class="error"><span id="error_password_chk"><?php echo isset($errors['password_chk']) ? $errors['password_chk'] : ''; ?></span></p>
        </div>
        <div class="form-group height_auto clearfix">
          <div class="pull-left">
            <div class="checkbox checkbox-red ">
              <input id="checkbox_signup" type="checkbox" name="agree">
              <label for="checkbox-signup"> I agree to <a class="popup-with-form red-link" href="admin_terms.php" id="terms" >Terms and Conditions</a> </label>
              <div class="mid" id="agree_err"><span></span></div>
              <p class="error"><span id="error_agree"><?php echo isset($errors['agree']) ? $errors['agree'] : ''; ?></span></p>
            </div>
          </div>
          <div class="pull-right">
            <input type="hidden" name="admin_id" value="<?php echo $row['id'] ?>">
            <input type="hidden" name="key" value="<?php echo $key ?>">
            <button name="r_signup" id="r_signup" class="btn btn-action  signup " type="button">Sign Up</button>
          </div>
        </div>
      </div>
    </form>
     <div class="login-footer"><a href="#"><?=$POWERED_BY_TEXT;?></a></div>
  </div>
</div>
<div class="panel-body login-alert-modal" id="bap1" style="display:none">
  <div class="media br-n pn mn">
    <div class="media-left"> <img src="images/<?= $DEFAULT_LOGO_IMAGE ?>" align="left"> </div>
    <div class="media-body theme-form">
      <h3 class="blue-link m-t-n fw600 fs24 m-b-10" >Success!</h3>
      <p class="m-b-20">Your admin account has been created successfully. Please click the button below to login to your account.</p>
      <a href="javascript:void(0);" class="btn btn-info confirm">Login</a>
    </div>
  </div>
</div>
<div class="panel-body login-alert-modal" id="error" style="display:none">
  <div class="media-left"> <img src="images/<?= $DEFAULT_LOGO_IMAGE ?>" align="left"> </div>
  <div class="media-body theme-form">
    <h3 class="text-red m-t-n fw600 fs24 m-b-10" >Uh Oh!</h3>
    <p class="m-b-20">Something went wrong. Refresh your browser and try again. If you are still having issues, please contact your admin today.</p>
    <a href="javascript:void(0);" class="btn btn-action m-r-10 refresh_error">Refresh Page</a>
    <a href="javascript:void(0);" class="btn red-link close_error" data-dismiss="error" aria-label="Close">Close</a>
  </div>
</div>
<script>
  $(document).ready(function() {
    $(".close_error").click(function() {
      $.colorbox.close();
      $("#error").hide();
    });
    $(".refresh_error").click(function() {
      location.reload();
    });
  });

  var HOST = "<?php echo $ADMIN_HOST; ?>";
  $(".popup-with-form").colorbox({
    iframe: true,
    width: '800px',
    height: '400px'
  });

  $(document).on('click', '.confirm', function() {
    window.location = HOST + '/index.php';
  });
</script>