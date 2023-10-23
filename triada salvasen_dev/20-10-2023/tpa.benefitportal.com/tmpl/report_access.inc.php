<?php include_once('notify.inc.php'); ?>
<div class="login_wrap">
    <div class="login-left-panel">
		<div class="lf_inner">
			<div class="lf_cont"> <img src="<?php echo $HOST; ?>/images/logo_white.svg<?=$cache;?>" alt="">
        	</div>
		</div>
    </div>
    <div class="login-right_panel">
		<form name="frm_verify_password" id="frm_verify_password" action="">
    		<div style="display: none;"><input type="password" name="fake_password"></div>
			<input type="hidden" name="is_ajax" id="is_ajax" value="1">

			<?php if(!empty($res_file)){ ?>
				<div class="login-form mw350 theme-form">
	    			<h3 class="mb15 fw600"><strong>Hello</strong> <?=$user_name?>,</h3>
	        		<p class="fs18 m-b-25">Verify password below to access file.</p>
	          		<div class="form-group ">
	    				<input type="password" class="form-control" id="password" name="password">
						<label>Password</label>
						<p class="error text-left" id="error_password"></p>
	    	  		</div>
	        		<div class="form-group height_auto clearfix">
	          			<button type="submit" id="submit" class="btn btn-action btn-block">Submit</button>
	    			</div>   
		        	<div class="form-group height_auto clearfix download_div" style="display: none;">
		          		<div class="text-center">
							<h5>Click here to download</h5>
							<a href="download_exported_report.php?file_name=<?=$res_file['filename']?>"><i class="fa-lg fa fa-download"></i></a>
						</div>
		        	</div>       
	      		</div>
			<?php } else { ?>
				<div class="login-form mw350 theme-form">
	        		<p class="fs18 m-b-25">It seems you have accessed a completed, expired, or restricted link. Please contact the sender of this page for assistance.</p>
	      		</div>
			<?php } ?>
    	</form>
    	<div class="login-footer"><a href="#"><?=$POWERED_BY_TEXT;?></a></div>
    </div>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		$('#frm_verify_password').on('submit',function(e){
			e.preventDefault();
			$("#ajax_loader").show();
			$(".error").html('');
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