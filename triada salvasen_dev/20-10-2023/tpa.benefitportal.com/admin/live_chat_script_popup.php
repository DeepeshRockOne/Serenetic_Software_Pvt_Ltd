<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
require_once dirname(__DIR__) . '/includes/chat.class.php';
$LiveChat = new LiveChat();
$id = (isset($_GET['id'])?$_GET['id']:0);
$user_id = (isset($_GET['user_id'])?$_GET['user_id']:0);
$con_data = $LiveChat->conversation_messages($user_id,$id);
//pre_print($con_data);
                               
$chatClass = "";

if(!empty($con_data['details'])) {
	
	$appUserType = $con_data['details']['app_user_type'];
	if($appUserType == "Agent"){
	    $chatClass='agent';
	}else if($appUserType == "Group"){
	    $chatClass='group';
	}else if($appUserType == "Customer"){
	    $chatClass='member';
	}else if($appUserType == "Website"){
	    $chatClass='ex_web';
	}
	
}


/*--- Activity Feed -----*/
$desc = array();
$desc['ac_message'] =array(
    'ac_red_1'=>array(
        'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
        'title'=>$_SESSION['admin']['display_id'],
    ),
    'ac_message_1' =>'  read Live Chat script',
    'ac_red_2'=>array(
        'href'=> 'javascript:void(0);',
        'title'=> $con_data['details']['display_id'],
    ),
);
$desc=json_encode($desc);
activity_feed(3,$_SESSION['admin']['id'], 'Admin',$_SESSION['admin']['id'],'admin','Admin Read Live Chat script',$_SESSION['admin']['name'],"",$desc);
/*---/Activity Feed -----*/

$exStylesheets = array('thirdparty/malihu_scroll/css/jquery.mCustomScrollbar.css');
$exJs = array(
	'thirdparty/malihu_scroll/js/jquery.mCustomScrollbar.concat.min.js', 
	'thirdparty/bootstrap-tables/js/bootstrap-table.min.js',
	'live_chat/js/init.js'.$cache,
);

$template = "live_chat_script_popup.inc.php";
include_once 'layout/iframe.layout.php';
?>