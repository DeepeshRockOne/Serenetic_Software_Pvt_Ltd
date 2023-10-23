<?php
$IMPORTDB='BENEFITS_PORTAL';

include_once dirname(__DIR__) . "/includes/other_connect.php";

ini_set('memory_limit', '-1');
ini_set('max_execution_time', 30000);

$sql="SELECT p.product_code,pvbe.*
	FROM prd_main p 
	JOIN prd_variable_by_enrollee pvbe ON(pvbe.product_id=p.id and pvbe.is_deleted='N')
	WHERE p.product_code IN ('".implode("','",$productIDArray)."')";
$res=$OtherPdo->select($sql);

if(!empty($res)){
	foreach ($res as $key => $value) {
		$sqlPrd="SELECT id FROM prd_main where product_code=:code";
		$resPrd=$pdo->selectOne($sqlPrd,array(":code"=>$value['product_code']));

		if(!empty($resPrd)){
			$product_id = $resPrd['id'];
			$sqlCheck="SELECT id FROM prd_variable_by_enrollee where product_id=:product_id AND is_deleted='N'";
			$resCheck=$pdo->selectOne($sqlCheck,array(":product_id"=>$product_id));
			if(empty($resCheck['id'])){
				$insSubParams=array(
					'product_id'=>$product_id,
					'child_dependent_rate_calculation' => $value['child_dependent_rate_calculation'],
					'allowed_child' => $value['allowed_child'],
					'is_banded_rates' => $value['is_banded_rates'],
					'banded_rate_change_after' => $value['banded_rate_change_after'],
					'is_banded_criteria' => $value['is_banded_criteria'],
					'is_primary_eldest'=>$value['is_primary_eldest'],
					'is_rider_for_enrolles'=>$value['is_rider_for_enrolles'],
					'offer_rider_for'=>$value['offer_rider_for'],
					'rider_product'=>$value['rider_product'],
					'rider_question'=>$value['rider_question'],
				);
				$pdo->insert('prd_variable_by_enrollee',$insSubParams);
			}
		}
	}
}
echo "import_product_variable_by_enrollee->Completed";
dbConnectionClose();
exit;
?>
