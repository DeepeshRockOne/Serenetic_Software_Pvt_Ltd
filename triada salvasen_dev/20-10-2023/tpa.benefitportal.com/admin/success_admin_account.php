<?php
include_once __DIR__ . '/includes/connect.php';

$_SESSION['admin_suceess']=false; 
 unset($_SESSION['match']);
$template = 'success_admin_account.inc.php';
$layout = 'create.account.layout.php';
include_once 'layout/end.inc.php';
?>
