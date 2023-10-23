<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
has_access(2);
$agent_id = $_POST["agent_id"];
$status = $_POST['status'];
$pid=$_POST['pid'];

$res = [];

$query = "SELECT id, status FROM agent_product_rule WHERE id =:pid";
$srow = $pdo->selectOne($query,array(":pid"=>$pid));
if ($srow) {
  $update_params = array(
      'status' => makeSafe($status),
      'updated_at'=>"msqlfunc_NOW()"
  );

  $update_where = array(
      'clause' => 'id = :id',
      'params' => array(
          ':id' => makeSafe($pid)
      )
  );
  
  $pdo->update("agent_product_rule", $update_params, $update_where);
  
  $res['status'] = 'success';
  $res['msg'] = 'Status Changed Successfully';  

} else {
  $res['status'] = 'error';
  $res['msg'] = 'Something went wrong';
}

header('Content-type: application/json');
echo json_encode($res);
dbConnectionClose();
exit;
?>

