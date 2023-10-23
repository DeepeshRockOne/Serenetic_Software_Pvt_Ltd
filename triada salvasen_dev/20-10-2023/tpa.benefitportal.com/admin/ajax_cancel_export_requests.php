<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
//We are not use this file

$request_id = $_POST['request_id'];


if(isset($request_id)){
  $request_sql = "SELECT * FROM $REPORT_DB.export_requests WHERE is_proceed='N' AND is_cancelled='N' AND id=:id";
  $where_id = array(':id' => $request_id);
  $export_res = $pdo->selectOne($request_sql, $where_id);

  $res = array();
   
  if (!$export_res) {
    $res['status'] = 'fail';
    $res['msg'] = 'Export request can not be Cancelled.';
  } else {
    $up_params = array(
        'is_deleted' => 'Y',
        'updated_at' => 'mysqlfunc_NOW()'
    );
    $up_where = array(
        'clause' => 'id=:id',
        'params' => array(
            ':id' => $request_id
        )
    );
    
    $pdo->update("$REPORT_DB.export_requests", $up_params, $up_where);

    $res['status'] = 'success';
    $res['msg'] = 'Export Request Cancelled Successfully';
  }
}


header('Content-Type:application/json');
echo json_encode($res);
dbConnectionClose();
exit;
?>