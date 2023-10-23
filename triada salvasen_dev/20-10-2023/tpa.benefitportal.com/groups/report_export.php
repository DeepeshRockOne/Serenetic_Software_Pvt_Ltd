<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
require dirname(__DIR__) .'/libs/awsSDK/vendor/autoload.php';
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Reporting';
$breadcrumbes[1]['link'] = 'reporting.php';
$breadcrumbes[2]['title'] = 'Export Requests';

if(isset($_GET['is_download']) && isset($_GET['file_name']) && $_GET['file_name']!=''){
	$keyname = urldecode($_GET['file_name']);
	
	$s3 = new S3Client([
		'version' => 'latest',
		'region'  => $S3_REGION,
		'credentials'=>array(
            'key'=> $S3_KEY,
			'secret'=> $S3_SECRET
		)
	]);
	
	try {
		// Get the object.
		$result = $s3->getObject([
			'Bucket' => $S3_BUCKET_NAME,
			'Key'    => $keyname,
		]);

		header("Content-Type: {$result['ContentType']}");
		header("Content-Disposition: attachment; filename=" . $_GET['file_name']);
		echo $result['Body']; exit;
	} catch (S3Exception $e) {
		redirect('report_export.php');
	}
}

if(isset($_GET['action']) && $_GET['action'] == "Delete"){
	$request_id = $_GET['id'];
	$request_sql = "SELECT * FROM $REPORT_DB.export_requests WHERE id=:id";
	$request_where = array(':id' => $request_id);
	$request_row = $pdo->selectOne($request_sql,$request_where);

	$up_params = array(
	    'is_deleted' => 'Y',
	    'updated_at' => 'mysqlfunc_NOW()'
	);
	$up_where = array(
	    'clause' => 'id=:id',
	    'params' => array(
	        ':id' => $request_id
	    )
	);
	$pdo->update("$REPORT_DB.export_requests", $up_params, $up_where);
	$res = array();
	$res['status'] = 'success';
	$res['message'] = 'Export Request Deleted Successfully';
	echo json_encode($res);
	exit;
}

if(isset($_GET['action']) && $_GET['action'] == "Reload"){
	$request_id = $_GET['id'];
	$request_sql = "SELECT er.*,rr.report_key,rr.id as reporId FROM $REPORT_DB.export_requests er LEFT JOIN $REPORT_DB.rps_reports rr ON(rr.id=er.report_id) WHERE er.id=:id AND status='Running'";
	$request_where = array(':id' => $request_id);
	$request_row = $pdo->selectOne($request_sql,$request_where);
	$res = array();
	if(!empty($request_row['id'])){
		$update_where = array(
			'clause' => 'id=:id',
			'params' => array(
				':id' => $request_row['id']
			)
		);
		$pdo->update("$REPORT_DB.export_requests",array("is_reprocess"=>'Y','reprocess_at'=>"msqlfunc_NOW()"),$update_where);
		$reportKey = !empty($request_row['reporId']) ? $request_row['report_key'] : $request_row['export_location'];

		if(!empty($reportKey)){
			include_once dirname(__DIR__) . '/includes/aws_reporting_api_url.php';
			$reportDownloadURL = $AWS_REPORTING_URL[$reportKey]."&job_id=".$request_row['id'];
			$ch = curl_init($reportDownloadURL);
			curl_setopt($ch, CURLOPT_TIMEOUT, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLINFO_HEADER_OUT, true);
			curl_setopt($ch, CURLOPT_POST, false);
			curl_exec($ch);
			$apiResponse = curl_exec($ch);
			curl_close($ch);
			
			$res['status'] = 'success';
			$res['message'] = 'Export Request Reloaded Successfully';
		}else{
			$res['status'] = 'fail';
			$res['message'] = 'Something went wrong.';
		}
		
	}
	echo json_encode($res);
	exit;
}

//Merge CSV Code for Local,Dev And Stag Only.
if(isset($_POST['action']) && $_POST['action'] == "mergeCsv" && $SITE_ENV!='Live'){
	$id = isset($_POST['id']) ? $_POST['id'] : '';
	$res = array();
	if(!empty($id)){
		$reportMergeURL = $HOST.'/cron_scripts/merge_csv.php?id='.$id;
		$ch = curl_init($reportMergeURL);
		curl_setopt($ch, CURLOPT_TIMEOUT, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);
		curl_setopt($ch, CURLOPT_POST, false);
		$api_response = curl_exec($ch);
		curl_close($ch);
		$res['status'] = 'success';
		$res['message'] = 'Merge Request Added Successfully';
	}
	echo json_encode($res);
	exit;
}

$template = 'report_export.inc.php';
include_once 'layout/end.inc.php';
?>
