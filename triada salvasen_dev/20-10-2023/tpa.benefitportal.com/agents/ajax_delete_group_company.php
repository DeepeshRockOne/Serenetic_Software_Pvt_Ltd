<?php
include_once __DIR__ . '/includes/connect.php';

$response = array();
$group_id = $_POST['group_id'];
$company_id = $_POST['id'];

if(!empty($group_id) && !empty($company_id)){
    $selGroup = "SELECT fname,lname,rep_id FROM customer where id=:id";
    $resGroup = $pdo->selectOne($selGroup,array(":id"=>$group_id));

	$selCompany = "SELECT id,name FROM group_company WHERE is_deleted='N' AND group_id = :group_id AND id=:company_id";
    $resCompany = $pdo->selectOne($selCompany,array(":group_id"=>$group_id,":company_id"=>$company_id));

    if (!empty($resCompany) && !empty($resGroup)) {
        $update_params = array(
            'is_deleted' => 'Y',
        );
        $update_where = array(
            'clause' => 'id = :id',
            'params' => array(
                ':id' => $resCompany['id']
            )
        );
      
        $pdo->update("group_company", $update_params, $update_where);

        $description['ac_message'] =array(
            'ac_red_1'=>array(
                'href' => 'agent_detail_v1.php?id='.md5($_SESSION['agents']['id']),
                'title' => $_SESSION['agents']['rep_id'],
            ),
            'ac_message_1' =>' Deleted Group Company ',
            'ac_red_2'=>array(
                'title'=>$resCompany['name'],
            ),
        ); 
        activity_feed(3, $group_id, 'Group', $company_id, 'group_company','Agent Deleted Group Company', $resGroup['fname'],$resGroup['lname'],json_encode($description));
        
        $response['status'] = 'success';
        $response['msg'] = 'Company deleted successfully';
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