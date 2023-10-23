<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$id = isset($_POST['ce_id']) ? $_POST['ce_id'] : "";
$response = array();
if($id){
	$ce_row = $pdo->selectOne("SELECT * from customer_enrollment where id = :id",array(':id' => $id));

	if($ce_row){
		$updParam = array('old_coverage_file' => '');
			$updWhere = array(
			'clause' => 'id = :id',
			'params' => array(':id' => $id)
			);
			$pdo->update('customer_enrollment', $updParam, $updWhere);

			$response['status'] = 'success';
			$response['message'] = 'Document deleted';
			$response['id'] = $id;
	}else{
		$response['status'] = 'fail';
		$response['message'] = 'File not Found';
	}
	
}else{
	$response['status'] = 'fail';
	$response['message'] = 'File not Found';
}
echo json_encode($response);
dbConnectionClose();
exit();

?>