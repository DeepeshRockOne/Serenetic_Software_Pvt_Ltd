<?php
include_once 'layout/start.inc.php';
$response = array();
$id = $_POST['id'];
$name = $_POST['name'];

$assigned_admin_row = $pdo->selectOne("SELECT id FROM admin WHERE type=:type AND is_deleted='N'",array(":type"=>$name));
if(!empty($assigned_admin_row)) {
    $response['status'] = 'need_reassign_admin';
} else {
    $lv_name = $pdo->selectOne("SELECT name,id FROM access_level where md5(id) = :id",array(":id"=>$id));
    $delTrgSql = "DELETE FROM access_level WHERE md5(id)=:id ";
    $params = array(
        ':id' => $id,
    );
    $pdo->delete($delTrgSql, $params);

    $activity_upd_params = array(
        'is_deleted' => 'Y',
        'changed_at' => 'msqlfunc_NOW()'
    );

    $activity_update_where = array(
        'clause' => 'entity_id=:entity_id AND entity_type=:entity_type',
        'params' => array(
            ':entity_id' => makeSafe($id),
            ':entity_type' => 'access_level'
        )
    );
    $extra['user_display_id'] = $_SESSION['admin']['display_id'];
    $extra['access_lvl_name'] = $lv_name['name'];

    $description = array();
    $description['description'] = $_SESSION['admin']['display_id'].' deleted admin access level '.$lv_name['name'];
    activity_feed(3, $_SESSION['admin']['id'],'Admin', $lv_name['id'], 'access_level','Access Level Deleted', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description),'',json_encode($extra));
    $response['status'] = 'deleted';
}

header('Content-Type: application/json');
echo json_encode($response);
dbConnectionClose();
exit;
?>