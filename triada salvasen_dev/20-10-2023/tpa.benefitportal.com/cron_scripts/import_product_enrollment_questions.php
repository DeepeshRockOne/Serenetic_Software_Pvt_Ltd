<?php
$IMPORTDB='BENEFITS_PORTAL';

include_once dirname(__DIR__) . "/includes/other_connect.php";

ini_set('memory_limit', '-1');
ini_set('max_execution_time', 30000);

$sql="SELECT p.product_code,peqa.*,IF(peq.questionType!='Custom',peq.label,peq.display_label) as prd_question_label,peq.id as peqID,
	peq.questionType as peqQuestionType,peq.display_label as peq_display_label,peq.control_type as peq_control_type,peq.control_class as peq_control_class,peq.control_maxlength as peq_control_maxlength,peq.control_attribute as peq_control_attribute,
	peq.is_member as peq_is_member, peq.is_member_asked as peq_is_member_asked,peq.is_member_required as peq_is_member_required,
	peq.is_spouse as peq_is_spouse, peq.is_spouse_asked as peq_is_spouse_asked,peq.is_spouse_required as peq_is_spouse_required,
	peq.is_child as peq_is_child, peq.is_child_asked as peq_is_child_asked,peq.is_child_required as peq_is_child_required
	FROM prd_main p 
	JOIN prd_enrollment_questions_assigned peqa ON(peqa.product_id=p.id and peqa.is_deleted='N')
	JOIN prd_enrollment_questions peq ON(peq.id=peqa.prd_question_id AND peq.is_deleted='N')
	WHERE p.product_code IN ('".implode("','",$productIDArray)."')";
$res=$OtherPdo->select($sql);

if(!empty($res)){
	foreach ($res as $key => $value) {
		$sqlPrd="SELECT id FROM prd_main where product_code=:code";
		$resPrd=$pdo->selectOne($sqlPrd,array(":code"=>$value['product_code']));
		if(!empty($resPrd)){
			$product_id = $resPrd['id'];

			$sqlCheck="SELECT peqa.id FROM prd_enrollment_questions_assigned peqa 
			JOIN  prd_enrollment_questions peq ON(peq.id=peqa.prd_question_id AND peq.is_deleted='N')
			where peqa.product_id=:product_id AND peq.label=:label AND peqa.is_deleted='N'";
			$resCheck=$pdo->selectOne($sqlCheck,array(":product_id"=>$product_id,":label"=>$value['prd_question_label']));
			$resQue = $pdo->selectOne("SELECT id from prd_enrollment_questions where IF(questionType!='Custom',label=:label,display_label=:label) AND is_deleted='N'",array(":label"=>$value['prd_question_label']));
			if(empty($resQue['id'])){
				$resOrder=$pdo->selectOne("SELECT order_by FROM prd_enrollment_questions ORDER BY order_by DESC");
				$insEQAParam = array(
					"questionType" => $value['peqQuestionType'],
					"display_label" => $value['peq_display_label'],
					"control_type" => $value['peq_control_type'],
					"control_class" => $value['peq_control_class'],
					"control_maxlength" => $value['peq_control_maxlength'],
					"control_attribute" => $value['peq_control_attribute'],
					'order_by'=>$resOrder['order_by'] + 1
				);
				if($value['peqQuestionType'] != 'Custom'){
					$insEQAParam['is_member'] = $value['peq_is_member'];
					$insEQAParam['is_member_asked'] = $value['peq_is_member_asked'];
					$insEQAParam['is_member_required'] = $value['peq_is_member_required'];
					$insEQAParam['is_spouse'] = $value['peq_is_spouse'];
					$insEQAParam['is_spouse_asked'] = $value['peq_is_spouse_asked'];
					$insEQAParam['is_spouse_required'] = $value['peq_is_spouse_required'];
					$insEQAParam['is_child'] = $value['peq_is_child'];
					$insEQAParam['is_child_asked'] = $value['peq_is_child_asked'];
					$insEQAParam['is_child_required'] = $value['peq_is_child_required'];
				}
				$insQueId = $pdo->insert('prd_enrollment_questions',$insEQAParam);
				if($value['peqQuestionType'] == 'Custom'){
					$ins_params1 =array('label'=>'queCustom'.$insQueId);
					$upd_where = array(
						'clause' => 'id = :id',
						'params' => array(
							':id' => $insQueId,
						),
					);
					$pdo->update('prd_enrollment_questions', $ins_params1, $upd_where);
				}
				$questionAnsRes = $OtherPdo->select("SELECT * from prd_enrollment_answers pea JOIN prd_enrollment_questions peq ON(peq.id=pea.prd_question_id) where peq.id=:id",array(":id"=>$value['peqID']));
				if(!empty($questionAnsRes)){
					foreach ($questionAnsRes as $key => $valueA) {
						$ins_params = array(
							'is_deleted' =>'N',
							'prd_question_id'=>$insQueId,
							'answer'=>$valueA['answer'],
							'answer_eligible'=>$valueA['answer_eligible'],
						);
						$pdo->insert('prd_enrollment_answers',$ins_params);
					}
				}
				$resQue['id'] = $insQueId;
			}
			if(empty($resCheck['id']) && !empty($resQue['id'])){
				$insQuesParams = array(
					"product_id" => $product_id,
					"prd_question_id" => $resQue['id'],
					'is_member_asked'=> $value['is_member_asked'],
					'is_spouse_asked'=> $value['is_spouse_asked'],
					'is_child_asked'=> $value['is_child_asked'],
					'is_member_required'=> $value['is_member_required'],
					'is_spouse_required'=> $value['is_spouse_required'],
					'is_child_required'=> $value['is_child_required'],
				);
				$insQue = $pdo->insert('prd_enrollment_questions_assigned',$insQuesParams);
			}
			
		}
	}
}
echo "import_product_enrollment_questions->Completed";
dbConnectionClose();
exit;
?>
