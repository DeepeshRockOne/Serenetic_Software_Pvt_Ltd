<?php 
	include_once dirname(__FILE__) . '/layout/start.inc.php';
  	include_once __DIR__ . '/../includes/function.class.php';
  	$functionsList = new functionsList();
  
	$res = array();
	$data = '';
	$userGroup = checkIsset($_REQUEST['userGroup']);
	$broadcasterId = checkIsset($_REQUEST['broadcast_id']);
	$specific = checkIsset($_REQUEST['specific']);
	$group_id = !empty($_REQUEST["group_id"]) ? $_REQUEST["group_id"] : $_SESSION["groups"]["id"];
	$broadcasterRes = array();
	if(!empty($broadcasterId)){
		  $broadcasterRes = $pdo->selectOne("SELECT id, brodcast_name, display_id, from_address, subject, user_type, email_template_id, mail_content, is_for_specific, admin_level, specific_user_ids, product_ids, product_status, lead_tags, is_schedule_in_future, status FROM broadcaster WHERE type='sms' AND id = :broadcaster_id", array(":broadcaster_id" => $broadcasterId));
	}

    $specificUserIdsArr = !empty($broadcasterRes['specific_user_ids']) ? explode(",", $broadcasterRes['specific_user_ids']) : array();
 
    $productIdsArr = !empty($broadcasterRes['product_ids']) ? explode(",", $broadcasterRes['product_ids']) : array();
    $productStatus = checkIsset($broadcasterRes['product_status']);

  	$adminLevelArr = !empty($broadcasterRes['admin_level']) ? explode(",", $broadcasterRes['admin_level']) : array(); 
    $leadTagsArr = !empty($broadcasterRes['lead_tags']) ? explode(",", $broadcasterRes['lead_tags']) : array();
	
	if($userGroup == 'Leads'){

		$selLead = "SELECT id,opt_in_type,lead_id,CONCAT(fname,' ',lname) as lead_name 
					FROM leads WHERE is_deleted='N' AND status NOT IN ('Converted',' ','NULL') AND sponsor_id=:sponsor_id";
		$resLead = $pdo->select($selLead,array(":sponsor_id" => $group_id));

		$resLeadTag = array();
		if(!empty($resLead)){
		  foreach ($resLead as $key => $value) {
		    if(!in_array($value['opt_in_type'], $resLeadTag)){
		      array_push($resLeadTag, $value['opt_in_type']);
		    }
		  }
		}

		if($specific == 'Leads'){

				$data .= '<div class="col-md-7"><div class="form-group "><select name="specific_leads[]" class="se_multiple_select" multiple="multiple">';
			if(!empty($resLead)) {
                foreach ($resLead as $lead) {
                	$selected =  in_array($lead['id'], $specificUserIdsArr) ? "selected='selected'" : "";
                	$data .= '<option value="'.$lead["id"].'"'.$selected.'>'.$lead["lead_name"].' ('.$lead['lead_id'].')</option>';
                }        
                      
            }
                $data .= '</select><label>Select Specific Enrollees</label><span class="error error_preview" id="error_specific_leads"></span></div></div>';
        }else{
        	$data .= '<div class="col-md-1"><div class="form-group  text-center"><p class="mn text-light-gray fs12 p-t-7 text-nowrap">— Filter —</p></div></div>';

            		$data .= '<div class="col-md-6"><div class="form-group "><select name="lead_tags[]" class="se_multiple_select" multiple="multiple">';
				if(!empty($resLeadTag)){

                    foreach ($resLeadTag as $leadTag) {
                    	$selected =  in_array($leadTag, $leadTagsArr) ? "selected='selected'" : "";
                    	$data .= '<option value="'.$leadTag.'"'.$selected.'>'.$leadTag.'</option>';
                    }

				}
                    $data .= '</select><label>Enrollee Tag</label><span class="error" id="error_lead_tags"></span></div></div>';
        }

		$checked = ($specific == 'Leads') ? "checked='checked'" : "";
		$data .= '<div class="col-md-2 text-center"><div class="form-group "><label class="mn  p-t-5 fs12"><input type="checkbox" name="specific_user_group" id="specific_user_group" '.$checked.'><span id="specific_user_group_label">Specific Enrollees </span></label></div></div>';
	}else if($userGroup == "Members"){
		if(empty($specific)){
			// get active products and products status
	        	$data .= '<div class="col-md-1"><div class="form-group  text-center"><p class="mn text-light-gray fs12 p-t-7 text-nowrap">— Filter —</p></div></div>';

				$companyArr = get_active_global_products_for_filter($group_id,false,false,true);
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
		
				$data .= '<div class="col-md-2 text-center"><div class="form-group "><label class="mn  p-t-5 fs12"><input type="checkbox" name="specific_user_group" id="specific_user_group"><span id="specific_user_group_label">Specific '.(($userGroup != "Employer Groups") ? $userGroup : "Groups").'</span></label></div></div>';
        }else{

			// get list of active agents in system
        	if($specific == 'Members'){
				$selMember = "SELECT id,rep_id as mbrDispId,CONCAT(fname,' ', lname) as name,type FROM customer WHERE is_deleted='N' AND status NOT IN ('Customer Abandon','Pending Quote','Invited') AND type IN ('Customer') AND sponsor_id IN ($group_id)";
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

	        $checked = (!empty($specific) && in_array($specific,array("Members"))) ? "checked='checked'" : "";
			
	        $data .= '<div class="col-md-2 text-center"><div class="form-group "><label class="mn  p-t-5 fs12"><input type="checkbox" name="specific_user_group" id="specific_user_group" '.$checked.'><span id="specific_user_group_label">Specific '.(($userGroup != "Employer Groups") ? $userGroup : "Groups").'</span></label></div></div>';
        }
	}

	$res['data'] = $data;

	header('Content-type: application/json');
	echo json_encode($res); 
	dbConnectionClose();
	exit;
?>