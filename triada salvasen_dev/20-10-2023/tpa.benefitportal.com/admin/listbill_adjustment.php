<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Payment";
$breadcrumbes[1]['link'] = "payment_listbills.php";
$breadcrumbes[2]['title'] = "+ Adjustment";
$breadcrumbes[2]['class'] = "Active";

$sqlGroup = "SELECT c.id,c.rep_id,c.business_name,c.fname,c.lname FROM customer c WHERE c.type='Group' AND c.is_deleted='N'";
$resGroup = $pdo->select($sqlGroup);


$exJs = array(
	'thirdparty/price_format/jquery.price_format.2.0.js',
);

$template = 'listbill_adjustment.inc.php';
include_once 'layout/end.inc.php';
?>