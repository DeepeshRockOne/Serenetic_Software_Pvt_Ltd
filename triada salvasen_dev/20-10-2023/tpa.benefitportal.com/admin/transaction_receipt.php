<?php
  include_once dirname(__FILE__) . '/layout/start.inc.php';

$transId = $_REQUEST["transId"];
$resTrans = array();

$tz = new UserTimeZone('M d, Y h:i:s A T', $_SESSION['admin']['timezone']);

if(!empty($transId)){
  $selTrans = "SELECT 
                  IF(c.type='Group',c.business_name,CONCAT(c.fname,' ',c.lname)) as mbrName,
                  c.id as mbrId,
                  c.rep_id as mbrDispId,
                  c.cell_phone as mbrPhone,
                  c.email as mbrEmail,
                  c.sponsor_id,
                  c.user_name as mbrUserName,

                  t.id as transId,
                  t.transaction_id as processorTransId,
                  t.billing_info as billingInfo,
                  t.created_at as transactionDate,
                  t.transaction_status as transStatus, 
                  t.reason as transReason,
                  t.transaction_response as processorResponse,
                  t.order_type,
                  t.credit as creditAmt,
                  t.debit as debitAmt,

                  o.id as odrId,
                  o.display_id as odrDispId,
                  o.post_date as odrPostDate,
                  o.is_list_bill_order,
                  c.type as user_type

          FROM transactions t
          JOIN orders o ON(t.order_id=o.id)
          LEFT JOIN customer c ON (c.id = o.customer_id)
          WHERE md5(t.id) = :transId ORDER BY t.id DESC";
  $resTrans = $pdo->selectOne($selTrans, array(':transId' => makeSafe($transId)));
}

if(empty($resTrans)){
  setNotifyError("Transaction Receipt not found");
  redirect("payment_transaction.php");
}
$orderDispId = checkIsset($resTrans['odrDispId']);
$transId = checkIsset($resTrans["transId"]);

// Read transactions activity code start
  $description['ac_message'] =array(
    'ac_red_1'=>array(
      'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
      'title'=>$_SESSION['admin']['display_id'],
    ),
    'ac_message_1' =>'  viewed transaction order ',
    'ac_red_2'=>array(
      'href'=> "transaction_receipt.php?transId=".md5($transId),
      'title'=>$orderDispId,
    ),
  ); 

  activity_feed(3, $_SESSION['admin']['id'], 'Admin',$transId, 'transactions','Viewed Transactions', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
// Read transactions activity code ends

$mbrEmail = checkIsset($resTrans["mbrEmail"]);
$orderId = checkIsset($resTrans["odrId"]);
$orderDispId = checkIsset($resTrans['odrDispId']);
$transStatus = checkIsset($resTrans["transStatus"]);
$transDate = !empty($resTrans["transactionDate"]) ? date("m/d/Y",strtotime($resTrans["transactionDate"])) : "";
$odrPostDate = !empty($resTrans["odrPostDate"]) ? date("m/d/Y",strtotime($resTrans["odrPostDate"])) : "";
$billArr = !empty($resTrans["billingInfo"]) ? json_decode($resTrans["billingInfo"],true) : array();

$transTotal = checkIsset($resTrans["order_type"]) == "Credit" ? $resTrans["creditAmt"] : $resTrans["debitAmt"];
$subTotal = 0;
$stepFeePrice = 0;
$serviceFeePrice = 0;

$reason = "";
if(in_array($transStatus, array("Payment Approved"))){
  $tblClass = "table-success";
  $txtClass = "text-success";
  $iconClass = "fa-check-circle";
}else if(in_array($transStatus, array("Pending Settlement","Post Payment"))){
  $tblClass = "table-warning";
  $txtClass = "text-warning";
  $iconClass = "fa-minus-circle";
}else if(in_array($transStatus, array("Refund","Void","Cancelled","Chargeback","Payment Returned","Payment Declined"))){
  $tblClass = "table-danger";
  $txtClass = "text-danger";
  $iconClass = "fa-times-circle";
}else{
  $tblClass = "table-success";
  $txtClass = "text-success";
  $iconClass = "fa-check-circle";
}

$reason = checkIsset($resTrans["transReason"]);
if($transStatus == "Payment Declined"){
  $processorResponse = !empty($resTrans['processorResponse']) ? json_decode($resTrans['processorResponse'],true) : array();
  $reason = get_declined_reason_from_tran_response($resTrans['processorResponse'],false,$reason);
}

$billType = (checkIsset($billArr["payment_mode"]) == "CC" ? checkIsset($billArr["card_type"]) : (checkIsset($billArr["payment_mode"]) !='' ? $billArr["payment_mode"] : "ACH" ));

$exJs = array('thirdparty/jquery-match-height/js/jquery.matchHeight.js');

$template = 'transaction_receipt.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>