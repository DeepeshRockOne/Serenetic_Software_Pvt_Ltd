<?php
include_once dirname(__DIR__) . "/cron_scripts/connect.php";
require_once dirname(__DIR__) . '/includes/function.class.php';
require dirname(__DIR__) .'/libs/awsSDK/vendor/autoload.php';

$function_list = new functionsList();

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

$s3 = new S3Client([
	'version' => 'latest',
	'region'  => $S3_REGION,
	'credentials'=>array(
	    'key'=> $S3_KEY,
		'secret'=> $S3_SECRET
	)
]);

$sel_sql = "SELECT er.* FROM eligibility_requests er
				JOIN eligibility_files ef ON(er.file_id = ef.id)
			";
$req_res = $pdo->select($sel_sql);

if(!empty($req_res)){
	foreach($req_res as $request){

		$processed_file = $request['processed_file_name'];
		if(!empty($processed_file)){
			if($function_list->doesFileExistFromS3Bucket("",$processed_file)){
				$copy_processed_file = $s3->copyObject([
					'Bucket' => $S3_BUCKET_NAME,
					'Key' => $ELIGIBILITY_FILES_PATH.$processed_file,
					'CopySource' => $S3_BUCKET_NAME."/".$processed_file
				]);

				if(!empty($copy_processed_file)){
					$s3->deleteObject([
						'Bucket' => $S3_BUCKET_NAME,
						'Key' => $processed_file
					]);
				}
			}
		}

		if(!empty($request['files_name'])){
			$attempt_files = explode(',',$request['files_name']);
			unset($attempt_files[0]);
			foreach($attempt_files as $attempts){
				if($function_list->doesFileExistFromS3Bucket("",$attempts)){
					$copy_file = $s3->copyObject([
						'Bucket' => $S3_BUCKET_NAME,
						'Key' => $ELIGIBILITY_FILES_PATH.$attempts,
						'CopySource' => $S3_BUCKET_NAME."/".$attempts
 					]);
 					
 					if(!empty($copy_file)){
 						$s3->deleteObject([
							'Bucket' => $S3_BUCKET_NAME,
							'Key' => $attempts
						]);
 					}
				}
			}
		}
		
	}
}
echo "Completed";
dbConnectionClose();
exit;
?>