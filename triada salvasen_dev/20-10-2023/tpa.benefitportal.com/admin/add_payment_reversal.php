<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$location = isset($_REQUEST['location'])?$_REQUEST['location']:'admin';
$tmp_order_id = isset($_GET['orderId']) ? $_GET['orderId'] : "";

if(!empty($tmp_order_id)){
	$orderSql = "SELECT o.id,o.display_id,o.status 
	FROM orders o 
	JOIN customer c ON(c.id = o.customer_id) 
	WHERE o.status IN ('Payment Approved','Pending Settlement') 
	AND c.status NOT IN('Customer Abandon','Pending Quote','Pending Validation','Post Payment') 
	AND md5(o.id) = :orderId
	ORDER BY o.id DESC";
	$orderRes = $pdo->selectOne($orderSql,array(":orderId" => $tmp_order_id));
}
$odrId = !empty($orderRes["id"]) ? $orderRes["id"] : 0;
$odrDispId = !empty($orderRes["display_id"]) ? $orderRes["display_id"] : 0;


$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Payment';
$breadcrumbes[3]['title'] = 'Reversals';
$breadcrumbes[3]['link'] = 'payment_reversal.php';

$selectize = true;
$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache);
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache, 'thirdparty/jquery-match-height/js/jquery.matchHeight.js');

$page_title = "Payment Reversals";
$template = 'add_payment_reversal.inc.php';
include_once 'layout/end.inc.php';
?>