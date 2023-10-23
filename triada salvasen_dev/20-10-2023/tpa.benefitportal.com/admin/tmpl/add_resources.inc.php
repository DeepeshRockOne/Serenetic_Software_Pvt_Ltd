<?php if(!empty($type)) { ?>
<div class="panel panel-default panel-block">
	<div class="panel-heading">
		<h4 class="mn">+ Resource - <span class="fw300"><?=ucfirst($type)?> Portal</span></h4>
	</div>
	<form action="" id="add_resources" name="add_resources">
		<input type="hidden" name="is_ajaxed" value="1">
		<input type="hidden" name="portal_type" value="<?=$type?>">
		<div class="panel-body">
			<h4 class="fs16 m-b-5">Select Module</h4>
			<p>Select the module that this resource will be added to.</p>
			<div class="theme-form">
				<div class="form-group">
					<select class="form-control" name="module_name" id="module_name" onchange="$('#resource_details').show();">
						<option value="" hidden selected ></option>
						<option value="dashboard">Dashboard</option>
						<?php if(!empty($resModule)){
							
								if($type !='member'){
									foreach($resModule as $module){ ?>
									<option value="<?=$module['title']?>"><?=$module['title']?></option>
								<?php } ?> 
							<?php } else{
									foreach($resModule as $key => $module){  ?>
									<option value="<?= $key ?>"><?= $module ?></option>
							<?php 	} //End Foreach
								} //else
							} //if resModule
						?>
					</select>
					<label>Select Module</label>
					<p class="error error_module_name"></p>
				</div>
				<div id="resource_details" style="display:none">
					<div class="form-group">
						<input type="text" name="resource_name" id="resource_name" class="form-control">
						<label>Resource Name</label>
						<p class="error error_resource_name"></p>
					</div>
					<p>PDF</p>
					<div class="form-group">
						<div class="custom_drag_control">
							<span class="btn btn-action" style="border-radius:0px;">Upload PDF</span>
							<input type="file" class="gui-file" id="" name="pdf_file" id="pdf_file">
							<input type="text" class="gui-input" placeholder="Choose File">
						</div>
						<p class="error error_pdf_file"></p>
					</div>
				</div>
			</div>
		</div>
		<div class="text-center">
			<a href="javascript:void(0)" class="btn btn-action" onclick="$('#add_resources').submit()">Save</a>
			<!-- <a href="javascript:void(0)" class="btn red-link">Back</a> -->
			<a href="javascript:void(0)" class="btn red-link" onclick="parent.$.colorbox.close()">Cancel</a>
		</div>
	</form>
</div>
<script type="text/javascript">
$('#add_resources').ajaxForm({
  beforeSend: function(e) {
      $("#ajax_loader").show();
  },
  beforeSubmit:function(arr, $form, options){
  },
  url:"add_resources.php",
  type: 'post',
  dataType: 'json',
  success: function(res) {
    $(".error").html("").hide();
      $("#ajax_loader").hide();
      if(res.status == 'success'){
        parent.$.colorbox.close();
		parent.location.reload();
		parent.setNotifySuccess(res.msg,true);
      }else if(res.status == 'fail'){
        parent.setNotifyError(res.msg);
      }else{
        $.each(res.errors, function(key, value) {
          $('.error_' + key).html(value).show();
        })
      }
  }
});
</script>
<?php } else echo "<p class='text-center'>Not found Any Group</p>"; ?>