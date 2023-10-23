<?php
$IMPORTDB='BENEFITS_PORTAL';

include_once dirname(__DIR__) . "/includes/other_connect.php";

ini_set('memory_limit', '-1');
ini_set('max_execution_time', 30000);

$sql="SELECT p.product_code,pmg.* FROM prd_main p
JOIN prd_match_globals pmg ON (p.id = pmg.product_id)
WHERE p.product_code IN ('".implode("','",$productIDArray)."') AND pmg.is_deleted='N'";
$res=$OtherPdo->select($sql);

if(!empty($res)){
	foreach ($res as $key => $value) {
		$sqlPrd="SELECT id FROM prd_main where product_code=:code";
        $resPrd=$pdo->selectOne($sqlPrd,array(":code"=>$value['product_code']));
        
		if(!empty($resPrd)){
			$product_id = $resPrd['id'];
			$insSubParams=array(
                'product_id'=>$product_id,
                'match_globals' => $value['match_globals'],
			);
			$pdo->insert('prd_match_globals',$insSubParams);	
		}
		
	}
}
echo "import_product_match_globals -> Completed";
dbConnectionClose();
exit;
?>