<?php
$IMPORTDB='BENEFITS_PORTAL';

include_once dirname(__DIR__) . "/includes/other_connect.php";

ini_set('memory_limit', '-1');
ini_set('max_execution_time', 30000);

$sql="SELECT p.product_code,ptc.terms_condition
	FROM prd_terms_condition ptc
	JOIN prd_main p ON(ptc.product_id=p.id and ptc.is_deleted='N')
	WHERE p.product_code IN ('".implode("','",$productIDArray)."')";
$res=$OtherPdo->select($sql);
if(!empty($res)){
	foreach ($res as $key => $value) {
		$sqlPrd="SELECT id FROM prd_main where product_code=:code";
		$resPrd=$pdo->selectOne($sqlPrd,array(":code"=>$value['product_code']));

		if(!empty($resPrd)){
			$product_id = $resPrd['id'];

			$termsConditionData = $value['terms_condition'];
			
			$sqlCheck="SELECT id FROM prd_terms_condition where product_id=:product_id AND is_deleted='N'";
			$resCheck=$pdo->selectOne($sqlCheck,array(":product_id"=>$product_id));

			if(empty($resCheck)){
				$insTermsParams=array(
					'product_id'=>$product_id,
					'terms_condition'=>$termsConditionData,
				);
				$pdo->insert('prd_terms_condition',$insTermsParams);
			}
		}
	}
}
echo "import_product_terms_condition->Completed";
dbConnectionClose();
exit;
?>
