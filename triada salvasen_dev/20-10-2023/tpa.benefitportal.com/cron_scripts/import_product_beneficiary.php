<?php
$IMPORTDB='BENEFITS_PORTAL';

include_once dirname(__DIR__) . "/includes/other_connect.php";

ini_set('memory_limit', '-1');
ini_set('max_execution_time', 30000);

$sql="SELECT p.product_code,pbqa.*,pbq.label as prd_question_label
	FROM prd_main p 
	JOIN prd_beneficiary_questions_assigned pbqa ON(pbqa.product_id=p.id and pbqa.is_deleted='N')
	JOIN prd_beneficiary_questions pbq ON(pbq.id=pbqa.prd_beneficiary_question_id AND pbq.is_deleted='N')
	WHERE p.product_code IN ('".implode("','",$productIDArray)."') AND p.is_beneficiary_required='Y'";
$res=$OtherPdo->select($sql);

if(!empty($res)){
	foreach ($res as $key => $value) {
		$sqlPrd="SELECT id FROM prd_main where product_code=:code";
		$resPrd=$pdo->selectOne($sqlPrd,array(":code"=>$value['product_code']));

		if(!empty($resPrd)){
			$product_id = $resPrd['id'];

			if(!empty($tmpRows)){
				$sqlCheck="SELECT pbqa.id FROM prd_beneficiary_questions_assigned pbqa 
				JOIN  prd_beneficiary_questions pbq ON(pbq.id=pbqa.prd_beneficiary_question_id AND pbq.is_deleted='N')
				where pbqa.product_id=:product_id AND pbq.label=:label AND is_deleted='N'";
				$resCheck=$pdo->selectOne($sqlCheck,array(":product_id"=>$product_id,":label"=>$value['prd_question_label']));
				
				$resQue = $pdo->selectOne("SELECT id from prd_beneficiary_questions where label=:label AND is_deleted='N'",array(":label"=>$value['prd_question_label']));

				if(empty($resCheck) && !empty($resQue['id'])){
					$insQuesParams = array(
						"product_id" => $product_id,
						"prd_beneficiary_question_id" => $resQue['id'],
						'is_principal_beneficiary_asked'=> $value['is_principal_beneficiary_asked'],
						'is_contigent_beneficiary_asked'=> $value['is_contigent_beneficiary_asked'],
						'is_principal_beneficiary_required'=> $value['is_principal_beneficiary_required'],
						'is_contigent_beneficiary_required'=> $value['is_contigent_beneficiary_required'],
					);
					$insQue = $pdo->insert('prd_beneficiary_questions_assigned',$insQuesParams);
				}
			}
			
		}
	}
}
echo "import_product_beneficiary->Completed";
dbConnectionClose();
exit;
?>
