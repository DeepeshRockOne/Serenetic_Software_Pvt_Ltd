<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$breadcrumbes[0]['title'] = '<i class="material-icons">home</i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Billing';
$breadcrumbes[1]['link'] = 'group_billing.php';
$breadcrumbes[2]['title'] = 'List Bill';

$list_bill_id = $_GET['list_bill'];

$list_bill_found = false;
if(!empty($list_bill_id)){
	$sqlListBill = "SELECT lb.id,lb.list_bill_no,lb.status,lb.notes,lb.due_amount FROM list_bills lb WHERE md5(id)=:id";
	$resListBill = $pdo->selectOne($sqlListBill,array(":id"=>$list_bill_id));

	if($resListBill){
		$notes = $resListBill['notes'];
		$list_bill_found = true;

		$description['ac_message'] = array(
	    'ac_red_1' => array(
		        'href'=>$ADMIN_HOST.'/groups_details.php?id='.md5($_SESSION['groups']['id']),
		    	'title'=>$_SESSION['groups']['rep_id'],
		    ),
		    'ac_message_1' => ' read Billing ',
		    'ac_red_2' => array(
		        'title' => $resListBill['list_bill_no'],
		    ),
		);
		$desc = json_encode($description);

		activity_feed(3, $_SESSION['groups']['id'], 'Group', $resListBill['id'], 'list_bills', 'Group Read Billing', $_SESSION['groups']['fname'], $_SESSION['groups']['lname'], $desc);
	}
}


if(!$list_bill_found){
	setNotifyError('No List Bill Found');
	redirect('group_billing.php');
}

$template = 'view_listbill_statement.inc.php';
$layout = 'main.layout.php';
include_once 'layout/end.inc.php';
?>
