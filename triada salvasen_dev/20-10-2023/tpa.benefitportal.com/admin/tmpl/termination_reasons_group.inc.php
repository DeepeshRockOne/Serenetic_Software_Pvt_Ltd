<div class="panel panel-default">
	<div class="panel-heading">
		<h4>+ Termination Reason</h4>
	</div>
	<form  method="POST" id="manage_group_form" enctype="multipart/form-data"  autocomplete="off">
		<input type="hidden" name="rule_id" id="rule_id" value="<?= $rule_id ?>">
		<div class="panel-body">
			<div class="theme-form">
				<div class="form-group">
					<input type="text" name="reason" class="form-control" value="<?= !empty($reason) ? $reason : '' ?>">
					<label>Name Termination Reason</label>
					<p class="error" id="error_reason"></p>
				</div>
				<p class="m-b-10">Does this termination reason qualify for COBRA?</p>
				<div class="form-group height_auto">
					<div class="m-b-10">
						<label class="mn"><input type="radio" name="is_qualifies_for_cobra" value="Y" <?= !empty($is_qualifies_for_cobra) && $is_qualifies_for_cobra == 'Y' ? 'checked' : '' ?>> Yes</label>
					</div>
					<div class="m-b-0">
						<label class="mn"><input type="radio" name="is_qualifies_for_cobra" value="N" <?= !empty($is_qualifies_for_cobra) && $is_qualifies_for_cobra == 'N' ? 'checked' : '' ?>> No</label>
					</div>
					<p class="error" id="error_is_qualifies_for_cobra"></p>
				</div>
				<div class="text-center">
					<a href="javascript:void(0);" class="btn btn-action" id="save">Save</a>
					<a href="javascript:void(0);" class="btn red-link" id="cancel">Cancel</a>
				</div>
			</div>
		</div>
	</form>
</div>
<script type="text/javascript">
  	//******************** Button Code Start **********************
		$(document).on("click","#save",function(){
			$("#ajax_loader").show();
			$(".error").html("");
			$.ajax({
				url:'ajax_termination_reasons_group.php',
				dataType:'JSON',
				data:$("#manage_group_form").serialize(),
				type:"POST",
				success:function(res){
					$("#ajax_loader").hide();
					if(res.status=="success"){
						window.parent.setNotifySuccess(res.msg);
						window.parent.$.colorbox.close();
					}else{
						var is_error = true;
		              	$.each(res.errors, function (index, value) {
		                  $('#error_' + index).html(value).show();
		                  if(is_error){
		                      var offset = $('#error_' + index).offset();
		                      var offsetTop = offset.top;
		                      var totalScroll = offsetTop - 50;
		                      $('body,html').animate({scrollTop: totalScroll}, 1200);
		                      is_error = false;
		                  }
		              	});
					}
				}
			});
		});
		$(document).on("click","#cancel",function(){
			window.parent.$.colorbox.close();
		});
	//******************** Button Code End   **********************
</script>