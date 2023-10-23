<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
group_has_access(10);
$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Resources';
$breadcrumbes[2]['title'] = 'Product Information';
$breadcrumbes[1]['link'] = 'product_informations.php';

$selProducts = "SELECT apr.product_id,apr.agent_id,
                apr.id AS apr_id,pm.name as name,pm.product_code as product_code,pm.id AS pid,pm.pricing_model AS pricing_model,apr.product_billing_type,sp.url,sp.id as sp_id
                FROM agent_product_rule apr
                LEFT JOIN prd_main pm ON(pm.id=apr.product_id AND pm.status!='Inactive' AND pm.type!='Fees' AND pm.product_type='Group Enrollment' AND pm.is_deleted='N' )
                LEFT JOIN sub_provider sp ON(sp.product_id=(IF(pm.parent_product_id > 0,pm.parent_product_id,pm.id)) and sp.is_deleted='N')
                WHERE apr.agent_id=:group_id AND apr.is_deleted='N' 
                GROUP BY apr.product_id ORDER BY FIELD(pm.status,'Active','Pending','Suspended','Extinct'),FIELD(apr.status,'Contracted','Pending Approval','Suspended','Extinct'),pm.name ASC";
$resProducts = $pdo->select($selProducts,array(":group_id"=>$_SESSION['groups']['id']));
// pre_print($resProducts);
$template = 'product_informations.inc.php';
include_once 'layout/end.inc.php';
?>

