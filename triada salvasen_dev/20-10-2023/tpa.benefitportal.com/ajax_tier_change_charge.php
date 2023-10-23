<?php
include_once __DIR__ . '/includes/connect.php';
$ws_id = $_REQUEST['ws_id'];
$new_plan_id = $_REQUEST['plan_id'];
$tier_change_date = $_REQUEST['tier_change_date'];
$response = tier_change_charge($ws_id,$new_plan_id,$tier_change_date);
echo json_encode($response);