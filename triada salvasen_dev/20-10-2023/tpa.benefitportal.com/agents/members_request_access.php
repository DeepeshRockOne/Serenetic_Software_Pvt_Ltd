<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$validate = new Validation();
$REQ_URL = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$member_id = $_REQUEST['member_id'];
$member_sql = "SELECT * FROM customer WHERE md5(id)=:id";
$member_res = $pdo->selectOne($member_sql,array(":id"=>$member_id));

if(isset($_POST['save'])){
	$send_via = isset($_POST['send_via'])?$_POST['send_via']:'';
	$validate->string(array('required' => true, 'field' => 'send_via', 'value' => $send_via), array('required' => 'Please select any option'));
	if($validate->isValid()){

		$otp = generateOTP();
		$trigger_id = getname('triggers','T631','id','display_id');

		if($send_via=="email" && !empty($member_res["email"])){
			$params = array();
			$params['fname'] = $member_res["fname"];
			$params['agent_name'] = $_SESSION['agents']['fname']. ' ' . $_SESSION['agents']['lname'];
			$params['agent_email'] = $_SESSION['agents']['email'];      
			$params['agent_phone'] = $_SESSION['agents']['cell_phone'];
			$params['Code'] = $otp;
			$contact_email = $member_res["email"];
			$params['USER_IDENTITY'] = array('rep_id' => $member_res['rep_id'], 'cust_type' => 'Member', 'location' => $REQ_URL);

			$smart_tags = get_user_smart_tags($member_res['id'],'member');
                
	        if($smart_tags){
	            $params = array_merge($params,$smart_tags);
	        }

			trigger_mail($trigger_id, $params, $contact_email);
			$_SESSION['member_otp'] = array(
			"member_id"=>$member_id,
			"otp"=>$otp,
			"send_via"=>"Email",
			);
			redirect("get_member_otp_popup.php?member_id=".$member_id);
		} elseif ($send_via=="sms" && !empty($member_res["cell_phone"])){
			$country_code = '+1';
			$toPhone = $country_code . $member_res["cell_phone"];
			$params = array();
			$params['Code'] = $otp;
			$params['agent_name'] = $_SESSION['agents']['fname']. ' ' . $_SESSION['agents']['lname'];
			$params['agent_email'] = $_SESSION['agents']['email'];      
			$params['agent_phone'] = $_SESSION['agents']['cell_phone'];
			$params['USER_IDENTITY'] = array('rep_id' => $member_res['rep_id'], 'cust_type' => 'Member', 'location' => $REQ_URL);
            if($SITE_ENV=='Local') {
                $toPhone = '+917984974053';
            }
			trigger_sms($trigger_id, $toPhone, $params);
			$_SESSION['member_otp'] = array(
				"member_id"=>$member_id,
				"otp"=>$otp,
				"send_via"=>"SMS",
			);
			redirect("get_member_otp_popup.php?member_id=".$member_id);
		} else {
			$validate->setError("send_via","Something went wrong.");
		}
	}
}
$errors = $validate->getErrors();
$template = 'members_request_access.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>