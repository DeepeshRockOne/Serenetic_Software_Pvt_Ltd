<?php
include dirname(__DIR__) . "/includes/connect.php";
include_once "function_api.php";
ini_set('max_execution_time',"-1");
ini_set('memory_limit','-1');
set_time_limit(0);

$selCred = "SELECT * FROM api_info";
$resCred = $pdo->selectOne($selCred);

$API_USERNAME = trim(checkIsset($resCred["username"]));
$API_PASSWORD = trim(checkIsset($resCred["password"]));
$IP_ADDRESS_ARR = !empty($resCred["ip_address"]) ? explode(",", $resCred["ip_address"]) : array();

$success_status = 200;
$success_value = true;

$fail_status = 200;
$fail_value = false;

$url_request = explode('/', $_SERVER['REQUEST_URI']);

if ($SITE_ENV=='Local') {
	$url_segment = 2;
} else {
	$url_segment = 1;
}

$requested_api = trim($url_request[$url_segment + 1]);
$param1 = isset($url_request[$url_segment + 2]) ? $url_request[$url_segment + 2] : "";
$param2 = isset($url_request[$url_segment + 3]) ? $url_request[$url_segment + 3] : "";
$param3 = isset($url_request[$url_segment + 4]) ? $url_request[$url_segment + 4] : "";

if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
  	$_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
}


if ($SITE_ENV!='Local') {
	if(!in_array($_SERVER['REMOTE_ADDR'],$IP_ADDRESS_ARR)) {
	  	$response = array(
			'success' => $fail_value,
			'message' => "Access denied"
		);
		return_response($fail_value,$response,401);
	}
}


if(!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])) {
	list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) = explode(':' , base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));	
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

/**
 *
 *
 */
function return_response($success,$response,$status = '')
{	
	global $pdo,$requested_api;

	//401 Unauthorized
	if(empty($status)) {
		$status = 200;
	}

	$insParams = array(
		'api' => $requested_api,
		'api_success' => $success,
		'api_message' => !empty($response['message'])?$response['message']:'',
		'api_url' => ($_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]),
		'ip_address' => $_SERVER['REMOTE_ADDR'],
		'created_at' => date('Y-m-d H:i:s'),
	);

	$insId = $pdo->insert('api_requests',$insParams);


	header("Content-Type:application/json");
	header("HTTP/1.1 ".$status);	
	$json_response = json_encode($response);
	$json_response = htmlspecialchars_decode($json_response);
	echo $json_response;
	exit();
}