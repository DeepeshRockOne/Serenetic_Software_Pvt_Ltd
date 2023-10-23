<div class="panel panel-default panel-block">
	<div class="panel-heading">
		<div class="panel-title">
			<p class="fs18">
				<strong class="fw500"><?=$name . ' ('.$display_id .')'?> </strong>
			</p>
		</div>
	</div>	
	<div class="panel-body">
		<div class="table-responsive">
			<table class="<?=$table_class?>">
				<thead>
					<tr>
						<th width="250px">Fee</th>
						<th>Plan Tier</th>
					</tr>
				</thead>
				<tbody>
					<?php if (!empty($fetch_rows)) { ?>
						<?php foreach ($fetch_rows as $rows) { ?>
							<tr>
								<td>
									<?php 
					                  $prd_price = '$'.$rows['price'];
					                  if($rows['price_calculated_on'] == 'Percentage'){
					                    $prd_price=(floor($rows['price']*100)/100).'%';
					                  } 
					                ?>
									<?php echo $prd_price ?> 
								</td>
								<td><?php echo $prdPlanTypeArray[$rows['plan_type']]['title'] ?></td>
							</tr>
						<?php } ?>
					<?php } else { ?>
					<tr>
						<td colspan="2">No record(s) found</td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
			<div class="text-center">
				<a href="javascript:void(0);" class="btn red-link">Close</a>
			</div>
		</div>
	</div>
	
</div>