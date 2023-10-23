<?php
include_once __DIR__ . '/includes/connect.php';

$response = array();
$group_id = $_POST['group_id'];
$resource_id = $_POST['id'];

if(!empty($group_id) && !empty($resource_id)){
    $selGroup = "SELECT fname,lname,rep_id FROM customer where id=:id";
    $resGroup = $pdo->selectOne($selGroup,array(":id"=>$group_id));

	$selResource = "SELECT id,label FROM group_resource_link WHERE is_deleted='N' AND group_id = :group_id AND id=:resource_id";
    $resResource = $pdo->selectOne($selResource,array(":group_id"=>$group_id,":resource_id"=>$resource_id));

    if (!empty($resResource) && !empty($resGroup)) {
        $update_params = array(
            'is_deleted' => 'Y',
        );
        $update_where = array(
            'clause' => 'id = :id',
            'params' => array(
                ':id' => $resResource['id']
            )
        );
      
        $pdo->update("group_resource_link", $update_params, $update_where);

        $description['ac_message'] =array(
          'ac_red_1'=>array(
                'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
                'title'=>$_SESSION['admin']['display_id'],
            ),
          'ac_message_1' =>' Deleted Group Resource ',
          'ac_red_2'=>array(
            //'href'=> '',
            'title'=>$resResource['label'],
          ),
        ); 
        activity_feed(3, $group_id, 'Group', $resource_id, 'group_resource_link','Group Resource Deleted', $resGroup['fname'],$resGroup['lname'],json_encode($description));
        
        $response['status'] = 'success';
        $response['msg'] = 'Resource deleted successfully';
    }
}else{
	$response['status'] = 'fail';
	$response['msg'] = 'Something went wrong';
}

header('Content-Type: application/json');
echo json_encode($response);
dbConnectionClose();
exit();
?>