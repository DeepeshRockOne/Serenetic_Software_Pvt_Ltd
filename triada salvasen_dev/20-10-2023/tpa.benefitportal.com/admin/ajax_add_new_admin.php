<?php
include_once 'layout/start.inc.php';

$validate = new Validation();
$response = array();
$features = array();

$is_feature_access = checkIsset($_POST['is_feature_access']);
$access_level = checkIsset($_POST['access_level']);
$company_id = 3;
$fname = checkIsset($_POST['fname']);
$lname = checkIsset($_POST['lname']);
$email = checkIsset($_POST['email']);
$cemail = checkIsset($_POST['cemail']);
$type = checkIsset($_POST['type']);
$invitation = checkIsset($_POST['invitation']);
$email_from = checkIsset($_POST['email_from']);
$email_sub = checkIsset($_POST['email_sub']);
$features = array_unique(checkIsset($_POST['feature'],"arr"));
$phone = phoneReplaceMain(checkIsset($_POST['phone']));
if(!empty($features)){
    foreach ($features as $a => $b) {
        if ($b == 'undefined') {
            unset($features[$a]);
        }
    }
}

$validate->string(array('required' => true, 'field' => 'access_level', 'value' => $access_level), array('required' => 'Access Level is required'));
// if ($access_level != 'Super Admin') {
//     $validate->string(array('required' => true, 'field' => 'company_name', 'value' => $company_id), array('required' => 'Company is required'));
// }
$validate->string(array('required' => true, 'field' => 'fname', 'value' => $fname), array('required' => 'First Name is required'));
$validate->string(array('required' => true, 'field' => 'lname', 'value' => $lname), array('required' => 'Last Name is required'));
$validate->email(array('required' => true, 'field' => 'email', 'value' => $email), array('required' => 'Email is required', 'invalid' => 'Valid email is required'));
$validate->email(array('required' => true, 'field' => 'cemail', 'value' => $cemail), array('required' => 'Email is required', 'invalid' => 'Valid email is required'));

if (!$validate->getError('email') && !$validate->getError('cemail')) {
    if (strtolower($email) != strtolower($cemail)) {
        $validate->setError('cemail', "Both emails must be the same");
    } else {
        $selectEmail = "SELECT email FROM admin WHERE email=:email And is_active = 'Y'";
        $where_select_email = array(':email' => makeSafe($email));
        $resultEmail = $pdo->selectOne($selectEmail, $where_select_email);

        if (!empty($resultEmail)) {
            $validate->setError("email", "This email is associated with another Admin Account in the system.");
        }
    }
}

if($is_feature_access=='Y'){
    if(empty($features)){
        $validate->setError("features","Please Select Any One Option");
    }
    $validate->string(array('required' => true, 'field' => 'invitation', 'value' => $invitation), array('required' => 'Please select any invitation.'));

    if ($invitation == 'smarte_invite') {
        $validate->string(array('required' => true, 'field' => 'type', 'value' => $type), array('required' => 'Select how you would like to send the Application link.'));
        if($type=="Email" || $type=="Both"){
            $validate->string(array('required' => true, 'field' => 'email_from', 'value' => $email_from), array('required' => 'Email from is required'));
            if(!preg_match('/^([a-z0-9]+)([\._-][a-z0-9]+)*@([a-z0-9]+)(\.)+[a-z]{2,6}$/ix', trim($email_from))) {
                $validate->setError("email_from", "Valid Email is required");
            }
            $validate->string(array('required' => true, 'field' => 'email_sub', 'value' => $email_sub), array('required' => 'Email subject is required'));
        }
    }
}

if ($validate->isValid()) {

    $key = md5($fname . $lname . sha1(time()));
    $link = $HOST . '/admin/sign_up.php?key=' . $key;
    // Add New Admin Trigger
    $trigger_id = 1; 

    $params['fname'] = $fname;
    $params['lname'] = $lname;
    $params['link'] = $link;
    $params['email'] = $email;
    $display_id = get_admin_id();

    $insert_params = array(
        'display_id' => $display_id,
        'fname' => makeSafe($fname),
        'lname' => makeSafe($lname),
        'type' => makeSafe($access_level),
        'email' => makeSafe($email),
        'invite_key' => $key,
        'is_active' => makeSafe("Y"),
        'status' => 'Pending',
        'invite_at' => 'msqlfunc_NOW()',
        'created_at' => 'msqlfunc_NOW()',
    );
    if($is_feature_access=='Y'){
        $insert_params['feature_access']=implode(',',$features);
    }
    if ($access_level != 'Super Admin') {
        $insert_params['company_id'] = $company_id;
    }
    if(!empty($phone)){
        $insert_params['phone']=$phone;
    }
     
    $inserted_id = $pdo->insert("admin", $insert_params);

    $extra['display_id'] = $_SESSION['admin']['display_id'];
    $extra['fname'] = $_SESSION['admin']['fname'];
    $extra['lname'] = $_SESSION['admin']['lname'];
    $description['description'] = 'Created By: '.$extra['fname'].' '.$extra['lname']."(".$extra['display_id'].")";

    $activity_feed_id = activity_feed(3, $inserted_id, 'Admin', $inserted_id, 'admin', 'Admin Account Created', $fname, $lname,json_encode($description),'',json_encode($extra));
    setNotifySuccess('Invite created successfully');
    $user_data = get_user_data($_SESSION['admin']);
    audit_log($user_data, $inserted_id, 'Admin', 'Account Created');
    

    if($invitation == 'smarte_invite'){

        if($type=='SMS' || $type=='Both'){
            if (!empty($phone)  && (checkIsset($_POST['txt_msg_txt']) && $_POST['txt_msg_txt']!=='')) {
                $mobile_no = makeSafe($_POST['phone']); 
                $country_code = '+1';
                $toPhone = $country_code . $mobile_no;
                 
                $sms_content = makeSafe($_POST['txt_msg_txt']);
                trigger_sms($trigger_id, $toPhone, $params, true, strip_tags(htmlspecialchars_decode($sms_content,ENT_QUOTES)));
            }    
        }

        if($type=='Email' || $type=='Both'){
            try {
                $email_content = makeSafe($_POST['txt_email_txt1']);
                if(!empty($email_from)){
                    $params['EMAILER_SETTING']['from_mailid'] = $email_from;
                    $params['EMAILER_SETTING']['from_mail_name'] = $_SESSION['admin']['fname']." ".$_SESSION['admin']['lname'];
                }
                $params['USER_IDENTITY'] = array('rep_id' => $inserted_id, 'cust_type' => 'Admin', 'location' => checkIsset($REQ_URL));
                
                $smart_tags = get_user_smart_tags($inserted_id,'admin');
                
                if($smart_tags){
                    $params = array_merge($params,$smart_tags);
                }
                
                trigger_mail($trigger_id, $params, $email, true, 3, htmlspecialchars_decode($email_content),$email_sub);
            } catch (Exception $e) {
                $validate->setError('email', 'Email is not sent');
            }
        }

    }
        
    $response['link'] = $link;
    $response['fname'] = $fname;
    $response['lname'] = $lname;
    $response['invitation'] = '';
    if($invitation=='personal_invite'){
        $response['invitation'] = 'personal_invite';
    }
    $response['status'] = 'success';

}else {
    $errors = $validate->getErrors();
    $response['status'] = 'error';
    $response['errors'] = $errors;
}
  
header('Content-Type: application/json');
echo json_encode($response);
dbConnectionClose();
exit;
?>