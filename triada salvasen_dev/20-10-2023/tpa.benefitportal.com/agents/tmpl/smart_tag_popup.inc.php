<div class="panel panel-default panel-block">
	<div class="panel-heading">
		<div class="panel-title">
			<h4 class="mn">Smart Tags</h4>
		</div>
	</div>
	<div class="panel-body">
		<h4 class="m-t-20 ">Agent</h4>
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
						<td>[[AgentName]] </td>
						<td>Agent's Full Name</td>
					</tr>
					<tr>
						<td>[[AgentID]] </td>
						<td>Agent's ID</td>
					</tr>
					<tr>
						<td>[[AgentLevel]] </td>
						<td>Agent’s Level</td>
					</tr>
					<tr>
						<td>[[AgencyName]] </td>
						<td>Agency Name (if applicable)</td>
					</tr>
					<tr>
						<td>[[AgentEmail]] </td>
						<td>Agent's Email Address</td>
					</tr>
					<tr>
						<td>[[AgentPhone]] </td>
						<td>Agent's Phone Number</td>
					</tr>
					<tr>
						<td>[[AgentStatus]] </td>
						<td>Agent’s Status</td>
					</tr>
					<tr>
						<td>[[AgentUsername]] </td>
						<td>Agent’s Unique Username</td>
					</tr>
					<tr>
						<td>[[AgentEOAmount]] </td>
						<td>Agent’s E&O Amount</td>
					</tr>
					<tr>
						<td>[[AgentEOExpiration]] </td>
						<td>Agent’s E&O Expiration Date</td>
					</tr>
					<tr>
						<td>[[AgentPublicDisplay]] </td>
						<td>Agent's Display Name, Display Email, Display Phone</td>
					</tr>
					<tr>
						<td>[[AgentActiveProducts]] </td>
						<td>Product A Name, Product B Name, Product C Name</td>
					</tr>
					<tr>
						<td>[[AgentActiveLicense]] </td>
						<td>Agent License State, License Active Date, License Expiration Date, License Type, Lines of Authority of licenses with active status</td>
					</tr>
					<tr>
						<td>[[AgentInactiveLicense]] </td>
						<td>Agent License State, License Active Date, License Expiration Date, License Type, Lines of Authority of licenses with inactive status</td>
					</tr>
					<tr>
						<td>[[ParentAgentName]] </td>
						<td>Parent Agent's Full Name</td>
					</tr>
					<tr>
						<td>[[ParentAgentID]] </td>
						<td>Parent Agent's ID</td>
					</tr>
					<tr>
						<td>[[TreeAgentName]] </td>
						<td>Highest Upline Agent's Full Name</td>
					</tr>
					<tr>
						<td>[[TreeAgentID]] </td>
						<td>Highest Upline Agent's ID</td>
					</tr>
					<tr>
						<td>[[AgentPortal]] </td>
						<td>Agent’s Portal Login</td>
					</tr>
					<tr>
						<td>[[AgentServicesInfo]] </td>
						<td>Pulls content on system setup for Agent ServicesGroup</td>
					</tr>
					<tr>
						<td>[[AGENT_ASSOCIATED_COMPANY]] </td>
						<td>Agent's Company</td>
					</tr>
				</tbody>
			</table>
		</div>
		<h4 class="m-t-20 ">Lead</h4>
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
						<td>[[LeadName]] </td>
						<td>Lead’s Full Name</td>
					</tr>
					<tr>
						<td>[[LeadCompanyName]] </td>
						<td>Lead’s Company Name</td>
					</tr>
					<tr>
						<td>[[LeadID]] </td>
						<td>Lead’s ID</td>
					</tr>
					<tr>
						<td>[[LeadEmail]] </td>
						<td>Lead’s Email Address</td>
					</tr>
					<tr>
						<td>[[LeadPhone]] </td>
						<td>Lead’s Phone Number</td>
					</tr>
					<tr>
						<td>[[LeadState]] </td>
						<td>Lead’s State</td>
					</tr>
					<tr>
						<td>[[LeadStatus]] </td>
						<td>Lead’s Status</td>
					</tr>
					<tr>
						<td>[[LeadTag]] </td>
						<td>Lead’s Tag</td>
					</tr>
				</tbody>
			</table>
		</div>
		
		
		<?php /*<h4 class="m-t-0 ">Admin - Smart tags are case sensitive</h4>
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
						<td>[[AdminName]] </td>
						<td>Admin’s Full Name</td>
					</tr>
					<tr>
						<td>[[AdminID]] </td>
						<td>Admin’s ID</td>
					</tr>
					<tr>
						<td>[[AdminLevel]] </td>
						<td>Admin’s Access Level Name</td>
					</tr>
					<tr>
						<td>[[AdminEmail]] </td>
						<td>Admin’s Email Address</td>
					</tr>
					<tr>
						<td>[[AdminPhone]] </td>
						<td>Admin’s Phone Number</td>
					</tr>
					<tr>
						<td>[[AdminStatus]] </td>
						<td>Admin’s Status</td>
					</tr>
					<tr>
						<td>[[AdminPortal]] </td>
						<td>Admin’s Portal Login</td>
					</tr>
				</tbody>
			</table>
		</div>
		<h4 class="m-t-20 ">Group - Smart tags are case sensitive</h4>
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
						<td>[[GroupName]] </td>
						<td>Group’s Full Name</td>
					</tr>
					<tr>
						<td>[[GroupID]] </td>
						<td>Group’s ID</td>
					</tr>
					<tr>
						<td>[[GroupContactName]] </td>
						<td>Group contact Full Name</td>
					</tr>
					<tr>
						<td>[[GroupContactEmail]] </td>
						<td>Group contact Email Address</td>
					</tr>
					<tr>
						<td>[[GroupContactPhone]] </td>
						<td>Group contact Phone Number</td>
					</tr>
					<tr>
						<td>[[GroupStatus]] </td>
						<td>Group’s Status</td>
					</tr>
					<tr>
						<td>[[GroupUsername]] </td>
						<td>Group’s Unique Username</td>
					</tr>
					<tr>
						<td>[[GroupPublicDisplay]] </td>
						<td>Group’s Display Name, Display Email, Display Phone</td>
					</tr>
					<tr>
						<td>[[GroupActiveProducts]] </td>
						<td>Product A Name, Product B Name, Product C Name</td>
					</tr>
					<tr>
						<td>[[ParentAgentName]] </td>
						<td>Group’s Parent Agent's Full Name</td>
					</tr>
					<tr>
						<td>[[ParentAgentID]] </td>
						<td>Group’s Parent Agent's ID</td>
					</tr>
					<tr>
						<td>[[TreeAgentName]] </td>
						<td>Group’s Highest Upline Agent's Full Name</td>
					</tr>
					<tr>
						<td>[[TreeAgentID]] </td>
						<td>Group’s Highest Upline Agent's ID</td>
					</tr>
					<tr>
						<td>[[GroupPortal]] </td>
						<td>Group’s Portal Login</td>
					</tr>
					<tr>
						<td>[[GroupServicesInfo]] </td>
						<td>Pulls content on system setup for Group Services</td>
					</tr>
				</tbody>
			</table>
		</div>
	
		<h4 class="m-t-20 ">Member - Smart tags are case sensitive</h4>
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
						<td>[[MemberName]] </td>
						<td>Member’s Full Name</td>
					</tr>
					<tr>
						<td>[[MemberID]] </td>
						<td>Member’s ID</td>
					</tr>
					<tr>
						<td>[[MemberEmail]] </td>
						<td>Member’s Email Address</td>
					</tr>
					<tr>
						<td>[[MemberPhone]] </td>
						<td>Member’s Phone Number</td>
					</tr>
					<tr>
						<td>[[MemberStatus]] </td>
						<td>Member’s Status</td>
					</tr>
					<tr>
						<td>[[MemberAddress]] </td>
						<td>Member’s Full Address</td>
					</tr>
					<tr>
						<td>[[MemberActiveProducts]] </td>
						<td>Product A Name, Product B Name, Product C Name</td>
					</tr>
					<tr>
						<td>[[MemberFullActiveProducts]] </td>
						<td>Added Date - Policy ID - Product Name - Coverage - Effective Date - Termination Date - Next Billing Date - Total Premium (for each active product)</td>
					</tr>
					<tr>
						<td>[[ParentAgentName]] </td>
						<td>Member’s Parent Agent's Full Name</td>
					</tr>
					<tr>
						<td>[[ParentAgentID]] </td>
						<td>Member’s Parent Agent's ID</td>
					</tr>
					<tr>
						<td>[[TreeAgentName]] </td>
						<td>Member’s Highest Upline Agent's Full Name</td>
					</tr>
					<tr>
						<td>[[TreeAgentID]] </td>
						<td>Member’s Highest Upline Agent's ID</td>
					</tr>
					<tr>
						<td>[[MemberPortal]] </td>
						<td>Member’s Portal Login</td>
					</tr>
					<tr>
						<td>[[MemberServicesInfo]] </td>
						<td>Pulls content on system setup for Member Services</td>
					</tr>
				</tbody>
			</table>
		</div> */?>
	</div>
</div>
