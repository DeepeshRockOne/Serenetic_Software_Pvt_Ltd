<div class="panel panel-default">
  <div class="panel-body login-alert-modal">
    <div class="media br-n pn mn">
      <div class="media-left"> <img src="<?php echo $GROUP_HOST; ?>/images/<?= $DEFAULT_LOGO_IMAGE ?>" align="left"> </div>
      <div class="media-body theme-form">
        <h3 class="text-blue m-t-n fw600" >Forgot Password?</h3>
        <p>Enter the email address below to reset your password</p>
        <div class="clearfix"></div>
        <form method="post" autocomplete="off" >
          <div class="form-group height_auto">
            <input name="email" id="email" autocomplete="off" class="form-control no_space" type="text" value="<?=isset($email) ? $email : '';?>" required="" />
            <label>Email</label>
            <?php if (isset($errors['email'])) { ?>
              <p class="error"><?php echo $errors['email']; ?></p>
            <?php } ?>
          </div>
          <div class="text-center">
            <button class="btn btn-info" id="submit" type="submit" name="submit" value="forget">Submit</button>
            <button class="btn red-link " type="button" onclick='parent.$.colorbox.close(); window.parent.location.href="index.php";' >Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
  $(document).ready(function(){
    checkEmail();
  })
</script>