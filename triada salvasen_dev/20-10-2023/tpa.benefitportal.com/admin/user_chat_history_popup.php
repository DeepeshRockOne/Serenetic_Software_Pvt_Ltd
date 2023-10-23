<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
//has_access(5);

$threadid = $_REQUEST['thread'];

$selU = "SELECT userName FROM $WEBIM_DB.chatthread WHERE threadid=:threadid";
$whrU = array(":threadid"=>$threadid);
$resU = $pdo->selectOne($selU,$whrU);


$sel = "SELECT c.customer_id,c.userName,m.* 
		FROM $WEBIM_DB.chatthread c  
		LEFT JOIN $WEBIM_DB.chatmessage m ON(c.threadid=m.threadid)
		WHERE c.threadid=:threadid AND m.tmessage!='' ORDER BY m.messageid ASC";
$arr = array(":threadid"=>$threadid);		
$result = $pdo->select($sel,$arr);

//$exJs = array('thirdparty/simscroll/jquery.slimscroll.min.js');

$template = 'user_chat_history_popup.inc.php';
include_once 'layout/iframe.layout.php';
?>