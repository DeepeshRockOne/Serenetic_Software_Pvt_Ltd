<div class="panel panel-default panel-block">
	<div class="panel-heading">
		<h4 class="mn">+ Group</h4>
	</div>
	<div class="panel-body">
		<form action="" id="groupForm" name="groupForm">
		<input type="hidden" name="groupId" value="<?=checkIsset($_GET['id'])?>">
			<div class="theme-form">
				<div class="row">
					<div class="col-sm-3">
						<div class="form-group">
							<input type="text" name="groupName" id="groupName" value="<?=checkIsset($categoryRec['title'])?>" class="form-control">
							<label>Group Name</label>
							<p class="error error_groupName"></p>
						</div>
					</div>
					<div class="col-sm-9">
						<div class="form-group">
							<select class="se_multiple_select" name="assign_admins[]"  id="assign_admins" multiple="multiple" >
								<?php
									if(!empty($access_level_res)) {
										foreach ($access_level_res as $key => $access_level_row) {
											?>
											<optgroup label="<?=$access_level_row['name']?>">
											<?php
											foreach ($access_level_row['admin_res'] as $key2 => $admin_row) {
												?>
												<option value="<?=$admin_row['id']?>" <?=!empty($categoryRec['adminIds']) && in_array($admin_row['id'],explode(',',$categoryRec['adminIds'])) ? 'selected="selected"' : ''?>><?=$admin_row['display_id'].' - '.$admin_row['fname'].' '.$admin_row['lname'];?></option>		
												<?php
											}
											?>
											</optgroup>
											<?php
										}
									}
								?>
							</select>
							<label>Assign Admins</label>
							<p class="error error_assign_admins"></p>
						</div>
					</div>
				</div>
			</div>
			<div id="e_ticket_group_admin_div"></div>
			<div class="text-center m-t-20">
				<a href="javascript:void(0);" class="btn btn-action" id="saveGroup">Save</a>
				<a href="javascript:void(0);" class="btn red-link" onclick='parent.$.colorbox.close(); return false;'>Close</a>
			</div>
		</form>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function() {
	$("#assign_admins").multipleSelect({
	   selectAll: false,
	   onChange: function () {
      	ajax_get_e_ticket_group_admin();
      },
      onClick: function (view) {
      	ajax_get_e_ticket_group_admin();
      },
      onOptgroupClick: function (view) {
      	ajax_get_e_ticket_group_admin();
      },
      onCheckAll: function () {
      	ajax_get_e_ticket_group_admin();
      },
      onUncheckAll: function () {
      	ajax_get_e_ticket_group_admin();
	  },
	  onTagRemove:function(){
		ajax_get_e_ticket_group_admin();
	  }
	});
	<?php if(!empty($categoryRec)) { ?>
		ajax_get_e_ticket_group_admin();
	<?php } ?>

});

$(document).off("click","#saveGroup");
$(document).on("click","#saveGroup", function(){
	$(".error").hide();
	$.ajax({
			url: 'ajax_add_etickets_groups.php',
			type: 'POST',
			data: $("#groupForm").serialize(),
			dataType : 'json',
			beforeSend:function(){
				$("#ajax_loader").show();
			},
			success: function(res) {
				$('#ajax_loader').hide();
				if(res.status == 'success'){
					parent.$.colorbox.close();
					parent.get_eticket_groups();
					parent.setNotifySuccess(res.msg);
				}else{
					$.each(res.errors, function(key, value) {
						$('.error_' + key).html(value).show();
					});
				}
			}
		});
});

$(document).off("click",".adminSelected");
$(document).on("click",".adminSelected", function(){
	$adminId=$(this).attr('data-id');
	$("#assign_admins option[value='"+$adminId+"']").prop("selected", false);
	$("#assign_admins").multipleSelect("refresh");
	ajax_get_e_ticket_group_admin('N');
});

ajax_get_e_ticket_group_admin = function(is_edit){
	$adminIds = $("#assign_admins").multipleSelect('getSelects');	
	if($adminIds.length > 0){
			$.ajax({
			url: 'ajax_get_e_ticket_group_admin.php',
			type: 'POST',
			data: {
				is_ajaxed: 1,
				adminIds : $adminIds,
			},
			dataType : 'json',
			beforeSend:function(){
				$("#ajax_loader").show();
			},
			success: function(res) {
				$('#ajax_loader').hide();
				if(res.status == 'success'){
					$('#e_ticket_group_admin_div').html(res.data_html).show();
				}else{
					$('#e_ticket_group_admin_div').html('').hide();
				}
			}
		});
	}else{
		$('#e_ticket_group_admin_div').html('').hide();
	}
}
</script>