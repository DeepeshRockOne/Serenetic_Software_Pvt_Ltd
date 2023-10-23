<?php
$IMPORTDB='BENEFITS_PORTAL';

include_once dirname(__DIR__) . "/includes/other_connect.php";

ini_set('memory_limit', '-1');
ini_set('max_execution_time', 30000);

$sql="SELECT p.product_code,p.pricing_model as product_pricing_model, pm.*,
pma.age_from as pma_age_from,
pma.age_to as pma_age_to,
pma.state as pma_state,
pma.zipcode as pma_zipcode,
pma.gender as pma_gender,
pma.smoking_status as pma_smoking_status,
pma.tobacco_status as pma_tobacco_status,
pma.height_by as pma_height_by,
pma.height_feet as pma_height_feet,
pma.height_inch as pma_height_inch,
pma.height_feet_to as pma_height_feet_to,
pma.height_inch_to as pma_height_inch_to,
pma.weight_by as pma_weight_by,
pma.weight as pma_weight,
pma.weight_to as pma_weight_to,
pma.no_of_children_by as pma_no_of_children_by,
pma.no_of_children as pma_no_of_children,
pma.no_of_children_to as pma_no_of_children_to,
pma.has_spouse as pma_has_spouse,
pma.spouse_age_from as pma_spouse_age_from,
pma.spouse_age_to as pma_spouse_age_to,
pma.spouse_gender as pma_spouse_gender,
pma.spouse_smoking_status as pma_spouse_smoking_status,
pma.spouse_tobacco_status as pma_spouse_tobacco_status,
pma.spouse_height_feet as pma_spouse_height_feet,
pma.spouse_height_inch as pma_spouse_height_inch,
pma.spouse_weight as pma_spouse_weight,
pma.spouse_weight_type as pma_spouse_weight_type,
pma.benefit_amount as pma_benefit_amount,
pma.min_total as pma_min_total,
pma.max_total as pma_max_total,
pma.is_deleted as pma_is_deleted
FROM prd_matrix pm 
JOIN prd_main p ON (p.id=pm.product_id)
LEFT JOIN prd_matrix_criteria pma ON(pma.prd_matrix_id=pm.id AND pma.product_id=p.id AND pma.is_deleted='N')
WHERE p.product_code IN ('".implode("','",$productIDArray)."') AND pm.is_deleted='N'";
$res=$OtherPdo->select($sql);

$delete_prd_mat = $pdo->selectOne("SELECT GROUP_CONCAT(distinct(id)) as id from prd_main where product_code IN('".implode("','",$productIDArray)."') AND is_deleted='N'");

