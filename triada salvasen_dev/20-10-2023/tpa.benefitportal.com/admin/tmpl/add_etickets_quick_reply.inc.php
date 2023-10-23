<div class="panel panel-default panel-block">
	<form action="" name="quickForm" id="quickForm">
		<input type="hidden" name="quickId" id="quickId" value="<?=checkIsset($_GET['id'])?>">
		<div class="panel-heading">
			<h4 class="mn"><?=$panelHeading?></h4>
		</div>
		<div class="panel-body">
			<!-- <h4 class="fs16">Email</h4> -->
			<div class="theme-form">
				<div class="form-group" style="<?=!empty($view) ? 'display:none' : ''?>">
					<input type="text" name="quickreplyLabel" class="form-control" value="<?=checkIsset($categoryRec['title'])?>">
					<label>Quick Reply Label</label>
					<p class="error error_quickreplyLabel"></p>
				</div>
				<div class="form-group height_auto">
					<div class="row">
						<div class="<?=!empty($view) ? 'col-sm-12' : 'col-sm-9' ?>">
							<textarea class="summernote" name="quickreplyDesc" id="quickreplyDesc"><?=checkIsset($categoryRec['description'])?></textarea>
							<p class="error error_quickreplyDesc"></p>
						</div>
						<div class="col-sm-3" style="<?=!empty($view) ? 'display:none' : ''?>">
									<div class="editor_tag_wrap" >
									<div class="tag_head"><h4>AVAILABLE TAGS&nbsp;<span class="fa fa-info-circle"></span></h4></div>
									<div class="editor_tag_wrap_inner" >
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
											<div class="phone-control-wrap mn">
												<div class="phone-addon text-left" style="width: 30px;">
													<span class="fa fa-info-circle text-blue fs18"></span>
												</div>
												<div class="phone-addon">
													<label>[[phone]]</label>
												</div>
											</div>
										</div>
										
									</div>
								</div>
						</div>
					</div>
				</div>
			</div>
			<div class="text-center">
				<?php if(empty($view)) { ?>
				<a href="javascript:void(0);" class="btn btn-action m-t-20" id="saveQuick">Save</a>
				<?php } ?>
				<a href="javascript:void(0);" class="btn red-link m-t-20" onclick='parent.$.colorbox.close(); return false;'>Close</a>

				<a data-href="smart_tag_popup.php" class="btn btn-info pull-right m-r-10 tag_btn m-t-20 smart_tag_popup">Available Smart Tags (i)</a>
			</div>
		</div>
	</form>
</div>

<script type="text/javascript">
$(document).ready(function() {
	initCKEditor("quickreplyDesc",false,"165px");
	$(".smart_tag_popup").on('click',function(){
      	$href = $(this).attr('data-href');
      	window.open($href, "myWindow", "width=768,height=600");
    });

	<?=empty($view) ? '' : "CKEDITOR.instances['quickreplyDesc'].setReadOnly(true);"?>
});

$(document).off("click","#saveQuick");
$(document).on("click","#saveQuick", function(){
	$(".error").hide();
	$("#quickreplyDesc").val(CKEDITOR.instances.quickreplyDesc.getData());
	$.ajax({
			url: 'ajax_add_etickets_quick_reply.php',
			type: 'POST',
			data: $("#quickForm").serialize(),
			dataType : 'json',
			beforeSend:function(){
				$("#ajax_loader").show();
			},
			success: function(res) {
				$('#ajax_loader').hide();
				if(res.status == 'success'){
					parent.$.colorbox.close();
					parent.get_eticket_quick_reply();
					parent.setNotifySuccess(res.msg);
				}else{
					$.each(res.errors, function(key, value) {
						$('.error_' + key).html(value).show();
					});
				}
			}
		});
});
</script>