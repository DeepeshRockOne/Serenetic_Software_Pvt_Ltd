<?php
	include_once dirname(__FILE__) . '/layout/start.inc.php';

	$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
	if(!empty($id)){
		$reasonSql = "SELECT id,name FROM termination_reason WHERE md5(id)=:id";
		$reasonParams = array(":id" => $id);
		$reasonRes = $pdo->selectOne($reasonSql,$reasonParams);

		$type = $reasonRes['name'];
	
		$description['ac_message'] =array(
	      'ac_red_1'=>array(
	        'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
	        'title'=>$_SESSION['admin']['display_id'],
	      ),
	      'ac_message_1' =>' read Reversal Reasons ',
	    ); 

	    activity_feed(3, $_SESSION['admin']['id'], 'Admin', $reasonRes['id'], 'termination_reason','Viewed Reversal Reasons', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
	}

$template = "manage_reversal_reasons.inc.php";
include_once 'layout/iframe.layout.php';
?>