if(!empty($delete_prd_mat) && !empty($delete_prd_mat['id'])){
    foreach($delete_prd_mat as $prd_id){
        $update_param = array(
            "is_deleted" => 'Y'
        );
        $update_where = array(
            "clause" => "product_id=:product_id",
            "params" => array(
                ":product_id"=>$prd_id
            )
        );
        $activ[] = $pdo->update("prd_matrix",$update_param,$update_where,true);
    }
    
}
if(!empty($res)){
	foreach ($res as $key => $value) {
		$sqlPrd="SELECT id FROM prd_main where product_code=:code";
        $resPrd=$pdo->selectOne($sqlPrd,array(":code"=>$value['product_code']));
       
		if(!empty($resPrd)){

            $product_id = $resPrd['id'];

			$ins_params = array(
				"product_id"=>$product_id,
				"variation_id"=>0,
                "price"=>$value['price'],
                "plan_type"=>$value['plan_type'],
                "opt_product_sell_type"=>$value['opt_product_sell_type'],
                "price_calculated_on"=>$value['price_calculated_on'],
                "price_calculated_type"=>$value['price_calculated_type'],
                "total_payment"=>$value['total_payment'],
                "age_from"=>$value['age_from'],
                "age_to"=>$value['age_to'],
                "state"=>$value['state'],
                "zip"=>$value['zip'],
                "gender"=>$value['gender'],
                "smoking_status"=>$value['smoking_status'],
                "tobacco_status"=>$value['tobacco_status'],
                "height_feet"=>$value['height_feet'],
                "height_inch"=>$value['height_inch'],
                "weight"=>$value['weight'],
                "no_of_children"=>$value['no_of_children'],
                "has_spouse"=>$value['has_spouse'],
                "spouse_age_from"=>$value['spouse_age_from'],
                "spouse_age_to"=>$value['spouse_age_to'],
                "spouse_gender"=>$value['spouse_gender'],
                "spouse_smoking_status"=>$value['spouse_smoking_status'],
                "spouse_tobacco_status"=>$value['spouse_tobacco_status'],
                "spouse_height_feet"=>$value['spouse_height_feet'],
                "spouse_height_inch"=>$value['spouse_height_inch'],
                "spouse_weight"=>$value['spouse_weight'],
                "payment_type"=>$value['payment_type'],
                "enrollee_type"=>$value['enrollee_type'],
                "pricing_model"=>$value['pricing_model'],
                "is_new_price_on_renewal"=>$value['is_new_price_on_renewal'],
                "pricing_matrix_csv"=>$value['pricing_matrix_csv'],
                "matrix_group"=>$value['matrix_group'],
                "pricing_effective_date"=>$value['pricing_effective_date'],
                "pricing_termination_date"=>$value['pricing_termination_date'],
                "non_commission_amount"=>$value['non_commission_amount'],
                "commission_amount"=>$value['commission_amount'],
                "is_deleted"=>$value['is_deleted'],
                //"created_at"=>"msqlfunc_NOW()",
                //"updated_at"=>"msqlfunc_NOW()",
		    );
            $prd_matrix_id=$pdo->insert("prd_matrix", $ins_params);
            
            if(!empty($value['product_pricing_model']) && $value['product_pricing_model']!='FixedPrice'){
                $ins_matrix_criteria_params = array(
                    'product_id' => $product_id,
                    'prd_matrix_id' => $prd_matrix_id,
                    "age_from"=>$value['pma_age_from'],
                    "age_to"=>$value['pma_age_to'],
                    "state"=>$value['pma_state'],
                    "zipcode"=>$value['pma_zipcode'],
                    "gender"=>$value['pma_gender'],
                    "smoking_status"=>$value['pma_smoking_status'],
                    "tobacco_status"=>$value['pma_tobacco_status'],
                    "height_by"=>!empty($value['pma_height_by']) ? $value['pma_height_by']:'Exactly',
                    "height_feet"=>$value['pma_height_feet'],
                    "height_inch"=>$value['pma_height_inch'],
                    "height_feet_to"=>!empty($value['pma_height_feet_to']) ? $value['pma_height_feet_to'] : 0,
                    "height_inch_to"=>!empty($value['pma_height_inch_to']) ? $value['pma_height_inch_to'] : 0,
                    "weight_by"=>!empty($value['pma_weight_by']) ? $value['pma_weight_by'] : 'Exactly',
                    "weight"=>$value['pma_weight'],
                    "weight_to"=>!empty($value['pma_weight_to']) ? $value['pma_weight_to'] : 0,
                    "no_of_children_by"=>!empty($value['pma_no_of_children_by']) ? $value['pma_no_of_children_by'] : 'Exactly',
                    "no_of_children"=>$value['pma_no_of_children'],
                    "no_of_children_to"=>!empty($value['pma_no_of_children_to']) ? $value['pma_no_of_children_to'] : 0,
                    "has_spouse"=>$value['pma_has_spouse'],
                    "spouse_age_from"=>$value['pma_spouse_age_from'],
                    "spouse_age_to"=>$value['pma_spouse_age_to'],
                    "spouse_gender"=>$value['pma_spouse_gender'],
                    "spouse_smoking_status"=>$value['pma_spouse_smoking_status'],
                    "spouse_tobacco_status"=>$value['pma_spouse_tobacco_status'],
                    "spouse_height_feet"=>$value['pma_spouse_height_feet'],
                    "spouse_height_inch"=>$value['pma_spouse_height_inch'],
                    "spouse_weight"=>$value['pma_spouse_weight'],
                    "spouse_weight_type"=>!empty($value['pma_spouse_weight_type']) ? $value['pma_spouse_weight_type'] : 'Exactly',
                    "benefit_amount"=>!empty($value['pma_benefit_amount']) ? $value['pma_benefit_amount'] : 0,
                    "min_total"=>!empty($value['pma_min_total']) ? $value['pma_min_total'] : 0,
                    "max_total"=>!empty($value['pma_max_total']) ? $value['pma_max_total'] : 0,
                    "is_deleted"=>$value['pma_is_deleted'],
                    "created_at"=>"msqlfunc_NOW()",
                    "updated_at"=>"msqlfunc_NOW()",
                );
                $pdo->insert("prd_matrix_criteria", $ins_matrix_criteria_params);
            }
		}
	}
}
echo "import_product_pricing->Completed";
dbConnectionClose();
exit;
?>
