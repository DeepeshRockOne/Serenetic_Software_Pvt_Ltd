<?php if($report_row['report_key'] == 'admin_product_persistency') { ?>
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
				<p class="error"><span class="error_product_ids"></span></p>
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
            $("#agent_ids,#products").multipleSelect({
	      		selectAll: true,
				width:'100%',
				filter:true
		  	});
		});
	</script>
<?php }else if($report_row['report_key'] == 'product_overview') { ?>
	<h4 class="fs16 m-t-0 m-b-20">Filter Options</h4>
	<div class="row">
        <?=generateDateRange()?>
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
				<p class="error"><span class="error_product_ids"></span></p>
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
                height:490
            });
		    $(".date_picker").datepicker({
				changeDay: true,
				changeMonth: true,
				changeYear: true,
	            autoclose:true,
            });
            $("#products").multipleSelect({
	      		selectAll: true,
				width:'100%',
				filter:true
		  	});
		});
	</script>
<?php }else if(in_array($report_row['report_key'],array('carrier_overview','membership_overview','vendor_overview'))) { ?>
	<h4 class="fs16 m-t-0 m-b-20">Filter Options</h4>
	<div class="row">
        <?=generateDateRange()?>
        <div class="col-xs-6 ">
          	<div class="form-group">
              	<select class="se_multiple_select" name="carriers[]"  id="carriers" multiple="multiple" >
					<?php if(!empty($carrArr)) {
						foreach($carrArr as $carrier){ ?>
						<option value="<?=$carrier['id']?>"><?=$carrier['display_id'].' - '.$carrier['name']?></option>
					<?php }} ?>
              	</select>
				<label><?=$label?></label>
				<p class="error"><span class="error_carriers"></span></p>
          	</div>
        </div>
		<input type="hidden" name="feeType" value="<?=$label?>">
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
            $("#carriers").multipleSelect({
	      		selectAll: true,
				width:'100%',
				filter:true
		  	});
		});
	</script>
<?php }else if($report_row['report_key'] == 'payables_reconciliation') { ?>
	<h4 class="fs16 m-t-0 m-b-20">Filter Options</h4>
	<div class="row">
		<!-- <div class="col-xs-6">
			<label>
				<input type="radio" name="added_or_effective_date" value="added_date"> Added Date
			</label>
			<p class="error text-left"><span class="error_added_date"></span></p>
		</div>
		<br/>
		<div class="clearfix m-t-40"></div> -->
        <?=generateDateRange('Added Date')?>
        
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
				<p class="error"><span class="error_product_ids"></span></p>
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
            $("#agent_ids,#products").multipleSelect({
	      		selectAll: true,
				width:'100%',
				filter:true
		  	});
		});
	</script>
<?php } ?>