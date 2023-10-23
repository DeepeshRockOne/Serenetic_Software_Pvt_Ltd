<div class="panel panel-default panel-block">
	<div class="panel-heading">
		<h4 class="mn">Edit Group - <span class="fw300">Communications</span></h4>
	</div>
	<div class="panel-body">
		<div class="theme-form">
			<div class="row">
				<div class="col-sm-3">
					<div class="form-group">
						<input type="text" name="" class="form-control">
						<label>Group Name</label>
					</div>
				</div>
				<div class="col-sm-9">
					   <div class="form-group">
			              <select class="se_multiple_select" name=""  id="assign_admins" multiple="multiple" >
			                <option>Calvin Kelley (AD11111)</option>
			                <option>Alice Moody (AD11112)</option>
			                <option>Victoria Hudson (AD11113)</option>
			                <option>Betty Cobb (AD11114)</option>
			                <option>Jim Hardy (AD11115)</option>
			              </select>
			              <label>Assign Admins</label>
			            </div>
				</div>
			</div>
		</div>
		<div class="table-responsive">
			 <table class="<?=$table_class?>">
			 	<thead>
			 		<tr>
			 			<th>Admin ID</th>
			 			<th>Name</th>
			 			<th>Status</th>
			 			<th class="text-center" width="70px">Actions</th>
			 		</tr>
			 	</thead>
			 	<tbody>
			 		<tr>
			 			<td><a href="javascript:void(0);" class="fw500 text-action">AD11111</a></td>
			 			<td>Calvin Kelley</td>
			 			<td>Active</td>
			 			<td class="text-center icons"><a href="javascript:void(0);"><i class="fa fa-times-circle" aria-hidden="true"></i></a></td>
			 		</tr>
			 		<tr>
			 			<td><a href="javascript:void(0);" class="fw500 text-action">AD11112</a></td>
			 			<td>Alice Moody</td>
			 			<td>Active</td>
			 			<td class="text-center icons"><a href="javascript:void(0);"><i class="fa fa-times-circle" aria-hidden="true"></i></a></td>
			 		</tr>
			 	</tbody>
			 </table>
		</div>
		<div class="text-center m-t-20">
			<a href="javascript:void(0);" class="btn btn-action">Save</a>
			<a href="javascript:void(0);" class="btn red-link" onclick='parent.$.colorbox.close(); return false;'>Close</a>
		</div>
	</div>
</div>

<script type="text/javascript">
$(document).ready(function() {
	$("#assign_admins").multipleSelect({
	   selectAll: false,
	});
});
</script>