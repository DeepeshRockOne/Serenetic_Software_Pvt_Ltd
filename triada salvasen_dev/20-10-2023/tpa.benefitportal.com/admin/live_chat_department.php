<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
require_once dirname(__DIR__) . '/includes/chat.class.php';
$LiveChat = new LiveChat();
$lc_departments = $LiveChat->get_departments();
if(!is_array($lc_departments)) {
	$lc_departments = array();
}

$department_id = isset($_GET['department_id'])?$_GET['department_id']: 0;
$department_name = '';
if($department_id > 0 && !empty($lc_departments) && isset($lc_departments[$department_id])) {
	$department_name = isset($lc_departments[$department_id]['name'])?$lc_departments[$department_id]['name']:'';
}

if(isset($_POST['action']) && $_POST['action'] == "save_department") {
	$department_id = isset($_POST['department_id'])?$_POST['department_id']:0;
	$department_name = isset($_POST['department_name'])?$_POST['department_name']:'';
	$response = array();

	$validate = new Validation();
	$validate->string(array('required' => true, 'field' => 'department_name', 'value' => $department_name), array('required' => 'Name is required.'));

	if ($validate->isValid()) {		
		if($department_id > 0) {
			$department_data = array(
				"department-id" => $department_id,
				"department-name" => $department_name,
				"department-color" => '',
				"department-image" => '',
			);

			if(isset($lc_departments[$department_id]['name']) && $lc_departments[$department_id]['name'] != $department_name) {
				$desc = array();
				$desc['ac_message'] =array(
				    'ac_red_1'=>array(
				        'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
				        'title'=>$_SESSION['admin']['display_id'],
				    ),
				    'ac_message_1' =>'  updated Live Chat Department',
				    'ac_red_2'=>array(
				        'href'=> 'javascript:void(0);',
				        'title'=> $department_name,
				    ),
				);
				$desc['key_value']['desc_arr']["Department"]='From '.$lc_departments[$department_id]['name'].' To '.$department_name; 

				$desc=json_encode($desc);
				activity_feed(3,$_SESSION['admin']['id'], 'Admin',$_SESSION['admin']['id'],'admin','Admin Updated Live Chat Department',$_SESSION['admin']['name'],"",$desc);
			}
		} else {
			$department_data = array(
				"department-id" => $department_id,
				"department-name" => $department_name,
				"department-color" => '',
				"department-image" => '',
			);

			$desc = array();
			$desc['ac_message'] =array(
			    'ac_red_1'=>array(
			        'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
			        'title'=>$_SESSION['admin']['display_id'],
			    ),
			    'ac_message_1' =>'  created Live Chat Department',
			    'ac_red_2'=>array(
			        'href'=> 'javascript:void(0);',
			        'title'=> $department_name,
			    ),
			);
			$desc=json_encode($desc);
			activity_feed(3,$_SESSION['admin']['id'], 'Admin',$_SESSION['admin']['id'],'admin','Admin Created Live Chat Department',$_SESSION['admin']['name'],"",$desc);
		}
		$LiveChat->update_departments($department_id,$department_data);
		setNotifySuccess("Department saved successfully.");
		$response['status'] = 'success';
	} else {
		$errors = $validate->getErrors();
	    $response['status'] = 'fail';
	    $response['errors'] = $errors;
	}
	echo json_encode($response);
	exit();
}
$template = "live_chat_department.inc.php";
include_once 'layout/iframe.layout.php';
?>