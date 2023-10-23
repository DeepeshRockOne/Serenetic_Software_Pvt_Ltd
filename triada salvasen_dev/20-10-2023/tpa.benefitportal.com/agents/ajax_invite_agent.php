<?php
include_once 'layout/start.inc.php';
include_once dirname(__DIR__) . '/libs/twilio-php-master/Twilio/autoload.php';
include_once __DIR__ . '/../includes/function.class.php';
$functionsList = new functionsList();
$REQ_URL = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$validate = new Validation();

$access_type = $_POST['access_type'];
$allow_sell_prd = $_POST['allow_sell_prd'];
$profile_id = $_POST['profile_id'];


$sponsor_id = isset($_POST['parent_agent_id']) ? $_POST['parent_agent_id'] : '';

$fname = isset($_POST['fname']) ? $_POST['fname'] : '';
$lname = isset($_POST['lname']) ? $_POST['lname'] : '';
$cell_phone = isset($_POST['cell_phone']) ? $_POST['cell_phone'] : '';
$email = checkIsset($_POST['email']);

$cell_phone = phoneReplaceMain($cell_phone);

$products = isset($_POST['products']) ? $_POST['products'] : array();
$coded_level = isset($_POST['coded_level']) ? $_POST['coded_level'] : '';

$send_contract_radio = isset($_POST['send_contract_radio']) ? $_POST['send_contract_radio'] : array();
$select_type = isset($_POST['select_type']) ? $_POST['select_type'] : array();

$sms_content = isset($_POST['sms_content']) ? $_POST['sms_content'] : '';
$email_from = checkIsset($_POST['email_from']);
$email_subject = isset($_POST['email_subject']) ? $_POST['email_subject'] : '';
$email_content = isset($_POST['email_content']) ? $_POST['email_content'] : '';

$REAL_IP_ADDRESS = get_real_ipaddress();
$validate->string(array('required' => true, 'field' => 'parent_agent', 'value' => $sponsor_id), array('required' => 'Please select sponsor'));

if(empty($validate->getError('parent_agent'))) {
	$default_agent_row = $pdo->selectOne("SELECT upline_sponsors,level FROM customer  WHERE id=:id AND is_deleted='N'",array(":id"=>$sponsor_id));

	if(!empty($default_agent_row)) {
		$upline_sponsors = $default_agent_row["upline_sponsors"] . $sponsor_id .',';
		$level = $default_agent_row["level"] + 1;
	} else {
		$validate->setError('parent_agent',"Sponsor not found");
	}
}


$validate->string(array('required' => true, 'field' => 'fname', 'value' => $fname), array('required' => 'First Name is required'));
$validate->string(array('required' => true, 'field' => 'lname', 'value' => $lname), array('required' => 'Last Name is required'));
$validate->string(array('required' => true, 'field' => 'cell_phone', 'value' => $cell_phone), array('required' => 'Please Enter Phone'));
if(!$validate->getError('cell_phone')){
    $select_lead_phone = "SELECT id,is_email_unsubscribe, is_sms_unsubscribe, status, sponsor_id FROM leads WHERE cell_phone=:cell_phone AND is_deleted='N'";
    $where_select_phone = array(':cell_phone' => $cell_phone);
    $result_lead_phone_num = $pdo->selectOne($select_lead_phone, $where_select_phone);
    if (count($result_lead_phone_num) > 0) {
        if(($result_lead_phone_num['is_email_unsubscribe'] == 'Y') || ($result_lead_phone_num['is_sms_unsubscribe'] == 'Y') || ($result_lead_phone_num['status'] == 'Request Do Not Contact')){
            $validate->setError("cell_phone", "This mobile number requested for Request Do Not Contact");
        }
    }
}

$validate->string(array('required' => true, 'field' => 'email_from', 'value' => $email_from), array('required' => 'Email is required'));
if(!preg_match('/^([a-z0-9]+)([\._-][a-z0-9]+)*@([a-z0-9]+)(\.)+[a-z]{2,6}$/ix', trim($email_from))) {
	$validate->setError("email_from", "Valid Email is required");
}
$validate->email(array('required' => true, 'field' => 'email', 'value' => $email), array('required' => 'Email is required', 'invalid' => 'Valid email is required'));
/*if(!$validate->getError('email')){
	$selectEmail = "SELECT id, email, type FROM customer WHERE email = :email AND type IN('Agent','Group') AND is_deleted='N'";
	$where_select_email = array(':email' => $email);
	$resultEmail = $pdo->selectOne($selectEmail, $where_select_email);
	if ($resultEmail) {
		if($resultEmail['type'] == "Agent") {
			$validate->setError("email", "This email is already associated with another agent account");
		} else {
			$validate->setError("email", "This email is already associated with another group account");	
		}
	}
}*/

