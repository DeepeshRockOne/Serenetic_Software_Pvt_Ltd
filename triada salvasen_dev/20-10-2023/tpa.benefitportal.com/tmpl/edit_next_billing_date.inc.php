<div class="panel panel-default">
	<div class="panel-heading">
		<h4 class="mn">Next Billing Date</h4>
	</div>
	<div class="panel-body">
		<div class="theme-form">
			<form name="effective_form" id="effective_form" action="" method="POST">
				<input type="hidden" name="is_submit" value="Y">
				<input type="hidden" name="location" value="<?=$location?>">
				<input type="hidden" name="ws_id" value="<?=$ws_id?>">
				<div class="form-group">
					<div class="input-group">
					    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
					    <div class="pr">
					    <input id="next_purchase_date" type="text" class="form-control" name="next_purchase_date" value="<?=date('m/d/Y',strtotime($ws_row['next_purchase_date']))?>">
					    <label>Next Billing Date (MM/DD/YYYY)</label>
					    </div>
				    </div>
				    <p class="error" id="error_next_purchase_date"></p>
				</div>

				<div id="retainRuleDiv">
					<div class="text-left m-b-20">
						<label class="label-input">
							<input name="retain_rule" class="retain_rule" type="radio" value="allRenewal" <?=$retain_rule == "allRenewal" ? "checked='checked'" : ""?>> 
							<span class="p-l-10"> Retain this date for all future renewals</span> 
						</label>
						<div class="clearfix"></div>
						<label>
							<input name="retain_rule" class="retain_rule" type="radio" value="oneRenewal" <?=$retain_rule == "oneRenewal" ? "checked='checked'" : ""?>> 
							<span class="p-l-10"> Retain this date for one renewal</span>
						</label>
					</div>
					<p class="error" id="error_retain_rule"></p>
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
	$("#next_purchase_date").datepicker({
        changeDay: true,
        changeMonth: true,
        changeYear: true,
        autoclose: true,
        startDate: '<?=date("m/d/Y",strtotime('now'))?>',
        endDate: '<?=date("m/d/Y",strtotime($end_date))?>',
	});
	
	$(document).off('click','#submit');
	$(document).on('click','#submit',function(e){
	   e.preventDefault();
	   parent.disableButton($(this));
	   var params = $('#effective_form').serialize();
	   $('#ajax_loader').show();
	   $.ajax({
			url: 'edit_next_billing_date.php',
			type: 'POST',
			data: params,
			dataType: 'JSON',
			success: function(res) {
			 parent.enableButton($("#submit"));
			 $(".error").html('').hide();
			 $('#ajax_loader').hide();
			 if(res.status == 'fail'){
			 	$.each(res.errors, function (index, error) {
					$('#error_' + index).html(error).show();
				});
			 }else{
			 	window.parent.$('.next_billing_td').html(res.next_purchase_date);
			 	parent.setNotifySuccess(res.message);
				parent.$.colorbox.close();
			 }
			}
		});
	});
</script>