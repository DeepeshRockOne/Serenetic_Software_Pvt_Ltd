<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$broadcaster_id = isset($_GET['id']) ? $_GET['id'] : '';
$brodcast_name = '';
$mail_content = '';
$subject = '';
if(!empty($broadcaster_id)){
	$broadcaster_res = $pdo->selectOne("SELECT brodcast_name,subject,mail_content FROM broadcaster WHERE md5(id) = :id", array(":id" => $broadcaster_id));
	if(!empty($broadcaster_res)){
		$brodcast_name = $broadcaster_res['brodcast_name'];
		$mail_content = $broadcaster_res['mail_content'];
		$subject = $broadcaster_res['subject'];
	}
}

$template = 'emailer_content.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>
