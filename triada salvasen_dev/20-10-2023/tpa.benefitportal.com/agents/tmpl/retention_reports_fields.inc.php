<?php if($report_row['report_key'] == 'agent_p2p_comparison') { ?>
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
<?php } elseif($report_row['report_key'] == 'agent_member_persistency') { ?>
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
<?php } else if($report_row['report_key'] == 'agent_product_persistency') { ?>
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
<?php } ?>