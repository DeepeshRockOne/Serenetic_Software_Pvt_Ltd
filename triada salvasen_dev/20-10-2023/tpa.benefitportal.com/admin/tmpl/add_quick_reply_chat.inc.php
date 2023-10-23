<form method="POST" name="frm_reply" class="form_wrap" id="frm_reply" enctype="multipart/form-data">
	<input type="hidden" name="action" value="save_reply"> 
	<input type="hidden" name="reply_key" value="<?=$reply_key?>"> 
	<div class="panel panel-default panel-block quick_reply_wrap">
		<div class="panel-heading">
			<h4 class="mn">Quick Reply</h4>
		</div>
		<div class="panel-body">
			<div class="theme-form">
				<div class="form-group">
					<input type="text" name="reply_name" id="reply_name" value="<?=$reply_name?>" class="form-control" required>
					<label>Quick Reply Name</label>
					<p class="error" id="err_reply_name"></p>
				</div>
				<p class="fs16 fw500">Quick Reply</p>
				<div class="form-group height_auto">
					<textarea class="form-control" name="reply_text" id="reply_text" rows="4" placeholder="Quick Reply…" required><?=$reply_text?></textarea>
					<p class="error" id="err_reply_text"></p>
				</div>
				<div class="form-group height_auto" style="display: none;">
					<div class="tag_head"><p class="m-b-5 fs14">AVAILABLE TAGS&nbsp;<span class="fa fa-info-circle"></span></p></div>
					<div class="editor_tag_wrap bg_light_gray" >
						
						<div class="editor_tag_wrap_inner" >
							<div>
								<label>[[fname]]</label>
							</div>
							<div>
								<label>[[lname]]</label>
							</div>
							
						</div>
					</div>
				</div>
			</div>
			<div class="text-center">
				<button type="submit" class="btn btn-action">Save</button>
				<a href="javascript:void(0);" class="btn red-link" onclick='parent.$.colorbox.close(); return false;'>Cancel</a>
			</div>
		</div>
	</div>
</form>
<script type="text/javascript">
	$(document).ready(function() {
		$(document).off('submit', '#frm_reply');
        $(document).on('submit', '#frm_reply', function (e) {
            e.preventDefault();
            $('#ajax_loader').show();
            $.ajax({
                url: 'add_quick_reply_chat.php',
                data: new FormData(this),
                method: 'POST',
                cache: false,
                dataType:'json',
                contentType: false,
                processData: false,
                success: function (res) {
                    $("[id^='err_']").html('');

                    if (res.status == "fail") {
                        $('#ajax_loader').hide();
                        var is_error = true;
                        $.each(res.errors, function (index, value) {
                            if (typeof(index) !== "undefined") {
                                $("#err_" + index).html(value);
                                if (is_error) {
                                    var offset = $("#err_" + index).offset();
                                    var offsetTop = offset.top;
                                    var totalScroll = offsetTop - 200;
                                    $('body,html').animate({
                                        scrollTop: totalScroll
                                    }, 1200);
                                    is_error = false;
                                }                            
                            }
                        });
                    } else {
                        if(res.status == 'success'){
                            window.parent.$.colorbox.close()
                            parent.window.location.reload();
                        }
                    }
                }
            });
        });
	});
</script>