<?php if($report_row['report_key'] == 'agent_quick_sales_summary') { ?>
	<h4 class="fs16 m-t-0 m-b-20">Filter Options</h4>
	<div class="row">
	    <?=generateDateRange('Transaction Date')?>
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
	
<?php } elseif($report_row['report_key'] == 'agent_new_business_post_payments') { ?>
	<h4 class="fs16 m-t-0 m-b-20">Filter Options</h4>
	<div class="row">
	    <?=generateDateRange('Post-Payment Date')?>
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
<?php } elseif($report_row['report_key'] == 'agent_declines_summary') { ?>
	<h4 class="fs16 m-t-0 m-b-20">Filter Options</h4>
	<div class="row">
	    <?=generateDateRange('Transaction Date')?>
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
<?php } else if($report_row['report_key'] == 'agent_monthly_forecasting') { ?>
	<h4 class="fs16 m-t-0 m-b-20">Filter Options</h4>
	<div class="row">
		<input type="hidden" name="agent_id" value="<?=$_SESSION['agents']['id']?>">
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
				startDate:'+0m',
			});
		});
	</script>
<?php } ?>