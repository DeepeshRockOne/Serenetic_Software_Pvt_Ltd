<?php
  include_once __DIR__ . '/includes/connect.php';
  
$odrId = checkIsset($_REQUEST["orderId"]);
$memberId = checkIsset($_REQUEST["memberId"]);
$resOrder = array();

if(!empty($odrId)){
  $selOrder = "SELECT o.id as odrId,o.is_list_bill_order,o.display_id as odrDispId,IF(c.type='Group',c.business_name,CONCAT(c.fname,' ',c.lname)) as mbrName, 
  			c.id as mbrId,c.rep_id as mbrDispId,c.cell_phone as mbrPhone,c.email as mbrEmail,c.sponsor_id,
                c.user_name as mbrUserName,o.status as odrStatus,o.sub_total as subTotal,o.grand_total as grandTotal,o.created_at as odrDate,o.post_date as odrPostDate,
                ob.address as billAdd,ob.address2 as billAdd2,ob.city as billCity,ob.state as billState,
                ob.zip as billZip,ob.payment_mode as billPayType,ob.card_type as billCardType,ob.last_cc_ach_no as lastPayNo,t.transaction_id as transactionId,DATE_FORMAT(t.created_at,'%m/%d/%Y') as transactionDate,o.payment_processor_res as processorResponse,o.order_comments as odrReason,c.type as user_type,l.lead_id as leadDispId,c.status as mbrStatus
          FROM orders o
          LEFT JOIN customer c ON (c.id = o.customer_id)
          LEFT JOIN leads l ON(l.customer_id=c.id AND l.is_deleted='N')
          LEFT JOIN customer s ON (c.sponsor_id = s.id)
          LEFT JOIN transactions t ON(t.order_id=o.id)
          LEFT JOIN order_billing_info ob ON(ob.order_id=o.id)
          WHERE md5(o.id) = :odrId ORDER BY t.id DESC";
  $resOrder = $pdo->selectOne($selOrder, array(':odrId' => makeSafe($odrId)));
}

if(!empty($memberId)){
  $selMember = "SELECT IF(c.type='Group',c.business_name,CONCAT(c.fname,' ',c.lname)) as mbrName, 
  			c.id as mbrId,c.rep_id as mbrDispId,c.cell_phone as mbrPhone,c.email as mbrEmail,c.sponsor_id,
                c.user_name as mbrUserName,
                c.type as user_type,l.lead_id as leadDispId,c.status as mbrStatus,cgs.billing_type
          FROM customer c
          LEFT JOIN leads l ON(l.customer_id=c.id AND l.is_deleted='N')
          JOIN customer s ON (c.sponsor_id = s.id AND s.type='Group')
          JOIN customer_group_settings cgs ON (cgs.customer_id = s.id and cgs.billing_type IN('list_bill','TPA'))
          WHERE md5(c.id) = :memberId";
  $resOrder = $pdo->selectOne($selMember, array(':memberId' => makeSafe($memberId)));
}

$memberId = checkIsset($resOrder["mbrId"]);

$orderId = checkIsset($resOrder["odrId"]);
$orderDispId = checkIsset($resOrder['odrDispId']);

$orderStatus = checkIsset($resOrder["odrStatus"]);
$odrPostDate = !empty($resOrder["odrPostDate"]) ? date("m/d/Y",strtotime($resOrder["odrPostDate"])) : "";

$subTotal = !empty($resOrder["subTotal"]) ? $resOrder["subTotal"] : 0;
$grandTotal = !empty($resOrder["grandTotal"]) ? $resOrder["grandTotal"] : 0;
$stepFeePrice = 0;
$serviceFeePrice = 0;

$reason = "";
if(in_array($orderStatus, array("Payment Approved"))){
  $tblClass = "table-success";
  $txtClass = "text-success";
  $iconClass = "fa-check-circle";
}else if(in_array($orderStatus, array("Pending Settlement","Post Payment"))){
  $tblClass = "table-warning";
  $txtClass = "text-warning";
  $iconClass = "fa-minus-circle";
}else if(in_array($orderStatus, array("Refund","Void","Cancelled","Chargeback","Payment Returned","Payment Declined"))){
  $tblClass = "table-danger";
  $txtClass = "text-danger";
  $iconClass = "fa-times-circle";
}else{
  $tblClass = "table-success";
  $txtClass = "text-success";
  $iconClass = "fa-check-circle";
}
$reason = checkIsset($resOrder["odrReason"]);
if($orderStatus == "Payment Declined"){
  $processorResponse = !empty($resOrder['processorResponse']) ? json_decode($resOrder['processorResponse'],true) : array();
  $reason = get_declined_reason_from_tran_response($resOrder['processorResponse'],false,$reason);
}

$billType = (checkIsset($resOrder["billPayType"]) == "CC" ? checkIsset($resOrder["billCardType"]) : "ACH");

$exJs = array('thirdparty/jquery-match-height/js/jquery.matchHeight.js');

$template = 'enrollment_successfull.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>