<?php
  include_once dirname(__FILE__) . '/layout/start.inc.php';
  group_has_access(2);
  $odrId = $_REQUEST["orderId"];
  $resOrder = array();
  $tz = new UserTimeZone('M d, Y h:i:s A T', $_SESSION['groups']['timezone']);
if(!empty($odrId)){
  $selOrder = "SELECT o.id as odrId,o.display_id as odrDispId,CONCAT(c.fname,' ',c.lname) as mbrName, 
  			c.id as mbrId,c.rep_id as mbrDispId,c.cell_phone as mbrPhone,c.email as mbrEmail,c.sponsor_id,
                c.user_name as mbrUserName,o.status as odrStatus,o.sub_total as subTotal,o.grand_total as grandTotal,o.created_at as odrDate,o.post_date as odrPostDate,
                ob.address as billAdd,ob.address2 as billAdd2,ob.city as billCity,ob.state as billState,
                ob.zip as billZip,ob.payment_mode as billPayType,ob.card_type as billCardType,ob.last_cc_ach_no as lastPayNo,t.transaction_id as transactionId,DATE_FORMAT(t.created_at,'%m/%d/%Y') as transactionDate,o.payment_processor_res as processorResponse,t.reason as odrReason
          FROM orders o
          LEFT JOIN customer c ON (c.id = o.customer_id)
          LEFT JOIN customer s ON (c.sponsor_id = s.id)
          LEFT JOIN transactions t ON(t.id = (SELECT max(id) FROM transactions WHERE order_id = o.id))
          LEFT JOIN order_billing_info ob ON(ob.order_id=o.id)
          WHERE md5(o.id) = :odrId ORDER BY t.id DESC, ob.id DESC";
  $resOrder = $pdo->selectOne($selOrder, array(':odrId' => makeSafe($odrId)));
}

if(empty($resOrder)){
  setNotifyError("Order Receipt not found");
  redirect("all_orders.php");
}

$orderId = checkIsset($resOrder["odrId"]);
$orderDispId = checkIsset($resOrder['odrDispId']);

// Read order activity code start
$description['ac_message'] = array(
    'ac_red_1' => array(
        'href' => $ADMIN_HOST.'/groups_details.php?id=' . md5($_SESSION['groups']['id']),
        'title' => $_SESSION['groups']['rep_id'],
    ),
    'ac_message_1' =>'  viewed order ',
    'ac_red_2'=>array(
      'href'=> "order_receipt.php?orderId=".md5($orderId),
      'title'=>$orderDispId,
    ),
);
$desc = json_encode($description);
activity_feed(3, $_SESSION['groups']['id'], 'Group', $_SESSION['groups']['id'], 'Group', 'Agent Viewed Order.', $_SESSION['groups']['fname'], $_SESSION['groups']['lname'], $desc);
// Read order activity code ends


$orderStatus = checkIsset($resOrder["odrStatus"]);
$odrPostDate = !empty($resOrder["odrPostDate"]) ? date("m/d/Y",strtotime($resOrder["odrPostDate"])) : "";
$transactionDate =  $orderStatus != "Post Payment" ? date("m/d/Y",strtotime($resOrder['transactionDate'])) : "";
$transactionId =  !empty($resOrder["transactionId"]) ? $resOrder["transactionId"] : "";
$subTotal = !empty($resOrder["subTotal"]) ? $resOrder["subTotal"] : 0;
$grandTotal = !empty($resOrder["grandTotal"]) ? $resOrder["grandTotal"] : 0;
$stepFeePrice = 0;
$serviceFeePrice = 0;

$detSql = "SELECT pm.type,pm.product_type,od.product_name,od.start_coverage_period,od.end_coverage_period,ppt.title as planTitle,od.unit_price as price,od.is_refund
        FROM order_details od
        JOIN prd_main pm ON(pm.id=od.product_id)
        LEFT JOIN prd_plan_type ppt ON(od.prd_plan_type_id=ppt.id)
        WHERE od.order_id = :odrId AND od.is_deleted='N'
        ORDER BY od.product_name ASC";
$detRes = $pdo->select($detSql, array(':odrId' => makeSafe($orderId)));

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