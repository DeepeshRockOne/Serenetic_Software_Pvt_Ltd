<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$id = $_REQUEST['id'];

	$templateSql = "SELECT id,content FROM trigger_template WHERE md5(id)=:id AND is_deleted='N'";
	$templateParams = array(":id" => $id);
	$templateRes = $pdo->selectOne($templateSql,$templateParams);

	$prevTemplateContent = !empty($templateRes['content']) ? $templateRes['content'] : '';

$layout = 'iframe.layout.php';
$template = 'emailer_template_preview.inc.php';
include_once 'layout/end.inc.php';
?>
