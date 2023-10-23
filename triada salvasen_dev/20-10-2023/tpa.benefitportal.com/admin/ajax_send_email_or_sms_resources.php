<?php
include_once __DIR__ . '/includes/connect.php';

$validate = new Validation();
$REQ_URL = ($_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);

$response = array();

$sent_via = isset($_POST['sent_via'])?$_POST['sent_via']:"";
$email_content = isset($_POST['email_content'])?$_POST['email_content']:"";
$sms_content = isset($_POST['sms_content'])?$_POST['sms_content']:"";
$email_subject = isset($_POST['email_subject'])?$_POST['email_subject']:"";
$to_phone = isset($_POST['to_phone'])? phoneReplaceMain($_POST['to_phone']) :"";
$to_email = checkIsset($_POST['to_email']);
$cc_email = checkIsset($_POST['cc_email']);
$resource_id = isset($_POST['resource_id'])? $_POST['resource_id'] :"";

$trigger_id = 105;

    $validate->string(array('required' => true, 'field' => 'sent_via', 'value' => $sent_via), array('required' => 'Please Select share by is required'));

    if ($sent_via == 'SMS' || $sent_via == 'Both') {
        $validate->string(array('required' => true, 'field' => 'sms_content', 'value' => $sms_content), array('required' => 'SMS Content is required'));
        if (!$validate->getError("sms_content")) {
            if (strlen($sms_content) > 160) {
                $validate->setError('sms_content', 'SMS Content must be less then 160 character');
            }
        }
        $validate->digit(array('required' => true, 'field' => 'to_phone', 'value' => $to_phone,'min'=>10,'max'=>10), array('required' => 'Phone Number is required', 'invalid' => "Enter valid Phone Number"));
    }
    if ($sent_via == 'Email' || $sent_via == 'Both') {
        $validate->string(array('required' => true, 'field' => 'email_content', 'value' => $email_content), array('required' => 'Email Content is required'));
        $validate->string(array('required' => true, 'field' => 'email_subject', 'value' => $email_subject), array('required' => 'Email Subject is required'));
        $validate->email(array('required' => true, 'field' => 'to_email', 'value' => $to_email), array('required' => 'Email is required', 'invalid' => 'Please enter valid email'));
    }

if ($validate->isValid()) {
    $description = array();
    $email_status = $sms_status = $email_content_org = $sms_content_org = '';
    $resResource = $pdo->selectOne("SELECT * FROM portal_resources where md5(id)=:id and is_deleted='N'",array(":id"=>$resource_id));
    $link = $resResource['file'];//getname('portal_resources',$resource_id,'file','md5(id)');
    $link = $RESOURCE_DOCUMENT_WEB.$link;
    $resource_name = $resResource['resource_name'];

    if ($sent_via == 'SMS' || $sent_via == 'Both') {
        $sms_data = array();
        $sms_data['link'] = $link;
        $sms_data['ResourceName'] = $resource_name;
        $tophone = "+1".$to_phone ;

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
        // send_sms_to_phone($tophone,$sms_content,$sms_data);
    }

    if ($sent_via == 'Email' || $sent_via == 'Both') {

        $mail_data = array();
        $mail_data['link'] = $link;
        $mail_data['ResourceName'] = $resource_name;

        if(empty($email_content)){
            $email_content = getname('triggers',$trigger_id,'email_content');
        }

        $email_content_org = $email_content;
        foreach ($mail_data as $placeholder => $value) {
            $email_content_org = str_replace("[[" . $placeholder . "]]", $value, $email_content_org);
        }

        $toEmail[] = $to_email;
        if($cc_email!=''){
            $toEmail[] = $cc_email;
        }
        // if ($SITE_ENV == 'Local') {
        //     $to_email = 'karan@cyberxllc.com';
        // }
        
        $email_content = preg_replace('/[[:^print:]]/', '', $email_content);
        $email_status = trigger_mail($trigger_id, $mail_data, $toEmail, true, 3, $email_content, $email_subject);
        // trigger_mail_to_mail($mail_data, $toemail,3, $email_subject,htmlspecialchars_decode($email_content));
    }

    $cm_message = '';
    if(!empty($to_email)){
        $cm_message = 'Shared '.$resResource['module_name'] .' - '. $resResource['resource_name'].' to '.$to_email;
    }
    if(!empty($to_phone)){
        $cm_message .= '<br>Shared ' .$resResource['module_name'] .' - '. $resResource['resource_name'].' to '.$to_phone;
    }
    $msg = '';
    if($sent_via == 'Email'){
        if($email_status == 'fail'){
            $msg = 'Something went wrong Email not Sent.';
            $response['status'] = 'fail';
        }else{
            $msg = 'You have successfully Sent Email';
            $response['status'] = 'success';
        }
    }else if( $sent_via == 'SMS'){
        if($sms_status == 'fail'){
            $msg = 'Something went wrong SMS not Sent.';
            $response['status'] = 'fail';
        }else{

            $msg = 'You have successfully Sent SMS';
            $response['status'] = 'success';
        }
    }else{

        if($sms_status == 'fail' && $email_status == 'fail'){
            $msg = 'Something went wrong SMS OR Email not sent';
            $response['status'] = 'fail';
        }else{
            $msg = 'You have successfully Sent Email or SMS';
            $response['status'] = 'success';
        }
    }

        $description['ac_message'] =array(
            'ac_red_1'=>array(
              'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
              'title'=>$_SESSION['admin']['display_id'],
            ),
            'ac_message_1' =>$cm_message
        );     
          if($sent_via == 'SMS' || $sent_via == 'Both' ){
            $description['ac_descriptions_sms'] = ' To : '.$tophone.'<br>';
            $description["ac_description_link_1"] = array(
                'SMS'=>array('href'=>'#javascript:void(0)','class'=>'descriptionPopup','title'=>'SMS','data-desc'=>checkIsset($sms_content_org),'data-encode'=>'no')
            );
            if($sms_status == 'fail'){
                $description['ac_descriptions_sms_des'] = 'Something went wrong SMS not Sent.';
            }
          }
          if($sent_via == 'Email' || $sent_via == 'Both' ){
            $description['ac_descriptions_email'] = ' To : '.$to_email.'<br>';
            if(!empty($cc_email)){
                $description['ac_descriptions_cc_email'] = ' Cc : '.$cc_email.'<br>';
            }
            $description["ac_description_link_2"] = array(
                'Email'=>array('href'=>'#javascript:void(0)','class'=>'descriptionPopup','title'=>'Email','data-desc'=>htmlspecialchars(checkIsset($email_content_org)),'data-encode'=>'no')
            );       
            if($email_status == 'fail'){
                $description['ac_descriptions_email_desc'] = 'Something went wrong Email not Sent.';
            }
          }
        $desc=json_encode($description);
        activity_feed(3,$_SESSION['admin']['id'], 'Admin' , $_SESSION['admin']['id'], 'admin','Admin Shared Resources',$_SESSION['admin']['name'],"",$desc);

    $response['msg'] = $msg;
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
