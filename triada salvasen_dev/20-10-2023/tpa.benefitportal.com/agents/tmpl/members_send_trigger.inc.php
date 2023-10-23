<style type="text/css">
.editor_tag_wrap_inner  .mCSB_outside + .mCSB_scrollTools{ right:-23px; }
</style>
<div class="panel panel-default panel-block">
  <div class="panel-heading">
    <h4 class="mn">Send Trigger - <span class="fw300"><?=$trigger_name?></span></h4>
  </div>
  <div class="panel-body">
    <form action="" id="email_sms_form" name="email_sms_form">
      <div class="theme-form">
        <div class="form-group">
          <select class="form-control <?=!empty($triggerArr['type']) ? 'has-value' : ''?>" <?=$triggerArr['type'] != 'Both' ? 'disabled="disabled"' : '';?> id="delevery_method" onchange="changedeleMethod($(this))">
            <option data-hidden="true"></option>
            <option value="Email" <?=$triggerArr['type'] == 'Email' ? 'selected="selected"' : '';?> >Email</option>
            <option  value="SMS" <?=$triggerArr['type'] == 'SMS' ? 'selected="selected"' : '';?>>Text Message (SMS)</option>
            <option  value="Both" <?=$triggerArr['type'] == 'Both' ? 'selected="selected"' : '';?>>Email & Text Message (SMS)</option>
          </select>
          <label>Delivery Method<em>*</em></label>
        </div>
        <input type="hidden" name="sent_via" value="<?=$triggerArr['type']?>" id="sent_via">
        <input type="hidden" name="trigger_id" value="<?=$triggerArr['id']?>" id="trigger_id">
        <input type="hidden" name="customer_id" value="<?=md5($user_info['id'])?>" id="customer_id">
        <?php  if(in_array($triggerArr['type'],array("Both",'Email'))){ ?>
          <div id="email_div">
            <div class="form-group">
              <div class="pull-left">
                <h4 class="mn">Email</h4>
              </div>
              <!-- <div class="pull-right">
                <div class="phone-control-wrap">
                  <div class="phone-addon">
                    Preview
                  </div>
                  <div class="phone-addon">
                    <div class="custom-switch">
                      <label class="smart-switch">
                        <input type="checkbox" class="js-switch"  />
                        <div class="smart-slider round"></div>
                      </label>
                    </div>
                  </div>
                </div>
              </div> -->
            </div>
            <div class="form-group">
              <!-- <select class="form-control" name="email_from" id="email_from">
                <option data-hidden="true"></option>
                <option value="<?=$default_email?>"><?=$default_email?></option>
              </select> -->
              <input type="text" name="email_from" class="form-control <?=!empty($default_email) ? 'has-value' : ''?>" value="<?=$default_email?>">
              <label>From</label>
              <p class="error error_email_from"></p>
            </div>
            <div class="form-group">
              <input type="text" name="to_email" class="form-control <?=!empty($user_info['email']) ? 'has-value' : ''?>" value="<?=$user_info['email']?>">
              <label>To</label>
              <p class="error error_to_email"></p>
            </div>
            <div class="form-group">
              <input type="text" name="email_subject" value="<?=$triggerArr['email_subject']?>"  class="form-control <?=!empty($triggerArr['email_subject']) ? 'has-value' : ''?>">
              <label>Subject</label>
              <p class="error error_email_subject"></p>
            </div>
            <div class="row">
              <div class="col-sm-9">
                <textarea class="summernote" id="email_content" name="email_content"><?=$triggerArr['email_content']?></textarea>
                <p class="error error_email_content"></p>
              </div>
              <div class="col-sm-3">
                <div class="editor_tag_wrap" >
                  <div class="tag_head"><h4>*REQUIRED TAGS&nbsp;&nbsp;<span class="fa fa-info-circle"></span></h4></div>
                  <div class="editor_tag_wrap_inner" style="max-height:350px;">
                    <div>
                      <div class="phone-control-wrap">
                        <div class="phone-addon text-left" style="width: 30px;">
                          <span class="fa fa-info-circle text-blue fs18"></span>
                        </div>
                        <div class="phone-addon">
                          <label>[[fname]]</label>
                        </div>
                      </div>
                    </div>
                    <div>
                      <div class="phone-control-wrap">
                        <div class="phone-addon text-left" style="width: 30px;">
                          <span class="fa fa-info-circle text-blue fs18"></span>
                        </div>
                        <div class="phone-addon">
                          <label>[[lname]]</label>
                        </div>
                      </div>
                    </div>
                    <div class="tag_head"><h4>AVAILABLE TAGS&nbsp;&nbsp;<span class="fa fa-info-circle"></span></h4></div>
                    <div>
                      <div class="phone-control-wrap">
                        <div class="phone-addon text-left" style="width: 30px;">
                          <span class="fa fa-info-circle text-blue fs18"></span>
                        </div>
                        <div class="phone-addon">
                          <label>[[email]]</label>
                        </div>
                      </div>
                    </div>
                    <div>
                      <div class="phone-control-wrap">
                        <div class="phone-addon text-left" style="width: 30px;">
                          <span class="fa fa-info-circle text-blue fs18"></span>
                        </div>
                        <div class="phone-addon">
                          <label>[[phone]]</label>
                        </div>
                      </div>
                    </div>
                    <div>
                      <div class="phone-control-wrap">
                        <div class="phone-addon text-left" style="width: 30px;">
                          <span class="fa fa-info-circle text-blue fs18"></span>
                        </div>
                        <div class="phone-addon">
                          <label>[[product]]</label>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        <?php } ?>
        <div class="m-b-40"></div>
        <?php if(in_array($triggerArr['type'],array("Both",'SMS'))){ ?>
          <div id="sms_div">
            <div class="form-group">
                <div class="pull-left">
                  <h4 class="mn">Text Message (SMS)</h4>
                </div>
                <!-- <div class="pull-right">
                  <div class="phone-control-wrap">
                    <div class="phone-addon">
                      Preview
                    </div>
                    <div class="phone-addon">
                      <div class="custom-switch">
                        <label class="smart-switch">
                          <input type="checkbox" class="js-switch"  />
                          <div class="smart-slider round"></div>
                        </label>
                      </div>
                    </div>
                  </div>
                </div> -->
            </div>
            <div class="form-group">
              <input type="text" name="to_phone" value="<?=format_telephone($user_info['cell_phone'])?>" id="to_phone" class="form-control">
              <label>To</label>
              <p class="error error_to_phone"></p>
            </div>
            <div class="row">
              <div class="col-sm-9">
                <textarea class="form-control" rows="13" id="sms_content" name="sms_content"><?=$triggerArr['sms_content']?></textarea>
                <p>Characters Remaining: <label id="message1"></label><br>Messages over 160 characters will send in multiple SMS messages.</p>
                <p class="error error_sms_content"></p>
              </div>
              <div class="col-sm-3">
                <div class="editor_tag_wrap" >
                  <div class="tag_head"><h4>*REQUIRED TAGS&nbsp;&nbsp;<span class="fa fa-info-circle"></span></h4></div>
                  <div class="editor_tag_wrap_inner" style="max-height:350px;">
                    <div>
                      <div class="phone-control-wrap">
                        <div class="phone-addon text-left" style="width: 30px;">
                          <span class="fa fa-info-circle text-blue fs18"></span>
                        </div>
                        <div class="phone-addon">
                          <label>[[fname]]</label>
                        </div>
                      </div>
                    </div>
                    <div>
                      <div class="phone-control-wrap">
                        <div class="phone-addon text-left" style="width: 30px;">
                          <span class="fa fa-info-circle text-blue fs18"></span>
                        </div>
                        <div class="phone-addon">
                          <label>[[lname]]</label>
                        </div>
                      </div>
                    </div>
                    <div class="tag_head"><h4>AVAILABLE TAGS&nbsp;&nbsp;<span class="fa fa-info-circle"></span></h4></div>
                    <div>
                      <div class="phone-control-wrap">
                        <div class="phone-addon text-left" style="width: 30px;">
                          <span class="fa fa-info-circle text-blue fs18"></span>
                        </div>
                        <div class="phone-addon">
                          <label>[[email]]</label>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        <?php } ?>
        <div class="text-center m-t-30">
          <a href="javascript:void(0);" class="btn btn-action" id="send">Send</a>
          <a href="javascript:void(0);" class="btn red-link" onclick="parent.$.colorbox.close()">Cancel</a>
        </div>
      </div>
    </form>
  </div>
