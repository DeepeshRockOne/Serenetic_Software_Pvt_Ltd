<?php
include_once dirname(__DIR__) . "/cron_scripts/connect.php";
// generate schedule eligibility file request

$selScheduled = "SELECT * FROM eligibility_schedule WHERE is_deleted='N' GROUP BY id";
$getScheduled = $pdo->select($selScheduled);

if(!empty($getScheduled) && is_array($getScheduled)){
    foreach ($getScheduled as $schedule) {
        generate_eligibility_request($schedule['id']);
    }
}



echo "Complete";
dbConnectionClose();
exit;
?>