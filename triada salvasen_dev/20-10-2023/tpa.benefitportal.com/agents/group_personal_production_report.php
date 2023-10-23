<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$group_id = $_GET['group_id'];

$sqlGroup = "SELECT c.id,CONCAT(c.fname,' ',c.lname) as name,c.rep_id,c.business_name,c.sponsor_id
			from customer c  
			where c.is_deleted='N' and md5(c.id)=:id";
$resGroup = $pdo->selectOne($sqlGroup,array(":id"=>$group_id));

if(!empty($resGroup)){
	$group_id = $resGroup['id'];
	$agent_id = $resGroup['sponsor_id'];
}

$exStylesheets = array('thirdparty/select2/css/select2.css');
$exJs = array('thirdparty/select2/js/select2.full.min.js');

$template = 'group_personal_production_report.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
