<div class="panel panel-default panel-block">
	<div class="panel-heading">
		<h4 class="mn"><?=$tr_display_id?> -  <span class="fw300"> Reassigned (<?=$name?>)</span></h4>
	</div>
	<div class="panel-body theme-form">
		<form action="" name="assign_new_group" id="assign_new_group">
			<p class="text-center m-t-10 m-b-25">You have reassigned this E-Ticket to a different group. Please finish reassigning this ticket below.</p>
			<div class="form-group">
				<select class="form-control" data-live-search="true" id="assigned_id" name="assigned_id">
				<optgroup label="GROUP - <?=$name?>">
					<?php if(!empty($assigned_admins)){
						foreach($assigned_admins as $admin){ ?>
								<option value="<?=$admin['id']?>"><?=$admin['fname'].' '.$admin['lname']?></option>
						<?php }
					}?>
				</optgroup>
				</select>
				<label>Assignee</label>
			</div>
			<input type="hidden" name="tracking_id" value="<?=$tracking_id?>" id="tracking_id">
			<input type="hidden" name="category_id" value="<?=$category_id?>" id="category_id">
			<input type="hidden" name="is_ajaxed" value="1" id="is_ajaxed">
			<div class="text-center">
				<a href="javascript:void(0);" class="btn btn-action" id="save_assignee">Save</a>
				<a href="javascript:void(0);" class="btn red-link" onclick="parent.$.colorbox.close(); return false;">Close</a>
			</div>
		</form>
	</div>
</div>
<script type="text/javascript">
$(document).off('click','#save_assignee');
$(document).on('click','#save_assignee',function(e){
	var tracking_id = $("#tracking_id").val();
	$.ajax({
		url:'reassigned_etickets.php',
		data:$("#assign_new_group").serialize(),
		dataType:'json',
		type:'post',
		beforeSend : function(e){
			$("#ajax_loader").show();
		},
		success : function(res){
			$("#ajax_loader").hide();
			parent.window.$.colorbox.close();
			if(res.status == 'success'){
				parent.setNotifySuccess(res.msg);
				parent.ajax_submit();
			}else{
				parent.setNotifyError(res.msg);
				parent.ajax_submit();
			}
		}
	});
});
</script>