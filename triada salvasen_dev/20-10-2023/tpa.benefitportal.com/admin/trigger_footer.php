<?php

include_once dirname(__FILE__) . '/layout/start.inc.php';
has_access(10);

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Emailar Dashboard';
$breadcrumbes[1]['link'] = 'emailer_dashboard.php';
$breadcrumbes[2]['title'] = 'Trigger Footer';

$validate = new Validation();

$mode = "ADD";
$id = $_GET['id'];
if ($id) {
  $selSql = "SELECT * FROM trigger_footer WHERE id = :id ";
  $params = array(
      ':id' => makeSafe($id)
  );
  $row = $pdo->selectOne($selSql, $params);

  if (count($row) == 0) {
    redirect('trigger_footer.php');
  }
  $title = $row['title'];
  $company_id = $row['company_id'];
  $content = $row['content'];
  $mode = "EDIT";
} else {
  $content = "";
}
if (isset($_POST['save'])) {
  $title = $_POST['title'];
  $content = $_POST['content'];
  $company_id = $_POST['company_id'];
  
  $validate->string(array('required' => true, 'field' => 'content', 'value' => $content), array('required' => 'Content is required'));
  $validate->string(array('required' => true, 'field' => 'title', 'value' => $title), array('required' => 'Title is required'));
  $validate->string(array('required' => true, 'field' => 'company_id', 'value' => $company_id), array('required' => 'Company name is required'));
  
  if ($validate->isValid()) {

    if ($id) {
      $params = array(
          'title' => makeSafe($title),
          'company_id' => $company_id,
          'content' => makeSafe($content),
          'updated_at' => 'msqlfunc_NOW()'
      );
      $where = array(
          'clause' => 'id=:id',
          'params' => array(
              ':id' => makeSafe($id)
          )
      );
      
      /* Code for audit log*/
  
      $update_params_new=$params;
      unset($update_params_new['updated_at']);
      foreach($update_params_new as $key_audit=>$audit_params)
      {
        $extra_column.=",".$key_audit;
      }
      if($extra_column!='')
      {
        $extra_column=trim($extra_column,',');

        $select_customer_data="SELECT ".$extra_column." FROM trigger_footer WHERE id=:id";
        $select_customer_where=array(':id'=>$id);

        $result_audit_customer_data=$pdo->selectOne($select_customer_data,$select_customer_where);
      } 

      /* End Code for audit log*/

        $pdo->update('trigger_footer', $params, $where);
      
      /* Code for audit log*/
      $user_data = get_user_data($_SESSION['admin']);
      audit_log_new($user_data, $_SESSION['admin']['id'], "Admin", "Trigger Footer updated Id is ".$id, $result_audit_customer_data, $update_params_new, 'trigger footer updated by admin');

/* End Code for audit log*/
      setNotifySuccess('Footer updated successfully.');
    } else {
      $params = array(
          'title' => makeSafe($title),
          'company_id' => $company_id,
          'content' => makeSafe($content),
          'created_at' => 'msqlfunc_NOW()'
      );
      $triger_cat_id = $pdo->insert('trigger_footer', $params);
      /* Code for audit log*/
    $user_data = get_user_data($_SESSION['admin']);
    audit_log_new($user_data, $_SESSION['admin']['id'], "Admin", "Trigger Footer Insert ID is :".$triger_cat_id, '', $params, 'trigger footer created by admin');

/* End Code for audit log*/
      setNotifySuccess('Footer added successfully.');
    }
    redirect(basename($_SERVER['PHP_SELF']));
  }
}

$errors = $validate->getErrors();

$strQuery = "SELECT tf.*,count(DISTINCT ta.id) as total_used FROM trigger_footer tf LEFT JOIN trigger_template ta ON(tf.id=ta.trg_footer_id) WHERE tf.is_deleted='N' GROUP BY tf.id ORDER BY tf.id DESC";
$rows = $pdo->select($strQuery);

$company_sql = "SELECT * FROM company";
$company_res = $pdo->select($company_sql);

$exJs = array('thirdparty/ckeditor/ckeditor.js');

$page_title = "Trigger Footer";
$template = "trigger_footer.inc.php";
$layout = "main.layout.php";
include_once 'layout/end.inc.php';
?>
