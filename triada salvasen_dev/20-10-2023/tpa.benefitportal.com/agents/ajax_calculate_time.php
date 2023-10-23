<?php

include_once (__DIR__) . '/layout/start.inc.php';
$res = array();

$endtime = strtotime(date('Y-m-d H:i:s'));
$checkSql = "SELECT id,changed_at FROM $LOG_DB.audit_log WHERE id = :id";
$row = $pdo->selectOne($checkSql, array(':id' => makeSafe($_SESSION['agents']['audit_log_id'])));

$timeSpent = $endtime - strtotime($row['changed_at']);

if ($row) {
  $update_params = array('customer_time_spent' => $timeSpent);
  $update_where = array('clause' => 'id = :id', 'params' => array(':id' => makeSafe($_SESSION['agents']['audit_log_id'])));
  $pdo->update("$LOG_DB.audit_log", $update_params, $update_where);
  $res['status'] = 'success';
} else {
  $res['status'] = 'fail';
}

header('Content-Type: application/json');
echo json_encode($res);
dbConnectionClose();
exit;
?>