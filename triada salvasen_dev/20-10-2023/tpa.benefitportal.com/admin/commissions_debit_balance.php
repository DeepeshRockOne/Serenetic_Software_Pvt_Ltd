<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once dirname(__DIR__) . "/includes/commission.class.php";
$commObj = new Commission();
$agent_id = $_GET['agent_id'];
$agentSql= "SELECT id,rep_id,fname,lname FROM customer where id=:id";
$agentRes=$pdo->selectOne($agentSql,array(":id"=>$agent_id));

$agentName ='';
$agentRepID ='';

if(!empty($agentRes)){
	$agentName = $agentRes['fname'].' '.$agentRes['lname'];
	$agentRepID = $agentRes['rep_id'];
}

if(isset($_GET['action']) && $_GET['action'] == 'export_debit_balance') {
	include_once dirname(__DIR__) . '/includes/aws_reporting_api_url.php';
	include_once dirname(__DIR__) . '/includes/export_report.class.php';
	$config_data = array(
		'user_id' => $_SESSION['admin']['id'],
		'user_type' => 'Admin',
		'user_rep_id' => $_SESSION['admin']['display_id'],
		'user_profile_page' => $ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
		'timezone' => $_SESSION['admin']['timezone'],
		'file_type' => 'EXCEL',
		'report_location' => 'commissions_debit_balance_popup',
		'report_key' => 'admin_agent_debit_balance',
	);
	$_POST['join_range'] = 'before';
	$_POST['added_date'] = date('m/d/Y',strtotime('+1 day'));
	$exportreport = new ExportReport(0,$config_data);
	$response = $exportreport->run();
	echo json_encode($response);
	exit();
}

$debitBalance = 0;
if(!empty($agent_id)){
	$debitBalance = $commObj->getAgentDebitBalance($agent_id);
}
$template = "commissions_debit_balance.inc.php";
$layout = "iframe.layout.php";
include_once 'layout/end.inc.php';
?>