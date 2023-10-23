<?php
$IMPORTDB='BENEFITS_PORTAL';

include_once dirname(__DIR__) . "/includes/other_connect.php";

ini_set('memory_limit', '-1');
ini_set('max_execution_time', 30000);

$sql="SELECT p.product_code,pmat.*,t.title as trigger_name FROM prd_max_age_terminaion pmat 
JOIN prd_main p ON (p.id = pmat.product_id AND pmat.is_deleted='N')
LEFT JOIN triggers t ON (t.id=pmat.terminate_trigger) 
WHERE p.product_code IN ('".implode("','",$productIDArray)."')";
$res=$OtherPdo->select($sql);
if(!empty($res)){
	foreach ($res as $key => $value) {
		$sqlPrd="SELECT id FROM prd_main where product_code=:code";
		$resPrd=$pdo->selectOne($sqlPrd,array(":code"=>$value['product_code']));

		if(!empty($resPrd)){
			$product_id = $resPrd['id'];

			$sqlCheck="SELECT id FROM prd_max_age_terminaion where product_id=:product_id AND member_type=:member_type AND terminate_within=:terminate_within AND terminate_within_type=:terminate_within_type AND terminate_range=:terminate_range  AND is_deleted='N'";
            $resCheck=$pdo->selectOne($sqlCheck,array(":product_id"=>$product_id,
                                                    ":member_type"=>$value['member_type'],
                                                    ":terminate_within"=>$value['terminate_within'],
                                                    ":terminate_within_type"=>$value['terminate_within_type'],":terminate_range"=>$value['terminate_range'])
                                                );
            
            $sqlTCheck="SELECT id FROM triggers where title=:title AND is_deleted='N'";
            $resTCheck=$pdo->selectOne($sqlTCheck,array(":title"=>$value['trigger_name']));
            
			if(empty($resCheck)){
				$insCoverageParams = array(
                    "product_id" => $product_id,
                    "member_type" => $value['member_type'],
                    "terminate_within" => $value['terminate_within'],
                    "terminate_within_type" => $value['terminate_within_type'],
                    "terminate_range" => $value['terminate_range'],
                    "terminate_trigger" => !empty($resTCheck['id']) ? $resTCheck['id'] : 0,
				);
				$prd_coverage_options = $pdo->insert('prd_max_age_terminaion',$insCoverageParams);
			}
		}
	}
}
echo "import_max_age_termination->Completed";
dbConnectionClose();
exit;
?>