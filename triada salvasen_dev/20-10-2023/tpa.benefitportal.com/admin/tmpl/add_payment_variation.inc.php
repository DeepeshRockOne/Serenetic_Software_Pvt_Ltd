<div class="panel panel-default panel-block panel-title-block">
	<div class="panel-heading">
		<h4>Merchant Variation Details</h4>
	</div>
	<div class="panel-body theme-form">
		<div class="row">
			<div class="col-sm-3">
				<div class="form-group">
					<input type="text" class="form-control" name="">
					<label>Merchant Variation Name*</label>
				</div>
			</div>
			<div class="col-sm-3">
				<div class="form-group">
					<input type="text" class="form-control" name="">
					<label>Merchant ID*</label>
				</div>
			</div>
			<div class="col-sm-3">
				<div class="form-group">
					<input type="text" class="form-control" name="">
					<label>API Key*</label>
				</div>
			</div>
			<div class="col-sm-3">
				<div class="form-group">
					<select class="form-control">
						<option hidden=""></option>
						<option>123</option>
					</select>
					<label>Gateway Name*</label>
				</div>
			</div>
			<div class="col-sm-3">
				<div class="form-group">
					<input type="text" class="form-control" name="">
					<label>Monthly Threshold for Sales*</label>
				</div>
			</div>
			<div class="col-sm-9">
				<div class="form-group">
					<input type="text" class="form-control" name="">
					<label>Description</label>
				</div>
			</div>
		</div>
		<div class="">
			<h4 class="m-b-30">Settings</h4>
			<div class="m-b-30">
				<label><input type="checkbox" name="">Accept ACH</label>
			</div>
			<div class="m-b-30">
				<label><input type="checkbox" name="">Accept Credit/Debit</label>
			</div>
			<div class="m-b-30">
				<label><input type="checkbox" name="">Sales Threshold Alert</label>
			</div>
			<div class="m-b-30">
				<label><input type="checkbox" name="">Refund/Void Threshold Alert</label>
			</div>
			<div class="m-b-30">
				<label><input type="checkbox" name="">Chargeback Threshold Alert</label>
			</div>
		</div>
		<div class="m-b-30">
			<h4>Assign Agents</h4>
			<p>How would you like to assign agents to this Merchant Variation?</p>
			<div class="radio-inline">
				<label><input type="radio" name="">All Agents</label>
			</div>
			<div class="radio-inline">
				<label><input type="radio" name="">Specific Agent(s)</label>
			</div>
			<div class="m-t-25">
				<div class="row">
					<div class="col-sm-4">
						<div class="form-group">
							<select class="form-control">
								<option>123</option>
							</select>
							<label>Search Agent(s)</label>
						</div>
					</div>
				</div>
				<div class="table-responsive">
					<table class="<?=$table_class?>">
						<thead>
							<tr>
								<th width="150px;">Agent ID</th>
								<th width="150px;">Name</th>
								<th width="150px;" class="text-center">Include Downline?</th>
								<th width="150px;" class="text-center">Include LOA?</th>
								<th class="text-center" width="70px">Action</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>A1234567</td>
								<td>Eugene Carter</td>
								<td class="text-center"><input type="checkbox" name=""></td>
								<td class="text-center"><input type="checkbox" name=""></td>
								<td class="icons text-center">
									<a href="javascript:void(0)"><i class="fa fa-trash"></i></a>
								</td>
							</tr>
							<tr>
								<td>A1234567</td>
								<td>Eugene Carter</td>
								<td class="text-center"><input type="checkbox" name=""></td>
								<td class="text-center"><input type="checkbox" name=""></td>
								<td class="icons text-center">
									<a href="javascript:void(0)"><i class="fa fa-trash"></i></a>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="m-b-30">
			<h4>Assign Products</h4>
			<p>Would you like this merchant account to be for all products or only specific products?</p>
			<div class="radio-inline">
				<label><input type="radio" name="">All Products</label>
			</div>
			<div class="radio-inline">
				<label><input type="radio" name="">Specific Product(s)</label>
			</div>
			<div class="m-t-25">
				<div class="row">
					<div class="col-sm-4">
						<div class="form-group">
							<select class="form-control">
								<option>123</option>
							</select>
							<label>Search Product(s)</label>
						</div>
					</div>
				</div>
				<div class="table-responsive">
					<table class="<?=$table_class?>">
						<thead>
							<tr>
								<th width="150px;">Product ID</th>
								<th width="150px;">Name</th>
								<th width="150px;" class="text-center">Include Variation?</th>
								<th width="60px;" class="text-center">Action</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>BID_1500</td>
								<td>BrightIdea Dental 1500</td>
								<td class="text-center"><input type="checkbox" name=""></td>
								<td class="icons text-center">
									<a href="javascript:void(0)"><i class="fa fa-trash"></i></a>
								</td>
							</tr>
							<tr>
								<td>BID_1500</td>
								<td>BrightIdea Dental 1500</td>
								<td class="text-center"><input type="checkbox" name=""></td>
								<td class="icons text-center">
									<a href="javascript:void(0)"><i class="fa fa-trash"></i></a>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="m-b-40">
			<a href="javascript:void(0)" class="btn btn-info">Connect Processor</a>
			<a href="javascript:void(0)" class="bg_light_gray btn btn-">Test Processor</a>
		</div>
		<div class="text-center m-b-20">
			<a href="javascript:void(0)" class="btn btn-action">Save</a>
			<a href="payment_processor.php" class="btn text-red">Cancel</a>
		</div>
	</div>
</div>