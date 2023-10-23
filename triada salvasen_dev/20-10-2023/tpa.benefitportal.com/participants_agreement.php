<?php
include_once __DIR__ . '/includes/connect.php'; 
$participants_agreement = '';
$select = "SELECT * FROM `app_settings` WHERE setting_key='participants_agreement'";
$row = $pdo->selectOne($select);
if(!empty($row)) {
	$participants_agreement = $row['setting_value'];
}

$template = 'participants_agreement.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';

?>