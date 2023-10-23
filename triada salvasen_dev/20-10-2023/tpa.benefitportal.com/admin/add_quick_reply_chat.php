<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
require_once dirname(__DIR__) . '/includes/chat.class.php';
$LiveChat = new LiveChat();
$saved_replies_res = $LiveChat->get_saved_replies();
if(!is_array($saved_replies_res)) {
	$saved_replies_res = array();
}

$reply_key = isset($_GET['reply_key'])?$_GET['reply_key']: -1;
$reply_name = '';
$reply_text = '';
if($reply_key >= 0 && !empty($saved_replies_res) && isset($saved_replies_res[$reply_key])) {
	$reply_name = isset($saved_replies_res[$reply_key]['reply-name'])?$saved_replies_res[$reply_key]['reply-name']:'';
	$reply_text = isset($saved_replies_res[$reply_key]['reply-text'])?$saved_replies_res[$reply_key]['reply-text']:'';

	if(!empty($reply_name)) {
		/*--- Activity Feed -----*/
		$desc = array();
		$desc['ac_message'] =array(
		    'ac_red_1'=>array(
		        'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
		        'title'=>$_SESSION['admin']['display_id'],
		    ),
		    'ac_message_1' =>'  read Live Chat Quick Reply',
		    'ac_red_2'=>array(
		        'href'=> 'javascript:void(0);',
		        'title'=> $reply_name,
		    ),
		);
		$desc=json_encode($desc);
		activity_feed(3,$_SESSION['admin']['id'], 'Admin',$_SESSION['admin']['id'],'admin','Admin Read Live Chat Quick Reply',$_SESSION['admin']['name'],"",$desc);
		/*---/Activity Feed -----*/
	}
}

if(isset($_POST['action']) && $_POST['action'] == "save_reply") {
	$reply_key = isset($_POST['reply_key'])?$_POST['reply_key']:-1;
	$reply_name = isset($_POST['reply_name'])?$_POST['reply_name']:'';
	$reply_text = isset($_POST['reply_text'])?$_POST['reply_text']:'';
	$response = array();

	$validate = new Validation();
	$validate->string(array('required' => true, 'field' => 'reply_name', 'value' => $reply_name), array('required' => 'Reply Name is required.'));
	$validate->string(array('required' => true, 'field' => 'reply_text', 'value' => $reply_text), array('required' => 'Reply Text is required.'));

	if ($validate->isValid()) {		
		if($reply_key >= 0 && !empty($saved_replies_res) && isset($saved_replies_res[$reply_key])) {

			$desc = array();
			$desc['ac_message'] =array(
			    'ac_red_1'=>array(
			        'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
			        'title'=>$_SESSION['admin']['display_id'],
			    ),
			    'ac_message_1' =>'  updated Live Chat Quick Reply',
			    'ac_red_2'=>array(
			        'href'=> 'javascript:void(0);',
			        'title'=> $reply_name,
			    ),
			);

			if($saved_replies_res[$reply_key]['reply-name'] != $reply_name) {
				$desc['key_value']['desc_arr']["Reply Name"]='From '.$saved_replies_res[$reply_key]['reply-name'].' To '.$reply_name; 
			}

			if($saved_replies_res[$reply_key]['reply-text'] != $reply_text) {
				$desc['key_value']['desc_arr']["Reply Text"]='From '.$saved_replies_res[$reply_key]['reply-text'].' To '.$reply_text; 
			}
			
			if(!empty($desc['key_value']['desc_arr'])) {
				$desc=json_encode($desc);
				activity_feed(3,$_SESSION['admin']['id'], 'Admin',$_SESSION['admin']['id'],'admin','Admin Updated Live Chat Quick Reply',$_SESSION['admin']['name'],"",$desc);
			}

			$saved_replies_res[$reply_key]['reply-name'] = $reply_name;
			$saved_replies_res[$reply_key]['reply-text'] = $reply_text;
		} else {
			$saved_replies_res[] = array(
				"reply-name" => $reply_name,
				"reply-text" => $reply_text,
			);

			$desc = array();
			$desc['ac_message'] =array(
			    'ac_red_1'=>array(
			        'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
			        'title'=>$_SESSION['admin']['display_id'],
			    ),
			    'ac_message_1' =>'  created Live Chat Quick Reply',
			    'ac_red_2'=>array(
			        'href'=> 'javascript:void(0);',
			        'title'=> $reply_name,
			    ),
			);
			$desc=json_encode($desc);
			activity_feed(3,$_SESSION['admin']['id'], 'Admin',$_SESSION['admin']['id'],'admin','Admin Created Live Chat Quick Reply',$_SESSION['admin']['name'],"",$desc);
		}
		$LiveChat->update_saved_replies($saved_replies_res);
		setNotifySuccess("Quick Reply saved successfully.");
		$response['status'] = 'success';
	} else {
		$errors = $validate->getErrors();
	    $response['status'] = 'fail';
	    $response['errors'] = $errors;
	}
	echo json_encode($response);
	exit();
}

$template = "add_quick_reply_chat.inc.php";
include_once 'layout/iframe.layout.php';
?>