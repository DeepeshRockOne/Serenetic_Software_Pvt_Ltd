<?php
  include_once dirname(__FILE__) . '/layout/start.inc.php';
  
  // $odrId = $_REQUEST["orderId"];
  $t_id = checkIsset($_REQUEST["t_id"]);
  $resOrder = array();

  $tz = new UserTimeZone('M d, Y h:i:s A T', $_SESSION['admin']['timezone']);

if(!empty($t_id)){
  $selOrder = "SELECT t.id as transId,o.id as odrId,o.display_id as odrDispId,CONCAT(c.fname,' ',c.lname) as mbrName, 
        c.id as mbrId,c.rep_id as mbrDispId,c.cell_phone as mbrPhone,c.email as mbrEmail,c.sponsor_id,
                c.user_name as mbrUserName,t.transaction_type as odrStatus,t.debit as subTotal,t.debit as grandTotal,t.created_at as odrDate,o.post_date as odrPostDate,
                ob.address as billAdd,ob.address2 as billAdd2,ob.city as billCity,ob.state as billState,
                ob.zip as billZip,ob.payment_mode as billPayType,ob.card_type as billCardType,ob.last_cc_ach_no as lastPayNo,o.transaction_id as transactionId,DATE_FORMAT(o.created_at,'%m/%d/%Y') as transactionDate,o.payment_processor_res as processorResponse,o.order_comments as odrReason,t.reason
          FROM transactions t 
          JOIN orders o on(t.order_id = o.id)
          LEFT JOIN customer c ON (c.id = o.customer_id)
          LEFT JOIN customer s ON (c.sponsor_id = s.id)
          LEFT JOIN order_billing_info ob ON(ob.order_id=o.id)
          WHERE md5(t.id) = :t_id ORDER BY t.id DESC";
  $resOrder = $pdo->selectOne($selOrder,array(":t_id" => $t_id));
}

if(empty($resOrder)){
  setNotifyError("Order Receipt not found");
  redirect("all_orders.php");
}

$transId = checkIsset($resOrder["transId"]);
$orderId = checkIsset($resOrder["odrId"]);
$orderDispId = checkIsset($resOrder['odrDispId']);

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


$orderStatus = checkIsset($resOrder["odrStatus"]);
$odrPostDate = !empty($resOrder["odrPostDate"]) ? date("m/d/Y",strtotime($resOrder["odrPostDate"])) : "";
$transactionDate =  $orderStatus != "Post Payment" ? $resOrder['transactionDate'] : "";
$transactionId =  $resOrder["transactionId"] > 0 ? $resOrder["transactionId"] : "";
$subTotal = !empty($resOrder["subTotal"]) ? $resOrder["subTotal"] : 0;
$grandTotal = !empty($resOrder["grandTotal"]) ? $resOrder["grandTotal"] : 0;
$stepFeePrice = 0;
$stepFeeRefund = 'N';
$serviceFeePrice = 0;
$serviceFeeRefund = 'N';

$detSql = "SELECT pm.type,pm.product_type,od.product_name,od.start_coverage_period,od.end_coverage_period,ppt.title as planTitle,od.unit_price as price,od.is_refund
        FROM transactions t
        JOIN sub_transactions st on(st.transaction_id = t.id AND st.order_id = t.order_id)
        JOIN order_details od on(st.order_detail_id = od.id AND (od.is_refund = 'Y' OR od.is_chargeback = 'Y' or od.is_payment_return = 'Y') AND od.is_deleted='N')
        JOIN prd_main pm ON(pm.id=od.product_id)
        LEFT JOIN prd_plan_type ppt ON(od.prd_plan_type_id=ppt.id)
        WHERE md5(t.id) = :t_id
        ORDER BY od.product_name ASC";
$detRes = $pdo->select($detSql, array(':t_id' => makeSafe($t_id)));

$reason = "";
if(in_array($orderStatus, array("Payment Approved"))){
  $tblClass = "table-success";
  $txtClass = "text-success";
  $iconClass = "fa-check-circle";
}else if(in_array($orderStatus, array("Pending Settlement","Post Payment"))){
  $tblClass = "table-warning";
  $txtClass = "text-warning";
  $iconClass = "fa-minus-circle";
}else if(in_array($orderStatus, array('Refund Order','Void Order','Chargeback','Payment Returned'))){
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

$template = 'return_transaction_receipt.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>