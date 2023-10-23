<?php

include_once dirname(__FILE__) . '/layout/start.inc.php';
has_access(10);
$delete_id = $_POST["id"];

$res = array();

$query = "SELECT id,status FROM triggers WHERE is_deleted='N' AND id=:id ";
$srow = $pdo->selectOne($query,array(':id'=>$delete_id));

if ($srow) {
  $update_params = array(
      'is_deleted' => 'Y',
      'status' => 'Inactive',
      'update_at' => 'msqlfunc_NOW()'
  );
  $update_where = array(
      'clause' => 'id = :delete_id',
      'params' => array(
          ':delete_id' => makeSafe($delete_id)
      )
  );
  //$pdo->update("triggers", $update_params, $update_where);
  /* Code for audit log*/
  
      $update_params_new=$update_params;
      //unset($update_params_new['updated_at']);
      foreach($update_params_new as $key_audit=>$up_params)
      {
        $extra_column.=",".$key_audit;
      }
      if($extra_column!='')
      {
        $extra_column=trim($extra_column,',');

        $select_data="SELECT ".$extra_column." FROM triggers WHERE id=:id";
        $select_where=array(':id'=>$delete_id);

        $result_audit_data=$pdo->selectOne($select_data,$select_where);
      } 

      /* End Code for audit log*/
  
      $pdo->update("triggers", $update_params, $update_where);
      
      /* Code for audit log*/
      
      $user_data = get_user_data($_SESSION['admin']);
      audit_log_new($user_data, $_SESSION['admin']['id'], "Admin", "Trigger deleted by ID is ".$delete_id, $result_audit_data, $update_params_new, 'trigger deleted by admin');

      /* End Code for audit log*/
  $res['status'] = 'success';
  setNotifySuccess("Trigger deleted successfully");
} else {
  $res['status'] = 'fail';
}
header('Content-Type: application/json');
echo json_encode($res);
dbConnectionClose();
exit;
?>
