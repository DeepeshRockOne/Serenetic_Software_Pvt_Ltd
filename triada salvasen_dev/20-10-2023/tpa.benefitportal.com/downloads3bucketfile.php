<?php
include_once __DIR__ . '/includes/connect.php';
include_once __DIR__ . '/includes/function.class.php';
$functionsList = new functionsList();

$location = !empty($_GET['location']) ? $_GET['location'] : '';
$user_id = !empty($_GET['user_id']) ? $_GET['user_id'] : '';

if(!empty($_GET['file_path']) && !empty($_GET['file_name'])) {
	$file_path = urldecode($_GET['file_path']);
    $file_name = urldecode($_GET['file_name']);

    $doesFileExist = $functionsList->doesFileExistFromS3Bucket($file_path,$file_name);

    if(!empty($doesFileExist)){
		$result = $functionsList->getAwsS3Bucket($file_path,$file_name);
		header("Content-Type: {$result['ContentType']}");
		header("Content-Disposition: attachment; filename=". $file_name);
		echo $result['Body']; 
		exit;
	} else {
		setNotifyError("File Not Found");
	}
} else {
	setNotifyError("Oops... Something went wrong please try again later");
}

if(!empty($location) && !empty($user_id)){
	if($location == 'admin_agent_details'){
		redirect($ADMIN_HOST.'/agent_detail_v1.php?id='.$user_id);
	} else if($location == 'agent_profile'){
		redirect($AGENT_HOST.'/profile.php');
	} else if($location == 'admin_profile_details'){
		redirect($ADMIN_HOST.'/admin_profile.php?id='.$user_id);
	} else if($location == 'admin_group_details'){
		redirect($ADMIN_HOST.'/groups_details.php?id='.$user_id);
	} else if($location == 'agent_group_details'){
		redirect($AGENT_HOST.'/groups_details.php?id='.$user_id);
	} else if($location == 'group_profile'){
		redirect($GROUP_HOST.'/profile.php');
	}
}
?>
