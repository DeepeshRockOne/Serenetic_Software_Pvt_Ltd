<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once dirname(__DIR__) . '/includes/chat.class.php';
$LiveChat = new LiveChat();

$action = checkIsset($_REQUEST["action"]);
$adminsArr = array();
if(in_array($action, array("online_admins","idle_admins"))){
	$selAdmin = "SELECT id,display_id,CONCAT(fname,' ',lname) as name FROM admin";
	$resAdmin = $pdo->select($selAdmin);
	if(!empty($resAdmin)){
		foreach ($resAdmin as $key => $value) {
			$adminsArr[$value["id"]] = $value;
		}
	}
}

if($action == "online_admins"){
	$online_admins = $LiveChat->get_online_admins();
}else if($action == "idle_admins"){
	$idle_admins = $LiveChat->get_idle_admins();
}else if($action == "active_chats"){
	$live_conversations = $LiveChat->get_live_conversations();
	$livechatArr = array();
	if(!empty($live_conversations)){
		foreach ($live_conversations as $row) {
			$assignedId = !empty($row["assignedId"]) ? $row["assignedId"] : 999;
			if(!isset($livechatArr[$assignedId])){
				$livechatArr[$assignedId] = array(
					"assignedId" => $assignedId,
					"chatCount" => 1
					);
			}else{
				$livechatArr[$assignedId]["chatCount"] = $livechatArr[$assignedId]["chatCount"] + 1;
			}
		}
	}

}else if($action == "in_queue_chats"){
	$in_queue_conversations = $LiveChat->get_in_queue_conversations();
	$membersQueue = 0;
	$agentsQueue = 0;
	$groupsQueue = 0;
	$websiteQueue = 0;
	if(!empty($in_queue_conversations)){
		foreach ($in_queue_conversations as $row) {
			if($row["app_user_type"] == "Customer"){
				$membersQueue++;
			}else if($row["app_user_type"] == "Agent"){
				$agentsQueue++;
			}else if($row["app_user_type"] == "Group"){
				$groupsQueue++;
			}else if($row["app_user_type"] == "Website"){
				$websiteQueue++;
			}
		}	
	}
}else if($action == "total_served_chat"){
	$served_conversations = $LiveChat->get_served_conversations();	
	$membersServed = 0;
	$agentsServed = 0;
	$groupsServed = 0;
	$websiteServed = 0;
	if(!empty($served_conversations)){
		foreach ($served_conversations as $row) {
			if($row["app_user_type"] == "Customer"){
				$membersServed++;
			}else if($row["app_user_type"] == "Agent"){
				$agentsServed++;
			}else if($row["app_user_type"] == "Group"){
				$groupsServed++;
			}else if($row["app_user_type"] == "Website"){
				$websiteServed++;
			}
		}	
	}
}

$exStylesheets = array('thirdparty/bootstrap-tables/css/bootstrap-table.min.css'.$cache);
$exJs = array('thirdparty/bootstrap-tables/js/bootstrap-table.min.js'.$cache);

$template = "live_chat_activity_popup.inc.php";
include_once 'layout/iframe.layout.php';
?>