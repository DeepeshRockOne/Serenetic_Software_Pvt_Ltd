<?php
include_once __DIR__ . '/includes/connect.php';
include_once dirname(__DIR__) . '/includes/reporting_function.php';

$is_ajaxed_group = isset($_GET['is_ajaxed_group']) ? $_GET['is_ajaxed_group'] : '';
$agent_id = checkIsset($_GET['agent_id']);

$viewGroupSales = !empty($_GET["viewGroupSales"]) ? $_GET["viewGroupSales"] : 'todaySales';
$today = date("m/d/Y");

if($is_ajaxed_group){

    $group_id = checkIsset($_GET['group_id']);
    
    $searchArray = array();
    $join_range_group = isset($_GET['join_range_group']) ? $_GET['join_range_group']:"";
    $added_date_group = isset($_GET["added_date_group"]) ? $_GET["added_date_group"]: $today;

    $fromdate_group = isset($_GET["fromdate_group"]) ? $_GET["fromdate_group"]:"";
    $todate_group = isset($_GET["todate_group"]) ? $_GET["todate_group"]:"";

    if($viewGroupSales == "todaySales" && empty($join_range_group)){
      $join_range_group = "exactly";
    }

    if($join_range_group != "range"){
        $fromdate_group = $added_date_group;
    }

    $searchArray['type'] = $join_range_group;
    $searchArray['getfromdate'] = $fromdate_group;
    $searchArray['gettodate'] = $todate_group;
    $product_res = getTopOrganizationSelling($group_id,$searchArray);


    if(!empty($group_id)){
        $sch_param[':group_id'] = $group_id;
        $groups = $pdo->selectOne("SELECT c.id,c.rep_id,CONCAT(c.fname,' ',c.lname) as name from customer c where c.is_deleted='N' AND c.id=:group_id and c.type='Group'",$sch_param);
    }
    $sch_param[':agent_id'] = '%,' . makeSafe($agent_id) . ',%';
    unset($sch_param[':group_id']);
    $group = $pdo->select("SELECT c.id,c.rep_id,CONCAT(c.fname,' ',c.lname) as name,c.business_name from customer c where c.is_deleted='N' AND c.upline_sponsors LIKE :agent_id and c.type='Group'",$sch_param);
    
    include_once 'tmpl/agent_per_group_production_report.inc.php';
}else{
    include_once 'tmpl/agent_per_group_production_report.inc.php';
}
?>