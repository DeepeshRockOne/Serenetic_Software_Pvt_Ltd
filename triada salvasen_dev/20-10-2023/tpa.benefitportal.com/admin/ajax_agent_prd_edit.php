<?php
/* this script is used to check agent product rule script is runnung or not */
include_once 'layout/start.inc.php';

$script_running_check = $pdo->selectOne("SELECT is_running FROM system_scripts WHERE `script_code` = 'agent_product_rule'");

$response['status'] = $script_running_check['is_running']=='Y'?'running':'not_running';

header('Content-type: application/json');
echo json_encode($response);
dbConnectionClose();
exit;
?>