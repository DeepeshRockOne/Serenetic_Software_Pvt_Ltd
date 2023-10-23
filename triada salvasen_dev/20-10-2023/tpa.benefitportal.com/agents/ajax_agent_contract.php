<?php
include_once __DIR__ . '/includes/connect.php';
include_once __DIR__ . '/../includes/function.class.php';
$functionsList = new functionsList();

$validate = new Validation();


$REQ_URL = ($_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);

// $agent_id = !empty($_POST['agent_id']) ? $_POST['agent_id'] : 0;
$agent_id = $hdn_cust_id = !empty($_POST['agent_id']) ? $_POST['agent_id'] : 0;
$fname = !empty($_POST['fname']) ? $_POST['fname'] : '';
$lname = !empty($_POST['lname']) ? $_POST['lname'] : '';
$email = checkIsset($_POST['email']);
$cell_phone = !empty($_POST['cell_phone']) ? phoneReplaceMain($_POST['cell_phone']) : '';
$password = !empty($_POST['password']) ? $_POST['password'] : '';
$c_password = !empty($_POST['c_password']) ? $_POST['c_password'] : '';


$validate->string(array('required' => true, 'field' => 'fname', 'value' => $fname), array('required' => 'First name is required'));
$validate->string(array('required' => true, 'field' => 'lname', 'value' => $lname), array('required' => 'Last name is required'));

$validate->digit(array('required' => true, 'field' => 'phone', 'value' => $cell_phone, 'min' => 10, 'max' => 10), array('required' => 'Phone Number is required', 'invalid' => 'Valid Phone Number is required'));


$validate->email(array('required' => true, 'field' => 'email', 'value' => $email), array('required' => 'Email is required', 'invalid' => 'Valid Email is required'));
/*if ($email != "") {
	$selectEmail = "SELECT email FROM customer WHERE type='Agent' AND email = :email AND id!=:id AND is_deleted='N'";
	$where_select_email = array(':email' => $email, ":id" => $agent_id);
	$resultEmail = $pdo->selectOne($selectEmail, $where_select_email);
	if ($resultEmail) {
		$validate->setError("email", "This email is already associated with another agent account. <a href='".$AGENT_HOST."'>Click Here</a> to login");
	}
}*/

$validate->string(array('required' => true, 'field' => 'password', 'value' => $password), array('required' => 'Password is required'));
$validate->string(array('required' => true, 'field' => 'c_password', 'value' => $c_password), array('required' => 'Confirm Password is required'));
//for strong password
if (!$validate->getError('password')) {
	if (strlen($password) < 8 || strlen($password) > 20) {
		$validate->setError('password', 'Password must be 8-20 characters');
	} else if ((!preg_match('`[A-Z]`', $password) || !preg_match('`[a-z]`', $password)) // at least one alpha
		 || !preg_match('`[0-9]`', $password)) {
		// at least one digit
		$validate->setError('password', 'Valid Password is required');
	} else if (!ctype_alnum($password)) {
		$validate->setError('password', 'Special character not allowed');
	} else if (preg_match('`[?/$\*+]`', $password)) {
		$validate->setError('password', 'Password not valid');
	} else if (preg_match('`[,"]`', $password)) {
		$validate->setError('password', 'Password not valid');
	} else if (preg_match("[']", $password)) {
		$validate->setError('password', 'Password not valid');
	}
}
if (!$validate->getError('c_password') && !$validate->getError('password')) {
	if ($password != $c_password) {
		$validate->setError('c_password', 'Both Password must be same');
	}
}


