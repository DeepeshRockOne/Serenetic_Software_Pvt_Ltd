<?php
include_once __DIR__ . '/layout/start.inc.php';

$payment_master_id = $_GET['pay_id'];
$is_success = $_GET['is_success'];
$payment_master_res = $pdo->selectOne("SELECT name FROM payment_master WHERE md5(id) = :id", array(":id" => $payment_master_id));
$onClick = '';
if($is_success == 'Y'){
	$link = $HOST . '/images/enroll_success.svg';
	$text = 'Test Transaction Successful';
	$btn_id = 'btn_continue';
	$btn_text = 'Continue';
	$onClick = 'onclick="parent.$.colorbox.close();"';
} else {
	$link = $HOST . '/images/fail-icon.svg';
	$text = checkIsset($_GET['errorMessage']);
	$btn_id = 'btn_retry';
	$btn_text = 'Retry';
}

$template = 'success_processor_transaction.inc.php';
include_once 'layout/iframe.layout.php';
include_once 'tmpl/' . $template;
?>