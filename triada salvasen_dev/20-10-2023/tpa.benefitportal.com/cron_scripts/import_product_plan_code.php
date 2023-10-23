<?php
$IMPORTDB='BENEFITS_PORTAL';

include_once dirname(__DIR__) . "/includes/other_connect.php";

ini_set('memory_limit', '-1');
ini_set('max_execution_time', 30000);

$sql="SELECT p.product_code,ppc.* FROM prd_main p
JOIN prd_plan_code ppc ON (p.id = ppc.product_id)
WHERE p.product_code IN ('".implode("','",$productIDArray)."') AND ppc.is_deleted='N'";
$res=$OtherPdo->select($sql);

if(!empty($res)){
	foreach ($res as $key => $value) {
		$sqlPrd="SELECT id FROM prd_main where product_code=:code";
        $resPrd=$pdo->selectOne($sqlPrd,array(":code"=>$value['product_code']));
        
		if(!empty($resPrd)){
			$product_id = $resPrd['id'];
			$insSubParams=array(
                'product_id'=>$product_id,
                'code_no' => $value['code_no'],
                'plan_code_value' => $value['plan_code_value'],
			);
			$pdo->insert('prd_plan_code',$insSubParams);	
		}
		
	}
}
echo "import_product_plan_code -> Completed";
dbConnectionClose();
exit;
?>