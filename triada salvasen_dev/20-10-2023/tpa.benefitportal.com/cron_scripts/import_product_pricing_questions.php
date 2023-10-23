<?php
$IMPORTDB='BENEFITS_PORTAL';

include_once dirname(__DIR__) . "/includes/other_connect.php";

ini_set('memory_limit', '-1');
ini_set('max_execution_time', 30000);

$sql="SELECT p.product_code,ppqa.*,ppq.label as prd_question_label
	FROM prd_main p 
	JOIN prd_pricing_question_assigned ppqa ON(ppqa.product_id=p.id and ppqa.is_deleted='N')
	JOIN prd_pricing_question ppq ON(ppq.id=ppqa.prd_pricing_question_id AND ppq.is_deleted='N')
	WHERE p.product_code IN ('".implode("','",$productIDArray)."')";
$res=$OtherPdo->select($sql);

if(!empty($res)){
	foreach ($res as $key => $value) {
		$sqlPrd="SELECT id FROM prd_main where product_code=:code";
		$resPrd=$pdo->selectOne($sqlPrd,array(":code"=>$value['product_code']));

		if(!empty($resPrd)){
			$product_id = $resPrd['id'];

			$sqlCheck="SELECT ppqa.id FROM prd_pricing_question_assigned ppqa 
			JOIN  prd_pricing_question ppq ON(ppq.id=ppqa.prd_pricing_question_id AND ppq.is_deleted='N')
			where ppqa.product_id=:product_id AND ppq.label=:label AND ppqa.is_deleted='N'";
			$resCheck=$pdo->selectOne($sqlCheck,array(":product_id"=>$product_id,":label"=>$value['prd_question_label']));

			$resQue = $pdo->selectOne("SELECT id from prd_pricing_question where label=:label AND is_deleted='N'",array(":label"=>$value['prd_question_label']));

			if(empty($resCheck) && !empty($resQue['id'])){
				$insQuesParams = array(
					"product_id" => $product_id,
					"prd_pricing_question_id" => $resQue['id'],
					'assign_type'=> $value['assign_type'],
				);
				$insQue = $pdo->insert('prd_pricing_question_assigned',$insQuesParams);
			}
			
		}
	}
}
echo "import_product_pricing_questions->Completed";
dbConnectionClose();
exit;
?>
