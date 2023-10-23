<?php
include_once __DIR__ . '/includes/connect.php';

$list_bill_id = $_GET['list_bill'];
$breadcrumbes[0]['title'] = '<i class="material-icons">home</i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Billing';
$breadcrumbes[1]['link'] = 'group_billing.php';
$breadcrumbes[2]['title'] = 'List Bill';
$breadcrumbes[2]['link'] = 'view_listbill_statement.php?list_bill='.$list_bill_id;
$breadcrumbes[3]['title'] = 'Amendment';



$list_bill_found = false;
if(!empty($list_bill_id)){
	$sqlListBill = "SELECT lb.list_bill_no FROM list_bills lb WHERE md5(id)=:id AND lb.status='open'";
	$resListBill = $pdo->selectOne($sqlListBill,array(":id"=>$list_bill_id));

	if($resListBill){
		$list_bill_found = true;
	}
}

$sqlMember = "SELECT c.id,c.fname,c.lname,c.rep_id FROM customer c 
			JOIN list_bill_details lbd ON (c.id = lbd.customer_id)
			WHERE md5(lbd.list_bill_id) = :id GROUP BY c.id";
$resMember = $pdo->select($sqlMember,array(":id"=>$list_bill_id));

if(!$list_bill_found){
	setNotifyError('No List Bill Amendment Found');
	redirect('group_billing.php');
}


$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css' . $cache);
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js' . $cache);

$template = 'add_amendment_listbill.inc.php';
$layout = 'main.layout.php';
include_once 'layout/end.inc.php';
?>
