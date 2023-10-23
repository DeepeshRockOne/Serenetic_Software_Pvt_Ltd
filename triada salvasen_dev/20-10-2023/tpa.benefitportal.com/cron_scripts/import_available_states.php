<?php
$IMPORTDB='BENEFITS_PORTAL';

include_once dirname(__DIR__) . "/includes/other_connect.php";

ini_set('memory_limit', '-1');
ini_set('max_execution_time', 30000);

$sql="SELECT p.product_code,pas.* FROM prd_main p
JOIN prd_available_state pas ON(pas.product_id=p.id and pas.is_deleted='N')
WHERE p.product_code IN ('".implode("','",$productIDArray)."')";
$res=$OtherPdo->select($sql);

if(!empty($res)){
	foreach ($res as $key => $value) {
		$sqlPrd="SELECT id FROM prd_main where product_code=:code";
		$resPrd=$pdo->selectOne($sqlPrd,array(":code"=>$value['product_code']));
		if(!empty($resPrd)){
			$product_id = $resPrd['id'];

			$sqlCheck="SELECT id FROM prd_available_state where product_id=:product_id AND state_name=:name AND is_deleted='N'";
			$resCheck=$pdo->selectOne($sqlCheck,array(":product_id"=>$product_id,":name"=>$value['state_name']));
			if(empty($resCheck)){
				$insStateParams = array(
					"product_id" => $product_id,
					"state_id" => $value['state_id'],
					"state_name" => $value['state_name'],
				);
				$prd_available_state = $pdo->insert('prd_available_state',$insStateParams);
			}
		}
	}
}
echo "import_available_states->Completed";
dbConnectionClose();
exit;
?>
