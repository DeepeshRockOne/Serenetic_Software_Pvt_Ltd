<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$page_title = "Advance Commissions Builder";
$advFeeId = checkIsset($_GET['advFeeId']);
$advRuleId = checkIsset($_GET['advRuleId']);

$incr = '';
$sch_params = array();


if(!empty($advRuleId)){
	$incr .= "AND md5(pf.id)=:prdFeeId";
	$sch_params[":prdFeeId"] = $advRuleId;
}

if(!empty($advFeeId)){
	$incr .= "AND md5(pa.fee_id)=:feeId";
	$sch_params[":feeId"] = $advFeeId;
}

$prdRes = array();
if(!empty($advRuleId) || !empty($advFeeId)){
	$prdSql = "SELECT pm.name,pm.product_code,pfm.advance_month
            FROM prd_assign_fees pa
			JOIN prd_main pm ON(pa.product_id=pm.id AND pm.is_deleted='N')
			LEFT JOIN prd_fees pf ON(pf.id=pa.prd_fee_id AND pf.is_deleted='N')
			LEFT JOIN prd_main pfm ON(pa.fee_id=pfm.id)
			WHERE pa.is_deleted='N' $incr GROUP BY pm.id ORDER BY pm.name ASC";
	$prdRes = $pdo->select($prdSql,$sch_params);
}

$template = 'advance_commission_product.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>