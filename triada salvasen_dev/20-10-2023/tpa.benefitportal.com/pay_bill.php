<?php
include_once __DIR__ . '/includes/connect.php';
$location = isset($_REQUEST['location'])?$_REQUEST['location']:'admin';
if($location == "group") {
	$group_id = md5($_SESSION['groups']['id']);
} else {
	$group_id = isset($_REQUEST['group_id'])?$_REQUEST['group_id']:0;
}

$list_bill_id = (isset($_REQUEST['list_bill_id'])?$_REQUEST['list_bill_id']:'');

if(!empty($group_id)) {
	$list_bill_sql = "SELECT lb.id,lb.list_bill_no FROM list_bills lb
					WHERE 
					lb.is_deleted = 'N' AND 
					lb.status IN('open') AND 
					md5(lb.customer_id) =:group_id 
					GROUP BY lb.id 
					ORDER BY lb.id ASC";
	$list_bill_res = $pdo->select($list_bill_sql,array(":group_id" => $group_id));
} else if(!empty($list_bill_id)) {
	$list_bill_sql = "SELECT lb.id,lb.list_bill_no FROM list_bills lb
					WHERE 
					lb.is_deleted = 'N' AND 
					lb.status IN('open') AND 
					md5(lb.id) =:list_bill_id 
					ORDER BY lb.id ASC";
	$list_bill_res = $pdo->select($list_bill_sql,array(":list_bill_id" => $list_bill_id));
}

$template = 'pay_bill.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>
