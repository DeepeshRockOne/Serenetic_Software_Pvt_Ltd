<?php
include_once __DIR__ . '/includes/connect.php'; 
$group_id = $_REQUEST['group_id'];
$company_id = isset($_REQUEST['company_id'])?$_REQUEST['company_id']:0;

if(!empty($company_id)) {
	$gc_sql = "SELECT gc.*,cb.payment_mode,cb.card_type,cb.last_cc_ach_no  
					FROM group_company gc 
					LEFT JOIN customer_billing_profile cb ON(cb.id=gc.billing_id)
					WHERE gc.is_deleted='N' AND md5(gc.id)=:company_id";
	$row = $pdo->selectOne($gc_sql,array(":company_id"=>$company_id));
	$group_id_org = $row['group_id'];
	$company_id_org = $row['id'];
} else {
	$group_setting_sql = "SELECT cgs.*,c.business_name as name,cb.payment_mode,cb.card_type,cb.last_cc_ach_no 
							FROM customer_group_settings cgs 
							JOIN customer c ON(c.id = cgs.customer_id) 
							LEFT JOIN customer_billing_profile cb ON(cb.id=cgs.billing_id)
							WHERE md5(cgs.customer_id)=:group_id";
	$row = $pdo->selectOne($group_setting_sql,array(":group_id"=>$group_id));
	$group_id_org = $row['customer_id'];
	$company_id_org = 0;
}

$is_ach = 'N';
$is_cc = 'N';
$is_check = 'N';

$pay_options = $pdo->selectOne("SELECT * FROM group_pay_options WHERE is_deleted='N' AND md5(group_id)=:group_id",array(":group_id"=>$group_id));
if(empty($pay_options)) {
	$pay_options = $pdo->selectOne("SELECT * FROM group_pay_options WHERE is_deleted='N' AND rule_type='Global'");
}

if($pay_options) {
	$is_ach = $pay_options['is_ach'];
	$is_cc = $pay_options['is_cc'];
	$is_check = $pay_options['is_check'];
}

$sponsor_id = getname('customer',$group_id_org,'sponsor_id');
$pyament_methods = get_pyament_methods($sponsor_id);
$is_cc_accepted = $pyament_methods['is_cc_accepted'];
$is_ach_accepted = $pyament_methods['is_ach_accepted'];
$acceptable_cc = $pyament_methods['acceptable_cc'];
if($is_cc_accepted == false) {
	$is_cc = 'N';
}
if($is_ach_accepted == false) {
	$is_ach = 'N';
}

$ach_billing_opts = "<option data-hidden='true'></option>";
$cc_billing_opts = "<option data-hidden='true'></option>";
$check_billing_opts = '';
$billing_res = $pdo->select("SELECT cb.id,cb.payment_mode,cb.card_type,cb.last_cc_ach_no  FROM customer_billing_profile cb WHERE cb.is_deleted='N' AND md5(cb.customer_id)=:customer_id",array(":customer_id"=>$group_id));
if(!empty($billing_res)) {
	foreach ($billing_res as $billing_row) {
		$selected = '';
		if(!empty($row['billing_id'])) {
			$selected = ($row['billing_id'] == $billing_row['id']?'selected':'');
		}
		if($billing_row['payment_mode'] == "CC") {
			if(in_array($billing_row['card_type'],$acceptable_cc)) {
				$cc_billing_opts .= "<option value='".$billing_row['id']."' ".$selected.">".$billing_row['card_type']." *".$billing_row['last_cc_ach_no']."</option>";
			}

		} elseif($billing_row['payment_mode'] == "ACH") {
			$ach_billing_opts .= "<option value='".$billing_row['id']."' ".$selected."> ACH *".$billing_row['last_cc_ach_no']."</option>";

		} elseif($billing_row['payment_mode'] == "Check") {
			if(empty($check_billing_opts)) {
				$check_billing_opts .= "<option value='".$billing_row['id']."' ".$selected.">Check</option>";
			}
		}
	}
}

$exStylesheets = array(
	'thirdparty/bootstrap-datepicker-master/css/datepicker.css'.$cache
);
$exJs = array(
	'thirdparty/bootstrap-datepicker-master/js/bootstrap-datepicker.js'.$cache
);
$template = 'auto_draft_setting.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>
