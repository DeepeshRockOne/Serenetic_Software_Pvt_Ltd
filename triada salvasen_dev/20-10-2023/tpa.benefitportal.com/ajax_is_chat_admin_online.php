<?php
include_once __DIR__ . '/includes/connect.php';
require_once __DIR__ . '/includes/chat.class.php';
$LiveChat = new LiveChat();

$online_admins = $LiveChat->get_online_admins();

$status = "Offline";
if(!empty($online_admins)){
	$status = "Online";
}
$res = array('status'=>$status);
echo json_encode($res);
exit();
?>