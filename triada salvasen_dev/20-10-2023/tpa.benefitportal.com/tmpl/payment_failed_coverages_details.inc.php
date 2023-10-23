<div class="panel panel-default panel-block">
	<div class="panel-heading">
		<div class="panel-title">
			<p class="fs18">
				</strong> <span class="fw300"> Coverage Details</span> 
			</p>
		</div>
	</div>
	<div class="panel-body">
		<div class="table-responsive">
			<table class="<?=$table_class?>">
				<thead>
					<tr>
						<th>Order ID</th>
						<th>Product ID/Name</th>  
						<th>Coverage Period</th>  
						<th style="width: 130px;">Status</th>
					</tr>
				</thead>
				<tbody>
					<?php if (!empty($res["coverageData"])) { ?>
						<?php foreach ($res["coverageData"] as $rows) { ?>
							<tr>
								<td><a href="javascript:void(0);" class="text-red fw500 odrReceipt" data-odrid="<?=md5($rows['odrId'])?>"><?=$rows['odrDispId']?></a></td>
								<td><a href="javascript:void(0);" class="text-red fw500"><?=$rows['prdCode']?></a>
									<br><?=$rows['prdName']?></td>
								<td><?=displayDate($rows['start_coverage_period'])?> - <?=displayDate($rows['end_coverage_period'])?></td>
								<td><?=$rows['odrStatus']?></td>  
							</tr>
						<?php } ?>
					<?php } else { ?>
					<tr>
						<td colspan="4">No record(s) found</td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
		<div class="text-center">
			<a href="javascript:void(0);" class="btn red-link" onclick="parent.$.colorbox.close();">Close</a>
		</div>
	</div>
</div>
	
<script type="text/javascript">
    $(document).ready(function() {
		$(document).off('click', '.odrReceipt');
		$(document).on('click', '.odrReceipt', function(e) {
		  var odrId = $(this).attr("data-odrId");
		  $href = "order_receipt.php?orderId="+odrId;
		    var not_win = window.open($href, "myWindow", "width=1024,height=630");
		    if(not_win.closed) {  
		      alert('closed');  
		    } 
		});
    });
</script>
