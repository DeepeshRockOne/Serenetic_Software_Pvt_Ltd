<?php

include_once dirname(__FILE__) . '/layout/start.inc.php';

$image_id = $_GET['image_id'];
$validate = new Validation();

if (isset($_POST['is_ajax'])) {
  $image_id = $_POST['image_id'];
  $res = array();
  $t_query = "SELECT ttm.id,tt.title,tt.id as template_id 
              FROM trigger_template_images ttm 
              JOIN trigger_template tt ON(tt.id=template_id) 
              WHERE ttm.trg_image_id=:image_id";
  $t_where = array(':image_id' => $image_id);
  $t_res = $pdo->select($t_query, $t_where);
  if (count($t_res) == 0) {
    $u_params = array(
        'is_deleted' => 'Y',
    );
    $u_where = array(
        'clause' => 'id=:image_id',
        'params' => array(':image_id' => $image_id)
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

        $select_customer_data="SELECT ".$extra_column." FROM trigger_images WHERE id=:id";
        $select_customer_where=array(':id'=>$image_id);

        $result_audit_customer_data=$pdo->selectOne($select_customer_data,$select_customer_where);
      } 

      /* End Code for audit log*/

        $pdo->update('trigger_images', $u_params, $u_where);
      
      /* Code for audit log*/
      $user_data = get_user_data($_SESSION['admin']);
      audit_log_new($user_data, $_SESSION['admin']['id'], "Admin", "Trigger Imange Deleted Id is ".$image_id, $result_audit_customer_data, $update_params_new, 'trigger image deleted by admin');

      /* End Code for audit log*/

    $res['status'] = 'success';
    setNotifySuccess('Trigger image deleted successfully.');
  } else {
    $res['status'] = 'fail';
  }

  header('Content-Type:appliaction/json');
  echo json_encode($res);
  exit;
}

if ($image_id != '') {
//
  $i_query = "SELECT id FROM trigger_images WHERE id=:image_id";
  $i_where = array(':image_id' => $image_id);
  $i_res = $pdo->selectOne($i_query, $i_where);

  if ($i_res) {
    $t_query = "SELECT ttm.id,tt.title,tt.id as template_id 
                FROM trigger_template_images ttm 
                JOIN trigger_template tt ON(tt.id=ttm.template_id) 
                WHERE ttm.trg_image_id=:image_id";
    $t_where = array(':image_id' => $image_id);
    $t_res = $pdo->select($t_query, $t_where);
    if (count($t_res) == 0) {
      $u_params = array(
          'is_deleted' => 'Y',
      );
      $u_where = array(
          'clause' => 'id=:image_id',
          'params' => array(':image_id' => $o_image_id)
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

        $select_customer_data="SELECT ".$extra_column." FROM trigger_images WHERE id=:id";
        $select_customer_where=array(':id'=>$image_id);

        $result_audit_customer_data=$pdo->selectOne($select_customer_data,$select_customer_where);
      } 

      /* End Code for audit log*/

        $pdo->update('trigger_images', $u_params, $u_where);
      
      /* Code for audit log*/
      $user_data = get_user_data($_SESSION['admin']);
      audit_log_new($user_data, $_SESSION['admin']['id'], "Admin", "Trigger Imange Deleted Id is ".$image_id, $result_audit_customer_data, $update_params_new, 'trigger image deleted by admin');

      /* End Code for audit log*/
      setNotifySuccess('Trigger image deleted successfully.');
      redirect('trigger_images.php', 1);
    }
  }
  $img_query = "SELECT id,src,title FROM trigger_images WHERE is_deleted='N' AND id!=:image_id";
  $img_w = array(':image_id' => $image_id);
  $img_res = $pdo->select($img_query,$img_w);
}
if (isset($_POST['change'])) {
  $o_image_id = $_POST['image_id'];
  $image_replace = $_POST['image_replace'];
  $validate->string(array('required' => true, 'value' => $image_replace, 'field' => 'image_replace'), array('required' => 'Please select image'));

  if ($validate->isValid()) {
    $query = "SELECT id FROM trigger_images WHERE src=:src";
    $where = array(':src' => $image_replace);
    $u_res = $pdo->selectOne($query, $where);
    if ($u_res) {
      $u_params = array(
          'trg_image_id' => $u_res['id']
      );
      $u_where = array(
          'clause' => 'trg_image_id=:image_id',
          'params' => array(':image_id' => $o_image_id)
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

        $select_customer_data="SELECT ".$extra_column." FROM trigger_template_images WHERE trg_image_id=:trg_image_id";
        $select_customer_where=array(':trg_image_id'=>$image_id);

        $result_audit_customer_data=$pdo->selectOne($select_customer_data,$select_customer_where);
      } 

      /* End Code for audit log*/

        $pdo->update('trigger_template_images', $u_params, $u_where);
      
      /* Code for audit log*/
      $user_data = get_user_data($_SESSION['admin']);
      audit_log_new($user_data, $_SESSION['admin']['id'], "Admin", "Trigger Imange Deleted Id is ".$image_id, $result_audit_customer_data, $update_params_new, 'trigger image deleted by admin');

      /* End Code for audit log*/


      $u_params = array(
          'is_deleted' => 'Y',
      );
      $u_where = array(
          'clause' => 'id=:image_id',
          'params' => array(':image_id' => $o_image_id)
      );
      /* Code for audit log*/
  
      $update_params_new=$u_params;
      unset($update_params_new['updated_at']);
      foreach($update_params_new as $key_audit=>$audit_params){
        $extra_column.=",".$key_audit;
      }
      if($extra_column!='')
      {
        $extra_column=trim($extra_column,',');

        $select_customer_data="SELECT ".$extra_column." FROM trigger_images WHERE trg_image_id=:id";
        $select_customer_where=array(':id'=>$image_id);

        $result_audit_customer_data=$pdo->selectOne($select_customer_data,$select_customer_where);
      } 

      /* End Code for audit log*/

        $pdo->update('trigger_images', $u_params, $u_where);
      
      /* Code for audit log*/
      $user_data = get_user_data($_SESSION['admin']);
      audit_log_new($user_data, $_SESSION['admin']['id'], "Admin", "Trigger Imange Deleted Id is ".$image_id, $result_audit_customer_data, $update_params_new, 'trigger image deleted by admin');

      /* End Code for audit log*/
      
      setNotifySuccess('Trigger image deleted successfully.');
      redirect('trigger_images.php', 1);
    }
  }
}
$errors = $validate->getErrors();
$template = "delete_template_image.inc.php";
$layout = "iframe.layout.php";
include_once 'layout/end.inc.php';
?>
