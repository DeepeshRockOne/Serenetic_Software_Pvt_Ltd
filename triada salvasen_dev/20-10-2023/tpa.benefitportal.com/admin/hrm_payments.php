<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$module_access_type = has_access(91);
$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Payment";
$breadcrumbes[2]['title'] = "HRM Payments";
$breadcrumbes[2]['link'] = 'hrm_payments.php';

// Read HRM Payment Page activity code start
$description['ac_message'] = array(
    'ac_red_1' => array(
        'href' => $ADMIN_HOST . '/admin_profile.php?id=' . md5($_SESSION['admin']['id']),
        'title' => $_SESSION['admin']['display_id'],
    ),
    'ac_message_1' => ' read hrm payment page ',
);

activity_feed(3, $_SESSION['admin']['id'], 'Admin', 0, 'hrm_payment', 'Read HRM Payments', $_SESSION['admin']['fname'], $_SESSION['admin']['lname'], json_encode($description));
// Read HRM Payment Page activity code ends

$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css');
$exJs = array('thirdparty/multiple-select-master/multiple-select-old/jquery.multiple.select.js');

$template = 'hrm_payments.inc.php';
include_once 'layout/end.inc.php';
