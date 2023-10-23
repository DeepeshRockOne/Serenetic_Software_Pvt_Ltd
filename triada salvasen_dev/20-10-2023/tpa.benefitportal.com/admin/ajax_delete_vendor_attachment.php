<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$response = array();
$vendor_attachment_id = checkIsset($_POST['id']);

if(!empty($vendor_attachment_id)){
  
  $updParam = array('is_deleted'=>'Y');
  $updWhere = array(
    'clause' => 'id = :id',
    'params' => array(
        ':id' => $vendor_attachment_id
    )
  );
  $update_status = $pdo->update('fees_attachments', $updParam, $updWhere);
  
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