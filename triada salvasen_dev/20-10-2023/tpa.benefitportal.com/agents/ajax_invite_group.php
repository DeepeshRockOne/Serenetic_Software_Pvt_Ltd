<?php
include_once 'layout/start.inc.php';
include_once dirname(__DIR__) . '/libs/twilio-php-master/Twilio/autoload.php';
include_once __DIR__ . '/../includes/function.class.php';
$functionsList = new functionsList();
$REQ_URL = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$validate = new Validation();

$agent_id = isset($_POST['agent_id']) ? $_POST['agent_id'] : '';
$group_name = isset($_POST['company_name']) ? $_POST['company_name'] : '';
$contact_person_fname = isset($_POST['fname']) ? $_POST['fname'] : '';
$contact_person_lname = isset($_POST['lname']) ? $_POST['lname'] : '';
$contact_person_email = checkIsset($_POST['email']);
// $tpa_group = isset($_POST['tpa_group']) ? $_POST['tpa_group'] : '';
$cell_phone = isset($_POST['cell_phone']) ? $_POST['cell_phone'] : '';
$contact_person_phone = phoneReplaceMain($cell_phone);


$products = isset($_POST['products']) ? $_POST['products'] : array();

$trigger_id = 61;
$send_contract_radio = isset($_POST['send_contract_radio']) ? $_POST['send_contract_radio'] : array();
$select_type = isset($_POST['select_type']) ? $_POST['select_type'] : array();

$sms_content = isset($_POST['sms_content']) ? $_POST['sms_content'] : '';
$email_from = checkIsset($_POST['email_from']);
$email_subject = isset($_POST['email_subject']) ? $_POST['email_subject'] : '';
$email_content = isset($_POST['email_content']) ? $_POST['email_content'] : '';

$validate->string(array('required' => true, 'field' => 'agent_id', 'value' => $agent_id), array('required' => 'Please select valid agent'));
$validate->string(array('required' => true, 'field' => 'group_name', 'value' => $group_name), array('required' => 'Employer Group Name is required'));
$validate->string(array('required' => true, 'field' => 'contact_person_fname', 'value' => $contact_person_fname), array('required' => 'First name is required'));
$validate->string(array('required' => true, 'field' => 'contact_person_lname', 'value' => $contact_person_lname), array('required' => 'Last Name is required'));

$validate->email(array('required' => true, 'field' => 'contact_person_email', 'value' => $contact_person_email), array('required' => 'Email is required', 'invalid' => 'Valid Email is required'));
$validate->digit(array('required' => true, 'field' => 'contact_person_phone', 'value' => $contact_person_phone, 'min' => 10, 'max' => 10), array('required' => 'Phone Number is required', 'invalid' => 'Valid Phone Number is required'));
$validate->string(array('required' => true, 'field' => 'email_from', 'value' => $email_from), array('required' => 'Email is required'));
if(!preg_match('/^([a-z0-9]+)([\._-][a-z0-9]+)*@([a-z0-9]+)(\.)+[a-z]{2,6}$/ix', trim($email_from))) {
	$validate->setError("email_from", "Valid Email is required");
}
if(!$validate->getError('contact_person_phone')){
    $select_lead_phone = "SELECT id, is_sms_unsubscribe, status, sponsor_id FROM leads WHERE cell_phone=:cell_phone AND is_deleted='N'";
    $where_select_phone = array(':cell_phone' => $contact_person_phone);
    $result_lead_phone_num = $pdo->selectOne($select_lead_phone, $where_select_phone);
    if (count($result_lead_phone_num) > 0) {
        if(($result_lead_phone_num['is_sms_unsubscribe'] == 'Y') || ($result_lead_phone_num['status'] == 'Request Do Not Contact')){
            $validate->setError("contact_person_phone", "This mobile number requested for Request Do Not Contact");
        }
    }
}

