<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$id = $_GET['activity'];
$type = $_GET['type'];

$sel_sql = "SELECT extra FROM activity_feed  WHERE md5(id)= :id";
$res_sql = $pdo->selectOne($sel_sql, array(":id" => $id));


$commission_json = json_decode($res_sql['extra'], true);
$commission_jsonType = $commission_json[$type.'Type'];
$commission_json = json_decode($commission_json[$type],true);


$template = 'activity_commission_view.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>