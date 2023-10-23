<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
has_access(3);

$id = $_GET['id'];

$admin_sql = "SELECT id,display_id,fname,lname FROM admin WHERE md5(id)=:id";
$where_id = array(':id' => $id);
$admin_res = $pdo->selectOne($admin_sql, $where_id);
$res = array();
 
if (!$admin_res) {
  $res['status'] = 'fail';
  $res['msg'] = 'Admin can not be deleted.';
} else {
  $up_params = array(
      'is_active' => 'N',
      'is_deleted' => 'Y',
  );
  $up_where = array(
      'clause' => 'md5(id)=:id',
      'params' => array(
          ':id' => $id
      )
  );
  $pdo->update('admin', $up_params, $up_where);

  $entity = $pdo->selectOne('SELECT fname,lname,display_id from admin where md5(id)=:id',array(':id'=>$id));

  $description['description'] =  $_SESSION['admin']['display_id'].' admin account deleted '.$entity['fname'].' '.$entity['lname'].' '.$entity['display_id'];
  activity_feed(3, $_SESSION['admin']['id'], 'Admin',  $id , 'admin_account_deleted','Admin Account Deleted', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
  $email_subject="Admin Deleted";
  $template_content = "Hello, Admin account deleted <br/><br/>Admin Name : ".$admin_res['fname'].' '.$admin_res['lname']."<br/> Admin ID : ".$admin_res['display_id']."<br/><br/>Admin deleted by : ".$_SESSION['admin']['fname'].' '.$_SESSION['admin']['lname']."<br/> <br/> Thank You!";
 

  $res['status'] = 'success';
  $res['msg'] = 'Admin deleted successfully';  
}
header('Content-Type:application/json');
echo json_encode($res);
dbConnectionClose();
exit;
?>