<?php
include_once __DIR__ . '/includes/connect.php';
ini_set('memory_limit', '-1');
$id = isset($_REQUEST['id'])?$_REQUEST['id']:'-1';
$fId = isset($_REQUEST['fId'])?$_REQUEST['fId']:'-1';
$is_ajax = isset($_POST['is_ajax']) ? $_POST['is_ajax'] : "";
$password = isset($_POST['password']) ? $_POST['password'] : "";

$sel_file = "SELECT *,AES_DECRYPT(password,'" . $CREDIT_CARD_ENC_KEY . "') as file_passord FROM $REPORT_DB.export_requests WHERE md5(id)=:id AND md5(report_id)=:report_id";
$sel_params = array(":id" => $id,":report_id" => $fId);
$res_file = $pdo->selectOne($sel_file,$sel_params);
$ELIGIBILITY_FILES_PATH = $UPLOAD_WEB.'/uploads/eligibility_files/';
$eligibility_file_uploads = '';
$user_name = "";
if(!empty($res_file)){
	if($res_file['user_type'] == "Admin") {
		$user_row = $pdo->selectOne("SELECT display_id,CONCAT(fname,' ',lname) as user_name FROM admin WHERE id=:id",array(':id' => $res_file['user_id']));
		$user_name = $user_row['user_name'];
	} else {
		$user_row = $pdo->selectOne("SELECT rep_id,CONCAT(fname,' ',lname) as user_name FROM customer WHERE id=:id",array(':id' => $res_file['user_id']));
		$user_name = $user_row['user_name'];	
	}
	$req_where = array(
		"clause"=>"id=:id",
		"params"=>array(
		  ":id"=>$res_file['id'],
		)
	);
	$req_data = array(
		'is_read_email' => "Y",
	);
	$pdo->update("$REPORT_DB.export_requests",$req_data,$req_where);
}

if($is_ajax) {
	if($password != ""){
		$response = array();
		if($res_file['file_passord'] == $password){
			$response['status'] = "success";
		} else {
			$response['status'] = 'error';
			$response['error'] = 'Password not match';
		}
	} else {
		$response['error'] = "Please enter password";
		$response['status'] = 'error';
	}
	echo json_encode($response);
	exit;
}
$layout = 'iframe.layout.php';
$template = 'report_access.inc.php';
include_once 'layout/end.inc.php';

?>
