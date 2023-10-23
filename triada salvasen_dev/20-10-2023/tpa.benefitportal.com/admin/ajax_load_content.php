<?php
include_once 'layout/start.inc.php';

$product_id = isset($_POST['product_id']) ? $_POST['product_id'] : "";

$response = array();
if($product_id){

	$sqlDesc = "SELECT enrollment_desc,agent_portal,agent_info,limitations_exclusions FROM prd_descriptions where md5(product_id) = :product_id";
			$resDesc = $pdo->selectOne($sqlDesc,array(":product_id"=>$product_id));

	if(!empty($resDesc)){
		$response['data']['enrollmentPage'] = $resDesc['enrollment_desc'];
		$response['data']['agent_portal'] = $resDesc['agent_portal'];
		// $agent_info = !empty($resDesc['agent_info']) ? explode(",", $resDesc['agent_info']) : '';
		$response['data']['limitations_exclusions'] = $resDesc['limitations_exclusions'];
		$response['status'] = 'success';
	}

	$sqlDepartment="SELECT id,name,description FROM prd_member_portal_information where md5(product_id) = :product_id AND is_deleted='N'";
	$resDepartment=$pdo->select($sqlDepartment,array(":product_id"=>$product_id));
	if($resDepartment){
		foreach ($resDepartment as $key => $value) {
			$response['data']['department_desc_'.$value['id']] = $value['description'];
			$response['status'] = 'success';
		}
	}

}else{
	$response['status'] = 'fail';
}

echo json_encode($response);
dbConnectionClose();
exit();