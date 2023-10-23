<?php
ini_set('memory_limit', '-1');
ini_set('max_execution_time', '-1');
include_once dirname(__DIR__) . "/includes/connect.php";
if(!isset($_SESSION['site_access'])){
	$_SESSION["HTTP_REFERER"]=(isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	redirect("site_access.php");
}
include_once dirname(__DIR__) . "/includes/commission.class.php";
error_reporting(E_ALL);
echo "<br/>order_id: ".$_GET['order_id'];
echo "<br/>transaction_tbl_id: ".$_GET['transaction_tbl_id'];
echo "<br/>note: ".$_GET['note'];
echo "<br/>date: ".$_GET['date'];

if(isset($_GET['reverse_commission']) && $_GET['reverse_commission'] == 'yes' && isset($_GET['transaction_tbl_id']) && isset($_GET['order_id']) && isset($_GET['note']) && isset($_GET['date'])) {
	$commObj = new Commission();
	$extra_params = array();
	$extra_params['note'] = $_GET['note'];//"Doesn't Cover Specific Needs";
	$extra_params['date'] = $_GET['date'];//"2020-09-29";
	$extra_params['transaction_tbl_id'] = $_GET['transaction_tbl_id']; //771
	//$commObj->reverseOrderCommissions($_GET['order_id'],$extra_params);//229

	$request_params = array();
    $request_params['order_id'] = $_GET['order_id'];
    $request_params['extra_params'] = $extra_params;
    add_commission_request('reverse_commissions',$request_params);
    pre_print($request_params,false);
}
echo "<br>Completed";
?>
