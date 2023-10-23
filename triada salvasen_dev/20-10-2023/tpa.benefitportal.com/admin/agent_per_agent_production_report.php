<?php
include_once __DIR__ . '/includes/connect.php';
include_once dirname(__DIR__) . '/includes/reporting_function.php';

$is_ajaxed = isset($_GET['is_ajaxed_production']) ? $_GET['is_ajaxed_production'] : '';
$incr = "";
$agent_id = checkIsset($_GET['agent_id']);
$agent_id_production = checkIsset($_GET['agent_id_production']) !='' ? $_GET['agent_id_production'] : checkIsset($_GET['agent_id']) ;
if($is_ajaxed){
    $searchArray = array();
    $select_downline = 0;
    $select_downline = isset($_GET['select_downline']) ? $_GET['select_downline']:"";

    if(!empty($select_downline)){
        $agent_id = $select_downline;
    }

    $searchArray = array();
    $join_range = isset($_GET['join_range_per_per']) ? $_GET['join_range_per_per']:"";
    $fromdate = isset($_GET["per_per_fromdate"]) ? $_GET["per_per_fromdate"]:"";
    $todate = isset($_GET["per_per_todate"]) ? $_GET["per_per_todate"]:"";

    $searchArray['type'] = $join_range ;
    $searchArray['getfromdate'] = $fromdate ;
    $searchArray['gettodate'] = $todate ;

    $agent_name = $pdo->selectOne("SELECT CONCAT(c.fname,' ',c.lname) as name,cs.company_name as agency_name,c.rep_id,cs.agent_coded_level from customer c LEFT JOIN customer_settings cs on(cs.customer_id=c.id) where c.is_deleted='N' and c.id=:id",array(":id"=>$select_downline));

    $product_res = getTopOrganizationSelling($agent_id,$searchArray);
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