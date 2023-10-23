<?php

class TriggerMailSms{
	public function get_trigger_display_id() {
		global $pdo;
		$display_id = rand(100, 999);
		$sql = "SELECT display_id FROM triggers WHERE display_id = 'T" . $display_id . "'";
		$res = $pdo->selectOne($sql);
		if (!empty($res['display_id'])) {
			return $this->get_trigger_display_id();
		} else {
			return "T" . $display_id;
		}
	}
	public function get_emailer_template_display_id() {
		global $pdo;
		$display_id = rand(100, 999);
		$sql = "SELECT display_id FROM trigger_template WHERE display_id = 'ET" . $display_id . "'";
		$res = $pdo->selectOne($sql);
		if (!empty($res['display_id'])) {
			return $this->get_emailer_template_display_id();
		} else {
			return "ET" . $display_id;
		}
	}
	public function trigger_action_mail($action,$user_id,$user_type,$specific = '',$products =array(),$extra=array()){
		global $pdo;
		$schedule_id = 0;

		$triggerSel = "SELECT t.id,t.template_id,t.type,t.user_group,t.trigger_action,t.specifically,
							t.from_email,t.from_name,t.email_subject,t.email_content,
							t.to_email_specific,t.cc_email_specific,t.bcc_email_specific,
							t.to_email_user,t.cc_email_user,t.bcc_email_user,
							t.to_phone_specific,t.to_phone_user,t.sms_content,
						GROUP_CONCAT(DISTINCT(tp.product_id)) as triggerProducts
						FROM triggers t
						LEFT JOIN trigger_products tp ON(tp.trigger_id=t.id AND tp.is_deleted='N')
						WHERE t.trigger_action=:action AND t.is_deleted='N' AND t.status='Active' GROUP BY t.id";
		$triggerParams = array(":action" => $action);
		$triggerRes = $pdo->select($triggerSel,$triggerParams);
		// pre_print($triggerRes);
		if(!empty($triggerRes)){

			if($action == "agent_onboarding"){
				$userRes = $this->recipientUserInfo($user_id,'agent');

				if(!empty($userRes)){
					foreach ($triggerRes as $triggerRow) {
						if(!isset($extra['is_cron'])){

							$schedule_id = set_communication_schedule($triggerRow['id'],$action,$user_id,$user_type,$specific,$products,$extra);

							if(!empty($schedule_id)){
								continue;
							}
						}
						if($triggerRow['type'] == 'Both' || $triggerRow['type'] == 'Email'){
							$this->sendTriggerActionEmail($triggerRow,$userRes,$user_id,$user_type);
						}

						if($triggerRow['type'] == 'Both' || $triggerRow['type'] == 'SMS'){
							$this->sendTriggerActionSMS($triggerRow,$userRes,$user_id,$user_type);
						}
					}
				}
			}else if($action == "group_onboarding"){
				$userRes = $this->recipientUserInfo($user_id,'group');

				if(!empty($userRes)){
					foreach ($triggerRes as $triggerRow) {

						if(!isset($extra['is_cron'])){

							$schedule_id = set_communication_schedule($triggerRow['id'],$action,$user_id,$user_type,$specific,$products,$extra);

							if(!empty($schedule_id)){
								continue;
							}
						}

						if($triggerRow['type'] == 'Both' || $triggerRow['type'] == 'Email'){
							$this->sendTriggerActionEmail($triggerRow,$userRes,$user_id,$user_type);
						}

						if($triggerRow['type'] == 'Both' || $triggerRow['type'] == 'SMS'){
							$this->sendTriggerActionSMS($triggerRow,$userRes,$user_id,$user_type);
						}
					}
				}
			}else if($action == "member_enrollment"){
				$today = date("Y-m-d");
				if(isset($extra['is_cron']) && isset($extra['request_date'])){
					$today = $extra['request_date'];
				}
				$feeProducts = $pdo->selectOne("SELECT GROUP_CONCAT(DISTINCT(id)) AS feeProducts FROM prd_main WHERE type='Fees' AND is_deleted='N'");
				$feeProductArr = !empty($feeProducts["feeProducts"]) ? explode(",", $feeProducts["feeProducts"]) : array();
				
				$tmpUserId = $user_id;
				$tmpUserType = $user_type;
				
				foreach ($triggerRes as $triggerRow) {

					if(!isset($extra['is_cron'])){

						$schedule_id = set_communication_schedule($triggerRow['id'],$action,$user_id,$user_type,$specific,$products,$extra);

						if(!empty($schedule_id)){
							continue;
						}
					}

					if(in_array($triggerRow["user_group"], array("agent","group")) && $user_type == 'member'){
						$selSpon = "SELECT sponsor_id FROM customer WHERE id=:id AND is_deleted='N' AND type='Customer'";
						$resSpon = $pdo->selectOne($selSpon,array(":id"=>$user_id));
						if(!empty($resSpon["sponsor_id"])){
							$tmpUserId = $resSpon["sponsor_id"];
							$tmpUserType = $triggerRow["user_group"] == "agent" ? "agent" : "group";
						}
					}else{
						$tmpUserId = $user_id;
						$tmpUserType = $user_type;
					}

					if($triggerRow["user_group"] == "agent"){
						$userRes = $this->recipientUserInfo($tmpUserId,'agent');
					}else if($triggerRow["user_group"] == "group"){
						$userRes = $this->recipientUserInfo($tmpUserId,'group');
					}else if($triggerRow["user_group"] == "member"){
						$userRes = $this->recipientUserInfo($tmpUserId,'member');
					}
				
					if(!empty($userRes)){
						$triggerProductsArr = !empty($triggerRow["triggerProducts"]) ? explode(",", $triggerRow["triggerProducts"]) : array();
						
						if(!empty($triggerProductsArr)){
							$productIdsArr = !empty($products) ? array_keys($products) : array();
							$matchedProducts = array_intersect($productIdsArr, $triggerProductsArr);
							// pre_print($matchedProducts);
							if(!empty($matchedProducts)){
								foreach ($matchedProducts as $prdId) {
									if($triggerRow["specifically"] == "added_date" && ($specific == "addedEffectiveDate" || $specific == "addedDate")){
										if($triggerRow['type'] == 'Both' || $triggerRow['type'] == 'Email'){
											$this->sendTriggerActionEmail($triggerRow,$userRes,$tmpUserId,$tmpUserType,$prdId);
										}

										if($triggerRow['type'] == 'Both' || $triggerRow['type'] == 'SMS'){
											$this->sendTriggerActionSMS($triggerRow,$userRes,$tmpUserId,$tmpUserType,$prdId);
										}
									}else if($triggerRow["specifically"] == "effective_date" && ($specific == "addedEffectiveDate" || $specific == "effectiveDate") && (strtotime(date("Y-m-d",strtotime($products[$prdId]))) == strtotime($today))){
										if($triggerRow['type'] == 'Both' || $triggerRow['type'] == 'Email'){
											$this->sendTriggerActionEmail($triggerRow,$userRes,$tmpUserId,$tmpUserType,$prdId);
										}

										if($triggerRow['type'] == 'Both' || $triggerRow['type'] == 'SMS'){
											$this->sendTriggerActionSMS($triggerRow,$userRes,$tmpUserId,$tmpUserType,$prdId);
										}
									}
								}
							}
						}else if(empty($triggerProductsArr) && !empty($products)){
							
							foreach ($products as $prdId => $effDate) {
								if(!in_array($prdId, $feeProductArr)){
									if($triggerRow["specifically"] == "added_date" && ($specific == "addedEffectiveDate" || $specific == "addedDate")){
										if($triggerRow['type'] == 'Both' || $triggerRow['type'] == 'Email'){
											$this->sendTriggerActionEmail($triggerRow,$userRes,$tmpUserId,$tmpUserType,$prdId);
										}
										if($triggerRow['type'] == 'Both' || $triggerRow['type'] == 'SMS'){
											$this->sendTriggerActionSMS($triggerRow,$userRes,$tmpUserId,$tmpUserType,$prdId);
										}
									}else if($triggerRow["specifically"] == "effective_date" && ($specific == "addedEffectiveDate" || $specific == "effectiveDate") && (strtotime(date("Y-m-d",strtotime($products[$prdId]))) == strtotime($today))){
										if($triggerRow['type'] == 'Both' || $triggerRow['type'] == 'Email'){
											$this->sendTriggerActionEmail($triggerRow,$userRes,$tmpUserId,$tmpUserType,$prdId);
										}
										if($triggerRow['type'] == 'Both' || $triggerRow['type'] == 'SMS'){
											$this->sendTriggerActionSMS($triggerRow,$userRes,$tmpUserId,$tmpUserType,$prdId);
										}
									}
								}
							}
						}
					}
				}
			}else if($action == "member_cancellation"){
				$today = date("Y-m-d");
				if(isset($extra['is_cron']) && isset($extra['request_date'])){
					$today = $extra['request_date'];
				}
				$feeProducts = $pdo->selectOne("SELECT GROUP_CONCAT(DISTINCT(id)) AS feeProducts FROM prd_main WHERE type='Fees' AND is_deleted='N'");
				$feeProductArr = !empty($feeProducts["feeProducts"]) ? explode(",", $feeProducts["feeProducts"]) : array();
				
				$tmpUserId = $user_id;
				$tmpUserType = $user_type;

				foreach ($triggerRes as $triggerRow) {
					if(!isset($extra['is_cron'])){
						$schedule_id = set_communication_schedule($triggerRow['id'],$action,$user_id,$user_type,$specific,$products,$extra);

						if(!empty($schedule_id)){
							continue;
						}
					}
					if(in_array($triggerRow["user_group"], array("agent","group")) && $user_type == 'member'){
						$selSpon = "SELECT sponsor_id FROM customer WHERE id=:id AND is_deleted='N' AND type='Customer'";
						$resSpon = $pdo->selectOne($selSpon,array(":id"=>$user_id));
						if(!empty($resSpon["sponsor_id"])){
							$tmpUserId = $resSpon["sponsor_id"];
							$tmpUserType = $triggerRow["user_group"] == "agent" ? "agent" : "group";
						}
					}else{
						$tmpUserId = $user_id;
						$tmpUserType = $user_type;
					}

					if($triggerRow["user_group"] == "agent"){
						$userRes = $this->recipientUserInfo($tmpUserId,'agent');
					}else if($triggerRow["user_group"] == "group"){
						$userRes = $this->recipientUserInfo($tmpUserId,'group');
					}else if($triggerRow["user_group"] == "member"){
						$userRes = $this->recipientUserInfo($tmpUserId,'member');
					}
					
					if(!empty($userRes)){
						$triggerProductsArr = !empty($triggerRow["triggerProducts"]) ? explode(",", $triggerRow["triggerProducts"]) : array();
						
						if(!empty($triggerProductsArr)){
							$productIdsArr = !empty($products) ? array_keys($products) : array();
							$matchedProducts = array_intersect($productIdsArr, $triggerProductsArr);
							
							if(!empty($matchedProducts)){
								foreach ($matchedProducts as $prdId) {
									if($triggerRow["specifically"] == "date_terminated" && ($specific == "addedTerminationDate" || $specific == "dateTerminated")){
										if($triggerRow['type'] == 'Both' || $triggerRow['type'] == 'Email'){
											$this->sendTriggerActionEmail($triggerRow,$userRes,$tmpUserId,$tmpUserType,$prdId);
										}

										if($triggerRow['type'] == 'Both' || $triggerRow['type'] == 'SMS'){
											$this->sendTriggerActionSMS($triggerRow,$userRes,$tmpUserId,$tmpUserType,$prdId);
										}
									}else if($triggerRow["specifically"] == "termination_date" && ($specific == "addedTerminationDate" || $specific == "terminationDate") && (strtotime(date("Y-m-d",strtotime($products[$prdId]))) == strtotime($today))){
										if($triggerRow['type'] == 'Both' || $triggerRow['type'] == 'Email'){
											$this->sendTriggerActionEmail($triggerRow,$userRes,$tmpUserId,$tmpUserType,$prdId);
										}

										if($triggerRow['type'] == 'Both' || $triggerRow['type'] == 'SMS'){
											$this->sendTriggerActionSMS($triggerRow,$userRes,$tmpUserId,$tmpUserType,$prdId);
										}
									}
								}
							}
						}else if(empty($triggerProductsArr) && !empty($products)){
							foreach ($products as $prdId => $termDate) {
								if(!in_array($prdId, $feeProductArr)){
									if($triggerRow["specifically"] == "date_terminated" && ($specific == "addedTerminationDate" || $specific == "dateTerminated")){

										if($triggerRow['type'] == 'Both' || $triggerRow['type'] == 'Email'){
											$this->sendTriggerActionEmail($triggerRow,$userRes,$tmpUserId,$tmpUserType,$prdId);
										}

										if($triggerRow['type'] == 'Both' || $triggerRow['type'] == 'SMS'){
											$this->sendTriggerActionSMS($triggerRow,$userRes,$tmpUserId,$tmpUserType,$prdId);
										}
									}else if($triggerRow["specifically"] == "termination_date" && ($specific == "addedTerminationDate" || $specific == "terminationDate") && (strtotime(date("Y-m-d",strtotime($products[$prdId]))) == strtotime($today))){
										if($triggerRow['type'] == 'Both' || $triggerRow['type'] == 'Email'){
											$this->sendTriggerActionEmail($triggerRow,$userRes,$tmpUserId,$tmpUserType,$prdId);
										}

										if($triggerRow['type'] == 'Both' || $triggerRow['type'] == 'SMS'){
											$this->sendTriggerActionSMS($triggerRow,$userRes,$tmpUserId,$tmpUserType,$prdId);
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}
	public function recipientUserInfo($user_id,$user_type){
		global $pdo;
		$response = array();
		if(!empty($user_id)){
			if(strtolower($user_type) == 'agent'){
				$agentSel = "SELECT 
								a.email as agentEmail,a.cell_phone as agentPhone,
								s.email as parentAgentEmail,s.cell_phone as parentAgentPhone,
								u.email as highestAgentEmail,u.cell_phone as highestAgentPhone
						FROM customer a
						LEFT JOIN customer s ON(a.sponsor_id=s.id AND s.type='Agent')
						LEFT JOIN customer u ON
							(u.id=IF(REPLACE(SUBSTRING(SUBSTRING_INDEX(a.upline_sponsors, ',', 2), LENGTH(SUBSTRING_INDEX(a.upline_sponsors, ',', 2 - 1)) + 1), ',', '') = '1' AND REPLACE(SUBSTRING(SUBSTRING_INDEX(a.upline_sponsors, ',', 3), LENGTH(SUBSTRING_INDEX(a.upline_sponsors, ',', 3 - 1)) + 1), ',', '')!='', REPLACE(SUBSTRING(SUBSTRING_INDEX(a.upline_sponsors, ',', 3), LENGTH(SUBSTRING_INDEX(a.upline_sponsors, ',', 3 - 1)) + 1), ',', ''),REPLACE(SUBSTRING(SUBSTRING_INDEX(a.upline_sponsors, ',', 2), LENGTH(SUBSTRING_INDEX(a.upline_sponsors, ',', 2 - 1)) + 1), ',', '')) AND u.type='Agent')
						WHERE a.id=:user_id AND a.is_deleted='N' AND a.type='Agent'";
				$agentParams = array(":user_id" => $user_id);
				$agentRes = $pdo->selectOne($agentSel,$agentParams);
				
				if(!empty($agentRes)){
					$response = $agentRes;
				}
			}else if(strtolower($user_type) == 'group'){
				$groupSel = "SELECT 
								g.email as groupEmail,g.cell_phone as groupPhone,
								g.business_email as groupContactEmail,g.business_phone as groupContactPhone,
								s.email as parentAgentEmail,s.cell_phone as parentAgentPhone,
								u.email as highestAgentEmail,u.cell_phone as highestAgentPhone
						FROM customer g
						JOIN customer s ON(g.sponsor_id=s.id AND s.type='Agent')
						LEFT JOIN customer u ON
							(u.id=IF(REPLACE(SUBSTRING(SUBSTRING_INDEX(g.upline_sponsors, ',', 2), LENGTH(SUBSTRING_INDEX(g.upline_sponsors, ',', 2 - 1)) + 1), ',', '') = '1' AND REPLACE(SUBSTRING(SUBSTRING_INDEX(g.upline_sponsors, ',', 3), LENGTH(SUBSTRING_INDEX(g.upline_sponsors, ',', 3 - 1)) + 1), ',', '')!='', REPLACE(SUBSTRING(SUBSTRING_INDEX(g.upline_sponsors, ',', 3), LENGTH(SUBSTRING_INDEX(g.upline_sponsors, ',', 3 - 1)) + 1), ',', ''),REPLACE(SUBSTRING(SUBSTRING_INDEX(g.upline_sponsors, ',', 2), LENGTH(SUBSTRING_INDEX(g.upline_sponsors, ',', 2 - 1)) + 1), ',', '')) AND u.type='Agent')
						WHERE g.id=:user_id AND g.is_deleted='N' AND g.type='Group'";
				$groupParams = array(":user_id" => $user_id);
				$groupRes = $pdo->selectOne($groupSel,$groupParams);
				if(!empty($groupRes)){
					$response = $groupRes;
				}
			}else if(strtolower($user_type) == 'member'){
				$customerSel = "SELECT 
								c.email as memberEmail,c.cell_phone as memberPhone,
								a.email as agentEmail,a.cell_phone as agentPhone,
								s.email as parentAgentEmail,s.cell_phone as parentAgentPhone,
								u.email as highestAgentEmail,u.cell_phone as highestAgentPhone
						FROM customer c 
						JOIN customer a ON(c.sponsor_id=a.id)
						LEFT JOIN customer s ON(a.sponsor_id=s.id AND s.type='Agent')
						LEFT JOIN customer u ON
							(u.id=IF(REPLACE(SUBSTRING(SUBSTRING_INDEX(a.upline_sponsors, ',', 2), LENGTH(SUBSTRING_INDEX(a.upline_sponsors, ',', 2 - 1)) + 1), ',', '') = '1' AND REPLACE(SUBSTRING(SUBSTRING_INDEX(a.upline_sponsors, ',', 3), LENGTH(SUBSTRING_INDEX(a.upline_sponsors, ',', 3 - 1)) + 1), ',', '')!='', REPLACE(SUBSTRING(SUBSTRING_INDEX(a.upline_sponsors, ',', 3), LENGTH(SUBSTRING_INDEX(a.upline_sponsors, ',', 3 - 1)) + 1), ',', ''),REPLACE(SUBSTRING(SUBSTRING_INDEX(a.upline_sponsors, ',', 2), LENGTH(SUBSTRING_INDEX(a.upline_sponsors, ',', 2 - 1)) + 1), ',', '')) AND u.type='Agent')
						WHERE c.id=:user_id AND c.is_deleted='N' AND c.type='Customer'";
				$customerParams = array(":user_id" => $user_id);
				$customerRes = $pdo->selectOne($customerSel,$customerParams);
				if(!empty($customerRes)){
					$response = $customerRes;
				}
			}
		}
		return $response;
	}
	public function sendTriggerActionEmail($triggerRow,$userRes,$user_id,$user_type,$product_id=''){
		global $pdo;
			$trigger_id = $triggerRow['id'];
			$email_subject = $triggerRow['email_subject'];
						
			$params = array();
			$smart_tags = get_user_smart_tags($user_id,$user_type,$product_id);
	       
            if(!empty($smart_tags)){
                $params = array_merge($params,$smart_tags);
            }

			if(!empty($triggerRow['from_email'])){
				$params['EMAILER_SETTING']['from_mailid'] = $triggerRow['from_email'];
			}
			if(!empty($triggerRow['from_name'])){
				$params['EMAILER_SETTING']['from_mail_name'] = $triggerRow['from_name'];
			}

			if(!empty($triggerRow['to_email_specific']) && !empty($triggerRow['email_content'])){
				$email = $triggerRow['to_email_specific'];
				$email_content = $triggerRow['email_content'];
				
				if(!empty($triggerRow['cc_email_specific'])){
					$params['EMAILER_SETTING']['cc_email'] = $triggerRow['cc_email_specific'];	
				}
				if(!empty($triggerRow['bcc_email_specific'])){
					$params['EMAILER_SETTING']['bcc_email'] = $triggerRow['bcc_email_specific'];	
				}
				if(!empty($email)){
					trigger_mail($trigger_id, $params, $email,true,3, $email_content, $email_subject);
				}
			}

			if(!empty($triggerRow['to_email_user']) && !empty($triggerRow['email_content'])){
				if($triggerRow['to_email_user'] == "agent" || $triggerRow['to_email_user'] == "mbr_enrolle"){
					$email = $userRes['agentEmail'];
				}else if($triggerRow['to_email_user'] == "member"){
					$email = $userRes['memberEmail'];
				}else if($triggerRow['to_email_user'] == "group"){
					$email = $userRes['groupEmail'];
				}else if($triggerRow['to_email_user'] == "billing_contact"){
					$email = $userRes['groupContactEmail'];
				}else if($triggerRow['to_email_user'] == "parent_agent"){
					$email = $userRes['parentAgentEmail'];
				}else if($triggerRow['to_email_user'] == "highest_upline_agent"){
					$email = $userRes['highestAgentEmail'];
				}

				if(!empty($triggerRow['cc_email_user'])){
					if($triggerRow['cc_email_user'] == "agent" || $triggerRow['cc_email_user'] == "mbr_enrolle"){
						$params['EMAILER_SETTING']['cc_email'] = $userRes['agentEmail'];
					}else if($triggerRow['cc_email_user'] == "member"){
						$params['EMAILER_SETTING']['cc_email'] = $userRes['memberEmail'];
					}else if($triggerRow['cc_email_user'] == "group"){
						$params['EMAILER_SETTING']['cc_email'] = $userRes['groupEmail'];
					}else if($triggerRow['cc_email_user'] == "billing_contact"){
						$params['EMAILER_SETTING']['cc_email'] = $userRes['groupContactEmail'];
					}else if($triggerRow['cc_email_user'] == "parent_agent"){
						$params['EMAILER_SETTING']['cc_email'] = $userRes['parentAgentEmail'];
					}else if($triggerRow['cc_email_user'] == "highest_upline_agent"){
						$params['EMAILER_SETTING']['cc_email'] = $userRes['highestAgentEmail'];
					}
				}

				if(!empty($triggerRow['bcc_email_user'])){
					if($triggerRow['bcc_email_user'] == "agent" || $triggerRow['bcc_email_user'] == "mbr_enrolle"){
						$params['EMAILER_SETTING']['bcc_email'] = $userRes['agentEmail'];
					}else if($triggerRow['bcc_email_user'] == "member"){
						$params['EMAILER_SETTING']['bcc_email'] = $userRes['memberEmail'];
					}else if($triggerRow['bcc_email_user'] == "group"){
						$params['EMAILER_SETTING']['bcc_email'] = $userRes['groupEmail'];
					}else if($triggerRow['bcc_email_user'] == "billing_contact"){
						$params['EMAILER_SETTING']['bcc_email'] = $userRes['groupContactEmail'];
					}else if($triggerRow['bcc_email_user'] == "parent_agent"){
						$params['EMAILER_SETTING']['bcc_email'] = $userRes['parentAgentEmail'];
					}else if($triggerRow['bcc_email_user'] == "highest_upline_agent"){
						$params['EMAILER_SETTING']['bcc_email'] = $userRes['highestAgentEmail'];
					}
				}

				$email_content = $triggerRow['email_content'];
				if(!empty($email)){
					trigger_mail($trigger_id, $params, $email,true,3, $email_content, $email_subject);
				}
			}
	}
	public function sendTriggerActionSMS($triggerRow,$userRes,$user_id,$user_type,$product_id=''){
		global $pdo;
		$trigger_id = $triggerRow['id'];
		$params = array();

		$smart_tags = get_user_smart_tags($user_id,$user_type,$product_id);

        if($smart_tags){
            $params = array_merge($params,$smart_tags);
        }

		if(!empty($triggerRow['to_phone_specific']) && !empty($triggerRow['sms_content'])){
			$toPhone = $triggerRow['to_phone_specific'];
			$sms_content = $triggerRow['sms_content'];
			
			$country_code = '+1';
			$toPhone = $country_code . $toPhone;
			trigger_sms($trigger_id, $toPhone, $params, true, $sms_content);
		}

		if(!empty($triggerRow['to_phone_user']) && !empty($triggerRow['sms_content'])){
			
			if($triggerRow['to_phone_user'] == "agent" || $triggerRow['to_phone_user'] == "mbr_enrolle"){
				$toPhone = $userRes['agentPhone'];
			}else if($triggerRow['to_phone_user'] == "member"){
				$toPhone = $userRes['memberPhone'];
			}else if($triggerRow['to_phone_user'] == "group"){
				$toPhone = $userRes['groupPhone'];
			}else if($triggerRow['to_phone_user'] == "billing_contact"){
				$toPhone = $userRes['groupContactPhone'];
			}else if($triggerRow['to_phone_user'] == "parent_agent"){
				$toPhone = $userRes['parentAgentPhone'];
			}else if($triggerRow['to_phone_user'] == "highest_upline_agent"){
				$toPhone = $userRes['highestAgentPhone'];
			}

			$country_code = '+1';
			$toPhone = $country_code . $toPhone;

			$sms_content = $triggerRow['sms_content'];
			trigger_sms($trigger_id, $toPhone, $params, true, $sms_content);
		}
	}
	public function is_unsubscribe($type,$email = '',$phone = '') {
		global $pdo,$callingCode,$callingCodeReplace;
		$incr = '';
		$params = array();

		if($type == 'email'){
			$incr .= " AND type='email' AND email=:email";
			$params[":email"] = $email;
		}elseif ($type == 'sms') {
			$incr .= " AND type='sms' AND phone=:phone";
			$params[":phone"] = str_replace($callingCode,$callingCodeReplace, $phone);
		}

		$sql = "SELECT id,type,email,phone FROM unsubscribes WHERE is_deleted='N' $incr";
		$unSubscribe = $pdo->selectOne($sql, $params);

		if (!empty($unSubscribe['id'])) {
			return true;
		} else {
			return false;
		}
	}
}

?>