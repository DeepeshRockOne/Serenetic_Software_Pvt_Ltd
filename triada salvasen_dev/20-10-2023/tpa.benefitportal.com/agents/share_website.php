<?php
include_once __DIR__ . '/includes/connect.php'; 
$agent_id = $_SESSION["agents"]["id"];
$sent_via = $_GET['sent_via']; 
$id = $_GET['id']; 
$trigger_row = $pdo->selectOne("SELECT * FROM triggers WHERE display_id='T634'");

if($id != "aae_enrollment_website") {
	$pb_sql = "SELECT * FROM page_builder WHERE agent_id=:agent_id AND md5(id)=:id AND is_deleted='N'";
	$pb_where = array(":agent_id" => $agent_id, ":id" => $id);
	$pb_row = $pdo->selectOne($pb_sql, $pb_where);	
}

if($id == "aae_enrollment_website") {
	$url_link = $AAE_WEBSITE_HOST . '/' . $_SESSION['agents']['user_name'];
	$page_name = 'AAE Enrollment Website';
} else {
	if(empty($pb_row)) {
	    setNotifyError("Sorry! Website not found.");
	    echo '<script type="text/javascript">window.parent.location.href=window.parent.location.href;</script>';
	    exit;
	}
	$url_link = $ENROLLMENT_WEBSITE_HOST . '/' . $pb_row['user_name'];	
	$page_name = $pb_row['page_name'];
}

