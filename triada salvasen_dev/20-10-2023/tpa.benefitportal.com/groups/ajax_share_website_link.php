<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$validate = new Validation();
$REQ_URL = ($_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);

$response = array();

$sent_via = isset($_POST['sent_via'])?$_POST['sent_via']:"";
$website_url = isset($_POST['website_url'])?$_POST['website_url']:"";


$sms_content = isset($_POST['sms_content'])?$_POST['sms_content']:"";
$email_subject = isset($_POST['email_subject'])?$_POST['email_subject']:"";
$email_content = isset($_POST['email_content'])?$_POST['email_content']:"";

$to_phone = isset($_POST['to_phone'])? phoneReplaceMain($_POST['to_phone']) :"";
$to_email = checkIsset($_POST['to_email']);

$email_from = isset($_POST['email_from'])? $_POST['email_from'] :"";

$trigger_id = isset($_POST['trigger_id'])? $_POST['trigger_id'] :"";
$autocomplete_enrolee = isset($_POST['autocomplete_enrolee'])? $_POST['autocomplete_enrolee'] :"";



if ($sent_via == 'SMS' || $sent_via == 'Both') {
    if(empty($to_phone)){
        $to_phone = $autocomplete_enrolee;
    }
    $validate->string(array('required' => true, 'field' => 'sms_content', 'value' => $sms_content), array('required' => 'SMS Content is required'));
    if (!$validate->getError("sms_content")) {
        if (strlen($sms_content) > 160) {
            $validate->setError('sms_content', 'SMS Content must be less then 160 character');
        }

        $is_link = stripos($sms_content, "[[link]]");
        if ($is_link <= 0) {
            $validate->setError('sms_content', 'SMS content must have [[link]] tag');
        }
    }
    
    $validate->digit(array('required' => true, 'field' => 'to_phone', 'value' => $to_phone,'min'=>10,'max'=>10), array('required' => 'Phone Number is required', 'invalid' => "Enter valid Phone Number"));
    
}
if ($sent_via == 'Email' || $sent_via == 'Both') {
    if(empty($to_email)){
        $to_email = $autocomplete_enrolee;
    }
    $validate->string(array('required' => true, 'field' => 'email_from', 'value' => $email_from), array('required' => 'From Email is required'));
    $validate->string(array('required' => true, 'field' => 'email_content', 'value' => $email_content), array('required' => 'Email Content is required'));
    $validate->string(array('required' => true, 'field' => 'email_subject', 'value' => $email_subject), array('required' => 'Email Subject is required'));
    $validate->email(array('required' => true, 'field' => 'to_email', 'value' => $to_email), array('required' => 'Email is required', 'invalid' => 'Please enter valid email'));

    if (!$validate->getError("email_content")) {
        $is_link = stripos($email_content, "[[link]]");
        if ($is_link <= 0) {
            $validate->setError('email_content', 'Email content must have [[link]] tag');
        }
    }
}


if ($validate->isValid()) {
    $description = array();
    $email_status = $sms_status = $email_content_org = $sms_content_org = '';
    if ($sent_via == 'SMS' || $sent_via == 'Both') {
        $sms_data = array();
        $sms_data['link'] = $website_url;
        $tophone = "+1". !empty($to_phone) ? $to_phone : '';

        if ($SITE_ENV=='Local') {
            $tophone = '+919429548647';
        }
        if(empty($sms_content)){
            $sms_content = getname('triggers',$trigger_id,'sms_content');
        }
        $sms_content_org = $sms_content;
        foreach ($sms_data as $placeholder => $value) {
            $sms_content_org = str_replace("[[" . $placeholder . "]]", $value, $sms_content_org);
        }

        $sms_status = trigger_sms($trigger_id, $tophone, $sms_data, true, $sms_content);
    }

    if ($sent_via == 'Email' || $sent_via == 'Both') {

        $mail_data = array();
        $mail_data['link'] = $website_url;

        if(empty($email_content)){
            $email_content = getname('triggers',$trigger_id,'email_content');
        }

        $email_content_org = $email_content;
        foreach ($mail_data as $placeholder => $value) {
            $email_content_org = str_replace("[[" . $placeholder . "]]", $value, $email_content_org);
        }

        $to_email = $toemail = !empty($to_email) ? $to_email : '';
        if ($SITE_ENV == 'Local') {
            $toemail = 'karan@cyberxllc.com';
        }

        if($email_from){
            $mail_data['EMAILER_SETTING']['from_mailid'] = $email_from;
        }

        $email_content = preg_replace('/[[:^print:]]/', '', $email_content);
        $email_status = trigger_mail($trigger_id, $mail_data, $to_email, true, 3, $email_content, $email_subject);
        
    }

    $cm_message = $msg = '';
    if($sent_via == 'Email'){
        if($email_status == 'fail'){
            $msg = 'Something went wrong Email not Sent.';
            $response['status'] = 'fail';
        }else{
            $msg = 'You have successfully Sent Email';
            $response['status'] = 'success';
        }
        $cm_message = ' Sent Email To Member ';
    }else if( $sent_via == 'SMS'){
        if($sms_status == 'fail'){
            $msg = 'Something went wrong SMS not Sent.';
            $response['status'] = 'fail';
        }else{

            $msg = 'You have successfully Sent SMS';
            $response['status'] = 'success';
        }
        $cm_message = ' Sent SMS To Member ';
    }else{

        if($sms_status == 'fail' && $email_status == 'fail'){
            $msg = 'Something went wrong SMS OR Email not sent';
            $response['status'] = 'fail';
        }else{
            $msg = 'You have successfully Sent Email or SMS';
            $response['status'] = 'success';
        }
        $cm_message = ' Sent SMS or Email To Member ';
    }

    $response['msg'] = $msg;
} else {
    $response['status'] = "Error";
    $response['errors'] = $validate->getErrors();
}

echo json_encode($response);
dbConnectionClose();
exit();
?>
