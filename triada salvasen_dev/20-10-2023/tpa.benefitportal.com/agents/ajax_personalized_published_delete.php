<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$id=$_POST['id'];
$res = array();

$query = "SELECT id FROM page_builder WHERE is_deleted='N' AND id =" . $id;
$srow = $pdo->selectOne($query);

if ($srow) {
  $update_params = array(
      'is_deleted' => 'Y'
  );
  $update_where = array(
      'clause' => 'id = :id',
      'params' => array(
          ':id' => $id
      )
  );
  
  $pdo->update("page_builder", $update_params, $update_where);
  
  
  $res['status'] = 'success';
  $res['msg'] = 'Page deleted Successfully';

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

