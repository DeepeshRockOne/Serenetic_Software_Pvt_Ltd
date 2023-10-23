<?php if($report_row['report_key'] == 'admin_export') { ?>
  <h4 class="fs16 m-t-0 m-b-20">Filter Options</h4>
	<div class="row">
	    <div class="col-xs-6">
          	<div class="form-group">
              	<select class="se_multiple_select" name="display_id[]" id="display_id" multiple="multiple">
                  <?php if(!empty($fl_res)){ ?>
                      <?php 
                      foreach($fl_res as $admin_row){ ?>
                          <option value="<?=$admin_row['display_id']?>"><?=$admin_row['display_id'].' - '.$admin_row['fname'].' '.$admin_row['lname']?></option>
                      <?php } ?>
                  <?php } ?>
          		</select>
              	<label>Admins</label>
              	<p class="error"><span class="error_display_id"></span></p>
          	</div>
        </div>
	</div>
	<script type="text/javascript">
		$(document).ready(function(){
			$("#display_id").multipleSelect({
				width:'100%'
		  	});
		});
	</script>
<?php } elseif($report_row['report_key'] == 'admin_history') { ?>
  <h4 class="fs16 m-t-0 m-b-20">Filter Options</h4>
	<div class="row">
		<div class="col-xs-6">
          	<div class="form-group">
              	<select class="form-control" name="admin_id" id="admin_id" data-live-search="true">
          			<option data-hidden="true"></option>
                  	<?php if(!empty($fl_res)){
                      	foreach($fl_res as $admin_row){ ?>
                          <option value="<?=$admin_row['id']?>"><?=$admin_row['display_id'].' - '.$admin_row['fname'].' '.$admin_row['lname']?></option>
                      <?php } ?>
                  	<?php } ?>
          		</select>
              	<label>Admin</label>
              	<p class="error"><span class="error_admin_id"></span></p>
          	</div>
        </div>
        <div class="clearfix"></div>
	    <?=generateDateRange()?>
	</div>
	<script type="text/javascript">
		$(document).ready(function(){
			$(".date_picker").mask("99/99/9999");
		    /*$(".date_picker").datepicker({
				changeDay: true,
				changeMonth: true,
				changeYear: true,
	            autoclose:true
			});*/
		});
	</script>
<?php } ?>