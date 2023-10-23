<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$product_id = $_GET['product_id'];
$agent_id = $_GET['agent_id'];
$levelHeading = isset($agentCodedRes[$_GET['level_id']]['level_heading']) ? $agentCodedRes[$_GET['level_id']]['level_heading'] : '';
$agentCommRuleRow  = $pdo->selectOne("SELECT pm.id as id,cs.agent_coded_profile,acr.product_id,pm.name,prm.plan_type,prm.price,pm.product_code,cr.commission_on,cr.commission_json,prm.commission_amount,cr.rule_code,cr.id as cr_id
    FROM agent_commission_rule acr
    LEFT JOIN customer_settings cs ON(cs.customer_id=acr.agent_id)
    LEFT JOIN prd_main pm ON(pm.id=acr.product_id AND pm.is_deleted='N' AND pm.status='Active') 
    LEFT JOIN prd_matrix prm ON(prm.product_id = pm.id AND prm.is_deleted='N')
    LEFT JOIN commission_rule cr ON(cr.id = acr.commission_rule_id AND cr.is_deleted='N')
    WHERE acr.product_id=:product_id AND md5(acr.agent_id)=:agent_id AND acr.is_deleted='N'
    ",array(":product_id"=>$product_id,":agent_id"=>$agent_id));

$agent_prd = $pdo->select("SELECT p.id as id,p.name from prd_main p LEFT JOIN agent_product_rule apr ON(apr.product_id = p.id) where md5(agent_id) = :agent_id ",array(":agent_id"=>$agent_id));
if(empty($agentCommRuleRow)){
    $agentCommRuleRow = $pdo->selectOne("SELECT name,product_code,id from prd_main where id=:id",array(":id"=>$product_id));
}
$prdPlanSql = "SELECT pm.price,pm.id,pm.commission_amount,pm.plan_type
          FROM prd_main p
          JOIN prd_matrix pm ON pm.product_id=p.id
          where p.id=:id AND pm.is_deleted='N'";
$prdPlanRow = $pdo->select($prdPlanSql, array(":id" => $product_id));
$upline_sponsors = get_upline($agent_id,$product_id);
$displayAgentLevelArr1 = $displayAgentLevelArr = array();
$original_price_arr = array();
{
	foreach ($agentCodedRes as $coded) {
		$displayAgentLevelArr1[$coded['id']] = $coded;
	}
	$AgentCommRuleLevelArr = array();
	if ($agentCommRuleRow) {
		foreach ($displayAgentLevelArr1 as $lvl_id => $level1) {
			if(checkIsset($agentCommRuleRow['commission_on']) == 'Plan'){
				$AgentCommRuleLevelArr = json_decode($agentCommRuleRow['commission_json'], true);
				$tmpArr = array();
				//assigning current rule to upline agents
				foreach ($upline_sponsors as $user) {
					if (isset($agentCodedRes[$user['agent_coded_id']]['level']) && $agentCodedRes[$user['agent_coded_id']]['level'] == $level1['level']) {
						$tmpArr = $user;
						$tmpArr['advance_commission'] = $user['is_on'] == 'Y' ? 'Y' : 'N';
						$tmpArr['advance_months'] = $user['advance_month'];
					}
				}
                $tmpArr['commission'] = array();
				foreach ($prdPlanRow as $plan) {
					$tmpArr['commission'][$prdPlanTypeArray[$plan['plan_type']]['title']] = $AgentCommRuleLevelArr[$plan['plan_type']][$level1['level']];
					$tmpArr['commission'][$prdPlanTypeArray[$plan['plan_type']]['title']]['price'] = $plan['commission_amount'];
					$original_price_arr[$prdPlanTypeArray[$plan['plan_type']]['title']]['commission_price'] =$plan['commission_amount'];
				}
				$displayAgentLevelArr[$level1['level_heading']] = $tmpArr;
			}else{
				$AgentCommRuleLevelArr = json_decode($agentCommRuleRow['commission_json'], true);
				$tmpArr = array();
				//assigning current rule to upline agents
				foreach ($upline_sponsors as $user) {
					if (isset($agentCodedRes[$user['agent_coded_id']]['level']) && $agentCodedRes[$user['agent_coded_id']]['level'] == $level1['level']) {
						$tmpArr = $user;
						$tmpArr['advance_months'] = $user['advance_month'];
					}
				}

                $tmpArr['commission'] = array();
				foreach ($prdPlanRow as $plan) {
					$tmpArr['commission'][$prdPlanTypeArray[$plan['plan_type']]['title']] = $AgentCommRuleLevelArr[$level1['level']];
					$tmpArr['commission'][$prdPlanTypeArray[$plan['plan_type']]['title']]['price'] = $plan['commission_amount'];
					$original_price_arr[$prdPlanTypeArray[$plan['plan_type']]['title']]['commission_price'] =$plan['commission_amount'];
				}

				$displayAgentLevelArr[$level1['level_heading']] = $tmpArr;
			}
		}
	}

	//re-calculating commission % based on commision distribution
	$displayAgentLevelArr = array_reverse($displayAgentLevelArr);
	$tmp_total = array();
	foreach ($displayAgentLevelArr as $key1 => $tmp) {
    
		foreach ($tmp['commission'] as $key2 => $tmp2) {
			if (!isset($tmp_total[$key2])) {
				$tmp_total[$key2] = 0;
            }
			$displayAgentLevelArr[$key1]['commission'][$key2]['original_amount'] = $tmp2['amount'];
			$displayAgentLevelArr[$key1]['commission'][$key2]['amount'] = 0;
			if ($tmp2['amount_type'] == 'Percentage' && $tmp2['amount'] > 0  && isset($tmp['rep_id']) != '' ) {
				$per_clc = $tmp2['amount'] - $tmp_total[$key2];
				$tmp_total[$key2] = $tmp_total[$key2] + $per_clc;
				$displayAgentLevelArr[$key1]['commission'][$key2]['amount'] = $per_clc;
			}
        }
	}
    $displayAgentLevelArr = array_reverse($displayAgentLevelArr);
}

function get_upline($id, $product_id, $upArr = array()) {
	global $pdo;
	$ids = array();
	if ($id != '') {
		
		$sel = "SELECT cs.advance_on as is_on,c.id,c.rep_id,cs.agent_coded_level,cs.agent_coded_id,c.fname,c.lname,md5(c.sponsor_id) as sponsor_id,c.business_name,adv_comm.advance_month,adv_comm.product_id
        FROM customer c
        LEFT JOIN customer_settings cs ON(cs.customer_id=c.id)
        LEFT JOIN (SELECT pfd.advance_month,paf.product_id,pf.agent_id
                   FROM prd_assign_fees paf
                   JOIN prd_fees pf ON(paf.prd_fee_id=pf.id AND pf.is_deleted='N')
                   JOIN prd_main pfd ON(paf.fee_id=pfd.id AND pfd.is_deleted='N')
                   WHERE paf.is_deleted = 'N'
            ) AS adv_comm ON(adv_comm.agent_id = c.id AND adv_comm.product_id = :product_id)
        WHERE c.is_deleted='N' AND MD5(c.id)= :id";
		$arr = array(":id" => $id,":product_id"=>$product_id);
        $res = $pdo->selectOne($sel, $arr);
     
		if (count($res) > 0) {
			if(!isset($upArr[$res['agent_coded_id']])) {
				$upArr[$res['agent_coded_id']] = $res;
			}
			if ($res['agent_coded_level'] == 'IMO3') {
				return $upArr;
			} else {
				return get_upline($res['sponsor_id'], $product_id, $upArr);
			}
		}
	}
	return $upArr;
}

$template = 'agents_commission_level.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>