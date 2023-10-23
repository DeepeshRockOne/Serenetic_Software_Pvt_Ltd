<?php if($report_row['report_key'] == 'list_bill_overview') { ?>
	<h4 class="fs16 m-t-0 m-b-20">Filter Options</h4>
	<div class="row">
        <?=generateDateRange()?>
        <div class="col-xs-7">
          	<div class="form-group ">
					<select class="se_multiple_select" name="group_ids[]" id="group_ids" multiple="multiple">
						<?php if(!empty($fl_res_group)){ ?>
							<?php foreach ($fl_res_group as $row) { ?>
								<option value="<?= $row['id'] ?>">
								<?= $row['rep_id'] .' - '.$row['fname'].' '.$row['lname']?>    
								</option>
							<?php } ?>
						<?php } ?>
					</select>
				  <label>Groups</label>
              	<p class="error"><span class="error_group_ids"></span></p>
          	</div>
        </div>
        <div class="col-xs-7">
          	<div class="form-group ">
					<select class="se_multiple_select" name="tree_agent_ids[]" id="tree_agent_ids" multiple="multiple">
						<?php if(!empty($fl_res_agent)){ ?>
							<?php foreach ($fl_res_agent as $row) { ?>
								<option value="<?= $row['id'] ?>">
								<?= $row['rep_id'] .' - '.$row['fname'].' '.$row['lname']?>    
								</option>
							<?php } ?>
						<?php } ?>
					</select>
				  <label>Agent Tree</label>
              	<p class="error"><span class="error_tree_agent_ids"></span></p>
          	</div>
        </div>
	</div>
	<script type="text/javascript">
		$(document).ready(function(){
			parent.$.colorbox.resize({
		        height:475
		    });
			$("#group_ids,#tree_agent_ids").multipleSelect({
				width:'100%'
		  	});
		});
        $(".date_picker").datepicker({
            changeDay: true,
            changeMonth: true,
            changeYear: true,
            autoclose:true,
        });
	</script>
<?php } elseif($report_row['report_key'] == 'payables_export') { ?>
	<h4 class="fs16 m-t-0 m-b-20">Filter Options</h4>
	<div class="row">
        <div class="col-xs-6">
			<label>
				<input type="radio" name="added_or_transaction_date" value="added_date"> Added Date
			</label>
			<label class="m-l-10">
				<input type="radio" name="added_or_transaction_date" value="transaction_date"> Transaction Date
			</label>
			<p class="error text-left"><span class="error_added_or_transaction_date"></span></p>
		</div>
		<br/>
		<div class="clearfix m-t-40"></div>
	    <?=generateDateRange('Date')?>

	    <div class="col-xs-6">
          	<div class="form-group">
                <select class="se_multiple_select" name="payee_type[]"  id="payee_type" multiple="multiple">
                  	<option value="Advance Commission">Advance</option>
                  	<option value="Carrier">Carrier</option>
                  	<option value="Commission">Commission</option>
                  	<option value="Fee Commission">Fee Commission</option>
                  	<option value="Membership">Membership</option>
                  	<option value="PMPM">PMPM</option>
                  	<option value="Vendor">Vendor</option>
                </select>
                <label>Payee Type</label>
                <p class="error text-left"><span class="error_payee_type"></span></p>
          	</div>
        </div>
	    <div class="col-xs-6 ">
          	<div class="form-group">
                <select class="se_multiple_select" name="payee[]"  id="payee" multiple="multiple">
                	<?php if(!empty($fl_payee_res)){ ?>
	                  	<?php foreach ($fl_payee_res as $fl_payee_row) { 
                  				if(empty($fl_payee_row['PAYEE_ID'])) {
                  					continue;
                  				}
                  		?>
	                  	<option value="<?= $fl_payee_row['PAYEE_ID'] ?>"><?= $fl_payee_row['PAYEE_ID'].' - '.$fl_payee_row['PAYEE']?></option>
	                    <?php } ?>
                    <?php } ?>
                </select>
                <label>Payee</label>
                <p class="error text-left"><span class="error_payee"></span></p>
          	</div>
        </div>
        <div class="col-xs-6 ">
          	<div class="form-group">
              	<select class="se_multiple_select" name="products[]"  id="products" multiple="multiple" >
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
        <div class="col-xs-6">
        	<select id='pre-selected-options' multiple='multiple'>
        		<?php if(!empty($fields)){ ?>
	              <?php foreach ($fields as $column_key=> $field) { ?>
			    		<option value='<?= $column_key ?>' <?=in_array($column_key,$selected_columns)?'selected="selected"':'' ?> <?= in_array($column_key,$selected_columns) && $field['is_default'] == "Y"?"class='disabled'":"";?>><?= $column_key ?></option>
			    	<?php } ?>
                <?php } ?>
			  </select>
		  	<br/>
			<label for="columns_save_as_defualt"><input type="checkbox" name="columns_save_as_defualt" id="columns_save_as_defualt" value="1" <?=!empty($setting_selected_columns)?"checked":""?>> Save selected columns as default</label>
        </div>
	</div>
	<script type="text/javascript">
		$(document).ready(function(){
			parent.$.colorbox.resize({
		        innerHeight:590
		    });
	        $(".date_picker").datepicker({
	            changeDay: true,
	            changeMonth: true,
	            changeYear: true,
	            autoclose:true,
	        });

	        $("#payee_type").multipleSelect({
	        	width:'100%',
	        	selectAll: true,
	        });

	        $("#products,#payee").multipleSelect({
	        	width:'100%',
	        	filter:true,
	        	selectAll: true
	        });
		});
	</script>
<?php }else if($report_row['report_key'] == 'daily_order_summary') { ?>
    <h4 class="fs16 m-t-0 m-b-20">Filter Options</h4>
	<div class="row">
        <?=generateDateRange('Transaction Date')?>
    </div>
    <script type="text/javascript">
        $(".date_picker").datepicker({
            changeDay: true,
            changeMonth: true,
            changeYear: true,
            autoclose:true,
            endDate: '0d',
        });
	</script>
<?php }else if($report_row['report_key'] == 'top_performing_agency') { ?>
    <h4 class="fs16 m-t-0 m-b-20">Filter Options</h4>
	<div class="row">
        <?=generateDateRange('Transaction Date')?>
    </div>
    <script type="text/javascript">
        $(".date_picker").datepicker({
            changeDay: true,
            changeMonth: true,
            changeYear: true,
            autoclose:true,
            endDate: '0d',
        });
	</script>
<?php }else if($report_row['report_key'] == 'admin_next_billing_date') { ?>
    <h4 class="fs16 m-t-0 m-b-20">Filter Options</h4>
	<div class="row">
		<?=generateDateRange('Next Billing Date')?>
		<div class="col-xs-7">
          	<div class="form-group ">
					<select class="se_multiple_select" name="agents_id[]" id="agents_id" multiple="multiple">
						<?php if(!empty($fl_res_agent)){ ?>
							<?php foreach ($fl_res_agent as $key=> $row) { ?>
								<option value="<?= $row['id'] ?>">
								<?= $row['rep_id'] .' - '.$row['fname'].' '.$row['lname']?>    
								</option>
							<?php } ?>
						<?php } ?>
					</select>
				  <label>Agent Tree</label>
              	<p class="error"><span class="error_display_id"></span></p>
          	</div>
        </div>
    </div>
	<script type="text/javascript">
		$(document).ready(function(){
			$("#agents_id").multipleSelect({
				width:'100%',
				filter:true
			});
		});
        $(".date_picker").datepicker({
            changeDay: true,
            changeMonth: true,
            changeYear: true,
            autoclose:true,
        });
	</script>
<?php }else if($report_row['report_key'] == 'admin_new_business_post_payments_org') { ?>
    <h4 class="fs16 m-t-0 m-b-20">Filter Options</h4>
	<div class="row">
		<div class="col-xs-6">
			<label>
				<input type="radio" name="added_or_post_payment_date" value="added_date"> Added Date
			</label>
			<label class="m-l-10">
				<input type="radio" name="added_or_post_payment_date" value="post_payment_date"> Post-Payment Date
			</label>
			<p class="error text-left"><span class="error_added_or_post_payment_date"></span></p>
		</div>
		<br/>
		<div class="clearfix m-t-40"></div>

		<?=generateDateRange('Date')?>

		<div class="col-xs-7">
          	<div class="form-group ">
					<select class="se_multiple_select" name="agent_ids[]" id="agent_ids" multiple="multiple">
						<?php if(!empty($fl_res_agent)){ ?>
							<?php foreach ($fl_res_agent as $key=> $row) { ?>
								<option value="<?= $row['id'] ?>">
								<?= $row['rep_id'] .' - '.$row['fname'].' '.$row['lname']?>    
								</option>
							<?php } ?>
						<?php } ?>
					</select>
				  <label>Agent Tree</label>
              	<p class="error"><span class="error_agent_ids"></span></p>
          	</div>
        </div>
    </div>
	<script type="text/javascript">
		$(document).ready(function(){
			parent.$.colorbox.resize({
		        height:475
		    });

			$("#agent_ids").multipleSelect({
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
<?php }else if(in_array($report_row['report_key'],array('admin_payment_transaction_report','admin_payment_failed_payment_recapture_analytics','admin_payment_reversal_transactions'))) { ?>
    <h4 class="fs16 m-t-0 m-b-20">Filter Options</h4>
	<div class="row">
		<div class="col-xs-8">
			<label>
				<input type="radio" name="transaction_or_effective_date" value="transaction_date"> Transaction Date
			</label>
			<label class="m-l-10"> 
				<input type="radio" name="transaction_or_effective_date" value="effective_date"> Effective Date
			</label>
			<?php if(in_array($report_row['report_key'],array('admin_payment_transaction_report'))){ ?>
				<label class="m-l-10">
					<input type="radio" name="transaction_or_effective_date" value="coverage_period"> Plan Period
				</label>
			<?php } ?>
			<p class="error text-left"><span class="error_transaction_or_effective_date"></span></p>
		</div>
		<br/>
		<div class="clearfix m-t-40"></div>

		<?=generateDateRange('Date')?>

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

		<div class="col-xs-6">
          	<div class="form-group ">
					<select class="se_multiple_select" name="agent_ids[]" id="agent_ids" multiple="multiple">
						<?php if(!empty($fl_res_agent)){ ?>
							<?php foreach ($fl_res_agent as $key=> $row) { ?>
								<option value="<?= $row['id'] ?>">
								<?= $row['rep_id'] .' - '.$row['fname'].' '.$row['lname']?>    
								</option>
							<?php } ?>
						<?php } ?>
					</select>
				  <label>Agent Tree</label>
              	<p class="error"><span class="error_agent_ids"></span></p>
          	</div>
        </div>
        <?php if(in_array($report_row['report_key'],array('admin_payment_transaction_report'))){ ?>
			<div class="col-xs-6">
	        	<select id='pre-selected-options' multiple='multiple'>
	        		<?php if(!empty($fields)){ ?>
		              <?php foreach ($fields as $column_key=> $field) { ?>
				    		<option value='<?= $column_key ?>' <?=in_array($column_key,$selected_columns)?'selected="selected"':'' ?> <?= in_array($column_key,$selected_columns) && $field['is_default'] == "Y"?"class='disabled'":"";?>><?= $column_key ?></option>
				    	<?php } ?>
	                <?php } ?>
				  </select>
			  	<br/>
				<label for="columns_save_as_defualt"><input type="checkbox" name="columns_save_as_defualt" id="columns_save_as_defualt" value="1" <?=!empty($setting_selected_columns)?"checked":""?>> Save selected columns as default</label>
	        </div>
    	<?php } ?>
    </div>
	<script type="text/javascript">
		$(document).ready(function(){
			<?php if(in_array($report_row['report_key'],array('admin_payment_transaction_report'))){ ?>
				parent.$.colorbox.resize({
			        height:615
			    });
			<?php } else { ?>
				parent.$.colorbox.resize({
			        height:475
			    });
			<?php } ?>
			$("#agent_ids,#product_ids").multipleSelect({
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
<?php }else if(in_array($report_row['report_key'],array('admin_payment_p2p_renewal_comparison'))) { ?>
    <h4 class="fs16 m-t-0 m-b-20">Filter Options</h4>
	<div class="row">
		<?=generateDateRange('Transaction Date')?>

		<div class="col-xs-6">
          	<div class="form-group">
              	<select class="form-control" name="sale_type"  id="sale_type">
              		<option value=""></option>
              		<option value="N">New Business Only</option>
              		<option value="Y">Renewals Only</option>
              		<option value="Both">New Business and Renewals</option>
              	</select>
              	<label>Sale Type</label>
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

		<div class="col-xs-6">
          	<div class="form-group ">
					<select class="se_multiple_select" name="agent_ids[]" id="agent_ids" multiple="multiple">
						<?php if(!empty($fl_res_agent)){ ?>
							<?php foreach ($fl_res_agent as $key=> $row) { ?>
								<option value="<?= $row['id'] ?>">
								<?= $row['rep_id'] .' - '.$row['fname'].' '.$row['lname']?>    
								</option>
							<?php } ?>
						<?php } ?>
					</select>
				  <label>Agent Tree</label>
              	<p class="error"><span class="error_agent_ids"></span></p>
          	</div>
        </div>
    </div>
	<script type="text/javascript">
		$(document).ready(function(){
			parent.$.colorbox.resize({
		        height:475
		    });
			$("#agent_ids,#product_ids").multipleSelect({
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
<?php }else if($report_row['report_key'] == 'admin_payment_outstanding_renewals') { ?>
    <h4 class="fs16 m-t-0 m-b-20">Filter Options</h4>
	<div class="row">
		<?=generateDateRange('Next Billing Date')?>

		<div class="col-xs-7">
          	<div class="form-group ">
					<select class="se_multiple_select" name="agent_ids[]" id="agent_ids" multiple="multiple">
						<?php if(!empty($fl_res_agent)){ ?>
							<?php foreach ($fl_res_agent as $key=> $row) { ?>
								<option value="<?= $row['id'] ?>">
								<?= $row['rep_id'] .' - '.$row['fname'].' '.$row['lname']?>    
								</option>
							<?php } ?>
						<?php } ?>
					</select>
				  <label>Agent Tree</label>
              	<p class="error"><span class="error_agent_ids"></span></p>
          	</div>
        </div>
    </div>
	<script type="text/javascript">
		$(document).ready(function(){
			$("#agent_ids").multipleSelect({
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
<?php } else if($report_row['report_key'] == 'platform_pmpm') { ?>
	<h4 class="fs16 m-t-0 m-b-20">Filter Options</h4>
	<div class="row">
		<div class="col-xs-2">
			Select Month</div>
			<div class="col-xs-4">
			<div class="input-group">
				<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
				<div class="pr">
					<input type="text" name="select_month" id="select_month" value="" class="form-control date_picker" placeholder="MM / DD / YYYY" />
				</div>
			</div>
			<p class="error text-left"><span class="error_select_month"></span></p>
		</div>
	</div>
	<script type="text/javascript">
		$(document).ready(function(){
			$('.date_picker').datepicker({
				format: 'mm/yyyy',
				minViewMode: 1,
			});
		});
	</script>
<?php } else if($report_row['report_key'] == 'payment_policy_overview') { ?>
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
        <div class="col-xs-6">
        	<select id='pre-selected-options' multiple='multiple'>
        		<?php if(!empty($fields)){ ?>
	              <?php foreach ($fields as $column_key=> $field) { ?>
			    		<option value='<?= $column_key ?>' <?=in_array($column_key,$selected_columns)?'selected="selected"':'' ?> <?= in_array($column_key,$selected_columns) && $field['is_default'] == "Y"?"class='disabled'":"";?>><?= $column_key ?></option>
			    	<?php } ?>
                <?php } ?>
			  </select>
		  	<br/>
			<label for="columns_save_as_defualt"><input type="checkbox" name="columns_save_as_defualt" id="columns_save_as_defualt" value="1" <?=!empty($setting_selected_columns)?"checked":""?>> Save selected columns as default</label>
        </div>
	</div>
	<script type="text/javascript">
		$(document).ready(function(){
			parent.$.colorbox.resize({
		        height:550
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
<?php }else if($report_row['report_key'] == 'payment_nb_sales' || $report_row['report_key'] == 'payment_rb_sales') { ?>
    <h4 class="fs16 m-t-0 m-b-20">Filter Options</h4>
	<div class="row">
		<?=generateDateRange()?>

		<div class="col-xs-7">
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
				  <label>Agent Tree</label>
              	<p class="error"><span class="error_tree_agent_ids"></span></p>
          	</div>
        </div>
    </div>
	<script type="text/javascript">
		$(document).ready(function(){
			parent.$.colorbox.resize({
		        height:475
		    });
			$("#tree_agent_ids").multipleSelect({
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
