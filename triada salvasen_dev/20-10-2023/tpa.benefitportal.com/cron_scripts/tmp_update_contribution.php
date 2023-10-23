<?php
ini_set('memory_limit', '-1');
ini_set('max_execution_time', '-1');
include_once dirname(__DIR__) . "/cron_scripts/connect.php";
//Select group coverage offering periods and product details
$sel_group = "SELECT gco.id as offering_id,gco.group_id,gco.group_coverage_period_id,gco.class_id,gco.is_contribution,
              p.id as product_id,p.name,p.product_code,p.pricing_model,pm.id AS plan_id
              FROM group_coverage_period_offering gco
              JOIN prd_main p ON(FIND_IN_SET(p.id,gco.products) AND p.is_deleted='N')
              JOIN prd_matrix pm ON (p.id=pm.product_id AND pm.is_deleted='N')
              WHERE gco.is_deleted='N' AND gco.is_contribution='N'";
$groupContribution  = $pdo->select($sel_group);

if(!empty($groupContribution)){
    foreach($groupContribution as $contribution){
        $sqlCheck = "SELECT id FROM group_coverage_period_contributions WHERE group_coverage_period_offering_id=:id AND is_deleted='N' AND group_coverage_period_id=:coverage_id AND product_id=:product_id AND plan_id=:plan_id AND group_id=:group_id";
        $whrCheck = array(
            ":group_id"=>$contribution['group_id'],
            ":id"=>$contribution['offering_id'],
            ":coverage_id"=>$contribution['group_coverage_period_id'],
            ":product_id"=>$contribution['product_id'],
            ":plan_id"=>$contribution['plan_id'],
        );
        $resCheck = $pdo->selectOne($sqlCheck,$whrCheck);
        if(empty($resCheck['id'])){
            $params = array(
                'group_id'=>$contribution['group_id'],
                'group_coverage_period_id'=>$contribution['group_coverage_period_id'],
                'group_coverage_period_offering_id'=>$contribution['offering_id'],
                'class_id'=>$contribution['class_id'],
                'product_id'=>$contribution['product_id'],
                'plan_id'=>$contribution['plan_id'],
                'type'=>'Percentage',
                'con_value'=>0,
            );
            $pdo->insert("group_coverage_period_contributions",$params);
        }
    }
}
dbConnectionClose();