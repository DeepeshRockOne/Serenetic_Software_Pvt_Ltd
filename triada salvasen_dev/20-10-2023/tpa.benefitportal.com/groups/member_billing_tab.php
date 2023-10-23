<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
group_has_access(2);
$customer_id = $_POST['id'];
$billing_info = $pdo->select("SELECT md5(id) as id,created_at,fname,lname,payment_mode,last_cc_ach_no,if(payment_mode ='CC',card_type,'ACH') as card_type,is_default FROM customer_billing_profile where is_direct_deposit_account='N' AND md5(customer_id)=:id and is_deleted='N' order by created_at  DESC",array(":id"=>$customer_id));
$res = array();

if(isset($_POST['bill_id']) && (isset($_POST["action"]) && $_POST["action"]=="updDefault")){
	$billId = $_POST['bill_id'];
	$old_default = $pdo->selectOne("SELECT id,customer_id,is_default,last_cc_ach_no,payment_mode FROM customer_billing_profile WHERE  md5(customer_id)=:customer_id AND is_default='Y'", array(
		':customer_id' => makesafe($customer_id),
	));

	$billing_info_details = $pdo->selectOne("SELECT id,customer_id,is_default,last_cc_ach_no,payment_mode FROM customer_billing_profile WHERE md5(id)=:id AND md5(customer_id)=:customer_id", array(
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

		$crep_id = getname("customer",$customer_id,'rep_id','md5(id)');
		$activityFeedDesc['ac_message'] =array(
			'ac_red_1'=>array(
				'href'=>$ADMIN_HOST.'/groups_details.php?id='.md5($_SESSION['groups']['id']),
				'title'=>$_SESSION['groups']['rep_id'],
			),
            'ac_message_1' =>' updated Default Billing profile For member ',
            'ac_red_2'=>array(
				'href'=>$ADMIN_HOST.'/members_details.php?id='.$customer_id,
				'title'=>$crep_id,
            ),
            'ac_message_2' =>"<br>From : ".$old_default['payment_mode'].' (*'.$old_default['last_cc_ach_no'].') To '.$billing_info_details['payment_mode'].' (*'.$billing_info_details['last_cc_ach_no'].')',
        );

		activity_feed(3,$_SESSION['groups']['id'], 'Group', $billing_info_details["customer_id"], 'Customer', 'Member Billing Profile',$_SESSION['groups']['fname'],"",json_encode($activityFeedDesc));
		$res["status"] = "success";
	}else{
		$res["status"] = "fail";
	}
	echo json_encode($res);
	exit;
}
include_once 'tmpl/member_billing_tab.inc.php';
?>