<?php

include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once dirname(__DIR__) . '/libs/twilio-php-master/Twilio/autoload.php';
$validate = new Validation();
$REQ_URL = ($_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]); 

$agent_search = $_POST['agent_search'];
$agent_id = $_POST['agent_id'];
$company_name = $_POST['company_name'];
$contact_person_fname = $_POST['contact_person_fname'];
$contact_person_lname = $_POST['contact_person_lname'];
$contact_email = $_POST['contact_email'];
$phone1 = $_POST['phone1'];
$phone2 = $_POST['phone2'];
$phone3 = $_POST['phone3'];
$phone = $phone1 . $phone2 . $phone3;
$select_type = $_POST['select_type'] ? $_POST['select_type'] : array();
$sms_content = $_POST['sms_content'];
$email_content = $_POST['email_content'];
$products = isset($_POST['products']) ? $_POST['products'] : array();

$trigger_id = 201;
$trigger_content = $pdo->selectOne("SELECT * FROM triggers WHERE id=:id",array(":id"=>$trigger_id));
// $email_content = $trigger_content['email_content'];
// $sms_content = $trigger_content['sms_content'];

$upline_sponsor = getname('customer', $agent_id, "upline_sponsors", "rep_id");
$sponsor_id = getname('customer', $agent_id, 'id', 'rep_id');
$level = getname("customer", $agent_id, 'level', 'rep_id') + 1;
$upline_sponsors = $upline_sponsor . $sponsor_id . ',';

$validate->string(array('required' => true, 'field' => 'agent_search', 'value' => $agent_id), array('required' => 'Please select valid agent'));
$validate->string(array('required' => true, 'field' => 'company_name', 'value' => $company_name), array('required' => 'Employer Group Name is required'));
$validate->string(array('required' => true, 'field' => 'contact_person_fname', 'value' => $contact_person_fname), array('required' => 'First name is required'));
$validate->string(array('required' => true, 'field' => 'contact_person_lname', 'value' => $contact_person_lname), array('required' => 'Last Name is required'));
$validate->email(array('required' => true, 'field' => 'contact_email', 'value' => $contact_email), array('required' => 'Email is required', 'invalid' => 'Valid Email is required'));
$validate->digit(array('required' => true, 'field' => 'phone', 'value' => $phone, 'min' => 10, 'max' => 10), array('required' => 'Phone Number is required', 'invalid' => 'Valid Phone Number is required'));

