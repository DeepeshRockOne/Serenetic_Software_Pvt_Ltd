<?php
include_once __DIR__ . '/includes/connect.php';
include_once __DIR__ . '/includes/Api.class.php';

if(!empty($_POST['api_key'])){
	$ajaxApiCall = new Api();
	$apiResponse = $ajaxApiCall->ajaxApiCall($_POST,true);
	if($apiResponse['status'] == 'Success'){
		$responseData = $apiResponse['data'];
		if(!empty($responseData) && !empty($responseData['triggerDetails'])){
			$ajaxApiCall->sendTriggerMail($_POST['api_key'],$responseData['triggerDetails']);
		}
	}
}else{
	$response['status'] = 'fail';
	$response['message'] = "Something Went Wrong";
	$apiResponse = $response;
}
header('Content-Type: application/json');
echo json_encode($apiResponse);
dbConnectionClose();
exit;
?>