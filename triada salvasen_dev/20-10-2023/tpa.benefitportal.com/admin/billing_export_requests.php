<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
require dirname(__DIR__) .'/libs/awsSDK/vendor/autoload.php';
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

$id = isset($_GET['id']) ? $_GET['id'] : "";
$action = isset($_GET['action']) ? $_GET['action'] : "";

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
		redirect('billing_export_requests.php');
	}
}

if($id && $action == 'Delete'){
	$updWhere = array(
        'clause' => 'id = :id',
        'params' => array(
          ':id' => $id
        )
      );
      $update_status = $pdo->update('billing_requests', array('is_deleted' => 'Y'), $updWhere);
    echo json_encode(array('status' => 'success','message' => 'Request remove successfully'));
    exit();  
}

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Billing';
$breadcrumbes[1]['link'] = 'billing_files.php';
$breadcrumbes[2]['title'] = 'Export Requests';
$breadcrumbes[2]['link'] = 'billing_export_requests.php';
$template = 'billing_export_requests.inc.php';
include_once 'layout/end.inc.php';
?>
