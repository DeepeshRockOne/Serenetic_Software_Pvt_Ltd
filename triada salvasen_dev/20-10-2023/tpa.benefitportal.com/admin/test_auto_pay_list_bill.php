<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
require_once dirname(__DIR__) . '/includes/list_bill.class.php';
require_once dirname(__DIR__) . '/includes/function.class.php';
$validate = new Validation();
$listBillObj = new ListBill();
$function_list = new functionsList();

if ($SITE_ENV == 'Live') {
  redirect('404.php');
} else {
  $admin_id = $_SESSION['admin']['id'];
  $validate->string(array('required' => true, 'field' => 'paylbDate', 'value' => $_GET['date']), array('required' => 'Date is required'));

  if ($validate->isValid()) {
    

    $today = date('Y-m-d', strtotime($_GET['date']));
    // **** Call auto list bill payment script curl call start ****
    $url = $HOST."/cron_scripts/auto_list_bill_payment.php?date=".$today;

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    $script_response = curl_exec($ch);
    curl_close($ch);
    // **** Call auto list bill payment script curl call end ****

    setNotifyError("Auto Payment List Bill Successfully");
    $response['status'] = "success";
  } else {
    $response['status'] = "fail";
    $response['errors'] = $validate->getErrors();
  }
  header('Content-Type: application/json');
  echo json_encode($response);
  dbConnectionClose();
  exit;
}