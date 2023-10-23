<?php if($report_row['report_key'] == 'eticket_overview') { ?>
	<h4 class="fs16 m-t-0 m-b-20">Filter Options</h4>
	<div class="row">
		<?=generateDateRange()?>
	</div>
	<script type="text/javascript">
		$(document).ready(function(){
            parent.$.colorbox.resize({
                height:475
            });
		    $(".date_picker").datepicker({
				changeDay: true,
				changeMonth: true,
				changeYear: true,
	            autoclose:true,
            });
		});
	</script>
<?php }else if($report_row['report_key'] == 'eticket_script') { ?>
	<h4 class="fs16 m-t-0 m-b-20">Filter Options</h4>
	<div class="row">
		<div class="col-xs-6">
			<div class="form-group">
				<select class="se_multiple_select" name="etickets[]" id="etickets" multiple="multiple">
					<?php if(!empty($fl_res_Eticket)){ ?>
						<?php foreach ($fl_res_Eticket as $key=> $row) { ?>
							<option value="<?= $row['id'] ?>" >
							<?= $row['tracking_id'] ?>    
							</option>
						<?php } ?>
					<?php } ?>
				</select>
				<label>E-Ticket</label>
				<p class="error"><span class="error_etickets"></span></p>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		$(document).ready(function(){
			parent.$.colorbox.resize({
                height:415
            });
			$("#etickets").multipleSelect({
				selectAll: false,
				width:'100%',
				filter:true
		  	});
		    $(".date_picker").datepicker({
				changeDay: true,
				changeMonth: true,
				changeYear: true,
	            autoclose:true,
            });
		});
	</script>
<?php } ?>