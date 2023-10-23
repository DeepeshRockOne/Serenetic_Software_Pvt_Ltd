<?php
$IMPORTDB='BENEFITS_PORTAL';

include_once dirname(__DIR__) . "/includes/other_connect.php";

ini_set('memory_limit', '-1');
ini_set('max_execution_time', 30000);

$sql="SELECT p.product_code,pco.* FROM prd_coverage_options pco 
JOIN prd_main p ON (p.id = pco.product_id AND pco.is_deleted='N')
WHERE p.product_code IN ('".implode("','",$productIDArray)."')";
$res=$OtherPdo->select($sql);

if(!empty($res)){
	foreach ($res as $key => $value) {
		$sqlPrd="SELECT id FROM prd_main where product_code=:code";
		$resPrd=$pdo->selectOne($sqlPrd,array(":code"=>$value['product_code']));

		if(!empty($resPrd)){
			$product_id = $resPrd['id'];

			$sqlCheck="SELECT id FROM prd_coverage_options where product_id=:product_id AND prd_plan_type_id=:tmpValue AND is_deleted='N'";
			$resCheck=$pdo->selectOne($sqlCheck,array(":product_id"=>$product_id,":tmpValue"=>$value['prd_plan_type_id']));
			if(empty($resCheck)){
				$insCoverageParams = array(
					"product_id" => $product_id,
					"prd_plan_type_id" => $value['prd_plan_type_id'],
				);
				$prd_coverage_options = $pdo->insert('prd_coverage_options',$insCoverageParams);
			}
		}
	}
}
echo "import_product_coverage_options -> Completed";
dbConnectionClose();
exit;
?>
