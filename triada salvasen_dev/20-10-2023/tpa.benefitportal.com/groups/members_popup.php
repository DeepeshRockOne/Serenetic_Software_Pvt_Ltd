<?php
include_once __DIR__ . '/includes/connect.php'; 
$group_classes_id = !empty($_GET['class']) ? $_GET['class'] : 0;

$resMembers = array();

	$sqlMembers = "SELECT md5(c.id) as id,c.fname,c.lname,c.rep_id FROM customer c
		JOIN customer_settings cs ON (cs.customer_id = c.id)
		WHERE md5(cs.class_id) = :group_classes_id AND c.type='Customer'";
	$resMembers = $pdo->select($sqlMembers,array(":group_classes_id"=>$group_classes_id));
	



$template = 'members_popup.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>
