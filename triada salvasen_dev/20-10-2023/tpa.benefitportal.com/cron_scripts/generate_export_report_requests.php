<?php
ini_set('memory_limit', '-1');
ini_set('max_execution_time',0);
include_once dirname(__DIR__) . "/cron_scripts/connect.php";
// generate schedule report export request
$today = date('Y-m-d H:i:s');
echo "Today Date ".$today;
echo "<br/>";
$selScheduled = "SELECT * FROM $REPORT_DB.rps_reports_schedule WHERE is_deleted='N' AND cancel_processing='N' GROUP BY id";
$getScheduled = $pdo->select($selScheduled);
if(!empty($getScheduled) && is_array($getScheduled)){
    foreach ($getScheduled as $schedule) {
        generate_report_request($schedule['id']);
    }
}
echo "Complete";
$DEFAULT_ORDER_EMAIL = array("shailesh@cyberxllc.com");
trigger_mail_to_email("Generate Export Report Request", $DEFAULT_ORDER_EMAIL,"Op29 : Generate Export Report Request");
dbConnectionClose();
exit;
?>