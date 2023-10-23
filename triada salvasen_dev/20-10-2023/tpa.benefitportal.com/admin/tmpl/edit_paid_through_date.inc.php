<div class="panel panel-default">
	<div class="panel-heading">
		<h4 class="mn">Paid Through Date</h4>
	</div>
	<div class="panel-body">
		<div class="theme-form">
			<form name="paid_date_form" id="paid_date_form" action="" method="POST">
				<input type="hidden" name="is_submit" value="Y">
				<input type="hidden" name="ws_id" value="<?=$ws_id?>">
			<div class="form-group">
				<div class="input-group">
				    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
				    <div class="pr">
				    <select name="paid_date" id="paid_date" class="form-control">
				    	<option></option>
				    	<?php foreach ($date_selection_options as $coverage) { ?>
                        	<option value="<?=$coverage['end_coverage_period'];?>"><?=date('m/d/Y',strtotime($coverage['end_coverage_period']));?></option>
                    	<?php } ?>
				    </select>
				    <label>Paid Through Date (MM/DD/YYYY)</label>
				    </div>
			    </div>
				    <p class="error"><span id="err_paid_date"><?=$error?></span></p>
			</div>
			<div class="text-center">
				<button id="submit" class="btn btn-action">Save</button>
				<a href="javascript:void(0);" class="btn red-link">Cancel</a>
			</div>
		</form>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		
		$(document).on('click','#submit',function(e){
			   e.preventDefault();
			   var params = $('#paid_date_form').serialize();
			   $('#ajax_loader').show();
			   $.ajax({
			   url: 'edit_paid_through_date.php',
			   type: 'POST',
			   data: params,
			   dataType: 'JSON',
			   success: function(res) {
			     $('#ajax_loader').hide();
			     if(res.status == 'fail'){
			         $('#err_paid_date').text(res.error);
			     }else{
			     	window.parent.$('#paid_date_td').html(res.paid_date);
			     	parent.setNotifySuccess(res.message);
			    	parent.$.colorbox.close();
			     }
			   }
			});
		});
	});
</script>