<?php
$IMPORTDB='BENEFITS_PORTAL';

include_once dirname(__DIR__) . "/includes/other_connect.php";

ini_set('memory_limit', '-1');
ini_set('max_execution_time', 30000);

$sql="SELECT p.product_code,pbad.* FROM prd_beyond_age_disablity pbad 
JOIN prd_main p ON (p.id = pbad.product_id AND pbad.is_deleted='N')
WHERE p.product_code IN ('".implode("','",$productIDArray)."')";
$res=$OtherPdo->select($sql);
if(!empty($res)){
	foreach ($res as $key => $value) {
		$sqlPrd="SELECT id FROM prd_main where product_code=:code";
		$resPrd=$pdo->selectOne($sqlPrd,array(":code"=>$value['product_code']));

		if(!empty($resPrd)){
			$product_id = $resPrd['id'];

			$sqlCheck="SELECT id FROM prd_beyond_age_disablity where product_id=:product_id AND member_type=:member_type AND is_deleted='N'";
            $resCheck=$pdo->selectOne($sqlCheck,array(":product_id"=>$product_id,":member_type"=>$value['member_type'],));
            
			if(empty($resCheck)){
				$insCoverageParams = array(
                    "product_id" => $product_id,
                    "member_type" => $value['member_type'],
				);
				$prd_coverage_options = $pdo->insert('prd_beyond_age_disablity',$insCoverageParams);
			}
		}
	}
}

echo "import_beyond_age_disablity->Completed";
dbConnectionClose();
exit;

?>