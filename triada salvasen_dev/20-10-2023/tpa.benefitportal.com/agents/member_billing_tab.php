<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$customer_id = $_POST['id'];
$has_full_access = agent_has_member_access($customer_id);
$billing_info = $pdo->select("SELECT md5(id) as id,created_at,fname,lname,payment_mode,last_cc_ach_no,if(payment_mode ='CC',card_type,'ACH') as card_type,is_default FROM customer_billing_profile where md5(customer_id)=:id and is_deleted='N' order by created_at  DESC",array(":id"=>$customer_id));
$res = array();

if(isset($_POST['bill_id']) && (isset($_POST["action"]) && $_POST["action"]=="updDefault")){
	$billId = $_POST['bill_id'];
	$billing_info_details = $pdo->selectOne("SELECT id,customer_id FROM customer_billing_profile WHERE md5(id)=:id AND md5(customer_id)=:customer_id", array(
		':id' => makesafe($billId),
		':customer_id' => makesafe($customer_id),
	));

	if (!empty($billing_info_details["id"])) {
		$updParams = array(
			'is_default' => 'N',
			'updated_at' => 'msqlfunc_NOW()',
		);
		$updWhere = array(
			'clause' => 'customer_id=:customer_id',
			'params' => array(
				':customer_id' => $billing_info_details["customer_id"],
			),
		);

		$pdo->update('customer_billing_profile', $updParams, $updWhere);

		$params = array(
			'is_default' => 'Y',
			'updated_at' => 'msqlfunc_NOW()',
		);
		$where = array(
			'clause' => 'id=:id and customer_id=:customer_id',
			'params' => array(
				':id' => $billing_info_details["id"],
				':customer_id' => $billing_info_details["customer_id"]
			),
		);

		$pdo->update('customer_billing_profile', $params, $where);
		$res["status"] = "success";
	}else{
		$res["status"] = "fail";
	}
	echo json_encode($res);
	exit;
}
include_once 'tmpl/member_billing_tab.inc.php';
?>