if(!$validate->getError('contact_person_email')){
  $selectEmail = "SELECT id, email, type FROM customer WHERE email = :email AND type IN('Group') AND is_deleted='N'";
  $where_select_email = array(':email' => $contact_person_email);
  $resultEmail = $pdo->selectOne($selectEmail, $where_select_email);
  if ($resultEmail) {
    if($resultEmail['type'] == "Agent") {
      $validate->setError("contact_person_email", "This email is already associated with another agent account");
    } else {
      $validate->setError("contact_person_email", "This email is already associated with another group account");  
    }
  }

  $select_lead_phone = "SELECT id,is_email_unsubscribe, status, sponsor_id FROM leads WHERE email=:email AND is_deleted='N'";
  $where_select_phone = array(':email' => $contact_person_email);
  $result_lead_phone_num = $pdo->selectOne($select_lead_phone, $where_select_phone);
    if (count($result_lead_phone_num) > 0) {
        if(($result_lead_phone_num['is_email_unsubscribe'] == 'Y')  || ($result_lead_phone_num['status'] == 'Request Do Not Contact')){
            $validate->setError("contact_person_email", "This Email requested for Request Do Not Contact");
        }
    }
}

if (empty($products)) {
    $validate->setError("products", "Please select at least one product");
}
if (empty($send_contract_radio)) {
  $validate->setError("send_contract_radio", "Please select option");
}
if ($send_contract_radio=='Y' && empty($select_type)) {
  $validate->setError("select_type", "Please select option");
}
if ($send_contract_radio == "Y"){ 
  if(in_array($select_type,array('text','email_text'))) {
    $validate->string(array('required' => true, 'field' => 'sms_content', 'value' => $sms_content), array('required' => 'Sms Content is required'));
    if (strpos($sms_content, '[[link]]') == false && !empty($sms_content)) {
      $validate->setError("sms_content", "[[link]] is required in the text message");
    }
  }
  if(in_array($select_type,array('email','email_text'))) {
    $validate->string(array('required' => true, 'field' => 'email_content', 'value' => $email_content), array('required' => 'Email content is required'));
    if (strpos($email_content, '[[link]]') == false && !empty($email_content)) {
      $validate->setError("email_content", "[[link]] is required in the email");
    }
  }
}


$default_agent_row = $pdo->selectOne("SELECT upline_sponsors,level FROM customer  WHERE id=:id AND is_deleted='N'",array(":id"=>$agent_id));

if(!empty($default_agent_row)) {
  $upline_sponsors = $default_agent_row["upline_sponsors"] . $agent_id .',';
  $level = $default_agent_row["level"] + 1;
} else {
  $validate->setError('agent_id',"Sponsor not found");
}

