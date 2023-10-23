<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
has_access(6);
$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Payment";
$breadcrumbes[1]['link'] = 'payment_listbills.php';
$breadcrumbes[2]['title'] = "Manage List Bills";
$breadcrumbes[2]['link'] = 'manage_listbills.php';


$days_range=range(1,10);
$auto_pay_day_range=range(1,9);
// $auto_set_payment_received='';

$sqlListBillOptions = "SELECT days_prior_pay_period as listbillday,auto_payment_days FROM list_bill_options where rule_type='Global' and is_deleted='N'";
$resListBillOptions = $pdo->selectOne($sqlListBillOptions);
if(!empty($resListBillOptions)){
   // $auto_set_payment_received=$resListBillOptions['auto_set_payment_received'];
   $auto_pay_day=$resListBillOptions['auto_payment_days'];
}

// $exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache);
// $exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache, );

$template = 'manage_listbills.inc.php';
include_once 'layout/end.inc.php';
?>
