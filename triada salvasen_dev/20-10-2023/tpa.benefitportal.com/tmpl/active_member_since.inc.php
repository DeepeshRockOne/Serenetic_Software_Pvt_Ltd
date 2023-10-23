<div class="panel panel-default">
	<div class="panel-heading">
		<h4 class="mn">Active Member Since Date</h4>
	</div>
	<div class="panel-body">
		<div class="theme-form">
			<form name="active_form" id="active_form" action="" method="POST">
				<input type="hidden" name="is_submit" value="Y">
				<input type="hidden" name="ws_id" value="<?=$ws_id?>">
				<input type="hidden" name="location" value="<?=$location?>">
				<input type="hidden" name="customer_id" value="<?=$customer_id?>">
				<input type="hidden" name="product_id" value="<?=$product_id?>">
				<input type="hidden" name="plan_id" value="<?=$plan_id?>">
			<div class="form-group">
				<div class="input-group">
				    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
				    <div class="pr">
				    <input id="active_date" type="text" class="form-control" name="active_date" value="<?=$ws_row['eligibility_date'] ? date('m/d/Y',strtotime($ws_row['eligibility_date'])) : date('m/d/Y',strtotime($ws_row['created_at']))?>">
				    <label>Active Since Date (MM/DD/YYYY)</label>
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
	$("#active_date").datepicker({
            changeDay: true,
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            // endDate: '<?=date("m/d/Y", strtotime('now'))?>'
    });

	$(document).off('click','#submit');
    $(document).on('click','#submit',function(e){
	   e.preventDefault();
	   parent.disableButton($(this));
	   var params = $('#active_form').serialize();
	   $('#ajax_loader').show();
	   $.ajax({
	   url: 'active_member_since.php',
	   type: 'POST',
	   data: params,
	   dataType: 'JSON',
	   success: function(res) {
		 parent.enableButton($("#submit"));
	     $('#ajax_loader').hide();
	     if(res.status == 'fail'){
	         $('#err_effective_date').text(res.error);
	     }else{
	     	window.parent.$('.active_member_td').html(res.active_date);
	     	parent.setNotifySuccess(res.message);
	    	parent.$.colorbox.close();
	     }
	   }
	});
});
</script>