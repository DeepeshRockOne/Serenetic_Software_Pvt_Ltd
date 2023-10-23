<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
has_access(10);

$id = $_POST["broadcaster_id"];
$status = $_POST['status'];
 
$res = array();

$query = "SELECT id, status FROM broadcaster WHERE id =:id";
$srow = $pdo->selectOne($query,array(':id'=>$id));

if ($srow) {
  $update_params = array(
      'status' => makeSafe($status)
  );  
  $update_where = array(
      'clause' => 'id = :id',
      'params' => array(
          ':id' => makeSafe($id)
      )
  );
  
  /* Code for audit log*/
  
      $update_params_new=$update_params;
      unset($update_params_new['updated_at']);
      foreach($update_params_new as $key_audit=>$audit_params){
        $extra_column.=",".$key_audit;
      }
      if($extra_column!=''){
        $extra_column=trim($extra_column,',');

        $select_customer_data="SELECT ".$extra_column." FROM broadcaster WHERE id=:id";
        $select_customer_where=array(':id'=>$id);

        $result_audit_customer_data=$pdo->selectOne($select_customer_data,$select_customer_where);
      } 

      /* End Code for audit log*/

        $pdo->update("broadcaster", $update_params, $update_where);
      
      /* Code for audit log*/
      $user_data = get_user_data($_SESSION['admin']);
      audit_log_new($user_data, $_SESSION['admin']['id'], "Admin", "Broadcaster Status Change From " . $srow['status'] . " To " . $status, $result_audit_data, $update_params_new, 'broadcaster status change by admin');


/* End Code for audit log*/
  
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

