<div class="panel panel-default panel-block">
		<div class="panel-heading">
			<div class="panel-title">
				<h4 class="mn">Manually Generate Eligibility File - <span class="fw300"><?=$file_name?> File</span></h4>
			</div>
		</div>
		<div class="panel-body">
			<div class="eligibility_wrap theme-form">
				<div class="cust_tab_ui">
					<ul class="nav nav-tabs nav-noscroll" role="tablist">
						<li role="presentation" class="active"><a href="#full_name" class="fs16" aria-controls="full_name" role="tab" data-toggle="tab">Full File</a></li>
						<li role="presentation"><a href="#add_change_file" aria-controls="add_change_file" role="tab" data-toggle="tab">Add/Change/Delete <br>File</a></li>
					</ul>
				</div>
				<div class="tab-content">
					
						<div class="tab-pane active" id="full_name">
							<form name="generate_full_file" id="generate_full_file" method="POST" action="">
								<input type="hidden" name="file_type" value="full_file">
						        <input type="hidden" name="file" value="<?=$file_id?>">
							<p class="fw500">Active Through</p>
							<div class="row">
								<div class="col-sm-6">
									<div class="form-group">
										<div class="input-group">
											<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
											<div class="pr">
												<input id="" type="text" class="form-control full_datepicker" name="active_through">
												<label>MM / DD / YYYY</label>
											</div>
										</div>
										<span class="error error_active_through"></span>
									</div>
								</div>
							</div>
							<p class="fw500">Generate Via</p>
							<div class="row">
								<div class="col-sm-6">
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
								<div class="col-sm-6" id="ftp_div1">
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
						<div class="tab-pane" id="add_change_file">
							<form name="generate_add_change_file" id="generate_add_change_file" method="POST" action="">
								<input type="hidden" name="file_type" value="add_change_file">
						    <input type="hidden" name="file_key" value="<?=$file_key?>">
						        <input type="hidden" name="file" value="<?=$file_id?>">
										<div class="row">
											<?php if(checkIsset($file_key) != 'HEALTHY_STEP_AUGEO'){ ?>
											<div class="col-sm-6">
												<p class="fw500">Since</p>
												<div class="form-group">
													<div class="input-group">
														<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
														<div class="pr">
															<input id="since" type="text" class="form-control datepicker_active2" name="since_date">
															<label>MM / DD / YYYY</label>
														</div>
													</div>
													<span class="error error_since_date"></span>
												</div>
											</div>
										<?php } ?>
											<div class="col-sm-6">
												<p class="fw500">Active Through</p>
												<div class="form-group">
													<div class="input-group">
														<span class="input-group-addon full_datepicker_calender"><i class="fa fa-calendar"></i></span>
														<div class="pr">
															<input id="" type="text" class="form-control datepicker_active2" name="active_through_change_file">
															<label>MM / DD / YYYY</label>
														</div>
													</div>
													<span class="error error_active_through_change_file"></span>
												</div>
											</div>
										</div>
								<p class="fw500">Generate Via</p>
								<div class="row">
									<div class="col-sm-6">
										<div class="form-group">
											<select class="form-control" name="generate_via" id="generate_via2">
							                    <option value="Download">Download</option>
							                    <option value="Email">Email</option>
							                    <option value="FTP">FTP</option>
							                </select>
											<label>Select</label>
											<span class="error error_generate_via"></span>
										</div>
									</div>
									<div class="clearfix"></div>
									<div id="email_div2">
										<div class="col-sm-6" >
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
									<div class="col-sm-6" id="ftp_div2">
										<div class="form-group">
											<select class="form-control" name="ftp">
												<option value=""></option>
												<option value="system_ftp">System FTP</option>
											</select>
											<label>Destination</label>
											<span class="error error_ftp" id="error_ftp"></span>
										</div>
									</div>
								</div>
							</form>
							<div class="text-center ">
								<a href="javascript:void(0);" id="generate_add_change_file_btn" class="btn btn-action">Generate File</a>
								<a href="javascript:void(0);" onclick="window.parent.$.colorbox.close()" class="btn red-link">Cancel</a>
							</div>
						</div>
				</div>
			</div>
		</div>
	</form>
</div>

<script type="text/javascript">
$(document).ready(function() {
	checkEmail();
	$(".full_datepicker").datepicker({
	    format: "mm/dd/yyyy",
    });

    $(".full_datepicker").inputmask({"mask": "99/99/9999",'showMaskOnHover': false});
    $('.full_datepicker_calender').datepicker({
          format: "mm/dd/yyyy",
    }).on('changeDate', function (ev) {
         var val= ev.dates;
         var date = $(".full_datepicker_calender").data('datepicker').getFormattedDate('mm/dd/yyyy');
         $(".full_datepicker").html(date);
         $(".full_datepicker").val(date);
    });			

  	$("#generate_full_file_btn").click(function(){
	  	$("#ajax_loader").show();
	    $(".error").html("");
	  	var params = $("#generate_full_file").serialize();
	    $.ajax({
	      url: 'ajax_eligiblity_requests.php',
	      type: 'POST',
	      dataType: "json",
	      data: params,
	      success: function(res) {
	        $(".error").html("");
	          $("#ajax_loader").hide();
	        if (res.status == "success") {
	          window.parent.setNotifySuccess("Eligiblity File Scheduled Successfully");
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

	$("#generate_add_change_file_btn").click(function(){
	  	$("#ajax_loader").show();
	    $(".error").html("");
	  	var params = $("#generate_add_change_file").serialize();
	    $.ajax({
	      url: 'ajax_eligiblity_requests.php',
	      type: 'POST',
	      dataType: "json",
	      data: params,
	      success: function(res) {
	        $(".error").html("");
	          $("#ajax_loader").hide();
	        if (res.status == "success") {
	          window.parent.setNotifySuccess("Eligiblity File Scheduled Successfully");
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

	$(".datepicker_active2").datepicker({
          format: "mm/dd/yyyy",
      });
	$(".datepicker_active2").inputmask({"mask": "99/99/9999",'showMaskOnHover': false});
	$('.datepicker_addchange').datepicker({
	  format: "mm/dd/yyyy",
	}).on('changeDate', function (ev) {
		 var val= ev.dates;
		 var date = $(".datepicker_addchange").data('datepicker').getFormattedDate('mm/dd/yyyy');
		 $(".datepicker_active2").html(date);
		 $(".datepicker_active2").val(date);
	});
});
</script>