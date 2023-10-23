<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
has_access(75);
$id = $_GET['id'];
$resResource = $pdo->selectOne("SELECT id,resource_name,file from portal_resources where md5(id)=:id and is_deleted='N'",array(":id"=>$id));

$trigger_id = 105;//use this trigger_id =  101 For Admin - Share Resource
$triggersArr = $pdo->selectOne("SELECT * from triggers where id=:id and is_deleted='N'",array(":id"=>$trigger_id));

$exStylesheets = array('thirdparty/summernote-master/dist/summernote.css'.$cache);
$exJs = array(
	'thirdparty/summernote-master/dist/popper.js'.$cache,
	'thirdparty/summernote-master/dist/summernote.js'.$cache,
	'thirdparty/ckeditor/ckeditor.js'
);

$template = "share_resources.inc.php";
include_once 'layout/iframe.layout.php';
?>