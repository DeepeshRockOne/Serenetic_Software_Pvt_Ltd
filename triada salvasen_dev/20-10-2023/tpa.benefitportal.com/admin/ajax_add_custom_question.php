<?php
include_once 'layout/start.inc.php';
$res = array();

$validate = new Validation();

$question = !empty($_POST['question']) ? $_POST['question'] : 0;


$control_type = !empty($_POST['control_type']) ? $_POST['control_type'] : '';
$display_label = !empty($_POST['display_label']) ? $_POST['display_label'] : '';
$answers = !empty($_POST['answers']) ? $_POST['answers'] : array();
$answer_eligible = !empty($_POST['answer_eligible']) ? $_POST['answer_eligible'] : array();




$validate->string(array('required' => true, 'field' => 'control_type', 'value' => $control_type), array('required' => 'Select Question Type'));


$validate->string(array('required' => true, 'field' => 'display_label', 'value' => $display_label), array('required' => 'Please Enter Question'));

if(!empty($answers)){
	foreach ($answers as $key => $value) {
		$validate->string(array('required' => true, 'field' => 'answers'.$key, 'value' => $answers[$key]), array('required' => 'Please Enter Answer'));
		$validate->string(array('required' => true, 'field' => 'answer_eligible'.$key, 'value' => $answer_eligible[$key]), array('required' => 'Please Select Answer Eligible'));
	}
}

if ($validate->isValid()) {
		
		$ins_params = array(
			'control_type' => $control_type,
			'display_label' => $display_label,
			'questionType'=> 'Custom',
		);
		$sqlPrdQuestion="SELECT id FROM prd_enrollment_questions where md5(id)=:id";
		$resPrdQuestion=$pdo->selectOne($sqlPrdQuestion,array(":id"=>$question));


		if(!empty($resPrdQuestion)){
			$question_id = $resPrdQuestion['id'];
			$res['msg'] = "Question updated Successfully";
			$upd_where = array(
				'clause' => 'id = :id',
				'params' => array(
					':id' => $question_id,
				),
			);
			$pdo->update('prd_enrollment_questions', $ins_params, $upd_where);

			$description['ac_message'] =array(
			    'ac_red_1'=>array(
			      'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
			      'title'=>$_SESSION['admin']['display_id'],
			    ),
			    'ac_message_1' =>' Updated Custom Question ',
			    'ac_red_2'=>array(
			        'title'=>$display_label,
			    ),
		  	); 

		  	activity_feed(3, $_SESSION['admin']['id'], 'Admin', $question_id, 'prd_enrollment_questions','Added Custom Question', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));

			$sqlAnswers = "SELECT id FROM prd_enrollment_answers WHERE prd_question_id=:id AND is_deleted='N'";
			$resAnswers = $pdo->select($sqlAnswers,array(":id"=>$question_id));

			if(!empty($resAnswers)){
				foreach ($resAnswers as $key => $value) {
					$ins_params = array('is_deleted' =>'Y');
					$upd_where = array(
						'clause' => 'id = :id',
						'params' => array(
							':id' => $value['id'],
						),
					);
					$pdo->update('prd_enrollment_answers', $ins_params, $upd_where);
				}
			}

		}else{
			$resOrder=$pdo->selectOne("SELECT order_by FROM prd_enrollment_questions ORDER BY order_by DESC");

			$ins_params['order_by']=$resOrder['order_by'] + 1;
			$question_id = $pdo->insert("prd_enrollment_questions", $ins_params);
			$res['msg'] = "Question added Successfully";

			$ins_params =array('label'=>'queCustom'.$question_id);
			$upd_where = array(
				'clause' => 'id = :id',
				'params' => array(
					':id' => $question_id,
				),
			);
			$pdo->update('prd_enrollment_questions', $ins_params, $upd_where);

			$description['ac_message'] =array(
			    'ac_red_1'=>array(
			      'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
			      'title'=>$_SESSION['admin']['display_id'],
			    ),
			    'ac_message_1' =>' Added Custom Question ',
			    'ac_red_2'=>array(
			        'title'=>$display_label,
			    ),
		  	); 

		  	activity_feed(3, $_SESSION['admin']['id'], 'Admin', $question_id, 'prd_enrollment_questions','Added Custom Question', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
		}

		if(!empty($answers)){
			foreach ($answers as $key => $value) {
				$ins_params = array(
					'is_deleted' =>'N',
					'prd_question_id'=>$question_id,
					'answer'=>$value,
					'answer_eligible'=>$answer_eligible[$key],
				);
				if($key<=0){
					$pdo->insert('prd_enrollment_answers',$ins_params);
				}else{
					$upd_where = array(
						'clause' => 'id = :id',
						'params' => array(
							':id' => $key,
						),
					);
					$pdo->update('prd_enrollment_answers', $ins_params, $upd_where);
				}
			}
		}

		
		

		$res['status'] = "success";
	
} else {
	$res['status'] = "fail";
	$res['errors'] = $validate->getErrors();
}

header('Content-Type: application/json');
echo json_encode($res);
dbConnectionClose();
exit;
?>