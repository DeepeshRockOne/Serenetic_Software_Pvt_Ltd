<div class="panel panel-default panel-block schedule_popup">
	<form name="frmSchedule" id="frmSchedule" action="" method="POST">
		<input type="hidden" name="file_id" value="<?=$file_id?>">
		<div class="panel-heading">
			<div class="panel-title">
				<h4 class="mn">Schedule - <span class="fw300"><?=$file_name?> File</span></h4>
			</div>
		</div>
		<div class="panel-body theme-form">
			<div class="fulfillment_schedule">
				<p class="fw500">Frequency</p>
				<div class="row theme-form">
					<div class="col-xs-6">
						<div class="form-group">
							<select class="form-control frequency" name="schedule_type">
								<option value=""></option>
								<option value="daily" data-text="Day(s)" <?=$schedule_type == 'daily' ?'selected=selected' : ''?>>Daily</option>
								<option value="weekly" data-text="Week(s)" <?=$schedule_type == 'weekly' ?'selected=selected' : ''?>>Weekly</option>
								<option value="monthly" data-text="Month(s)" <?=$schedule_type == 'monthly' ?'selected=selected' : ''?>>Monthly</option>
								<option value="yearly" data-text="Year(s)" <?=$schedule_type == 'yearly' ?'selected=selected' : ''?>>Yearly</option>
							</select>
							<label>Select Frequency</label>
						</div>
					</div>
					
					<div class="col-xs-6 frequency_div" style="display:<?=($schedule_type == '' || $schedule_type == 'daily' || $schedule_type == 'weekly')?"block":"none"?>">
						<div class="phone-control-wrap">
							<div class="phone-addon w-30">
								<div class="form-group p-t-7">
									Every
								</div>
							</div>
							<div class="phone-addon">
								<div class="form-group">
									<select class="form-control" name="schedule_frequency">
										<option value=""></option>
										<?php for($i=1;$i<=5;$i++){ ?>
		                                  <option value="<?=$i?>" <?=$schedule_frequency == $i ?'selected=selected' : ''?>><?=$i?></option>
		                                <?php } ?>
									</select>
									<label>Select</label>
								</div>
							</div>
							<div class="phone-addon w-30">
								<div class="form-group frequency_text p-t-7">
									<?=$schedule_type == 'weekly' ? 'Week(s)' : 'Day(s)';?>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="day_of_week div_weekDay m-b-25" style="display:<?=$schedule_type == 'weekly'?"block":"none"?>">
					<p>Day of Week</p>
					<div class="btn-group" data-toggle="buttons">
                      <label class='btn btn-info <?=(!empty($days_of_week_arr) && is_array($days_of_week_arr) && in_array("Mon",$days_of_week_arr))?"active":""?>'>
                        <input type="checkbox" name="days_of_week[]" class="js-switch" value="Mon" <?=(!empty($days_of_week_arr) && is_array($days_of_week_arr) && in_array("Mon",$days_of_week_arr))?"checked='checked'":""?>> MON
                      </label>
                      <label class='btn btn-info <?=(!empty($days_of_week_arr) && is_array($days_of_week_arr) && in_array("Tue",$days_of_week_arr))?"active":""?>'>
                        <input type="checkbox" name="days_of_week[]" class="js-switch" value="Tue" <?=(!empty($days_of_week_arr) && is_array($days_of_week_arr) && in_array("Tue",$days_of_week_arr))?"checked='checked'":""?>> TUES
                      </label>
                       <label class='btn btn-info <?=(!empty($days_of_week_arr) && is_array($days_of_week_arr) && in_array("Wed",$days_of_week_arr))?"active":""?>'>
                        <input type="checkbox" name="days_of_week[]" class="js-switch" value="Wed" <?=(!empty($days_of_week_arr) && is_array($days_of_week_arr) && in_array("Wed",$days_of_week_arr))?"checked='checked'":""?>> WED
                      </label>
                         <label class='btn btn-info <?=(!empty($days_of_week_arr) && is_array($days_of_week_arr) && in_array("Thu",$days_of_week_arr))?"active":""?>'>
                        <input type="checkbox" name="days_of_week[]" class="js-switch" value="Thu" <?=(!empty($days_of_week_arr) && is_array($days_of_week_arr) && in_array("Thu",$days_of_week_arr))?"checked='checked'":""?>> THURS
                      </label>
                       <label class='btn btn-info <?=(!empty($days_of_week_arr) && is_array($days_of_week_arr) && in_array("Fri",$days_of_week_arr))?"active":""?>'>
                        <input type="checkbox" name="days_of_week[]" class="js-switch" value="Fri" <?=(!empty($days_of_week_arr) && is_array($days_of_week_arr) && in_array("Fri",$days_of_week_arr))?"checked='checked'":""?>> FRI
                      </label>
                        <label class='btn btn-info <?=(!empty($days_of_week_arr) && is_array($days_of_week_arr) && in_array("Sat",$days_of_week_arr))?"active":""?>'>
                        <input type="checkbox" name="days_of_week[]" class="js-switch" value="Sat" <?=(!empty($days_of_week_arr) && is_array($days_of_week_arr) && in_array("Sat",$days_of_week_arr))?"checked='checked'":""?>> SAT
                      </label>
                       <label class='btn btn-info <?=(!empty($days_of_week_arr) && is_array($days_of_week_arr) && in_array("Sun",$days_of_week_arr))?"active":""?>'>
                        <input type="checkbox" name="days_of_week[]" class="js-switch" value="Sun" <?=(!empty($days_of_week_arr) && is_array($days_of_week_arr) && in_array("Sun",$days_of_week_arr))?"checked='checked'":""?>> SUN
                      </label>
                  </div>
                  <p class="error"><span id="error_days_of_week"></span></p>
				</div>
				<div class="row">

					<div class="div_monthly" style="display:<?=$schedule_type == 'monthly' || $schedule_type == 'yearly'?"block":"none"?>">
						<div class="col-xs-6">
							<p>
								 <label class="mn"><input type="radio" name="day_month" value="days_of_month" <?=$month_option == 'days_of_month' ? 'checked=checked' : ''?>>Day of Month</label>
							</p>
                                <p class="error"><span id="error_day_month"></span></p>
							    <div class="form-group">
					              <select class="se_multiple_select" name="days_of_month[]"  id="day_of_month" multiple="multiple" >
					                <?php 
                                      $i=1;
                                      for($i=1;$i<31;$i++){
                                    ?>
                                        <option value="<?=$i?>" <?=(!empty($days_of_month_arr) && in_array($i,$days_of_month_arr))?"selected='selected'":""?>><?=$i?></option>
                                    <?php 
                                      }
                                    ?>
					              </select>
					              <label>Select</label>
					            <p class="error"><span id="error_days_of_month"></span></p>
					            </div>
						</div>
						<div class="col-xs-6">
							<p>
								 <label class="mn"><input type="radio" name="day_month" value="on_the_day" <?=$month_option == 'on_the_day' ? 'checked=checked' : ''?>> On the</label>
							</p>
							<div class="row">
								<div class="col-xs-6">
									<select class="form-control" name="day_type">
									  <option value=""></option>
                                      <option value="first" <?=$day_type == 'first' ? 'selected="selected"' : ''?>>First</option>
                                      <option value="second" <?=$day_type == 'second' ? 'selected="selected"' : ''?>>Second</option>
                                      <option value="third" <?=$day_type == 'third' ? 'selected="selected"' : ''?>>Third</option>
                                      <option value="fourth" <?=$day_type == 'fourth' ? 'selected="selected"' : ''?>>Fourth</option>
                                      <option value="fifth" <?=$day_type == 'fifth' ? 'selected="selected"' : ''?>>Fifth</option>
                                      <option value="last" <?=$day_type == 'last' ? 'selected="selected"' : ''?>>Last</option>
                                    </select>
								</div>
								<div class="col-xs-6">
									<div class="form-group">
									<select class="form-control" name="selected_day" id="selected_day">
									  <option data-hidden="true"></option>
                                      <option value="monday" <?=$selected_day == 'monday' ? 'selected="selected"' : ''?>>Monday</option>
                                      <option value="tuesday" <?=$selected_day == 'tuesday' ? 'selected="selected"' : ''?>>Tuesday</option>
                                      <option value="wednesday" <?=$selected_day == 'wednesday' ? 'selected="selected"' : ''?>>Wednesday</option>
                                      <option value="thursday" <?=$selected_day == 'thursday' ? 'selected="selected"' : ''?>>Thursday</option>
                                      <option value="friday" <?=$selected_day == 'friday' ? 'selected="selected"' : ''?>>Friday</option>
                                      <option value="saturday" <?=$selected_day == 'saturday' ? 'selected="selected"' : ''?>>Saturday</option>
                                      <option value="day" <?=$selected_day == 'day' ? 'selected="selected"' : ''?>>day</option>
                                      <option value="weekday" <?=$selected_day == 'weekday' ? 'selected="selected"' : ''?>>weekday</option>
                                      <option value="weekend day" <?=$selected_day == 'weekend day' ? 'selected="selected"' : ''?>>weekend day</option>
                                    </select>
										<label>Select</label>
									<p class="error"><span id="error_selected_day"></span></p>
									</div>
								</div>
							
							</div>
						</div>
					</div>


					<div class="col-xs-12 div_yearly" style="display:<?=$schedule_type == 'yearly' ? "block":"none"?>">
							<p>Month</p>
							 <div class="form-group">
			              <select name="months[]" multiple="multiple" class="se_multiple_select" id="year_of_month">
	                            <option value='Jan' <?php echo (is_array($months_arr) && in_array('Jan', $months_arr)) ? 'selected=selected' : ''; ?>>January</option>
	                            <option value='Feb' <?php echo (is_array($months_arr) && in_array('Feb', $months_arr)) ? 'selected=selected' : ''; ?>>February</option>
	                            <option value='Mar' <?php echo (is_array($months_arr) && in_array('Mar', $months_arr)) ? 'selected=selected' : ''; ?>>March</option>
	                            <option value='Apr' <?php echo (is_array($months_arr) && in_array('Apr', $months_arr)) ? 'selected=selected' : ''; ?>>April</option>
	                            <option value='May' <?php echo (is_array($months_arr) && in_array('May', $months_arr)) ? 'selected=selected' : ''; ?>>May</option>
	                            <option value='Jun' <?php echo (is_array($months_arr) && in_array('Jun', $months_arr)) ? 'selected=selected' : ''; ?>>June</option>
	                            <option value='Jul' <?php echo (is_array($months_arr) && in_array('Jul', $months_arr)) ? 'selected=selected' : ''; ?>>July</option>
	                            <option value='Aug' <?php echo (is_array($months_arr) && in_array('Aug', $months_arr)) ? 'selected=selected' : ''; ?>>August</option>
	                            <option value='Sep' <?php echo (is_array($months_arr) && in_array('Sep', $months_arr)) ? 'selected=selected' : ''; ?>>September</option>
	                            <option value='Oct' <?php echo (is_array($months_arr) && in_array('Oct', $months_arr)) ? 'selected=selected' : ''; ?>>October</option>
	                            <option value='Nov' <?php echo (is_array($months_arr) && in_array('Nov', $months_arr)) ? 'selected=selected' : ''; ?>>November</option>
	                            <option value='Dec' <?php echo (is_array($months_arr) && in_array('Dec', $months_arr)) ? 'selected=selected' : ''; ?>>December</option>
	                       </select>
			              <label>Select</label>
			            </div>
			            <p class="error"><span id="error_months"></span></p>
				    </div>

				</div>
				<div class="bg_light_gray p-15 m-b-25">
					<div class="row">
						<div class="col-xs-6 br-r ">
							<p class="fw500">End Repeat</p>
							<div class="m-b-10">
								<label class="mn"><input type="radio" name="schedule_end_type" value="never" <?=$schedule_end_type == 'never' ? 'checked=checked' : ''?>> Never</label>
							</div>
							<div class="m-b-0 m-t-7">
								<label class="mn w-100">
									<div class="phone-control-wrap">
										<div class="phone-addon text-left">
											<input type="radio" name="schedule_end_type" value="no_of_times" <?=$schedule_end_type == 'no_of_times' ? 'checked=checked' : ''?>> After
										</div>
										<div class="phone-addon w-90 no_of_times" style="display: <?=$schedule_end_type == 'no_of_times' ? 'block' : 'none'; ?>">
											<div class="pr">
												<select class="form-control" name="schedule_no_of_times">
												<?php  
					                                $i = 1;
					                                for($i=1;$i<=5;$i++){
					                              ?>
					                               <option value="<?=$i?>" <?=$schedule_end_times == $i ? 'selected=selected' : ''?>><?=$i?></option>
					                              <?php    
					                                }
					                              ?>
												</select>
												<label>Select</label>
											</div>
										</div>
										<div class="phone-addon no_of_times" style="<?=$schedule_end_type == 'no_of_times' ? '' : 'display: none'; ?>">
											Times
										</div>
									</div>
								</label>
							</div>
							<div class="m-b-0 m-t-7">
								<label class="mn w-100">
									<div class="phone-control-wrap">
										<div class="phone-addon text-left text-nowrap">
											<input type="radio" name="schedule_end_type" value="on_date" <?=$schedule_end_type == 'on_date' ? 'checked=checked' : ''?>> On Date
										</div>
										<div class="phone-addon theme-form on_date" style="display: <?=$schedule_end_type == 'on_date' ? 'block' : 'none'; ?>">
											<div class="pr">
												<input type="text" class="form-control date_picker" name="schedule_on_date" value="<?=$schedule_end_date != '' ? $schedule_end_date : ''?>">
												<label>Select Date</label>
											</div>
										</div>
										
									</div>
								</label>
							</div>
							<p class="error"><span id="error_schedule_end_type"></span></p>
							<!-- <div class="">
								<label class="mn"><input type="radio" name="optradio">On Date</label>
								
							</div> -->
							<div class="visiable-xs"><div class="m-b-15"></div></div>
						</div>
						<div class="col-xs-6 p-l-15">
							<p class="fw500">Time</p>
							<div class="form-group">
								<select class="form-control" name="time">
									<option value=""></option>
	                                     <?php for ($i = 0; $i < 24; $i++) { ?>
	                                    <option value="<?php echo date('H:i:s', strtotime("$i:00")); ?>" <?= $time == date('H:i:s', strtotime("$i:00")) ? 'selected="selected"' : '' ?>><?php echo date('h:00 A', strtotime("$i:00")); ?></option>                 
	                                        <?php } ?>
								</select>
								<label>Time</label>
								<p class="error"><span id="error_time"></span></p>
							</div>
							<input type="hidden" name="timezone" value="CST">
							<!-- <div class="form-group  m-b-10">
								<select class="form-control" name="timezone">
									<option value=""></option>
									<option value="CST" <?=$timezone == 'CST' ? 'selected="selected"' : ''?>>CST</option>
									<option value="EST" <?=$timezone == 'EST' ? 'selected="selected"' : ''?>>EST</option>
									<option value="MST" <?=$timezone == 'MST' ? 'selected="selected"' : ''?>>MST</option>
									<option value="PST" <?=$timezone == 'PST' ? 'selected="selected"' : ''?>>PST</option>
								</select>
								<label>Time Zone</label>
								<p class="error"><span id="error_timezone"></span></p>
							</div> -->
						</div>
					</div>
				</div>
				<!-- <div class="theme-form">
				<p class="fw500">File Type</p>
				<div class="m-b-25">
					<div class="btn-group" data-toggle="buttons">
                        <label class='btn btn-info <?=$file_type == "full_file" ? "active":""?>'>
                        <input type="radio" class="file_type js-switch" name="file_type" value="full_file" <?=$file_type == 'full_file' ? 'checked=checked' : ''?>> Full File
                      </label>
                        <label class='btn btn-info <?=$file_type == "schedule_change_file" ? "active":""?>'>
                        <input type="radio" class="file_type js-switch" name="file_type" value="add_change_file" <?=$file_type == 'add_change_file' ? 'checked=checked' : ''?>> Add/Change/Delete File
                      </label>
                      <p class="error"><span id="error_file_type"></span></p>
                  </div>
				</div> -->
				<div class="form-group ">
					<select class="form-control" name="generate_via" id="generate_via1">
						<option value=""></option>
	                    <option value="Download" <?=$generate_via == 'Download' ? 'selected="selected"' : ''?>>Download</option>
	                    <option value="Email" <?=$generate_via == 'Email' ? 'selected="selected"' : ''?>>Email</option>
	                    <option value="FTP" <?=$generate_via == 'FTP' ? 'selected="selected"' : ''?>>FTP</option>
	                  </select>
					<label>Generate Via</label>
					<p class="error"><span id="error_generate_via"></span></p>
				</div>
				<div class="row" id="email_div1" style="display: <?=$generate_via == 'Email' ? 'block' : 'none'?>;">
					<div class="col-sm-12">
						<div class="form-group">
							<input type="text" name="email" value="<?=$email?>" class="form-control no_space">
							<label>Email</label>
						</div>
						<p class="error"><span id="error_email"></span></p>
				    </div>
				    <div class="col-sm-12">
				    	<div class="form-group">
							<input type="password" name="password" value="<?=$password?>" class="form-control">
							<label>Password</label>
						</div>
						<p class="error"><span id="error_password"></span></p>
				    </div>
				</div>
				<div class="row" id="ftp_div1" style="display: <?=$generate_via == 'FTP' ? 'block' : 'none'?>;">
					<div class="col-sm-12">
						<div class="form-group">
							<select class="form-control" name="ftp_name">
								<option value=""></option>
								<option value="system_ftp">System FTP</option>
							</select>
							<label>Destination</label>
						</div>
						<p class="error"><span id="error_ftp_name"></span></p>
					</div>
				</div>
			</div>
			<div class="m-b-15">
				<label class="label-input" style="margin: 0px auto;">
					<input type="checkbox" class="form-control" name="cancel_processing" id="cancel_processing" value="Y">Stop scheduler from future processing
				</label>
			</div>
			<div class="text-center">
				<a href="javascript:void(0);" id="schedule" class="btn btn-action"><?=$button_text?></a>
				<a href="javascript:void(0);" onclick="parent.$.colorbox.close();" class="btn red-link">Cancel</a>
			</div>
			</div>
			
		</div>
	</form>
