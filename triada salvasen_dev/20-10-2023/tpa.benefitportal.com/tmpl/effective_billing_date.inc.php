<div class="panel panel-default">
	<div class="panel-heading">
		<h4 class="mn">Effective Date</h4>
	</div>
	<div class="panel-body">
		<div class="theme-form">
			<form name="effective_form" id="effective_form" action="" method="POST">
				<input type="hidden" name="is_submit" value="Y">
				<input type="hidden" id="location" name="location" value="<?=$location?>">
				<input type="hidden" name="ws_id" value="<?=$ws_id?>">
			<div class="form-group">
				<div class="input-group">
				    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
				    <div class="pr">
				    <input id="effective_date" name="effective_date" type="text" class="form-control" value="<?=date('m/d/Y',strtotime($ws_row['eligibility_date']))?>" onkeydown="return false">
				    <label>Effective Date (MM/DD/YYYY)</label>
				    </div>
			    </div>
				    <p class="error"><span id="err_effective_date"><?=$error?></span></p>
			</div>
			<div class="text-center">
				<button id="submit" class="btn btn-action">Save</button>
				<a href="javascript:void(0);" class="btn red-link" onclick="parent.$.colorbox.close();">Cancel</a>
			</div>
		</form>
		</div>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		<?php if(!empty($coverage_period_data)) { ?>
		$disableDays = <?=json_encode($disableDays)?>;
		$("#effective_date").datepicker({
			beforeShowDay: function(date){
				if($disableDays.length > 0){
					pickerDate = date.getDate();
					pickerDate = pickerDate.toString();
					if (pickerDate.length == 1){
	       				pickerDate = '0' + pickerDate;
					}
					if ($.inArray(pickerDate , $disableDays) !== -1) {
	                	return false;
	                }else{
						return true;
					}
				}
			},
            startView: <?=$coverage_period_data['startView']?>,
            minViewMode: <?=$coverage_period_data['minViewMode']?>,
            <?php 
            if(!empty($earliest_effective_date)) {
        	echo "startDate:'".date("m/d/Y",strtotime($earliest_effective_date))."',";
            }
            ?>
	    });
		<?php } else { ?>
		$("#effective_date").datepicker({
	        changeDay: true,
	        changeMonth: true,
	        changeYear: true,
	        autoclose: true,
	        <?php 
            if(!empty($earliest_effective_date)) {
        	echo "startDate:'".date("m/d/Y",strtotime($earliest_effective_date))."',";
            }
            ?>
	    });
		<?php } ?>

		$(document).on('click','#submit',function(e){
			   e.preventDefault();
			   if($("#effective_date").val() == ''){
			   	return false;
			   }
			   $effective_date = $("#effective_date").val();
			   $location = $("#location").val();
			   parent.$.colorbox({
                  iframe:true,
                  href:"<?=$HOST?>/coverage_details.php?ws_id=<?=$ws_id?>&effective_date=" + $effective_date + "&location="+$location,
                  width: '700px',
                  height: '500px',
              });
			   // $('#ajax_loader').show();
			//    $.ajax({
			//    url: 'effective_billing_date.php',
			//    type: 'POST',
			//    data: params,
			//    dataType: 'JSON',
			//    success: function(res) {
			//      $('#ajax_loader').hide();
			//      if(res.status == 'fail'){
			//          $('#err_effective_date').text(res.error);
			//      }else{
			//      	window.parent.$('.effective_td_' + res.product_id).html(res.effective_date);
			//      	parent.setNotifySuccess(res.message);
			//     	parent.$.colorbox.close();
			//      }
			//    }
			// });
		});
	});
</script>