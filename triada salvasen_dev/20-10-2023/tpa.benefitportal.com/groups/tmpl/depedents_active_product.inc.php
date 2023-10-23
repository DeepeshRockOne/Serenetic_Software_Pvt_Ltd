<div class="panel panel-default">
	<div class="panel-heading">
		<h4 class="mn">Dependent -  <span class="fw300"><?=$dep_info['fname'].' '.$dep_info['lname']?></span></h4>
	</div>
	<div class="panel-body">
		<div class="table-responsive br-n">
			<table cellspacing="0" cellpadding="0" class="table-theadless" width="100%">
				<tbody>
					<tr>
						<td>Active Products:</td>
						<td>Effective Date:</td>
					</tr>
					<?php if(!empty($customer_dep) && count($customer_dep) > 0) {
							foreach($customer_dep as $dep)  { ?>
						<tr>
							<td class="fw500 text-success"><?=$dep['name']?></td>
							<td><i><?=getCustomDate($dep['eligibility_date'])?></i></td>
						</tr>
					<?php } } else echo "<tr><td colspan='2'>No record found!</td></tr>" ?>
				</tbody>
			</table>
		</div>
		<div class="text-center">
			<a href="javascript:void(0)" class="btn red-link" onclick="parent.$.colorbox.close(); return false;">Close</a>
		</div>
	</div>
	</div>
</div>