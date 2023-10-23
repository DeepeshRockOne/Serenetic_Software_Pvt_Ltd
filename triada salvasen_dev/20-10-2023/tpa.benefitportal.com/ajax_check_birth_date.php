<?php
include_once 'includes/connect.php';

$response = array();

$birth_date = $_POST['dob'];
$customer_id = $_POST['id'];
$lead_id = $_POST['lead_id'];
$lead_dis_id = $_POST['lead_dis_id'];
$REQ_URL = $_SERVER['HTTP_REFERER'];
if(!empty($customer_id)){
	if(!empty($birth_date)){
		$customer_res = $pdo->selectOne("SELECT birth_date FROM customer WHERE id = :id and is_deleted='N'", array(':id' => $customer_id));
		if(!empty($customer_res)){
			$db_birth_date = date('m/d/Y', strtotime($customer_res['birth_date']));
			if($db_birth_date == $birth_date){

				$activity_description['ac_message'] = array(
					'ac_red_1'=>array(
							'href'=>$ADMIN_HOST.'/lead_details.php?id='.md5($lead_id),
							'title'=>$lead_dis_id,
					),
					'ac_message_1' => 'read enrollment verification',
				);

				activity_feed(3, $lead_id, 'Lead', $lead_id, 'Lead', 'Read Enrollment Verification', '', '', json_encode($activity_description), $REQ_URL);

                $response['status'] = 'success';
                $response['redirect_url'] = 'enrollment_verification.php';
			} else {
				$response['status'] = 'fail';
				$response['message'] = 'Valid Date of Birth is required';	
			}
		} else {
			$response['status'] = 'fail_access';
			$response['message'] = 'Something is worng. Please try later';
		}
	} else {
		$response['status'] = 'fail';
		$response['message'] = 'Date of Birth is required';	
	}
} else {
	$response['status'] = 'fail_access';
	$response['message'] = 'Something is worng. Please try later';
}



header('Content-type: application/json');
echo json_encode($response);
dbConnectionClose();
exit;
?>