<?php

include_once dirname(__FILE__) . '/layout/start.inc.php';

$address_id = $_GET['address_id'];
$validate = new Validation();

if (isset($_POST['is_ajax'])) {
  $address_id = $_POST['address_id'];
  $res = array();
  $t_query = "SELECT ttm.id,tt.title,tt.id as template_id 
              FROM trigger_template_address ttm 
              JOIN trigger_template tt ON(tt.id=ttm.template_id) 
              WHERE ttm.trg_address_id=:address_id";
  $t_where = array(':address_id' => $address_id);
  $t_res = $pdo->select($t_query, $t_where);
  if (count($t_res) == 0) { 
    $u_params = array(
        'is_deleted' => 'Y',
    );
    $u_where = array (
        'clause' => 'id=:address_id',
        'params' => array(':address_id' => $address_id)
    );
    
    /* Code for audit log */
  
      $update_params_new=$u_params;
      unset($update_params_new['updated_at']);
      foreach($update_params_new as $key_audit=>$audit_params)
      {
        $extra_column.=",".$key_audit;
      }
      if($extra_column!='')
      {
        $extra_column=trim($extra_column,',');

        $select_customer_data="SELECT ".$extra_column." FROM trigger_address WHERE id=:id";
        $select_customer_where=array(':id'=>$address_id);

        $result_audit_customer_data=$pdo->selectOne($select_customer_data,$select_customer_where);
      } 

      /* End Code for audit log*/

        $pdo->update('trigger_address', $u_params, $u_where);
      
      /* Code for audit log*/
      $user_data = get_user_data($_SESSION['admin']);
      audit_log_new($user_data, $_SESSION['admin']['id'], "Admin", "Trigger Address Delete Rep Id is ".$address_id, $result_audit_customer_data, $update_params_new, 'trigger template delete by admin');

/* End Code for audit log*/
    $res['status'] = 'success';
    setNotifySuccess('Trigger address deleted successfully.');
  } else {
    $res['status'] = 'fail';
  }

  header('Content-Type:appliaction/json');
  echo json_encode($res);
  exit;
}

if ($address_id != '') { 
//
  $i_query = "SELECT id FROM trigger_address WHERE id=:address_id";
  $i_where = array(':address_id' => $address_id);
  $i_res = $pdo->selectOne($i_query, $i_where);

  if ($i_res) {
    $t_query = "SELECT ttm.id,tt.title,tt.id as template_id FROM trigger_template_address ttm JOIN trigger_template tt ON(tt.id=template_id) WHERE ttm.trg_address_id=:address_id";
    $t_where = array(':address_id' => $address_id);
    $t_res = $pdo->select($t_query, $t_where);
    if (count($t_res) == 0) {
      $u_params = array(
          'is_deleted' => 'Y',
      );
      $u_where = array(
          'clause' => 'id=:address_id',
          'params' => array(':address_id' => $o_address_id)
      );
      
      /* Code for audit log*/
  
      $update_params_new=$u_params;
      unset($update_params_new['updated_at']);
      foreach($update_params_new as $key_audit=>$audit_params)
      {
        $extra_column.=",".$key_audit;
      }
      if($extra_column!='')
      {
        $extra_column=trim($extra_column,',');

        $select_customer_data="SELECT ".$extra_column." FROM trigger_address WHERE id=:id";
        $select_customer_where=array(':id'=>$address_id);

        $result_audit_customer_data=$pdo->selectOne($select_customer_data,$select_customer_where);

      } 

      /* End Code for audit log*/

        $pdo->update('trigger_address', $u_params, $u_where);
      
      /* Code for audit log*/
      $user_data = get_user_data($_SESSION['admin']);
      audit_log_new($user_data, $_SESSION['admin']['id'], "Admin", "Trigger Address Delete Rep Id is ".$address_id, $result_audit_customer_data, $update_params_new, 'trigger template delete by admin');

/* End Code for audit log*/
      setNotifySuccess('Trigger address deleted successfully.');
      redirect('trigger_address.php', 1);
    }
  }
  $img_query = "SELECT id,title,address FROM trigger_address WHERE is_deleted='N' AND id!=:address_id";
  $img_w = array(':address_id' => $address_id);
  $img_res = $pdo->select($img_query, $img_w);

}
if (isset($_POST['change'])) { 
  $o_address_id = $_POST['address_id'];
  $address_replace = $_POST['address_replace'];
  $validate->string(array('required' => true, 'value' => $address_replace, 'field' => 'address_replace'), array('required' => 'Please select address'));

  if ($validate->isValid()) {
    $query = "select id FROM trigger_address WHERE id=:id";
    $where = array(':id' => $address_replace);
    $u_res = $pdo->selectOne($query, $where);
    if ($u_res) {
      $u_params = array(
          'trg_address_id' => $u_res['id']
      );
      $u_where = array(
          'clause' => 'trg_address_id=:address_id',
          'params' => array(':address_id' => $o_address_id)
      );
      
      /* Code for audit log*/
  
      $update_params_new=$u_params;
      unset($update_params_new['updated_at']);
      foreach($update_params_new as $key_audit=>$audit_params)
      {
        $extra_column.=",".$key_audit;
      }
      if($extra_column!='')
      {
        $extra_column=trim($extra_column,',');

        $select_customer_data="SELECT ".$extra_column." FROM trigger_template_address WHERE trg_address_id=:id";
        $select_customer_where=array(':id'=>$address_id);

        $result_audit_customer_data=$pdo->selectOne($select_customer_data,$select_customer_where);
      } 

      /* End Code for audit log*/

        $pdo->update('trigger_template_address', $u_params, $u_where);
      
      /* Code for audit log*/
      $user_data = get_user_data($_SESSION['admin']);
      audit_log_new($user_data, $_SESSION['admin']['id'], "Admin", "Trigger Address Delete Rep Id is ".$address_id, $result_audit_customer_data, $update_params_new, 'trigger template delete by admin');

/* End Code for audit log*/

      $u_params = array(
          'is_deleted' => 'Y',
      );
      $u_where = array(
          'clause' => 'id=:address_id',
          'params' => array(':address_id' => $o_address_id)
      );
      
      /* Code for audit log*/
  
      $update_params_new=$u_params;
      unset($update_params_new['updated_at']);
      foreach($update_params_new as $key_audit=>$audit_params)
      {
        $extra_column.=",".$key_audit;
      }
      if($extra_column!='')
      {
        $extra_column=trim($extra_column,',');

        $select_customer_data="SELECT ".$extra_column." FROM trigger_address WHERE id=:id";
        $select_customer_where=array(':id'=>$address_id);

        $result_audit_customer_data=$pdo->selectOne($select_customer_data,$select_customer_where);
      } 

      /* End Code for audit log*/

        $pdo->update('trigger_address', $u_params, $u_where);
      
      /* Code for audit log*/
      $user_data = get_user_data($_SESSION['admin']);
      audit_log_new($user_data, $_SESSION['admin']['id'], "Admin", "Trigger Address Delete Rep Id is ".$address_id, $result_audit_customer_data, $update_params_new, 'trigger template delete by admin');

/* End Code for audit log*/
      setNotifySuccess('Trigger address deleted successfully.');
      redirect('trigger_address.php', 1);
    }
  }
}
$errors = $validate->getErrors();
$template = "delete_template_address.inc.php";
$layout = "iframe.layout.php";
include_once 'layout/end.inc.php';
?>
