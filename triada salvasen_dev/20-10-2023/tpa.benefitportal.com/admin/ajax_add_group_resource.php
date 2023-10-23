<?php
include_once __DIR__ . '/includes/connect.php';

$validate = new Validation();


$resource_id = !empty($_POST['resource_id']) ? $_POST['resource_id'] : 0;
$group_id = !empty($_POST['group_id']) ? $_POST['group_id'] : 0;

$label = $_POST['label'];
$url = $_POST['url'];



if($group_id != '' && $resource_id != ''){
	$selResource = "SELECT * FROM group_resource_link WHERE is_deleted='N' AND group_id=:group_id AND id=:resource_id";
   	$resResource = $pdo->selectOne($selResource,array(":group_id"=>$group_id,":resource_id"=>$resource_id));
}



$validate->string(array('required' => true, 'field' => 'label', 'value' => $label), array('required' => 'Label is required'));
$validate->string(array('required' => true, 'field' => 'url', 'value' => $url), array('required' => 'URL is required'));




if ($validate->isValid()) {

	$params = array(
		'group_id' => $group_id,
		'label' => $label,
		'url' => $url,
	);
	if(!empty($resResource)){
        $update_where = array(
            'clause' => 'id = :id',
            'params' => array(
                ':id' => makeSafe($resResource['id'])
            )
        );
        $pdo->update("group_resource_link", $params, $update_where);
        $response['msg'] = "Resource updated Successfully";
	}else{
		$pdo->insert('group_resource_link', $params);
		$response['msg'] = "Resource added Successfully";
	}
	$response['status'] = 'success';
} else {
	$response['status'] = 'fail';
}
if (count($validate->getErrors()) > 0) {
	$response['status'] = "fail";
	$response['errors'] = $validate->getErrors();
}

header('Content-Type: application/json');
echo json_encode($response);
dbConnectionClose();
exit();
?>