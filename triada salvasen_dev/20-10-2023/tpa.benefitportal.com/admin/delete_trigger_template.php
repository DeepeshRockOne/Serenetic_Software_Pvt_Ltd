<?php

include_once dirname(__FILE__) . '/layout/start.inc.php';

$id = $_GET["id"];
$res = array();
$validate = new Validation();

$check_query = "SELECT id FROM trigger_template  
          WHERE is_deleted='N' AND id ='" . $id . "'";
$check_data = $pdo->selectOne($check_query);

if (!empty($check_data)) {
  $query = "SELECT tt.id as template_id,t.id as triger_id FROM trigger_template tt
          JOIN triggers t ON(t.template_id=tt.id)  
          WHERE tt.is_deleted='N' AND tt.id ='" . $id . "'";
  $srow = $pdo->select($query);
} else {
  setNotifyError('No Record Found');
  redirect('trigger_template.php');
}

if (!empty($srow)) {
  $strQuery = "SELECT id,title FROM trigger_template WHERE is_deleted='N' ORDER BY id DESC";
  $rows = $pdo->select($strQuery);
}

if (isset($_POST['save'])) {
  $template = $_POST['template'];
  if (!empty($srow)) {
    $validate->string(array('required' => true, 'field' => 'template', 'value' => $template), array('required' => 'Template title is required'));
  }
  if ($validate->isValid()) {
    if (!empty($srow)) {
      $update_titles = array(
          'id' => makeSafe($id)
      );
      $where_sel_id = array(
          'clause' => 'template_id = :tmpl_id',
          'params' => array(
              ':tmpl_id' => makeSafe($id)
          )
      );
      
      /* Code for audit log*/
  
      $update_params_new=$update_titles;
      unset($update_params_new['updated_at']);
      foreach($update_params_new as $key_audit=>$audit_params)
      {
        $extra_column.=",".$key_audit;
      }
      if($extra_column!='')
      {
        $extra_column=trim($extra_column,',');

        $select_customer_data="SELECT ".$extra_column." FROM triggers WHERE template_id=:id";
        $select_customer_where=array(':id'=>$id);

        $result_audit_customer_data=$pdo->selectOne($select_customer_data,$select_customer_where);
      } 

      /* End Code for audit log*/

        $pdo->update("triggers", $update_titles, $where_sel_id);
      
      /* Code for audit log*/
      $user_data = get_user_data($_SESSION['admin']);
      audit_log_new($user_data, $_SESSION['admin']['id'], "Admin", "Trigger Template Deleted And ID is : ".$id, $result_audit_customer_data, $update_params_new, 'trigger template delterd by admin');

/* End Code for audit log*/
    }

    $update_params = array(
        'is_deleted' => 'Y'
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
      foreach($update_params_new as $key_audit=>$audit_params)
      {
        $extra_column.=",".$key_audit;
      }
      if($extra_column!='')
      {
        $extra_column=trim($extra_column,',');

        $select_customer_data="SELECT ".$extra_column." FROM trigger_template WHERE id=:id";
        $select_customer_where=array(':id'=>$id);

        $result_audit_customer_data=$pdo->selectOne($select_customer_data,$select_customer_where);
      } 

      /* End Code for audit log*/

        $pdo->update("trigger_template", $update_params, $update_where);
      
      /* Code for audit log*/
      $user_data = get_user_data($_SESSION['admin']);
      audit_log_new($user_data, $_SESSION['admin']['id'], "Admin", "Trigger Template Deleted And ID is : ".$id, $result_audit_customer_data, $update_params_new, 'trigger template delterd by admin');

/* End Code for audit log*/
    setNotifySuccess('Trigger Template Deleted Successfully');
    redirect('trigger_template.php', true);
  }
}

$errors = $validate->getErrors();
$template = "delete_trigger_template.inc.php";
$layout = "iframe.layout.php";
include_once 'layout/end.inc.php';
?>