$validate->string(array('required' => true, 'field' => 'coded_level', 'value' => $coded_level), array('required' => 'Please select level'));

if (empty($send_contract_radio)) {
	$validate->setError("send_contract_radio", "Please select option");
}
if ($send_contract_radio=='yes' && empty($select_type)) {
	$validate->setError("select_type", "Please select option");
}

if ($send_contract_radio == "yes"){ 
	if(in_array($select_type,array('text','email_text'))) {
		$validate->string(array('required' => true, 'field' => 'sms_content', 'value' => $sms_content), array('required' => 'Sms Content is required'));
		if (strpos($sms_content, '[[AgentEnrollmentLink]]') == false && !empty($sms_content)) {
			$validate->setError("sms_content", "[[AgentEnrollmentLink]] is required in the text message");
		}
	}
	if(in_array($select_type,array('email','email_text'))) {
		$validate->string(array('required' => true, 'field' => 'email_content', 'value' => $email_content), array('required' => 'Email content is required'));
		if (strpos($email_content, '[[AgentEnrollmentLink]]') == false && !empty($email_content)) {
			$validate->setError("email_content", "[[AgentEnrollmentLink]] is required in the email");
		}
	}
}


if (count($products) == 0){
	$validate->setError("products", "Please select at least one product");
}else{
	
	$withOutCommissionPrd = array();
	foreach ($products as $pKey => $pVal) {
		$getCommissionRuleId=$functionsList->getCommissionRuleId($pVal,$sponsor_id);
		
		if ($getCommissionRuleId == 0) {
			$withOutCommissionPrd[] = $pVal;
		}
	}
	if (!empty($withOutCommissionPrd)) {
		$getProductsName = $pdo->selectOne("SELECT group_concat(concat(name,' (',product_code,')')) as prds_name from prd_main where id in (" . implode(',', $withOutCommissionPrd) . ")");
		$validate->setError("products", "<b>" . $getProductsName['prds_name'] . "</b> products commission rule not found!");
	}
}


