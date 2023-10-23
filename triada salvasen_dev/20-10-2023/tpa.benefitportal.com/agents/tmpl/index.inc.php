<?php include_once('notify.inc.php'); ?>
<div class="login_wrap">
    <div class="login-right_panel">
   <form action="<?=$AGENT_HOST?>/index.php" method="post" autocomplete="off" id="frm_login">
   <input type="hidden" name="timezone" id="timezone">
   <input type="hidden" name="submit" value="login" id="submit">
   <input type="hidden" name="verify_otp" value="no" id="verify_otp">
   <input type="hidden" name="previous_page" value="<?php echo  isset($_GET['previous_page']) ? $_GET['previous_page']: ''; ?>" id="">
      <div id="login_div" class="login-form mw350 theme-form">
        <p class="fs18 m-b-25">Sign in below using your credentials.</p>
          <div class="form-group">
            <input type="text" name="rep_id" id="rep_id" class="form-control input-lg" value="<?=isset($rep_id) ? $rep_id : '';?>" required="" />
            <label>Agent ID</label>
            <p class="error" id="error_rep_id"></p>
          </div>
          <div class="form-group">
            <input type="password" name="r_password" id="r_password" class="form-control input-lg" required="" />
            <label>Password</label>
            <p class="error" id="error_r_password"></p>
            <p class="error" id="error_general"></p>
        </div>
        <div class="form-group height_auto">
          <div class="m-t-7 pull-left">
          <i class="fa fa-lock fa-lg blue-link"></i> &nbsp;
          <a href="forgot_password.php" class="forgot blue-link">Forgot Password?</a>
          </div>
          <button type="button" class="btn btn-action  pull-right btn_login">Sign In</button>
          <div class="clearfix"></div>
        </div>
      </div>
      <div id="secure_access_div" class="login-form mw350 theme-form" style="display: none;">
        <div class="fs18 m-b-25 send_via_email"><strong>Check Email : </strong> We have sent a secure six-digit code to your email address on file. Please enter the code below to access your account.</div>
        <div class="fs18 m-b-25 send_via_sms"><strong>Check Message Box : </strong> We have sent a secure six-digit code to your  registered Phone Number. Please enter the code below to access your account.</div>
        <div class="form-group">
            <input type="text" name="otp" id="otp" class="form-control input-lg" value=""  required="" maxlength="6" />
            <label>Six-Digit Code</label>
            <p class="error" id="error_otp"></p>
        </div>
        <div class="form-group height_auto">
          <div class="m-t-7 pull-left">
            Didn’t receive the code? <a href="javascript:void(0);" class="blue-link" id="resend_otp">Resend</a>
          </div>
          <button type="button" class="btn btn-action  pull-right btn_login">Submit</button>
          <div class="clearfix"></div>
        </div>
      </div>
    </form>
    <div class="login-footer">
      <p class="fs12 text-black"><?=$POWERED_BY_TEXT;?></p>
   </div>
    </div>
  </div>

<div style="display:none">
  <div id="agent_support">
          <div class="panel-body login-alert-modal">
            <div class="media br-n pn mn">
              <div class="media-left"> <img src="<?php echo $ADMIN_HOST; ?>/images/<?= $DEFAULT_LOGO_IMAGE ?>" align="left"> </div>
              <div class="media-body">
                <h3 class="m-t-15 m-b-15 fw600" >Access Request</h3>
                <p class="mn">This account cannot be accessed due to an unresolved issue.  Please contact a services leader to assist in resolving this matter.</p>
              </div>
            </div>
            <div class="text-center m-t-20">
              <?=$service?> &nbsp;&nbsp;|&nbsp;&nbsp;
              <?=$cell_phone ?> &nbsp;&nbsp;|&nbsp;&nbsp;
              <?=$service_email?>
            </div>
            <div class="m-t-10 text-center">
              <button class="btn red-link" onclick="javascript:parent.$.colorbox.close()">Close</button>
            </div>
          </div>
    </div>
</div>
<script  type="text/javascript">
  $(document).ready(function() {
    $('.forgot').colorbox({iframe: true, width:'420px',height:'250px'});

    $(document).on('input blur paste',"#otp",function(){
        $(this).val($(this).val().replace(/\D/g, ''))
    });

    $(document).off('click','#resend_otp');
    $(document).on('click','#resend_otp',function(){
        $("#submit").val("resend_otp");
        $('#ajax_loader').show();
        $(".error").hide();
        var params = $('#frm_login').serialize();
        $.ajax({
            url: $('#frm_login').attr('action'),
            data: params,
            method: 'POST',
            dataType: 'json',
            success: function(res) {
              $('#ajax_loader').hide();
              $("#submit").val("login");
              if(res.status == "success") {
                  setNotifySuccess('Access code sent successfully.');
              } else {
                  window.location="<?=$AGENT_HOST?>";
              }
            }
        });
    });

    $(document).off('click','.btn_login');
    $(document).on('click','.btn_login',function(){
      $('#ajax_loader').show();
      $(".error").hide();
      var params = $('#frm_login').serialize();
      $.ajax({
          url: $('#frm_login').attr('action'),
          data: params,
          method: 'POST',
          dataType: 'json',
          success: function(res) {
            $('#ajax_loader').hide();

            if(typeof(res.customer_status) !== "undefined" && res.customer_status == "Terminated") {
                $.colorbox({href:'#agent_support', inline:true,  width: '500px', height: '260px', closeButton:false});
                return false;
            }

            if (res.status == "previous_page" && res.redirect_url != undefined && res.redirect_url!='') {
              window.location=res.redirect_url;
            }else if (res.status == "login_success") {
                window.location="<?=$AGENT_HOST?>/dashboard.php";
            
            } else if (res.status == "unsubscribe_email") {
                window.location="<?=$AGENT_HOST?>/unsubscribe_email.php?email="+res.unsubscribe_email;

            } else if (res.status == "otp_send") {
                if(res.otp_via == 'sms'){
                  $(".send_via_email").hide();
                  $(".send_via_sms").show();
                }else{
                  $(".send_via_sms").hide();
                  $(".send_via_email").show();
                }
                $("#login_div").hide();
                $("#secure_access_div").slideDown();
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
  });

<?php if(isset($_GET['link']) && $_GET['link']=='expired' && $_GET['key']!=''){ ?>
    $.colorbox({href:'link_expired.php?key=<?= $_GET['key']?>',iframe: true,width: '550px', height: '350px'});
<?php  } ?>

$(function() {  
  var timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
  if(timezone === undefined){
    timezone = moment.tz.guess()
  }
  $("#timezone").val(timezone);
});
</script>