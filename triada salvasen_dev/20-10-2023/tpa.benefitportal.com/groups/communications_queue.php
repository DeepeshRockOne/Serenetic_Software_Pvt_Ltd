<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
group_has_access(13);
$breadcrumbes[0]['title'] = '<i class="material-icons">home</i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Resources';
$breadcrumbes[2]['title'] = 'Communications Queue';


if ((isset($_GET['action']) && $_GET['action']=='delete') && isset($_GET['bro_id'])) {
	$broadcaster_id = $_GET['bro_id'];
	$broadcaster_res = $pdo->selectOne("SELECT id,display_id FROM broadcaster WHERE md5(id) = :broadcaster_id", array(":broadcaster_id" => $broadcaster_id));

	if(!empty($broadcaster_res)) {
		$updateSql = array('is_deleted' => "Y");
		$where = array("clause" => 'id=:id', 'params' => array(':id' => makeSafe($broadcaster_res['id'])));
		$pdo->update("broadcaster", $updateSql, $where);

		$updateSql = array('is_deleted' => "Y");
		$where = array("clause" => 'broadcaster_id=:id', 'params' => array(':id' => makeSafe($broadcaster_res['id'])));
		$pdo->update("broadcaster_schedule_settings", $updateSql, $where);

		$description['ac_message'] = array(
	      'ac_red_1'=>array(
	        'href'=>$ADMIN_HOST.'/groups_details.php?id='.md5($_SESSION['groups']['id']),
	        'title'=>$_SESSION['groups']['rep_id'],
	      ),
	      'ac_message_1' =>' Deleted Broadcaster ',
	      'ac_red_2'=>array(
	          'href'=>$GROUP_HOST.'/communications_queue.php',
	          'title'=>$broadcaster_res['display_id'],
	      ),
	    ); 
	    activity_feed(3, $_SESSION['groups']['id'], 'Group', $broadcaster_res['id'], 'broadcaster','Deleted Broadcaster', $_SESSION['groups']['fname'],$_SESSION['groups']['lname'],json_encode($description));

		setNotifySuccess("Broadcaster deleted successfully!");
		redirect("communications_queue.php");
	} else {
		setNotifyError("No record Found!");
		redirect("communications_queue.php");
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

		$pdo->update("broadcaster", $updateSql, $where);

		$description['ac_message'] = array(
	      'ac_red_1'=>array(
	        'href'=>$ADMIN_HOST.'/groups_details.php?id='.md5($_SESSION['groups']['id']),
	        'title'=>$_SESSION['groups']['rep_id'],
	      ),
	      'ac_message_1' =>' Updated Broadcaster Status From'.$broadcaster_res['status'].'to'.$status,
	      'ac_red_2'=>array(
	          'href'=>$GROUP_HOST.'/communications_queue.php',
	          'title'=>$broadcaster_res['display_id'],
	      ),
	    ); 
	    activity_feed(3, $_SESSION['groups']['id'], 'Group', $broadcaster_res['id'], 'broadcaster','Updated Broadcaster', $_SESSION['groups']['fname'],$_SESSION['groups']['lname'],json_encode($description));

		setNotifySuccess("Broadcaster status updated successfully!");
		redirect("communications_queue.php");
	} else {
		setNotifyError("No record Founnd!");
		redirect("communications_queue.php");
	}
}

$is_ajax = isset($_GET['is_ajax']) ? $_GET['is_ajax'] : '';
if (isset($_GET["is_ajax"])) {
    include 'tmpl/communications_queue.inc.php';
    exit;
}

$description['ac_message'] = array(
    'ac_red_1' => array(
        'href' => 'groups_details.php?id=' . md5($_SESSION['groups']['id']),
        'title' => $_SESSION['groups']['rep_id'],
	),
	'ac_message_1' => 'read Resource',
	'ac_red_2'=>array(
		'href'=>$GROUP_HOST.'/communications_queue.php',
		'title'=>'Communications Queue'
	),
);
$desc = json_encode($description);
activity_feed(3, $_SESSION['groups']['id'], 'Group', $_SESSION['groups']['id'], 'Group', 'Group Read Resources', $_SESSION['groups']['fname'], $_SESSION['groups']['lname'], $desc);

$template = 'communications_queue.inc.php';
include_once 'layout/end.inc.php';
?>
