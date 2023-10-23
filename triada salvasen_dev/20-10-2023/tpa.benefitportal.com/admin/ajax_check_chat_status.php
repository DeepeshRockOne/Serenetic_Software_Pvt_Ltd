<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$response = array();
$response['status'] = "success";	

header('Content-type: application/json');
echo json_encode($response);
exit;

?>