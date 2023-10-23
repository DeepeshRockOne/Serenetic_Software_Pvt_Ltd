<?php if(in_array($report_row['report_key'],array('member_verifications','member_paid_through'))) { ?>
	<h4 class="fs16 m-t-0 m-b-20">Filter Options</h4>
	<div class="row">
		<?=generateDateRange()?>
		<div class="col-xs-6">
          	<div class="form-group ">
					<select class="se_multiple_select" name="tree_agent_ids[]" id="tree_agent_ids" multiple="multiple">
						<?php if(!empty($fl_res_agent)){ ?>
							<?php foreach ($fl_res_agent as $key=> $row) { ?>
								<option value="<?= $row['id'] ?>">
								<?= $row['rep_id'] .' - '.$row['fname'].' '.$row['lname']?>    
								</option>
							<?php } ?>
						<?php } ?>
					</select>
				  <label>Tree Agent</label>
				</div>
              	<p class="error"><span class="error_tree_agent_ids"></span></p>
		</div>
		<div class="col-xs-6 ">
          	<div class="form-group">
              	<select class="se_multiple_select" name="product_ids[]"  id="product_ids" multiple="multiple" >
                  	<?php if(!empty($fl_productRes)){ ?>
                    <?php foreach ($fl_productRes as $key=> $category) { ?>
                  		<?php if(!empty($category)){ ?>
	                  		<optgroup label='<?= $key ?>'>
		                    <?php foreach ($category as $pkey => $row) { ?>
	                      	<option value="<?= $row['id'] ?>"><?= $row['product_code'].' - '.$row['name']?></option>
		                    <?php } ?>
	                  		</optgroup>
                    	<?php } ?>
                 	<?php } ?>
		            <?php } ?>
              	</select>
              	<label>Products</label>
          	</div>
        </div>
		<?php if($report_row['report_key'] == 'member_verifications') { ?>
        <div class="col-xs-6 ">
          	<div class="form-group">
          		<!-- <div class="group_select"> -->
              	<select class="form-control" name="verification_method"  id="verification_method">
                    <option value=""></option>
                    <option value="eSign">E-Sign</option>
                    <option value="email_sms_verification">Email/SMS</option>
                    <option value="voice_verification">Voice Verification</option>
                    <option value="upload_document">Upload</option>
              	</select>
              	<label>Verification Method</label>
              	<!-- </div> -->
          	</div>
        </div>
		<?php }else if($report_row['report_key'] == 'member_paid_through') { ?>
			<?=generateDateRange('Last Payment Date','lpd')?>
		<?php } ?>
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
			$("#tree_agent_ids,#product_ids").multipleSelect({
				width:'100%',
				filter:true
			});
		});

		$(document).off('change', '#lpd_join_range');
		$(document).on('change', '#lpd_join_range', function(e) {
			e.preventDefault();
			if($(this).val() == ''){			        
				$('#lpd_range_join').hide();
			} else {
				$("#lpd_date_range").removeClass('col-xs-8').addClass('col-xs-4');
				if ($(this).val() == 'Range') {
					$('#lpd_range_join').show();
					$('#lpd_all_join').hide();
				} else {
					$('#lpd_range_join').hide();
					$('#lpd_all_join').show();
				}
			}
		});
	</script>
