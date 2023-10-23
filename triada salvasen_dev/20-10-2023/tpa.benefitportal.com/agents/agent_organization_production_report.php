<?php
include_once __DIR__ . '/includes/connect.php';
include_once dirname(__DIR__) . '/includes/reporting_function.php';

$is_ajaxed_org = isset($_GET['is_ajaxed_org']) ? $_GET['is_ajaxed_org'] : '';
$incr = "";
$agent_id = checkIsset($_GET['agent_id']);
$viewOrgSales  = !empty($_GET["viewOrgSales"]) ? $_GET["viewOrgSales"] : 'todaySales';
$today = date("m/d/Y");

if($is_ajaxed_org){

    $searchArray = array();
    $join_range_org = isset($_GET['join_range_org']) ? $_GET['join_range_org']:"";
    $added_date_org = isset($_GET["added_date_org"]) ? $_GET["added_date_org"]: $today;

    $fromdate_org = isset($_GET["fromdate_org"]) ? $_GET["fromdate_org"]:"";
    $todate_org = isset($_GET["todate_org"]) ? $_GET["todate_org"]:"";

    if($viewOrgSales == "todaySales" && empty($join_range_org)){
      $join_range_org = "exactly";
    }

    if($join_range_org != "range"){
        $fromdate_org = $added_date_org;
    }

    $searchArray['type'] = $join_range_org;
    $searchArray['getfromdate'] = $fromdate_org;
    $searchArray['gettodate'] = $todate_org ;
    $product_res = getTopOrganizationSelling($agent_id,$searchArray);
    include_once 'tmpl/agent_organization_production_report.inc.php';
}else{
    include_once 'tmpl/agent_organization_production_report.inc.php';
}
?>