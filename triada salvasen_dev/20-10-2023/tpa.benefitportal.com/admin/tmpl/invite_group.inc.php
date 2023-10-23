<form action="" role="form" method="post" name="group_form" id="group_form" enctype="multipart/form-data">
<div class="panel panel-default panel-block ">
  <div class="panel-body advance_info_div">
    <div class="phone-control-wrap ">
      <div class="phone-addon w-90 v-align-top">
        <img src="images/<?= $DEFAULT_LOGO_IMAGE ?>" height="75px">
      </div>
      <div class="phone-addon text-left v-align-top">
        <p class="fs12 mn">Fill in the form below with the recipients details. A generated link will be created to send to the recipient. </p>
        <p class="fs12 mn text-action">The link that will be generated and sent off to this new group is valid for 7 Days.</p>
       
      </div>
    </div>
  </div>
  <div class="panel-body">
    <div class="theme-form">
      <h4 class="m-t-0 m-b-20">+ Group</h4>
      <div class="row">
        <div class="col-sm-6">
          <div class="form-group">
            <select class="form-control" name="agent_id" id="agent_id" data-live-search="true">
              <option data-hidden="true"></option>
              <?php if(!empty($rowAgent)) { ?>
                <?php foreach ($rowAgent as $key => $resAgent) { ?>
                  <option value="<?= $resAgent['id'] ?>" <?= $resAgent['id'] == '1' ? 'selected' : '' ?>><?= $resAgent['rep_id'] ." - ".$resAgent['fname'] .' '.$resAgent['lname'] ?></option>
                <?php } ?>
              <?php } ?>
              <p class="error"><span id="err_agent_id"></span></p>
            </select>
            <label>Enrolling Agent</label>
          </div>
          <div class="form-group">
            <input type="text" name="group_name" id="group_nane" class="form-control">
            <label>Group Name<em>*</em></label>
            <p class="error"><span id="err_group_name"></span></p>
          </div>
          <div class="form-group">
            <input type="text" name="contact_person_fname" id="contact_person_fname" class="form-control">
            <label>Contact First Name<em>*</em></label>
            <p class="error"><span id="err_contact_person_fname"></span></p> 
          </div>
          <div class="form-group">
            <input type="text" name="contact_person_lname" id="contact_person_lname" class="form-control">
            <label>Contact Last Name<em>*</em></label>
            <p class="error"><span id="err_contact_person_lname"></span></p>        
          </div>
          <div class="form-group">
            <input type="text" name="contact_person_phone" id="contact_person_phone" class="form-control">
            <label>Contact Phone<em>*</em></label>
            <p class="error"><span id="err_contact_person_phone"></span></p>        
          </div>
          <div class="form-group">
            <input type="text" name="contact_person_email" id="contact_person_email" class="form-control no_space">
            <label>Contact Email<em>*</em></label>
            <p class="error"><span id="err_contact_person_email"></span></p>    
          </div>
          <div class="form-group">
              <select class="se_multiple_select" id="products" name="products[]" multiple="multiple">
              </select>
              <label>Assign Product(s)<em>*</em></label>
              <p class="error"><span id="err_products"></span></p>
          </div>
           <p>Would you like  <!-- <a id="smartE-link" href="javascript:void(0);" class="red-link " tabindex="0" data-placement="top"  data-popover-content="#smartE-popover"><?= $DEFAULT_SITE_NAME ?></a> --> system to invite this group or do this personally?</p>
            <div id="smartE-popover" class="hide">
               <p><strong class="fs16 text-blue"><i class="fa fa-info-circle"></i> &nbsp; Who is <?= $DEFAULT_SITE_NAME ?>?</strong></p>
               <p>Built into our platform, <?= $DEFAULT_SITE_NAME ?> is part of our artificial intelligence that delivers information based on your unique business processes and customer data. Using those insights to automate responses and actions, making you more productive, and your members even happier. Talk about Smart!<br> <img src="images/<?= $DEFAULT_LOGO_IMAGE ?>" width="50px" align="right" /></p>
            </div>
          <div class="m-b-20">
            <div class="m-b-10">
              <label class="mn"><input type="radio" class="send_contract_radio" name="send_contract_radio" value="Y"> System Invite</label>
            </div>
            <div class="m-b-0">
              <label class="mn"><input type="radio" class="send_contract_radio" name="send_contract_radio" value="N"> Personal Invite</label>
            </div>
            <p class="error"><span id="err_send_contract_radio"></span></p>
          </div>
          <div id="smarte_div" class="send_contract_div" style="display: none;">
                  <div class="form-group">
                     <select class="form-control" id="select_type" name="select_type" title=" &nbsp;" >
                        <option value="" disabled selected hidden> </option>
                        <option value="email">Email</option>
                        <option value="text">Text Message (SMS)</option>
                        <option value="email_text">Email & Text Message (SMS)</option>
                     </select>
                     <label>Select Delivery Method<em>*</em></label>
                       <div id="select_type_err" class="mid"><span></span></div>
                  <p class="error"><span id="err_select_type"></span></p>
                  </div>
                  
                  <div class="emailtp tp" style="display: <?php echo (!empty($select_type) && in_array('email', $select_type)) ? 'block' : 'none'; ?>">
                     <hr class="m-t-0" />
                     <div class="form-group">
                        <input type="text" name="email_from" id="email_from" class="form-control no_space" value="<?= $email_from ?>">
                        <label>From</label>
                        <p class="error"><span id="err_email_from"></span></p>
                     </div>
                     <div class="form-group">
                        <input type="text" name="email_subject" id="email_subject" value="<?= $email_subject ?>" class="form-control ">
                        <label>Subject</label>
                     </div>
                     <div class="m-b-20">
                     <textarea id="email_content" name="email_content" class="cust_summernote"><?=$email_content?></textarea>
                     <div id="email_content_err" class="mid"><span></span></div>
                     <p class="error"><span id="err_email_content"></span></p>
                   </div>
                  </div>
                  <div class="clearfix"></div>
                  <div class="smstp tp m-b-20" style="display:<?php echo (!empty($select_type) && in_array('text', $select_type)) ? 'block' : 'none'; ?>" >
                     <textarea id="sms_content" name="sms_content" rows="7" class="form-control <?php echo isset($errors['sms_content']) ? 'parsley-error' : '' ?>" maxlength="160"><?=$sms_content?></textarea>
                     <div>Character Remaining : <span id="sms_content_count"><?php echo $sms_content != "" ? 160 - strlen($sms_content) : 160; ?></span></div>
                     <div id="sms_content_err" class="mid"><span></span></div>
                     <p class="error"><span id="err_sms_content"></span></p>
                  </div>
                
          </div>
           <hr class="m-t-0">
           <div class="text-left">
             <button class="btn btn-action" type="submit" name="save" id="save">Generate Invite</button>
             <button class="btn red-link " type="button" name="cancel" onClick="window.location = 'groups_listing.php'">Cancel</button>
           </div>
        </div>
      </div>
    </div>
  </div>
