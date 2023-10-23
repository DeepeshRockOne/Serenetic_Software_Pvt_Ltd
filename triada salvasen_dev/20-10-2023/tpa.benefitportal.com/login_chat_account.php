<?php
header('Access-Control-Allow-Origin: *');
include_once __DIR__ . '/includes/connect.php';
require_once __DIR__ . '/includes/chat.class.php';
$LiveChat = new LiveChat();

if(isset($_REQUEST['action']) && $_REQUEST['action'] == "login_chat_account" && !empty($_REQUEST['location'])) {
	$user_id = '';
	$user_type = '';
	if($_REQUEST['location'] == "agent") {
		if(isset($_SESSION['agents']['chat_logout'])) {
			unset($_SESSION['agents']['chat_logout']);
		}
		$user_id = $_SESSION['agents']['id'];
		$user_type = 'Agent';

	} else if($_REQUEST['location'] == "admin") {
		if(isset($_SESSION['admin']['chat_logout'])) {
			unset($_SESSION['admin']['chat_logout']);
		}
		$user_id = $_SESSION['admin']['id'];
		$user_type = 'Admin';

	} else if($_REQUEST['location'] == "group") {
		if(isset($_SESSION['groups']['chat_logout'])) {
			unset($_SESSION['groups']['chat_logout']);
		}
		$user_id = $_SESSION['groups']['id'];
		$user_type = 'Group';

	} else if($_REQUEST['location'] == "member") {
		if(isset($_SESSION['customer']['chat_logout'])) {
			unset($_SESSION['customer']['chat_logout']);
		}
		$user_id = $_SESSION['customer']['id'];
		$user_type = 'Customer';
	}

	if(!empty($user_id) && !empty($user_type)) {
		$is_login = $LiveChat->login_to_chat_account($user_id,$user_type);
		if($is_login) {
			if($_REQUEST['location'] == "admin") {
				/*--- Activity Feed -----*/
				$desc = array();
				$desc['ac_message'] =array(
				    'ac_red_1'=>array(
				        'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
				        'title'=>$_SESSION['admin']['display_id'],
				    ),
				    'ac_message_1' =>'  went GO LIVE on chat',
				);
				$desc=json_encode($desc);
				activity_feed(3,$_SESSION['admin']['id'], 'Admin',$_SESSION['admin']['id'],'admin','Admin Went GO LIVE on Chat',$_SESSION['admin']['name'],"",$desc);
				/*---/Activity Feed -----*/
			}
			$res = array('status'=>'success');
		} else {
			$res = array('status'=>'fail');
		}
	} else {
		$res = array('status'=>'fail');
	}
	echo json_encode($res);
	exit();
}
if(isset($_REQUEST['action']) && $_REQUEST['action'] == "logout_chat_account" && !empty($_REQUEST['location'])) {
	$user_id = '';
	$user_type = '';
	$userID = $_REQUEST['userID'];
	$conversationID = $_REQUEST['conversationID'];
	if($_REQUEST['location'] == "agent") {
		$user_id = $_SESSION['agents']['id'];
		$user_type = 'Agent';
		$_SESSION['agents']['chat_logout'] = true;
		
	} else if($_REQUEST['location'] == "admin") {
		$user_id = $_SESSION['admin']['id'];
		$user_type = 'Admin';
		$_SESSION['admin']['chat_logout'] = true;

	} else if($_REQUEST['location'] == "group") {
		$user_id = $_SESSION['groups']['id'];
		$user_type = 'Group';
		$_SESSION['groups']['chat_logout'] = true;

	} else if($_REQUEST['location'] == "member") {
		$user_id = $_SESSION['customer']['id'];
		$user_type = 'Customer';
		$_SESSION['customer']['chat_logout'] = true;
	} 
	$status = $LiveChat->updateConversation($conversationID,4,$userID,'User has left conversation');
	$res = array('status'=>'success','api'=>$status);
	echo json_encode($res);
	exit();
}
?>