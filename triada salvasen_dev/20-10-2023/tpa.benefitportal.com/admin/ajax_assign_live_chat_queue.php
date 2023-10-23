<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$res = array();
$conversationID = !empty($_POST['conversationID']) ? $_POST['conversationID'] : '';
$loginChatID = $_SESSION['sb-session']['id'];


$sqlQueue = "SELECT sb_c.id as conversationID FROM $LIVE_CHAT_DB.sb_conversations sb_c
			WHERE sb_c.agent_id is null AND sb_c.id = :id";
$resQueue = $pdo->selectOne($sqlQueue,array(":id"=>$conversationID));

if(!empty($resQueue)){
	$updParams = array(
		'agent_id'=>$loginChatID,
		'assign_id'=>$loginChatID,
		'initial_assign_id'=>$loginChatID,
	);
	$updWhere = array('clause'=>'id=:id','params'=>array(":id"=>$conversationID));
	$pdo->update($LIVE_CHAT_DB.'.sb_conversations',$updParams,$updWhere);

	$res['status'] = 'success';
	$res['msg'] = "Assigned Successfull";
}else{
	$res['status'] = 'fail';
	$res['msg'] = "Already Assigned";
}

header('Content-Type:appliaction/json');
echo json_encode($res);
dbConnectionClose();
exit;
?>