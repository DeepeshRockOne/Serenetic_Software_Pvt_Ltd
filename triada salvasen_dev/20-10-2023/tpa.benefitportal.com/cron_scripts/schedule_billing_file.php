<?php
include_once dirname(__DIR__) . "/cron_scripts/connect.php";
// generate schedule billing file request

$sqlSchedule = "SELECT id FROM billing_schedule WHERE is_deleted='N' GROUP BY id";
$resSchedule = $pdo->select($sqlSchedule);

if(!empty($resSchedule)){
    foreach ($resSchedule as $schedule) {
        generate_billing_request($schedule['id']);
    }
}



echo "Complete";
dbConnectionClose();
exit;
?>