// $validate->string(array('required' => true, 'field' => 'tpa_group', 'value' => $tpa_group), array('required' => 'Please select option'));
if ($validate->isValid()) {
    $group_sql = $pdo->selectOne("SELECT * FROM customer WHERE email=:email AND status= 'Invited' AND type='Group'", array(':email' => $contact_person_email));

    $update_param = array(
        "fname" => $contact_person_fname,
        "lname" => $contact_person_lname,
        "email" => $contact_person_email,
        "type" => 'Group',
        "cell_phone" => $contact_person_phone,
        "business_name" => $group_name,
        "level" => $level,
        "sponsor_id" => $agent_id,
        "upline_sponsors" => $upline_sponsors,
        "status" => 'Invited',
        "access_type" => 'full_access',
        "invite_at" => "msqlfunc_NOW()",
    );
    if(!empty($group_sql)){
     
      $upd_where = array(
        'clause' => 'id = :id',
        'params' => array(
            ':id' => $group_sql['id'],
        ),
    	);
      $pdo->update('customer', $update_param, $upd_where);
      $group_id = $group_sql['id'];
      $group_display_id = $group_sql['rep_id'];
    }else{
      $update_param1 = array(
        'company_id' => 3,
        'display_id' => get_group_display_id(),
        'rep_id' => get_group_id(),
        'created_at' => 'msqlfunc_NOW()',
      );
      $update_param = array_merge($update_param,$update_param1);
      $group_id = $pdo->insert("customer", $update_param);
      $group_display_id = $update_param['rep_id'];

      $extra['email_trigger_id'] = 61;
      $extra['sms_trigger_id'] = 61;
      $extra['en_email'] = $contact_person_email;
      $extra['en_fname'] = $contact_person_fname;
      $extra['en_lname'] = $contact_person_lname;
      $extra['en_display_id'] = $group_display_id;

      $enrollment_url = $AGENT_HOST.'/invite_group.php';
      $extra['user_admin_id'] = $_SESSION['agents']['id'];
      $extra['user_display_id'] = $_SESSION['agents']['rep_id'];
      $extra['user_fname'] = $_SESSION['agents']['fname'];
      $extra['user_lname'] = $_SESSION['agents']['lname'];
      $description['ac_agent'] = "GROUP: ". $contact_person_fname.' '.$contact_person_lname.'('.$group_display_id.')';
      $description['ac_url'] = "URL: ". $enrollment_url;

      $email_link = 'trigger_detail_popup.php?trigger_id=61&type=email';
      $sms_link = 'trigger_detail_popup.php?trigger_id=61&type=sms';
      $description['ac_email_link'] = array(
        'email_popup'=>['text'=>'Email:','label'=>'T408','href'=>$email_link,'class'=>'email_content_popup','title'=>'','id'=>'','on_click'=>'','data_toggle'=>''],
        'email_resend'=>['text'=>'','label'=>'','href'=>'javascript:void(0)','class'=>'fa fa-mail-forward fa-lg m-l-10','title'=>'Resend','id'=>'email_resend','on_click'=>"email_resend(61,'".$contact_person_email."')",'data_toggle'=>'tooltip']
      );
      $description['ac_sms_link'] = array(
        'sms_popup'=>['text'=>'SMS:','label'=>'T408','href'=>$sms_link,'class'=>'email_content_popup','title'=>'','id'=>'','on_click'=>'','data_toggle'=>''],
        'email_resend'=>['text'=>'','label'=>'','href'=>'javascript:void(0)','class'=>'fa fa-mail-forward fa-lg m-l-10','title'=>'Resend','id'=>'email_resend','on_click'=>"email_resend(61,'".$contact_person_email."','sms')",'data_toggle'=>'tooltip']
      );
      $description['invited_by'] = 'Invited By:'.$_SESSION['agents']['fname'].' '.$_SESSION['agents']['lname'].' ('.$_SESSION['agents']['rep_id'].')';
      $ag_ext['email_link'] = 'trigger_detail_popup.php?trigger_id=61&type=email';
      $ag_ext['sms_link'] = 'trigger_detail_popup.php?trigger_id=61&type=email';
      

      $extra['user_description'] = $ag_ext;
      
      activity_feed(3, $agent_id, 'Agent', $group_id, 'customer', 'Invited Group', $contact_person_fname, $contact_person_lname,json_encode($description), $enrollment_url, json_encode($extra));
      unset($description['ac_agent']);
      if($agent_id != $_SESSION['agents']['id']) {
          activity_feed(3, $_SESSION['agents']['id'], 'agent', $group_id, 'customer', 'Invited Group', $contact_person_fname, $contact_person_lname,json_encode($description), $enrollment_url, json_encode($extra));  
      }      
    }
    $customer_settings = array(
      'tpa_for_billing'=>'N',//$tpa_group,
    );
    $agentSettings=$functionsList->addCustomerSettings($customer_settings,$group_id);

    $customer_group_settings = array();
    $GroupSettings = $functionsList->addCustomerGroupSettings($customer_group_settings,$group_id);
    
    $enroll_lead_sql = "SELECT id FROM leads WHERE lead_type='Agent/Group' AND sponsor_id=:sponsor_id AND email=:email AND is_deleted='N'";
    $where_lead_params = array(':sponsor_id'=>$agent_id,':email'=>$contact_person_email);
    $res_lead_enroll = $pdo->selectOne($enroll_lead_sql, $where_lead_params);

    $lead_params = array(
      'sponsor_id' => $agent_id,
      'customer_id' => $group_id,
      'fname' => $contact_person_fname,
      'lname' => $contact_person_lname,
      'email' => $contact_person_email,
      'cell_phone' => $contact_person_phone,
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
          'ac_message_1' => ' Invited lead as group'
      );
      $desc = json_encode($desc);
      activity_feed(3,$inserted_id,'Lead',$inserted_id,'Lead','Invited Lead As Agent',$contact_person_fname,$contact_person_lname,$desc);
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
          'ac_message_1' => ' Invited lead as group'
      );
      $desc = json_encode($desc);
      activity_feed(3,$inserted_id,'Lead',$inserted_id,'Lead','Invited Lead As Group',$contact_person_fname,$contact_person_lname,$desc);
    }

    $checkPrdSql = "SELECT id FROM agent_product_rule WHERE agent_id=:agent_id  AND is_deleted='N'";
    $checkPrdRow = $pdo->selectOne($checkPrdSql, array(":agent_id" => $group_id));
    if ($checkPrdRow) {
      //deleting all previous assigned rules
      $updateSql = array("is_deleted" => 'Y', "updated_at" => 'msqlfunc_NOW()');
      $updateWhere = array("clause" => "agent_id=:agent_id", "params" => array(":agent_id" => $group_id));
      $pdo->update("agent_product_rule", $updateSql, $updateWhere);
    }

    foreach ($products as $pKey => $pVal) {
    
      $insert_product_rule = array(
        'agent_id' => $group_id,
        'product_id' => $pVal,
        'status' => 'Pending Approval',
        'created_at' => 'msqlfunc_NOW()',
      );      
      $ap_rule_id = $pdo->insert("agent_product_rule", $insert_product_rule);
    }

    $trigger_id = 61;

    $long_url = $HOST . '/group_contract/' . md5($group_display_id);
    $url_params = array(
      "agent_id" => $group_id,
      "lead_id" => $inserted_id,
      "dest_url" => $long_url,
    );
    $link = get_short_url($url_params);

    $resEnrollAgent = array();
    if(!empty($agent_id)){
      $selEnrollAgent = "SELECT id,rep_id,CONCAT(fname,' ',lname) as name,email,cell_phone FROM customer WHERE id=:id AND type='Agent'";
      $paramsEnrollAgent = array(":id" => $agent_id);
      $resEnrollAgent = $pdo->selectOne($selEnrollAgent,$paramsEnrollAgent);
    }

    if ($send_contract_radio == "Y" && $contact_person_email != '' && in_array($select_type,array('email','email_text'))) {
      $params = array();
      $params['fname'] = $contact_person_fname;
      $params['lname'] = $contact_person_lname;
      $params['link'] = $link;
      $params['link'] = "<a href='" . $link . "' target='_BLANK'>$link</a>";
      $params['Email'] = $contact_person_email;
      $params['Phone'] = $contact_person_phone;
      $params['Agent'] = $contact_person_fname.' '.$contact_person_lname;
      $params['ParentAgent'] = !empty($resEnrollAgent['name']) ? $resEnrollAgent['name'] : '';
      
      $params['USER_IDENTITY'] = array('rep_id' => $group_id, 'cust_type' => 'Group', 'location' => $REQ_URL);

      $params['GroupName'] = $group_name;

      $params['EnrollingAgentDisplayName'] = !empty($resEnrollAgent['name']) ? $resEnrollAgent['name'] : '';
      $params['EnrollingAgentDisplayEmail'] = !empty($resEnrollAgent['email']) ? $resEnrollAgent['email'] : '';
      $params['EnrollingAgentDisplayPhone'] = !empty($resEnrollAgent['cell_phone']) ? $resEnrollAgent['cell_phone'] : '';
      
      if(!empty($email_from)){
        $params['EMAILER_SETTING']['from_mailid'] = $email_from;
        $params['EMAILER_SETTING']['from_mail_name'] = $_SESSION['agents']['fname']." ".$_SESSION['agents']['lname'];
      }
      
      $smart_tags = get_user_smart_tags($group_id,'group');
                
      if($smart_tags){
          $params = array_merge($params,$smart_tags);
      }

      trigger_mail($trigger_id, $params, $contact_person_email,true,3, $email_content, $email_subject);
    }
    if ($send_contract_radio == "Y" && $contact_person_phone != '' && in_array($select_type,array('text','email_text'))) {
      $country_code = '+1';
      $toPhone = $country_code . $contact_person_phone;
      $params = array();
      $params['fname'] = $contact_person_fname;
      $params['lname'] = $contact_person_lname;
      $params['link'] = $link;
      $params['email'] = $contact_person_email;
      $params['USER_IDENTITY'] = array('rep_id' => $group_id, 'cust_type' => 'Group', 'location' => $REQ_URL);

      $smart_tags = get_user_smart_tags($group_id,'group');
                
      if($smart_tags){
          $params = array_merge($params,$smart_tags);
      }

      trigger_sms($trigger_id, $toPhone, $params, true, $sms_content);
    }
    
    $response['invite_by']=$select_type;
    if($send_contract_radio=='N'){
      $response['invite_by']='personal_invite';
    }
    setNotifySuccess("Group contract created successfully");
    $response['link'] = $link;
    $response['group_name'] = trim($group_name);
    // $response['fname'] = trim($contact_person_fname);
    // $response['lname'] = trim($contact_person_lname);
    $response['status'] = 'success';
}
else{
	$response['status'] = "fail";
	$response['errors'] = $validate->getErrors();
}



header('Content-Type: application/json');
echo json_encode($response);
dbConnectionClose();
exit();
?>