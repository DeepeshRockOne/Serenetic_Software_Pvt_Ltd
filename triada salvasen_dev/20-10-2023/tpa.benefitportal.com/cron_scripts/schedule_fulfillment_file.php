<?php
include_once dirname(__DIR__) . "/cron_scripts/connect.php";
// generate schedule Fullfillment file request

$selScheduled = "SELECT * FROM fulfillment_schedule WHERE is_deleted='N' GROUP BY id";
$getScheduled = $pdo->select($selScheduled);

if(!empty($getScheduled) && is_array($getScheduled)){
    foreach ($getScheduled as $schedule) {
        generate_fulfillment_request($schedule['id']);
    }
}

echo "Complete";
dbConnectionClose();
exit;
?>