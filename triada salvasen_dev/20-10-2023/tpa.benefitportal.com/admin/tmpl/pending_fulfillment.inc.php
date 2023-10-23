<div class="panel panel-default panel-block">
	<div class="panel-heading">
		<h4 class="mn">ASH File -  <span class="fw300">(14) Pending Records</span></h4>
	</div>
	<div class="panel-body">
		<div class="clearfix m-b-15">
			<div class="pull-left">
				<div class="form-inline" id="DataTables_Table_0_length top_paginate_cont">
					<div class="form-group mn">
						<label for="user_type">Records Per Page </label>
					</div>
					<div class="form-group mn">
						<select size="1" id="pages" name="pages" class="form-control" >
							<option>10</option>
							<option>25</option>
							<option>50</option>
							<option>100</option>
						</select>
					</div>
				</div>
			</div>
			<div class="pull-right">
				<div class="note_search_wrap" id="search_div" style="display: none; max-width: 100%;">
					<div class="phone-control-wrap theme-form">
						<div class="phone-addon">
							<div class="form-group height_auto mn">
								<a href="javascript:void(0);" class="search_close_btn text-light-gray">X</a>
							</div>
						</div>
						<div class="phone-addon w-300">
							<div class="form-group height_auto mn">
								<input type="text" name="" class="form-control">
								<label>Member ID</label>
							</div>
						</div>
						<div class="phone-addon w-80">
							<div class="form-group height_auto mn">
								<a href="javascript:void(0);" class="btn btn-info">Search</a>
							</div>
						</div>
					</div>
				</div>
				<a href="javascript:void(0);" class="search_btn" ><i class="fa fa-search fa-lg text-blue"></i></a>
			</div>
		</div>
		<table data-toggle="table" data-height="360" data-mobile-responsive="true" class="<?=$table_class?>">
			<thead>
				<tr>
					<th>ID/Member Name</th>
					<th class="col-sm-4">ID/Product Name</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><a href="javascript:void(0);" class="fw500 text-action">M11111111<br></a>Howard Davidson</td>
					<td><a href="javascript:void(0);" class="fw500 text-action">BID_1500<br></a>BrightIdea Dental 1500</td>
				</tr>
				<tr>
					<td><a href="javascript:void(0);" class="fw500 text-action">M11111111<br></a>Howard Davidson</td>
					<td><a href="javascript:void(0);" class="fw500 text-action">BID_1500<br></a>BrightIdea Dental 1500</td>
				</tr>
				<tr>
					<td><a href="javascript:void(0);" class="fw500 text-action">M11111111<br></a>Howard Davidson</td>
					<td><a href="javascript:void(0);" class="fw500 text-action">BID_1500<br></a>BrightIdea Dental 1500</td>
				</tr>
				<tr>
					<td><a href="javascript:void(0);" class="fw500 text-action">M11111111<br></a>Howard Davidson</td>
					<td><a href="javascript:void(0);" class="fw500 text-action">BID_1500<br></a>BrightIdea Dental 1500</td>
				</tr>
				<tr>
					<td><a href="javascript:void(0);" class="fw500 text-action">M11111111<br></a>Howard Davidson</td>
					<td><a href="javascript:void(0);" class="fw500 text-action">BID_1500<br></a>BrightIdea Dental 1500</td>
				</tr>
				<tr>
					<td><a href="javascript:void(0);" class="fw500 text-action">M11111111<br></a>Howard Davidson</td>
					<td><a href="javascript:void(0);" class="fw500 text-action">BID_1500<br></a>BrightIdea Dental 1500</td>
				</tr>
				<tr>
					<td><a href="javascript:void(0);" class="fw500 text-action">M11111111<br></a>Howard Davidson</td>
					<td><a href="javascript:void(0);" class="fw500 text-action">BID_1500<br></a>BrightIdea Dental 1500</td>
				</tr>
				<tr>
					<td><a href="javascript:void(0);" class="fw500 text-action">M11111111<br></a>Howard Davidson</td>
					<td><a href="javascript:void(0);" class="fw500 text-action">BID_1500<br></a>BrightIdea Dental 1500</td>
				</tr>
				<tr>
					<td><a href="javascript:void(0);" class="fw500 text-action">M11111111<br></a>Howard Davidson</td>
					<td><a href="javascript:void(0);" class="fw500 text-action">BID_1500<br></a>BrightIdea Dental 1500</td>
				</tr>
			</tbody>
		</table>
		<div class="row table-footer-row">
			<div class="col-sm-12">
				<div class="pull-left">
					<div class="dataTables_info">1 to 3 of 3 Records</div>
				</div>
				<div class="pull-right">
					<div class="dataTables_paginate paging_bs_normal">
						<ul class="pagination pagination-md">
							<li class="prev disabled"><span>&lt;</span></li>
							<li class="live-link active"><a href="javascript:void(0);">1</a></li>
							<li class="disabled"><span>&gt;</span></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
		<div class="text-center m-t-15">
			<a href="javascript:void(0)" class="red-link pn" onclick="window.parent.$.colorbox.close()">Cancel</a>
		</div>
	</div>
</div>

<script type="text/javascript">
$(document).ready(function(){
  $(document).on("click", ".search_btn", function(e) {
    e.preventDefault();
    $(this).hide();
    $("#search_div").css('display', 'inline-block');
  });
  $(document).on("click", ".search_close_btn", function(e) {
    e.preventDefault();
    $("#search_div").hide();
    $(".search_btn").show();
  });
});
</script>