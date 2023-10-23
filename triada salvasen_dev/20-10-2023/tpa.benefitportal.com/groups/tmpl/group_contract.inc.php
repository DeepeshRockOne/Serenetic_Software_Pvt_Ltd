<div class="group_contract_page">
   <div class="section_wrap">
      <div class="container">
         <div class="text-center">
            <p class="fs32 fw300 mb20"><strong>Hello</strong> <?= $group_res['fname'] .' '. $group_res['lname'] ?>,</p>
            <p class="fs18 m-b-40 p-b-30">Welcome to the <?= $DEFAULT_SITE_NAME ?> Group Application System. <i>You were referred to this page by <strong class="fw600 text-info"><?= $group_res['s_fname'] .' '. $group_res['s_lname'] ?>.</strong></i></p>
         </div>
         <div id="group_contract_div">
            <form method="post" autocomplete="off" id="group_contract_form">
               <input type="hidden" name="group_id" id="group_id" value="<?= $group_res['id'] ?>">
               <div class="row">
                  <div class="col-md-10 col-md-offset-1">
                     <div class="group_contract_box">
                        <h4 class="m-t-0 m-b-20">Account Contact Information</h4>
                        <div class="theme-form">
                           <div class="row">
                              <div class="col-sm-4">
                                 <div class="form-group">
                                    <input type="text" name="group_name" id="group_name" value="<?= $group_res['business_name'] ?>" class="form-control">
                                    <label>Group Name<em>*</em></label>
                                    <p class="error"><span id="err_group_name"></span></p>
                                 </div>
                              </div>
                              <div class="col-sm-4">
                                 <div class="form-group">
                                    <input type="text" name="fname" id="fname" class="form-control" value="<?= !empty($group_res['fname']) ? $group_res['fname'] : '' ?>">
                                    <label>First Name</label>
                                    <p class="error"><span id="err_fname"></span></p>
                                 </div>
                              </div>
                              <div class="col-sm-4">
                                 <div class="form-group">
                                    <input type="text" name="lname" id="lname" value="<?= !empty($group_res['lname']) ? $group_res['lname'] : '' ?>" class="form-control">
                                    <label>Last Name</label>
                                    <p class="error"><span id="err_lname"></span></p>
                                 </div>
                              </div>
                              <div class="col-sm-4">
                                 <div class="form-group">
                                    <input type="text" name="email" id="email" value="<?= !empty($group_res['email']) ? $group_res['email'] : '' ?>" class="form-control no_space">
                                    <label>Email<em>*</em></label>
                                    <p class="error"><span id="err_email"></span></p>
                                 </div>
                              </div>
                              <div class="col-sm-4">
                                 <div class="form-group">
                                    <input type="text" name="cell_phone" id="cell_phone" value="<?= !empty($group_res['cell_phone']) ? $group_res['cell_phone'] : '' ?>" class="form-control">
                                    <label>Phone<em>*</em></label>
                                    <p class="error"><span id="err_cell_phone"></span></p>
                                 </div>
                              </div>
                           </div>
                           <h4 class="m-t-0 m-b-20">Create an Account Password</h4>
                           <div class="row">
                              <div class="col-sm-4">
                                 <div class="form-group">
                                    
                                    <input type="password" id="password" name="password" value="" class="form-control"  maxlength="20" onblur="check_password(this, 'password_err', 'error_password', event, 'input_validation');" onkeyup="check_password_Keyup(this, 'password_err', 'error_password', event, 'input_validation');">
                                    <label>Set Password<em>*</em></label>
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
                              </div>
                              <div class="col-sm-4">
                                 <div class="form-group">
                                    <input type="password" id="c_password" name="c_password" class="form-control"  maxlength="20">
                                    <label>Confirm Password</label>
                                    <div id="c_password_err" class="mid"><span></span></div>
                                    <p class="error"><span id="err_c_password"></span></p>
                                 </div>
                              </div>
                           </div>
                           <div class="text-center">
                              <button type="button" class="btn btn-action" name="group_contract" id="group_contract">Enroll Now</button>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </form>
         </div>
         <div class="row" id="group_contract_success_div" style="display: none">
            <div class="col-md-6 col-md-offset-3">
               <div class="group_contract_box">
                  <h4 class="m-t-0 p-b-10 br-b text-success"><span class="material-icons v-align-middle">check_circle</span> Success!</h4>
                  <p class="text-center m-t-30 m-b-30">Your account has been created and your application form is ready to be completed. Click the button below to access your group portal to finish the application process.
                  </p>
                  <div class="text-center">
                     <a href="<?= $GROUP_HOST ?>" class="btn btn-success">Access Group Portal</a>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
   <div class="verification_banner group_contract" style="background-image: url(<?= $GROUP_HOST ?>/images/group_contract_bg.jpg?_v=1.00);">
   </div>
   <div class="smarte_footer mn">
      <div class="container m-b-15" >
         <div class="row footer_help">
            <div class="col-xs-7">
               <h4 class="text-action m-t-0">NEED HELP?</h4>
               <p class="mn need_help"><span><?= $group_res['s_fname'] .' '. $group_res['s_lname'] ?></span> <span>  <?= format_telephone($group_res['s_cell_phone']) ?> </span> <span> <?= $group_res['s_email'] ?> </span></p>
            </div>
            <div class="col-xs-5 mn text-right">
               <div class="powered_by_logo">
                  <img src="<?=$POWERED_BY_LOGO?>" height="43px" />
               </div>
            </div>
         </div>
      </div>
      <div class="bottom_footer ">
         <div class="container">
            <ul>
               <li><?= $DEFAULT_SITE_NAME ?>  &copy; <?php echo date('Y')?> </li>
            </ul>
         </div>
      </div>
   </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        checkEmail();
        $("#cell_phone").inputmask({"mask": "(999) 999-9999",'showMaskOnHover': false});
    });

    $(document).off("click","#group_contract");
    $(document).on("click","#group_contract",function(e){
        e.preventDefault();
        $('.error span').html('');
        $('#ajax_loader').show();
        $.ajax({
          url:"<?= $GROUP_HOST ?>/ajax_group_contract.php",
          data: $("#group_contract_form").serialize(),
          method: 'POST',
          dataType: 'json',
          success: function(res) {
              $('#ajax_loader').hide();
              
              if (res.status == 'account_approved') {
                 $("#group_contract_div").hide();
                 $("#group_contract_success_div").show();
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
              }else if(res.status == 'no_group_found'){
                setNotifyError("No Group Found");
              }
              return false;
          }
        });
    });
</script>