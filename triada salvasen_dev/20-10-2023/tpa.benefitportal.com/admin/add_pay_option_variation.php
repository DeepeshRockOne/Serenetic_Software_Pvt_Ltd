<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$rule_id=!empty($_GET['id']) ? $_GET['id'] : 0;
$is_clone=!empty($_GET['clone']) ? $_GET['clone'] : 'N';

$sqlPayOptions = "SELECT * FROM group_pay_options where rule_type='Variation' and is_deleted='N' AND id=:id";
$resPayOptions = $pdo->selectOne($sqlPayOptions,array(":id"=>$rule_id));

$incr="";
$sch_params = array();

if(!empty($resPayOptions)){
	$group_id=$resPayOptions['group_id'];
	$is_cc=$resPayOptions['is_cc'];
	$is_check=$resPayOptions['is_check'];
	$is_ach=$resPayOptions['is_ach'];

	$cc_additional_charge = $resPayOptions['cc_additional_charge'];
	$cc_charge_type = $resPayOptions['cc_charge_type'];
	$cc_charge = $resPayOptions['cc_charge'];
	
	$check_additional_charge = $resPayOptions['check_additional_charge'];
	$check_charge = $resPayOptions['check_charge'];
	
	$remit_to_address = $resPayOptions['remit_to_address'];
	if($is_clone == 'Y'){
		$rule_id = 0;
		$group_id = 0;
		$sqlPayOptions = "SELECT group_concat(group_id) as group_id FROM group_pay_options where rule_type='Variation' and is_deleted='N'";
		$resPayOptions = $pdo->selectOne($sqlPayOptions);

		if(!empty($resPayOptions) && !empty($resPayOptions['group_id'])){
			$incr .= " AND id NOT IN (".$resPayOptions['group_id'].")";
		}
	}else{
		$incr .= " AND id=:id";
		$sch_params[':id']=$group_id;
	}
	
}else{
	$sqlPayOptions = "SELECT group_concat(group_id) as group_id FROM group_pay_options where rule_type='Variation' and is_deleted='N'";
	$resPayOptions = $pdo->selectOne($sqlPayOptions);

	if(!empty($resPayOptions) && !empty($resPayOptions['group_id'])){
		$incr .= " AND id NOT IN (".$resPayOptions['group_id'].")";
	}
}

$sqlGroup = "SELECT id,fname,lname,rep_id,business_name FROM customer where type='Group' AND status='Active' $incr";
$resGroup = $pdo->select($sqlGroup,$sch_params);

$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache);
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache);

$template = "add_pay_option_variation.inc.php";
include_once 'layout/iframe.layout.php';
?>