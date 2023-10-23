<?php
include_once __DIR__ . '/includes/connect.php';
include_once __DIR__ .'/libs/awsSDK/vendor/autoload.php';
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
if(!empty($_GET['file_name'])) {
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
		$result = $s3->getObject([
			'Bucket' => $S3_BUCKET_NAME,
			'Key'    => $keyname,
		]);
		header("Content-Type: {$result['ContentType']}");
		header("Content-Disposition: attachment; filename=" . $_GET['file_name']);
		echo $result['Body']; exit;
		
	} catch (S3Exception $e) {
		setNotifyError("File Not Found");
		redirect('report_access.php');
	}
} else {
	setNotifyError("File Not Found");
	redirect('report_access.php');
}
	
?>
