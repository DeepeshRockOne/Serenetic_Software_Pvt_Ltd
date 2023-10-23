<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$id = $_REQUEST['id'];

	$triggerSql = "SELECT id,email_content,sms_content FROM triggers WHERE md5(id)=:id AND is_deleted='N'";
	$triggerParams = array(":id" => $id);
	$triggerRes = $pdo->selectOne($triggerSql,$triggerParams);

	$prevEmailContent = !empty($triggerRes['email_content']) ? $triggerRes['email_content'] : '';
	$prevSMSContent = !empty($triggerRes['sms_content']) ? $triggerRes['sms_content'] : '';

$layout = 'iframe.layout.php';
$template = 'email_trigger_preview.inc.php';
include_once 'layout/end.inc.php';
?>
