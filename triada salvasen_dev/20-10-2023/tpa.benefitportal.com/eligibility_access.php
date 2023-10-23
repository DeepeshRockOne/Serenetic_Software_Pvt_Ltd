<?php
include_once __DIR__ . '/includes/connect.php';
ini_set('memory_limit', '-1');
$id = $_REQUEST['id'];
$fId = $_REQUEST['fId'];
$is_ajax = isset($_POST['is_ajax']) ? $_POST['is_ajax'] : "";
$password = isset($_POST['password']) ? $_POST['password'] : "";

// pre_print($_REQUEST);

$sel_file = "SELECT *,AES_DECRYPT(password,'" . $CREDIT_CARD_ENC_KEY . "') as file_passord FROM eligibility_requests WHERE md5(id)=:id AND md5(file_id)=:file_id";
$sel_params = array(":id" => $id,":file_id" => $fId);
$res_file = $pdo->selectOne($sel_file,$sel_params);
$ELIGIBILITY_FILES_PATH = $UPLOAD_WEB.'/uploads/eligibility_files/';
$eligibility_file_uploads = '';
$admin_name = "";
if($res_file){
		$admin_data = $pdo->selectOne("SELECT display_id,CONCAT(fname,' ',lname) as admin_name FROM admin WHERE id = :id",array(':id' => $res_file['user_id']));
		$admin_name = $admin_data['admin_name'];

		$req_where = array(
				"clause"=>"id=:id",
				"params"=>array(
				  ":id"=>$res_file['id'],
				)
			);
			$req_data = array(
				'is_read_email' => "Y",
			);
			$pdo->update("eligibility_requests",$req_data,$req_where);
}else{
	setNotifyError("Invalid Link");
	// redirect($HOST);
}

if($is_ajax){
	if($password != ""){
		$response = array();
		if($res_file['file_passord'] == $password){
			$response['status'] = "success";
		}else{
			$response['status'] = 'error';
			$response['error'] = 'Password not match';
		}
	}else{
		$response['error'] = "Please enter password";
		$response['status'] = 'error';
	}

	echo json_encode($response);
	exit;
}


$layout = 'iframe.layout.php';
$template = 'eligibility_access.inc.php';
include_once 'layout/end.inc.php';

?>
