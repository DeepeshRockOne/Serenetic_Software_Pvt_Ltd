<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once dirname(__DIR__) . "/includes/commission.class.php";
$commObj = new Commission();

if(isset($_GET['action']) && $_GET['action'] == 'export_debit_balance') {
	include_once dirname(__DIR__) . '/includes/aws_reporting_api_url.php';
	include_once dirname(__DIR__) . '/includes/export_report.class.php';
	$config_data = array(
		'user_id' => $_SESSION['agents']['id'],
		'user_type' => 'Agent',
		'user_rep_id' => $_SESSION['agents']['rep_id'],
		'user_profile_page' => $AGENT_HOST.'/agent_detail_v1.php?id='.md5($_SESSION['agents']['id']),
		'timezone' => $_SESSION['agents']['timezone'],
		'file_type' => 'EXCEL',
		'report_location' => 'commissions_debit_balance_popup',
		'report_key' => 'agent_debit_balance',
	);
	$_POST['join_range'] = 'before';
	$_POST['added_date'] = date('m/d/Y',strtotime('+1 day'));
	$exportreport = new ExportReport(0,$config_data);
	$response = $exportreport->run();
	echo json_encode($response);
	exit();
}

$agent_id = $_SESSION['agents']['id'];
$agentName = $_SESSION['agents']['fname'].' '.$_SESSION['agents']['lname'];
$debitBalance = 0;
if(!empty($agent_id)){
	$debitBalance = $commObj->getAgentDebitBalance($agent_id);
}
$template = "commissions_debit_balance.inc.php";
$layout = "iframe.layout.php";
include_once 'layout/end.inc.php';
?>