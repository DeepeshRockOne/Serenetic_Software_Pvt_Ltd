<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$status = $_REQUEST['status'];
$id=$_POST['id'];
$res = array();

$query = "SELECT id, status FROM page_builder WHERE id =" . $id;
$srow = $pdo->selectOne($query);

if ($srow) {
  $update_params = array(
      'status' => $status
  );
  $update_where = array(
      'clause' => 'id = :id',
      'params' => array(
          ':id' => $id
      )
  );
  
  $pdo->update("page_builder", $update_params, $update_where);
  
  
  $res['status'] = 'success';
  $res['msg'] = 'Published Status Changed Successfully';
  setNotifySuccess('Published Status Changed Successfully');

} else {
  setNotifyError('Something went wrong');
  $res['status'] = 'error';
  $res['msg'] = 'Something went wrong';
}

header('Content-type: application/json');
echo json_encode($res);
dbConnectionClose();
exit;
?>

