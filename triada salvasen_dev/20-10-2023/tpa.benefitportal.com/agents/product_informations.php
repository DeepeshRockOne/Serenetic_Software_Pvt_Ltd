<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'My Production';
$breadcrumbes[2]['title'] = 'Products';
$breadcrumbes[1]['link'] = 'product_informations.php';

$selProducts = "SELECT apr.product_id,apr.agent_id,
                apr.id AS apr_id,pm.name as name,pm.product_code as product_code,pm.id AS pid,pm.pricing_model AS pricing_model,sp.url,sp.id as sp_id,pc.title as prdCategory,pm.product_type as enrollmentMethod,cr.commission_json,cr.commission_on,MIN(prm.plan_type) AS min_plans
                FROM agent_product_rule apr
                LEFT JOIN prd_main pm ON(pm.id=apr.product_id AND pm.status!='Inactive' AND pm.type!='Fees' AND pm.is_deleted='N' AND pm.product_type!='Admin Only Product') 
                LEFT JOIN sub_provider sp ON(sp.product_id=(IF(pm.parent_product_id > 0,pm.parent_product_id,pm.id)) and sp.is_deleted='N')
                LEFT JOIN prd_category pc ON(pm.category_id=pc.id)
                LEFT JOIN prd_matrix prm ON(prm.product_id = pm.id AND prm.is_deleted='N')
                LEFT JOIN agent_commission_rule acr ON(acr.agent_id= apr.agent_id AND acr.product_id=apr.product_id AND acr.is_deleted='N')
                LEFT JOIN commission_rule cr ON(cr.id= acr.commission_rule_id AND cr.is_deleted='N')
                WHERE pm.name != '' AND apr.agent_id=:agent_id AND apr.is_deleted='N' 
                GROUP BY apr.product_id ORDER BY FIELD(pm.status,'Active','Pending','Suspended','Extinct'),FIELD(apr.status,'Contracted','Pending Approval','Suspended','Extinct'),pc.title,pm.name ASC";
$resProducts = $pdo->select($selProducts,array(":agent_id"=>$_SESSION['agents']['id']));
$level=$_SESSION['agents']['agent_coded_level'];
$desc = array();
$desc['ac_message'] = array(
    'ac_red_1' => array(
        'href' => 'agent_detail_v1.php?id=' . md5($_SESSION['agents']['id']),
        'title' => $_SESSION['agents']['rep_id'],
    ),
    'ac_message_1' => ' read Products'
);
$desc = json_encode($desc);
activity_feed(3,$_SESSION['agents']['id'],'Agent',$_SESSION['agents']['id'],'Agent','Agent Read Products.',$_SESSION['agents']['fname'],$_SESSION['agents']['lname'],$desc);

$template = 'product_informations.inc.php';
include_once 'layout/end.inc.php';
?>

