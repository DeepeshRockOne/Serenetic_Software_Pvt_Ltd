<?php 
include_once dirname(__FILE__) . '/layout/start.inc.php';

if (isset($_GET['id'])) {
	$id = $_GET['id'];

	$update_params = array(
		'invite_at' => 'msqlfunc_NOW()',
	);

	$update_where = array(
		'clause' => 'md5(id) = :id',
		'params' => array(
			':id' => makeSafe($id),
		),
	);
	$update_status = $pdo->update('customer', $update_params, $update_where);
  	$result['status'] = "success";
} else {
	$result['status'] = "fail";
}

header('Content-type: application/json');
echo json_encode($result);
dbConnectionClose(); 
exit;	
?>