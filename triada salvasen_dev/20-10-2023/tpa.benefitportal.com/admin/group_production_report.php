<?php
include_once __DIR__ . '/includes/connect.php';
include_once dirname(__DIR__) . '/includes/reporting_function.php';

$is_ajaxed = isset($_GET['is_ajaxed_group']) ? $_GET['is_ajaxed_group'] : '';
$incr = "";
$group_id = checkIsset($_GET['group_id']);
if($is_ajaxed){
    $group_id = checkIsset($_GET['group_id']);
    $searchArray = array();
    $join_range = isset($_GET['join_range_group']) ? $_GET['join_range_group']:"";
    $fromdate = isset($_GET["group_fromdate"]) ? $_GET["group_fromdate"]:"";
    $todate = isset($_GET["group_todate"]) ? $_GET["group_todate"]:"";

    $searchArray['type'] = $join_range ;
    $searchArray['getfromdate'] = $fromdate ;
    $searchArray['gettodate'] = $todate ;

    if(!empty($group_id)){
        $sch_param[':group_id'] = $group_id;
        $groups = $pdo->selectOne("SELECT c.id,c.rep_id,CONCAT(c.fname,' ',c.lname) as name from customer c where c.is_deleted='N' AND c.id=:group_id and c.type='Group'",$sch_param);
    }
    $sch_param[':agent_id'] = '%,' . makeSafe($agent_id) . ',%';
    unset($sch_param[':group_id']);
    $Group = $pdo->select("SELECT c.id,c.rep_id,CONCAT(c.fname,' ',c.lname) as name from customer c where c.is_deleted='N' AND c.upline_sponsors LIKE :agent_id and c.type='Group'",$sch_param);
    $product_res = getTopOrganizationSelling($group_id);
    include_once 'tmpl/group_production_report.inc.php';
}else{
    include_once 'tmpl/group_production_report.inc.php';
}
?>