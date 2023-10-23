<?php
include_once (__DIR__) . '/includes/connect.php';
include_once '../includes/chat.class.php';
$LiveChat = new LiveChat();


$sqlAdmin = "SELECT a.id from admin a 
            LEFT JOIN $LIVE_CHAT_DB.sb_users sb_u ON (sb_u.app_user_id = a.id AND sb_u.app_user_type='Admin')
            where a.status !='Pending' AND sb_u.id is null";
$resAdmin = $pdo->select($sqlAdmin);

if(!empty($resAdmin)){
  foreach ($resAdmin as $key => $value) {
    $id=$value['id'];
    $live_chat_use = $LiveChat->addLiveChatUser($id,'Admin');
  }
}


?>