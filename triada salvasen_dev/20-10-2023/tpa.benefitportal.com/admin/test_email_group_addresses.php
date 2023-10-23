<?php

include_once dirname(__FILE__) . '/layout/start.inc.php';

has_access(10);

$group_id = $_GET['id'];
$validate = new Validation();

if ($group_id == "") {
  redirect("test_email_groups.php", TRUE);
}

if (isset($_GET['del_id'])) {
  $delTrgSql = "DELETE FROM test_email_addresses WHERE id = :cat_id ";
  $params = array(
    ':cat_id' => makeSafe($_GET['del_id'])
  );
  $pdo->delete($delTrgSql, $params);

  setNotifySuccess('Deleted successfully');
  redirect('test_email_group_addresses.php?id=' . $group_id);
}


$mode = "ADD";
$edit_id = $_GET['edit_id'];

if (isset($_POST['edit_id'])) {
  $edit_id = $_POST['edit_id'];
}
if ($edit_id) {
  $selSql = "SELECT * FROM test_email_addresses WHERE id = :id ";
  $params = array(
    ':id' => makeSafe($edit_id)
  );
  $row = $pdo->selectOne($selSql, $params);
  $email = $row['email'];
  $mode = "EDIT";
} else {
  $email = "";
}
if (isset($_POST['cat_save'])) {
  $email = trim($_POST['email']);

  $validate->email(array('required' => true, 'field' => 'email', 'value' => $email), array('required' => 'Email is required', 'invalid' => 'Invalid email'));

  $select_group = "SELECT email from test_email_addresses where email='" . $email . "' and test_email_group_id=" . $group_id . ($edit_id != "" ? ' AND id != ' . $edit_id : '');
  $res_email = $pdo->selectOne($select_group);
  if (count($res_email) > 0) {
    $validate->setError('email', 'Email exist in this group');
  }

  if ($validate->isValid()) {
    if ($edit_id) {
      $params = array(
        'email' => makeSafe($email),
        'updated_at' => 'msqlfunc_NOW()'
      );
      $where = array(
        'clause' => 'id=:id',
        'params' => array(
          ':id' => makeSafe($edit_id)
        )
      );
      $pdo->update('test_email_addresses', $params, $where);
      setNotifySuccess('Test Group email updated successfully.');
    } else {
      $params = array(
        'email' => makeSafe($email),
        'test_email_group_id' => makeSafe($group_id),
        'created_at' => 'msqlfunc_NOW()'
      );
      $triger_cat_id = $pdo->insert('test_email_addresses', $params);
      setNotifySuccess('Test Group email added successfully.');
    }
    redirect('test_email_group_addresses.php?id=' . $group_id);
  }
}

$strQuery = "SELECT * FROM test_email_addresses WHERE test_email_group_id=" . $group_id . " ORDER BY id DESC";
$fetch_rows = $pdo->select($strQuery);

//get group name
$select_group = "SELECT title from test_email_group where id=" . $group_id;
$res_group = $pdo->selectOne($select_group);


$errors = $validate->getErrors();
$template = "test_email_group_addresses.inc.php";
$layout = "iframe.layout.php";
include_once 'layout/end.inc.php';
?>