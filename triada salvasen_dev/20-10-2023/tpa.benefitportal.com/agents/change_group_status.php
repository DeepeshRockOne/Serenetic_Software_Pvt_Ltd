<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$location = isset($_GET['location'])?$_GET['location']:'agent';

$group_id = checkIsset($_GET['group_id']);
$new_status = checkIsset($_GET['new_status']);
$old_status = checkIsset($_GET['old_status']);
$from = checkIsset($_GET['from']);

$text = '';
$disp_status = $new_status;
if($new_status == 'Active'){
  $disp_status = 'Contracted';
    $text = 'Contracted Status: Contracted status allows group to login to account, continues payment of renewal commissions, and allows new applications.';
}else if($new_status == 'Suspended'){
    $text = 'Suspended Status: Suspended status allows group to login to account, continues payment of renewal commissions, but stops new applications.';
}else if($new_status == 'Terminated'){
    $text = 'Terminated Status: Terminated status blocks group access to login to account, stops payment of renewal commissions, and stops new applications. <p class="fs14 m-b-15">Additionally setting a termination status will also  terminate all policies of account as of selection below :</p>';
}

$template = 'change_group_status.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>
