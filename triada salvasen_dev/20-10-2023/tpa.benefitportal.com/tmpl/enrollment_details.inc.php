<div class="panel-body pn enrollment_details_wrap">
	<div class="container">
		<h4 class="mb20 fw300 fs34 text-center">Enrollment Details</h4>
		<p class="mb50 text-center">Please review the information below and use the edit link provided to make any necessary changes or corrections. For further assistance, please contact your healthcare agent.</p>
	</div>
	<div class="enrollment_table_bg">
		<div class="container">
			<div class="enrollment_receipt bg_white">
		    <div class="row">
		      <div class="col-sm-3">
		        <div class="bg_dark_primary">
		          <div class="panel-body">
		            <h4 class="text-uppercase m-t-0 fs14 m-b-5" style="color: rgba(255,255,255,0.39);">Primary Details</h4>
		            <p class="text-white mn"><span class="fw700  fs18">Jeffrey Canfield</span><br>jeff@yahoo.com<br>(555) 123-4321<br> M12345678</p>
		          </div>
		        </div>
		          <div class="panel-body">
		            <h4 class="text-uppercase m-t-0 fs14 m-b-5" style="color: rgba(255,255,255,0.39);">DEPENDENTS</h4>
		            <p class="text-white mn">Spouse - Achaia <br>Canfield <br> (1) Child</p>
		          </div>
		          <div class="panel-body">
		            <h4 class="text-uppercase m-t-0 fs14 m-b-5" style="color: rgba(255,255,255,0.39);">Billing</h4>
		            <p class="text-white mn">Jeffrey Canfield <br> VISA *1114</p>
		          </div>
		          <div class="panel-body">
		          	<a href="enrollment_summary_edit.php" class="enrollment_summary_edit btn bg_white">edit</a>
		        </div>
		      </div>
		      <div class="col-sm-9">
		        <div class="p-10">
		          <p class="fw500 pull-left fs18"> Summary</p>
		          <p class="pull-right"><strong>04/30/2019</strong></p>
		          <div class="clearfix"></div>
		          <div class="table-responsive">
		            <table class="table table-borderless table-striped">
		              <thead>
		                <tr>
		                  <th>Product</th>
		                  <th>Coverage Period</th>
		                  <th>Coverage</th>
		                  <th class="text-right">Total</th>
		                </tr>
		              </thead>
		              <tbody>
		                <tr>
		                  <td>Critical Illness</td>
		                  <td>05/01/2019 - 05/31/2019</td>
		                  <td>Member</td>
		                  <td class="text-right">$59.99</td>
		                </tr>
		              </tbody>
		            </table>
		            <table class="table table-borderless pull-right  receipt_table m-t-20" style="max-width: 250px;">
		              <tbody>
		              	<tr>
		              		<td>SubTotal(s)</td>
		              		<td class="text-right">$43.23</td>
		              	</tr>
		                <tr>
		                  <td>Healthy Step - 2</td>
		                  <td class="text-right">$20.96</td>
		                </tr>
		                <tr>
		                  <td>Service Fee(s)</td>
		                  <td class="text-right">$7.00</td>
		                </tr>
		                <tr>
		                  <td>Discount</td>
		                  <td class="text-right">$0.00</td>
		                </tr>
		                <tr>
		                  <td class="fw500">Total</td>
		                  <td class="text-right fw500">$239.96</td>
		                </tr>
		              </tbody>
		            </table>
		          </div>
		        </div>
		      </div>
		    </div>
		  </div>
		</div>
	</div>
	<div class="container">
		<h4 class="m-t-30 m-b-30">Access Portal</h4>
		<p class="m-b-30"><em>As a member, you receive a personalized member portal to access your account, view your benefits, and manage your coverages. Set a password below to access your portal:</em></p>
		<div class="theme-form">
			<div class="row">
				<div class="col-sm-6">
					<div class="form-group">
						<input type="password" class="form-control" name="">
						<label>Password*</label>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group">
						<input type="password" class="form-control" name="">
						<label>Confirm Password*</label>
					</div>
				</div>
			</div>
		</div>
		<h4 class="m-t-50">Electronic Signature</h4>
		<p class="m-t-40">I agree that I have a fill and complete understanding of the products for which I am electing and I am the applicant listed above. I accept the following:</p>
		<div class="m-t-40">
			<div><label><input type="checkbox" name=""><span class="p-l-10">BrightIdea Dental 1500</span></label></div>
			<div><label><input type="checkbox" name=""><span class="p-l-10">BrightIdea Dental 1500</span></label></div>
		</div>
		<hr>
		<label class="label-input"><input type="checkbox" name=""><span class="p-l-10">I acknowledge that I have read and agree to the <a class="text-red font-bold" href="javascript:void(0);">terms and conditions </a> in this agreement.</span></label>
		<div class="text-center">
			<a href="javascript:void(0)" class="btn btn-action">Enroll</a>
			<a href="javascript:void(0)" class="btn red-link">Cancel Plan</a>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		$('.enrollment_summary_edit').colorbox({
			iframe:true,
			width:"80%;",
			height:"500px;",
		});
	});
</script>