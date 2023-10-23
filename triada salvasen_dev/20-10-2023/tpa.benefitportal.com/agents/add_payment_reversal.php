<?php
include_once __DIR__ . '/includes/connect.php';

$location = isset($_REQUEST['location'])?$_REQUEST['location']:'admin';
$tmp_order_id = isset($_GET['orderId']) ? $_GET['orderId'] : "";

$orders = $pdo->select("SELECT o.id,o.display_id from orders o join customer c on(c.id = o.customer_id) where o.status = 'Payment Approved' and c.status not in('Customer Abandon','Pending Quote','Pending Validation','Post Payment') order by id desc");


$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Payment';
$breadcrumbes[3]['title'] = 'Reversals';
$breadcrumbes[3]['link'] = 'payment_reversal.php';

$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache);
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache, 'thirdparty/jquery-match-height/js/jquery.matchHeight.js');

$page_title = "Payment Reversals";
$template = 'add_payment_reversal.inc.php';
include_once 'layout/end.inc.php';
?>