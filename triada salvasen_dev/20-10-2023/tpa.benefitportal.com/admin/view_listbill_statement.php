<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Payment';
$breadcrumbes[1]['link'] = 'payment_listbills.php';
$breadcrumbes[2]['title'] = 'List Bills';

$list_bill_id = $_GET['list_bill'];

$list_bill_found = false;
if(!empty($list_bill_id)){
	$sqlListBill = "SELECT lb.id,lb.list_bill_no,lb.status,lb.notes,lb.customer_id,lb.due_amount FROM list_bills lb WHERE md5(id)=:id";
	$resListBill = $pdo->selectOne($sqlListBill,array(":id"=>$list_bill_id));

	if($resListBill){
		$notes = $resListBill['notes'];
		$list_bill_found = true;

		$description['ac_message'] = array(
	    'ac_red_1' => array(
		        'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
		    	'title'=>$_SESSION['admin']['display_id'],
		    ),
		    'ac_message_1' => ' read Billing ',
		    'ac_red_2' => array(
		        'title' => $resListBill['list_bill_no'],
		    ),
		);
		$desc = json_encode($description);

		activity_feed(3, $_SESSION['admin']['id'], 'Admin', $resListBill['customer_id'], 'Group', 'Admin Read Billing', $_SESSION['admin']['fname'], $_SESSION['admin']['lname'], $desc);
	}
}


if(!$list_bill_found){
	setNotifyError('No List Bill Found');
	redirect('payment_listbills.php');
}


$template = 'view_listbill_statement.inc.php';
include_once 'layout/end.inc.php';
?>
