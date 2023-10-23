<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$id = $_REQUEST['id'];

$msgSql = "SELECT id,broadcaster_id,message FROM broadcaster_message WHERE md5(broadcaster_id)=:id AND is_deleted='N'";
$msgParams = array(":id" => $id);
$msgRes = $pdo->select($msgSql,$msgParams);

$broadcastName = getname("broadcaster",$id,'brodcast_name','md5(id)');

$layout = 'iframe.layout.php';
$template = 'broadcaster_sms_preview.inc.php';
include_once 'layout/end.inc.php';
?>
