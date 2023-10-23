<?php if($report_row['report_key'] == 'group_summary') { ?>
	<h4 class="fs16 m-t-0 m-b-20">Filter Options</h4>
	<div class="row">
        <?=generateDateRange()?>
        <div class="col-xs-7">
          	<div class="form-group height_auto">
				<div class="group_select">
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
				</div>
              	<p class="error"><span class="error_group_ids"></span></p>
          	</div>
        </div>
	</div>
	<script type="text/javascript">
		$(document).ready(function(){
			parent.$.colorbox.resize({
		        height:475
		    });
			$("#group_ids").multipleSelect({
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
<?php } elseif($report_row['report_key'] == 'group_interactions') { ?>
	<input type="hidden" name="user_type" value="group">
	<h4 class="fs16 m-t-0 m-b-20">Filter Options</h4>
	<div class="row">
		<div class="col-xs-6">
          	<div class="form-group height_auto">
              	<select class="se_multiple_select" name="user_ids[]" id="user_ids" data-live-search="true" multiple>
					<?php if(!empty($fl_res_group)){ ?>
						<?php foreach ($fl_res_group as $key=> $row) { ?>
							<option value="<?= $row['id'] ?>" >
							<?= $row['rep_id'] .' - '.$row['fname'].' '.$row['lname']?>    
							</option>
						<?php } ?>
					<?php } ?>
          		</select>
              	<label>Group</label>
              	<p class="error"><span class="error_user_id"></span></p>
          	</div>
        </div>
        <div class="clearfix"></div>
		<?=generateDateRange()?>
		<div class="col-xs-6">
            <div class="form-group height_auto">
                <select class="se_multiple_select" name="interactions[]" id="interactions"  multiple >
                    <?php if ($fl_interaction_res) {?>
                    <?php foreach ($fl_interaction_res as $interaction) { ?>
                    <option value="<?=$interaction["id"];?>"><?php echo $interaction['type']; ?></option>
                    <?php } } ?>
                </select>
                <label>Group Interactions</label>
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
<?php } elseif($report_row['report_key'] == 'group_history') { ?>
	<h4 class="fs16 m-t-0 m-b-20">Filter Options</h4>
	<div class="row">
		<?=generateDateRange()?>
		<div class="clearfix"></div>
		<div class="col-xs-6">
          	<div class="form-group height_auto pn" >
              	<select class="form-control" name="group_id" id="group_id" data-live-search="true">
          			<option data-hidden="true"></option>
                  	<?php if(!empty($fl_res_group)){
                      	foreach($fl_res_group as $row){ ?>
                          <option value="<?=$row['id']?>"><?=$row['rep_id'].' - '.$row['fname'].' '.$row['lname']?></option>
                      <?php } ?>
                  	<?php } ?>
          		</select>
              	<label>Group</label>
              	<p class="error"><span class="error_group_id"></span></p>
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
<?php } elseif($report_row['report_key'] == 'group_full_coverage') { ?>
	<h4 class="fs16 m-t-0 m-b-20">Filter Options</h4>
	<div class="row">
		<?=generateDateRange('Added Date')?>
		<div class="clearfix"></div>
		<div class="col-xs-6">
          	<div class="form-group height_auto pn" >
              	<select class="form-control" name="group_id" id="group_id" data-live-search="true">
          			<option data-hidden="true"></option>
                  	<?php if(!empty($fl_res_group)){
                      	foreach($fl_res_group as $row){ ?>
                          <option value="<?=$row['id']?>"><?=$row['rep_id'].' - '.$row['fname'].' '.$row['lname']?></option>
                      <?php } ?>
                  	<?php } ?>
          		</select>
              	<label>Group</label>
              	<p class="error"><span class="error_group_id"></span></p>
          	</div>
        </div>
        <div class="col-xs-6 ">
          	<div class="form-group">
          		<div class="group_select">
              	<select class="se_multiple_select" name="products[]"  id="rpt_products" multiple="multiple" >
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
        <div class="col-xs-6 ">
          	<div class="form-group">
          		<select class="form-control" name="report_type"  id="report_type">
          			<option value="masked">SSN (Masked)</option>
          			<option value="unmasked">SSN (Unmasked)</option>
          		</select>
          		<label>Report Type</label>
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
			$("#rpt_products").multipleSelect({
	      		selectAll: true,
				width:'100%',
				filter:true
		  	});
		});
	</script>
<?php } elseif($report_row['report_key'] == 'group_enroll_overview') { ?>
	<h4 class="fs16 m-t-0 m-b-20">Filter Options</h4>
	<div class="row">
		<!-- <?=generateDateRange('Added Date')?> -->
		<div class="col-xs-6">
			<div class="form-group height_auto pn" >
				<input type="text" name="date_picker" id="date_picker" class="date_picker form-control">
				<label>Plan Period</label>
				<p class="error"><span class="error_date_picker"></span></p>
			</div>
		</div>
		<!-- <div class="clearfix"></div> -->
		<div class="col-xs-6">
          	<div class="form-group height_auto pn" >
              	<select class="se_multiple_select" name="group_id[]" id="group_id"  multiple="multiple">
                  	<?php if(!empty($fl_res_group)){
                      	foreach($fl_res_group as $row){ ?>
                          <option value="<?=$row['id']?>"><?=$row['rep_id'].' - '.$row['fname'].' '.$row['lname']?></option>
                      <?php } ?>
                  	<?php } ?>
          		</select>
              	<label>Group</label>
              	<p class="error"><span class="error_group_id"></span></p>
          	</div>
        </div>
        <div class="col-xs-6 ">
          	<div class="form-group">
          		<div class="group_select">
              	<select class="se_multiple_select" name="products[]"  id="rpt_products" multiple="multiple" >
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
	</div>
	<script type="text/javascript">
		$(document).ready(function(){
			parent.$.colorbox.resize({
		        height:475
		    });
		 //    $(".date_picker").datepicker({
			// 	changeDay: true,
			// 	changeMonth: true,
			// 	changeYear: true,
	  //           autoclose:true,
			// 	orientation: 'auto top'
			// });

			$('.date_picker').datepicker({
                format: 'mm/yy',
                startView : 1,
                minViewMode: 1,
                autoclose: true,    
                // startDate:new Date(),
                // endDate : '+15y'
            });
			$("#rpt_products").multipleSelect({
	      		selectAll: true,
				width:'100%',
				filter:true
		  	});
		  	$("#group_id").multipleSelect({
	      		selectAll: true,
				width:'100%',
				filter:true
		  	});
		});
	</script>
<?php } elseif($report_row['report_key'] == 'group_member_age_out') { ?>
	<h4 class="fs16 m-t-0 m-b-20">Filter Options</h4>
	<div class="row">
		<?=generateDateRange('Added Date')?>
		<!-- <div class="col-xs-6">
			<div class="form-group height_auto pn" >
				<input type="text" name="date_picker" id="date_picker" class="date_picker form-control">
				<label>Age Out Month</label>
				<p class="error"><span class="error_date_picker"></span></p>
			</div>
		</div> -->
		<div class="col-xs-6">
          	<div class="form-group height_auto pn" >
              	<select class="se_multiple_select" name="group_id[]" id="group_id"  multiple="multiple">
                  	<?php if(!empty($fl_res_group)){
                      	foreach($fl_res_group as $row){ ?>
                          <option value="<?=$row['id']?>"><?=$row['rep_id'].' - '.$row['fname'].' '.$row['lname']?></option>
                      <?php } ?>
                  	<?php } ?>
          		</select>
              	<label>Group</label>
              	<p class="error"><span class="error_group_id"></span></p>
          	</div>
        </div>
        <div class="col-xs-6 ">
          	<div class="form-group">
          		<div class="group_select">
              	<select class="se_multiple_select" name="products[]"  id="rpt_products" multiple="multiple" >
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
        <div class="col-xs-6 ">
          	<div class="form-group">
          		<select class="form-control" name="product_status"  id="product_status">
          			<option value="Acive">Active</option>
          			<option value="Inactive">Inactive</option>
          			<option value="Both">Active/Inactive</option>
          		</select>
          		<label>Plan Status</label>
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

		    // $('.date_picker').datepicker({
      //           format: 'mm/yy',
      //           startView : 1,
      //           minViewMode: 1,
      //           autoclose: true,    
      //           // startDate:new Date(),
      //           // endDate : '+15y'
      //       });
			$("#rpt_products").multipleSelect({
	      		selectAll: true,
				width:'100%',
				filter:true
		  	});
		  	$("#group_id").multipleSelect({
	      		selectAll: true,
				width:'100%',
				filter:true
		  	});
		});
	</script>
<?php } elseif($report_row['report_key'] == 'group_change_product') { ?>
	<h4 class="fs16 m-t-0 m-b-20">Filter Options</h4>
	<div class="row">
		<div class="col-xs-6">
			<div class="form-group height_auto pn" >
				<input type="text" name="date_picker" id="date_picker" class="date_picker form-control">
				<label>Plan Period</label>
				<p class="error"><span class="error_date_picker"></span></p>
			</div>
		</div>
		<div class="clearfix"></div>
		<div class="col-xs-6">
          	<div class="form-group height_auto pn" >
              	<select class="se_multiple_select" name="group_id[]" id="group_id"  multiple="multiple">
                  	<?php if(!empty($fl_res_group)){
                      	foreach($fl_res_group as $row){ ?>
                          <option value="<?=$row['id']?>"><?=$row['rep_id'].' - '.$row['fname'].' '.$row['lname']?></option>
                      <?php } ?>
                  	<?php } ?>
          		</select>
              	<label>Group</label>
              	<p class="error"><span class="error_group_id"></span></p>
          	</div>
        </div>
        <div class="col-xs-6 ">
          	<div class="form-group">
          		<div class="group_select">
              	<select class="se_multiple_select" name="products[]"  id="rpt_products" multiple="multiple" >
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
	</div>
	<script type="text/javascript">
		$(document).ready(function(){
			parent.$.colorbox.resize({
		        height:475
		    });
		 //    $(".date_picker").datepicker({
			// 	changeDay: true,
			// 	changeMonth: true,
			// 	changeYear: true,
	  //           autoclose:true,
			// 	orientation: 'auto top'
			// });
			$('.date_picker').datepicker({
                format: 'mm/yy',
                startView : 1,
                minViewMode: 1,
                autoclose: true,    
                // startDate:new Date(),
                // endDate : '+15y'
            });
			$("#rpt_products").multipleSelect({
	      		selectAll: true,
				width:'100%',
				filter:true
		  	});
		  	$("#group_id").multipleSelect({
	      		selectAll: true,
				width:'100%',
				filter:true
		  	});
		});
	</script>
<?php } ?>