</div>
<script type="text/javascript">
  $(document).ready(function() {
  $('.summernote').summernote({
  toolbar: $SUMMERNOTE_TOOLBAR,
  disableDragAndDrop : $SUMMERNOTE_DISABLE_DRAG_DROP,
  focus: true, // set focus to editable area after initializing summernote
  height:330,
  callbacks: {
    onImageUpload: function(image) {
      editor = $(this);
      uploadImageContent(image[0], editor);
    },
    onMediaDelete : function(target) {
        deleteImage(target[0].src);
        target.remove();
    }
  }
});
  $("#to_phone").mask("(999) 999-9999");
  $(".editor_tag_wrap_inner").mCustomScrollbar({
      theme:"dark",
      scrollbarPosition: "outside"
  });
  var chars = $("#sms_content").val().length;
  $("#message1").text(160 - chars);

  $("#sms_content").keyup(function (e) {
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
});
$(document).off('click','#send');
$(document).on('click','#send',function(e){
  e.preventDefault();
  $.ajax({
    url:"<?=$HOST?>/ajax_send_email_sms.php?location=agent",
    data : $("#email_sms_form").serialize(),
    dataType : 'json',
    type:'post',
    beforeSend : function(e){
      $("#ajax_loader").show();
    },
    success :function(res){
      $("#ajax_loader").hide();
      $(".error").html('');
      if(res.status =='success'){
        parent.$.colorbox.close();
        parent.setNotifySuccess(res.msg);
      }else if(res.status == 'fail'){
        parent.$.colorbox.close();
        parent.setNotifyError(res.msg);
      }else{
        $.each(res.errors,function(index,error){
          $('.error_' + index).html(error).show();
          scrollToElement("#email_sms_form");
        });
      }
    }
  });
});

scrollToElement = function(e) {
    add_scroll = 0;
    element_id = $(e).attr('id');
    var offset = $(e).offset();
    var offsetTop = offset.top;
    var totalScroll = offsetTop - 200 + add_scroll;
    $('body,html').animate({
        scrollTop: totalScroll
    }, 1200);

}
changedeleMethod = function(element){
  var $val = element.val();
  $("#sent_via").val($val);
  if($val == 'SMS'){
    $("#sms_div").show();
    $("#email_div").hide();
  }else if($val == 'Email'){
    $("#email_div").show();
    $("#sms_div").hide();
  }else if($val == 'Both'){
    $("#email_div").show();
    $("#sms_div").show();
  }else{
    $("#sms_div").hide();
    $("#email_div").hide();
  }
}
</script>