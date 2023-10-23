<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once dirname(__DIR__) . '/includes/aws_reporting_api_url.php';
include_once dirname(__DIR__) . '/includes/export_report.class.php';
$validate = new Validation();
$response = array();
$report_id = isset($_POST['report_id'])?$_POST['report_id']:0;
$report_row = $pdo->selectOne("SELECT * FROM $REPORT_DB.rps_reports WHERE md5(id)=:id",array(':id' => $report_id));
if(!empty($report_row)) {
	$config_data = array(
		'user_id' => $_SESSION['groups']['id'],
		'user_type' => 'Group',
		'user_rep_id' => $_SESSION['groups']['rep_id'],
		'user_profile_page' => 'groups_details.php?id='.md5($_SESSION['groups']['id']),
		'timezone' => $_SESSION['groups']['timezone'],
		'file_type' => 'EXCEL',
		'report_location' => 'group_portal_set_report',
	);
	$exportreport = new ExportReport($report_id,$config_data);
	$response = $exportreport->run();
	echo json_encode($response);
	exit();
} else {
	setNotifyError("Report not found");
	$response['status'] = "report_not_found";
}
header('Content-Type: application/json');
echo json_encode($response);
dbConnectionClose();
exit;

?>