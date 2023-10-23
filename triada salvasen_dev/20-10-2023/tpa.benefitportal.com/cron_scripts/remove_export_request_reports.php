<?php 
include_once dirname(__DIR__) .'/includes/connect.php';
include_once dirname(__DIR__) . '/includes/aws_reporting_api_url.php';

$sqlReports="SELECT id,filename,created_at FROM $REPORT_DB.export_requests WHERE created_at < (NOW() - INTERVAL 8 DAY) ORDER BY id DESC";
$resReports=$pdo->select($sqlReports);

$deleteReportID = array();
$extra_export_arr = array();

if(!empty($resReports)){
	foreach ($resReports as $key => $rows) {
		$id=$rows['id'];
		array_push($deleteReportID,$id);
	}
}

if(!empty($deleteReportID)){
	$extra_export_arr['id']=$deleteReportID;
	$job_id=add_export_request_api('EXCEL',1,'System',"Delete Report","deleteFile",'', array(),json_encode($extra_export_arr));


	$reportDownloadURL = $AWS_REPORTING_URL['deleteFile']."&job_id=".$job_id;

	$ch = curl_init($reportDownloadURL);
	curl_setopt($ch, CURLOPT_TIMEOUT, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLINFO_HEADER_OUT, true);
	curl_setopt($ch, CURLOPT_POST, false);
	curl_exec($ch);
	$apiResponse = curl_exec($ch);
	curl_close($ch);
}
?>