<?php
ini_set('memory_limit', '-1');
ini_set('max_execution_time',0);
include_once dirname(__DIR__) . "/cron_scripts/connect.php";
require_once dirname(__DIR__) . '/includes/list_bill.class.php';
$ListBill = new ListBill();
if(isset($_GET['group_id'])) {
    $regenerate_group_id=$_GET['group_id'];
    $regenerate = false;
    $regenerate_id = 0;
    $regenerate_company_id = '';
    if(isset($_GET['regenerate'])) {
        $regenerate = true;
        $regenerate_id = $_GET['regenerate_id'];
        $regenerate_company_id = $_GET['regenerate_company_id'];
    }
    $list_bill_id_arr = $ListBill->generateListBill($regenerate,$regenerate_group_id,$regenerate_id,0);
}
echo "Completed";