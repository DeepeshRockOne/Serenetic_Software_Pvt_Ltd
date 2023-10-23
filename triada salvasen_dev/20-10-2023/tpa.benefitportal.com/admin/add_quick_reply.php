<?php

include_once dirname(__FILE__) . '/layout/start.inc.php';
has_access(26);
$type = $_GET['type'];

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Support Dashboard';
$breadcrumbes[1]['link'] = 'support_dashboard.php';
$breadcrumbes[2]['title'] = 'Quick Reply';
$breadcrumbes[2]['link'] = 'quick_reply.php';
$breadcrumbes[3]['title'] = "Add ". $type ."Quick Reply";
$breadcrumbes[3]['class'] = 'Active';

$page_title = "Add ".$type . " Quick Reply";

$validate = new Validation();

if (isset($_POST['save'])) {

  $title = $_POST['title'];
  $response = $_POST['response'];
  $type = $_POST['type'];

  $validate->string(array('required' => true, 'field' => 'title', 'value' => $title), array('required' => 'Title is required'));
  $validate->string(array('required' => true, 'field' => 'response', 'value' => $response), array('required' => 'Response is required'));


  if ($validate->isValid()) {

    $ins_params = array(
        'title' => makeSafe($title),
        'response' => makeSafe($response),
        'type' => makeSafe($type),
        'created_at' => 'msqlfunc_NOW()',
        'updated_at' => 'msqlfunc_NOW()'
    );

    $ins_id = $pdo->insert("s_canned_messages", $ins_params);

    setNotifySuccess('Quick Reply added successfully');
    redirect("quick_reply.php?type=" . $type);
  }
}

$errors = $validate->getErrors();
$exStylesheets = array('thirdparty/colorbox/colorbox.css');
$exJs = array('thirdparty/colorbox/jquery.colorbox.js', 'thirdparty/ckeditor/ckeditor.js');

$template = "add_quick_reply.inc.php";
include_once 'layout/end.inc.php';
?>