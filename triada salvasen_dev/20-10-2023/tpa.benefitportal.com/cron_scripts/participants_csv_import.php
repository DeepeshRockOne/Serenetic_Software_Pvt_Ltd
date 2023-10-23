<?php
include_once dirname(__DIR__) . "/cron_scripts/connect.php";
require_once dirname(__DIR__) . '/includes/participants.class.php';

$participantsObj = new Participants();
ini_set('memory_limit', '-1');
ini_set('max_execution_time', '-1');

$checkSql = "SELECT id FROM participants_csv WHERE status='Pending' AND is_running='Y' AND is_deleted='N'";
$checkRes = $pdo->selectOne($checkSql);
if (!empty($checkRes["id"])) {
    exit("Already Running");
}

$csvSql = "SELECT * FROM participants_csv WHERE status='Pending' AND is_running='N' AND is_deleted='N' LIMIT 0,3";
$csvRes = $pdo->select($csvSql);

if (!empty($csvRes)) {
    foreach ($csvRes as $fileRow) {
        $participantsObj->participants_csv_import($fileRow["id"]);
    }
    echo "All Processed..";
} else {
    exit("No import request found");
}
dbConnectionClose();


?>