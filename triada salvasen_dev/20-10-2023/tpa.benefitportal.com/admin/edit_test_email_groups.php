<?php

include_once dirname(__FILE__) . '/layout/start.inc.php';
has_access(10);

$validate = new Validation();

$id = $_GET['id'];
if ($id) {
  $selSql = "SELECT * FROM test_email_group WHERE id = :id ";
  $params = array(
      ':id' => makeSafe($id)
  );
  $row = $pdo->selectOne($selSql, $params);
  $title = $row['title'];
  $company_id = $row['company_id'];
  $mode = "EDIT";
} else {
  $title = "";
}
if (isset($_POST['cat_save'])) {
  $title = $_POST['title'];
  $company_id = $_POST['company_id'];
  $validate->string(array('required' => true, 'field' => 'title', 'value' => $title), array('required' => 'Title is required'));
  $validate->string(array('required' => true, 'field' => 'company_id', 'value' => $company_id), array('required' => 'Company name is required'));
  if ($validate->isValid()) {
    if ($id) {
      $params = array(
          'title' => makeSafe($title),
          'company_id' => $company_id,
          'updated_at' => 'msqlfunc_NOW()'
      );
      $where = array(
          'clause' => 'id=:id',
          'params' => array(
              ':id' => makeSafe($id)
          )
      );
      $pdo->update('test_email_group', $params, $where);
      setNotifySuccess('Test Group updated successfully.');
      echo '<script type="text/javascript">
            window.parent.location = "test_email_groups.php";
          </script>';
    exit;
    } 
    // redirect('test_email_groups.php');
  }
}
$company_sql = "SELECT * FROM company";
$company_res = $pdo->select($company_sql);

$errors = $validate->getErrors();
$template = "edit_test_email_groups.inc.php";
$layout = "iframe.layout.php";
include_once 'layout/end.inc.php';
?>