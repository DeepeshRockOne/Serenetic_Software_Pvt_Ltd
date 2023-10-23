<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once __DIR__ . '/../includes/function.class.php';
$functionsList = new functionsList();

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Communications';
$breadcrumbes[2]['title'] = 'Text Message (SMS)';
$breadcrumbes[2]['link'] = 'sms_dashboard.php';
$breadcrumbes[3]['title'] = 'Broadcaster';
$breadcrumbes[3]['link'] = 'sms_broadcaster.php';

if ((isset($_GET['action']) && $_GET['action']=='delete') && isset($_GET['bro_id'])) {
	$broadcaster_id = $_GET['bro_id'];
	$broadcaster_res = $pdo->selectOne("SELECT id,display_id FROM broadcaster WHERE type='sms' AND md5(id) = :broadcaster_id", array(":broadcaster_id" => $broadcaster_id));

	if(!empty($broadcaster_res)) {
		$updateSql = array('is_deleted' => "Y");
		$where = array("clause" => 'id=:id', 'params' => array(':id' => makeSafe($broadcaster_res['id'])));
		$pdo->update("broadcaster", $updateSql, $where);

		$updateSql = array('is_deleted' => "Y");
		$where = array("clause" => 'broadcaster_id=:id', 'params' => array(':id' => makeSafe($broadcaster_res['id'])));
		$pdo->update("broadcaster_schedule_settings", $updateSql, $where);

		$description['ac_message'] =array(
			'ac_red_1'=>array(
				'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
				'title'=>$_SESSION['admin']['display_id'],
			),
			'ac_message_1' =>' Deleted SMS Broadcaster ',
			'ac_red_2'=>array(
		  	'href'=>$ADMIN_HOST.'/add_sms_broadcast.php?broadcaster_id='.md5($broadcaster_res['id']),
		  	'title'=>$broadcaster_res['display_id'],
			),
		); 

  		activity_feed(3, $_SESSION['admin']['id'], 'Admin', $broadcaster_res['id'], 'broadcaster','Deleted SMS Broadcaster', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));

		setNotifySuccess("SMS Broadcast deleted successfully!");
		redirect("sms_broadcaster.php");
	} else {
		setNotifyError("No record Found!");
		redirect("sms_broadcaster.php");
	}
}

if ((isset($_GET['action']) && $_GET['action']=='status') && isset($_GET['status']) && isset($_GET['bro_id'])){
	$broadcaster_id = $_GET['bro_id'];
	$status = $_GET['status'];
	$broadcaster_res = $pdo->selectOne("SELECT id, status, display_id FROM broadcaster WHERE md5(id) = :broadcaster_id", array(":broadcaster_id" => $broadcaster_id));

	if(!empty($broadcaster_res)) {
		$updateSql = array('status' => makeSafe($status));
		$where = array("clause" => 'id=:id', 'params' => array(':id' => makeSafe($broadcaster_res['id'])));
		$broadcaster_inc_id = $broadcaster_res['id'];
		//************* Activity Code Start *************
			$oldVaArray = $broadcaster_res;
			$NewVaArray = $updateSql;
			unset($oldVaArray['id']);
			unset($oldVaArray['display_id']);

			$activity=array_diff_assoc($NewVaArray, $oldVaArray);

		    $tmp = array();
		    $tmp2 = array();
	        if(!empty($activity)){
	          	if(array_key_exists('status',$activity)){
		            $tmp['status'] = $oldVaArray['status'];
		            $tmp2['status'] = $NewVaArray['status'];
	          	}

	            $link = $ADMIN_HOST.'/add_sms_broadcast.php?broadcaster_id='.md5($broadcaster_inc_id);

	            $functionsList->generalActivityFeed($tmp,$tmp2,$link,$broadcaster_res['display_id'],$broadcaster_inc_id,'broadcaster','Admin Updated SMS Broadcaster','Updated SMS Broadcaster');
			}
		//************* Activity Code End *************
		$pdo->update("broadcaster", $updateSql, $where);
		setNotifySuccess("SMS Broadcaster status updated successfully!");
		redirect("sms_broadcaster.php");
	} else {
		setNotifyError("No record Founnd!");
		redirect("sms_broadcaster.php");
	}
}

$is_ajax = isset($_GET['is_ajax']) ? $_GET['is_ajax'] : '';
if (isset($_GET["is_ajax"])) {
    include 'tmpl/sms_broadcaster.inc.php';
    exit;
}


$page_title = "SMS";
$template = 'sms_broadcaster.inc.php';
include_once 'layout/end.inc.php';
?>