if ($validate->isValid()) {


	/*$enroll_sql = "SELECT id,sponsor_id,rep_id FROM customer WHERE email = :email AND status in ('Agent Abandon','Invited') AND type = 'Agent' AND is_deleted='N'";
	$where_cust_email = array(':email' => $email);
	$res_enroll = $pdo->selectOne($enroll_sql, $where_cust_email);*/
	$res_enroll = array();
	
	$selSpon = "SELECT cs.id,cs.customer_id,
						CASE
					    WHEN cs.advance_on = 'Y' THEN 'advance'
					    WHEN cs.graded_on = 'Y' THEN 'graded'
					    ELSE 'earned'
					    END AS commType
					FROM customer_settings cs
					WHERE cs.customer_id=:agentId";
	$resSpon = $pdo->selectOne($selSpon,array(":agentId" => $sponsor_id));
	$commission_type = checkIsset($resSpon["commType"]);


	$agent_coded_level =  $agentCodedRes[$coded_level]['level'];

	$feature_res = $pdo->selectOne("SELECT feature_access FROM agent_coded_level WHERE id=:coded_level_id",array(":coded_level_id"=>$coded_level));

	$feature_list = !empty($feature_res["feature_access"]) ? $feature_res["feature_access"] : '';

	$inserted_id = '';

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

		$enroll_lead_sql = "SELECT id FROM leads WHERE lead_type='Agent/Group' AND sponsor_id=:sponsor_id AND email=:email AND is_deleted='N' AND status IN('New')";
		$where_lead_params = array(':sponsor_id'=>$sponsor_id,':email'=>$email);
		$res_lead_enroll = $pdo->selectOne($enroll_lead_sql, $where_lead_params);
		if(!empty($res_lead_enroll['id'])){
			$inserted_id = $res_lead_enroll['id'];
		}
		
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

		// $extra_arr = array();
		$extra['email_trigger_id'] = 17;
		$extra['sms_trigger_id'] = 17;
		$extra['en_email'] = $email;
		$extra['en_fname'] = $fname;
		$extra['en_lname'] = $lname;
		$extra['en_display_id'] = $agent_display_id;
		$enrollment_url = $AGENT_HOST.'/invite_agent.php';
		$extra['user_admin_id'] = $_SESSION['agents']['id'];
		$extra['user_display_id'] = $_SESSION['agents']['rep_id'];
		$extra['user_fname'] = $_SESSION['agents']['fname'];
		$extra['user_lname'] = $_SESSION['agents']['lname'];
		$description['ac_agent'] = "AGENT: ". $fname.' '.$lname.'('.$agent_display_id.')';
		$description['ac_url'] = "URL: ". $enrollment_url;
		$email_link = 'trigger_detail_popup.php?trigger_id=17&type=email';
		$sms_link = 'trigger_detail_popup.php?trigger_id=17&type=sms';
		$description['ac_email_link'] = array(
			'email_popup'=>['text'=>'Email:','label'=>'T320','href'=>$email_link,'class'=>'email_content_popup','title'=>'','id'=>'','on_click'=>'','data_toggle'=>''],
			'email_resend'=>['text'=>'','label'=>'','href'=>'javascript:void(0)','class'=>'fa fa-mail-forward fa-lg m-l-10','title'=>'Resend','id'=>'email_resend','on_click'=>"email_resend(17,'".$email."')",'data_toggle'=>'tooltip']
		);
		$description['ac_sms_link'] = array(
			'sms_popup'=>['text'=>'SMS:','label'=>'T320','href'=>$sms_link,'class'=>'email_content_popup','title'=>'','id'=>'','on_click'=>'','data_toggle'=>''],
			'email_resend'=>['text'=>'','label'=>'','href'=>'javascript:void(0)','class'=>'fa fa-mail-forward fa-lg m-l-10','title'=>'Resend','id'=>'email_resend','on_click'=>"email_resend(17,'".$email."','sms')",'data_toggle'=>'tooltip']
		);
		$description['invited_by'] = 'Invited By:'.$_SESSION['agents']['fname'].' '.$_SESSION['agents']['lname'].' ('.$_SESSION['agents']['rep_id'].')';
		$ag_ext['email_link'] = 'trigger_detail_popup.php?trigger_id=17&type=email';
		$ag_ext['sms_link'] = 'trigger_detail_popup.php?trigger_id=17&type=email';
		// $links['email_text'] = 17;

		$extra['user_description'] = $ag_ext;
		
		activity_feed(3, $sponsor_id, 'Agent', $sponsor_id, 'Agent', 'Invited Agents', $fname, $lname,json_encode($description), $enrollment_url, json_encode($extra));
		unset($description['ac_agent']);
		
		activity_feed(3, $agent_id, 'Agent', $agent_id, 'Agent', 'Agent Invited', $fname, $lname,json_encode($description), $enrollment_url, json_encode($extra));

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
	                'href' => 'agent_detail_v1.php?id=' . md5($_SESSION['agents']['id']),
                	'title' => $_SESSION['agents']['rep_id'],
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
	                'href' => 'agent_detail_v1.php?id=' . md5($_SESSION['agents']['id']),
                	'title' => $_SESSION['agents']['rep_id'],
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
		'agent_coded_profile' => 1,
		'commission_type' => $commission_type
	);
	if($commission_type == 'advance'){
		$customer_settings["advance_on"] = "Y";
	}else if($commission_type == 'graded'){
		$customer_settings["graded_on"] = "Y";
	}
	$agentSettings=$functionsList->addCustomerSettings($customer_settings,$agent_id);

	if($commission_type == 'advance'){
        $historyData = array(
                    'agent_id' => $agent_id,
                    'is_on' => "Y",
                    'admin_id' => 0,
                    'entity_action' => "Advanced Commissions ON",
                    'ip_address' => !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'],
                    'req_url' => $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
                );
        $pdo->insert("advance_comm_rule_history", $historyData);
	}else if($commission_type == 'graded'){
        $historyData = array(
                    'agent_id' => $agent_id,
                    'is_on' => "Y",
                    'admin_id' => 0,
                    'entity_action' => "Graded Commissions ON",
                    'ip_address' => !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'],
                    'req_url' => $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
                );
        $pdo->insert("graded_comm_rule_history", $historyData);
	}

	//checking for existing product assignments and rules
	$checkPrdSql = "SELECT id FROM agent_product_rule WHERE agent_id=:agent_id  AND is_deleted='N'";
	$checkPrdRow = $pdo->selectOne($checkPrdSql, array(":agent_id" => $agent_id));
	if ($checkPrdRow) {
		//deleting all previous assigned rules
		$updateSql = array("is_deleted" => 'Y', "updated_at" => 'msqlfunc_NOW()');
		$updateWhere = array("clause" => "agent_id=:agent_id", "params" => array(":agent_id" => $agent_id));
		$pdo->update("agent_product_rule", $updateSql, $updateWhere);
	}

	foreach ($products as $pKey => $pVal) {
		$commission_rule_id = $functionsList->getCommissionRuleId($pVal,$sponsor_id);
		$insert_product_rule = array(
			'agent_id' => $agent_id,
			'product_id' => $pVal,
			'commission_rule_id'=>$commission_rule_id,
			'status' => 'Pending Approval',
			'created_at' => 'msqlfunc_NOW()',
		);
		
		$assignCommissionRule = $functionsList->assignCommissionRuleToAgent($agent_id,$pVal,$commission_rule_id);
		$ap_rule_id = $pdo->insert("agent_product_rule", $insert_product_rule);
	}
	//assign variation code end
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
	// Agent - Welcome Invite
	$trigger_id = 17;
	$long_url = $HOST . '/contract/' . md5($agent_display_id);
	$url_params = array(
		"agent_id" => $agent_id,
		"lead_id" => $inserted_id,
		"dest_url" => $long_url,
	);
	$link = get_short_url($url_params);
	// trigger SmartTag replace
	$resEnrollAgent = array();
	if(!empty($sponsor_id)){
		$selEnrollAgent = "SELECT id,rep_id,CONCAT(fname,' ',lname) as name,email,cell_phone FROM customer WHERE id=:id AND type='Agent'";
		$paramsEnrollAgent = array(":id" => $sponsor_id);
		$resEnrollAgent = $pdo->selectOne($selEnrollAgent,$paramsEnrollAgent);
	}

	if ($send_contract_radio == "yes" && $email != '' && in_array($select_type,array('email','email_text'))) {
		$params = array();
		$params['fname'] = $fname;
		$params['lname'] = $lname;
		$params['link'] = $link;
		$params['AgentEnrollmentLink'] = "<a href='" . $link . "' target='_BLANK'>$link</a>";
		$params['Email'] = $email;
		$params['Phone'] = $cell_phone;
		$params['Agent'] = $fname.' '.$lname;
		$params['ParentAgent'] = !empty($resEnrollAgent['name']) ? $resEnrollAgent['name'] : '';
		
		$params['USER_IDENTITY'] = array('rep_id' => $agent_id, 'cust_type' => 'Agent', 'location' => $REQ_URL);

		$params['AgentName'] = $fname.' '.$lname;

		$params['EnrollingAgentDisplayName'] = !empty($resEnrollAgent['name']) ? $resEnrollAgent['name'] : '';
		$params['EnrollingAgentDisplayEmail'] = !empty($resEnrollAgent['email']) ? $resEnrollAgent['email'] : '';
		$params['EnrollingAgentDisplayPhone'] = !empty($resEnrollAgent['cell_phone']) ? $resEnrollAgent['cell_phone'] : '';
		
		if(!empty($email_from)){
            $params['EMAILER_SETTING']['from_mailid'] = $email_from;
            $params['EMAILER_SETTING']['from_mail_name'] = $_SESSION['agents']['fname']." ".$_SESSION['agents']['lname'];
        }

        $smart_tags = get_user_smart_tags($agent_id,'agent');
                
        if($smart_tags){
            $params = array_merge($params,$smart_tags);
        }

		trigger_mail($trigger_id, $params, $email,true,3, $email_content, $email_subject);
	}
	if ($send_contract_radio == "yes" && $cell_phone != '' && in_array($select_type,array('text','email_text'))) {
		$country_code = '+1';
		$toPhone = $country_code . $cell_phone;
		$params = array();
		$params['fname'] = $fname;
		$params['lname'] = $lname;
		$params['AgentEnrollmentLink'] = $link;
		$params['email'] = $email;
		$params['USER_IDENTITY'] = array('rep_id' => $agent_id, 'cust_type' => 'Agent', 'location' => $REQ_URL);

		$smart_tags = get_user_smart_tags($agent_id,'agent');
                
        if($smart_tags){
            $params = array_merge($params,$smart_tags);
        }

		trigger_sms($trigger_id, $toPhone, $params, true, $sms_content);
	}
	
	$response['invite_by']=$select_type;
	if($send_contract_radio=='no'){
		$response['invite_by']='personal_invite';
	}
	setNotifySuccess("Invite generated successfully");
	$response['link'] = $link;
	$response['fname'] = trim($fname);
	$response['lname'] = trim($lname);
	$response['status'] = 'success';
} else {
	$errors = $validate->getErrors();
	$response['status'] = 'fail';
	$response['errors'] = $errors;
}
header('Content-Type: application/json');
echo json_encode($response);
dbConnectionClose();
exit;
?>