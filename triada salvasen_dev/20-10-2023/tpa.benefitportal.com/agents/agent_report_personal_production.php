<?php
include_once __DIR__ . '/includes/connect.php';
include_once dirname(__DIR__) . '/includes/reporting_function.php';

$is_ajaxed_personal = isset($_GET['is_ajaxed_personal']) ? $_GET['is_ajaxed_personal'] : '';
$incr = "";
$agent_id = checkIsset($_GET['agent_id']);
$viewPersonalSales  = !empty($_GET["viewPersonalSales "]) ? $_GET["viewPersonalSales "] : 'todaySales';
$today = date("m/d/Y");
if($is_ajaxed_personal){

    $searchArray = array();
    $join_range_personal = isset($_GET['join_range_personal']) ? $_GET['join_range_personal']:"";
    $added_date_personal = isset($_GET["added_date_personal"]) ? $_GET["added_date_personal"]: $today;

    $fromdate_personal = isset($_GET["fromdate_personal"]) ? $_GET["fromdate_personal"]:"";
    $todate_personal = isset($_GET["todate_personal"]) ? $_GET["todate_personal"]:"";

    if($viewPersonalSales == "todaySales" && empty($join_range_personal)){
      $join_range_personal = "exactly";
    }

    if($join_range_personal != "range"){
        $fromdate_personal = $added_date_personal;
    }

    $searchArray['type'] = $join_range_personal;
    $searchArray['getfromdate'] = $fromdate_personal;
    $searchArray['gettodate'] = $todate_personal ;
    $product_res = getTopSelling($agent_id,$searchArray);
    include_once 'tmpl/agent_report_personal_production.inc.php';
    exit;
}else{
    include_once 'tmpl/agent_report_personal_production.inc.php';
}
?>