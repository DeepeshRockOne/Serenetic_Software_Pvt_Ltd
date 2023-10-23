<div class="panel panel-default panel-block">
	<div class="panel-heading">
		<div class="panel-title">
			<h4 class="mn">Smart Tags</h4>
		</div>
	</div>
	<div class="panel-body">
		<!-- <h4 class="m-t-0 ">Admin</h4> -->
		<div class="text-right">
			<p class="text-action">Smart tags are case sensitive and should not be copied and pasted into content area as they will not work.</p>
		</div>
		<div class="table-responsive">
			<table class="<?=$table_class?>">
				<thead>
					<tr>
						<th width="40%">Smart Tag</th>
						<th width="60%">Description</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>[[Date]] </td>
						<td>displays the current date in format MM/DD/YYYY</td>
					</tr>
					<tr>
						<td>[[Day]] </td>
						<td>display the current day in format 1, 7, 10, 31</td>
					</tr>
					<tr>
						<td>[[Month]] </td>
						<td>display the current month in format January, March, July, August</td>
					</tr>
					<tr>
						<td>[[Name]] </td>
						<td>displays the primary agent full name or, if applicable, it would display agency name if account type is agency</td>
					</tr>
					<tr>
						<td>[[AgentType]] </td>
						<td>displays if agent type is "Agent" or "Agency"</td>
					</tr>
					<tr>
						<td>[[Address]] </td>
						<td>displays the primary agent full address (address 1, address 2, city, state, zip) or, if applicable, it would display agency full address if account is agency</td>
					</tr>
					<tr>
						<td>[[AGENT_ASSOCIATED_COMPANY]] </td>
						<td>Agent's Company</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>
