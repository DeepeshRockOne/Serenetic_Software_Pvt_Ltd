<?php
include_once dirname(__FILE__) . '/includes/connect.php';

$log_id = checkIsset($_GET['log_id']); 

$emailLogRes = array();

if(!empty($log_id)){
	$emailLogSql = "SELECT md5(id) as id,to_email,subject,message FROM email_log WHERE md5(id) = :id";
	$emailLogRes = $pdo->selectOne($emailLogSql,array(":id" => $log_id));

	
	$toEmail = !empty($emailLogRes['to_email']) ? $emailLogRes['to_email'] : '';
	$subject = !empty($emailLogRes['subject']) ? $emailLogRes['subject'] : '';
	$mailContent = !empty($emailLogRes['message']) ? $emailLogRes['message'] : '';
}

$template = 'send_email_content.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>
