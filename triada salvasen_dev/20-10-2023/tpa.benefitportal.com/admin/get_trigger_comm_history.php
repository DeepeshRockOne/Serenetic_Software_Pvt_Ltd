<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$sch_params = array();
$SortBy = "tl.created_at";
$SortDirection = "ASC";
$currSortDirection = "DESC";

$id = $_GET['id'];
$user_type = $_GET['user_type'];  
$content = $_POST['trigger_com_his_content'];
$custom_date = $_POST['trigger_com_his_custom_date'];
$fromdate = $_POST["trigger_com_his_fromdate"];
$todate = $_POST["trigger_com_his_todate"];

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

if($content != ""){
  $sch_params[':content'] = "%".  makeSafe($content)."%";
  $incr.=" AND (tg.title LIKE :content OR tg.email_subject LIKE :content )";
}  

$group_number = filter_var($_POST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH);
$items_per_group = 5;
$position = ($group_number * $items_per_group);

try {  
  $sel_sql = "SELECT tl.*,tg.title FROM $LOG_DB.trigger_log as tl
              JOIN $DATABASENAME.triggers as tg ON tg.id = tl.trigger_id
              WHERE tl.rep_id = $id $incr
              ORDER BY $SortBy $currSortDirection LIMIT $position,$items_per_group";
  
    $fetch_rows = $pdo->select($sel_sql,$sch_params);
    $total_rows = count($fetch_rows);
} catch (Exception $e) {
  echo $e;
  exit();
}
include_once 'tmpl/get_trigger_comm_history.inc.php';
?>