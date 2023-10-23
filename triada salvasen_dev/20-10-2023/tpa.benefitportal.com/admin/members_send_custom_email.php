<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$triggerArr = array();
$trigger_id = !empty($_GET['id']) ? $_GET['id'] : '';
$trigger_name = !empty($_GET['name']) ? $_GET['name'] : '';
$member_id = $_GET['member_id'];
if(!empty($trigger_id)){
	$triggerArr = $pdo->selectOne("SELECT * from triggers where id=:id and is_deleted='N' and status='Active' and user_group IN('member','other')",array(":id"=>$trigger_id));
}
$user_info = $pdo->selectOne("SELECT id,fname,lname,email,cell_phone from customer where md5(id)=:id and is_deleted='N'",array(":id"=>$member_id));

$default_email = getname('app_settings','default_email_from','setting_value','setting_key');

$default_email = !empty($default_email) ? $default_email : $emailer_settings[3]['tg_from_mailid'];

$exStylesheets = array('thirdparty/malihu_scroll/css/jquery.mCustomScrollbar.css'.$cache);
$exJs = array('thirdparty/malihu_scroll/js/jquery.mCustomScrollbar.concat.min.js', 'thirdparty/masked_inputs/jquery.maskedinput.min.js', 'thirdparty/ckeditor/ckeditor.js');

$template = 'members_send_custom_email.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>
