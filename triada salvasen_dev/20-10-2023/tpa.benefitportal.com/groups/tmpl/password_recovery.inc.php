<?php include('notify.inc.php');?>
<div class="login_wrap">
   <div class="login-left-panel">
      <div class="lf_inner">
         <div class="lf_cont"> <img src="<?php echo $HOST; ?>/images/logo_white.svg<?=$cache;?>" alt="">
         </div>
      </div>
   </div>
   <div class="login-right_panel">
      <form method="post"  autocomplete="off">
         <div class="login-form mw350 theme-form">
            <h3 class="mb15 fw600">Password Recovery</h3>
            <p class="fs18 m-b-25">Please Enter Your New Password.</p>
            <div class="form-group">
               <input name="password" id="password" maxlength="20" autocomplete="off" class="form-control input-lg" type="password" value="<?php echo (isset($password)?$password:''); ?>"onblur="check_password(this, 'password_err', 'password_error', event);" onkeyup="check_password_Keyup(this, 'password_err', 'password_error', event);" required="">
               <label>Password</label>
               <p class="error" id="password_error"><?php if (isset($errors['password'])) { ?><?php echo $errors['password']; ?><?php } ?></p>
               <div id="pswd_info" class="pswd_popup" style="display: none;">
                  <div class="pswd_popup_inner">
                     <h4>Password Requirements</h4>
                     <ul style="list-style:none; padding-left:10px;">
                        <li id="pwdLength" class="invalid"><em></em>Minimum 8 characters</li>
                         <li id="pwdUpperCase" class="invalid"><em></em>At least 1 uppercase letter</li>
                         <li id="pwdLowerCase" class="invalid"><em></em>At least 1 lowecase letter</li>
                         <li id="pwdNumber" class="invalid"><em></em>At least 1 number</li>
                     </ul>
                     <div class="btarrow"></div>
                  </div>
               </div>
            </div>
            <div class="form-group">
               <input name="conf_password" id="conf_password" maxlength="20" autocomplete="off" class="form-control input-lg" type="password" value="<?php echo (isset($conf_password)?$conf_password:''); ?>">
               <label>Confirm Password</label>
               <p class="error" id="conf_password_error"><?php if (isset($errors['conf_password'])) { ?><?php echo $errors['conf_password']; ?><?php } ?></p>
            </div>
            <div class="form-group height_auto clearfix text-right">
               <button class="btn btn-action" id="submit" name="submit">Update</button>
               <a href="<?=$GROUP_HOST?>" class="btn red-link">Cancel</button>
            </div>
         </div>
      </form>
      <div class="login-footer"><a href="#"><?=$POWERED_BY_TEXT;?></a></div>
   </div>
</div>