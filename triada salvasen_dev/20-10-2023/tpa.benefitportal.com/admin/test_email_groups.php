<?php

include_once dirname(__FILE__) . '/layout/start.inc.php';
has_access(10);

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Emailar Dashboard';
$breadcrumbes[1]['link'] = 'emailer_dashboard.php';
$breadcrumbes[2]['title'] = 'Test Email Group';

$validate = new Validation();

if (isset($_GET['del_id'])) {
  $delTrgSql = "DELETE FROM test_email_addresses WHERE test_email_group_id = :cat_id ";
  $params = array(
      ':cat_id' => makeSafe($_GET['del_id'])
  );
  $pdo->delete($delTrgSql, $params);

  $delCatSql = "DELETE FROM test_email_group WHERE id = :id ";
  $params = array(
      ':id' => makeSafe($_GET['del_id'])
  );
  $pdo->delete($delCatSql, $params);

  setNotifySuccess('Test Group and it\'s email addresses are deleted successfully.');
  redirect(basename($_SERVER['PHP_SELF']));
}

 
if (isset($_POST['cat_save'])) {
  $title = $_POST['title'];
  $company_id = $_POST['company_id'];
  $validate->string(array('required' => true, 'field' => 'title', 'value' => $title), array('required' => 'Title is required'));
  $validate->string(array('required' => true, 'field' => 'company_id', 'value' => $company_id), array('required' => 'Company name is required'));
  if ($validate->isValid()) {
    
      $params = array(
          'title' => makeSafe($title),
          'company_id' => $company_id,
          'created_at' => 'msqlfunc_NOW()'
      );
      $triger_cat_id = $pdo->insert('test_email_group', $params);
      setNotifySuccess('Test Group added successfully.');
     
    redirect(basename($_SERVER['PHP_SELF']));
  }
}
$errors = $validate->getErrors();

$strQuery = "SELECT * FROM test_email_group ORDER BY id DESC";
$rows = $pdo->select($strQuery);

$company_sql = "SELECT * FROM company";
$company_res = $pdo->select($company_sql);

$page_title = 'Test Email Group';
$template = "test_email_groups.inc.php";
$layout = "main.layout.php";
include_once 'layout/end.inc.php';
?>