if (!$validate->getError('agent_id')) {
    $selectAgentSql = "SELECT * FROM customer WHERE type='Agent' and rep_id=:id";
    $agentRow = $pdo->selectOne($selectAgentSql, array(":id" => $agent_id));
    if (!count($agentRow) > 0) {
        $validate->setError("agent_id", "Please select valid agent");
    }
}
if(!$validate->getError('phone')){
    $select_lead_phone = "SELECT id,is_email_unsubscribe, is_sms_unsubscribe, status, sponsor_id FROM leads WHERE cell_phone=:cell_phone AND is_deleted='N'";
    $where_select_phone = array(':cell_phone' => makeSafe($phone));
    $result_lead_phone_num = $pdo->selectOne($select_lead_phone, $where_select_phone);
    if (count($result_lead_phone_num) > 0) {
        if(($result_lead_phone_num['is_email_unsubscribe'] == 'Y') || ($result_lead_phone_num['is_sms_unsubscribe'] == 'Y') || ($result_lead_phone_num['status'] == 'Request Do Not Contact')){
            $validate->setError("phone", "This mobile number requested for Request Do Not Contact");
        }
    }
}
if (empty($products)) {
    $validate->setError("products", "Please select at least one product");
}
$checkSql = "SELECT id FROM customer WHERE type='Group' AND email=:email AND is_deleted='N' ";
$checkRow = $pdo->selectOne($checkSql, array(":email" => $contact_email));
if ($checkRow) {
    $validate->setError("contact_email", "This email is already registered");
}
if(!$validate->getError('contact_email')) {
  $IsRequestedForDoNotContact = checkLeadRequestedForDoNotContact('',$contact_email);
  if($IsRequestedForDoNotContact == true) {
      $validate->setError("contact_email", "This email requested for Request Do Not Contact");
  }
}
if (in_array("text", $select_type)) {
    $validate->string(array('required' => true, 'field' => 'sms_content', 'max' => 160, 'value' => $sms_content), array('required' => 'SMS Content is required'));
}
if (in_array("email", $select_type)) {
    $validate->string(array('required' => true, 'field' => 'email_content', 'value' => $email_content), array('required' => 'Email Content is required'));
}
if ($validate->isValid()) {
    $group_sql = $pdo->selectOne("SELECT * FROM customer WHERE email=:email AND status= 'Invited'", array(':email' => $contact_email));

    if($group_sql){
      $update_param = array(
	      "fname" => $contact_person_fname,
	      "lname" => $contact_person_lname,
	      "email" => $contact_email,
	      "type" => 'Group',
	      "cell_phone" => $phone,
	      "business_name" => $company_name,
	      "level" => $level,
	      "sponsor_id" => $sponsor_id,
	      'products' => implode(',', $products),
	      "upline_sponsors" => $upline_sponsors,
	      "status" => 'Invited',
	      "access_type" => 'full_access',
	      "ip_address" => $_SERVER["REMOTE_ADDR"],
	      "invite_at" => "msqlfunc_NOW()",
	      "updated_at" => "msqlfunc_NOW()",
        "agent_coded_profile"=>'1',
  		);
      $upd_where = array(
        'clause' => 'id = :id',
        'params' => array(
            ':id' => $group_sql['id'],
        ),
    	);
      $pdo->update('customer', $update_param, $upd_where);
      $group_id = $group_sql['id'];
      $group_display_id = $group_sql['rep_id'];

      $enroll_lead_sql = $pdo->selectOne("SELECT * FROM leads WHERE customer_id = :customer_id AND status = 'Invited' AND is_deleted='N' AND email = :email", array(':email' => makeSafe($contact_email) , 'customer_id' => $group_id));
      if($enroll_lead_sql){
        $update_lead = array(
	        'sponsor_id' => $sponsor_id,
	        'customer_id' => ($group_id),
	        'fname' => makeSafe($contact_person_fname),
	        'lname' => makeSafe($contact_person_lname),
	        'email' => makeSafe($contact_email),
	        'cell_phone' => makeSafe($phone),
	        'company_name' => makeSafe($company_name),
        );
        $lead_update = array(
          'clause' => 'id = :id',
          'params' => array(
              ':id' => $enroll_lead_sql['id'],
          ),
  			);
        $pdo->update('leads', $update_lead, $lead_update);
        $inserted_id = $enroll_lead_sql['id'];
        // CODE FOR ACTIVITY FEED
        $activity_feed_data = array(
        'admin_id' => $_SESSION['admin']['id']
        );
        $activity_feed_data = json_encode($activity_feed_data);
        activity_feed(3, $sponsor_id, 'Group', $enroll_lead_sql['id'], 'leads', 'Site Visit', $contact_person_fname, $contact_person_lname,'','',$activity_feed_data);
        activity_feed(3, $group_id, 'Group', $enroll_lead_sql['id'], 'leads', 'Site Visit', $contact_person_fname, $lname,'','',$activity_feed_data);
      }
    }
 else {
      $display_id = get_group_display_id();
      $insSql = array(
          "display_id" => get_display_id(),
          "rep_id" => $display_id,
          "type" => 'Group',
          "company_id" => 0,
          "fname" => $contact_person_fname,
          "lname" => $contact_person_lname,
          "email" => $contact_email,
          "cell_phone" => $phone,
          "level" => $level,
          "business_name" => $company_name,
          'products' => implode(',', $products),
          "sponsor_id" => $sponsor_id,
          "upline_sponsors" => $upline_sponsors,
          "status" => 'Invited',
          "access_type" => 'full_access',
          "ip_address" => $_SERVER["REMOTE_ADDR"],
          "invite_at" => "msqlfunc_NOW()",
          "updated_at" => "msqlfunc_NOW()",
          "created_at" => "msqlfunc_NOW()",
          "agent_coded_profile"=>'1',
      );
      $group_id = $pdo->insert("customer", $insSql);
      $group_display_id = $display_id;
      $insert_params = array(
	      'lead_id' => get_lead_id(),
	      'sponsor_id' => $sponsor_id,
	      'customer_id' => ($group_id),
	      'fname' => makeSafe($contact_person_fname),
	      'lname' => makeSafe($contact_person_lname),
	      'email' => makeSafe($contact_email),
	      'cell_phone' => makeSafe($phone),
	      'company_name' => makeSafe($company_name),
	      'status' => 'Invited',
	      'opt_in_type'=>'New Lead',
	      'country' => 'United States',
	      'created_at' => 'msqlfunc_NOW()',
      );
      $inserted_id = $pdo->insert("leads", $insert_params);

      $activity_feed_data = array(
          'admin_id' => $_SESSION['admin']['id']
      );
      $activity_feed_data = json_encode($activity_feed_data);

      // CODE FOR ACTIVITY FEED
      activity_feed(3, $sponsor_id, 'Agent', $inserted_id, 'leads', 'Enrolled A New Lead', $contact_person_fname, $contact_person_lname,'','',$activity_feed_data);
      activity_feed(3, $group_id, 'Group', $inserted_id, 'leads', 'Lead Created At', $contact_person_fname, $contact_person_lname,'','',$activity_feed_data);
    }
    //checking for existing product assignments and rules
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
        'status' => 'Pending',
        'created_at' => 'msqlfunc_NOW()',
      );
      //assign default commission rule
      $ap_rule_id = $pdo->insert("agent_product_rule", $insert_product_rule);
    }
    $link = $HOST . '/group_contract/' . md5($group_display_id);
    $url_params = array(
      "agent_id" => $group_id,
      "lead_id" => $inserted_id,
      "dest_url" => $link,
    );

   
    $shortUrl = get_short_url($url_params);
   
    if ($contact_email != '' && in_array('email', $select_type)) {
      $params = array();
      $params['fname'] = $contact_person_fname;
      $params['lname'] = $contact_person_lname;
      $params['group_name'] = $company_name;
      $params['GroupEnrollmentLink'] = $params['link'] = "<a href='" . $shortUrl . "' target='_BLANK'>$shortUrl</a>";
      $params['email'] = $company_name;
      $params['USER_IDENTITY'] = array('rep_id' => $group_display_id, 'cust_type' => 'Group', 'location' => $REQ_URL);

      $smart_tags = get_user_smart_tags($group_id,'group');
      if($smart_tags){
        $params = array_merge($params,$smart_tags);
      }
      
      if($_SERVER['SERVER_NAME'] == "192.168.1.30") {
        $contact_email = 'dharmesh@cyberxllc.com';
    }
      trigger_mail($trigger_id, $params, $contact_email, '', 3, $email_content);
    }
    if ($phone != '' && in_array('text', $select_type)) {
	    $country_code = '+1';
	    $toPhone = $country_code . $phone;
	    $params = array();
	    $params['fname'] = $contact_person_fname;
      $params['lname'] = $contact_person_lname;
      $params['GroupEnrollmentLink'] = $params['link'] =$response['link'] = $shortUrl;
      $params['email'] = $contact_email;
      $params['USER_IDENTITY'] = array('rep_id' => $group_display_id, 'cust_type' => 'Group', 'location' => $REQ_URL);
      if($_SERVER['SERVER_NAME'] == "192.168.1.30") {
          $toPhone = '+919712028991';
      }
      trigger_sms($trigger_id, $toPhone, $params, '', $sms_content);
    }
    if (in_array('website', $select_type)) {
        $_SESSION['tmp_group']['id'] = $group_id;
        $_SESSION['tmp_group']['rep_id'] = $group_display_id;
    } 
    $website_link = "";
    if (in_array('website', $select_type)) {
      $website_link = $shortUrl;
    }
    $response['link'] = $website_link;
    $response['company_name'] = $company_name;
    $response['status'] = "success";
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