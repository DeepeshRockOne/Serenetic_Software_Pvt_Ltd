<div id="smarteapp_vue" class="panel panel-default ">
	<div class="panel-heading">
		<div class="panel-title">
			<h4 class="mn">Share Website - <span class="fw300"><?=!empty($_SESSION['agents']['public_name']) ? $_SESSION['agents']['public_name'] : $_SESSION['agents']['fname'].' '.$_SESSION['agents']['lname'];?></span></h4>
		</div>
	</div>
	<form action="share_website.php?sent_via=<?=$_GET['sent_via']?>&id=<?=$_GET['id']?>" role="form" method="post" class="theme-form " name="form_share_website" id="form_share_website" enctype="multipart/form-data">
		<input type="hidden" name="operation" value="share_website">
		<div class="panel-body">
			<div v-show="sent_via === 'email'">
				<h4 class="m-b-20 fs18">Email</h4>
				<div class="theme-form">
					<div class="row">
						<div class="col-sm-6">
							<div class="form-group">
								<input type="text" name="from_email" id="from_email" v-model="from_email" class="form-control">
								<label>From</label>
								<p class="error" id="error_from_email"></p>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<input type="text" name="to_email" id="to_email" v-model="to_email" class="form-control">
								<label>To</label>
								<p class="error" id="error_to_email"></p>
							</div>
						</div>
						<div class="col-sm-12">
							<div class="form-group">
								<input type="text" name="email_subject" id="email_subject" v-model="email_subject" class="form-control">
								<label>Subject</label>
								<p class="error" id="error_email_subject"></p>
							</div>
						</div>
					</div>
					<div class="form-group height_auto">
						<textarea name="email_content" id="email_content" class="summernote"><?=$email_content;?></textarea>
						<p class="error" id="error_email_content"></p>
					</div>
				</div>
			</div>
			<div v-show="sent_via === 'text'">
				<h4 class="m-b-20 fs18">Text Message (SMS)</h4>
				<div class="theme-form">
					<div class="row">
						<div class="col-sm-12">
							<div class="form-group">
								<input type="text" name="to_phone" id="to_phone" v-model="to_phone" @keyup="to_phone = this.event.target.value;" class="form-control">
								<label>To Phone</label>
								<p class="error" id="error_to_phone"></p>
							</div>
						</div>
					</div>
					<div class="form-group height_auto">
						<textarea id="sms_content" name="sms_content" v-model="sms_content" rows="3" class="form-control" maxlength="160"></textarea>
                      	<span class="error textarea_error" id="error_sms_content"></span>
						<p>Characters Remaining: <span id="message1" class="text-light-gray">160</span><br>Messages over 160 characters will send in multiple SMS messages.</p>
					</div>
					
				</div>
			</div>
		</div>
	</form>
	<div class="panel-footer text-center">
		<button type="button" class="btn btn-action btn_share_website" v-show="sent_via === 'email'" :disabled="from_email == '' || to_email == '' || email_subject == ''">Send</button>
		<button type="button" class="btn btn-action btn_share_website" v-show="sent_via === 'text'" :disabled="to_phone == '' || sms_content == ''">Send</button>
      	<a href="javascript:void(0)" class="btn red-link m-l-15" onclick="parent.$.colorbox.close();">Cancel</a>
	</div>
</div>
<script type="text/javascript">
	var smarteapp_vue = new Vue({
        el: '#smarteapp_vue',
        data: {
            sent_via: '<?=$sent_via;?>',
            from_email: '<?=$from_email;?>',
            to_email: '',
            email_subject: '<?=$email_subject;?>',
            to_phone: '',
            sms_content: '<?= $sms_content ?>',
        },
        methods: {},
        computed: {}
    });
	$(document).ready(function() {
		$("#to_phone").inputmask({"mask": "(999) 999-9999",'showMaskOnHover': false});

		var chars = jQuery("#sms_content").val().length;
        jQuery("#message1").text(160 - chars);

        jQuery("#sms_content").keyup(function (e) {
            var chars = jQuery(this).val().length;
            jQuery("#message1").text(160 - chars);

            if (chars > 160 || chars <= 0) {
                jQuery("#message1").addClass("minus");
                jQuery(this).css("text-decoration", "line-through");
            } else {
                jQuery("#message1").removeClass("minus");
                jQuery(this).css("text-decoration", "");
                e.preventDefault();
            }
        });

		$('.summernote').summernote({
		  toolbar: $SUMMERNOTE_TOOLBAR,
		  disableDragAndDrop : $SUMMERNOTE_DISABLE_DRAG_DROP,
		  focus: true, // set focus to editable area after initializing summernote
		  height:250,
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
	    $(document).off('click', '.btn_share_website');
        $(document).on('click', '.btn_share_website', function (e) {
        	$(".btn_share_website").prop('disabled',true);
            formHandler($("#form_share_website"),
                function () {
                    $("#ajax_loader").show();
                },
                function (data) {
                    $("#ajax_loader").hide();
                    $(".btn_share_website").prop('disabled',false);
                    $("p.error").hide();
                    if (data.status == 'success') {
                        window.parent.location.href=window.parent.location.href;
                    } else if (data.status == "fail") {
                        window.parent.location.href=window.parent.location.href;
                    } else {
                        $(".error").hide();
                        $.each(data.errors, function (key, value) {
                            $('#error_' + key).parent("p.error").show();
                            $('#error_' + key).html(value).show();
                            $('.error_' + key).parent("p.error").show();
                            $('.error_' + key).html(value).show();
                            if ($("[name='" + key + "']").length > 0) {
                                $('html, body').animate({
                                    scrollTop: parseInt($("[name='" + key + "']").offset().top) - 100
                                }, 1000);
                            }
                        });
                    }
                });
        });
	});
</script>