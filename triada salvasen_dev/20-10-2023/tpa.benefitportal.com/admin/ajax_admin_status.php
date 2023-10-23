<?php  
include_once dirname(__FILE__) . '/layout/start.inc.php';

$res = array();

$res['status'] = 'success';


header('Content-Type: application/json');
echo json_encode($res);
exit;
?>