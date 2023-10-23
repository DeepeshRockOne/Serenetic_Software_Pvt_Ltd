<?php
include_once __DIR__ . '/includes/connect.php';
include_once dirname(__DIR__) . '/includes/reporting_function.php';

$is_ajaxed = isset($_GET['is_ajaxed_org']) ? $_GET['is_ajaxed_org'] : '';
$incr = "";
$agent_id = checkIsset($_GET['agent_id']);
if($is_ajaxed){

    $searchArray = array();
    $join_range = isset($_GET['join_range_org']) ? $_GET['join_range_org']:"";
    $fromdate = isset($_GET["org_fromdate"]) ? $_GET["org_fromdate"]:"";
    $todate = isset($_GET["org_todate"]) ? $_GET["org_todate"]:"";

    $searchArray['type'] = $join_range ;
    $searchArray['getfromdate'] = $fromdate ;
    $searchArray['gettodate'] = $todate ;

    $product_res = getTopOrganizationSelling($agent_id,$searchArray);
    include_once 'tmpl/agent_organization_production_report.inc.php';
}else{
    include_once 'tmpl/agent_organization_production_report.inc.php';
}
?>