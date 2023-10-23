<?php
include_once __DIR__ . '/includes/connect.php';
include_once dirname(__DIR__) . '/includes/reporting_function.php';

$is_ajaxed_per_agent = isset($_GET['is_ajaxed_per_agent']) ? $_GET['is_ajaxed_per_agent'] : '';
$agent_id = checkIsset($_GET['agent_id']);

$viewPerAgentSales = !empty($_GET["viewPerAgentSales"]) ? $_GET["viewPerAgentSales"] : 'todaySales';
$today = date("m/d/Y");

$agent_id_production = checkIsset($_GET['agent_id_production']) !='' ? $_GET['agent_id_production'] : checkIsset($_GET['agent_id']);

if($is_ajaxed_per_agent){
    
    $select_downline = isset($_GET['select_downline']) ? $_GET['select_downline'] : 0;
    if(!empty($select_downline)){
        $agent_id = $select_downline;
    }

    $searchArray = array();
    $join_range_per_agent = isset($_GET['join_range_per_agent']) ? $_GET['join_range_per_agent']:"";
    $added_date_per_agent = isset($_GET["added_date_per_agent"]) ? $_GET["added_date_per_agent"]: $today;

    $fromdate_per_agent = isset($_GET["fromdate_per_agent"]) ? $_GET["fromdate_per_agent"]:"";
    $todate_per_agent = isset($_GET["todate_per_agent"]) ? $_GET["todate_per_agent"]:"";

    if($viewPerAgentSales == "todaySales" && empty($join_range_per_agent)){
      $join_range_per_agent = "exactly";
    }

    if($join_range_per_agent != "range"){
        $fromdate_per_agent = $added_date_per_agent;
    }

    $searchArray['type'] = $join_range_per_agent;
    $searchArray['getfromdate'] = $fromdate_per_agent;
    $searchArray['gettodate'] = $todate_per_agent ;
    $product_res = getTopOrganizationSelling($agent_id,$searchArray);

    $agent_name = $pdo->selectOne("SELECT CONCAT(c.fname,' ',c.lname) as name,cs.company_name as agency_name,c.rep_id,cs.agent_coded_level from customer c LEFT JOIN customer_settings cs on(cs.customer_id=c.id) where c.is_deleted='N' and c.id=:id",array(":id"=>$select_downline));

    $sch_param[':agent_id'] = '%,' . makeSafe($agent_id_production) . ',%';
    $agents = $pdo->select("SELECT c.id,c.rep_id,CONCAT(c.fname,' ',c.lname) as name,cs.company_name as agency_name,cs.agent_coded_id,cs.agent_coded_level from customer c LEFT JOIN customer_settings cs on (cs.customer_id = c.id) where c.is_deleted='N'  and status='Active' AND c.upline_sponsors LIKE :agent_id and c.type='Agent'",$sch_param);
    foreach($agents as $key => $val){
        if($val['agent_coded_id']!='')
        {
            $levels[$val['agent_coded_level']][] = $val;
        }
    }
    include_once 'tmpl/agent_per_agent_production_report.inc.php';
}else{
    include_once 'tmpl/agent_per_agent_production_report.inc.php';
}
?>