if(isset($_POST['operation'])) {
	$validate = new Validation();
	$REQ_URL = ($_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
	$response = array();
	
	$from_email = isset($_POST['from_email'])?$_POST['from_email']:"";
	$to_email = isset($_POST['to_email'])?$_POST['to_email']:"";
	$email_content = isset($_POST['email_content'])?$_POST['email_content']:"";
	$email_subject = isset($_POST['email_subject'])?$_POST['email_subject']:"";
	$to_phone = isset($_POST['to_phone'])?phoneReplaceMain($_POST['to_phone']):"";
	$sms_content = isset($_POST['sms_content'])?$_POST['sms_content']:"";

	if($sent_via == "email") {
		$validate->string(array('required' => true, 'field' => 'from_email', 'value' => $from_email), array('required' => 'From Email is required'));

		if(!$validate->getError('from_email')){
		    if (!filter_var($from_email, FILTER_VALIDATE_EMAIL)) {
		        $validate->setError("from_email", "Valid Email is required");
		    }
		}
		$validate->string(array('required' => true, 'field' => 'to_email', 'value' => $to_email), array('required' => 'To Email is required'));

		if(!$validate->getError('to_email')){
		    if (!filter_var($to_email, FILTER_VALIDATE_EMAIL)) {
		        $validate->setError("to_email", "Valid Email is required");
		    }
		}

		$validate->string(array('required' => true, 'field' => 'email_content', 'value' => $email_content), array('required' => 'Email Content is required'));

	    $validate->string(array('required' => true, 'field' => 'email_subject', 'value' => $email_subject), array('required' => 'Email Subject is required'));

	    /*if (!$validate->getError("email_content")) {
	        $is_link = strpos($email_content, "[[link]]");
	        if ($is_link <= 0) {
	            $validate->setError('email_content', 'Email content must have [[link]] tag');
	        }
	    }*/
	}

	if($sent_via == "text") {
		$validate->digit(array('required' => true, 'field' => 'to_phone', 'value' => $to_phone, 'min' => 10, 'max' => 10), array('required' => 'Phone Number is required', 'invalid' => 'Valid Phone Number is required'));

		$validate->string(array('required' => true, 'field' => 'sms_content', 'value' => $sms_content), array('required' => 'SMS Content is required'));
	    if (!$validate->getError("sms_content")) {
	        if (strlen($sms_content) > 160) {
	            $validate->setError('sms_content', 'SMS Content must be less then 160 character');
	        }
	        /*$is_link = strpos($sms_content, $url_link);
	        if ($is_link <= 0) {
	            $validate->setError('sms_content', 'SMS content must have [[link]] tag');
	        }*/
	    }
	}

	if ($validate->isValid()) {
    	$desc = array();
    	$desc['ac_message'] = array(
            'ac_red_1' => array(
                'href' => 'agent_detail_v1.php?id=' . md5($_SESSION['agents']['id']),
                'title' => $_SESSION['agents']['rep_id'],
            ),
            'ac_message_1' => ' shared Website',
            'ac_red_2' => array(
                'href' => $url_link,
                'title' => $url_link,
            ),
            'ac_message_2' => ' <br/>'
        );

		if($sent_via == "email") {
			$mail_data = array();
	        $mail_data['link'] = $url_link;
	        $mail_data['EMAILER_SETTING']['from_mailid'] = $from_email;
	        $email_content = preg_replace('/[[:^print:]]/', '', $email_content);

	        $smart_tags = get_user_smart_tags($agent_id,'agent');
                
	        if($smart_tags){
	            $mail_data = array_merge($mail_data,$smart_tags);
	        }

	        if (!empty($trigger_row)) {
	        	trigger_mail($trigger_row['id'],$mail_data, $to_email, array(), 3, $email_content, $email_subject);
	        }
	        $desc['email'] = "Email : ".$to_email;
		}

		if($sent_via == "text") {
			$sms_data = array();
	        $sms_data['link'] = $url_link;
	        $tmp_to_phone = "+1". $to_phone;

	        $smart_tags = get_user_smart_tags($agent_id,'agent');
                
	        if($smart_tags){
	            $sms_data = array_merge($sms_data,$smart_tags);
	        }

	        if (!empty($trigger_row)) {
	        	if ($SITE_ENV=='Local') {
		            $tmp_to_phone = '+919429548647';
		        }
	    		trigger_sms($trigger_row['id'],$tmp_to_phone,$sms_data, true, $sms_content);
	        }
	        $desc['phone'] = "Phone : ".format_telephone($to_phone);
		}

		$desc = json_encode($desc);

		if($id == "aae_enrollment_website") {
			activity_feed(3, $_SESSION['agents']['id'], 'Agent', $_SESSION['agents']['id'], 'Agent', 'Agent Shared AAE Application Website', $_SESSION['agents']['fname'], $_SESSION['agents']['lname'], $desc);
			setNotifySuccess('You have successfully shared AAE Application Website');
		} else {
			activity_feed(3, $_SESSION['agents']['id'], 'Agent', $pb_row['id'], 'page_builder', 'Agent Shared Website', $_SESSION['agents']['fname'], $_SESSION['agents']['lname'], $desc);
			setNotifySuccess('You have successfully shared website');
		}
	    $response['status'] = 'success';
	    echo json_encode($response);
	    exit;
    } else {
    	if(count($validate->getErrors()) > 0){
	        $response['status'] = "errors";   
	        $response['errors'] = $validate->getErrors();   
	    }
    }
    echo json_encode($response);
    exit();
}

$from_email = '';
$from_name = '';
$email_subject = '';
$email_content = '';
$sms_content = '';

if (!empty($trigger_row)) {
    $from_email = $trigger_row['from_email'];
    $from_name = $trigger_row['from_name'];
    $sms_content = $trigger_row['sms_content'];
    $email_subject = $trigger_row['email_subject'];
    $email_content = html_entity_decode($trigger_row['email_content']);

    $email_content = str_replace('[[link]]',$url_link,$email_content);
    $sms_content = str_replace('[[link]]',$url_link,$sms_content);
}

$exStylesheets = array('thirdparty/summernote-master/dist/summernote.css');
$exJs = array(
	'thirdparty/summernote-master/dist/popper.js',
	'thirdparty/summernote-master/dist/summernote.js',
	'thirdparty/masked_inputs/jquery.inputmask.bundle.js',
	'thirdparty/vue-js/vue.min.js'
);

$template = 'share_website.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>