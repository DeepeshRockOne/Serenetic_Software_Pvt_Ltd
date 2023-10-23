<?php if($report_row['report_key'] == 'advance_funding' || $report_row['report_key'] == 'advance_collection') { ?>
    <h4 class="fs16 m-t-0 m-b-20">Filter Options</h4>
	<div class="row">
		<?=generateDateRange()?>
	</div>
    <script type="text/javascript">
		$(document).ready(function(){
			$("#pay_period").multipleSelect({
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
<?php }else if($report_row['report_key'] == 'commission_setup') { ?>
	<h4 class="fs16 m-t-0 m-b-20">Filter Options</h4>
	<div class="row">
		<?=generateDateRange()?>
        <div class="col-xs-6 ">
          	<div class="form-group">
              	<select class="se_multiple_select" name="product_ids[]"  id="product_ids" multiple="multiple" >
                  	<?php if(!empty($productSearchList)){ ?>
                    <?php foreach ($productSearchList as $key=> $category) { ?>
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
              	<select class="se_multiple_select" name="commission_ids[]"  id="commission_ids" multiple="multiple" >
                  	<?php if(!empty($resCommRules)){ ?>
                        <?php foreach ($resCommRules as $pkey => $row) { ?>
	                      	<option value="<?= $row['id'] ?>"><?= $row['rule_code'].' - '.$row['name']?></option>
                     	<?php } } ?>
              	</select>
              	<label>Commission ID</label>
              	</div>
		</div>
		<div class="col-xs-12">
        	<select id='pre-selected-options' multiple='multiple'>
        		<?php if(!empty($fields)){ ?>
	              <?php foreach ($fields as $column_key => $field) { ?>
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
                height:550
            });
		    $(".date_picker").datepicker({
				changeDay: true,
				changeMonth: true,
				changeYear: true,
	            autoclose:true,
			});
            $("#product_ids,#commission_ids").multipleSelect({
				width:'100%',
				filter:true
			});
		});
	</script>
<?php }else if($report_row['report_key'] == 'admin_agent_debit_balance') { ?>
	<h4 class="fs16 m-t-0 m-b-20">Filter Options</h4>
	<div class="row">
	    <div id="date_range" class="col-xs-4">
	       <div class="form-group">
	          <select class="form-control" id="join_range" name="join_range">
		            <option value="Before" selected>Before</option>
	          </select>
	          <label>As of Date</label>
	          <p class="error"><span class="error_join_range"></span></p>
	       </div>
	    </div>
	    <div class="select_date_div col-xs-8" >
	       	<div class="form-group">
				<div id="all_join">
					<div class="input-group">
						<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
						<div class="pr">
							<input type="text" name="added_date" id="added_date" value="" class="form-control date_picker" placeholder="MM / DD / YYYY" />
						</div>
					</div>
					<p class="error text-left"><span class="error_added_date"></span></p>
				</div>
	       	</div>
		</div>
		<div class="col-xs-6">
          	<div class="form-group">
              	<select class="form-control" name="agent_id"  id="agent_id" data-live-search="true">
					<option value="" hidden selected></option>
                  	<?php if(!empty($resCommRules)){ ?>
                        <?php foreach ($resCommRules as $pkey => $row) { ?>
	                      	<option value="<?= $row['id'] ?>"><?= $row['rep_id'].' - '.$row['name']?></option>
                     	<?php } } ?>
              	</select>
				<label>Agent ID</label>
				<p class="error text-left"><span class="error_agent_id"></span></p>
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
		});
	</script>
<?php }else if($report_row['report_key'] == 'debit_balance_overview' || $report_row['report_key'] == 'admin_agent_debit_ledger') { ?>
	<h4 class="fs16 m-t-0 m-b-20">Filter Options</h4>
	<div class="row">
	    <div id="date_range" class="col-xs-4">
	       <div class="form-group">
	          <select class="form-control" id="join_range" name="join_range">
				<option value="Before" selected>Before</option>
	          </select>
	          <label>As of Date</label>
	          <p class="error"><span class="error_join_range"></span></p>
	       </div>
	    </div>
	    <div class="select_date_div col-xs-8" >
	       	<div class="form-group">
				<div id="all_join">
					<div class="input-group">
						<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
						<div class="pr">
							<input type="text" name="added_date" id="added_date" value="" class="form-control date_picker" placeholder="MM / DD / YYYY" />
						</div>
					</div>
					<p class="error text-left"><span class="error_added_date"></span></p>
				</div>
	       	</div>
		</div>
		<div class="col-xs-6 ">
			<div class="form-group">
						<select class="se_multiple_select" name="agent_ids[]" id="agent_ids" multiple="multiple">
						<?php if(!empty($resCommRules)){ ?>
							<?php foreach ($resCommRules as $pkey => $row) { ?>
								<option value="<?= $row['id'] ?>"><?= $row['rep_id'].' - '.$row['name']?></option>
							<?php } } ?>
					</select>
					<label>Agent ID</label>
					<p class="error text-left"><span class="error_agent_ids"></span></p>
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
			$("#agent_ids").multipleSelect({
				width:'100%',
				filter:true
			});
		});
	</script>
<?php } ?>