</div>
</form>
<div class="modal fade" id="copy_alert" >
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <p class="mn fs20"><i class="fa fa-link" aria-hidden="true"></i>&nbsp; <span class="agent_name fw600"></span> - Group Application Link</p>
      </div>
      <div class="modal-body">
       <label>Copy the link below to copy and share</label>
        <div class="row">
          <div class="col-sm-10">
            <div class="form-group">
              <input type="text" class="form-control" id="copytext" readonly="readonly"  data-clipboard-text="1111" tabindex="" placeholder="display link here" value=""/>
              <textarea id="holdtext" style="display:none;"></textarea>
            </div>
          </div>
          <div class="col-sm-2">
            <div class="form-group">
              <button class="btn btn-info" id="copyingg" data-clipboard-target="#copytext" >COPY LINK </button>
            </div>
          </div>
        </div>
        <div class="text-center">
          <button class="no-thanks btn red-link">Continue</button>
        </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  var clipboard = new Clipboard('#copyingg'); 
  $(document).ready(function(){
    checkEmail();
    initCKEditor("email_content");
    $("#contact_person_phone").inputmask({"mask": "(999) 999-9999",'showMaskOnHover': false});
    
    
      
    set_groups_products($("#agent_id").val());
    
    $(document).off("change","#agent_id");
    $(document).on("change","#agent_id",function(e){
      set_groups_products($(this).val());
    });
    $('#smartE-link').popover({
      container: 'body',              
      html: true,
      trigger : 'hover',
      template: '<div class="popover smarte-popover"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>',
      content: function () {
          var clone = $($(this).data('popover-content')).clone(true).removeClass('hide');
          return clone;
          }
    }).click(function(e) {
      e.preventDefault();
    });
  });

  $(document).off("click", ".no-thanks");
  $(document).on("click", ".no-thanks", function () {
      $('#copy_alert').modal('hide')
      location.href="groups_listing.php"
  });

  $(document).off("click", "#copyingg");
  $(document).on("click", "#copyingg", function () {

      clipboard.on('success', function (e) {
          setNotifySuccess("Link Copied!");
          $('#copy_alert').modal('hide')
          location.href="groups_listing.php"
      });
  });

  $(document).off("click", "#save");
  $(document).on("click", "#save", function (e) {
      e.preventDefault();
      $('.error span').html('');
      $('#ajax_loader').show();
      $("#email_content").val(CKEDITOR.instances.email_content.getData());
      $.ajax({
          url:"<?= $ADMIN_HOST ?>/ajax_invite_group.php",
          data: $("#group_form").serialize(),
          method: 'POST',
          dataType: 'json',
          success: function(res) {
              $('#ajax_loader').hide();
              
              if (res.status == 'success') {
                  var val = res.link;
                  var group_name = res.group_name;
                  // var lname = res.lname;
                  if(res.invite_by=='personal_invite'){
                      $('#copytext').val(val);
                      $('.agent_name').html(group_name);
                      $('#copy_alert').modal('show')
                  }else{
                      window.location.href="groups_listing.php";
                  }
              } else if (res.status == 'fail') {
                  var is_error = true;
                  $('.error span').html('');
                  $('.form-group').removeClass('has-error');
                  $.each(res.errors, function (index, value) {
                     $('#err_' + index).closest('.form-group').addClass('has-error');
                      $('#err_' + index).html(value).show();
                      if(is_error){
                          var offset = $('#err_' + index).offset();
                          var offsetTop = offset.top;
                          var totalScroll = offsetTop - 50;
                          $('body,html').animate({scrollTop: totalScroll}, 1200);
                          is_error = false;
                      }
                  });
              }
              return false;
          }
      });
  });
    
  $(document).off("click", ".send_contract_radio");
  $(document).on('click', '.send_contract_radio', function(){
    var val = $(this).val();
    if(val == 'Y'){
        $("#smarte_div").show();
        $.uniform.update();
    } else {
        $("#smarte_div").hide();
        $.uniform.update();
    }
  });
  
  $(document).off("change", "#select_type");
  $(document).on("change", "#select_type", function () {
      var check_val = $(this).val();
      if(check_val == 'text'){
          $(".emailtp").hide();
          $(".smstp").show();
      }else if(check_val == 'email'){
          $(".smstp").hide();
          $(".emailtp").show();
      }else if(check_val == 'email_text'){
          $(".smstp").show();
          $(".emailtp").show();
      }
  });

  $(document).off("keyup", "#sms_content");
  $(document).on('keyup', '#sms_content', function (e) {
      var chars = $("#sms_content").val().length;
      if(160 - chars<=0){
          $("#sms_content_count").parent("span").addClass("text-danger");
      }else{
          $("#sms_content_count").parent("span").removeClass("text-danger");
      }
      $("#sms_content_count").text(160 - chars);
  });

  $(document).off("blur", "#email_from");
  $(document).on('blur', '#email_from', function (e) {
      $val = $(this).val();
      if($val==''){
        $(this).val('<?= $email_from ?>');
      }
  });

  $(document).off("blur", "#email_subject");
  $(document).on('blur', '#email_subject', function (e) {
      $val = $(this).val();
      if($val==''){
        $(this).val('<?= $email_subject ?>');
      }
  });


  function set_groups_products(agent_id) {
     $('#ajax_loader').show();
     
      $.ajax({
          url: "<?=$HOST ?>/get_products_invite_group.php",
          type: "POST",
          dataType: "json",
          data: {agent_id:agent_id},
          success: function (res) {
            $('#ajax_loader').hide();
            if(res.status == 'success'){
                $("#err_products").html();
                $("#products").html(res.products_drop_down_html);
                $("#products").multipleSelect('refresh');
            }else{
                $("#products").html("");
                $("#products").multipleSelect('refresh');
                
            }
          }
      });
  }
</script> 