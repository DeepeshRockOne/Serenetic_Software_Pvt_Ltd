<?php
include_once __DIR__ . '/includes/connect.php';
require __DIR__  .'/libs/awsSDK/vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

$group_id = checkIsset($_GET['group_id']);
$result = [];
if(!empty($group_id)){
	$groupInfo = $pdo->selectOne("SELECT signature_file FROM customer_settings WHERE md5(customer_id)=:id",array(":id"=>$group_id));

	if(!empty($groupInfo['signature_file'])){
		$signature = $groupInfo['signature_file'];

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
				'Key'    => $SIGNATURE_FILE_PATH.$signature,
			]);

			header("Content-Type: {$result['ContentType']}");
			header("Content-Disposition: attachment; filename=" . $groupInfo['signature_file']);
			echo $result['Body']; 
			exit;
		} catch (S3Exception $e) {
			setNotifyError("Oops... Something went wrong please try again later");
		}
	}

}
?>