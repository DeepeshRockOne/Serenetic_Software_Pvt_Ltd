<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$res = array();
$group_id = $_REQUEST['group_id'];
$company_id = (!empty($_REQUEST['company_id'])?$_REQUEST['company_id']:0);

$prev_balance = 0;
$prev_received_amount = 0;
$prev_payment_date = '';
$balance_forward = 0;
$grand_total = 0;
$amendment = 0;
$due_amount = 0;
$current_amount = 0;
$due_date = '';
$is_auto_draft_set = 'N';
$auto_draft_payment_method = '';
$auto_draft_date = '';
$has_open_list_bill = 'N';
$list_bill_id = 0;
$billing_id = 0;

$group_setting_sql = "SELECT cgs.invoice_broken_locations,cgs.is_auto_draft_set,cgs.billing_id,cgs.auto_draft_date FROM customer_group_settings cgs WHERE cgs.customer_id=:group_id";
$group_setting_row = $pdo->selectOne($group_setting_sql,array(":group_id"=>$group_id));
if(!empty($group_setting_row)) {
	if($company_id == 0) {
		if($group_setting_row['is_auto_draft_set'] == "Y" && !empty($group_setting_row['billing_id']) && strtotime($group_setting_row['auto_draft_date']) > 0) {
			$billing_id = $group_setting_row['billing_id'];
			$auto_draft_date = $group_setting_row['auto_draft_date'];
			$is_auto_draft_set = 'Y';
		}
	} elseif($group_setting_row['invoice_broken_locations'] == "Y") {
		$gc_sql = "SELECT gc.is_auto_draft_set,gc.billing_id,gc.auto_draft_date FROM group_company gc WHERE gc.id=:company_id";
		$gc_row = $pdo->selectOne($gc_sql,array(":company_id"=>$company_id));
		if(!empty($gc_row)) {
			if($gc_row['is_auto_draft_set'] == "Y" && !empty($gc_row['billing_id']) && strtotime($gc_row['auto_draft_date']) > 0) {
				$billing_id = $gc_row['billing_id'];
				$auto_draft_date = $gc_row['auto_draft_date'];
				$is_auto_draft_set = 'Y';
			}
		}
	}
}

if($is_auto_draft_set == "Y") {
	$cb_sql = "SELECT cb.payment_mode,cb.card_type,cb.last_cc_ach_no FROM customer_billing_profile cb WHERE cb.id=:id";
	$cb_row = $pdo->selectOne($cb_sql,array(":id"=>$billing_id));
	if(!empty($cb_row)) {
		if($cb_row['payment_mode'] == "CC") {
            $auto_draft_payment_method = "Auto Draft - ".$cb_row['card_type']." *".$cb_row['last_cc_ach_no'];

        } else if($cb_row['payment_mode'] == "ACH") {
            $auto_draft_payment_method = "Auto Draft - "."ACH *".$cb_row['last_cc_ach_no'];

        } else if($cb_row['payment_mode'] == "Check") {
            $auto_draft_payment_method = "Auto Draft - "."Check";
        }
	}
}

$list_bill_sql = "SELECT lb.grand_total,lb.amendment,lb.due_amount,lb.due_date,lb.received_amount,lb.next_purchase_date,lb.status,lb.id FROM list_bills lb WHERE lb.is_deleted = 'N' and lb.customer_id=:group_id AND lb.company_id=:company_id ORDER BY lb.id DESC LIMIT 1";
$list_bill_where = array(":group_id" => $group_id,":company_id" => $company_id);
$list_bill_row = $pdo->selectOne($list_bill_sql,$list_bill_where); 

if(!empty($list_bill_row)) {
	$grand_total = $list_bill_row['grand_total'];
	$amendment = $list_bill_row['amendment'];
	$due_amount = $list_bill_row['due_amount'];
	$due_date = $list_bill_row['due_date'];
	$current_amount = $list_bill_row['due_amount'] + $list_bill_row['received_amount']; 

	$auto_draft_date = $list_bill_row['next_purchase_date'];
	$has_open_list_bill = ($list_bill_row['status'] == 'open'?"Y":"N");
	$list_bill_id = $list_bill_row['id'];
	
	$prev_list_bill_sql = "SELECT lb.grand_total,lb.amendment,lb.received_amount,lb.status,lb.payment_received_date FROM list_bills lb WHERE lb.is_deleted = 'N' AND lb.status='paid' AND lb.customer_id=:group_id AND lb.company_id=:company_id ORDER BY lb.id DESC LIMIT 1";
	$list_bill_where = array(":group_id" => $group_id,":company_id" => $company_id);
	$prev_list_bill_row = $pdo->selectOne($prev_list_bill_sql,$list_bill_where); 
	if(!empty($prev_list_bill_row)) {
		$prev_balance = ($prev_list_bill_row['grand_total'] + $prev_list_bill_row['amendment']);	
		$prev_received_amount = $prev_list_bill_row['received_amount'];
		$balance_forward = $prev_list_bill_row['amendment'];
		$prev_payment_date = $prev_list_bill_row['payment_received_date'];
	}
}

$res['prev_balance'] = displayAmount($prev_balance);
$res['prev_received_amount'] = "(".displayAmount($prev_received_amount).")";
$res['prev_payment_date'] = ($prev_received_amount > 0?displayDate($prev_payment_date):'');
$res['balance_forward'] = displayAmount($balance_forward);

$res['grand_total'] = displayAmount($grand_total);
$res['amendment'] = displayAmount($amendment);
$res['due_amount'] = displayAmount($due_amount);
$res['current_amount'] = strip_tags(displayAmount2($current_amount));
$res['current_amount_org'] = $current_amount;
$res['due_date'] = displayDate($due_date);

$res['is_auto_draft_set'] = $is_auto_draft_set;
$res['auto_draft_payment_method'] = $auto_draft_payment_method;
$res['auto_draft_date'] = displayDate($auto_draft_date);
$res['has_open_list_bill'] = $has_open_list_bill;
$res['list_bill_id'] = md5($list_bill_id);
echo json_encode($res);
dbConnectionClose();