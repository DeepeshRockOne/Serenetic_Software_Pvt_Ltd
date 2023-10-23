<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$id=!empty($_GET['id']) ? $_GET['id'] : 0;

$display_label = '';
$resAnswers = array();
if(!empty($id)){
	$sqlCustomQuestion="SELECT id,control_type,display_label FROM prd_enrollment_questions WHERE md5(id)=:id";
	$resCustomQuestion=$pdo->selectOne($sqlCustomQuestion,array(":id"=>$id));

	if(!empty($resCustomQuestion)){
		$control_type = $resCustomQuestion['control_type'];
		$display_label = $resCustomQuestion['display_label'];

		$sqlAnswers = "SELECT * FROM prd_enrollment_answers WHERE prd_question_id=:id AND is_deleted='N'";
		$resAnswers = $pdo->select($sqlAnswers,array(":id"=>$resCustomQuestion['id']));
		
	}
}

$template = "view_custom_question_answers.inc.php";
include_once 'layout/iframe.layout.php';
?>