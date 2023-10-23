<div class="panel panel-block panel-default">
	<div class="panel-heading">
		<h4 class="mn">+ Eligibility File</h4>
	</div>
	<div class="panel-body">
		<div class="pull-right">
			<a href="javascript:void(0)" class="m-b-25 btn btn-action-o">Download Specifications File</a>
		</div>
		<div class="form-group">
			<input type="text" class="form-control" name="">
		</div>
		<div class="theme-form">
			<div class="form-group">
					<select class="se_multiple_select" id="elegibility_product" multiple="multiple">
						<optgroup label="ACCIDENT">
							<option>Accident Product (A&DD_001)</option>
						</optgroup>
						<optgroup label="DENTAL">
							<option>BrightIdea Dental 1500 (BID_1500)</option>
							<option>BrightIdea Dental 3000 (BID_3000)</option>
							<option>BrightIdea Dental 5000 (BID_5000)</option>
						</optgroup>
						<optgroup label="TELEHEALTH">
							<option>Telehealth</option>
						</optgroup>
					</select>
			</div>
		</div>
		<div class="text-center">
			<a href="eligibility_generator.php" class="btn btn-action">Save</a>
			<a href="javascript:void(0)"  onclick="window.parent.$.colorbox.close()" class="btn text-red">Cancel</a>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		$("#elegibility_product").multipleSelect({
	       selectAll: false,
	  });
	});
</script>