<?php
include_once __DIR__ . '/includes/connect.php';
$lead_quote_id = isset($_GET['id'])?$_GET['id']:'0';
$sent_via = isset($_GET['sent_via'])?$_GET['sent_via']:'Both';
$enrollmentLocation = isset($_GET['enrollmentLocation'])?$_GET['enrollmentLocation']:'';
$is_add_product = isset($_GET['is_add_product'])?$_GET['is_add_product']:'N';
$lead_quote_sql = "SELECT lq.id,lq.is_opened,lq.link_opened_at,l.lead_id as lead_rep_id,l.fname,l.lname,lq.order_ids as order_id,lq.customer_ids as customer_id
					FROM lead_quote_details lq
					JOIN leads l ON(l.id = lq.lead_id)
					WHERE lq.id=:id";
$lead_quote_row = $pdo->selectOne($lead_quote_sql, array(":id"=>$lead_quote_id));
if(empty($lead_quote_row)) {
	setNotifyError("Enrollment Application Not Found");
	echo "<script>window.parent.location.reload();</script>";
	exit();
}

$customer_id = $lead_quote_row['customer_id'];
$link_opened_at = $lead_quote_row['link_opened_at'];

$customer_sql = "SELECT c.id,c.sponsor_id,s.type as sponsor_type,c.fname,c.lname,c.rep_id,c.email,c.is_password_set 
					FROM customer c 
					JOIN customer s ON(s.id = c.sponsor_id)  
					WHERE c.id=:id";
$customer_row = $pdo->selectOne($customer_sql,array(":id" => $customer_id));

$sponsor_billing_method = "individual";
if($customer_row['sponsor_type'] == "Group") {
	$sqlBillingType = "SELECT billing_type FROM customer_group_settings where customer_id=:customer_id";
	$resBillingType = $pdo->selectOne($sqlBillingType,array(":customer_id"=>$customer_row['sponsor_id']));
	if(!empty($resBillingType)){
		$sponsor_billing_method = $resBillingType['billing_type'];
	}
}

$group_ord_row = array();
$tran_res = array();
$ord_row = array();
if(strtotime($link_opened_at)) {
	$ord_row = $pdo->selectOne("SELECT * FROM orders WHERE id=:order_id",array(":order_id" => $lead_quote_row['order_id']));
	$ord_row['future_payment'] = "N";

	$tran_res = $pdo->select("SELECT * FROM `transactions` WHERE order_id=:order_id AND created_at > :created_at ORDER BY id ASC",array(":order_id" => $lead_quote_row['order_id'],':created_at' => $link_opened_at));
	if(count($tran_res) == 1) {
		if($tran_res[0]['transaction_status'] == "Post Payment") {
			$ord_row['future_payment'] = "Y";			
		}
	}
	if($sponsor_billing_method !== "individual") {
		$group_ord_row = $pdo->selectOne("SELECT * FROM group_orders WHERE id=:order_id AND status='Payment Approved'",array(":order_id" => $lead_quote_row['order_id']));
	}
}




$is_approved_tran = false;
$template = 'enrollment_status.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>