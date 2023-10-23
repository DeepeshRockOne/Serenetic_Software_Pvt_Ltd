<style type="text/css">
.editor_tag_wrap_inner  .mCSB_outside + .mCSB_scrollTools{ right:-23px; }
</style>
<div class="panel panel-default panel-block">
  <div class="panel-heading">
    <?php if(!empty($trigger_name)){ ?>
      <h4 class="mn">Send Trigger - <span class="fw300"><?=$trigger_name?></span></h4>
    <?php } else { ?>
      <h4 class="mn">Send Custom Email & Text Message (SMS)</h4>
    <?php } ?>
  </div>
  <div class="panel-body">
    <form action="" id="email_sms_form" name="email_sms_form">
      <div class="theme-form">
        <div class="form-group">
          <select class="form-control <?= !empty($triggerArr) && !empty($triggerArr['type']) ? 'has-value' : ''?>" <?= !empty($triggerArr) && $triggerArr['type'] != 'Both' ? 'disabled="disabled"' : '';?> id="delevery_method" onchange="changedeleMethod($(this))">
            <option data-hidden="true"></option>
            <option value="Email" <?= !empty($triggerArr) && $triggerArr['type'] == 'Email' ? 'selected="selected"' : '';?> >Email</option>
            <option  value="SMS" <?= !empty($triggerArr) && $triggerArr['type'] == 'SMS' ? 'selected="selected"' : '';?>>Text Message (SMS)</option>
            <option  value="Both" <?= !empty($triggerArr) && $triggerArr['type'] == 'Both' ? 'selected="selected"' : '';?>>Email & Text Message (SMS)</option>
          </select>
          <label>Delivery Method<em>*</em></label>
          <p class="error error_sent_via"></p>
        </div>
        <input type="hidden" name="from_email" value="<?=checkIsset($default_email)?>" id="from_email">
        <input type="hidden" name="sent_via" value="<?=checkIsset($triggerArr['type'])?>" id="sent_via">
        <input type="hidden" name="trigger_id" value="<?=checkIsset($triggerArr['id'])?>" id="trigger_id">
        <input type="hidden" name="customer_id" value="<?=md5($user_info['id'])?>" id="customer_id">
        <input type="hidden" name="user_email" value="<?=$user_info['email']?>" id="user_email">
        <input type="hidden" name="user_phone" value="<?=$user_info['cell_phone']?>" id="user_phone">
        <div id="email_div" style="display: <?php echo !empty($triggerArr) && in_array($triggerArr['type'],array("Both",'Email')) ? '' : 'none'?>">
          <div class="form-group">
            <div class="pull-left">
              <h4 class="mn">Email</h4>
            </div>
          </div>
          <?php if(empty($trigger_id)){ ?>
            <div class="form-group">
              <input type="text" name="email_name" class="form-control has-value" value="Custom Communication">
              <label>Name</label>
              <p class="error error_email_name"></p>
            </div>
          <?php } ?>
          <div class="form-group">
            <input type="text" name="email_from" class="form-control no_space <?=!empty($default_email) ? 'has-value' : ''?>" value="<?=$default_email?>" disabled="disabled">
            <label>From</label>
            <p class="error error_email_from"></p>
          </div>
          <div class="form-group">
            <input type="text" name="to_email" class="form-control no_space <?=!empty($user_info['email']) ? 'has-value' : ''?>" value="<?=$user_info['email']?>">
            <label>To</label>
            <p class="error error_to_email"></p>
          </div>
          <div class="form-group">
            <input type="text" name="email_subject" value="<?=checkIsset($triggerArr['email_subject'])?>"  class="form-control <?=!empty($triggerArr['email_subject']) ? 'has-value' : ''?>">
            <label>Subject</label>
            <p class="error error_email_subject"></p>
          </div>
          <div class="row">
            <div class="col-sm-12">
              <textarea class="summernote" id="email_content" name="email_content"><?=checkIsset($triggerArr['email_content'])?></textarea>
              <p class="error error_email_content"></p>
            </div>
          </div>
          <div class="m-t-20 text-right">
            <a data-href="product_smart_tag_popup.php" class="btn btn-info btn-outline product_smart_tag_popup">Available Smart Tags <i class="fa fa-info-circle" aria-hidden="true"></i></a>
          </div>
        </div>
        <div class="m-b-40"></div>
        <div id="sms_div" style="display: <?php echo !empty($triggerArr) && in_array($triggerArr['type'],array("Both",'SMS')) ? '' : 'none'?>">
          <div class="form-group">
              <div class="pull-left">
                <h4 class="mn">Text Message (SMS)</h4>
              </div>
          </div>
          <?php if(empty($trigger_id)){ ?>
            <div class="form-group">
              <input type="text" name="sms_name" class="form-control has-value" value="Custom Communication">
              <label>Name</label>
              <p class="error error_sms_name"></p>
            </div>
          <?php } ?>
          <div class="form-group">
            <input type="text" name="to_phone" value="<?=format_telephone($user_info['cell_phone'])?>" id="to_phone" class="form-control">
            <label>To</label>
            <p class="error error_to_phone"></p>
          </div>
          <div class="row">
            <div class="col-sm-12">
              <textarea class="form-control" rows="13" id="sms_content" name="sms_content"><?=checkIsset($triggerArr['sms_content'])?></textarea>
              <p>Characters Remaining: <label id="message1"></label><br>Messages over 160 characters will send in multiple SMS messages.</p>
              <p class="error error_sms_content"></p>
            </div>
          </div>
          <div class="m-t-20 text-right">
            <a data-href="product_smart_tag_popup.php" class="btn btn-info btn-outline product_smart_tag_popup">Available Smart Tags <i class="fa fa-info-circle" aria-hidden="true"></i></a>
          </div>
        </div>
        <div class="text-center m-t-30">
          <a href="javascript:void(0);" class="btn btn-action" id="send">Send</a>
          <a href="javascript:void(0);" class="btn red-link" onclick="window.close()">Cancel</a>
        </div>
      </div>
    </form>
  </div>
</div>
<script type="text/javascript">
  $(document).ready(function() {
    checkEmail();
  initCKEditor("email_content");
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
$(document).off('click','.product_smart_tag_popup');
$(document).on('click','.product_smart_tag_popup',function(e){  
  e.preventDefault();
  $href = $(this).attr('data-href');
  var not_win = window.open($href, "myWindow", "width=768,height=600");
  if(not_win.closed) {  
    alert('closed');  
  } 
});
$(document).off('click','#send');
$(document).on('click','#send',function(e){
  e.preventDefault();
  $("#email_content").val(CKEDITOR.instances.email_content.getData());
  $.ajax({
    url:"<?=$HOST?>/ajax_send_custom_email_sms.php",
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
        window.opener.setNotifySuccess(res.msg);
        window.opener.changeCommunication();
        window.location.reload();
        window.close();
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