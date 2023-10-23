<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$group_id = !empty($_GET['group_id']) ? $_GET['group_id'] : 0;
$resource_id = !empty($_GET['id']) ? $_GET['id'] : 0;

if(!empty($resource_id)){
	$selSql="SELECT * from group_resource_link where id=:id";
	$selRes=$pdo->selectOne($selSql,array(":id"=>$resource_id));

	if(!empty($selRes)){
		$group_id = $selRes['group_id'];
		$label = $selRes['label'];
		$url = $selRes['url'];
	}
}

$template = 'groups_add_resource.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
