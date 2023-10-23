<?php

include_once (__DIR__) . '/includes/connect.php';

$key = $_GET['key'];
$type = checkIsset($_GET['type']);
if (isset($_GET['key']) && !empty($_GET['key'])) {
  $query = 'SELECT id,phone,fname,lname,display_id,email,type,status,TIMESTAMPDIFF(HOUR,invite_at,now()) as difference,invite_at FROM admin WHERE invite_key=:invite_key';
  $where = array(':invite_key' => $key);
  $row = $pdo->selectOne($query, $where);
  $phone1='';
  $phone2='';
  $phone3='';
  if(!empty($row['phone'])){
    $phone1=substr($row['phone'], 0,3);
    $phone2=substr($row['phone'], 3,3);
    $phone3=substr($row['phone'], 6,4);
  }
} else {
  setNotifyError('Invalid Link'); 
  redirect('index.php');
}

if (count($row) <= 0) {
  setNotifyError('Invalid Link');
  redirect('index.php');
} elseif ($row['difference'] > 168) {
  setNotifyError('Admin Registration link has expired');
  redirect('index.php');
}


$exStylesheets = array(
    'thirdparty/colorbox/colorbox.css', 'thirdparty/sweetalert/sweetalert.css',
);

$exJs = array(
    'admin/js/signup.js',
    'thirdparty/masked_inputs/jquery.maskedinput.min.js',
    'thirdparty/jquery_autotab/jquery.autotab-1.1b.js',
    // 'thirdparty/MaskedPassword/password_validation.js',
    'thirdparty/iPhonePassword/js/jQuery.dPassword.js',
    'thirdparty/colorbox/jquery.colorbox.js',
    'thirdparty/sweetalert/sweetalert.min.js'
    ,'js/password_validation.js'.$cache
);
$template = 'sign_up.inc.php';
$layout = 'single.layout.php';
include_once 'layout/end.inc.php';
?>
