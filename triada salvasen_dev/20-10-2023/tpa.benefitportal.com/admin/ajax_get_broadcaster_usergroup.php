<?php 
	include_once dirname(__FILE__) . '/layout/start.inc.php';
  	include_once __DIR__ . '/../includes/function.class.php';
  	$functionsList = new functionsList();
  
	$res = array();
	$data = '';
	$userGroup = checkIsset($_REQUEST['userGroup']);
	$broadcasterId = checkIsset($_REQUEST['broadcast_id']);
	$specific = checkIsset($_REQUEST['specific']);

	$broadcasterRes = array();
	$specific_agent_level = array(); 
	$enrolling_agent = array(); 
	$member_state = array();

	$agent_levels = $pdo->select("SELECT id,level,level_heading from agent_coded_level WHERE is_active = 'Y' ORDER BY id desc");
	$states = $pdo->select("SELECT id,name from states_c WHERE country_id = 231 AND is_deleted = 'N'"); 

	// Agent, Member and Group value start
	$sel_customer = "SELECT id,rep_id,CONCAT(fname,' ', lname) as name,type FROM customer WHERE is_deleted='N' AND status NOT IN ('Customer Abandon','Pending Quote','Invited') AND type IN ('Agent','Group','Customer')";
	$res_customer = $pdo->select($sel_customer);

	$agent_res = array();
	$group_res = array();
	$member_res = array();
	if(!empty($res_customer) && count($res_customer) > 0) {
		$agent_count = 0;
		$group_count = 0;
		$member_count = 0;
		foreach ($res_customer as $key => $value) {
			if($value['type'] == 'Agent') {
				$agent_res[$agent_count] = $value;
				$agent_count++;
			} else if($value['type'] == 'Group') {
				$group_res[$group_count] = $value;
				$group_count++;
			} else {
				$member_res[$member_count] = $value;
				$member_count++;
			}
		}
	}

	if(!empty($broadcasterId)){
		  $broadcasterRes = $pdo->selectOne("SELECT id, brodcast_name, display_id, from_address, subject, user_type, email_template_id, mail_content, is_for_specific,specific_agent_level,enrolling_agent_ids,tree_agent_ids,states, admin_level, specific_user_ids, product_ids,agent_status, product_status, lead_tags, is_schedule_in_future, status FROM broadcaster WHERE type='sms' AND id = :broadcaster_id", array(":broadcaster_id" => $broadcasterId));
	}

    $specificUserIdsArr = !empty($broadcasterRes['specific_user_ids']) ? explode(",", $broadcasterRes['specific_user_ids']) : array();
 
    $productIdsArr = !empty($broadcasterRes['product_ids']) ? explode(",", $broadcasterRes['product_ids']) : array();
    $productStatus = checkIsset($broadcasterRes['product_status']);

  	$adminLevelArr = !empty($broadcasterRes['admin_level']) ? explode(",", $broadcasterRes['admin_level']) : array(); 
    $leadTagsArr = !empty($broadcasterRes['lead_tags']) ? explode(",", $broadcasterRes['lead_tags']) : array();
	
    $specific_agent_level = !empty($broadcasterRes['specific_agent_level']) ? explode(',', $broadcasterRes['specific_agent_level']) : array();
    $agent_status = !empty($broadcasterRes['agent_status']) ? explode(',', $broadcasterRes['agent_status']) : array();
    $enrolling_agent = !empty($broadcasterRes['enrolling_agent_ids']) ? explode(',', $broadcasterRes['enrolling_agent_ids']) : array();
    $tree_agent_ids = !empty($broadcasterRes['tree_agent_ids']) ? explode(',', $broadcasterRes['tree_agent_ids']) : array();
    $member_state = !empty($broadcasterRes['states']) ? explode(',', $broadcasterRes['states']) : array();

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
                    	$data .= '<option value="'.$accessLevel['name'].'"'. $selected.'>'.$accessLevel['name'].'</option>';
                    }

                    $data .= '</select><label>Admin Level</label><span class="error" id="error_admin_level"></span></div></div>';
				}
				// pre_print($data);
        }
        $checked = ($specific == 'Admins') ? "checked='checked'" : "";
		$data .= '<div class="col-md-2 text-left"><div class="form-group "><label class="mn  p-t-5 fs12"><input type="checkbox" name="specific_user_group" id="specific_user_group" '.$checked.'><span id="specific_user_group_label">Specific Admins </span></label></div></div>';
	}else if($userGroup == 'Leads'){

		$selLead = "SELECT id,opt_in_type,lead_id,CONCAT(fname,' ',lname) as lead_name 
					FROM leads WHERE is_deleted='N' AND status NOT IN ('Converted',' ','NULL')";
		$resLead = $pdo->select($selLead);

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

				if(!empty($resLeadTag)){
            		$data .= '<div class="col-md-7"><div class="form-group"><select name="lead_tags[]" class="se_multiple_select" multiple="multiple">';

                    foreach ($resLeadTag as $leadTag) {
                    	$selected =  in_array($leadTag, $leadTagsArr) ? "selected='selected'" : "";
                    	$data .= '<option value="'.$leadTag.'"'.$selected.'>'.$leadTag.'</option>';
                    }

                    $data .= '</select><label>Lead Tag</label><span class="error" id="error_lead_tags"></span></div></div>';
				}
		}
		if(empty($specific)){
			$data .= '<div class="col-md-1">
                  <div class="form-group  text-center">
                    <p class="mn text-light-gray fs12 p-t-7">— And —</p>
                  </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-3">
                  <div class="form-group">
                    <select name="lead_agent_ids[]" class="se_multiple_select" multiple="multiple">';
                      if(!empty($agent_res)) {
                        foreach ($agent_res as $key => $value) {
                         $data .= '<option value="'.$value['id'].'"';
                          if(!empty($broadcasterRes) && in_array($value['id'], $enrolling_agent)){ 
                          	$data .= 'selected="selected"';
                      	  }
                          	$data .= '>' . $value['rep_id'] . ' - ' . $value['name'] . '</option>';
                        }
                      }
                    $data .= '</select>
                    <label>Agent</label>
                    <span class="error error_preview" id="error_group_agent_ids"></span>
                  </div>
                </div>

                <div class="col-md-1">
                  <div class="form-group  text-center">
                    <p class="mn text-light-gray fs12 p-t-7">— And —</p>
                  </div>
                </div>

                <div class="col-md-3">
                  <div class="form-group">
                    <select name="lead_agent_tree_ids[]" class="se_multiple_select" multiple="multiple">';
                      if(!empty($agent_res)) {
                        foreach ($agent_res as $key => $value) {
                          $data .= '<option value="' . $value['id'] . '"';
                          if(!empty($broadcasterRes) && in_array($value['id'], $tree_agent_ids)){
                            $data .= "selected='selected'";
                          }
                          
                          $data .=	'>' . $value['rep_id'] . ' - ' . $value['name']. '</option>';
                        } 
                      }  
                    $data .= '</select>
                    <label>Agent Tree</label>
                    <span class="error error_preview" id="error_group_agent_tree_ids"></span>
                  </div>
                </div>';

        	$data .= '<div class="col-md-1">
                  <div class="form-group  text-center">
                    <p class="mn text-light-gray fs12 p-t-7">— And —</p>
                  </div>
                </div>

                <div class="col-md-2">
                  <div class="form-group">
                    <select name="lead_state[]" class="se_multiple_select" multiple="multiple">';
                      if(!empty($states)) {
                        foreach ($states as $key => $value) {
                          $data .= '<option value="' . $value['name'] .'"';
                           if(!empty($broadcasterRes) && in_array($value['name'], $member_state)){
                            	$data .= "selected='selected'";
                        	}
                            $data .= '>' . $value['name'] . '</option>';
                        } 
                      } 
                    
                    $data .= '</select>
                    <label>State</label>
                    <span class="error error_preview" id="error_member_state"></span>
                  </div>
                </div>'; 
		}               

		$checked = ($specific == 'Leads') ? "checked='checked'" : "";
		$data .= '<div class="col-md-2 text-left"><div class="form-group "><label class="mn  p-t-5 fs12"><input type="checkbox" name="specific_user_group" id="specific_user_group" '.$checked.'><span id="specific_user_group_label">Specific Leads </span></label></div></div>';
	}else if(in_array($userGroup, array("Agents","Employer Groups","Members"))){
		if(empty($specific)){

			// get active products and products status
	        	$data .= '<div class="col-md-1"><div class="form-group  text-center"><p class="mn text-light-gray fs12 p-t-7 text-nowrap">— Filter —</p></div></div>';

				$companyArr = get_active_global_products_for_filter();

        		$data .= '<div class="col-md-3"><div class="form-group">
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

	            $data .= '<div class="col-md-3"><div class="form-group "><select name="product_status" class="form-control"><option value=""></option>
	                    <option value="Active"'.($productStatus == "Active" ? "selected=selected" : "").'>Active</option>
	                    <option value="Pending"'.($productStatus == "Pending" ? "selected=selected" : "").'>Pending</option>
	                    <option value="Terminated"'.($productStatus == "Terminated" ? "selected=selected" : "").'>Terminated</option>
	                  </select>
	                  <label>Product Status</label>
	                  <span class="error error_preview" id="error_product_status"></span>
	                </div>
	              </div>';

	            if($userGroup == 'Agents'){
	            	$data .= "<div class='col-md-1'>
				                  <div class='form-group  text-center'>
				                    <p class='mn text-light-gray fs12 p-t-7'>— And —</p>
				                  </div>
				                </div>
				                <div class='clearfix'></div>
				                <div class='col-md-3'>
				                  <div class='form-group'>
				                    <select name='agent_level[]'' class='se_multiple_select' multiple='multiple'>";
				                     
			                    foreach ($agent_levels as $key => $value) {
			                       $data.= "<option value='". $value['id'] ."'";
			                       	if(in_array($value['id'], $specific_agent_level)){
			                       		$data .= "selected='selected'";
			                       	}

			                       $data .= ">" . $value['level_heading']."</option>";
			                    } 
			                $data .= "</select>
			                    <label>Agent Level</label>
			                    <span class='error error_preview' id='error_agent_level'></span>
			                  </div>
			                </div>";
			         	$data .= "<div class='col-md-1'>
				                  <div class='form-group  text-center'>
				                    <p class='mn text-light-gray fs12 p-t-7'>— And —</p>
				                  </div>
				                </div>
				                <div class='col-md-3'>
				                  <div class='form-group'>
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
				}
				if(in_array($userGroup, array('Employer Groups','Members'))){

					$data .= '<div class="col-md-1">
				                  <div class="form-group  text-center">
				                    <p class="mn text-light-gray fs12 p-t-7">— And —</p>
				                  </div>
				                </div>
				                <div class="clearfix"></div>
				                <div class="col-md-3">
				                  <div class="form-group">
				                    <select name="group_agent_ids[]" class="se_multiple_select" multiple="multiple">';
				                      if(!empty($agent_res)) {
				                        foreach ($agent_res as $key => $value) {
				                         $data .= '<option value="'.$value['id'].'"';
				                          if(!empty($broadcasterRes) && in_array($value['id'], $enrolling_agent)){ 
				                          	$data .= 'selected="selected"';
				                      	  }
				                          	$data .= '>' . $value['rep_id'] . ' - ' . $value['name'] . '</option>';
				                        }
				                      }
				                    $data .= '</select>
				                    <label>Agent</label>
				                    <span class="error error_preview" id="error_group_agent_ids"></span>
				                  </div>
				                </div>

				                <div class="col-md-1">
				                  <div class="form-group  text-center">
				                    <p class="mn text-light-gray fs12 p-t-7">— And —</p>
				                  </div>
				                </div>

				                <div class="col-md-3">
				                  <div class="form-group">
				                    <select name="group_agent_tree_ids[]" class="se_multiple_select" multiple="multiple">';
				                      if(!empty($agent_res)) {
				                        foreach ($agent_res as $key => $value) {
				                          $data .= '<option value="' . $value['id'] . '"';
				                          if(!empty($broadcasterRes) && in_array($value['id'], $tree_agent_ids)){
				                            $data .= "selected='selected'";
				                          }
				                          
				                          $data .=	'>' . $value['rep_id'] . ' - ' . $value['name']. '</option>';
				                        } 
				                      }  
				                    $data .= '</select>
				                    <label>Agent Tree</label>
				                    <span class="error error_preview" id="error_group_agent_tree_ids"></span>
				                  </div>
				                </div>';


				}

				if($userGroup == 'Members'){

					$data .= '<div class="col-md-1">
				                  <div class="form-group  text-center">
				                    <p class="mn text-light-gray fs12 p-t-7">— And —</p>
				                  </div>
				                </div>

				                <div class="col-md-2">
				                  <div class="form-group">
				                    <select name="member_state[]" class="se_multiple_select" multiple="multiple">';
				                      if(!empty($states)) {
				                        foreach ($states as $key => $value) {
				                          $data .= '<option value="' . $value['name'] .'"';
				                           if(!empty($broadcasterRes) && in_array($value['name'], $member_state)){
				                            	$data .= "selected='selected'";
				                        	}
				                            $data .= '>' . $value['name'] . '</option>';
				                        } 
				                      } 
				                    
				                    $data .= '</select>
				                    <label>State</label>
				                    <span class="error error_preview" id="error_member_state"></span>
				                  </div>
				                </div>';


				}  
		
				$data .= '<div class="col-md-2 text-left"><div class="form-group "><label class="mn  p-t-5 fs12"><input type="checkbox" name="specific_user_group" id="specific_user_group"><span id="specific_user_group_label">Specific '.(($userGroup != "Employer Groups") ? $userGroup : "Groups").'</span></label></div></div>';
        }else{

			// get list of active agents in system
        	if($specific == 'Agents'){

				$selAgent = "SELECT id,rep_id as agentDispId,CONCAT(fname,' ', lname) as name,type FROM customer WHERE is_deleted='N' AND status NOT IN ('Customer Abandon','Pending Quote','Invited') AND type IN ('Agent')";
				$resAgents = $pdo->select($selAgent);
				// pre_print($resAgents);
				if(!empty($resAgents)) {
					$data .= '<div class="col-md-7"><div class="form-group"><select name="specific_agent[]" class="se_multiple_select" multiple="multiple">';

	                foreach ($resAgents as $agent) {
	                	$selected =  in_array($agent['id'], $specificUserIdsArr) ? "selected='selected'" : "";
	                	$data .= '<option value="'.$agent["id"].'"'.$selected.'>'.$agent["name"].' ('.$agent['agentDispId'].')</option>';
	                }        
	                      
	                $data .= '</select><label>Select Specific Agent(s)</label><span class="error error_preview" id="error_specific_agent"></span></div></div>';
	            }
	        // get list of active groups in system
	        }else if($specific == 'Employer Groups'){

				$selGroup = "SELECT id,rep_id as groupDispId,CONCAT(fname,' ', lname) as name,type FROM customer WHERE is_deleted='N' AND status NOT IN ('Customer Abandon','Pending Quote','Invited') AND type IN ('Group')";
				$resGroup = $pdo->select($selGroup);

				$data .= '<div class="col-md-7"><div class="form-group"><select name="specific_group[]" class="se_multiple_select" multiple="multiple">';
				if(!empty($resGroup)) {
	                foreach ($resGroup as $group) {
	                	$selected =  in_array($group['id'], $specificUserIdsArr) ? "selected='selected'" : "";
	                	$data .= '<option value="'.$group["id"].'"'.$selected.'>'.$group["name"].' ('.$group['groupDispId'].')</option>';
	                }        
	            }
	            $data .= '</select><label>Select Specific Group(s)</label><span class="error error_preview" id="error_specific_group"></span></div></div>';
        	// get list of active members in system
        	}else if($specific == 'Members'){
				$selMember = "SELECT id,rep_id as mbrDispId,CONCAT(fname,' ', lname) as name,type FROM customer WHERE is_deleted='N' AND status NOT IN ('Customer Abandon','Pending Quote','Invited') AND type IN ('Customer')";
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
			
	        $data .= '<div class="col-md-2 text-left"><div class="form-group "><label class="mn  p-t-5 fs12"><input type="checkbox" name="specific_user_group" id="specific_user_group" '.$checked.'><span id="specific_user_group_label">Specific '.(($userGroup != "Employer Groups") ? $userGroup : "Groups").'</span></label></div></div>';
        }
	}

	$res['data'] = $data;

	header('Content-type: application/json');
	echo json_encode($res);
	dbConnectionClose(); 
	exit;
?>