if ($validate->isValid()) {

	if ($hdn_cust_id == 0) {
		$sponsor_id = $_SESSION['AGENT_INFO']['sponsor_id'];
		$coded_level = $_SESSION['AGENT_INFO']['level_id'];
		$allow_sell_prd = $_SESSION['AGENT_INFO']['allow_to_sell'];
		$upline_sponsors = getUplineSponsor($_SESSION['AGENT_INFO']['sponsor_id']);
		$level = getUplineLevel($_SESSION['AGENT_INFO']['sponsor_id'])+1;
		$agent_coded_level =  $agentCodedRes[$coded_level]['level'];
		$enroll_sql = "SELECT id,sponsor_id,rep_id FROM customer WHERE email = :email AND status in ('Agent Abandon','Invited') AND type='Agent' AND is_deleted='N'";
		$where_cust_email = array(':email' => $email);
		$res_enroll = $pdo->selectOne($enroll_sql, $where_cust_email);

		$feature_res = $pdo->selectOne("SELECT id,fname,lname,rep_id,access_type,feature_access FROM customer WHERE id=:id", array(":id" => $sponsor_id));

		$access_type = $feature_res["access_type"];
		$feature_list = $feature_res["feature_access"];

		$cust_params = array(
			'fname' => $fname,
			'lname' => $lname,
			'email' => $email,
			'cell_phone' => $cell_phone,
			'type' => 'Agent',
			'status' => 'Invited',
			'sponsor_id' => $sponsor_id,
			'upline_sponsors' => $upline_sponsors,
			'level' => $level,
			'invite_at' => 'msqlfunc_NOW()',
			'access_type' => $access_type,
			'feature_access' => $feature_list,
			'allow_to_sell' => $allow_sell_prd,
		);

		if (count($res_enroll) > 0) {
			$agent_id = $res_enroll['id'];
			
			if ($res_enroll['sponsor_id'] == 0) {
				$cust_params['sponsor_id'] = $sponsor_id;
				$cust_params['upline_sponsors'] = $upline_sponsors;
				$cust_params['level'] = $level;
			}
			$upd_where = array(
				'clause' => 'id = :id',
				'params' => array(
					':id' => $agent_id,
				),
			);
			$pdo->update('customer', $cust_params, $upd_where);
			$agent_display_id = $res_enroll['rep_id'];

			/*--- Update Lead ---*/
			$lead_params = array(
				'sponsor_id' => $sponsor_id,
				'fname' => $fname,
				'lname' => $lname,
				'email' => $email,
				'cell_phone' => $cell_phone,
				'status' => 'Working',
				'country' => 'United States',
			);		
			$upd_where = array(
				'clause' => 'customer_id = :customer_id',
				'params' => array(
					':customer_id' => $agent_id,
				),
			);
			$pdo->update('leads', $lead_params, $upd_where);
			/*---/Update Lead ---*/
		} else {
			$cust_params1 = array(
				'company_id' => 3,
				'display_id' => get_agent_display_id(),
				'rep_id' => get_agent_id(),
				'created_at' => 'msqlfunc_NOW()',
			);
			$cust_params = array_merge($cust_params,$cust_params1);
			$agent_id = $pdo->insert("customer", $cust_params);
	
			$agent_display_id = $cust_params['rep_id'];
	
			$extra['email_trigger_id'] = 17;
			$extra['sms_trigger_id'] = 17;
			$extra['en_email'] = $email;
			$extra['en_fname'] = $fname;
			$extra['en_lname'] = $lname;
			$extra['en_display_id'] = $agent_display_id;
			$enrollment_url = $_SERVER["HTTP_REFERER"];
			$extra['user_admin_id'] = $feature_res['id'];
			$extra['user_display_id'] = $feature_res['rep_id'];
			$extra['user_fname'] = $feature_res['fname'];
			$extra['user_lname'] = $feature_res['lname'];
			$description['ac_agent'] = "AGENT: ". $fname.' '.$lname.'('.$agent_display_id.')';
			$description['ac_url'] = "URL: ". $enrollment_url;
			$email_link = 'trigger_detail_popup.php?trigger_id=17&type=email';
			$sms_link = 'trigger_detail_popup.php?trigger_id=17&type=sms';
			$description['ac_email_link'] = array(
				'email_popup'=>['text'=>'Email:','label'=>'17','href'=>$email_link,'class'=>'email_content_popup','title'=>'','id'=>'','on_click'=>'','data_toggle'=>''],
				'email_resend'=>['text'=>'','label'=>'','href'=>'javascript:void(0)','class'=>'fa fa-mail-forward fa-lg m-l-10','title'=>'Resend','id'=>'email_resend','on_click'=>"email_resend(17,'".$email."')",'data_toggle'=>'tooltip']
			);
			$description['ac_sms_link'] = array(
				'sms_popup'=>['text'=>'SMS:','label'=>'17','href'=>$sms_link,'class'=>'email_content_popup','title'=>'','id'=>'','on_click'=>'','data_toggle'=>''],
				'email_resend'=>['text'=>'','label'=>'','href'=>'javascript:void(0)','class'=>'fa fa-mail-forward fa-lg m-l-10','title'=>'Resend','id'=>'email_resend','on_click'=>"email_resend(17,'".$email."','sms')",'data_toggle'=>'tooltip']
			);
			$description['invited_by'] = 'Invited By:'.$feature_res['fname'].' '.$feature_res['lname'].' ('.$feature_res['rep_id'].')';
			$ag_ext['email_link'] = 'trigger_detail_popup.php?trigger_id=17&type=email';
			$ag_ext['sms_link'] = 'trigger_detail_popup.php?trigger_id=17&type=email';
	
			$extra['user_description'] = $ag_ext;
			
			activity_feed(3, $sponsor_id, 'Agent', $agent_id, 'customer', 'Invited Agents', $fname, $lname,json_encode($description), $enrollment_url, json_encode($extra));
			unset($description['ac_agent']);
			activity_feed(3, $agent_id, 'Agent', $agent_id, 'customer', 'Agent Invited', $fname, $lname,json_encode($description), $enrollment_url, json_encode($extra));

			/*--- Lead ---*/
			$enroll_lead_sql = "SELECT id FROM leads WHERE lead_type='Agent/Group' AND sponsor_id=:sponsor_id AND email=:email AND is_deleted='N' AND status IN('New')";
			$where_lead_params = array(':sponsor_id'=>$sponsor_id,':email'=>$email);
			$res_lead_enroll = $pdo->selectOne($enroll_lead_sql, $where_lead_params);

			$lead_params = array(
				'sponsor_id' => $sponsor_id,
				'customer_id' => $agent_id,
				'fname' => $fname,
				'lname' => $lname,
				'email' => $email,
				'cell_phone' => $cell_phone,
				'status' => 'Working',
				'country' => 'United States',
			);
			
			if (count($res_lead_enroll) > 0) {
				$inserted_id = $res_lead_enroll['id'];
				$upd_where = array(
					'clause' => 'id = :id',
					'params' => array(
						':id' => $res_lead_enroll['id'],
					),
				);
				$pdo->update('leads', $lead_params, $upd_where);
		
				$desc = array();
				$desc['ac_message'] = array(
					'ac_red_1' => array(
						'href' => 'agent_detail_v1.php?id=' . md5($feature_res['id']),
						'title' => $feature_res['rep_id'],
					),
					'ac_message_1' => ' Invited lead as agent'
				);
				$desc = json_encode($desc);
				activity_feed(3,$inserted_id,'Lead',$inserted_id,'Lead','Invited Lead As Agent',$fname,$lname,$desc);
			} else {
				$lead_params['lead_id']=get_lead_id();
				$lead_params['lead_type']='Agent/Group';
				$lead_params['opt_in_type']='Agent/Group Invite';
				$lead_params['created_at']='msqlfunc_NOW()';
		
				$inserted_id = $pdo->insert("leads", $lead_params);
		
				$desc = array();
				$desc['ac_message'] = array(
					'ac_red_1' => array(
						'href' => 'agent_detail_v1.php?id=' . md5($feature_res['id']),
						'title' => $feature_res['rep_id'],
					),
					'ac_message_1' => ' Invited lead as agent'
				);
				$desc = json_encode($desc);
				activity_feed(3,$inserted_id,'Lead',$inserted_id,'Lead','Invited Lead As Agent',$fname,$lname,$desc);
			}
			/*---/Lead ---*/
		}

		$customer_settings = array(
			'agent_coded_id' => $coded_level,
			'agent_coded_level' => $agent_coded_level,
		);
		$agentSettings=$functionsList->addCustomerSettings($customer_settings,$agent_id);

		//checking for existing product assignments and rules
			$checkPrdSql = "SELECT id FROM agent_product_rule WHERE agent_id=:agent_id  AND is_deleted='N'";
			$checkPrdRow = $pdo->selectOne($checkPrdSql, array(":agent_id" => $agent_id));
			if ($checkPrdRow) {
				//deleting all previous assigned rules
				$updateSql = array("is_deleted" => 'Y', "updated_at" => 'msqlfunc_NOW()');
				$updateWhere = array("clause" => "agent_id=:agent_id", "params" => array(":agent_id" => $agent_id));
				$pdo->update("agent_product_rule", $updateSql, $updateWhere);
			}
			//get parent agent products
			$productSql = "SELECT p.id,p.product_code,p.name as prdName,p.type,p.parent_product_id,pc.title as company_name 
                    FROM prd_main p
                    JOIN agent_product_rule rp ON (rp.product_id=p.id AND rp.is_deleted='N' AND rp.status='Contracted' AND rp.agent_id=:agent_id)
                    LEFT JOIN prd_category pc ON (pc.id = p.category_id)
                    WHERE p.is_deleted='N' AND p.status='Active' AND p.type!='Fees' AND p.product_type ='Direct Sale Product'
                    ORDER BY company_name,p.name ASC";
    		$products = $pdo->select($productSql, array(":agent_id" => $sponsor_id));

			foreach ($products as $pKey => $pVal) {
				$commission_rule_id = $functionsList->getCommissionRuleId($pVal['id'],$sponsor_id);
				$insert_product_rule = array(
					'agent_id' => $agent_id,
					'product_id' => $pVal['id'],
					'commission_rule_id'=>$commission_rule_id,
					'status' => 'Pending Approval',
					'created_at' => 'msqlfunc_NOW()',
				);
				if(!empty($commission_rule_id)){
					$assignCommissionRule = $functionsList->assignCommissionRuleToAgent($agent_id,$pVal['id'],$commission_rule_id);
					$ap_rule_id = $pdo->insert("agent_product_rule", $insert_product_rule);
				}
			}
			//Assign Merchant Processor start
				$processor = $pdo->selectOne("SELECT GROUP_CONCAT(id) as ids from payment_master where is_assigned_to_all_agent='Y' AND is_deleted='N'");
				if(!empty($processor['ids'])){
					$processorArr = explode(',',$processor['ids']);
					if(!empty($processorArr)){
						foreach($processorArr as $id){
							$pdo->insert('payment_master_assigned_agent',array("agent_id"=>$agent_id,"payment_master_id"=>$id));
						}
					}
				}
				if(!empty($sponsor_id)){
					$typeIncr = '';		
					if($agent_coded_level == 'LOA'){
						$typeIncr= " AND (include_downline='Y' OR loa_only='Y')";
					}else{
						$typeIncr= " AND (include_downline='Y')";
					}
					$processorRes = $pdo->selectOne("SELECT GROUP_CONCAT(res.payment_master_id) as ids from payment_master p JOIN (
						SELECT GROUP_CONCAT(distinct(payment_master_id)) as payment_master_id from payment_master_assigned_agent WHERE agent_id=:sponsor_id AND is_deleted='N' ".$typeIncr.") res ON (p.id IN(res.payment_master_id)) AND p.is_deleted='N'",array(":sponsor_id"=>$sponsor_id));
					if(!empty($processorRes['ids'])){
						$processorArrRes = explode(',',$processorRes['ids']);
						if(!empty($processorArrRes)){
							foreach($processorArrRes as $id){
								$prRes = $pdo->selectOne("SELECT id from payment_master_assigned_agent where payment_master_id=:payment_master_id AND agent_id=:agent_id AND is_deleted='N'",array("agent_id"=>$agent_id,"payment_master_id"=>$id));
								if(empty($prRes['id'])){
									$pdo->insert('payment_master_assigned_agent',array("agent_id"=>$agent_id,"payment_master_id"=>$id));
								}
							}
						}
					}
				}
			//Assign Merchant Processor end
	}

	$custSql = "SELECT c.rep_id,s.email as sponsor_email,s.fname as sponsor_fname,c.sponsor_id 
		FROM customer c
		JOIN customer_settings cs ON (c.id=cs.customer_id)
		JOIN customer s ON (s.id=c.sponsor_id)
		WHERE (c.status in('Agent Abandon','Invited') OR cs.recontract_status='Pending') AND c.id=:id";
	$custWhr = array(':id' => $agent_id);
	$custRes = $pdo->selectOne($custSql, $custWhr);

	if (!empty($custRes)) {
		$sponsor_id = $custRes['sponsor_id'];
		$sponsor_email = $custRes['sponsor_email'];
		$sponsor_name = $custRes['sponsor_fname'];
		$agentRepID = $custRes["rep_id"];


		//************* Customer Update Code Start *************
			$params = array(
				'fname' => makesafe($fname),
				'lname' => makesafe($lname),
				'email' => $email,
				'cell_phone' => makesafe($cell_phone),
				'joined_date' => 'msqlfunc_NOW()',
				'password' => "msqlfunc_AES_ENCRYPT('" . $password . "','" . $CREDIT_CARD_ENC_KEY . "')",
				'status' => 'Pending Documentation',
			);
		
			$upd_where = array(
				'clause' => 'id = :id',
				'params' => array(
					':id' => $agent_id,
				),
			);
			$pdo->update('customer', $params, $upd_where);
		//************* Customer Update Code End   *************

		//************* Lead Update Code Start *************
			$leads_update_params = array(
				'fname'=>$fname,
				'lname'=>$lname,
				'email'=>$email,
				'cell_phone' => makeSafe($cell_phone),
				'status' => "Converted",
			);
			$leads_update_where = array(
				'clause' => 'customer_id = :customer_id',
				'params' => array(
					':customer_id' => $agent_id,
				),
			);
			$pdo->update("leads", $leads_update_params, $leads_update_where);
		//************* Lead Update Code End   *************

		//************* Invite Agent Send Mail Code Start *************
			$rep_params = array();
			$rep_params['fname'] = $fname;
			$rep_params['lname'] = $lname;
			$rep_params['name'] = $fname . ' ' . $lname;
			$rep_params['email'] = $email;
			$rep_params['username'] = $fname . ' ' . $lname;
			$rep_params['agentname'] = $fname . ' ' . $lname;

			$rep_params['link'] = $HOST;
			$rep_params['invite_link'] = $HOST;
			$rep_params['userid'] = $agentRepID;
			$rep_params['phone'] = $cell_phone;
			$rep_params['login'] = $email;

			$rep_params['USER_IDENTITY'] = array('rep_id' => $agentRepID, 'cust_type' => 'Agent', 'location' => $REQ_URL);
			// Agent - Welcome Email
			$trigger_id = 20;

			$smart_tags = get_user_smart_tags($agent_id,'agent');
                
		    if($smart_tags){
		        $rep_params = array_merge($rep_params,$smart_tags);
		    }
			trigger_mail($trigger_id, $rep_params, $email);
		//************* Invite Agent Send Mail Code End   *************

		//************* Agent Sponsor Send Mail Code Start *************
			$rep_params['fname'] = $sponsor_name;
			// Agent - Agent Setup Successfully
			$trigger_id2 = 79;
			if (!empty($sponsor_email)) {
				trigger_mail($trigger_id2, $rep_params, $sponsor_email);
			}
		//************* Agent Sponsor Send Mail Code End   *************

		//************* Audit Log Code Start *************
			$user_data = array();
			$user_data['id'] = $agent_id;
			$user_data['user_id'] = $agent_id;
			$user_data['display_id'] = $agentRepID;
			$user_data['agent_ID'] = $agentRepID;
			$user_data['fname'] = makesafe($fname);
			$user_data['lname'] = makesafe($lname);
			$user_data['full_name'] = $fname.' '.$lname;
			$user_data['user_type'] = 'Agent';
			$user_data['type'] = 'Agent';
			$audit_log_id = audit_log($user_data, $agent_id, "Agents", "Agent Account Created", '', '', 'agent account created');
		//************* Audit Log Code End   *************

		//************* Admin AND Agent Notification Code Start *************
			addAdminNotification(0, 1, "{HOST}/agent_detail_v1.php?id=" . md5($agent_id), 0, 'N', $agent_id);
			addAgentNotification($sponsor_id, 18, "{AGENT}/agent_list.php?1=1", 0, 'N', $agent_id,$fname.' '.$lname,"Agent","Agent");
		//************* Admin AND Agent Notification Code End   *************

		//************* Notification Email To Chris Code Start *************
			$trigger_id = 6;
			if($SITE_ENV == 'Live'){
				$NOTIFICATION_EMAIL = array("cpearson@cyberxllc.com");
				$rep_params = array();
				$rep_params["fname"] = $fname;
				$rep_params["lname"] = $lname;
				$rep_params["agent_ID"] = $agentRepID;
				$rep_params['link'] = $ADMIN_HOST . "/agent_detail_v1.php?id=" .md5($agent_id);

				$smart_tags = get_user_smart_tags($agent_id,'agent');
                
			    if($smart_tags){
			        $rep_params = array_merge($rep_params,$smart_tags);
			    }
				trigger_mail($trigger_id, $rep_params, $NOTIFICATION_EMAIL);
			}
		//************* Notification Email To Chris Code End   *************

		//************* Activity Feed Generate Code Start *************
			$email_link_agent = "trigger_detail_popup.php?trigger_id=".$trigger_id."&type=email";
			$email_link_admin = "trigger_detail_popup.php?trigger_id=".$trigger_id."&type=email";
			// $description = array('parent_agent_trigger'=>$trigger_id2,'admin_trigger'=>$trigger_id);
			$description['ac_link'] = array(
				'email_agent'=>['text'=>'Email sent to parent agent: ','label'=>$trigger_id2,'href'=>$email_link_agent,'class'=>'email_content_popup','title'=>'','id'=>'','on_click'=>'','data_toggle'=>''],
				'email_admin'=>['text'=>'Email sent to admin: ','label'=>$trigger_id,'href'=>$email_link_admin,'class'=>'email_content_popup','title'=>'','id'=>'','on_click'=>'','data_toggle'=>'']
			);
			activity_feed(3, $agent_id, 'Agent', $agent_id, 'customer', 'Agent Invite Accepted', $fname, $lname,json_encode($description));
		//************* Activity Feed Generate Code End   *************

		$response['status'] = 'account_approved';
		$response['agent_id'] = $agent_id;
		$response['agent_rep_id'] = $agentRepID;
	} else {
		$response['status'] = 'no_agent_found';
		// setNotifyError("No Agent Found");
	}
	
}else{
	$response['status'] = "fail";
	$response['errors'] = $validate->getErrors();
}


function getUplineSponsor($agent_id) {
	global $pdo;
	$res = $pdo->selectOne("SELECT upline_sponsors from customer where id=:id", array(":id" => $agent_id));
	if ($res["upline_sponsors"]) {
		return $res["upline_sponsors"] . $agent_id . ",";
	} else {
		return "";
	}
}
function getUplineLevel($agent_id) {
	global $pdo;
	$res = $pdo->selectOne("SELECT level,upline_sponsors from customer where id=:id", array(":id" => $agent_id));
	if ($res["upline_sponsors"]) {
		return $res["level"];
	} else {
		return 0;
	}
}

header('Content-Type: application/json');
echo json_encode($response);
dbConnectionClose();
exit();
?>