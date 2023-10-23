<form action="" role="form" method="post" class="uform" name="address_form"  id="address_form" enctype="multipart/form-data">
<input type="hidden" name='member_id' value="<?=$member_id?>">
<div class="panel panel-default">
	<div class="panel-heading">
	    <h4 class="mn">Request Access - <span class="fw300"><?=$member_res['rep_id']?></span></h4>
	</div>
	<div class="panel-body">
		<div class="first-part">
			<p>To make changes to a member account, outside of basic demographic updates, a unique 6-digit security code is required. This temporary code will be sent to the email/phone on file and the member must share this code to give you access.</p>
		    <div class="form-group height_auto">
				<p>Send by:</p>
				<div class="m-b-10">
					<label class="mn"><input type="radio" id="send_via_email" name="send_via" value="email">Email</label>
				</div>
				<div class="m-b-10">
					<label class="mn"><input type="radio" id="send_via_sms" name="send_via" value="sms">Text Message</label>
				</div>
				<p class="error"><?=isset($errors['send_via'])?$errors['send_via']:''?></p>
			</div>	
		    <div class="text-center">
		        <input type="submit" name="save" class="btn btn-action btn-send" onclick="show_loader();" value="Send">
		        <a href="javascript:void(0);" class="btn red-link" onclick="parent.$.colorbox.close();">Cancel</a>
		    </div>
		</div>
	</div>
  </div>
</form>
<script type="text/javascript">
  	function show_loader(){
	    $("#ajax_loader").show();
  	}
</script>