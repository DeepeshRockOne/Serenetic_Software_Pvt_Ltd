<?php 
	include_once dirname(__FILE__) . '/layout/start.inc.php';
  	include_once __DIR__ . '/../includes/function.class.php';
  	$functionsList = new functionsList();
  
	$res = array();
	$data = '';
	$userGroup = checkIsset($_REQUEST['userGroup']);
	$broadcasterId = checkIsset($_REQUEST['broadcast_id']);
	$specific = checkIsset($_REQUEST['specific']);
	$agent_id = !empty($_REQUEST["agent_id"]) ? $_REQUEST["agent_id"] : $_SESSION["agents"]["id"];
	$broadcasterRes = array();
	$specificUserIdsArr = "";
	if(!empty($broadcasterId)){
		  $broadcasterRes = $pdo->selectOne("SELECT id, brodcast_name, display_id, from_address, subject, user_type, email_template_id, mail_content, is_for_specific, admin_level, specific_user_ids, product_ids, product_status,agent_status, lead_tags,lead_status, is_schedule_in_future, status FROM broadcaster WHERE type='sms' AND id = :broadcaster_id", array(":broadcaster_id" => $broadcasterId));
	}

    $specificUserIdsArr = !empty($broadcasterRes['specific_user_ids']) ? explode(",",$broadcasterRes['specific_user_ids']) : array();
    $productIdsArr = !empty($broadcasterRes['product_ids']) ? explode(",", $broadcasterRes['product_ids']) : array();
    $productStatus = checkIsset($broadcasterRes['product_status']);
    $agent_status = !empty($broadcasterRes['agent_status']) ? explode(',', $broadcasterRes['agent_status']) : array();

  	$adminLevelArr = !empty($broadcasterRes['admin_level']) ? explode(",", $broadcasterRes['admin_level']) : array(); 
    $leadTagsArr = !empty($broadcasterRes['lead_tags']) ? explode(",", $broadcasterRes['lead_tags']) : array();
    $leadStatus = checkIsset($broadcasterRes['lead_status']);
	
	if($userGroup == 'Admins'){
		if($specific == 'Admins'){
			// get list of active admins in system
			$selAdmins = "SELECT id,display_id,CONCAT(fname,' ', lname) as name FROM admin WHERE is_active='Y' AND is_deleted='N' AND status = 'Active'";
			$resAdmins = $pdo->select($selAdmins);

			if(!empty($resAdmins)) {
				$data .= '<div class="col-md-7"><div class="form-group"><select name="specific_admin[]" class="se_multiple_select" multiple="multiple">';

                foreach ($resAdmins as $admin) {
                	$selected =  in_array($admin['id'], $specificUserIdsArr) ? "selected='selected'" : "";
                	$data .= '<option value="'.$admin["id"].'"'.$selected.'>'.$admin["name"].' ('.$admin['display_id'].')</option>';
                }        
                      
                $data .= '</select><label>Select Specific Admin(s)</label><span class="error error_preview" id="error_specific_admin"></span></div></div>';
            }
        }else{
        	// get admin levels

        	$data .= '<div class="col-md-1"><div class="form-group  text-center"><p class="mn text-light-gray fs12 p-t-7 text-nowrap">— Filter —</p></div></div>';

            	$featureAccessArr = array();
				$selAccessLevel = "SELECT id,name,dashboard,feature_access 
						            FROM access_level WHERE feature_access !='' 
						            ORDER BY name ASC"; 
				$resAccessLevel = $pdo->select($selAccessLevel);

				if(!empty($resAccessLevel)){
            		$data .= '<div class="col-md-6"><div class="form-group"><select name="admin_level[]" class="se_multiple_select" multiple="multiple">';


                    foreach ($resAccessLevel as $accessLevel) {
            			$selected =  in_array($accessLevel['id'], $adminLevelArr) ? "selected='selected'" : "";
                    	$data .= '<option value="'.$accessLevel['id'].'"'. $selected.'>'.$accessLevel['name'].'</option>';
                    }

                    $data .= '</select><label>Admin Level</label><span class="error" id="error_admin_level"></span></div></div>';
				}
				// pre_print($data);
        }
        $checked = ($specific == 'Admins') ? "checked='checked'" : "";
		$data .= '<div class="col-md-2 text-center"><div class="form-group"><label class="mn  p-t-5 fs12"><input type="checkbox" name="specific_user_group" id="specific_user_group" '.$checked.'><span id="specific_user_group_label">Specific Admins </span></label></div></div>';
	}else if($userGroup == 'Leads'){

		$selLead = "SELECT id,opt_in_type,lead_id,CONCAT(fname,' ',lname) as lead_name 
					FROM leads WHERE is_deleted='N' AND status NOT IN ('Converted',' ','NULL') AND sponsor_id=:sponsor_id";
		$resLead = $pdo->select($selLead,array(":sponsor_id" => $agent_id));

		$resLeadTag = array();
		if(!empty($resLead)){
		  foreach ($resLead as $key => $value) {
		    if(!in_array($value['opt_in_type'], $resLeadTag)){
		      array_push($resLeadTag, $value['opt_in_type']);
		    }
		  }
		}

		if($specific == 'Leads'){

			if(!empty($resLead)) {
				$data .= '<div class="col-md-6"><div class="form-group"><select name="specific_leads[]" class="se_multiple_select" multiple="multiple">';
                foreach ($resLead as $lead) {
                	$selected =  in_array($lead['id'], $specificUserIdsArr) ? "selected='selected'" : "";
                	$data .= '<option value="'.$lead["id"].'"'.$selected.'>'.$lead["lead_name"].' ('.$lead['lead_id'].')</option>';
                }        
                      
                $data .= '</select><label>Select Specific Lead(s)</label><span class="error error_preview" id="error_specific_leads"></span></div></div>';
            }
        }else{
        	$data .= '<div class="col-md-1"><div class="form-group  text-center"><p class="mn text-light-gray fs12 p-t-7 text-nowrap">— Filter —</p></div></div>';

            		$data .= '<div class="col-md-3"><div class="form-group "><select name="lead_tags[]" class="se_multiple_select" multiple="multiple">';
				if(!empty($resLeadTag)){

                    foreach ($resLeadTag as $leadTag) {
                    	$selected =  in_array($leadTag, $leadTagsArr) ? "selected='selected'" : "";
                    	$data .= '<option value="'.$leadTag.'"'.$selected.'>'.$leadTag.'</option>';
                    }

				}
                $data .= '</select><label>Lead Tag</label><span class="error" id="error_lead_tags"></span></div></div>';
                
                $data .='<div class="col-md-1"><div class="form-group  text-center"><p class="mn text-light-gray fs12 p-t-7">— And —</p></div></div>';

	            $data .= '<div class="col-md-2"><div class="form-group "><select name="lead_status" class="form-control"><option value=""></option>
	                    <option value="New"'.($leadStatus == "New" ? "selected=selected" : "").'>New</option>
	                    <option value="Working"'.($leadStatus == "Working" ? "selected=selected" : "").'>Working</option>
	                    <option value="Open"'.($leadStatus == "Open" ? "selected=selected" : "").'>Open</option>
	                    <option value="Unqualified"'.($leadStatus == "Unqualified" ? "selected=selected" : "").'>Unqualified</option>
	                    <option value="Abandoned"'.($leadStatus == "Abandoned" ? "selected=selected" : "").'>Abandoned</option>
	                    <option value="Converted"'.($leadStatus == "Converted" ? "selected=selected" : "").'>Converted</option>
	                  </select>
	                  <label>Lead Status</label>
	                  <span class="error error_preview" id="error_lead_status"></span>
	                </div>
	              </div>';
        }

		$checked = ($specific == 'Leads') ? "checked='checked'" : "";
		$data .= '<div class="col-md-2 text-center"><div class="form-group "><label class="mn  p-t-5 fs12"><input type="checkbox" name="specific_user_group" id="specific_user_group" '.$checked.'><span id="specific_user_group_label">Specific Leads </span></label></div></div>';
	}else if(in_array($userGroup, array("Agents","Employer Groups","Members"))){
		if(empty($specific)){

			// get active products and products status
	        	$data .= '<div class="col-md-1"><div class="form-group  text-center"><p class="mn text-light-gray fs12 p-t-7 text-nowrap">— Filter —</p></div></div>';

				$companyArr = get_active_global_products_for_filter($agent_id);

        		$data .= '<div class="col-md-3"><div class="form-group ">
                    <select name="product_ids[]" class="se_multiple_select" multiple="multiple">';

	           	if(!empty($companyArr)){
	                foreach ($companyArr as $key => $company) {
	                        $data .= '<optgroup label="'.$key.'">';
								foreach ($company as $pkey => $row) {
									$selected =  in_array($row['id'], $productIdsArr) ? "selected='selected'" : "";
	                            	$data .='<option value="'.$row['id'].'"'.$selected.'>'.$row['name']. '('.$row['product_code'].') </option>';
	                            }
	                            
	                        $data .= '</optgroup>';
	                }
	            }
                        
	            $data .= '</select><label>Select Product(s)</label><span class="error" id="error_product_ids"></span></div></div>';

	            $data .='<div class="col-md-1"><div class="form-group  text-center"><p class="mn text-light-gray fs12 p-t-7">— And —</p></div></div>';

	            $data .= '<div class="col-md-2"><div class="form-group "><select name="product_status" class="form-control"><option value=""></option>
	                    <option value="Active"'.($productStatus == "Active" ? "selected=selected" : "").'>Active</option>
	                    <option value="Pending"'.($productStatus == "Pending" ? "selected=selected" : "").'>Pending</option>
	                    <option value="Terminated"'.($productStatus == "Terminated" ? "selected=selected" : "").'>Terminated</option>
	                  </select>
	                  <label>Product Status</label>
	                  <span class="error error_preview" id="error_product_status"></span>
	                </div>
	              </div>';

	            $data .= "<div class='col-md-1'>
				                  <div class='form-group  text-center'>
				                    <p class='mn text-light-gray fs12 p-t-7'>— And —</p>
				                  </div>
				                </div>
				                <div class='col-md-3'>
				                  <div class='form-group '>
				                    <select name='agent_status[]'' class='se_multiple_select' multiple='multiple'>";
			                  
			                       $data.= "<option value='Invited'";
			                       	if(in_array('Invited', $agent_status)){
			                       		$data .= "selected='selected'";
			                       	}
			                       $data .= ">Invited</option>";

			                       $data.= "<option value='Pending Documentation'";
			                       	if(in_array('Pending Documentation', $agent_status)){
			                       		$data .= "selected='selected'";
			                       	}
			                       $data .= ">Pending Documentation</option>";

			                       $data.= "<option value='Pending Approval'";
			                       	if(in_array('Pending Approval', $agent_status)){
			                       		$data .= "selected='selected'";
			                       	}
			                       $data .= ">Pending Approval</option>";

			                       $data.= "<option value='Pending Contract'";
			                       	if(in_array('Pending Contract', $agent_status)){
			                       		$data .= "selected='selected'";
			                       	}
			                       $data .= ">Pending Contract</option>";

			                       $data.= "<option value='Active'";
			                       	if(in_array('Active', $agent_status)){
			                       		$data .= "selected='selected'";
			                       	}
			                       $data .= ">Active</option>";

			                        $data.= "<option value='Suspended'";
			                       	if(in_array('Suspended', $agent_status)){
			                       		$data .= "selected='selected'";
			                       	}
			                       $data .= ">Suspended</option>";

			                       $data.= "<option value='Terminated'";
			                       	if(in_array('Terminated', $agent_status)){
			                       		$data .= "selected='selected'";
			                       	}
			                       $data .= ">Terminated</option>";

			                  
			                $data .= "</select>
			                    <label>Agent Status</label>
			                    <span class='error error_preview' id='error_agent_status'></span>
			                  </div>
			                </div>";
		
				$data .= '<div class="col-md-2 text-center"><div class="form-group "><label class="mn  p-t-5 fs12"><input type="checkbox" name="specific_user_group" id="specific_user_group"><span id="specific_user_group_label">Specific '.(($userGroup != "Employer Groups") ? $userGroup : "Groups").'</span></label></div></div>';
        }else{

			// get list of active agents in system
        	if($specific == 'Agents'){

				$selAgent = "SELECT id,rep_id as agentDispId,CONCAT(fname,' ', lname) as name,type FROM customer WHERE is_deleted='N' AND status NOT IN ('Customer Abandon','Pending Quote','Invited') AND type IN ('Agent') AND sponsor_id=:sponsor_id";
				$resAgents = $pdo->select($selAgent,array(":sponsor_id"=>$agent_id));
				$data .= '<div class="col-md-7"><div class="form-group "><select name="specific_agent[]" class="se_multiple_select" multiple="multiple">';
				if(!empty($resAgents)) {
	                foreach ($resAgents as $agent) {
	                	$selected =  in_array($agent['id'], $specificUserIdsArr) ? "selected='selected'" : "";
	                	$data .= '<option value="'.$agent["id"].'"'.$selected.'>'.$agent["name"].' ('.$agent['agentDispId'].')</option>';
	                }        
				}
				$data .= '</select><label>Select Specific Agent(s)</label><span class="error error_preview" id="error_specific_agent"></span></div></div>';

	        // get list of active groups in system
	        }else if($specific == 'Employer Groups'){

				$selGroup = "SELECT id,rep_id as groupDispId,CONCAT(fname,' ', lname) as name,type FROM customer WHERE is_deleted='N' AND status NOT IN ('Customer Abandon','Pending Quote','Invited') AND type IN ('Group') AND sponsor_id=:sponsor_id";
				$resGroup = $pdo->select($selGroup,array(":sponsor_id" => $agent_id));

				$data .= '<div class="col-md-7"><div class="form-group "><select name="specific_group[]" class="se_multiple_select" multiple="multiple">';
				if(!empty($resGroup)) {
	                foreach ($resGroup as $group) {
	                	$selected =  in_array($group['id'], $specificUserIdsArr) ? "selected='selected'" : "";
	                	$data .= '<option value="'.$group["id"].'"'.$selected.'>'.$group["name"].' ('.$group['groupDispId'].')</option>';
	                }        
	            }
	            $data .= '</select><label>Select Specific Group(s)</label><span class="error error_preview" id="error_specific_group"></span></div></div>';
        	// get list of active members in system
        	}else if($specific == 'Members'){
				$selMember = "SELECT id,rep_id as mbrDispId,CONCAT(fname,' ', lname) as name,type FROM customer WHERE is_deleted='N' AND status NOT IN ('Customer Abandon','Pending Quote','Invited') AND type IN ('Customer') AND sponsor_id IN ($agent_id)";
				$resMember = $pdo->select($selMember);
				
				$data .= '<div class="col-md-7"><div class="form-group "><select name="specific_member[]" class="se_multiple_select" multiple="multiple">';
				if(!empty($resMember)) {
	                foreach ($resMember as $member) {
	                	$selected =  in_array($member['id'], $specificUserIdsArr) ? "selected='selected'" : "";
	                	$data .= '<option value="'.$member["id"].'"'.$selected.'>'.$member["name"].' ('.$member['mbrDispId'].')</option>';
	                }        
	            }
	            $data .= '</select><label>Select Specific Member(s)</label><span class="error error_preview" id="error_specific_member"></span></div></div>';
	        }

	        $checked = (!empty($specific) && in_array($specific,array("Agents","Employer Groups","Members"))) ? "checked='checked'" : "";
			
	        $data .= '<div class="col-md-2 text-center"><div class="form-group "><label class="mn  p-t-5 fs12"><input type="checkbox" name="specific_user_group" id="specific_user_group" '.$checked.'><span id="specific_user_group_label">Specific '.(($userGroup != "Employer Groups") ? $userGroup : "Groups").'</span></label></div></div>';
        }
	}

	$res['data'] = $data;

	header('Content-type: application/json');
	echo json_encode($res);
	dbConnectionClose(); 
	exit;
?>