</div>
<script type="text/javascript">
	$(document).ready(function() {
	  checkEmail();
	  common_select();
	  $(".date_picker").datepicker({
	    orientation: 'top'
	  });

	  $("#day_of_month").multipleSelect({
       filter: false
  	 });
	  $("#year_of_month").multipleSelect({
  	 });

	$('#cancel_processing').on('click',function(){
		if($(this).is(':checked')){
	    $('.error span').html('');						
			$('option').attr('selected', false);
			$('select.form-control').selectpicker('refresh');			
			$('input:radio').attr("checked",false);
			$('input:text').val('');
			$('input:password').val('');
			$('select.se_multiple_select').multipleSelect("uncheckAll");
			$('select.se_multiple_select').multipleSelect("refresh");
			$.uniform.update();
			fRefresh();
			$('.on_date').hide();
			$('.no_of_times').hide();
			$('.div_weekDay').hide();
			$('.div_monthly').hide();
			$('.div_yearly').hide();
			$("#email_div1").fadeOut().hide();
	    $("#ftp_div1").fadeOut().hide();
		}
	}); 

	 $('input[name="schedule_end_type"]').on('click', function(e) {
	    if($(this).val() == 'no_of_times'){
	    	$('.no_of_times').show('slow');
	    	$('.on_date').hide('slow');
	    }else if($(this).val() == 'on_date'){
	    	$('.on_date').show('slow');
	    	$('.no_of_times').hide('slow');
	    }else if($(this).val() == 'never'){
	    	$('.on_date').hide('slow');
	    	$('.no_of_times').hide('slow');
	    }
	}); 

	  $(document).on("change",".frequency",function(){
	      var frequency = $(this).val();
	      var frequency_title = $(this).find(':selected').attr("data-text")
	      $(".frequency_text").html(frequency_title);
	      if(frequency == "daily"){
	         $(".frequency_div").show().fadeIn();
	        $(".div_weekDay").hide().fadeOut();
	         $(".div_monthly").hide().fadeOut();
	        $(".div_yearly").hide().fadeOut();
	      }else if(frequency == "weekly"){
	        $(".div_weekDay").show().fadeIn();
	        $(".frequency_div").show().fadeIn();
	        $(".div_yearly").hide().fadeOut();
	        $(".div_monthly").hide().fadeOut();
	      }else if(frequency == "monthly"){
	        $(".div_weekDay").hide().fadeOut();
	        $(".div_yearly").hide().fadeOut();
	        $(".div_monthly").show().fadeIn();
	        $(".frequency_div").hide().fadeOut();
	      }else if(frequency == "yearly"){
	        $(".div_weekDay").hide().fadeOut();
	        $(".div_monthly").show().fadeIn();
	        $(".div_yearly").show().fadeIn();
	        $(".frequency_div").hide().fadeOut();
	      }
	  });

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

	  $(document).on("click","#schedule",function(){
	    fulfillment_schedule($(this));
	  });

	});

function fulfillment_schedule(btn_obj) {
    btn_obj.prop('disabled',true);
    $("#ajax_loader").show();
    $('.error span').html('');

    $.ajax({
        url: '<?= $HOST ?>/admin/ajax_fulfillment_schedule.php',
        data: $("#frmSchedule").serialize(),
        type: 'POST',
        dataType: "json",
        success: function(res) {
            btn_obj.prop('disabled',false);
            $("#ajax_loader").hide();
            if (res.status == "success") {
                window.parent.setNotifySuccess("Fulfillment File Scheduled Successfully");
				setTimeout(function(){
					window.parent.location.reload();
				}, 1000);
               
            } else if(res.status=="error") {
                var is_error = true;
                $.each(res.errors, function (index, value) {
                    $('#error_' + index).html(value).show();
                });
            
            }
        }
    });
} 
</script>