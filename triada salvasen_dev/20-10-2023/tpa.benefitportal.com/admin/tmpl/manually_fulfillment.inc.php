<div class="panel panel-default panel-block">
		<div class="panel-heading">
			<div class="panel-title">
				<h4 class="mn">Manually Generate Fulfillment File - <span class="fw300"><?=$file_name?> File</span></h4>
			</div>
		</div>
		<div class="panel-body">
			<div class="eligibility_wrap theme-form">
				<div class="bg_light_gray p-15 text-center">
					<p class="m-b-5 fw400">Total Records</p>
					<p class="mn fs18 text-action fw600"><?=$total_records?></p>
				</div>
				<div class="tab-content">
					
						<div class="tab-pane active" id="full_name">
							<form name="generate_full_file" id="generate_full_file" method="POST" action="">
								<input type="hidden" name="file_type" value="full_file">
						        <input type="hidden" name="file" value="<?=$file_id?>">
							
							<p class="fw500">Generate Via</p>
							<div class="row">
								<div class="col-sm-12">
									<div class="form-group">
										<select class="form-control" name="generate_via" id="generate_via1">
						                    <option value="Download">Download</option>
						                    <option value="Email">Email</option>
						                    <option value="FTP">FTP</option>
						                </select>
										<label>Select</label>
										<span class="error error_generate_via"></span>
									</div>
								</div>
								<div class="clearfix"></div>
								<div id="email_div1">
									<div class="col-sm-6">
										<div class="form-group">
											<input type="text" name="email" class="form-control no_space">
											<label>Enter Email Address</label>
											<span class="error error_email"></span>
										</div>
									</div>
									<div class="col-sm-6">
										<div class="form-group">
											<input type="password" name="password" class="form-control">
											<label>Set Password</label>
											<span class="error error_password"></span>
										</div>
									</div>
								</div>
								<div class="col-sm-12" id="ftp_div1">
									<div class="form-group">
										<select class="form-control" name="ftp">
											<option value=""></option>
											<option value="system_ftp">System FTP</option>
										</select>
										<label>Destination</label>
										<span class="error error_ftp"></span>
									</div>
								</div>
							</div>
							</form>
						
						<div class="text-center ">
							<a href="javascript:void(0);" id="generate_full_file_btn" class="btn btn-action">Generate File</a>
							<a href="javascript:void(0);" onclick="window.parent.$.colorbox.close()" class="btn red-link">Cancel</a>
						</div>
					</form>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>

<script type="text/javascript">
$(document).ready(function() {

	checkEmail();	

  	$("#generate_full_file_btn").click(function(){
	  	$("#ajax_loader").show();
	    $(".error").html("");
	  	var params = $("#generate_full_file").serialize();
	    $.ajax({
	      url: 'ajax_fulfillment_requests.php',
	      type: 'POST',
	      dataType: "json",
	      data: params,
	      success: function(res) {
	        $(".error").html("");
	          $("#ajax_loader").hide();
	        if (res.status == "success") {
	          window.parent.setNotifySuccess("Fulfillment File Scheduled Successfully");
				setTimeout(function(){
					window.parent.location.reload();
				}, 1000);
	        }else if(res.status == "fail"){
	            $.each(res.errors, function (index, error) {
	              $('.error_' + index).html(error).show();
	            });
	        }
	      }
	    });
	});

  	$("#email_div1").fadeOut().hide();
    $("#ftp_div1").fadeOut().hide();
   $(document).on('change','#generate_via1',function(){
	    $generate_via = $(this).val();
	    if($generate_via == 'Email'){
	      $("#email_div1").fadeIn().show();
	      $("#ftp_div1").fadeOut().hide();
	    }else if($generate_via == 'FTP'){
	       $("#ftp_div1").fadeIn().show();
	       $("#email_div1").fadeOut().hide();
	    }else{
	      $("#email_div1").fadeOut().hide();
	      $("#ftp_div1").fadeOut().hide();
	    }
   });

   $("#email_div2").fadeOut().hide();
	  	$("#ftp_div2").fadeOut().hide();
	   	$(document).on('change','#generate_via2',function(){
	    $generate_via = $(this).val();
	    if($generate_via == 'Email'){
	      $("#email_div2").fadeIn().show();
	      $("#ftp_div2").fadeOut().hide();
	    }else if($generate_via == 'FTP'){
	       $("#ftp_div2").fadeIn().show();
	       $("#email_div2").fadeOut().hide();
	    }else{
	      $("#email_div2").fadeOut().hide();
	      $("#ftp_div2").fadeOut().hide();
	    }
   });
});
</script>