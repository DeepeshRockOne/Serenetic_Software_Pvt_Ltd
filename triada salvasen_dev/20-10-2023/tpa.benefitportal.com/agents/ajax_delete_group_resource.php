<?php
include_once __DIR__ . '/includes/connect.php';
$response = array();
$group_id = $_POST['group_id'];
$resource_id = $_POST['id'];

if(!empty($group_id) && !empty($resource_id)){
    $selGroup = "SELECT fname,lname,rep_id,id FROM customer where id=:id";
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

        $description = array();
        $description['ac_message'] = array(
            'ac_red_1'=>array(
                'href' => 'agent_detail_v1.php?id='.md5($_SESSION['agents']['id']),
                'title' => $_SESSION['agents']['rep_id'],
            ),
            'ac_message_1' =>'  deleted resource link In Group '.($resGroup['fname'].' '.$resGroup['lname']).' (',
            'ac_red_2'=>array(
                'href'=> $ADMIN_HOST.'/groups_details.php?id='.md5($resGroup['id']),
                'title'=> $resGroup['rep_id'],
            ),
            'ac_message_2' =>') Resource link : ',
            'ac_red_3'=>array(
                'title'=> $resResource['label'],
            ),
        );
        activity_feed(3,$_SESSION['agents']['id'],'Agent',$group_id,'Group','Agent Deleted Group Resource Link','','',json_encode($description));
        
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