<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$response = array();
$attachment_id = checkIsset($_POST['id']);
$type = checkIsset($_POST['type']);
$table_name = "email_attachment";
if($type == "trigger"){
  $table_name = "trigger_attachment";
}
if(!empty($attachment_id)){
  
  $updParam = array('is_deleted'=>'Y');
  $updWhere = array(
    'clause' => 'id = :id',
    'params' => array(
        ':id' => $attachment_id
    )
  );
  $update_status = $pdo->update($table_name, $updParam, $updWhere);
  
  $response['status']="success";
  $response['message']="Attachment Deleted Successfully";
}else{
  $response['status']="fail";
  $response['message']="Attachment Not Found";
}

header('Content-Type: application/json');
echo json_encode($response);
dbConnectionClose();
exit;
?>