<div class="panel panel-default">
	<div class="panel-heading">
		<div class="panel-title">
			<h4 class="mn">+ Resource Link</h4>
		</div>
	</div>
	<div class="panel-body">
		<form id="resource_form" name="resource_form" >
			<input type="hidden" name="group_id" id="group_id" value="<?= $group_id ?>">
			<input type="hidden" name="resource_id" id="resource_id" value="<?= $resource_id ?>">
			<p class="fs16 fw500 m-b-20">Company Information</p>
			<div class="theme-form">
				<div class="col-sm-4">
					<div class="form-group">
						<input type="text" name="label" class="form-control" value="<?= !empty($label) ? $label : '' ?>">
						<label>Label<em>*</em></label>
						<p class="error" id="error_label">
					</div>
				</div>
				<div class="col-sm-8">
					<div class="form-group">
						<input type="text" name="url" class="form-control" value="<?= !empty($url) ? $url : '' ?>">
						<label>URL<em>*</em></label>
						<p class="error" id="error_url">
					</div>
				</div>
			</div>
			<div class="clearfix text-center">
				<a href="javascript:void(0);" class="btn btn-action" id="save_comapny">Save</a>
				<a href="javascript:void(0);" class="btn red-link" id="cancel">Cancel</a>
			</div>
		</form>
	</div>
</div>
<script type="text/javascript">
	$(document).on("click","#save_comapny",function(){
		$('.error').html('');
		$('.form-group').removeClass('has-error');
		$.ajax({
			url:'ajax_add_group_resource.php',
			data:$("#resource_form").serialize(),
			dataType:'JSON',
			type:'POST',
			success:function(res){
				if(res.status=="success"){
					window.parent.$.colorbox.close();
				}else if(res.status=="fail"){
					var is_error = true;
                  	$.each(res.errors, function (index, value) {
                    	$('#error_' + index).closest('.form-group').addClass('has-error');
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
</script>