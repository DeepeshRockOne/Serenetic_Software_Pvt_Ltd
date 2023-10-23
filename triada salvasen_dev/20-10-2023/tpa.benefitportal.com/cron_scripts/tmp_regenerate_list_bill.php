<?php 
include_once dirname(__DIR__) . "/cron_scripts/connect.php";
require_once dirname(__DIR__) . '/includes/list_bill.class.php';
$ListBill = new ListBill();
$date = $_GET['date'];
$extra = array('type' => 'test','today' => $date);
$list_bill_id_arr = $ListBill->generateListBill(false,115,0,'',$extra);

?>