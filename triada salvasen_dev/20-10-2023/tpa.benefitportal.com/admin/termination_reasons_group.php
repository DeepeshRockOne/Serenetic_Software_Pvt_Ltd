<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$rule_id=!empty($_GET['id']) ? $_GET['id'] : 0;
$is_clone=!empty($_GET['clone']) ? $_GET['clone'] : 'N';

$sqlTerminationReason = "SELECT * FROM termination_reason where is_deleted='N' AND id=:id";
$resTerminationReason = $pdo->selectOne($sqlTerminationReason,array(":id"=>$rule_id));

$incr="";
$sch_params = array();

if(!empty($resTerminationReason)){
	$reason=$resTerminationReason['name'];
	$is_qualifies_for_cobra=$resTerminationReason['is_qualifies_for_cobra'];

	if($is_clone == 'Y'){
		$rule_id = 0;
		$group_id = 0;
	}
}

$template = "termination_reasons_group.inc.php";
include_once 'layout/iframe.layout.php';
?>