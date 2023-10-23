<form method="POST" name="frm_dep" class="form_wrap" id="frm_dep" enctype="multipart/form-data">
	<input type="hidden" name="action" value="save_department"> 
	<input type="hidden" name="department_id" value="<?=$department_id?>"> 
<div class="panel panel-default panel-block">
	<div class="panel-heading">
		<h4 class="mn">+ Category</h4>
	</div>
	<div class="panel-body">
		<div class="theme-form">
			<div class="form-group">
				<input type="text" name="department_name" id="department_name" value="<?=$department_name?>" class="form-control">
				<label>Name</label>
				<p class="error" id="err_department_name"></p>
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
		$(document).off('submit', '#frm_dep');
        $(document).on('submit', '#frm_dep', function (e) {
            e.preventDefault();
            $('#ajax_loader').show();
            $.ajax({
                url: 'live_chat_department.php',
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