<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$user_type = isset($_REQUEST['user_type']) ? $_REQUEST['user_type'] : '';
$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
$type = '';

	$popupTitle = '';
	if($user_type == 'agent'){
		$popupTitle = 'Agents';
	}else if($user_type == 'group'){
		$popupTitle = 'Groups';
	}elseif ($user_type == 'member'){
		$popupTitle = 'Members';
	}

	if(!empty($id)){
		$interactionSql = "SELECT * FROM interaction WHERE md5(id)=:id";
		$params = array(":id" => $id);
		$interactionRes = $pdo->selectOne($interactionSql,$params);
		$type = $interactionRes['type'];
		$user_type = $interactionRes['user_type'];

		$description['ac_message'] =array(
	      'ac_red_1'=>array(
	        'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
	        'title'=>$_SESSION['admin']['display_id'],
	      ),
	      'ac_message_1' =>' read interactions ',
	    ); 

	    activity_feed(3, $_SESSION['admin']['id'], 'Admin', $interactionRes['id'], 'interaction','Viewed Interaction', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
	}


$template = "manage_interaction.inc.php";
include_once 'layout/iframe.layout.php';
?>