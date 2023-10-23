<?php
include_once dirname(__DIR__) . "/cron_scripts/connect.php";
include_once dirname(__DIR__) . "/includes/chat.class.php";
    $LiveChat = new LiveChat();


    $sqlAdmin = "SELECT id FROM admin where password != ''";
    $resAdmin = $pdo->select($sqlAdmin);

    if(!empty($resAdmin)){
    	foreach ($resAdmin as $key => $value) {
    		$status = $LiveChat->addLiveChatUser($value['id'],'Admin');
    	}
    }

?>