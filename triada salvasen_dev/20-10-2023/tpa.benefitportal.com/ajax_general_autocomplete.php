<?php

include_once 'includes/connect.php';

$res = array();
$type = checkIsset($_GET['type']);
$query = checkIsset($_GET['query']);
$agentID = checkIsset($_GET['agent_id']);//Get From Agent Area
if($query != ''){
	$query = preg_replace('/[^A-Za-z0-9. -]/', '', $query);
	if($type == 'PolicyID'){
		$sql = "SELECT id, website_id FROM website_subscriptions WHERE status NOT IN('Pending Declined','Pending Payment','Post Payment') AND website_id LIKE :query";
		$rows = $pdo->select($sql, array(':query' => '%' . $query . '%'));
		foreach ($rows as $value) {
			$res[] = array(
				'value' => $value['id'],
				'text' => $value['website_id']
			);		  
		}
	} else if($type == 'MemberID'){
		$incr = '';
		$sch_param = array(':query' => '%' . $query . '%');
		if($agentID !=''){
			$incr = ' AND upline_sponsors LIKE :agent_id ';
			$sch_param[':agent_id'] = ",".$agentID.",";
		}
		$sql = "SELECT id, rep_id, fname, lname FROM customer where type='customer' AND status NOT IN('Pending Quote','Pending Validation','Post Payment','Pending') AND (rep_id LIKE :query OR fname LIKE :query OR lname LIKE :query)";
		$rows = $pdo->select($sql, $sch_param);
		foreach ($rows as $value) {
			$res[] = array(
				'value' => $value['id'],
				'text' => ($value['rep_id'] . ' - ' . $value['fname'] . ' ' . $value['lname'])
			);
		}
	} else if($type == 'MemberIDRep'){
		$sql = "SELECT id, rep_id, fname, lname FROM customer where type='customer' AND status NOT IN('Pending Quote','Pending Validation','Post Payment','Pending') AND (rep_id LIKE :query OR fname LIKE :query OR lname LIKE :query)";
		$rows = $pdo->select($sql, array(':query' => '%' . $query . '%'));
		foreach ($rows as $value) {
			$res[] = array(
				'value' => $value['rep_id'],
				'text' => ($value['rep_id'] . ' - ' . $value['fname'] . ' ' . $value['lname'])
			);
		}
	} else if($type == 'AgentID'){
		$sql = "SELECT id,rep_id, fname, lname FROM customer WHERE type='Agent' AND is_deleted = 'N' AND (rep_id LIKE :query OR fname LIKE :query OR lname LIKE :query)";
		$rows = $pdo->select($sql, array(':query' => '%' . $query . '%'));

		foreach ($rows as $value) {
			$res[] = array(
				'value' => $value['id'],
				'text' => ($value['rep_id'] . ' - ' . $value['fname'] . ' ' . $value['lname'])
			);
		}
	} else if($type == 'AgentGroupID'){
		$sql = "SELECT id,rep_id,fname,lname,IF(type='Group',business_name,CONCAT(fname,' ',lname)) as name FROM customer WHERE type IN('Agent','Group') AND is_deleted = 'N' AND (rep_id LIKE :query OR fname LIKE :query OR lname LIKE :query OR business_name LIKE :query)";
		$rows = $pdo->select($sql, array(':query' => '%' . $query . '%'));

		foreach ($rows as $value) {
			$res[] = array(
				'value' => $value['id'],
				'text' => ($value['rep_id'] . ' - ' . $value['name'])
			);
		}
	} else if($type == 'LeadID'){
		$sql = "SELECT id,lead_id,fname,lname FROM leads  WHERE is_deleted = 'N' AND (lead_id LIKE :query OR fname LIKE :query OR lname LIKE :query)";
		$rows = $pdo->select($sql, array(':query' => '%' . $query . '%'));
		foreach ($rows as $value) {
			$res[] = array(
				'value' => $value['id'],
				'text' => ($value['lead_id'] . ' - ' . $value['fname'] . ' ' . $value['lname'])
			);
		}
	}else if($type == 'CommissionOrderID'){
		$sql = "SELECT id,display_id FROM orders WHERE status IN('Payment Approved','Completed','Pending Settlement') AND display_id LIKE :query";
		$rows = $pdo->select($sql, array(':query' => '%' . $query . '%'));
		foreach ($rows as $value) {
			$res[] = array(
				'value' => $value['id'],
				'text' => $value['display_id']
			);		  
		}
	}else if($type == 'PayableOrderID'){
		$sql = "SELECT id,display_id FROM orders WHERE status NOT IN ('Pending Quote','Pending Quotes','Pending Validation')AND display_id LIKE :query";
		$rows = $pdo->select($sql, array(':query' => '%' . $query . '%'));
		foreach ($rows as $value) {
			$res[] = array(
				'value' => $value['id'],
				'text' => $value['display_id']
			);		  
		}
	}else if($type == 'ReverseOrderID'){
		$sql = "SELECT o.id,o.display_id,o.status 
				FROM orders o 
				JOIN customer c ON(c.id = o.customer_id AND c.is_deleted='N') 
				WHERE o.status IN ('Payment Approved','Pending Settlement') 
				AND c.status NOT IN('Customer Abandon','Pending Quote','Pending Validation','Post Payment')
				AND o.display_id LIKE :query
				ORDER BY o.id DESC";
		$rows = $pdo->select($sql, array(':query' => '%' . $query . '%'));
		foreach ($rows as $value) {
			if($value['status'] == 'Pending Settlement'){
	            if(!is_ach_voidable($value["id"])){
	              continue;
	            }
	        }

			$res[] = array(
				'value' => $value['id'],
				'text' => $value['display_id']
			);		  
		}
	}else if($type == 'AdminID'){
		$fl_admin_sql = "SELECT id,display_id,fname,lname FROM admin WHERE is_deleted = 'N' AND (display_id LIKE :search_admin OR fname LIKE :search_admin OR lname LIKE :search_admin)";
		$fl_admin_res = $pdo->select($fl_admin_sql,array(':search_admin' => '%'. $query .'%'));

		foreach($fl_admin_res as $admin_value) {
			$res[] = array(
				'value' => $admin_value['display_id'],
				'text' => ($admin_value['display_id'] . ' - ' . $admin_value['fname'] . ' ' . $admin_value['lname'])
			);
		}
	}else if($type == 'productsID'){
		$filter_prd_res = get_active_global_products_for_filter();
		if(!empty($filter_prd_res)){
			foreach ($filter_prd_res as $key=>$company) {
				foreach ($company as $pkey =>$row) { 
					$res[] = array(
						'value' => $row['id'],
						'text' => $row['name'] .' ('.$row['product_code'].')'
					);
				}
			}
		}
	}else if($type == 'treeAgentID'){
		$agency_sql = "SELECT c.id,c.rep_id,c.fname,c.lname,c.rep_id,cs.company_name as agencyNameDis
					FROM customer c
					JOIN customer_settings cs ON(cs.customer_id=c.id AND cs.account_type='Business' AND cs.company_name!='')
					WHERE c.type='Agent' AND c.is_deleted = 'N'ORDER BY agencyNameDis ASC";
		$agencyArr = $pdo->select($agency_sql);
		if(!empty($agencyArr)){ 
			foreach($agencyArr as $value){
				$res[] = array(
					'value' => $value['id'],
					'text' => ($value['rep_id']).' - '.($value['agencyNameDis'])
				);
			}
		}
	}else if($type == 'agentProductsID'){
		$filter_prd_res = get_active_global_products_for_filter(0,true);
		if(!empty($filter_prd_res)){
			foreach ($filter_prd_res as $key=>$company) {
				foreach ($company as $pkey =>$row) { 
					$res[] = array(
						'value' => $row['id'],
						'text' => $row['name'] .' ('.$row['product_code'].')'
					);
				}
			}
		}
	}else if($type == 'agentCompanyID'){
		$company_sql = "SELECT cs.company from customer c JOIN customer_settings cs ON(cs.customer_id=c.id) where c.type='Agent' AND c.is_deleted='N' AND cs.company IS NOT NULL GROUP BY cs.company";
		$companyArr = $pdo->select($company_sql);

        if(!empty($companyArr)){ 
            $temp_comp= array();
			foreach($companyArr as $company){
                if(empty($temp_comp) || !in_array(trim($company['company']),$temp_comp)){
                    $temp_comp[] = trim($company['company']);
                    $res[] = array(
						'value' => $company['company'],
						'text' => $company['company']
					);
                }
            }   
		}
	}else if($type == 'license_state'){
		if(!empty($allStateRes)){
			foreach($allStateRes as $state){
				$res[] = array(
					'value' => $state['name'],
					'text' => $state['name']
				);
			}
		}
	}else if($type == 'ticketRequesterID'){
		$incr ='';
		$params = array();
		if(!empty($query)){
			$incr.= " AND(a.display_id LIKE :query OR a.fname LIKE :query OR a.lname LIKE :query OR c.rep_id LIKE :query OR c.fname LIKE :query OR c.lname LIKE :query)";
			$params[':query'] = "%" . $query ."%";
		}
		$sql = "SELECT if(s.user_type='Admin',a.id,c.id) as id,if(s.user_type='Admin',concat(a.display_id,' - ',a.fname,' ',a.lname),concat(c.rep_id,' - ',c.fname,' ',c.lname)) as name,if(s.user_type='Admin','Admin',c.type) as type 
		FROM s_ticket s 
		LEFT JOIN admin a ON(a.id=s.user_id AND s.user_type='Admin')
		LEFT JOIN customer c ON(c.id=s.user_id AND s.user_type!='Admin')
		WHERE s.user_id!=0" .$incr ." GROUP BY a.id,c.id";
		$rows = $pdo->selectGroup($sql,$params,'type');
		
		foreach ($rows as $key=>$value) {
			foreach($value as $data){
				$res[] = array(
					'value' => $data['id'],
					'text' => $data['name']
				);
			}
		}
	}else if($type == 'ticketAdminID'){
		$sql = "SELECT a.id,concat(a.display_id,' - ',a.fname,' ',a.lname) as name
		FROM s_ticket s 
		LEFT JOIN admin a ON(a.id=s.assigned_admin_id)
		WHERE s.user_id!=0  AND (a.display_id LIKE :search_admin OR a.fname LIKE :search_admin OR a.lname LIKE :search_admin) GROUP BY a.id ";
		$rows = $pdo->select($sql,array(':search_admin' => '%'. $query .'%'));
		foreach ($rows as $key=>$value) {
			$res[] = array(
				'value' => $value['id'],
				'text' => $value['name']
			);
		}
	}else if($type == 'ticketID'){
		$sql = "SELECT id,tracking_id FROM s_ticket WHERE 1 AND (tracking_id LIKE :query)";
		$rows = $pdo->select($sql,array(':query' => '%'. $query .'%'));
		foreach ($rows as $key=>$value) {
			$res[] = array(
				'value' => $value['id'],
				'text' => $value['tracking_id']
			);
		}
	}else if($type == 'ticketGroupID'){
		$sql = "SELECT * FROM s_ticket_group WHERE is_deleted='N' AND (title LIKE :query) ORDER BY title ASC";
		$rows = $pdo->select($sql,array(':query' => '%'. $query .'%'));
		foreach ($rows as $key=>$value) {
			$res[] = array(
				'value' => $value['id'],
				'text' => $value['title']
			);
		}
	}else if($type == 'ticketCompanyID'){
		$sql = "SELECT DISTINCT TRIM(Replace(Replace(Replace(cs.company,'\t',''),'\n',''),'\r','')) as company FROM customer c JOIN customer_settings cs ON(cs.customer_id=c.id) WHERE c.type='Agent' AND c.is_deleted='N' AND cs.company IS NOT NULL AND(cs.company LIKE :query) GROUP BY company";
		$rows = $pdo->select($sql,array(':query' => '%'. $query .'%'));
		foreach ($rows as $key=>$value) {
			$res[] = array(
				'value' => $value['company'],
				'text' => $value['company']
			);
		}
	}else if($type == 'globalActivityAdmin'){
		$globalAdminSql = "SELECT id,display_id,fname,lname FROM admin WHERE status != 'Pending' AND is_deleted = 'N' AND (display_id LIKE :search_admins OR fname LIKE :search_admins OR lname LIKE :search_admins)";
		$globalAdminRes = $pdo->select($globalAdminSql,array(':search_admins' => '%'. $query .'%'));

		foreach($globalAdminRes as $globalAdminval) {
			$res[] = array(
				'value' => $globalAdminval['id'],
				'text' => ($globalAdminval['display_id'] . ' - ' . $globalAdminval['fname'] . ' ' . $globalAdminval['lname'])
			);
		}
	}else if($type == 'globalActivityAgent'){
		$globalAgentSql = "SELECT id,rep_id,fname,lname FROM customer WHERE type = 'Agent' AND status = 'Active' AND is_deleted = 'N' AND (rep_id LIKE :search_agent OR fname LIKE :search_agent OR lname LIKE :search_agent)";
		$globalAgentRes = $pdo->select($globalAgentSql,array(':search_agent' => '%'. $query .'%'));

		foreach($globalAgentRes as $globalAgentval) {
			$res[] = array(
				'value' => $globalAgentval['id'],
				'text' => ($globalAgentval['rep_id'] . ' - ' . $globalAgentval['fname'] . ' ' . $globalAgentval['lname'])
			);
		}
	}else if($type == 'globalActivityGroup'){
		$globalGroupSql = "SELECT id,rep_id,fname,lname FROM customer WHERE type = 'Group' AND status = 'Active' AND is_deleted = 'N' AND (rep_id LIKE :search_group OR fname LIKE :search_group OR lname LIKE :search_group)";
		$globalGroupRes = $pdo->select($globalGroupSql,array(':search_group' => '%'. $query .'%'));

		foreach($globalGroupRes as $globalGroupval) {
			$res[] = array(
				'value' => $globalGroupval['id'],
				'text' => ($globalGroupval['rep_id'] . ' - ' . $globalGroupval['fname'] . ' ' . $globalGroupval['lname'])
			);
		}
	}else if($type == 'globalActivityMember'){
		$globalMemberSql = "SELECT id,rep_id,fname,lname FROM customer WHERE type = 'Customer' AND status IN('Active','Inactive') AND is_deleted = 'N' AND (rep_id LIKE :search_member OR fname LIKE :search_member OR lname LIKE :search_member)";
		$globalMemberRes = $pdo->select($globalMemberSql,array(':search_member' => '%'. $query .'%'));

		foreach($globalMemberRes as $globalMemberval) {
			$res[] = array(
				'value' => $globalMemberval['id'],
				'text' => ($globalMemberval['rep_id'] . ' - ' . $globalMemberval['fname'] . ' ' . $globalMemberval['lname'])
			);
		}
	}else if($type == 'globalActivityLead'){
		$globalLeadSql = "SELECT id,lead_id,fname,lname FROM leads WHERE is_deleted = 'N' AND (lead_id LIKE :search_lead OR fname LIKE :search_lead OR lname LIKE :search_lead)";
		$globalLeadRes = $pdo->select($globalLeadSql,array(':search_lead' => '%'. $query .'%'));

		foreach($globalLeadRes as $globalLeadval) {
			$res[] = array(
				'value' => $globalLeadval['id'],
				'text' => ($globalLeadval['lead_id'] . ' - ' . $globalLeadval['fname'] . ' ' . $globalLeadval['lname'])
			);
		}
	}else if($type == 'IPaddressAdmin'){
		$ipAddressAdminSql = "SELECT DISTINCT ip_address FROM activity_feed WHERE user_type='Admin' AND ip_address LIKE :search_ipaddress";
		$ipAddressAdminRes = $rpdo->select($ipAddressAdminSql,array(':search_ipaddress' => '%'. $query .'%'));

		foreach($ipAddressAdminRes as $ipAddressAdminval) {
			$res[] = array(
				'value' => $ipAddressAdminval['ip_address'],
				'text' => $ipAddressAdminval['ip_address']
			);
		}
	}else if($type == 'IPaddressAgent'){
		$ipAddressAgentSql = "SELECT DISTINCT ip_address FROM activity_feed WHERE user_type='Agent' AND ip_address LIKE :search_ipaddress";
		$ipAddressAgentRes = $rpdo->select($ipAddressAgentSql,array(':search_ipaddress' => '%'. $query .'%'));

		foreach($ipAddressAgentRes as $ipAddressAgentval) {
			$res[] = array(
				'value' => $ipAddressAgentval['ip_address'],
				'text' => $ipAddressAgentval['ip_address']
			);
		}
	}else if($type == 'IPaddressGroup'){
		$ipAddressGroupSql = "SELECT DISTINCT ip_address FROM activity_feed WHERE user_type='Group' AND ip_address LIKE :search_ipaddress";
		$ipAddressGroupRes = $rpdo->select($ipAddressGroupSql,array(':search_ipaddress' => '%'. $query .'%'));

		foreach($ipAddressGroupRes as $ipAddressGroupval) {
			$res[] = array(
				'value' => $ipAddressGroupval['ip_address'],
				'text' => $ipAddressGroupval['ip_address']
			);
		}
	}else if($type == 'IPaddressLead'){
		$ipAddressLeadSql = "SELECT DISTINCT ip_address FROM activity_feed WHERE user_type IN('Lead','leads') AND ip_address LIKE :search_ipaddress";
		$ipAddressLeadRes = $rpdo->select($ipAddressLeadSql,array(':search_ipaddress' => '%'. $query .'%'));

		foreach($ipAddressLeadRes as $ipAddressLeadval) {
			$res[] = array(
				'value' => $ipAddressLeadval['ip_address'],
				'text' => $ipAddressLeadval['ip_address']
			);
		}
	}else if($type == 'IPaddressMember'){
		$ipAddressMemberSql = "SELECT DISTINCT ip_address FROM activity_feed WHERE user_type='Customer' AND ip_address LIKE :search_ipaddress";
		$ipAddressMemberRes = $rpdo->select($ipAddressMemberSql,array(':search_ipaddress' => '%'. $query .'%'));

		foreach($ipAddressMemberRes as $ipAddressMemberval) {
			$res[] = array(
				'value' => $ipAddressMemberval['ip_address'],
				'text' => $ipAddressMemberval['ip_address']
			);
		}
	}
}

header('Content-type: application/json');
echo json_encode($res);
dbConnectionClose();
exit;
?>