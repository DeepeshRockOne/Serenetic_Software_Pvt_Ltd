<?php
$IMPORTDB='BENEFITS_PORTAL';

include_once dirname(__DIR__) . "/includes/other_connect.php";

ini_set('memory_limit', '-1');
ini_set('max_execution_time', 30000);

$sql="SELECT p.product_code,ps.*,ps.state_name,ps.state_id,ps.zipcode FROM prd_main p 
JOIN prd_specific_zipcode ps ON (p.id = ps.product_id  AND ps.is_deleted='N')
WHERE p.product_code IN ('".implode("','",$productIDArray)."')";
$res=$OtherPdo->select($sql);
if(!empty($res)){
	foreach ($res as $key => $value) {
		$sqlPrd="SELECT id FROM prd_main where product_code=:code";
		$resPrd=$pdo->selectOne($sqlPrd,array(":code"=>$value['product_code']));

		if(!empty($resPrd)){
			$product_id = $resPrd['id'];
			$sqlCheck="SELECT id FROM prd_specific_zipcode where product_id=:product_id AND state_name=:name AND zipcode=:zipcode is_deleted='N'";
			$resCheck=$pdo->selectOne($sqlCheck,array(":product_id"=>$product_id,":name"=>$value['state_name'],":zipcode"=>$value['zipcode']));
			if(empty($resCheck) && empty($resCheck['id'])){
				$insertStateParams=array(
					'product_id'=>$product_id,
					'state_id'=>$value['state_id'],
                    'state_name'=>$value['state_name'],
                    'zipcode'=>$value['zipcode'],
				);
				$prd_specific_zipcode=$pdo->insert("prd_specific_zipcode",$insertStateParams);
			}
		}
		
	}
}
echo "import_product_specific_zipcode->Completed";
dbConnectionClose();
exit;
?>