<?php
ini_set('memory_limit', '-1');
ini_set('max_execution_time',0);
include_once dirname(__DIR__) . "/cron_scripts/connect.php";
include_once dirname(__DIR__) . "/includes/aws_reporting_api_url.php";
require_once dirname(__DIR__) . '/libs/php_sftp_libs/Net/SFTP.php';
set_include_path(dirname(__DIR__) . '/libs/php_sftp_libs/');

$today = date('Y-m-d H:i:s',strtotime('+30 minutes'));
echo "Today Date ".$today;
echo "<br/>";
$req_res = $pdo->select("SELECT er.id,er.report_id,er.user_id,er.user_type,er.generate_via,er.email,er.is_manual,r.report_name,r.report_key,er.schedule_id
	FROM $REPORT_DB.export_requests er
	JOIN $REPORT_DB.rps_reports_schedule rs ON(rs.id=er.schedule_id AND rs.cancel_processing='N' AND rs.is_deleted='N')
	JOIN $REPORT_DB.rps_reports r ON(er.report_id=r.id AND r.is_deleted = 'N') 
	WHERE er.is_deleted='N' AND er.status='Pending' AND er.process_datetime <= '$today' ORDER BY er.process_datetime ASC");
//pre_print($req_res);
if(!empty($req_res) && is_array($req_res)) {
	foreach ($req_res as $key => $req_row) {
		//update file process status to Running
		$req_where = array(
			"clause"=>"id=:id",
			"params"=>array(
			  ":id"=>$req_row['id'],
			)
		);
		$req_data = array(
			'status' => "Running",
			'updated_at' => "msqlfunc_NOW()",
		);
		$pdo->update("$REPORT_DB.export_requests",$req_data,$req_where);
		
		$report_url = isset($AWS_REPORTING_URL[$req_row['report_key']])?$AWS_REPORTING_URL[$req_row['report_key']]:'';
		if(!empty($report_url)) {

			/*-----CURL CALL----*/
			$report_export_url = $report_url."&job_id=".$req_row['id'];
		    $ch = curl_init($report_export_url);
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
		    curl_setopt($ch, CURLOPT_POST, false);
		    $apiResponse = curl_exec($ch);
		    curl_close($ch);
		    if(is_string($apiResponse)) {
				$apiResponse = json_decode($apiResponse,true);
		    }
		    /*-----/CURL CALL ----*/
		}
	}// end foreach loop
}
echo "Completed";
dbConnectionClose();
//$DEFAULT_ORDER_EMAIL = array("shailesh@cyberxllc.com");
//trigger_mail_to_email("Export Report Request", $DEFAULT_ORDER_EMAIL,"Op29 : Export Report Request");