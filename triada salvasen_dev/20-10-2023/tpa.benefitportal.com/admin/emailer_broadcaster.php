<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';


$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Communications';
$breadcrumbes[2]['title'] = 'Email';
$breadcrumbes[2]['link'] = 'emailer_dashboard.php';
$breadcrumbes[3]['title'] = 'Broadcaster';
$breadcrumbes[3]['link'] = 'emailer_broadcaster.php';

if (isset($_GET['is_deleted']) && isset($_GET['bro_id'])) {
	$broadcaster_id = $_GET['bro_id'];
	$broadcaster_res = $pdo->selectOne("SELECT id,display_id FROM broadcaster WHERE md5(id) = :broadcaster_id", array(":broadcaster_id" => $broadcaster_id));

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
			'ac_message_1' =>' Deleted Email Broadcaster ',
			'ac_red_2'=>array(
		  	'href'=>$ADMIN_HOST.'/add_email_broadcast.php?broadcaster_id='.md5($broadcaster_res['id']),
		  	'title'=>$broadcaster_res['display_id'],
			),
		); 

  	activity_feed(3, $_SESSION['admin']['id'], 'Admin', $broadcaster_res['id'], 'email_broadcaster','Deleted Email Broadcaster', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));

		setNotifySuccess("Email Broadcast deleted successfully!");
		redirect("emailer_broadcaster.php");
	} else {
		setNotifyError("No record Founnd!");
		redirect("emailer_broadcaster.php");
	}
}

if (isset($_GET['status']) && isset($_GET['bro_id'])) {
	$broadcaster_id = $_GET['bro_id'];
	$pro_status = $_GET['status'];
	$broadcaster_res = $pdo->selectOne("SELECT id, status, display_id FROM broadcaster WHERE md5(id) = :broadcaster_id", array(":broadcaster_id" => $broadcaster_id));

	if(!empty($broadcaster_res)) {
		$updateSql = array('status' => makeSafe($pro_status));
		$where = array("clause" => 'id=:id', 'params' => array(':id' => makeSafe($broadcaster_res['id'])));
		$broadcaster_inc_id = $broadcaster_res['id'];
		//************* Activity Code Start *************
		$oldVaArray = $broadcaster_res;
		$NewVaArray = $updateSql;
		unset($oldVaArray['id']);

		$checkDiff=array_diff_assoc($NewVaArray, $oldVaArray);
		if(!empty($checkDiff)){
			$activityFeedDesc['ac_message'] =array(
				'ac_red_1'=>array(
					'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
					'title'=>$_SESSION['admin']['display_id']),
				'ac_message_1' =>' Updated Email Broadcaster ',
			); 
			
			$extraJson = array();
			foreach ($checkDiff as $key1 => $value1) {
				$activityFeedDesc['key_value']['desc_arr'][$key1]='From '.$oldVaArray[$key1].' To '.$NewVaArray[$key1];
			}

			$activityFeedDesc['ac_message']['ac_red_2']=array(
				'href'=>$ADMIN_HOST.'/add_email_broadcast.php?broadcaster_id='.md5($broadcaster_inc_id),
				'title'=>$broadcaster_res['display_id']
			); 
			
			activity_feed(3, $_SESSION['admin']['id'], 'Admin', $broadcaster_inc_id, 'email_broadcaster','Admin Updated Email Broadcaster', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($activityFeedDesc),'',json_encode($extraJson));
		}
		//************* Activity Code End *************
		$pdo->update("broadcaster", $updateSql, $where);
		setNotifySuccess("Email Broadcaster status changed successfully!");
		redirect("emailer_broadcaster.php");
	} else {
		setNotifyError("No record Founnd!");
		redirect("emailer_broadcaster.php");
	}
}

$is_ajax = isset($_GET['is_ajax']) ? $_GET['is_ajax'] : '';
if (isset($_GET["is_ajax"])) {
    include 'tmpl/emailer_broadcaster.inc.php';
    exit;
}

$page_title = "Email";
$template = 'emailer_broadcaster.inc.php';
$layout = 'main.layout.php';
include_once 'layout/end.inc.php';
?>
