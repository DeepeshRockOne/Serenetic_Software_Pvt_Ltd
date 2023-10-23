<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$website_id = $_GET['id'];


$website_sql= "SELECT pb.id,pb.page_name,pb.user_name FROM page_builder pb WHERE pb.id=:id";
$website_res= $pdo->selectOne($website_sql,array(":id"=>$website_id));

$website_name = '';
$website_url = '';
if(!empty($website_res)){
	$website_name = $website_res['page_name'];
	$website_url = $GROUP_ENROLLMENT_WEBSITE_HOST.'/'.$website_res['user_name'];
}

if($website_id == 0){
	$website_name = 'Default';
	$website_url = $HOST.'/quote/'.$_SESSION['groups']['user_name'];
}

$exJs = array('thirdparty/clipboard/clipboard.min.js');
$template = "share_website_link.inc.php";
include_once 'layout/iframe.layout.php';
?>