<?php
$IMPORTDB='BENEFITS_PORTAL';

include_once dirname(__DIR__) . "/includes/other_connect.php";

ini_set('memory_limit', '-1');
ini_set('max_execution_time', 30000);

$sql="SELECT p.product_code,pev.verification_type
	FROM prd_main p 
	JOIN prd_enrollment_verification pev ON(pev.product_id=p.id and pev.is_deleted='N')
	WHERE p.product_code IN ('".implode("','",$productIDArray)."')";
$res=$OtherPdo->select($sql);

if(!empty($res)){
	foreach ($res as $key => $value) {
		$sqlPrd="SELECT id FROM prd_main where product_code=:code";
		$resPrd=$pdo->selectOne($sqlPrd,array(":code"=>$value['product_code']));

		if(!empty($resPrd)){
			$product_id = $resPrd['id'];
			$sqlCheck="SELECT id FROM prd_enrollment_verification where product_id=:product_id AND verification_type=:verification_type AND is_deleted='N'";
			$resCheck=$pdo->selectOne($sqlCheck,array(":product_id"=>$product_id,":verification_type"=>$value['verification_type']));
			if(empty($resCheck)){
				$insSubParams=array(
					'product_id'=>$product_id,
					'verification_type'=>$value['verification_type'],
				);
				$pdo->insert('prd_enrollment_verification',$insSubParams);
			}
		}
	}
}
echo "import_product_verification->Completed";
dbConnectionClose();
exit;
?>
