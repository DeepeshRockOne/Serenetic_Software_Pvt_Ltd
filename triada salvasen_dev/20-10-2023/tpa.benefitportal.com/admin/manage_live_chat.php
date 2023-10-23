<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
require_once dirname(__DIR__) . '/includes/chat.class.php';
$LiveChat = new LiveChat();
$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Live Chat";
$breadcrumbes[1]['link'] = "live_chat_dashboard.php";
$breadcrumbes[2]['title'] = "Manage Live Chat";
$breadcrumbes[2]['link'] = 'manage_live_chat.php';
$saved_replies_res = $LiveChat->get_saved_replies();
$lc_department_res = $LiveChat->get_departments_by_name();
	
	if(isset($_POST['action']) && $_POST['action'] == "delete_reply") {
		$reply_key = isset($_POST['reply_key'])?$_POST['reply_key']:-1;

		if(isset($saved_replies_res[$reply_key])) {

			$desc = array();
			$desc['ac_message'] =array(
			    'ac_red_1'=>array(
			        'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
			        'title'=>$_SESSION['admin']['display_id'],
			    ),
			    'ac_message_1' =>'  deleted Live Chat Quick Reply',
			    'ac_red_2'=>array(
			        'href'=> 'javascript:void(0);',
			        'title'=> $saved_replies_res[$reply_key]['reply-name'],
			    ),
			);
			$desc=json_encode($desc);
			activity_feed(3,$_SESSION['admin']['id'], 'Admin',$_SESSION['admin']['id'],'admin','Admin Deleted Live Chat Quick Reply',$_SESSION['admin']['name'],"",$desc);
		}

		unset($saved_replies_res[$reply_key]);
		$saved_replies_res_tmp = array();
		foreach ($saved_replies_res as $row) {
			$saved_replies_res_tmp[] = $row;
		}
		$LiveChat->update_saved_replies($saved_replies_res_tmp);
		setNotifySuccess("Quick Reply deleted successfully.");
		echo json_encode(array('status' => 'success'));
		exit();
	}
	
	if(isset($_POST['action']) && $_POST['action'] == "delete_department") {
		$department_id = isset($_POST['department_id'])?$_POST['department_id']:0;

		foreach ($lc_department_res as $lc_department_row) {
			if($lc_department_row['id'] == $department_id && !empty($lc_department_row['name'])) {
				$desc = array();
				$desc['ac_message'] =array(
				    'ac_red_1'=>array(
				        'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
				        'title'=>$_SESSION['admin']['display_id'],
				    ),
				    'ac_message_1' =>'  deleted Live Chat Department',
				    'ac_red_2'=>array(
				        'href'=> 'javascript:void(0);',
				        'title'=> $lc_department_row['name'],
				    ),
				);
				$desc=json_encode($desc);
				activity_feed(3,$_SESSION['admin']['id'], 'Admin',$_SESSION['admin']['id'],'admin','Admin Deleted Live Chat Department',$_SESSION['admin']['name'],"",$desc);
				break;
			}
		}

		$LiveChat->delete_department($department_id);
		setNotifySuccess("Department deleted successfully.");
		echo json_encode(array('status' => 'success'));
		exit();
	}
	

$template = 'manage_live_chat.inc.php';
include_once 'layout/end.inc.php';
?>