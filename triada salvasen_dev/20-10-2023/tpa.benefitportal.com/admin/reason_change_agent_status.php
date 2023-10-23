<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
has_access(5);
$downline = checkIsset($_GET['downline']);
$loa = checkIsset($_GET['loa']);
$from = checkIsset($_GET['from']);
if(isset($_POST['action']) && $_POST['action'] == "OldStatus"){
 	$response = array();

	$sql = "SELECT status FROM customer WHERE id ='".$_POST['customer_id']."'";
	$res = $pdo->selectOne($sql);
	if (!empty($res)) {	
		$response['status'] = "success";
		$response['member_status'] = $res['status'];
	}
	header('Content-Type: application/json');
	echo json_encode($response);
	exit();
}

$exStylesheets = array("thirdparty/jquery_ui/css/front/jquery-ui-1.9.2.custom.css", 'thirdparty/multiple-select-master/multiple-select.css');
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js','thirdparty/jquery_ui/js/jquery-ui-1.9.2.custom.js');

$template = 'reason_change_agent_status.inc.php';
include_once 'layout/iframe.layout.php';
?>