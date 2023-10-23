<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once __DIR__ . '/../includes/chat.class.php';

$LiveChat = new LiveChat();
$conversationID = $_POST['conversationID'];
$agent_id = $_POST['agent_id'];

$sqlQueue = "SELECT sb_c.id as conversationID,sb_c.agent_id,sb_c.assign_id,sb_c.assist_id FROM $LIVE_CHAT_DB.sb_conversations sb_c
			WHERE sb_c.id = :id";
$resQueue = $pdo->selectOne($sqlQueue,array(":id"=>$conversationID));

if(!empty($resQueue)){
	if($resQueue['assign_id']==$agent_id){
		$status = $LiveChat->adminSendMessage($conversationID,4,$agent_id,'Admin has left conversation');
		$updParams = array('agent_id'=>NULL,'assign_id'=>NULL);
	}else if($resQueue['assist_id'] == $agent_id){
		$updParams = array('agent_id'=>NULL,'assist_id'=>NULL);
	}
	$updWhere = array('clause'=>'id=:id','params'=>array(":id"=>$conversationID));
	$pdo->update($LIVE_CHAT_DB.'.sb_conversations',$updParams,$updWhere);

	$res['status'] = 'success';
	$res['msg'] = "Unassigned Successfull";
}else{
	$res['status'] = 'fail';
}

header('Content-Type:appliaction/json');
echo json_encode($res);
dbConnectionClose();
exit;
?>