<?php }else if($report_row['report_key'] == 'member_age_out') { ?>
	<h4 class="fs16 m-t-0 m-b-20">Filter Options</h4>
	<div class="row">
		<?=generateDateRange()?>
		<div class="col-xs-6">
          	<div class="form-group ">
					<select class="se_multiple_select" name="tree_agent_ids[]" id="tree_agent_ids" multiple="multiple">
						<?php if(!empty($fl_res_agent)){ ?>
							<?php foreach ($fl_res_agent as $key=> $row) { ?>
								<option value="<?= $row['id'] ?>">
								<?= $row['rep_id'] .' - '.$row['fname'].' '.$row['lname']?>    
								</option>
							<?php } ?>
						<?php } ?>
					</select>
				  <label>Tree Agent</label>
              	<p class="error"><span class="error_tree_agent_ids"></span></p>
          	</div>
		</div>
		<div class="col-xs-6 ">
          	<div class="form-group">
              	<select class="se_multiple_select" name="product_ids[]"  id="product_ids" multiple="multiple" >
                  	<?php if(!empty($fl_productRes)){ ?>
                    <?php foreach ($fl_productRes as $key=> $category) { ?>
                  		<?php if(!empty($category)){ ?>
	                  		<optgroup label='<?= $key ?>'>
		                    <?php foreach ($category as $pkey => $row) { ?>
	                      	<option value="<?= $row['id'] ?>"><?= $row['product_code'].' - '.$row['name']?></option>
		                    <?php } ?>
	                  		</optgroup>
                    	<?php } ?>
                 	<?php } ?>
		            <?php } ?>
              	</select>
              	<label>Products</label>
          	</div>
        </div>
        <div class="col-xs-6 ">
          	<div class="form-group">
          		<!-- <div class="group_select"> -->
              	<select class="form-control" name="policy_status"  id="policy_status">
                    <option value=""></option>
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                    <option value="Both">Active & Inactive</option>
              	</select>
              	<label>Plan Method</label>
              	<!-- </div> -->
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
			$("#tree_agent_ids,#product_ids").multipleSelect({
				width:'100%',
				filter:true
			});
		});
	</script>
<?php } else if($report_row['report_key'] == 'member_summary') { ?>
	<h4 class="fs16 m-t-0 m-b-20">Filter Options</h4>
	<div class="row">
		<?=generateDateRange()?>
		<div class="col-xs-6">
          	<div class="form-group ">
					<select class="se_multiple_select" name="tree_agent_ids[]" id="tree_agent_ids" multiple="multiple">
						<?php if(!empty($fl_res_agent)){ ?>
							<?php foreach ($fl_res_agent as $key=> $row) { ?>
								<option value="<?= $row['id'] ?>">
								<?= $row['rep_id'] .' - '.$row['fname'].' '.$row['lname']?>    
								</option>
							<?php } ?>
						<?php } ?>
					</select>
				  <label>Tree Agent</label>
              	<p class="error"><span class="error_tree_agent_ids"></span></p>
          	</div>
		</div>
		<div class="col-xs-6">
          	<div class="form-group ">
					<select class="se_multiple_select" name="enroll_agent_ids[]" id="enroll_agent_ids" multiple="multiple">
						<?php if(!empty($fl_res_agent)){ ?>
							<?php foreach ($fl_res_agent as $key=> $row) { ?>
								<option value="<?= $row['id'] ?>">
								<?= $row['rep_id'] .' - '.$row['fname'].' '.$row['lname']?>    
								</option>
							<?php } ?>
						<?php } ?>
					</select>
				  <label>Enrolling Agent</label>
              	<p class="error"><span class="error_enroll_agent_ids"></span></p>
          	</div>
		</div>
		<div class="col-xs-6 ">
          	<div class="form-group">
              	<select class="se_multiple_select" name="product_ids[]"  id="product_ids" multiple="multiple" >
                  	<?php if(!empty($fl_productRes)){ ?>
                    <?php foreach ($fl_productRes as $key=> $category) { ?>
                  		<?php if(!empty($category)){ ?>
	                  		<optgroup label='<?= $key ?>'>
		                    <?php foreach ($category as $pkey => $row) { ?>
	                      	<option value="<?= $row['id'] ?>"><?= $row['product_code'].' - '.$row['name']?></option>
		                    <?php } ?>
	                  		</optgroup>
                    	<?php } ?>
                 	<?php } ?>
		            <?php } ?>
              	</select>
              	<label>Products</label>
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
		        height:615
		    });
			$(".date_picker").datepicker({
	            changeDay: true,
	            changeMonth: true,
	            changeYear: true,
	            autoclose:true,
			});
			$("#tree_agent_ids,#enroll_agent_ids,#product_ids").multipleSelect({
				width:'100%',
				filter:true
			});
		});
	</script>
