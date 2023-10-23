<?php
$IMPORTDB='BENEFITS_PORTAL';

include_once dirname(__DIR__) . "/includes/other_connect.php";

ini_set('memory_limit', '-1');
ini_set('max_execution_time', 30000);

$sql="SELECT p.product_code,pls.* FROM prd_license_state pls 
JOIN prd_main p ON (p.id = pls.product_id  AND pls.is_deleted='N')
WHERE p.product_code IN ('".implode("','",$productIDArray)."')";
$res=$OtherPdo->select($sql);
if(!empty($res)){
	foreach ($res as $key => $value) {
		$sqlPrd="SELECT id FROM prd_main where product_code=:code";
		$resPrd=$pdo->selectOne($sqlPrd,array(":code"=>$value['product_code']));
		if(!empty($resPrd)){
			$product_id = $resPrd['id'];
			$sqlCheck="SELECT id FROM prd_license_state where 
            product_id=:product_id AND 
            license_rule=:license_rule AND 
            sale_type=:sale_type AND 
            state_name=:state_name AND 
            state_id=:state_id AND 
            is_deleted='N'";
            $resCheck=$pdo->selectOne($sqlCheck,array(":product_id"=>$product_id,
                                                        ":license_rule"=>$value['license_rule'],
                                                        ":sale_type"=>$value['sale_type'],
                                                        ":state_name"=>$value['state_name'],
                                                        ":state_id"=>$value['state_id'],
                                                    ));
			if(empty($resCheck)){
				$insertStateParams=array(
					'product_id'=>$product_id,
					'state_id'=>$value['state_id'],
                    'state_name'=>$value['state_name'],
                    'license_rule'=>$value['license_rule'],
                    'sale_type'=>$value['sale_type'],
				);
				$noSaleStateId=$pdo->insert("prd_license_state",$insertStateParams);
			}
		}
		
	}
}
echo "import_product_license_state->Completed";
dbConnectionClose();
exit;
?>