<?php
include_once __DIR__ . '/includes/connect.php';
include_once dirname(__DIR__) . '/includes/reporting_function.php';

$is_ajaxed = isset($_GET['is_ajaxed_personal']) ? $_GET['is_ajaxed_personal'] : '';
$incr = "";
$agent_id = checkIsset($_GET['agent_id']);
if($is_ajaxed){

    $searchArray = array();
    $join_range = isset($_GET['join_range_personal']) ? $_GET['join_range_personal']:"";
    $fromdate = isset($_GET["fromdate_personal"]) ? $_GET["fromdate_personal"]:"";
    $todate = isset($_GET["todate_personal"]) ? $_GET["todate_personal"]:"";

    $searchArray['type'] = $join_range ;
    $searchArray['getfromdate'] = $fromdate ;
    $searchArray['gettodate'] = $todate ;

    $product_res = getTopSelling($agent_id,$searchArray);
    include_once 'tmpl/agent_report_personal_production.inc.php';
}else{
    include_once 'tmpl/agent_report_personal_production.inc.php';
}
?>