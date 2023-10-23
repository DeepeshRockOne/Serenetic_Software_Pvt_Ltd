<?php
include_once __DIR__ . '/includes/connect.php';
include_once __DIR__ . '/includes/function.class.php';
$functionsList = new functionsList();

$profile_id = !empty($_GET["profile_id"]) ? $_GET["profile_id"] : 1;
$agent_id = !empty($_GET["agent_id"]) ? $_GET["agent_id"] : 1;
$plan_types_result = $prdPlanTypeArray;

$products_sql = "SELECT p.id AS product_id,p.product_code,p.name as ProName,p.type,p.parent_product_id,cr.commission_on,cr.commission_json
				FROM prd_main p
				JOIN agent_product_rule rp ON (rp.product_id=p.id AND rp.is_deleted='N' AND rp.status='Contracted' AND rp.agent_id=:agent_id)
				LEFT JOIN agent_commission_rule acr ON(acr.agent_id=rp.agent_id AND IF(acr.agent_id=1 AND acr.id is null,acr.product_id=p.parent_product_id,acr.product_id=p.id) AND acr.is_deleted='N')
				LEFT JOIN commission_rule cr ON(cr.id=acr.commission_rule_id AND cr.is_deleted='N')
				WHERE p.is_deleted='N' AND p.status='Active' AND p.type!='Fees' AND p.product_type != 'Admin Only Product'
				ORDER BY p.name ASC";
$products_result = $pdo->select($products_sql, array(":agent_id" => $agent_id));

$product_wise_commissions = array();

$uplineSql = "SELECT cs.agent_coded_id FROM customer c JOIN customer_settings cs ON(cs.customer_id=c.id) WHERE c.type = 'Agent' AND c.id=:id";
$uplineRes = $pdo->selectOne($uplineSql,array(":id"=>$agent_id));
$agent_level_incr='';
$agent_level_incr .= " id <= :upline_level";
$agent_level_params[':upline_level'] = $uplineRes['agent_coded_id'];
$sel_level = $pdo->selectOne("SELECT group_concat(level) as levels FROM agent_coded_level WHERE $agent_level_incr AND is_active='Y' order by id desc",$agent_level_params);
$levels = explode(",",$sel_level['levels']);
$planArr=array();
if(!empty($plan_types_result)){
	foreach ($plan_types_result as $key => $value) {
		$planArr[$value['id']]=$value['title'];
	}
}
$show_tr = false;

if (!empty($products_result)) {
	foreach ($products_result as $product_row) {

				if ($product_row['commission_on'] == "Plan") {
					$show_tr = true;
					$commission_plan_level = json_decode($product_row['commission_json'], true);
					foreach ($commission_plan_level as $ppt_id => $plan_commissions) {
						
						$product_wise_commissions[$product_row['product_id']][$ppt_id] = array(
							'commission_on' => $product_row['commission_on'],
							'product_id' => $product_row['product_id'],
							'product_name' => $product_row['ProName'],
							'product_code' => !empty($product_row['product_code']) ? $product_row['product_code'] : "",
							'plan_name' => $planArr[$ppt_id],
							'plan_commissions' => $plan_commissions,
						);
					}
				} else {
					// foreach ($plan_types_result as $plan_types_row) {
						$product_wise_commissions[$product_row['product_id']][$product_row['ProName']] = array(
							'commission_on' => $product_row['commission_on'],
							'product_id' => $product_row['product_id'],
							'product_name' => $product_row['ProName'],
							'product_code' => !empty($product_row['product_code']) ? $product_row['product_code'] : "",
							'plan_commissions' => json_decode($product_row['commission_json'], true),
						);
					// }
				}
	}
}



$template = 'view_commission_rule_info.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>