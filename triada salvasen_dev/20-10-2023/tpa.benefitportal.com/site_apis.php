<?php
include_once __DIR__ . '/includes/connect.php';
ini_set('max_execution_time',"-1");
ini_set('memory_limit','-1');
set_time_limit(0);

$API_USERNAME = "SITEAPIUser1";
$API_PASSWORD = "%N7MD254BM786vk";

$success_status = 200;
$success_value = true;

$fail_status = 200;
$fail_value = false;

if(!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])) {
	list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) = explode(':' , base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));	
}

function return_response($success,$response,$status = '')
{	
	//401 Unauthorized
	if(empty($status)) {
		$status = 200;
	}

	/*header("Content-Type:application/json");
	header("HTTP/1.1 ".$status);*/
	$json_response = json_encode($response);
	$json_response = htmlspecialchars_decode($json_response);
	echo $json_response;
	exit();
}

if(empty($_SERVER['PHP_AUTH_USER']) || empty($_SERVER['PHP_AUTH_PW'])) {
	$response = array(
		'success' => $fail_value,
		'message' => "User authentication failed"
	);
	return_response($fail_value,$response,401);
}

if($_SERVER['PHP_AUTH_USER'] != $API_USERNAME || $_SERVER['PHP_AUTH_PW'] != $API_PASSWORD) {
	$response = array(
		'success' => $fail_value,
		'message' => "User authentication failed"
	);
	return_response($fail_value,$response,401);	
}

$api_name = (isset($_REQUEST['api_name'])?$_REQUEST['api_name']:'');
if(empty($api_name)) {
	$response = array(
		'success' => $fail_value,
		'message' => "Your requested api not found"
	);
	return_response($fail_value,$response,405);	
}

if($api_name == "trigger_mail") {
	// Takes raw data from the request
	$param_json = file_get_contents("php://input");
	$param_data = json_decode($param_json,true);
	pre_print($param_data,false);
	$trigger_id = (isset($param_data['trigger_id'])?$param_data['trigger_id']:'');
	$mail_data = (isset($param_data['mail_data'])?$param_data['mail_data']:array());
	if(!empty($trigger_id) && !empty($mail_data)) {
		foreach ($mail_data as $data) {
			if(!empty($data['toEmail'])) {
				$trigger_params = (isset($data['params'])?$data['params']:array());
				trigger_mail($trigger_id,$trigger_params,$data['toEmail']);
			}
		}
		$response = array(
			'success' => $success_value,
			'message' => 'trigger sent success',
		);
		return_response($success_value,$response);
	} else {
		$response = array(
			'success' => $fail_value,
			'message' => "trigger_mail : required trigger_id and mail_data params"
		);
		return_response($fail_value,$response,405);
	}
}

