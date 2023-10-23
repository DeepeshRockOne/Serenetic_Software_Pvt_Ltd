<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once dirname(__DIR__) .'/includes/Api.class.php';


$perPages = checkIsset($_POST['perPages']);
$page = checkIsset($_POST['page']);
$is_ajaxed = checkIsset($_POST['is_ajaxed']);
$ajaxApiCall = new Api();

if($is_ajaxed){

		$question_id = isset($_POST['id']) ? $_POST['id'] : '';
		$apiParams = array(
			'perPages'=> $perPages,
			'page'=> $page,
			'id'=> $question_id,
			'api_key' => 'getQuestionAnswerBundleDetails'
		);
		$questionRes = $ajaxApiCall->ajaxApiCall($apiParams,true);
		$fetch_rows = !empty($questionRes['data']['data']) ? $questionRes['data']['data']: array();
		$total_rows = count($fetch_rows);
		$question = isset($fetch_rows[0]['question']) ? $fetch_rows[0]['question'] : '' ;
		if($total_rows > 0){
			$paginate = $ajaxApiCall->paginate($questionRes['data'],'view_bundle_question_answer_details.php');
			$paginageLinks = $paginate ['links'];
			$per_page = $paginate ['per_page'];
		}

		include_once 'tmpl/view_bundle_question_answer_details.inc.php';
		exit;
}else{
	$question_id = isset($_GET['id']) ? $_GET['id'] : '';
	$params = array(
		'id'=> $question_id,
		'api_key' => 'getQuestion'

	);
	$questiosData = $ajaxApiCall->ajaxApiCall($params,true);
	$questiosData = !empty($questiosData['data']) ? $questiosData['data'] : '';
	$question = $questiosData['questions'];

}


$template ='view_bundle_question_answer_details.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>