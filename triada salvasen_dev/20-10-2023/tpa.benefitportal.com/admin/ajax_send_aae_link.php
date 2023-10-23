<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once dirname(__FILE__) . './../includes/function.class.php';
$function_list = new functionsList();
$validate = new Validation();
$REQ_URL = ($_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);

$response = array();

$customer_id = isset($_POST['customer_id'])?$_POST['customer_id']:"";
$token = isset($_POST['token'])?$_POST['token']:"";
$sent_via = isset($_POST['sent_via'])?$_POST['sent_via']:"";
$email_content = isset($_POST['email_content'])?$_POST['email_content']:"";
$sms_content = isset($_POST['sms_content'])?$_POST['sms_content']:"";
$email_subject = isset($_POST['email_subject'])?$_POST['email_subject']:"";

$lq_sql = "SELECT lq.id,l.id as lead_id,l.lead_id as rep_lead_id  FROM lead_quote_details lq 
            JOIN leads l ON(l.id = lq.lead_id)
            WHERE token=:token";
$lq_where = array(":token"=>$token);
$lq_row = $pdo->selectOne($lq_sql,$lq_where);

$cust_row = $pdo->selectOne("SELECT * FROM customer c WHERE md5(c.id)=:id", array(":id" => $customer_id));
if (empty($cust_row)) {
    setNotifyError('AAE Not Found.');
    $response['status'] = 'fail';
    echo json_encode($response);
    exit;
}


$validate->string(array('required' => true, 'field' => 'sent_via', 'value' => $sent_via), array('required' => 'Resend Method is required'));

if ($sent_via == 'text' || $sent_via == 'Both') {
    $validate->string(array('required' => true, 'field' => 'sms_content', 'value' => $sms_content), array('required' => 'SMS Content is required'));
    if (!$validate->getError("sms_content")) {
        if (strlen($sms_content) > 160) {
            $validate->setError('sms_content', 'SMS Content must be less then 160 character');
        }
        $is_link = strpos($sms_content, "[[link]]");
        if ($is_link <= 0) {
            $validate->setError('sms_content', 'SMS content must have [[link]] tag');
        }
    }
}
if ($sent_via == 'email' || $sent_via == 'Both') {
    $validate->string(array('required' => true, 'field' => 'email_content', 'value' => $email_content), array('required' => 'Email Content is required'));
    $validate->string(array('required' => true, 'field' => 'email_subject', 'value' => $email_subject), array('required' => 'Email Subject is required'));

    if (!$validate->getError("email_content")) {
        $is_link = strpos($email_content, "[[link]]");
        if ($is_link <= 0) {
            $validate->setError('email_content', 'Email content must have [[link]] tag');
        }
    }
}

if ($validate->isValid()) {
    $url_link = $HOST . '/quote/enroll_varification/'. $token;
    $url_params = array(
        'dest_url' => $url_link,
        'agent_id' => $cust_row['sponsor_id'],
        'customer_id' => $cust_row['id'],
    );
    $link = get_short_url($url_params);
    $description = array();
    if ($sent_via == 'text' || $sent_via == 'Both') {
        $sms_data = array();
        $sms_data['fname'] = $cust_row['fname'];
        $sms_data['lname'] = $cust_row['lname'];
        $sms_data['link'] = $link;
        $tophone = "+1". $cust_row['cell_phone'];

        if ($SITE_ENV=='Local') {
            $tophone = '+919429548647';
        }
        trigger_sms(84, $tophone, $sms_data, true, $sms_content);

        $description['phone'] = "Phone : ".format_telephone($cust_row['cell_phone']);
    }

    if ($sent_via == 'email' || $sent_via == 'Both') {
        $agent_detail = $function_list->get_sponsor_detail_for_mail($cust_row['id'],$cust_row['sponsor_id']);

        $mail_data = array();
        $mail_data['link'] = $link;
        $mail_data['fname'] = $cust_row['fname'];
        $mail_data['lname'] = $cust_row['lname'];
        $mail_data['Email'] = $cust_row['email'];
        $mail_data['Phone'] = $cust_row['cell_phone'];
        $mail_data['MemberID'] = $cust_row['rep_id'];
        $mail_data['Agent'] = $agent_detail['agent_name'];
        if(!empty($cust_row['sponsor_id'])){
            $parent_agent_detail = $function_list->get_sponsor_detail_for_mail($cust_row['id'], $cust_row['sponsor_id']);
            $mail_data['ParentAgent'] = $parent_agent_detail['agent_name'];
        }

        if (!empty($agent_detail)) {
            $mail_data['agent_name'] = $agent_detail['agent_name'];
            $mail_data['agent_email'] = $agent_detail['agent_email'];
            $mail_data['agent_phone'] = format_telephone($agent_detail['agent_phone']);
            $mail_data['agent_id'] = $agent_detail['agent_id'];
            $mail_data['is_public_info'] = $agent_detail['is_public_info'];
        } else {
            $mail_data['is_public_info'] = 'display:none';
        }

        $mail_data['USER_IDENTITY'] = array('rep_id'=>$cust_row['rep_id'],'cust_type' => 'Member','location' => $REQ_URL);

        $to_email = $cust_row['email'];
        if ($SITE_ENV == 'Local') {
            $to_email = 'karan@cyberxllc.com';
        }
        $email_content = preg_replace('/[[:^print:]]/', '', $email_content);

        $smart_tags = get_user_smart_tags($cust_row['id'],'member');
                
        if($smart_tags){
            $mail_data = array_merge($mail_data,$smart_tags);
        }

        trigger_mail(84, $mail_data, $to_email, array(), 3, $email_content, $email_subject);

        $description['email'] = "Email : ".$cust_row['email'];
    }

    if(in_array($cust_row['status'],array('Customer Abandon','Pending Quote','Pending Validation'))) {
        $cust_params = array(
            'invite_at' => 'msqlfunc_NOW()',
        );
        $upd_where = array(
            'clause' => 'id = :id',
            'params' => array(
                ':id' => $cust_row['id'],
            ),
        );
        $pdo->update('customer', $cust_params, $upd_where);
    }

    $description['ac_message'] = array(
        'ac_red_1'=>array(
            'href'=> $ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
            'title'=> $_SESSION['admin']['display_id'],
        ),
        'ac_message_1' => 'Resent AAE verification link to  ' . $cust_row['fname'] . ' ' . $cust_row['lname'] . ' (',
        'ac_red_2' => array(
            'href' => 'lead_details.php?id=' . md5($lq_row['lead_id']),
            'title' => $lq_row['rep_lead_id'],
        ),
        'ac_message_2' => ')',
    );
    $desc = json_encode($description);
    activity_feed(3,$_SESSION['admin']['id'],'Admin',$lq_row['lead_id'], 'Lead', 'Admin Resent AAE Verification Link', $_SESSION['admin']['fname'], $_SESSION['admin']['lname'], $desc);

    setNotifySuccess('You have successfully resent AAE');
    $response['status'] = 'success';
    echo json_encode($response);
    dbConnectionClose();
    exit;
} else {
    if(count($validate->getErrors()) > 0){
        $response['status'] = "errors";   
        $response['errors'] = $validate->getErrors();   
    }
    echo json_encode($response);
    exit();
}
?>
