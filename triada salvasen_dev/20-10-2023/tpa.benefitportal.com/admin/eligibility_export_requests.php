<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
require dirname(__DIR__) .'/libs/awsSDK/vendor/autoload.php';
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

$id = isset($_GET['id']) ? $_GET['id'] : "";
$action = isset($_GET['action']) ? $_GET['action'] : "";

if($id && $action == 'Delete'){
	$updWhere = array(
        'clause' => 'id = :id',
        'params' => array(
          ':id' => $id
        )
      );
      $update_status = $pdo->update('eligibility_requests', array('is_deleted' => 'Y'), $updWhere);
    echo json_encode(array('status' => 'success','message' => 'Request remove successfully'));
    exit();  
}
if(isset($_GET['is_download']) && isset($_GET['file_name']) && $_GET['file_name']!=''){
	$keyname = $ELIGIBILITY_FILES_PATH.rawurldecode($_GET['file_name']);
	$location = !empty($_GET['location']) ? $_GET['location'] : '';
	$file_id = !empty($_GET['file_id']) ? $_GET['file_id'] : '';

	$s3 = new S3Client([
		'version' => 'latest',
		'region'  => $S3_REGION,
		'credentials'=>array(
            'key'=> $S3_KEY,
			'secret'=> $S3_SECRET
		)
	]);
	$response = $s3->doesObjectExist($S3_BUCKET_NAME,$keyname);

	if(!empty($response)){
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
			redirect('eligibility_export_requests.php');
		}
	} else {
		if(!empty($location) && $location == 'eligibility_processed_file'){
			setNotifyError('File not found!');
			redirect('eligibility_processed_file.php?id='.$file_id);
		} else {
			setNotifyError('There is no data found in file!');
			redirect('eligibility_export_requests.php');
		}
	}
}

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Eligibility';
$breadcrumbes[2]['title'] = 'History';
$breadcrumbes[2]['link'] = 'eligibility_history.php';
$breadcrumbes[3]['title'] = 'Export Requests';
$breadcrumbes[3]['link'] = 'eligibility_export_requests.php';


$template = 'eligibility_export_requests.inc.php';
include_once 'layout/end.inc.php';
?>
