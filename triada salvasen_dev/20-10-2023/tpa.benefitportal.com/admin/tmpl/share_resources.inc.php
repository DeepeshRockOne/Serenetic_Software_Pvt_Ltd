<div class="panel panel-default panel-block">
	<div class="panel-heading">
		<h4 class="mn">Resource - <span class="fw300"><?=$resResource['resource_name']?></span></h4>
	</div>
	<form action="" id="email_sms_form">
		<input type="hidden" name="resource_id" id="resource_id" value="<?=$id?>">
		<div class="panel-body">
			<div class="theme-form">
				<h4>Share By</h4>
				<div class="form-group">
					<select class="form-control" id="delevery_method" name="sent_via" onchange="changedeleMethod($(this))">
						<option data-hidden="true"></option>
						<option value="Email">Email</option>
						<option  value="SMS">Text Message (SMS)</option>
						<option  value="Both">Email & Text Message (SMS)</option>
					</select>
					<label>Select</label>
					<p class="error error_sent_via"></p>
				</div>
				<div id="email_div" class="share_both">
					<h4>Email</h4>
					<div class="row">
						<div class="col-sm-6">
							<div class="form-group">
								<input type="text" name="to_email"  class="form-control no_space">
								<label>To</label>
								<p class="error error_to_email"></p>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<input type="text" name="cc_email" class="form-control no_space">
								<label>Cc</label>
								<p class="error error_cc_email"></p>
							</div>
						</div>
						<div class="col-sm-12">
							<div class="form-group">
								<input type="text" name="email_subject" value="<?=$triggersArr['email_subject']?>" class="form-control">
								<label>Subject</label>
								<p class="error error_email_subject"></p>
							</div>
						</div>
					</div>
					<div class="m-b-25">
						<textarea class="summernote" name="email_content" id="email_content"><?=$triggersArr['email_content']?></textarea>
						<p class="error error_email_content"></p>
					</div>
				</div>
				<div id="sms_div" class="share_both">
					<h4>Text Message (SMS)</h4>
					<div class="row">
						<!-- <div class="col-sm-6">
							<div class="form-group">
								<input type="text" name="" class="form-control">
								<label>From Phone</label>
							</div>
						</div> -->
						<div class="col-sm-6">
							<div class="form-group">
								<input type="text" name="to_phone" class="form-control">
								<label>To Phone</label>
								<p class="error error_to_phone"></p>
							</div>
						</div>
					</div>
					<!-- <div class="thumbnail email_triger_shadow bg_white m-b-15">
						<p class="m-b-30">Lorem ipsum amal, simul atque insitam in animis nostris inesse notionem, ut calere ignem, nivem esse admonere interesse enim ipsam voluptatem, quia dolor sit, aspernatur aut quid est eligendi optio, cimque.</p>
						<p class="fs16 text-gray">ATTACHED - <a href="javascript:void(0);" class="red-link fw500">Left_menu.pdf</a></p>
					</div> -->
					<!-- <div class="m-b-25">
						<textarea class="summernote" name="sms_content" id="sms_content"><?=$triggersArr['sms_content']?></textarea>
					</div>
					<p class="fs12 m-b-15">Characters Remaining: 0<br>
						Messages over 160 characters will send in multiple SMS messages.
					</p> -->
					<div class=" fs12 m-b-15">
						<textarea class="form-control" rows="13" id="sms_content" name="sms_content"><?=$triggersArr['sms_content']?></textarea>
						<p class="fs12 m-b-15">Characters Remaining: <label id="message1"></label><br>Messages over 160 characters will send in multiple SMS messages.</p>
						<p class="error error_sms_content"></p>
				</div>
				</div>
			</div>
		</div>
		<div class="panel-footer text-center">
			<a href="javascript:void(0)" class="btn btn-action" id="send">Send</a>
			<a href="javascript:void(0)" data-href="view_resources.php?id=<?= $id ?>" class="btn red-link back_view_resources">Back</a>
		</div>
	</form>
</div>

<script type="text/javascript">
 $(document).off('click', '.back_view_resources');
  $(document).on('click', '.back_view_resources', function (e) {
    e.preventDefault();
    window.parent.$.colorbox({
      href: $(this).attr('data-href'),
      iframe: true, 
      width: '515px', 
      height: '300px'
    });
  });
  $(document).ready(function() {
	  checkEmail();  	
	  $(".share_both").hide();

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
		
		initCKEditor("email_content",false,"300px");
});

$(document).off('click','#send');
$(document).on('click','#send',function(e){
  e.preventDefault();
  $('#email_content').val(CKEDITOR.instances.email_content.getData());
  $.ajax({
    url:"ajax_send_email_or_sms_resources.php",
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
  if($val == 'SMS'){
    $("#sms_div").show();
    $("#email_div").hide();
  }else if($val == 'Email'){
    $("#email_div").show();
    $("#sms_div").hide();
  }else if($val == 'Both'){
    $(".share_both").show();
  }else{
    $("#sms_div").hide();
    $("#email_div").hide();
  }
}
</script>