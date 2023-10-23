<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Payment";
$breadcrumbes[2]['title'] = "Merchant Processor";
$breadcrumbes[2]['link'] = 'merchant_processor.php';

$agent_res = $pdo->select("SELECT id, fname, lname, rep_id FROM customer WHERE is_deleted = 'N' AND type ='Agent'");

$payment_master_res = $pdo->select("SELECT id,name FROM payment_master WHERE is_deleted='N' ORDER BY name ASC");

if (isset($_GET["is_ajax"])) {
    include 'tmpl/merchant_processor.inc.php';
    exit;
}

$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache);
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache);

$page_title = "Marchant Processor";
$template = 'merchant_processor.inc.php';
include_once 'layout/end.inc.php';
?>