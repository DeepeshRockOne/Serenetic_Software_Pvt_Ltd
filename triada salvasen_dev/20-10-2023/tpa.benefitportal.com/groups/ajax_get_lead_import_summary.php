<?php
include_once 'layout/start.inc.php';
$pending_files_count = 0;
$pending_lead_per = 0;
$completed_lead_per = 0;
$total_pending = 0;
$lead_import_eta = '';

$import_summary_sql = "SELECT count(id) as pending_files_count,SUM(total_leads) as total_leads,SUM(total_processed) as total_processed FROM csv_agent_leads WHERE status='Pending'";
$import_summary_res = $pdo->selectOne($import_summary_sql);
if(!empty($import_summary_res)) {
    $total_leads = $import_summary_res['total_leads'];
    $total_processed = $import_summary_res['total_processed'];
    $total_pending = $total_leads - $total_processed;
    $pending_files_count = $import_summary_res['pending_files_count'];

    $lead_import_eta = get_left_time_by_seconds($total_pending);
    if($total_processed > 0) {
        $completed_lead_per = ($total_processed * 100) / $total_leads;
    } else {
        $completed_lead_per = 0;
    }
    $pending_lead_per = 100 - $completed_lead_per;
}
$res['pending_files_count'] = $pending_files_count;
$res['pending_lead_per'] = $pending_lead_per;
$res['completed_lead_per'] = $completed_lead_per;
$res['lead_import_eta'] = $lead_import_eta;
echo json_encode($res);
dbConnectionClose();
exit;

?>
