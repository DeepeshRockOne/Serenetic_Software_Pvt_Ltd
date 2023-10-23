<?php
include_once __DIR__ . '/includes/connect.php'; 
$lead_agreement = '';
$select = "SELECT * FROM `app_settings` WHERE setting_key='lead_agreement'";
$row = $pdo->selectOne($select);
if(!empty($row)) {
	$lead_agreement = $row['setting_value'];
}

$template = 'lead_agreement.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';

?>