<?php if($report_row['report_key'] == 'group_summary_export') { ?>
	<h4 class="fs16 m-t-0 m-b-20">Filter Options</h4>
	<div class="row">
        <?=generateDateRange()?>
	</div>
	<script type="text/javascript">
		$(document).ready(function(){
			parent.$.colorbox.resize({
		        height:475
		    });
		});
        $(".date_picker").datepicker({
            changeDay: true,
            changeMonth: true,
            changeYear: true,
            autoclose:true,
        });
	</script>
<?php } elseif($report_row['report_key'] == 'group_history_export') { ?>
	<h4 class="fs16 m-t-0 m-b-20">Filter Options</h4>
	<div class="row">
		<?=generateDateRange()?>
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
<?php } elseif($report_row['report_key'] == 'list_bill_overview_export') { ?>
	<h4 class="fs16 m-t-0 m-b-20">Filter Options</h4>
	<div class="row">
        <?=generateDateRange()?>
	</div>
	<script type="text/javascript">
		$(document).ready(function(){
			parent.$.colorbox.resize({
		        height:475
		    });
		});
        $(".date_picker").datepicker({
            changeDay: true,
            changeMonth: true,
            changeYear: true,
            autoclose:true,
        });
	</script>
<?php } ?>