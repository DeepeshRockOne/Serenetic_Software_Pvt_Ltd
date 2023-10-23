<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$id = $_POST['id'];

$prd_sql = "SELECT id FROM prd_matrix WHERE id=:id";
$where_id = array(':id' => $id);
$prd_res = $pdo->selectOne($prd_sql, $where_id);

$res = array();
 
if (empty($prd_res)) {
  $res['status'] = 'fail';
  $res['msg'] = 'Price can not be deleted.';
  setNotifyError("Price can not be deleted.");
} else {
    $up_params = array(
        'is_deleted' => 'Y',
    );
    $up_where = array(
        'clause' => 'id=:id',
        'params' => array(
            ':id' => $id
        )
    );
    $pdo->update('prd_matrix', $up_params, $up_where);

  $res['status'] = 'success';
  $res['msg'] = 'Price deleted successfully';  
  setNotifySuccess("Price deleted successfully");

}

header('Content-Type:application/json');
echo json_encode($res);
dbConnectionClose();
exit;
?>