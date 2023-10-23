<div class="panel panel-default panel-block panel-space">
	<form name="crclFrom" id="crclFrom">
		<input type="hidden" name="circle_id" id="circle_id" value="<?=checkIsset($_GET['id'])?>">
		<div class="panel-heading">
			<div class="panel-title">
				<p class="fs18 mn"><strong class="fw500">New Circles</strong></p>
			</div>
		</div>
		<div class="panel-body theme-form">
			<div class="row">
				<div class="col-sm-6">
					<div class="form-group">
						<input type="text" name="circle_name" class="form-control" value="<?=$circleName?>">
						<label>Name Circle</label>
						<p class="error error_circle_name"></p>
					</div>
					<p class="fw500">Invite</p>
					<div class="form-group ">
						<select class="se_multiple_select" name="invite_admins[]"  id="invite_admins" multiple="multiple" >
							<?php if(count($activeAdmin) > 0) {
								foreach($activeAdmin as $admin){ ?>
								<option value="<?= $admin['id'] ?>" <?=in_array($admin['id'],$circlArr) ? 'selected' : ''?>><?=$admin['display_id'].' - '.$admin['fname'].' '.$admin['lname']?></option>
							<?php } } ?>
						</select>
						<label>Invite Admins</label>
						<p class="error error_invite_admins"></p>
					</div>
					<p class="fw500">Status</p>
					<div class="form-group">
						<select class="form-control" name="status" id="status">
							<option></option>
							<option value="Active" <?=$circleStatus == 'Active' ? 'selected' : '' ?>>Active</option>
               				<option value="Inactive" <?=$circleStatus == 'Inactive' ? 'selected' : '' ?>>Inactive</option>
						</select>
						<label>Set Status</label>
						<p class="error error_status"></p>
					</div>
				</div>
			</div>
			<hr class="m-t-0">
			<div class="clearfix p-t-5">
				<!-- <a href="javascript:void(0);" data-href="circle_chat.php" class="btn btn-action" id="chat_window" >Save</a> -->
				<a href="javascript:void(0);" class="btn btn-action" id="savel_circle" >Save</a>
				<a href="communication_circle.php" class="btn red-link">Cancel</a>
			</div>
		</div>
	</form>
</div>
<script type="text/javascript">
$(document).ready(function(){
//   var not_win = '';
//     $("#chat_window").on('click',function(){
//     $href = $(this).attr('data-href');
//     var not_win = window.open($href, "myWindow", "width=900,height=650");
//     if(not_win.closed) {  
//       alert('closed');  
//   } 
//    });		
  $("#invite_admins").multipleSelect({
     selectAll: false,
  });
});
$(document).off('click','#savel_circle');
$(document).on('click','#savel_circle',function(e){
	e.preventDefault();
	$.ajax({
		url:'ajax_save_circle.php',
		data:$("#crclFrom").serialize(),
		dataType:'json',
		type:'post',
		beforeSend : function(e){
			$("#ajax_loader").show();
			$(".error").html('');
		},
		success :function(res){
			$("#ajax_loader").hide();
			if(res.status == 'success'){
				setNotifySuccess(res.message);
				
				window.open('circle_chat.php', "myWindow", "width=900,height=600");
				window.location = "communication_circle.php";
			}else{
				if(res.status === 'error'){
					$.each(res.errors,function(i,val){
						$(".error_"+i).text(val).show();
					});
				}	
			}
		}
	});

});
</script>