<?php } elseif($report_row['report_key'] == 'member_history') { ?>
	<h4 class="fs16 m-t-0 m-b-20">Filter Options</h4>
	<div class="row">
		<div class="col-xs-6">
			<div class="form-group height_auto" >
              	<input type="text" class="listing_search" name="member_id" id="member_id">
              	<label>Member</label>
              	<p class="error"><span class="error_member_id"></span></p>
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
			initSelectize('member_id','MemberID',<?php echo $_BOOTSTRAP_TAGS_MIN_LENGTH; ?>);
		});
	</script>
<?php } elseif($report_row['report_key'] == 'member_interactions') { ?>
	<input type="hidden" name="user_type" value="customer">
	<h4 class="fs16 m-t-0 m-b-20">Filter Options</h4>
	<div class="row">
		<div class="col-xs-6">
			<div class="form-group height_auto">
			  <input type="text" name="user_ids" id="rep_id" class="listing_search">
			  <label>Member</label>
              <p class="error"><span class="error_user_id"></span></p>
          	</div>
        </div>
        <div class="clearfix"></div>
		<?=generateDateRange()?>
		<div class="col-xs-6">
            <div class="form-group ">
                <select class="se_multiple_select" name="interactions[]" id="interactions"  multiple >
                    <?php if ($fl_interaction_res) {?>
                    <?php foreach ($fl_interaction_res as $interaction) { ?>
                    <option value="<?=$interaction["id"];?>"><?php echo $interaction['type']; ?></option>
                    <?php } } ?>
                </select>
                <label>Member Interactions</label>
                <p class="error"><span class="error_interactions"></span></p>
            </div>
		</div>
	</div>
	<script type="text/javascript">
		$(document).ready(function(){
			parent.$.colorbox.resize({
		        height:475
		    });
			initSelectize('rep_id','MemberIDRep',<?php echo $_BOOTSTRAP_TAGS_MIN_LENGTH; ?>);
			$("#interactions").multipleSelect({
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
<?php }else if($report_row['report_key'] == 'member_product_cancellations') { ?>
	<h4 class="fs16 m-t-0 m-b-20">Filter Options</h4>
	<div class="row">
		<div class="col-xs-6">
			<label>
				<input type="radio" name="termination_or_terminated_date" value="termination_date"> Termination Date
			</label>
			<label class="m-l-10">
				<input type="radio" name="termination_or_terminated_date" value="date_terminated"> Date terminated
			</label>
			<p class="error text-left"><span class="error_termination_or_terminated_date"></span></p>
		</div>
		<br/>
		<div class="clearfix m-t-40"></div>

		<?=generateDateRange('Date')?>

		<div class="col-xs-6">
          	<div class="form-group ">
					<select class="se_multiple_select" name="tree_agent_ids[]" id="tree_agent_ids" multiple="multiple">
						<?php if(!empty($fl_res_agent)){ ?>
							<?php foreach ($fl_res_agent as $key=> $row) { ?>
								<option value="<?= $row['id'] ?>">
								<?= $row['rep_id'] .' - '.$row['fname'].' '.$row['lname']?>    
								</option>
							<?php } ?>
						<?php } ?>
					</select>
				  <label>Tree Agent</label>
              	<p class="error"><span class="error_tree_agent_ids"></span></p>
          	</div>
		</div>
		<div class="col-xs-6">
          	<div class="form-group ">
					<select class="se_multiple_select" name="enroll_agent_ids[]" id="enroll_agent_ids" multiple="multiple">
						<?php if(!empty($fl_res_agent)){ ?>
							<?php foreach ($fl_res_agent as $key=> $row) { ?>
								<option value="<?= $row['id'] ?>">
								<?= $row['rep_id'] .' - '.$row['fname'].' '.$row['lname']?>    
								</option>
							<?php } ?>
						<?php } ?>
					</select>
				  <label>Enrolling Agent</label>
              	<p class="error"><span class="error_enroll_agent_ids"></span></p>
          	</div>
		</div>
		<div class="col-xs-6 ">
          	<div class="form-group">
              	<select class="se_multiple_select" name="product_ids[]"  id="product_ids" multiple="multiple" >
                  	<?php if(!empty($fl_productRes)){ ?>
                    <?php foreach ($fl_productRes as $key=> $category) { ?>
                  		<?php if(!empty($category)){ ?>
	                  		<optgroup label='<?= $key ?>'>
		                    <?php foreach ($category as $pkey => $row) { ?>
	                      	<option value="<?= $row['id'] ?>"><?= $row['product_code'].' - '.$row['name']?></option>
		                    <?php } ?>
	                  		</optgroup>
                    	<?php } ?>
                 	<?php } ?>
		            <?php } ?>
              	</select>
              	<label>Products</label>
          	</div>
        </div>
	</div>
	<script type="text/javascript">
		$(document).ready(function(){
			parent.$.colorbox.resize({
		        height:530
		    });
			$(".date_picker").datepicker({
	            changeDay: true,
	            changeMonth: true,
	            changeYear: true,
	            autoclose:true,
			});
			$("#tree_agent_ids,#product_ids,#enroll_agent_ids").multipleSelect({
				width:'100%',
				filter:true
			});
		});
	</script>
<?php }else if($report_row['report_key'] == 'admin_member_persistency') { ?>
	<h4 class="fs16 m-t-0 m-b-20">Filter Options</h4>
	<div class="row">
		<div class="col-xs-6">
			<label>
				<input type="radio" name="added_or_effective_date" value="added_date"> Added Date
			</label>
			<label class="m-l-10">
				<input type="radio" name="added_or_effective_date" value="effective_date"> Effective Date
			</label>
			<p class="error text-left"><span class="error_added_or_effective_date"></span></p>
		</div>
		<br/>
		<div class="clearfix m-t-40"></div>
	    <?=generateDateRange('Date')?>

	    <div class="col-xs-6">
          	<div class="form-group ">
					<select class="se_multiple_select" name="tree_agent_ids[]" id="tree_agent_ids" multiple="multiple">
						<?php if(!empty($fl_res_agent)){ ?>
							<?php foreach ($fl_res_agent as $key=> $row) { ?>
								<option value="<?= $row['id'] ?>">
								<?= $row['rep_id'] .' - '.$row['fname'].' '.$row['lname']?>    
								</option>
							<?php } ?>
						<?php } ?>
					</select>
				  <label>Tree Agent</label>
              	<p class="error"><span class="error_tree_agent_ids"></span></p>
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
			$("#tree_agent_ids").multipleSelect({
				width:'100%',
				filter:true
			});
		});
	</script>
<?php }else if($report_row['report_key'] == 'life_insurance_beneficiaries') { ?>
	<h4 class="fs16 m-t-0 m-b-20">Filter Options</h4>
	<div class="row">
		<?=generateDateRange()?>
		<div class="col-xs-6">
          	<div class="form-group ">
					<select class="se_multiple_select" name="tree_agent_ids[]" id="tree_agent_ids" multiple="multiple">
						<?php if(!empty($fl_res_agent)){ ?>
							<?php foreach ($fl_res_agent as $key=> $row) { ?>
								<option value="<?= $row['id'] ?>">
								<?= $row['rep_id'] .' - '.$row['fname'].' '.$row['lname']?>    
								</option>
							<?php } ?>
						<?php } ?>
					</select>
				  <label>Tree Agent</label>
              	<p class="error"><span class="error_tree_agent_ids"></span></p>
          	</div>
		</div>
		<div class="col-xs-6 ">
          	<div class="form-group">
              	<select class="se_multiple_select" name="product_ids[]"  id="product_ids" multiple="multiple" >
                  	<?php if(!empty($fl_productRes)){ ?>
                    <?php foreach ($fl_productRes as $key=> $category) { ?>
                  		<?php if(!empty($category)){ ?>
	                  		<optgroup label='<?= $key ?>'>
		                    <?php foreach ($category as $pkey => $row) { ?>
	                      	<option value="<?= $row['id'] ?>"><?= $row['product_code'].' - '.$row['name']?></option>
		                    <?php } ?>
	                  		</optgroup>
                    	<?php } ?>
                 	<?php } ?>
		            <?php } ?>
              	</select>
              	<label>Products</label>
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
			$("#tree_agent_ids,#product_ids").multipleSelect({
				width:'100%',
				filter:true
			});
		});
	</script>
<?php } ?>