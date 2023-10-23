<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$group_id = $_POST['group_id'];
$selGroup = "SELECT fname,lname,rep_id FROM customer where id=:id";
$resGroup = $pdo->selectOne($selGroup,array(":id"=>$group_id));

$query = "SELECT * FROM page_builder WHERE md5(id)=:id and is_deleted='N'";
$pb_row = $pdo->selectOne($query,array(':id' => $_POST['id']));
if(!empty($pb_row)) {
	$status = $_POST['status'];
	$up_params = array(
        'status' => $status,
        'updated_at' => 'mysqlfunc_NOW()'
    );
    $up_where = array(
        'clause' => 'id=:id',
        'params' => array(
            ':id' => $pb_row['id']
        )
    );
    $pdo->update('page_builder', $up_params, $up_where);

    $desc = array();
    $desc['ac_message'] = array(
        'ac_red_1'=>array(
            'href' => 'agent_detail_v1.php?id='.md5($_SESSION['agents']['id']),
            'title' => $_SESSION['agents']['rep_id'],
        ),
        'ac_message_1' => ' changed Website ',
        'ac_red_2' => array(
            'href' => 'page_builder.php?id='.md5($pb_row['id']),
            'href' => 'javascript:void(0);',
            'title' => $pb_row['page_name'],
        ),
        'ac_message_2' => ' publish status from '.($pb_row['status'] == "Active"?"Published":"Unpublished").' to '.($status == "Active"?"Published":"Unpublished"),
    );
    $desc = json_encode($desc);
    activity_feed(3, $group_id, 'Group', $pb_row['id'], 'page_builder', 'Website Publish Status Changed', $resGroup['fname'], $resGroup['lname'], $desc);
    
    $res['status'] = 'success';
	$res['msg'] = 'Website Publish Status Changed Successfully';
	echo json_encode($res);
	exit();
}


header('Content-Type: application/json');
echo json_encode($response);
dbConnectionClose();
exit();

?>