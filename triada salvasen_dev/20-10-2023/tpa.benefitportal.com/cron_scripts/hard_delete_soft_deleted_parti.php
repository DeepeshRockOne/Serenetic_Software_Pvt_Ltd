<?php
ini_set('memory_limit', '-1');
ini_set('max_execution_time', '-1');
include_once dirname(__DIR__) . "/cron_scripts/connect.php";
$pdo->delete("DELETE FROM participants WHERE is_deleted='Y' AND id IN(SELECT participant_id FROM deleted_participants)");
$pdo->delete("DELETE FROM participants_products WHERE is_deleted='Y' AND participants_id IN(SELECT participant_id FROM deleted_participants)");
dbConnectionClose();
echo "Completed";
exit;
?>