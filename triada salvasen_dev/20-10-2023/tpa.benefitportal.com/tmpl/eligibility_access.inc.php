<div id="enrollment_verification_age">
<div class="bg_white">
	<div class="section_wrap">
		<div class="container">
			<div class="text-center">
				<p class="fs32 fw300 mb20"><strong>Hello</strong> <?=$admin_name?>,</p>
				<p class="fs16 m-b-30 password_div">Verify password below to access file.</p>
			</div>
		<div class="row theme-form">
			<div class="col-md-4 col-md-offset-4 col-sm-8 col-sm-offset-2 text-center">
			<div class="form-group height_auto mn password_div">
				<form name="frm_verify_password" id="frm_verify_password" action="">
					<div style="display: none;">
						<input type="password" name="fake_password">
					</div>
					<input type="hidden" name="is_ajax" id="is_ajax" value="1">
					<div class="phone-control-wrap">
						<div class="phone-addon">
							<div class="form-group">
								<div class="pr">
									<input type="password" class="form-control" id="password" name="password">
									<label>Password</label>
								</div>
									<p class="error text-left" id="error_password"></p>
							</div>
						</div>
						<div class="phone-addon w-70 v-align-top">
							<button type="submit" id="submit" class="btn btn-action btn-block">Submit</button>
						</div>
					</div>
				</form>
			</div>
			<div class="col-sm-12 download_div" style="display: none;">
				<div class="text-center">
					<a href="<?=$ELIGIBILITY_FILES_PATH . $res_file['processed_file_name']?>" class="btn btn-info btn-outline">Click here to download</a>
				</div>
			</div>
			</div>
		</div>
		</div>
	</div>
	<div class="verification_banner">
		<img src="<?=$HOST?>/images/member_verification_bg.jpg" class="img-responsive">
	</div>
	<div class="smarte_footer mn">
		
		<div class="bottom_footer ">
			<div class="container">
			<a href="javascript:void(0);" class="btn btn-white-o">Live Chat</a>
				<ul>
					<li><a href="#">Privacy Plan</a></li>
					<li><a href="#">Terms & Conditions</a></li>
					<li><a href="#">Legal</a></li>
					<li><a href="#">FAQ</a></li>
					<li>Smart &copy; <?php echo date('Y')?> </li>
				</ul>
			</div>
		</div>
	</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		$('#frm_verify_password').on('submit',function(e){
			e.preventDefault();
			$("#ajax_loader").show();
			$.ajax({
		        // url: 'eligiblity_access.php',
		        data: $("#frm_verify_password").serialize(),
		        type: 'POST',
		        dataType: "json",
		        success: function(res) {
		            $("#ajax_loader").hide();
		            if (res.status == "success") {
		                $('.download_div').show('slow');
		                $('.password_div').hide('slow');
		               
		            } else if(res.status=="error") {
		                if(res.error){
		                	$('#error_password').text(res.error);
		                }
		      			$('.download_div').hide('slow');
		      			$('.password_div').show('slow');
		            }
		        }
		    });
		});
	});
</script>