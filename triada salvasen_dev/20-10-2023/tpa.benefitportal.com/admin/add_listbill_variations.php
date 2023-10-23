<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$rule_id=!empty($_GET['id']) ? $_GET['id'] : 0;
$is_clone=!empty($_GET['clone']) ? $_GET['clone'] : 'N';

$incr="";
$sch_params = array();
$days_prior_pay_period='';
$auto_set_payment_received='';
$auto_set_payment_received_inside_sys='';
$auto_pay_day='';

$sqlListBillOptions = "SELECT * FROM list_bill_options where rule_type='Variation' and is_deleted='N' AND id=:id";
$resListBillOptions = $pdo->selectOne($sqlListBillOptions,array(":id"=>$rule_id));

if(!empty($resListBillOptions)){
	$group_id=$resListBillOptions['group_id'];
	$billing_setting=$resListBillOptions['billing_setting'];
	$days_prior_pay_period=$resListBillOptions['days_prior_pay_period'];
	$auto_set_payment_received=$resListBillOptions['auto_set_payment_received'];
	$auto_set_payment_received_inside_sys=$resListBillOptions['auto_set_payment_received_inside_sys'];
	$auto_pay_day=$resListBillOptions['auto_payment_days'];

	if($is_clone == 'Y'){
		$rule_id = 0;
		$group_id = 0;
		$sqlListBillOptions = "SELECT group_concat(group_id) as group_id FROM list_bill_options where rule_type='Variation' and is_deleted='N'";
		$resListBillOptions = $pdo->selectOne($sqlListBillOptions);

		if(!empty($resListBillOptions) && !empty($resListBillOptions['group_id'])){
			$incr .= " AND id NOT IN (".$resListBillOptions['group_id'].")";
		}
	}else{
		$incr .= " AND id=:id";
		$sch_params[':id']=$group_id;
	}
	
}else{
	$sqlListBillOptions = "SELECT group_concat(group_id) as group_id FROM list_bill_options where rule_type='Variation' and is_deleted='N'";
	$resListBillOptions = $pdo->selectOne($sqlListBillOptions);

	if(!empty($resListBillOptions) && !empty($resListBillOptions['group_id'])){
		$incr .= " AND id NOT IN (".$resListBillOptions['group_id'].")";
	}
}

$sqlGroup = "SELECT id,fname,lname,rep_id,business_name FROM customer where is_deleted='N' AND type='Group' AND status='Active' $incr";
$resGroup = $pdo->select($sqlGroup,$sch_params);

$prior_days_range=range(1,10);
$auto_pay_day_range=range(1,9);


$template = "add_listbill_variations.inc.php";
include_once 'layout/iframe.layout.php';
?>