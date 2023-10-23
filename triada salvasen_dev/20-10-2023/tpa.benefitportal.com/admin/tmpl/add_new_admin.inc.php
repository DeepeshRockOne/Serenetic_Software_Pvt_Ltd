<style type="text/css" media="screen">
.sub_features .checker {display: inline-block;}
</style>
<form action="" method="post"  name="add_new_admin_frm" id="add_new_admin_frm">
  <input type="hidden" name="is_feature_access" id="is_feature_access" value="N">
  <div class="panel panel-default panel-block theme-form">
      <div class="panel-body advance_info_div">
      <div class="phone-control-wrap ">
        <div class="phone-addon w-90 v-align-top">
          <img src="images/<?= $DEFAULT_LOGO_IMAGE ?>" height="75px">
        </div>
        <div class="phone-addon text-left v-align-top">
          <p class="fs12 mn">Fill in the form below with the recipients details.  A generated link will be created to send to the recipient. </p>
          <p class="fs12 text-action mn">The link that will be generated and sent off to this new admin is valid for 7 Days.</p>
        </div>
      </div>
  </div>
  <div class="panel-body">
      <h4 class="m-t-0 m-b-20">+ Admin</h4>
      <div class="row">
        <div class="col-sm-6">
          <div class="form-group">
            <select id="access_level" name="access_level" class="form-control">
              <option value=""></option>
              <?php  if(!empty($acl_features)) { ?>
                <?php foreach ($acl_features as $key => $value) { ?>
                <option value="<?= $key ?>" data-setting="feature_access" <?php echo ($access_level == $key ) ? "selected" : ""; ?>><?= $key ?></option>
                <?php } ?>
              <?php } ?>
            </select>
            <label for="access_level" >Access Level<em>*</em></label>
            <p class="error" id="error_access_level"></p>
          </div>
          <!-- <div class="form-group">
            <select id="company_name" name="company_name" class="form-control">
              <option value=""> </option>
              <?php if(!empty($company_res)){ ?>
                <?php foreach ($company_res as $val) {?>
                  <option value="<?=$val['id']?>"><?php echo $val['company_name']; ?></option>
                <?php } ?>
              <?php } ?>
            </select>
            <label for="company_name" >Company<em>*</em></label>
            <p class="error" id="error_company_name"></p>
          </div> -->
          <div class="form-group">
            <input type="text" id="fname" name="fname" class="form-control" value="" />
            <label for="fname" >First Name<em>*</em></label>
            <p class="error" id="error_fname" ></p>
          </div>
          <div class="form-group">
            <input type="text" id="lname" name="lname" class="form-control" value=""/>
            <label for="lname" >Last Name<em>*</em></label>
            <p class="error" id="error_lname"></p>
          </div>
          <div class="form-group">
            <input type="text" id="email" name="email" class="form-control no_space" value=""/>
            <label for="email" >Email<em>*</em></label>
            <p class="error" id="error_email"></p>
          </div>
          <div class="form-group">
            <input type="text" id="cemail" name="cemail" class="form-control no_space" value=""/>
            <label for="cemail" >Confirm Email<em>*</em></label>
            <p class="error" id="error_cemail" ></p>
          </div>
          <div class="form-group" id="phone_txt">
            <input type="text" id="phone" name="phone" class="form-control" value="" />
            <label for="phone" >Mobile Phone<em></em></label>
          </div>
          <div class="hidden-xs clearfix"></div>
          <?php include('acl.inc.php');?>
          <div class="clearfix"></div>
          
          <div class="_invite" style="display:none">
            <p  class="m-t-30">Would you like system to invite this admin or do this personally?</p>
            <div id="smartE-popover" class="hide">
              <p><strong class="fs16"><i class="fa fa-info-circle"></i> &nbsp; Who is <?= $DEFAULT_SITE_NAME ?>?</strong></p>
              <p>Built into our platform, <?= $DEFAULT_SITE_NAME ?> is part of our artificial intelligence that delivers information based on your unique business processes and customer data. Using those insights to automate responses and actions, making you more productive, and your members even happier. Talk about Smart! <br /><img src="images/<?= $DEFAULT_LOGO_IMAGE ?>" width="50px" align="right"></p>
              <div class="clearfix"></div>
            </div>
            <p>
              <label class="mn">
                <input type="radio" value="smarte_invite" id="smarte_invite" name="invitation" class="invitation">
              System Invite</label>
            </p>
            <p>
              <label class="mn">
                <input type="radio" value="personal_invite" id="personal_invite" name="invitation" class="invitation">
              Personal Invite</label>
            </p>
            <p class="error" id="error_invitation"></p>
          </div>
          <div class="form-group height_auto m-t-20 del_method" style="display:none">
            <select id="type" name="type" class="form-control">
              <option value=""></option>
              <option value="Email">Email</option>
              <option value="SMS">Text Message (SMS)</option>
              <option value="Both">Email & Text Message (SMS)</option>
            </select>
            <label>Select Delivery Method<em>*</em></label>
            <p class="error" id="error_type"></p>
          </div>
          <div class="emailtp tp" id="txt_email_toggle_div" style="display:none;">
            <hr />
            <div class="form-group height_auto">
              <input type="text" name="email_from" class="form-control no_space" value="<?= $from_email ?>" />
              <label>From</label>
              <p class="error" id="error_email_from"></p>
            </div>
            <div class="form-group height_auto">
              <input type="text" name="email_sub" value="<?= $trigger['email_subject'] ?>" class="form-control" />
              <label>Subject</label>
              <p class="error" id="error_email_sub"></p>
            </div>
            <div class="clearfix"></div>
            <textarea rows="13" class="summernote " name="txt_email_txt1" id="txt_email_txt1">
              <?php echo $trigger['email_content']; ?>
            </textarea>
          </div>
          <div class="smstp tp m-t-15 m-b-15" id="txt_msg_toggle_div" style="display:none;">
            <textarea id="sms_content" name="txt_msg_txt" rows="6" class="form-control" maxlength="160"><?php echo $trigger['sms_content']; ?>    
            </textarea>
            <div>Character Remaining : <span id="message1">30</span></div>
          </div>
          <hr class="m-t-0"/>
        </div>
        <div class="clearfix "></div>
        <div class="col-sm-12">
        <div class="text-left">
          <button class="btn btn-action m-r-10" type="button" id="save_btn">Generate Invite</button>
          <button class="btn red-link" type="button" name="cancel" onClick="window.location='admins.php'">Cancel</button>
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
        <p class="mn fs20"><i class="fa fa-link" aria-hidden="true"></i>&nbsp; <span class="agent_name fw600"></span> -  Admin Application Link</p>
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
              <button class="btn btn-info" id="copyingg" data-clipboard-target="#copytext" >COPY LINK</button>
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

  $(document).ready(function() {
    checkEmail();
    initCKEditor("txt_email_txt1");
    $('#acl').hide();
    var chars = $("#sms_content").val().length;
    $("#message1").text(160 - chars);
    
    $('#phone').inputmask({"mask": "999-999-9999",'showMaskOnHover': false});

    $('#smartE-link').popover({
      container: 'body',
      placement: 'top',
      html: true,
      trigger: 'hover',
      template: '<div class="popover smarte-popover"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>',
      content: function() {
        var clone = $($(this).data('popover-content')).clone(true).removeClass('hide');
        return clone;
      }
    }).click(function(e) {
      e.preventDefault();
    });

  });

  $(document).off('keyup', '#sms_content');
  $(document).on('keyup', '#sms_content', function(e) {
    e.preventDefault();
    var chars = $(this).val().length;
    $("#message1").text(160 - chars);
    if (chars > 160 || chars <= 0) {
      $("#message1").addClass("minus");
      $(this).css("text-decoration", "line-through");
    } else {
      $("#message1").removeClass("minus");
      $(this).css("text-decoration", "");
      e.preventDefault();
    }
  });

  $(document).off('click', '.no-thanks');
  $(document).on('click', '.no-thanks', function(e) {
    e.preventDefault();
    $('#copy_alert').modal('hide')
    window.location.href = "admins.php"
  });

  $(document).on("click", "#copyingg", function(e) {
    var clipboard = new Clipboard('#copyingg');
    clipboard.on('success', function(e) {
      setNotifySuccess("Link Copied!");
      $('#copy_alert').modal('hide');
      setTimeout(function(){ 
        window.location.href = "admins.php"
      }, 1000);
    });
  });

  $(document).off("change", "#type");
  $(document).on("change", "#type", function(e) {
    e.stopPropagation();
    var tp = $(this).val().toLowerCase() + 'tp';
    if ($(this).val().toLowerCase() == "both") {
      $('.tp').show();
    } else if (tp == 'smstp') {
      $('.emailtp').hide();
      $('.smstp').show();
    } else if (tp == 'emailtp') {
      $('.smstp').hide();
      $('.emailtp').show();
    } else {
      $('.tp').hide();
    }
  });

  $(document).off("change", ".invitation");
  $(document).on("change", ".invitation", function(e) {
    e.stopPropagation();
    if ($(this).val() == 'smarte_invite') {
      $(".del_method").show(500);
    } else {
      $(".del_method").hide(500);
      $("#txt_msg_toggle_div").hide();
      $("#txt_email_toggle_div").hide();
    }
  });

  $(document).off("change", "#access_level");
  $(document).on("change", "#access_level", function(e) {
    e.stopPropagation();
    $("._invite").show();
    $type = $(this).val();
    $("#lvl_name").text($type);
    $('input[name*="feature"]').prop('checked', false);
    $('[id^=checked_counter_]').html(0);

    var acl_names = <?=json_encode($acl_names);?>;
    var acl_features = <?=json_encode($acl_features);?>;
    $setting = $('option:selected', this).attr('data-setting');

    if ($setting == "feature_access") {
      $("#is_feature_access").val('Y');
      $('#acl').show();
      if ($type in acl_features) {
        $.each(acl_features[$type], function(index, value) {
          $('.feature_click_' + value + '').prop('checked', true);
          $('#feature_' + value + '').prop('checked', true);
        });

        $.each(acl_features[$type], function(index, value) {
          $length = $(".parent_" + value + ":checked").length;
          $("#checked_counter_" + value).html($length);
        });
      } else {
        $('input[name*="feature"]').prop('checked', false);
        $('[id^=checked_counter_]').html(0);
      }
    } else {
      $("#is_feature_access").val('N');
      $("#save_btn").html('Send Registration Invite');
      $('#acl').hide();
      $("._invite").hide();
    }
    $.uniform.update();
  });

  $(document).off("click", "#txt_email");
  $(document).on("click", "#txt_email", function(e) {
    e.preventDefault();
    if ($(this).is(":checked")) {
      $("#txt_email_toggle_div").show(1000);
      $("#txt_email").val(1);
    } else {
      $("#txt_email_toggle_div").hide(1000);
      $("#txt_email").val("");
      $("#txt_email_txt1").val("");
    }
  });

  $(document).off("click", "#txt_msg");
  $(document).on("click", "#txt_msg", function(e) {
    e.preventDefault();
    if ($(this).is(":checked")) {
      $("#txt_msg").val(1);
      $("#txt_msg_toggle_div").show(1000);
    } else {
      $("#txt_msg").val("");
      $("#txt_msg_toggle_div").hide(1000);
    }
  });

  $(document).off("click", "#save_btn");
  $(document).on("click", "#save_btn", function(e) {
    e.preventDefault();
    $("#ajax_loader").show();
    $(".error").html('');
    $('.form-group').removeClass('has-error');
    $("#txt_email_txt1").val(CKEDITOR.instances.txt_email_txt1.getData());
    $.ajax({
      url: 'ajax_add_new_admin.php',
      dataType: 'JSON',
      data: $("#add_new_admin_frm").serialize(),
      type: 'POST',
      success: function(res) {
        $("#ajax_loader").hide();
        if (res.status == "success") {
          if (res.invitation !== '' && res.invitation === 'personal_invite') {
            $('#copytext').val(res.link);
            $('.agent_name').html(res.fname + " " + res.lname);
            $('#copy_alert').modal('show')
          } else {
            window.location.href = "admins.php";
          }
        } else {
          var is_error = true;
          $.each(res.errors, function(index, error) {
            $('#error_' + index).closest('.form-group').addClass('has-error');
            $('#error_' + index).html(error);
            if (is_error) {
              var offset = $('#error_' + index).offset();
              if (typeof(offset) === "undefined") {
                console.log("Not found : " + index);
              } else {
                var offsetTop = offset.top;
                var totalScroll = offsetTop - 195;
                $('body,html').animate({
                  scrollTop: totalScroll
                }, 1200);
                is_error = false;
              }
            }
          });
        }
      }
    });
  });
 
</script>