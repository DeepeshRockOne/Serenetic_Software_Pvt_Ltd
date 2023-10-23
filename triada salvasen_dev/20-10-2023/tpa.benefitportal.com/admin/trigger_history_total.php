<?php
include_once __DIR__ . '/includes/connect.php';

$sch_params = array();

$id = $_GET['id'];
$user_type = $_GET['user_type'];
$trigger_id = $_GET['trigger_com_his_trigger_id'];  
$custom_date = $_GET['trigger_com_his_custom_date'];
$fromdate = $_GET["trigger_com_his_fromdate"];
$todate = $_GET["trigger_com_his_todate"];

$incr ="";
switch ($custom_date) {
  case "Today":
    $fromdate = date('m/d/Y');
    $todate = date('m/d/Y');
    break;
  case "Yesterday":
    $fromdate = date("m/d/Y", strtotime("-1 days"));
    $todate = date('m/d/Y', strtotime("-1 days"));
    break;
  case "Last7Days":
    $fromdate = date('m/d/Y', strtotime("-7 day"));
    $todate = date('m/d/Y', strtotime("-1 day"));
    break;
  case "ThisMonth":
    $fromdate = date('m/01/Y');
    $todate = date('m/d/Y');
    break;
  case "LastMonth":
    $fromdate = date('m/d/Y', strtotime(date('Y-m')." -1 month"));
    $todate = date('m/d/Y', strtotime(date('Y-m')." last day of -1 month"));
    break;
  case "ThisYear":
    $fromdate = date('01/01/Y');
    $todate = date('m/d/Y');
    break;
}


if ($fromdate != "") {
  $sch_params[':from_date'] = date('Y-m-d', strtotime($fromdate));
  $incr.=" AND DATE(tl.created_at) >= :from_date ";
}

if ($todate != "") {
  $sch_params[':to_date'] = date('Y-m-d', strtotime($todate));
  $incr.=" AND DATE(tl.created_at) <= :to_date ";
}


try {  
  $sel_sql = "SELECT count(*) as total FROM $LOG_DB.trigger_log as tl
              JOIN $DATABASENAME.triggers as tg ON tg.id = tl.trigger_id
              WHERE tl.rep_id = $id $incr";
    $fetch_rows = $pdo->selectOne($sel_sql,$sch_params);
    $total_rows = $fetch_rows['total'];
} catch (Exception $e) {
  echo $e;
  exit();
}

$res['status'] = 'success';
$res['total_groups'] = floor($total_rows/5);
header('Content-Type: application/json');
echo json_encode($res);
dbConnectionClose();
exit;
?>