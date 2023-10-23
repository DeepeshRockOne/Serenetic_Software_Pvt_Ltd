<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Payment';
$breadcrumbes[2]['title'] = 'Setting';
$breadcrumbes[2]['link'] = 'payment_setting.php';

	$endCoverageRes = $pdo->selectOne("SELECT id,is_open_enrollment,end_coverage_date FROM end_coverage_periods_settings WHERE is_deleted='N'");

	$resWeeklyCommSettings = $pdo->selectOne("SELECT id,commission_day,commission_period FROM commission_periods_settings WHERE commission_type='weekly'");
	$weeklyCommissionDay = checkIsset($resWeeklyCommSettings['commission_day']);

	$description['ac_message'] =array(
	      'ac_red_1'=>array(
	        'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
	        'title'=>$_SESSION['admin']['display_id'],
	      ),
	      'ac_message_1' =>' read payment settings ',
	    ); 

	activity_feed(3, $_SESSION['admin']['id'], 'Admin', 0, '','Admin Read Payment Settings', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));




$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache);
$exJs = array(
	'thirdparty/masked_inputs/jquery.inputmask.bundle.js',
	'thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache
);

$template = "payment_setting.inc.php";
include_once 'layout/end.inc.php';