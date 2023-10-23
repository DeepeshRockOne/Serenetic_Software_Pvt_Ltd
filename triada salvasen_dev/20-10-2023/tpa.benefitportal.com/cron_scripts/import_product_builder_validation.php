<?php
$IMPORTDB='BENEFITS_PORTAL';

include_once dirname(__DIR__) . "/includes/other_connect.php";

ini_set('memory_limit', '-1');
ini_set('max_execution_time', 30000);

$sql="SELECT p.product_code,ppbv.* FROM prd_main p
JOIN prd_product_builder_validation ppbv ON (p.id = ppbv.product_id)
WHERE p.product_code IN ('".implode("','",$productIDArray)."')";
$res=$OtherPdo->select($sql);
if(!empty($res)){
	foreach ($res as $key => $value) {
		$sqlPrd="SELECT id FROM prd_main where product_code=:code";
		$resPrd=$pdo->selectOne($sqlPrd,array(":code"=>$value['product_code']));

        
        $resPrd=$pdo->selectOne($sqlPrd,array(":code"=>$value['product_code']));
        
		if(!empty($resPrd)){
            $product_id = $resPrd['id'];
            $sqlPrdb="SELECT id FROM prd_product_builder_validation where product_id=:product_id";
            $prdB= $pdo->selectOne($sqlPrdb,array(":product_id"=>$product_id));
			if(empty($prdB['id'])){
                $insSubParams=array(
                    'product_id'=>$product_id,
                    'errorJson'=>$value['errorJson'],
                );
                $pdo->insert('prd_product_builder_validation',$insSubParams);	
            }
			
		}
		
	}
}
echo "import_product_builder_validation -> Completed";
dbConnectionClose();
exit;
?>