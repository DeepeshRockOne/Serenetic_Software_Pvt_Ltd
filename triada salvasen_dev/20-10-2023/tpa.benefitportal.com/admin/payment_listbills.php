<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Payment";
$breadcrumbes[2]['title'] = "List Bills";
$breadcrumbes[2]['class'] = "Active";

$admin_id = $_SESSION['admin']['id'];
$admin_display_id=$_SESSION['admin']['display_id'];

$description['ac_message'] = array(
	'ac_red_1' => array(
        'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
    	'title'=>$_SESSION['admin']['display_id'],
    ),
    'ac_message_1' => ' read List Bill Payments ',
);
$desc = json_encode($description);

activity_feed(3, $_SESSION['admin']['id'], 'Admin', $_SESSION['admin']['id'], 'Admin', 'Admin Read List Bill Payments', $_SESSION['admin']['fname'], $_SESSION['admin']['lname'], $desc);

$template = 'payment_listbills.inc.php';
include_once 'layout/end.inc.php';
?>