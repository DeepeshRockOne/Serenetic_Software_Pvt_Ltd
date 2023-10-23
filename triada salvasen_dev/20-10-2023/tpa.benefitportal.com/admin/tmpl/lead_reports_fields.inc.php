<?php if($report_row['report_key'] == 'lead_summary') { ?>
	<h4 class="fs16 m-t-0 m-b-20">Filter Options</h4>
	<div class="row">
		<?=generateDateRange()?>
        <div class="col-xs-6">
          	<div class="form-group">
              	<select class="se_multiple_select" name="agent_ids[]" id="agent_ids" data-live-search="true" multiple>
					<?php if(!empty($fl_res_agent)){ ?>
						<?php foreach ($fl_res_agent as $key=> $row) { ?>
							<option value="<?= $row['id'] ?>" >
							<?= $row['rep_id'] .' - '.$row['fname'].' '.$row['lname']?>    
							</option>
						<?php } ?>
					<?php } ?>
          		</select>
              	<label>Agent</label>
              	<p class="error"><span class="error_agent_ids"></span></p>
          	</div>
        </div>
         <div class="col-xs-6">
          	<div class="form-group">
              	<select class="se_multiple_select" name="group_ids[]" id="group_ids" data-live-search="true" multiple>
					<?php if(!empty($fl_res_group)){ ?>
						<?php foreach ($fl_res_group as $key=> $row) { ?>
							<option value="<?= $row['id'] ?>" >
							<?= $row['rep_id'] .' - '.$row['fname'].' '.$row['lname']?>    
							</option>
						<?php } ?>
					<?php } ?>
          		</select>
              	<label>Group</label>
              	<p class="error"><span class="error_group_ids"></span></p>
          	</div>
        </div>
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
            $("#agent_ids,#group_ids").multipleSelect({
	      		selectAll: true,
				width:'100%',
				filter:true
		  	});
		});
	</script>
<?php } ?>