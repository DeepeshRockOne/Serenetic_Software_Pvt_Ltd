<?php
include_once __DIR__ . '/includes/connect.php';

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

$trigger_id= 82;
$trigger_res = $pdo->selectOne("SELECT * FROM triggers WHERE id = :trigger_id",array(":trigger_id"=>$trigger_id));
if ($trigger_res > 0) {
	$sms_content = $trigger_res['sms_content'];
	$email_content = html_entity_decode($trigger_res['email_content']);
	$email_subject = $trigger_res['email_subject'];
}

$from_number = str_replace($callingCode,$callingCodeReplace, $TwilioNumber);

$exStylesheets = array(
	'thirdparty/jquery_ui/css/front/jquery-ui-1.9.2.custom.css'.$cache,
);
$exJs = array(
	'thirdparty/masked_inputs/jquery.inputmask.bundle.js',
	'thirdparty/jquery_ui/js/jquery-ui-autoComplete.js',	
);
$template = 'sms_share_website.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>
