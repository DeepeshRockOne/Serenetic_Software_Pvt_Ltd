<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Payment";
$breadcrumbes[2]['title'] = "Merchant Processor";
$breadcrumbes[3]['title'] = "+ Variation";
$breadcrumbes[2]['link'] = 'merchant_processor.php';
$breadcrumbes[3]['link'] = 'add_merchant_processor_variation.php';

$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache);
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache);

$template = 'add_merchant_processor_variation.inc.php';
include_once 'layout/end.inc.php';
?>

