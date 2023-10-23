<?php
include_once dirname(__FILE__) . '/includes/connect.php';

$log_id = checkIsset($_GET['log_id']); 

$smsLogRes = array();

if(!empty($log_id)){
	$smsLogSql = "SELECT md5(s.id) as id,s.to_number,s.message,t.display_id as triggerDispId
					FROM sms_log s 
					LEFT JOIN triggers t ON(s.trigger_id=t.id)
					WHERE md5(s.id) = :id";
	$smsLogRes = $pdo->selectOne($smsLogSql,array(":id" => $log_id));
}

$smsContent = !empty($smsLogRes['message']) ? $smsLogRes['message'] : '';
$toNumber = !empty($smsLogRes['to_number']) ? $smsLogRes['to_number'] : '';
$triggerDispId = !empty($smsLogRes['triggerDispId']) ? $smsLogRes['triggerDispId'] : '';


$template = 'send_sms_content.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>
