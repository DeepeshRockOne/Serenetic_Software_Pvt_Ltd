<?php
include_once __DIR__ . '/includes/connect.php';
require_once dirname(__DIR__) . '/includes/chat.class.php';
$LiveChat = new LiveChat();

if(isset($_SESSION['admin']['id'])){
	$description['description'] = $_SESSION['admin']['display_id'].' logged out account';

	activity_feed(3, $_SESSION['admin']['id'], 'Admin', $_SESSION['admin']['id'], 'Admin','Logged Admin Account', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
	$user_data = get_user_data($_SESSION['admin']);
	audit_log($user_data, $_SESSION['admin']['id'], "Admin", "Log out", '', '', 'logout');
}

$status = $LiveChat->chatLogout();
unset($_SESSION['admin']);
unset($_SESSION['order']); //For admin Order
unset($_SESSION['order_total']);
unset($_SESSION['account_type']);
if(isset($_SESSION['sb-session']) && $_SESSION['sb-session']['app_user_type']=='Admin'){
    unset($_SESSION['sb-session']);
}
$previous_page = isset($_REQUEST['previous_page']) ? $_REQUEST['previous_page'] : '';
if(!empty($previous_page)){
  redirect("index.php?previous_page=".urlencode($previous_page));
}else{
  redirect('index.php');  
}
?>