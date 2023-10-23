<?php
ini_set('memory_limit', '-1');
ini_set('max_execution_time',0);
include_once dirname(__DIR__) . "/cron_scripts/connect.php";
require_once dirname(__DIR__) . '/includes/list_bill.class.php';
$ListBill = new ListBill();

$list_bill_id_arr = $ListBill->generateListBill();

$sendEmailSummary = array();
if(!empty($list_bill_id_arr)){
  $trigger_param = array(
    'Inseted List bill Id' => implode(",", $list_bill_id_arr)
  );
  $sendEmailSummary[] = $trigger_param;
}

$DEFAULT_LIST_BILL_EMAIL = array('karan@cyberxllc.com');
trigger_mail_to_email($sendEmailSummary, $DEFAULT_LIST_BILL_EMAIL, "Generate List Bill ID", array(), 2);
dbConnectionClose();
?>