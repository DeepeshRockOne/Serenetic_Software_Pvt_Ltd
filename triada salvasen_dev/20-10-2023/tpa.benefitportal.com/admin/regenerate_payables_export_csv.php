<?php

include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once dirname(__DIR__) . '/includes/aws_reporting_api_url.php';
include_once dirname(__DIR__) . '/includes/export_report.class.php';

$res = array();
$regeneratedPayableId = checkIsset($_REQUEST['regeneratedPayableId']);

if(empty($regeneratedPayableId)) {
	$res["status"] = "fail";
	$res["message"] = "Payables not found";
	echo json_encode($res);
	exit;
}

	$incr = "";
	$sch_params = array();

	$config_data = array(
		'user_id' => $_SESSION['admin']['id'],
		'user_type' => 'Admin',
		'user_rep_id' => $_SESSION['admin']['display_id'],
		'user_profile_page' => $ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
		'timezone' => $_SESSION['admin']['timezone'],
		'file_type' => 'EXCEL',
		'report_location' => 'payables_listing',
		'report_key' => 'payables_export',
		'incr' => $incr,
		'sch_params' => $sch_params,
		'check_validation' => false,
	);
	$_POST['regeneratedPayableId'] = $regeneratedPayableId;
	$exportreport = new ExportReport(0,$config_data);
	$response = $exportreport->run();
	echo json_encode($response);
	exit();

?>