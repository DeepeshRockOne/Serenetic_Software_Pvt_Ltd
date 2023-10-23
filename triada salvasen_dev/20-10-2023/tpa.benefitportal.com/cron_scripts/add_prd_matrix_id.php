<?php
include_once dirname(__DIR__) . "/cron_scripts/connect.php";

// This Script using for prd_matrix_id is added on existing vendor fee and carrier fee.

$priceSql = "SELECT fee_p.product_type,pm.matrix_group AS prod_matrix,prdMat.matrix_group AS fee_matrix,prdMat.price,
prdMat.id AS feeMatrixId,pmc.prd_matrix_id,pm.pricing_effective_date,pm.pricing_termination_date,pm.product_id,
p.name AS product_name,p.product_code,pm.plan_type,pm.price as retail_price,pm.non_commission_amount,
pm.commission_amount,pm.pricing_model,pm.is_new_price_on_renewal
FROM prd_matrix pm
JOIN prd_main p ON (p.id=pm.product_id)
LEFT JOIN prd_plan_type ppt ON(pm.plan_type=ppt.id)
JOIN prd_matrix_criteria pmc ON (pmc.prd_matrix_id = pm.id AND pmc.is_deleted='N')
LEFT JOIN prd_fee_pricing_model pfm ON(pm.id=pfm.prd_matrix_id AND pfm.is_deleted='N')
LEFT JOIN prd_matrix prdMat ON(prdMat.id=pfm.prd_matrix_fee_id AND prdMat.is_deleted='N')
JOIN prd_main fee_p ON (fee_p.id=prdMat.product_id AND fee_p.product_type IN ('Vendor','Carrier'))
WHERE pm.is_deleted='N' AND pm.pricing_model != 'FixedPrice' AND prdMat.matrix_group = '' ORDER BY pm.product_id";
$priceRes = $pdo->select($priceSql);

if(!empty($priceRes)){
    foreach($priceRes as $val){
        if(!empty($val['prd_matrix_id']) && !empty($val['feeMatrixId'])){
            $prod_matrix_grp = $val['prod_matrix'];
            $fee_matrix_id = $val['feeMatrixId'];
            $update_data = array(
                "matrix_group" => $prod_matrix_grp
            );
            $update_where = array("clause" => "id=:id", "params" => array(":id" => $fee_matrix_id));
            $pdo->update("prd_matrix", $update_data, $update_where);
        }
    }
}
echo "Completed";
dbConnectionClose();
exit();


?>