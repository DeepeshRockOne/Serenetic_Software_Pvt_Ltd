<?php
ini_set('memory_limit', '-1');
ini_set('max_execution_time',0);
include_once dirname(__DIR__) . "/cron_scripts/connect.php";
require_once dirname(__DIR__) . '/includes/list_bill.class.php';
$ListBill = new ListBill();
$is_regenerate=false;
$regenerate_group_id=0;
$regenerate_id=0;
$regenerate_company_id='';
$extra = array(
  'type' => 'test',
  'today' => '2022-03-20',
);
$list_bill_id_arr = $ListBill->generateListBill($is_regenerate,$regenerate_group_id,$regenerate_id,$regenerate_company_id,$extra);
$sendEmailSummary = array();
if(!empty($list_bill_id_arr)){
  $trigger_param = array(
    'Inseted List bill Id' => implode(",", $list_bill_id_arr)
  );
  $sendEmailSummary[] = $trigger_param;
}
pre_print($sendEmailSummary,false);
$DEFAULT_LIST_BILL_EMAIL = array('kamlesh@cyberxllc.com');
trigger_mail_to_email($sendEmailSummary, $DEFAULT_LIST_BILL_EMAIL, "Generate List Bill ID", array(), 2);
?>