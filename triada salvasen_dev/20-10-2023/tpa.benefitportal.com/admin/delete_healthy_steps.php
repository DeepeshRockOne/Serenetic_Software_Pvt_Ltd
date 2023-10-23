<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$product_id = checkIsset($_GET['product_id']);
$health_id = checkIsset($_GET['health_id']);
$variation_porducts = array();
if($product_id!=''){
    $query = "SELECT name,product_code FROM prd_main WHERE id = :id and is_deleted='N'";
    $healthy_row = $pdo->selectOne($query, array(":id"=>$product_id));

    $variation_porducts = $pdo->select("SELECT pf.id as pfid,pf.display_id,c.id as agent_id,c.fname,c.lname,c.rep_id,p.id
    from prd_fees pf
    JOIN prd_main p
    JOIN prd_assign_fees paf ON(pf.id=paf.prd_fee_id and paf.fee_id=p.id)
    JOIN agent_product_rule apr on(apr.product_id=paf.fee_id and apr.is_deleted='N')
    JOIN customer c ON(c.id=apr.agent_id and c.type='Agent' and c.is_deleted='N')
    where setting_type='Healthy Step Variation' and pf.is_deleted='N' and p.is_deleted='N' and p.parent_product_id=:id group by pf.id",array(":id"=>$product_id));
}
$template = 'delete_healthy_steps.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>