<style type="text/css">
	.table thead:first-child tr:first-child th { white-space: nowrap; }
</style>
<div class="panel panel-default panel-block">
	<div class="panel-heading">
		<?php if(!empty($ws_row)) { ?>
		<h4 class="mn"><?=$ws_row['customer_name']?> - <span class="fw300"> <?=$ws_row['product_name']?> Dependent(s)</span></h4>
		<?php } else { ?>
			<h4 class="mn"><span class="fw300">Dependent(s)</span></h4>
		<?php } ?>
	</div>
	<div class="panel-body">
		<div class="table-responsive">
			<table class="<?=$table_class?>">
				<thead>
					<tr>
						<th>Added Date</th>
						<th>First Name</th>
						<th>Last Name</th>
						<th>Relationship</th>
						<th>Date of Birth</th>
						<th>Age</th>
						<th width="130px">Effective Date</th>
					</tr>
				</thead>
				<tbody>
					<?php 
						if(!empty($dep_res)) {
							foreach ($dep_res as $key => $dep_row) {
								?>
								<tr>
									<td><?=strtotime($ws_row['purchase_date']) > 0 ? date('m/d/Y',strtotime($ws_row['purchase_date'])) : '';?></td>
									<td><?=$dep_row['fname']?></td>
									<td><?=$dep_row['lname']?></td>
									<td><?=$dep_row['crelation']?></td>
									<td><?=date('m/d/Y',strtotime($dep_row['birth_date']));?></td>
									<td><?=calculateAge($dep_row['birth_date'])?></td>
									<td><?=strtotime($ws_row['eligibility_date']) > 0 ? date('m/d/Y',strtotime($ws_row['eligibility_date'])) : '';?></td>
								</tr>
								<?php
							}
						} else {
							?>
							<tr><td colspan="7" class="text-center">Dependent(s) not found.</td></tr>
							<?php
						}
					?>
				</tbody>
			</table>
		</div>
		<div class="text-center">
			<a href="javascript:void(0)" class="btn red-link" onclick="parent.$.colorbox.close(); return false;">Close</a>
		</div>
	</div>
</div>