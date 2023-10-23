<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
require_once dirname(__DIR__) . '/includes/chat.class.php';
$LiveChat = new LiveChat();

$loginChatID = $_SESSION['sb-session']['id'];

$saved_replies_res = $LiveChat->get_saved_replies();
$lc_department_res = $LiveChat->get_departments_by_name();

$exStylesheets = array(
	'thirdparty/malihu_scroll/css/jquery.mCustomScrollbar.css', 
	'thirdparty/bootstrap-tables/css/bootstrap-table.min.css');
$exJs = array(
	'thirdparty/malihu_scroll/js/jquery.mCustomScrollbar.concat.min.js', 
	'thirdparty/bootstrap-tables/js/bootstrap-table.min.js',
	'live_chat/js/init.js'.$cache,
);

$template = "go_live_chat.inc.php";
include_once 'layout/iframe.layout.php';
?>