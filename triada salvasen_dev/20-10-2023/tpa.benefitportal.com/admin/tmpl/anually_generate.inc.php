<style type="text/css">
.eligibil_popup .cust_tab_ui ul.nav-tabs li{height: 80px;}
.eligibil_popup .cust_tab_ui ul.nav-tabs li a {	border: 1px solid #E0E0E0;}
</style>
<div class="panel panel-default panel-block">
	<div class="panel-heading">
		<h4 class="mn">Manually Generate Eligibility File - <span class="fw300">HMA File</span></h4>
	</div>
	<div class="panel-body eligibil_popup">
		<div class="cust_tab_ui">
			<ul class="nav nav-tabs nav-noscroll" role="tablist">
		    <li role="presentation" class="active"><a href="#full_name" class="fs16" aria-controls="full_name" role="tab" data-toggle="tab">Full File</a></li>
		    <li role="presentation"><a href="#add_change_file" aria-controls="add_change_file" role="tab" data-toggle="tab">Add/Change/Delete <br>File</a></li>
		    <li role="presentation"><a href="#specific_member" aria-controls="specific_member" role="tab" data-toggle="tab">Specific Members<br> Only</a></li>
		    <li role="presentation"><a href="#agent_downline" aria-controls="agent_downline" role="tab" data-toggle="tab">Specific Agent & their <br> Downline</a></li>
		  </ul>
	  	<div class="tab-content mn">
				<div class="tab-pane active" id="full_name">
					<div class="theme-form">
						<div class="row">
							<div class="col-sm-6">
									<h5 class="m-t-30 m-b-15">Generate Via</h5>
								<div class="theme-form">
									<div class="form-group">
										<div class="input-group">
											<div class="input-group-addon">
												<i class="fa fa-calendar"></i>
											</div>
											<input type="text" class="form-control" name="">
										</div>
									</div>
									<h5 class="m-b-15">Generate Via</h5>
									<div class="form-group">
										<select class="form-control">
											<option><a href="javascript:void(0);" class="">Download</a></option>
											<option><a href="javascript:void(0);" class="email_show"> Email</a></option>
											<option><a href="javascript:void(0);" class="">FTP</a></option>
										</select>
										<label>Select</label>
									</div>
								</div>
							</div>
							<div class="">
								<div class="" id="generator_email_show" style="display:none ">
									<div class="row">
										<div class="col-sm-6">
											<div class="form-group">
												<input type="text" class="form-control" name="">
												<label>Enter Email Address</label>
											</div>
										</div>
										<div class="col-sm-6">
											<div class="form-group">
												<input type="text" class="form-control" name="">
												<label>Enter Email Address</label>
											</div>
										</div>
									</div>
								</div>
								<div class="" id="generator_email_show" style="display:none; ">
									<div class="row">
										<div class="col-sm-12">
											<input type="text" class="form-control" name="" value="Teladoc FTP">
											<label>Destination</label>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="tab-pane" id="add_change_file">
					<div class="row">
						<div class="col-sm-6">
							<div class="form-group">
								<div class="row">
									<div class="m-t-30">
										<div class="col-sm-6">
											<h5>Since</h5>
											<div class="form-group">
												<div class="input-group">
													<div class="input-group-addon">
														<i class="fa fa-calendar"></i>
													</div>
													<input type="text" class="form-control" name="">
												</div>
											</div>
										</div>
										<div class="col-sm-6">
											<h5>Active Through</h5>
											<div class="form-group">
												<div class="input-group">
													<div class="input-group-addon">
														<i class="fa fa-calendar"></i>
													</div>
													<input type="text" class="form-control" name="">
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="form-group">
								<h5 class="m-b-15">Generate Via</h5>
								<select class="form-control">
									<option>Download</option>
									<option>Email</option>
									<option>FTP</option>
								</select>
								<label>Select</label>
							</div>
						</div>
					</div>
				</div>
				<div class="tab-pane" id="specific_member">
					<div class="row">
						<div class="col-sm-12">
							<h5 class="m-t-30">Add Members by Name or ID:</h5>
							<div class="form-group">
								<select class="se_multiple_select" id="member_name_id">
									<option>Search Members by Name or ID</option>
								</select>
							</div>
						</div>
						<div class="col-sm-6">
							<h5 class="m-b-15">Generate Via</h5>
							<div class="form-group">
								<select class="form-control">
									<option>Download</option>
									<option>Email</option>
									<option>FTP</option>
								</select>
								<label>Select</label>
							</div>
						</div>
					</div>
				</div>
				<div class="tab-pane" id="agent_downline">
					<div class="row">
						<div class="col-sm-12">
							<h5 class="m-t-30">Select Agent</h5>
							<div class="form-group">
								<select class="se_multiple_select" id="elegibility_agent_name_id">
									<option>Jeffrey Canfield (A123444)</option>
								</select>
							</div>
						</div>
					</div>
					<h5 class="m-b-15">Include Downline</h5>
					<div class="table-responsive">
						<table class="<?=$table_class?>">
							<thead>
								<tr>
									<th width="50px"><input type="checkbox" name=""></th>
									<th>Check All</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<th><input type="checkbox" name=""></th>
									<th>Jeffrey Canfield</th>
								</tr>
							</tbody>
						</table>
					</div>
					<h5 class="m-t-25 m-b-15">Generate Via</h5>
					<div class="row">
						<div class="col-sm-6">
							<div class="form-group">
								<select class="form-control">
									<option>Download</option>
									<option>Email</option>
									<option>FTP</option>
								</select>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="text-center m-t-30">
			<a href="javascript:void(0);" class="btn btn-action">Generate File</a>
			<a href="javascript:void(0);"  onclick="window.parent.$.colorbox.close()" class="btn text-red">Cancel</a>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		$('#myTabs a').click(function (e) {
		  e.preventDefault()
		  $(this).tab('show')
		});

	  $("#member_name_id, #elegibility_agent_name_id").multipleSelect({
	       selectAll: false,
	  });
	});

</script>