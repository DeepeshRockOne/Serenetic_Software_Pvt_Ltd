<?php include_once('notify.inc.php'); ?>
  <div class="login_wrap">
    <div class="login-left-panel">
      <div class="lf_inner">
        <div class="lf_cont"> <img src="<?php echo $HOST; ?>/images/logo_white.svg<?=$cache;?>" alt="">
        </div>
      </div>
    </div>
    <div class="login-right_panel">
    <form role="form" id="loginform" action="<?=$ADMIN_HOST?>/index.php" method="POST" autocomplete="off" >
    <input type="hidden" name="timezone" id="timezone">
    <input type="hidden" name="submit" value="login" id="submit">
    <input type="hidden" name="verify_otp" value="no" id="verify_otp">
    <input type="hidden" name="previous_page" value="<?php echo  isset($_GET['previous_page']) ? $_GET['previous_page']: ''; ?>" id="">

      <div id="login_div"  class="login-form mw350 theme-form">
        <h3 class="mb15 fw600"><?= $DEFAULT_SITE_NAME ?> Admin Login</h3>
        <p class="fs18 m-b-25">Sign in below using your credentials.</p>
          
          <div class="form-group ">
            <input class="form-control <?php echo isset($errors['email']) ? 'parsley-error' : '' ?> input-lg" name="email" id="email" type="text"  value="<?php echo $email; ?>"  required="" />
            <label>Email/Admin ID</label>
            <p class="error" id="error_email"></p>
          </div>
          <div class="form-group ">
            <input class="form-control <?php echo isset($errors['password']) ? 'parsley-error' : '' ?> input-lg" type="password" name="password" id="password" value="<?= !empty($password) ? $password : '' ?>"  required="" />
            <label>Password</label>
            <p class="error" id="error_password"></p>
            <p class="error" id="error_general"></p>
            
        </div>
            <div class="form-group height_auto clearfix">
          <div class="m-t-7 pull-left">
          <i class="fa fa-lock fa-lg blue-link"></i> &nbsp;
          <a href="forgot_password.php"  id="to-recover" class="forgot blue-link cboxElement">Forgot Password?</a>
          </div>
          <?php /*?><div class="checkbox mn checkbox-red pull-left p-t-0">
            
            <input id="remember_chk" type="checkbox" name="remember">  <label for="remember_chk"> Remember me  </label>
          </div><?php */?>
          <button type="submit" class="btn btn-action  pull-right btn_login">Sign In</button>
        </div>
        

      </div>
      <div id="secure_access_div" class="login-form mw350 theme-form" style="display: none;">
        <div class="fs18 m-b-25 send_via_email"><strong>Check Email : </strong> We have sent a secure six-digit code to your email address on file. Please enter the code below to access your account.</div>
        <div class="fs18 m-b-25 send_via_sms"><strong>Check Message Box : </strong> We have sent a secure six-digit code to your  registered Phone Number. Please enter the code below to access your account.</div>
        <div class="form-group">
            <input type="text" name="otp" id="otp" class="form-control input-lg" value="" maxlength="6" />
            <label>Six-Digit Code</label>
            <p class="error" id="error_otp"></p>
        </div>
        <div class="form-group height_auto">
          <div class="m-t-7 pull-left">
            Didn’t receive the code? <a href="javascript:void(0);" class="blue-link" id="resend_otp">Resend</a>
          </div>
          <button type="submit" class="btn btn-action  pull-right btn_login">Submit</button>
          <div class="clearfix"></div>
        </div>
      </div>
    </form>
    <div class="login-footer"><a href="#"><?=$POWERED_BY_TEXT;?></a></div>
    <form class="theme-form" id="recoverform" action="index.html">
        <div class="form-group height_auto">
            <h3>Recover Password</h3>
            <p class="text-muted">Enter your Email and instructions will be sent to you! </p>
        </div>
        <div class="form-group height_auto">
            <input class="form-control" type="text" required="" placeholder="Email">
        </div>
        <div class="form-group text-center">
            <button class="btn btn-action" type="submit">Reset</button>
        </div>
      </form>
    </div>
  </div>


<script type="text/javascript">
$(document).ready(function() {
  var remember = "<?=$remember?>";
  if (remember == 'on') {
    $("#remember_chk").prop("checked", true);
  }
  $('.forgot').colorbox({
    iframe:true,width:'420px',height:'260px', right:'25%' 
  });

}); 

$(document).off('input blur paste',"#otp");
$(document).on('input blur paste',"#otp",function(){
    $(this).val($(this).val().replace(/\D/g, ''))
});

$(document).off('click','#resend_otp');
$(document).on('click','#resend_otp',function(){
    $("#submit").val("resend_otp");
    $('#ajax_loader').show();
    $(".error").hide();
    var params = $('#loginform').serialize();
    $.ajax({
        url: $('#loginform').attr('action'),
        data: params,
        method: 'POST',
        dataType: 'json',
        success: function(res) {
          $('#ajax_loader').hide();
          $("#submit").val("login");
          if(res.status == "success") {
              setNotifySuccess('Access code resent successfully.');
          } else {
              window.location="<?=$AGENT_HOST?>";
          }
        }
    });
});

$('#loginform').bind('submit',function(e) {
  e.preventDefault();
  $('#ajax_loader').show();
  $(".error").hide();
  var params = $('#loginform').serialize();
  $.ajax({
      url: $('#loginform').attr('action'),
      data: params,
      method: 'POST',
      dataType: 'json',
      success: function(res) {
        $('#ajax_loader').hide();

        if (res.status == "previous_page" && res.redirect_url != undefined && res.redirect_url!='') {
            window.location=res.redirect_url;
        }else if (res.status == "login_success") {
            window.location="<?=$ADMIN_HOST?>/dashboard.php";
        
        } else if (res.status == "support_dashboard") {
            window.location="<?=$ADMIN_HOST?>/support_dashboard.php";

        } else if (res.status == "unsubscribe_email") {
            window.location="<?=$ADMIN_HOST?>/unsubscribe_email.php?email="+res.unsubscribe_email;

        } else if (res.status == "otp_send") {
            if(res.otp_via == 'sms'){
              $(".send_via_email").hide();
              $(".send_via_sms").show();
            }else{
              $(".send_via_sms").hide();
              $(".send_via_email").show();
            }
            $("#secure_access_div").slideDown();
            $("#login_div").hide();            
            $("#verify_otp").val('yes');

        } else if (res.status == "error") {
            var tmp_flag = true;
            $.each(res.errors, function (key, value) {
                $('#error_' + key).html(value).show();
                                    
                if(tmp_flag == true && $("#error_" + key).length > 0) {
                    tmp_flag = false;
                    $('html, body').animate({
                        scrollTop: parseInt($("#error_" + key).offset().top) - 100
                    }, 1000);
                }
            });            
        } else {
            
        }
      }
  });
});

</script> 

<script type = "text/javascript">
$(function() {  
  var timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
  if(timezone === undefined){
    timezone = moment.tz.guess()
  }
  $("#timezone").val(timezone);
});
</script>