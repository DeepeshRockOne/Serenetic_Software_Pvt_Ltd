<?php
include_once __DIR__ . '/includes/connect.php';
$location = isset($_REQUEST['location'])?$_REQUEST['location']:'admin';
$customer_id = $_GET['id'];

$custInfo = $pdo->selectOne("SELECT id,fname,lname,rep_id from customer where md5(id)=:id and is_deleted='N'",array(":id"=>$customer_id));

$product_payment = array();
if(!empty($custInfo['id'])){
	$customer_id = $custInfo['id'];
	$selPrd = "SELECT ws.id,ws.website_id,p.name,p.product_code,p.type,p.product_type 
				FROM website_subscriptions ws 
				JOIN prd_main p ON(p.id=ws.product_id and p.is_deleted='N') 
				WHERE p.product_type NOT IN('Healthy Step','ServiceFee') AND ws.customer_id=:customer_id 
				AND ws.termination_date is NULL
				ORDER BY p.type DESC,p.name ASC";

	$resPrdPayment = $pdo->select($selPrd,array(":customer_id"=>$custInfo['id']));			
}

$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache);
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache);

$template = 'make_payment.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>