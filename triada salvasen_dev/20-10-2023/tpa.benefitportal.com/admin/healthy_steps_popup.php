<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$healthy_step = array();
$agent_id = checkIsset($_GET['agent_id']);
$agent_name = checkIsset($_GET['agent_name']);
$fee_id = checkIsset($_GET['fee_ids']);
$type = checkIsset($_GET['type']);
$ids = array();
if(!empty($fee_id)){
    $ids = explode(',',$fee_id);
}
if($type=='show' || $type=='edit'){

    $healthy_step = $pdo->select("SELECT p.id as id,pf.id as health_id,GROUP_CONCAT(DISTINCT (paf.product_id)) as products,pp.name,p.product_code as stepPrdCode,p.product_code,pm.pricing_effective_date,pm.price,pm.commission_amount
    FROM prd_main p
    JOIN prd_main pp ON(pp.id=p.parent_product_id and pp.is_deleted='N')
    LEFT JOIN prd_matrix pm ON(pm.product_id=p.id and pm.is_deleted='N')
    LEFT JOIN prd_assign_fees paf ON(paf.fee_id=p.id  and paf.is_deleted='N')
    LEFT JOIN prd_fees pf ON(paf.prd_fee_id = pf.id and pf.setting_type='Healthy Step Variation'  and pf.is_deleted='N')
    WHERE p.type='Fees' AND p.product_type='Healthy Step' AND p.record_type='Variation' and p.is_deleted='N'  AND p.id IN($fee_id)  GROUP BY p.id");
    
    $productSql="SELECT p.id,p.name,p.product_code,p.type,c.title 
                FROM prd_main p 
                LEFT JOIN prd_category c ON (c.id = p.category_id)
                where p.type!='Fees' AND p.is_deleted='N' and p.status='Active' ORDER BY c.title,p.name ASC";
    $productRes = $pdo->selectGroup($productSql,array(),'title');
}else{
    
    if($agent_name != ''){
    $healthy_step = $pdo->select("SELECT p.id as id,pf.id as health_id,GROUP_CONCAT(DISTINCT (paf.product_id)) as products,p.name,p.product_code as stepPrdCode,p.product_code,pm.pricing_effective_date,pm.price,pm.commission_amount
    FROM prd_main p
    LEFT JOIN prd_matrix pm ON(pm.product_id=p.id  and pm.is_deleted='N')
    LEFT JOIN prd_assign_fees paf ON(paf.fee_id=p.id and paf.is_deleted='N')
    LEFT JOIN prd_fees pf ON(paf.prd_fee_id = pf.id and pf.setting_type='Healthy Step Variation'  and pf.is_deleted='N')
    WHERE p.type='Fees' AND p.product_type='Healthy Step' AND p.record_type='Primary' and p.is_deleted='N' GROUP BY p.id");
    
    $productSql="SELECT p.id,p.name,p.product_code,p.type,c.title 
                FROM prd_main p 
                LEFT JOIN prd_category c ON (c.id = p.category_id)
                where p.type!='Fees' AND p.is_deleted='N' and p.status='Active' ORDER BY c.title,p.name ASC";
    $productRes = $pdo->selectGroup($productSql,array(),'title');
    }
}

$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache);
$exJs = array('thirdparty/multiple-select-master/multiple-select-old/jquery.multiple.select.js'.$cache);

$template = "healthy_steps_popup.inc.php";
include_once 'layout/iframe.layout.php';
?>