<?php
$IMPORTDB='BENEFITS_PORTAL';

include_once dirname(__DIR__) . "/includes/other_connect.php";

ini_set('memory_limit', '-1');
ini_set('max_execution_time', 30000);

$sql="SELECT p.product_code,pns.* FROM prd_no_sale_states pns 
JOIN prd_main p ON (p.id = pns.product_id  AND pns.is_deleted='N')
WHERE p.product_code IN ('".implode("','",$productIDArray)."')";
$res=$OtherPdo->select($sql);
if(!empty($res)){
	foreach ($res as $key => $value) {
		$sqlPrd="SELECT id FROM prd_main where product_code=:code";
		$resPrd=$pdo->selectOne($sqlPrd,array(":code"=>$value['product_code']));

		if(!empty($resPrd)){
			$product_id = $resPrd['id'];
			$sqlCheck="SELECT id FROM prd_no_sale_states where product_id=:product_id AND state_name=:name AND is_deleted='N'";
			$resCheck=$pdo->selectOne($sqlCheck,array(":product_id"=>$product_id,":name"=>$value['state_name']));
			if(empty($resCheck)){
				$insertStateParams=array(
					'product_id'=>$product_id,
					'state_id'=>$value['state_id'],
					'state_name'=>$value['state_name'],
					'is_deleted'=>'N',
					'effective_date'=>(!empty($value['effective_date'])) ? date('Y-m-d',strtotime($value['effective_date'])) : NULL,
					'termination_date'=>(!empty($value['termination_date'])) ? date('Y-m-d',strtotime($value['termination_date'])) : NULL,
				);
				$noSaleStateId=$pdo->insert("prd_no_sale_states",$insertStateParams);
			}
		}
		
	}
}
echo "import_product_no_sale_states->Completed";
dbConnectionClose();
exit;
?>