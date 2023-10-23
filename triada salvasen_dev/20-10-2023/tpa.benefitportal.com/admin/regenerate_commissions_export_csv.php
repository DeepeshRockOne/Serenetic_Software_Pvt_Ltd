<?php

include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once dirname(__DIR__) . '/includes/aws_reporting_api_url.php';
$res = array();
$regenerateCommId = checkIsset($_REQUEST['regenerateCommId']);

if(empty($regenerateCommId)) {
	$res["status"] = "fail";
	$res["message"] = "Commissions not found";
	echo json_encode($res);
	exit;
}

$incr = "";
$sch_params = array();

$extraParams = array();
$extraParams["regenerateCommId"] = $regenerateCommId;


$job_id = add_export_request_api('EXCEL',$_SESSION['admin']['id'],'Admin',"Regenerated Commission Report","commission_export",$incr,$sch_params,$extraParams,'commission_export');
$reportDownloadURL = $AWS_REPORTING_URL['commission_export']."&job_id=".$job_id;

$ch = curl_init($reportDownloadURL);
curl_setopt($ch, CURLOPT_TIMEOUT,1);//Timeout set to 1 Sec
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLINFO_HEADER_OUT, true);
curl_setopt($ch, CURLOPT_POST, false);
$apiResponse = curl_exec($ch);
curl_close($ch);

echo json_encode(array("status"=>"success","message"=>"Your export request is added","reportDownloadURL" => $reportDownloadURL)); 
exit;
?>