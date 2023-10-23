<?php
include_once __DIR__ . '/includes/connect.php';
$res = array();
$list_bill_id = $_REQUEST['list_bill_id'];
$location = isset($_REQUEST['location'])?$_REQUEST['location']:'admin';
$list_bill_sql = "SELECT lb.customer_id,lb.company_id,lb.due_date,lb.due_amount FROM list_bills lb WHERE lb.is_deleted = 'N' AND lb.id = :id";
$list_bill_where = array(':id' => makeSafe($list_bill_id));
$list_bill_row = $pdo->selectOne($list_bill_sql,$list_bill_where);


$sch_params = array();
$incr = '';

$incr .= " AND customer_id=:customer_id";
$sch_params[':customer_id'] = $list_bill_row['customer_id'];

$tbl_incr = '';

if($list_bill_row['company_id'] > 0) {
	$incr .= " AND cbp.company_id=:company_id";
	$sch_params[':company_id'] = $list_bill_row['company_id'];
	$tbl_incr = "JOIN group_company gc ON(gc.id=cbp.company_id AND gc.is_deleted='N')";
}

$sponsor_id = getname('customer',$list_bill_row['customer_id'],'sponsor_id');
$pyament_methods = get_pyament_methods($sponsor_id);
$is_cc_accepted = $pyament_methods['is_cc_accepted'];
$is_ach_accepted = $pyament_methods['is_ach_accepted'];
$acceptable_cc = $pyament_methods['acceptable_cc'];

$billing_profile_opt = '<option data-hidden="true"></option>';
$billing_sql = "SELECT cbp.id,cbp.payment_mode,cbp.card_type,cbp.last_cc_ach_no,cbp.is_default 
				FROM customer_billing_profile cbp
				$tbl_incr
				WHERE cbp.is_direct_deposit_account='N' AND cbp.payment_mode != 'Check' AND cbp.is_deleted='N' $incr GROUP BY cbp.card_no,cbp.ach_account_number";
$billing_res = $pdo->select($billing_sql,$sch_params);

if(!empty($billing_res)) {
	foreach ($billing_res as $key => $billing_row) {

		if($billing_row['payment_mode'] == "CC") {
			if($is_cc_accepted == false) {
				continue;

			} elseif(!in_array($billing_row['card_type'],$acceptable_cc)) {
				continue;
			}
		} else if($billing_row['payment_mode'] == "ACH" && $is_cc_accepted == false) {
			continue;
		}

		$option_text = '';
		if($billing_row['payment_mode'] == "CC") {
		    $option_text = $billing_row['card_type'].' *'.$billing_row['last_cc_ach_no'];

		} elseif($billing_row['payment_mode'] == "ACH") {
		    $option_text = 'ACH *'.$billing_row['last_cc_ach_no'];

		} elseif($billing_row['payment_mode'] == "Check") {
		    $option_text = 'Check';
		}

		if($billing_row['is_default'] == 'Y') {
		    $option_text .= " (Default)";
		}
		$billing_profile_opt .= '<option value="'.$billing_row['id'].'">'.$option_text.'</option>';
	}
}
if($location == "admin") {
	$billing_profile_opt .= "<option value='record_check_payment'>Record Payment (Check)</option>";
}
//$billing_profile_opt .= "<option value='new_billing'>+ New Payment Method</option>";

$res['due_amount'] = displayAmount($list_bill_row['due_amount'],2);
$res['due_date'] = date('m/d/Y',strtotime($list_bill_row['due_date']));
$res['billing_profile_opt'] = $billing_profile_opt;
$res['list_bill_row'] = $list_bill_row;
echo json_encode($res);
dbConnectionClose();
