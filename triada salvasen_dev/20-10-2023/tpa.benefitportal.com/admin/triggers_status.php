<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$referer = basename($_SERVER['HTTP_REFERER']);

if($referer != ""){  
  $sel_trigger_sql = "SELECT status,id FROM `triggers` WHERE id=:id";
  $trigger_param = array(":id"=>makeSafe($_GET['id']));
  $row = $pdo->selectOne($sel_trigger_sql,$trigger_param);
  if($_GET['s_status']){
    $update_status = array(
      'status' => makeSafe($_GET['s_status'])
    );
    $status_where = array(
      'clause' => 'id = :id',
      'params' => array(
        ':id' => makeSafe($_GET['id'])
      )
    );    
    $pdo->update("triggers",$update_status,$status_where);    
    setNotifySuccess('Trigger status changed successfully.');   
    redirect($referer);
  }
}else{
  redirect('dashboard.php');
}
?>
