<?php if($report_row['report_key'] == 'agent_export') { ?>
	<h4 class="fs16 m-t-0 m-b-20">Filter Options</h4>
	<div class="row">
	    <div class="col-xs-6">
          	<div class="form-group">
					<select class="se_multiple_select" name="display_id[]" id="display_id" multiple="multiple">
						<?php if(!empty($fl_res)){ ?>
							<?php foreach ($fl_res as $key=> $category) { ?>
								<?php if(!empty($category['id'])){ ?>
									<option value="<?= $category['id'] ?>"><?= $category['rep_id'] .' - '.$category['fname'].' '.$category['lname']?></option>
								<?php } else { ?>
									<?php if(!empty($category)){ ?>
										<optgroup label='<?= ucfirst($key) ?>'>
											<?php foreach ($category as $pkey => $row) { ?>
												<option value="<?= $row['id'] ?>">
												<?= $row['rep_id'] .' - '.$row['fname'].' '.$row['lname']?>    
												</option>
											<?php } ?>
										</optgroup>
									<?php } ?>
								<?php } ?>
							<?php } ?>										
						<?php } ?>
					</select>
				  <label>Agents</label>
              	<p class="error"><span class="error_display_id"></span></p>
          	</div>
        </div>
        <div class="col-xs-6 ">
          	<div class="form-group">
              	<select name="report_type" id="report_type" class="form-control">
					<option value="" hidden="true" selected></option>
					<option value="masked" selected="selected">Masked</option>
					<option value="unmasked">Unmasked</option>
              	</select>
              	<label>Report Type</label>
          	</div>
        </div>
        <div class="col-xs-12">
        	<select id='pre-selected-options' multiple='multiple'>
        		<?php if(!empty($fields)){ ?>
	              <?php foreach ($fields as $column_key=> $field) { ?>
	              		<option value='<?= $column_key ?>' <?=in_array($column_key,$selected_columns)?'selected="selected"':'' ?> <?= in_array($column_key,$selected_columns) && $field['is_default'] == "Y"?"class='disabled'":"";?>><?= $column_key ?></option>
			    	<?php } ?>
                <?php } ?>
		  	</select>
        </div>
        <div class="col-xs-12">
        	<br/>
        	<label for="columns_save_as_defualt"><input type="checkbox" name="columns_save_as_defualt" id="columns_save_as_defualt" value="1" <?=!empty($setting_selected_columns)?"checked":""?>> Save selected columns as default</label>
        </div>
	</div>
	<script type="text/javascript">
		$(document).ready(function(){
			parent.$.colorbox.resize({
                height:490
            });
			$("#display_id").multipleSelect({
				width:'100%'
		  	});
		});
	</script>
<?php } elseif($report_row['report_key'] == 'agent_history') { ?>
	<h4 class="fs16 m-t-0 m-b-20">Filter Options</h4>
	<div class="row">
		<div class="col-xs-6">
          	<div class="form-group" >
              	<select class="form-control" name="agent_id" id="agent_id" data-live-search="true">
          			<option data-hidden="true"></option>
                  	<?php if(!empty($fl_res)){
                      	foreach($fl_res as $row){ ?>
                          <option value="<?=$row['id']?>"><?=$row['rep_id'].' - '.$row['fname'].' '.$row['lname']?></option>
                      <?php } ?>
                  	<?php } ?>
          		</select>
              	<label>Agent</label>
              	<p class="error"><span class="error_agent_id"></span></p>
          	</div>
        </div>
        <div class="clearfix"></div>
	    <?=generateDateRange()?>
	</div>
	<script type="text/javascript">
		$(document).ready(function(){
		    $(".date_picker").datepicker({
				changeDay: true,
				changeMonth: true,
				changeYear: true,
	            autoclose:true,
			});
		});
	</script>
<?php } elseif($report_row['report_key'] == 'agent_license') { ?>
	<h4 class="fs16 m-t-0 m-b-20">Filter Options</h4>
	<div class="row">
		<div class="col-xs-6">
          	<!-- <div class="form-group">
              	<select class="se_multiple_select" name="agent_ids[]" id="agent_ids" multiple>
					<?php if(!empty($fl_res)){ ?>
						<?php foreach ($fl_res as $key=> $row) { ?>
							<option value="<?= $row['id'] ?>" >
							<?= $row['rep_id'] .' - '.$row['fname'].' '.$row['lname']?>    
							</option>
						<?php } ?>
					<?php } ?>
          		</select>
              	<label>Agent</label>
              	<p class="error"><span class="error_agent_id"></span></p>
			  </div> -->
			  <div class="form-group">
					<select class="se_multiple_select" name="agent_ids[]" id="agent_ids" multiple="multiple">
						<?php if(!empty($fl_res)){ ?>
							<?php foreach ($fl_res as $key=> $category) { ?>
								<?php if(!empty($category['id'])){ ?>
									<option value="<?= $category['id'] ?>"><?= $category['rep_id'] .' - '.$category['fname'].' '.$category['lname']?></option>
								<?php } else { ?>
									<?php if(!empty($category)){ ?>
										<optgroup label='<?= ucfirst($key) ?>'>
											<?php foreach ($category as $pkey => $row) { ?>
												<option value="<?= $row['id'] ?>">
												<?= $row['rep_id'] .' - '.$row['fname'].' '.$row['lname']?>    
												</option>
											<?php } ?>
										</optgroup>
									<?php } ?>
								<?php } ?>
							<?php } ?>										
						<?php } ?>
					</select>
				  <label>Agents</label>
              	<p class="error"><span class="error_display_id"></span></p>
          	</div>
        </div>
        <div class="clearfix"></div>
		<?=generateDateRange()?>
		<div class="col-xs-6">
            <div class="form-group">
                <select name="license_state[]" id="license_state"  class="se_multiple_select" multiple="multiple">
                    <?php if ($allStateRes) {?>
                    <?php foreach ($allStateRes as $state) { ?>
                    <option <?=$state["name"]?> value="<?=$state["name"];?>"><?php echo $state['name']; ?></option>
                    <?php } } ?>
                </select>
                <label>License state</label>
                <p class="error"><span class="error_license_state"></span></p>
            </div>
		</div>
	</div>
	<script type="text/javascript">
		$(document).ready(function(){
		    $(".date_picker").datepicker({
				changeDay: true,
				changeMonth: true,
				changeYear: true,
	            autoclose:true,
			});
			$("#agent_ids,#license_state").multipleSelect({
	      		selectAll: true,
				width:'100%',
				filter:true
		  	});
		});
	</script>
<?php } elseif($report_row['report_key'] == 'agent_merchant_assignment') { ?>
	<h4 class="fs16 m-t-0 m-b-20">Filter Options</h4>
	<div class="row">
		<div class="col-xs-6">
          	<div class="form-group">
              	<select class="se_multiple_select" name="agent_ids[]" id="agent_ids" data-live-search="true" multiple>
					<?php if(!empty($fl_res)){ ?>
						<?php foreach ($fl_res as $key=> $row) { ?>
							<option value="<?= $row['id'] ?>" >
							<?= $row['rep_id'] .' - '.$row['fname'].' '.$row['lname']?>    
							</option>
						<?php } ?>
					<?php } ?>
          		</select>
              	<label>Agent</label>
              	<p class="error"><span class="error_agent_id"></span></p>
          	</div>
        </div>
        <div class="clearfix"></div>
		<?=generateDateRange()?>
		<div class="col-xs-6">
            <div class="form-group">
                <select class="se_multiple_select" name="merchant_processor[]" id="merchant_processor"  multiple >
                    <?php if ($fl_merchant_res) {?>
                    <?php foreach ($fl_merchant_res as $processor) { ?>
                    <option value="<?=$processor["id"];?>"><?php echo $processor['name']; ?></option>
                    <?php } } ?>
                </select>
                <label>Merchant Processor</label>
                <p class="error"><span class="error_merchant_processor"></span></p>
            </div>
		</div>
	</div>
	<script type="text/javascript">
		$(document).ready(function(){
		    $(".date_picker").datepicker({
				changeDay: true,
				changeMonth: true,
				changeYear: true,
	            autoclose:true,
			});
			$("#merchant_processor,#agent_ids").multipleSelect({
	      		selectAll: true,
				width:'100%',
				filter:true
		  	});
		});
	</script>
<?php } elseif($report_row['report_key'] == 'agent_eo_coverage') { ?>
	<h4 class="fs16 m-t-0 m-b-20">Filter Options</h4>
	<div class="row">
		<div class="col-xs-6">
          	<div class="form-group">
              	<select class="se_multiple_select" name="agent_ids[]" id="agent_ids" data-live-search="true" multiple>
					<?php if(!empty($fl_res)){ ?>
						<?php foreach ($fl_res as $key=> $row) { ?>
							<option value="<?= $row['id'] ?>" >
							<?= $row['rep_id'] .' - '.$row['fname'].' '.$row['lname']?>    
							</option>
						<?php } ?>
					<?php } ?>
          		</select>
              	<label>Agent</label>
              	<p class="error"><span class="error_agent_id"></span></p>
          	</div>
        </div>
        <div class="clearfix"></div>
	</div>
	<script type="text/javascript">
		$(document).ready(function(){
			$("#agent_ids").multipleSelect({
	      		selectAll: true,
				width:'100%',
				filter:true
		  	});
		});
	</script>
<?php } elseif($report_row['report_key'] == 'agent_interactions') { ?>
	<input type="hidden" name="user_type" value="agent">
	<h4 class="fs16 m-t-0 m-b-20">Filter Options</h4>
	<div class="row">
		<div class="col-xs-6">
          	<div class="form-group">
              	<select class="se_multiple_select" name="user_ids[]" id="user_ids" data-live-search="true" multiple>
					<?php if(!empty($fl_res)){ ?>
						<?php foreach ($fl_res as $key=> $row) { ?>
							<option value="<?= $row['id'] ?>" >
							<?= $row['rep_id'] .' - '.$row['fname'].' '.$row['lname']?>    
							</option>
						<?php } ?>
					<?php } ?>
          		</select>
              	<label>Agent</label>
              	<p class="error"><span class="error_user_id"></span></p>
          	</div>
        </div>
        <div class="clearfix"></div>
		<?=generateDateRange()?>
		<div class="col-xs-6">
            <div class="form-group">
                <select class="se_multiple_select" name="interactions[]" id="interactions"  multiple >
                    <?php if ($fl_interaction_res) {?>
                    <?php foreach ($fl_interaction_res as $interaction) { ?>
                    <option value="<?=$interaction["id"];?>"><?php echo $interaction['type']; ?></option>
                    <?php } } ?>
                </select>
                <label>Agent Interactions</label>
                <p class="error"><span class="error_interactions"></span></p>
            </div>
		</div>
	</div>
	<script type="text/javascript">
		$(document).ready(function(){
			parent.$.colorbox.resize({
		        height:475
		    });
			$("#user_ids,#interactions").multipleSelect({
	      		selectAll: true,
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