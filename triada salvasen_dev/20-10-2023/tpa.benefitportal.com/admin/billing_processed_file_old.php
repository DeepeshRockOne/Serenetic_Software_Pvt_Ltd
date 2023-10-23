<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Payment";
$breadcrumbes[2]['title'] = "Billing Files";
$breadcrumbes[3]['title'] = "Processed Files For ASH";
$breadcrumbes[2]['link'] = 'billing_processed_file.php';

$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache);
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache);

$template = 'billing_processed_file.inc.php';
include_once 'layout/end.inc.php';
?>
