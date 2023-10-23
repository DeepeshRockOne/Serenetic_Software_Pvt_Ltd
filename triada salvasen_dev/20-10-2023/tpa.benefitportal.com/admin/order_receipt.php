<?php
  include_once dirname(__FILE__) . '/layout/start.inc.php';
  
  $odrId = $_REQUEST["orderId"];
  $t_id = checkIsset($_REQUEST["t_id"]);
  $resOrder = array();

  $tz = new UserTimeZone('M d, Y h:i:s A T', $_SESSION['admin']['timezone']);

if(!empty($odrId)){
  $sch = array(':odrId' => makeSafe($odrId));
  $incr = "";
  if($t_id){
    $incr .= " AND md5(t.id) = :t_id";
    $sch[':t_id'] = $t_id;
  }
  $selOrder = "SELECT o.id as odrId,o.is_list_bill_order,o.display_id as odrDispId,IF(c.type='Group',c.business_name,CONCAT(c.fname,' ',c.lname)) as mbrName, 
  			c.id as mbrId,c.rep_id as mbrDispId,c.cell_phone as mbrPhone,c.email as mbrEmail,c.sponsor_id,
                c.user_name as mbrUserName,o.status as odrStatus,o.sub_total as subTotal,o.grand_total as grandTotal,o.created_at as odrDate,o.post_date as odrPostDate,
                ob.address as billAdd,ob.address2 as billAdd2,ob.city as billCity,ob.state as billState,
                ob.zip as billZip,ob.payment_mode as billPayType,ob.card_type as billCardType,ob.last_cc_ach_no as lastPayNo,o.transaction_id as transactionId,DATE_FORMAT(o.created_at,'%m/%d/%Y') as transactionDate,o.payment_processor_res as processorResponse,t.reason as odrReason,c.type as user_type,o.cobra_service_fee
          FROM orders o
          LEFT JOIN customer c ON (c.id = o.customer_id)
          LEFT JOIN customer s ON (c.sponsor_id = s.id)
          LEFT JOIN transactions t ON(t.id = (SELECT max(id) FROM transactions WHERE order_id = o.id))
          LEFT JOIN order_billing_info ob ON(ob.order_id=o.id)
          WHERE md5(o.id) = :odrId $incr ORDER BY t.id DESC, ob.id DESC";
  $resOrder = $pdo->selectOne($selOrder,$sch);
}

if(empty($resOrder)){
  setNotifyError("Order Receipt not found");
  redirect("all_orders.php");
}

$orderId = checkIsset($resOrder["odrId"]);
$orderDispId = checkIsset($resOrder['odrDispId']);

// Read order activity code start
  $description['ac_message'] =array(
    'ac_red_1'=>array(
      'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
      'title'=>$_SESSION['admin']['display_id'],
    ),
    'ac_message_1' =>'  viewed order ',
    'ac_red_2'=>array(
      'href'=> "order_receipt.php?orderId=".md5($orderId),
      'title'=>$orderDispId,
    ),
  ); 

  activity_feed(3, $_SESSION['admin']['id'], 'Admin',$orderId, 'orders','Viewed Order', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
// Read order activity code ends


$orderStatus = checkIsset($resOrder["odrStatus"]);
$odrPostDate = !empty($resOrder["odrPostDate"]) ? date("m/d/Y",strtotime($resOrder["odrPostDate"])) : "";
$transactionDate =  $orderStatus != "Post Payment" ? $resOrder['transactionDate'] : "";
$transactionId =  !empty($resOrder["transactionId"]) ? $resOrder["transactionId"] : "";
$subTotal = !empty($resOrder["subTotal"]) ? $resOrder["subTotal"] : 0;
$grandTotal = !empty($resOrder["grandTotal"]) ? $resOrder["grandTotal"] : 0;
$cobraServiceFee = 0;
$stepFeePrice = 0;
$stepFeeRefund = 'N';
$serviceFeePrice = 0;
$serviceFeeRefund = 'N';
$CobraServiceFeeRefund = 'N';

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

$billType = (checkIsset($resOrder["billPayType"]) == "CC" ? checkIsset($resOrder["billCardType"]) : (checkIsset($resOrder["billPayType"]) !='' ? $resOrder["billPayType"] : "ACH" ));

$exJs = array('thirdparty/jquery-match-height/js/jquery.matchHeight.js');

$template = 'order_receipt.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>