<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$module_access_type = has_access(91);
$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Payment";
$breadcrumbes[2]['title'] = "HRM Payments";
$breadcrumbes[2]['link'] = 'hrm_payments.php';
$breadcrumbes[3]['title'] = date('m/d/Y', strtotime('-6 days', strtotime($_GET['pay_period']))) . ' - ' . date("m/d/Y", strtotime($_GET['pay_period']));
$pay_period = checkIsset($_GET['pay_period']);

if (empty($pay_period)) {
  redirect("hrm_payments.php");
}

$is_ajax = isset($_GET['is_ajax']) ? $_GET['is_ajax'] : '';
if (isset($_GET["is_ajax"])) {
  include 'tmpl/weekly_hrm_payment_status.inc.php';
  exit;
}

// Read HRM Payments Page activity code start
$description['ac_message'] = array(
  'ac_red_1' => array(
    'href' => $ADMIN_HOST . '/admin_profile.php?id=' . md5($_SESSION['admin']['id']),
    'title' => $_SESSION['admin']['display_id'],
  ),
  'ac_message_1' => ' read hrm payment page ',
);

activity_feed(3, $_SESSION['admin']['id'], 'Admin', 0, 'hrm_payment', 'Read HRM Payments', $_SESSION['admin']['fname'], $_SESSION['admin']['lname'], json_encode($description));
// Read HRM Payments Page activity code ends

$exStylesheets = array('thirdparty/bootstrap-tables/css/bootstrap-table.min.css');
$exJs = array('thirdparty/bootstrap-tables/js/bootstrap-table.min.js');


$template = 'weekly_hrm_payment_status.inc.php';
include_once 'layout/end.inc.php';
