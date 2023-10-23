<?php
include_once __DIR__ . '/includes/connect.php';
include_once __DIR__ . '/UserTimezone.php';

// include_once "tmpl/notify.inc.php";

$tz = new UserTimeZone('M d, Y h:i:s A T', $_SESSION['admin']['timezone']);

$transId = $_GET['transId'];
$action = $_GET['action'];
$email = $_GET['email'];
$PRICE_TAG = '$';


$resTrans = array();
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
  redirect($HOST . '/index.php');
}

$transId = checkIsset($resTrans["transId"]);
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

if($resTrans['is_list_bill_order'] == "Y") {
    $detSql = "SELECT pm.type,pm.product_type,od.product_name,od.start_coverage_period,od.end_coverage_period,ppt.title as planTitle,IF(od.is_refund = 'Y',(od.unit_price * -1),od.unit_price) as price,od.is_refund,c.rep_id
            FROM transactions t
            JOIN order_details od ON(t.order_id=od.order_id AND od.is_deleted='N')
            LEFT JOIN website_subscriptions ws ON(ws.id = od.website_id)
          LEFT JOIN customer c ON(c.id = ws.customer_id)
            JOIN prd_main pm ON(pm.id=od.product_id)
            LEFT JOIN prd_plan_type ppt ON(od.prd_plan_type_id=ppt.id)
            WHERE t.id = :transId
            GROUP BY od.list_bill_detail_id
            ORDER BY od.product_name ASC";
    $detRes = $pdo->select($detSql, array(':transId' => makeSafe($transId)));
} else {
    $detSql = "SELECT pm.type,pm.product_type,od.product_name,od.start_coverage_period,od.end_coverage_period,ppt.title as planTitle,od.is_refund,od.unit_price as price
            FROM transactions t
            JOIN sub_transactions st ON(st.transaction_id=t.id)
            JOIN order_details od ON(t.order_id=od.order_id AND od.product_id=st.product_id AND od.is_deleted='N')
            JOIN prd_main pm ON(pm.id=od.product_id)
            LEFT JOIN prd_plan_type ppt ON(od.prd_plan_type_id=ppt.id)
            WHERE t.id = :transId
            ORDER BY od.product_name ASC";
    $detRes = $pdo->select($detSql, array(':transId' => makeSafe($transId)));
}

$reason = "";
if(in_array($transStatus, array("Payment Approved"))){
  $tblClass = "background-color:#5FB89C";
  $txtClass = "color:#5FB89C";
  $iconClass = "check_icon.png";
}else if(in_array($transStatus, array("Pending Settlement","Post Payment"))){
  $tblClass = "background-color:#fec108";
  $txtClass = "color:#fec108";
  $iconClass = "minus_circle.png";
}else if(in_array($transStatus, array("Refund","Void","Cancelled","Chargeback","Payment Returned","Payment Declined"))){
  $tblClass = "background-color:#EB6E6E;";
  $txtClass = "color:#EB6E6E;";
  $iconClass = "times_circle.png";
}else{
  $tblClass = "background-color:#5FB89C";
  $txtClass = "color:#5FB89C";
  $iconClass = "check_icon.png";
}

$reason = checkIsset($resTrans["transReason"]);
if($transStatus == "Payment Declined"){
  $processorResponse = !empty($resTrans['processorResponse']) ? json_decode($resTrans['processorResponse'],true) : array();
  $reason = get_declined_reason_from_tran_response($resTrans['processorResponse'],false,$reason);
}

$billType = (checkIsset($billArr["payment_mode"]) == "CC" ? checkIsset($billArr["card_type"]) : (checkIsset($billArr["payment_mode"]) !='' ? $billArr["payment_mode"] : "ACH" ));

$pdf_html_code = '';
ob_start();
?>

<?php
if($orderId){
ob_get_clean();
    $pdf_html_code .= 
          '<!DOCTYPE html>
            <html>
            <head>
              <title></title>
              <style>
              @page {margin: 0;}
              </style>
            </head>
          <body style="font-family:Arial, Helvetica, sans-serif;">';
	  $pdf_html_code .= '<p style="font-size: 18px; margin:18px; font-family:Arial, Helvetica, sans-serif;">Receipt - <span style="font-weight: 300;">Order '
                  .$orderDispId.'</span></p>';
    $pdf_html_code .= '<table cellpadding="0" cellspacing="0" border="0" width="100%" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; border-top:1px solid #ddd;">
    <tbody>
    <tr>';
    $pdf_html_code .= '<td style="width:25%; background:#4daad4;  vertical-align:top;">';

/* Member details code start */
  $pdf_html_code .= '<div style="background-color: #0086C2;">
                    <div style="padding: 15px; color:#fff;">
                    <h4 style="margin:0px; color: rgba(255,255,255,0.39);">'.($resTrans["user_type"] == "Group"?"GROUP":"MEMBER").'</h4>';
  $pdf_html_code .= '<p style="font-size: 14px; margin:0px;"><span style="font-weight: 700; font-size: 18px;">';
  $pdf_html_code .= checkIsset($resTrans["mbrName"]).'</span><br>';
  $pdf_html_code .= checkIsset($resTrans["mbrDispId"]).' <br>';
  $pdf_html_code .= format_telephone(checkIsset($resTrans["mbrPhone"])).'<br>';
  $pdf_html_code .= checkIsset($resTrans["mbrEmail"]).'</p>';
  $pdf_html_code .= '</div></div>';
/* Member details code ends */

/* Billing details code start */
  $pdf_html_code .=  '<div style="padding: 15px; color:#fff;">
                    <h4 style="margin:0px; color: rgba(255,255,255,0.39); font-size: 14px">BILLING ADDRESS</h4>';

  $pdf_html_code .=  '<p style="font-size: 14px; margin:0px;">';
  $pdf_html_code .= checkIsset($billArr["address"]).'<br/>';
  $pdf_html_code .= !empty($billArr["address2"]) ? ($billArr["address2"].'<br/>') : '';
  $pdf_html_code .= checkIsset($billArr["city"]) .',';
  $pdf_html_code .= checkIsset($allStateShortName[$billArr["state"]]).'<br/>';
  $pdf_html_code .= checkIsset($billArr["zip"]);
  $pdf_html_code .= '</p>
                    </div>';
/* Billing details code ends */


/* PAYMENT details code start */
  $pdf_html_code .= '<div style="padding: 15px; color:#fff;">
                        <h4 style="margin:0px; color: rgba(255,255,255,0.39); font-size: 14px;">PAYMENT</h4>';
  $pdf_html_code .= '<p style="font-size: 14px; margin:0px;">';
  $pdf_html_code .= displayAmount($transTotal);
  $pdf_html_code .= '('.checkIsset($billType).' *'.checkIsset($billArr["last_cc_ach_no"]).')';
  $pdf_html_code .= '</p>
                    </div>';
/* PAYMENT details code ends */

/* TRANSACTION INFO details code start */
  $pdf_html_code .= '<div style="padding: 15px; color:#fff;">
                        <h4 style="margin:0px; color: rgba(255,255,255,0.39); font-size: 14px;">TRANSACTION INFO</h4>';
  $pdf_html_code .= '<p style="font-size: 14px; margin:0px;">';
  $pdf_html_code .= checkIsset($resTrans["processorTransId"]).'<br/>';
  $pdf_html_code .= checkIsset($transDate);
  $pdf_html_code .= '</p>
                    </div>';
/* TRANSACTION INFO details code ends */

/* ORDER ID code start */
  $pdf_html_code .= '<div style="padding: 15px; color:#fff;">
                        <h4 style="margin:0px; color: rgba(255,255,255,0.39); font-size: 14px;">ORDER ID</h4>';
  $pdf_html_code .= '<p style="font-size: 14px; margin:0px 0px 30px 0px;">';
  $pdf_html_code .= checkIsset($orderDispId);
  $pdf_html_code .= '</p>
                    </div>';
/* ORDER ID code ends */
$pdf_html_code .= '</td>';

$pdf_html_code .= '<td style="width:75%; vertical-align:top; padding:15px;">';
$pdf_html_code .= '<table cellpadding="0" cellspacing="0" border="0" width="100%" style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
<tbody>
<tr>';
$pdf_html_code .= '<td style="vertical-align:top; text-align:left;">';
$pdf_html_code .= '<h4 style="margin: 0px; '.$txtClass.'">';
$pdf_html_code .= '<span style="'.$txtClass.'">'.$transStatus.'</span><img src="images/icons/'.$iconClass.'" height="15px" style="margin-left:5px;">';
$pdf_html_code .= '</h4>';
$pdf_html_code .= '<p style="font-size: 10px; color: #999; margin:0px;">';
$pdf_html_code .=  $tz->getDate($resTrans["transactionDate"]);
$pdf_html_code .= '</p>';
$pdf_html_code .= '</td>';
$pdf_html_code .= '<td style="vertical-align:top; text-align:right;">';
$pdf_html_code .= '<div style="display:inline-block; text-align:right;">
                      RECEIPT
                  </div>';
$pdf_html_code .= '</td>';
$pdf_html_code .= '</tr>
			  </tbody>
			</table>';
if(in_array($transStatus, array("Refund","Void","Cancelled","Chargeback","Payment Returned","Payment Declined"))){
  $pdf_html_code .= '<p style="margin-bottom:20px; margin-top:20px;"><strong style="'.$txtClass.'">Reason : </strong>'.$reason.'</p>';
}
if(in_array($transStatus, array("Post Payment")) && !empty($odrPostDate)){
  $pdf_html_code .= '<p style="margin-bottom:20px;"><strong>Date :</strong>'.$odrPostDate.'</p>';
}
$pdf_html_code .= '<p style="margin-top: 20px; font-weight: 500;">Summary</p>';
$pdf_html_code .= '<table width="100%" cellpadding="0" cellspacing="0">';
$pdf_html_code .= '<thead>
                    <tr >';
                      if($resTrans['is_list_bill_order'] == "Y") {
                        $pdf_html_code .= '<th style="font-weight: bold; color:#fff; text-align: left; padding:8px;'.$tblClass.'">Member ID</th>';
                      }
$pdf_html_code .= ' <th style="font-weight: bold; color:#fff; text-align: left; padding:8px;'.$tblClass.'">Product</th>
                      <th style="font-weight: bold; color:#fff; text-align: left; padding:8px;'.$tblClass.'">Coverage Period</th>
                      <th style="font-weight: bold; color:#fff; text-align: left; padding:8px;'.$tblClass.'">Coverage</th>
                      <th width="50px" style="font-weight: bold; text-align: right; color:#fff; padding:8px;'.$tblClass.'">Total</th>
                    </tr>
                  </thead>
                  <tbody>';
if(!empty($detRes)){
  $fee_prd_res = array();
  foreach ($detRes as $key => $order) {
    if($order["type"] == 'Fees'){
      if($order["product_type"] == "Healthy Step"){
        $stepFeePrice = $order["price"];
        continue;
      }
      if($order["product_type"] == "ServiceFee" || $order["product_type"] == "ListBillServiceFee"){
        $serviceFeePrice = $order["price"];
        continue;
      }
      $fee_prd_res[] = $order;
      continue;
    }else{
      $subTotal += $order["price"];
    }

    if($transStatus != 'Payment Approved'){
      $pdf_html_code .= '<tr>';
    }elseif($transStatus == 'Payment Approved'){
      if($order['is_refund'] == 'Y'){
        $pdf_html_code .= '<tr style="color:#EB6E6E;">';
      }else{
        $pdf_html_code .= '<tr>';
      }
    }
    if($resTrans['is_list_bill_order'] == "Y") {
      $pdf_html_code .= '<td style="padding:8px; text-align: left; font-wight:normal;">'.$order["rep_id"].'</td>';
    }
    $pdf_html_code .= '<td style="padding:8px; text-align: left; font-wight:normal;">'.$order["product_name"].'</td>
        <td style="padding:8px; text-align: left; font-wight:normal;">'.date("m/d/Y",strtotime($order["start_coverage_period"])).' - '.date("m/d/Y",strtotime($order["end_coverage_period"])).'</td>
        <td style="padding:8px; text-align: left; font-wight:normal;">'.$order["planTitle"].'</td>
        <td style="padding:8px; text-align: right; font-wight:normal;">'.displayAmount($order["price"]).'</td>
      </tr>';
  }
  foreach ($fee_prd_res as $key => $fee_prd_row) {
      if($transStatus != 'Payment Approved'){
        $pdf_html_code .= '<tr>';
      }elseif($transStatus == 'Payment Approved'){
        if($fee_prd_row['is_refund'] == 'Y'){
          $pdf_html_code .= '<tr style="color:#EB6E6E;">';
        }else{
          $pdf_html_code .= '<tr>';
        }
      }
      if($resTrans['is_list_bill_order'] == "Y") {
        $pdf_html_code .= '<td style="padding:8px; text-align: left; font-wight:normal;">'.$fee_prd_row["rep_id"].'</td>';
      }
    $pdf_html_code .= '<td style="padding:8px; text-align: left; font-wight:normal;">'.$fee_prd_row["product_name"].'</td>
        <td style="padding:8px; text-align: left; font-wight:normal;"></td>
        <td style="padding:8px; text-align: left; font-wight:normal;">Fees</td>
        <td style="padding:8px; text-align: right; font-wight:normal;">'.displayAmount($fee_prd_row["price"]).'</td>
      </tr>';
  }
}
$pdf_html_code .= '</tbody>
                </table>';
$pdf_html_code .= '<table cellpadding="0" cellspacing="0" border="0" width="100%" style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
<tbody>
<tr>';
$pdf_html_code .= '<td>';
$pdf_html_code .= '</td>';
$pdf_html_code .= '<td >';
$pdf_html_code .= '<table style="padding-top: 20px; width:250px;" align="right">
                  <tbody>';
$pdf_html_code .= '<tr>
                    <td  style="padding:6px; font-wight:normal;">SubTotal(s)  </td>
                    <td style="text-align: right;">'.displayAmount($subTotal).'</td>
                  </tr>';
$pdf_html_code .= '<tr>
                    <td style="padding:6px; font-wight:normal;">Healthy Step(s)   </td>
                    <td style="text-align: right;">'.displayAmount($stepFeePrice).'</td>
                  </tr>';

$pdf_html_code .= '<tr>
                    <td style="padding:6px; font-wight:normal;">Service Fee(s)  </td>
                    <td style="text-align: right;">'.displayAmount($serviceFeePrice).'</td>
                  </tr>';

$pdf_html_code .= '<tr>
                    <td style="padding:6px; font-wight:normal;"><strong>Total</strong></td>
                    <td style="text-align: right;"><strong>'.displayAmount($transTotal).'</strong></td>
                  </tr>';
$pdf_html_code .= '</tbody>
                  </table>';
$pdf_html_code .= '</td>';
      $pdf_html_code .= '</tr>
                      </tbody>
                    </table>';

$pdf_html_code .= '</td>';
      $pdf_html_code .= '</tr>
                      </tbody>
                    </table>';
$pdf_html_code .= '</body></html>';


if($action == "emailReceipt" && !empty($email)){
  $params = array();
  $emailSubject= "Transaction Order Receipt";
  $emailContent = $pdf_html_code;
  $mailStatus = trigger_mail_to_mail($params,$email,3,$emailSubject,$emailContent);
  
  if($mailStatus == 'success'){
  // Sent Order Receipt activity code start
    $description['ac_message'] =array(
      'ac_red_1'=>array(
        'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
        'title'=>$_SESSION['admin']['display_id'],
      ),
      'ac_message_1' =>' sent transaction receipt for '.$orderDispId.' to',
      'ac_red_2'=>array(
        'href'=> "transaction_receipt.php?transId=".md5($transId),
        'title'=>$email,
      ),
    ); 
    activity_feed(3, $_SESSION['admin']['id'], 'Admin',$transId, 'transactions','Viewed Transactions', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
  // Sent Order Receipt activity code ends

    setNotifySuccess('Email Sent Successfully!');
    redirect($HOST."/admin/transaction_receipt.php?transId=".md5($transId));
  }else{
    setNotifyError("Something went wrong!");
  }
}else if($action == "downloadReceipt"){
  // Downloaded Order Receipt activity code start
    $description['ac_message'] =array(
      'ac_red_1'=>array(
        'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
        'title'=>$_SESSION['admin']['display_id'],
      ),
      'ac_message_1' =>'  downloaded transaction receipt for ',
      'ac_red_2'=>array(
        'href'=> "transaction_receipt.php?transId=".md5($transId),
        'title'=>$orderDispId,
      ),
    ); 
    activity_feed(3, $_SESSION['admin']['id'], 'Admin',$transId, 'transactions','Viewed Transactions', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
  // Downloaded Order Receipt activity code ends
 
   downloadPdf($pdf_html_code);
}
            
} else {
    redirect($HOST . '/index.php');
}
function downloadPdf($pdf_html_code) {
    define("DOMPDF_ENABLE_HTML5PARSER", true);
    require_once "libs/dompdf/dompdf_config.inc.php";
    $dompdf = new DOMPDF();
    $dompdf->load_html($pdf_html_code);
    $dompdf->render();
    $content = $dompdf->output();
    $dompdf->stream("Transaction_Receipt_" . date('YmdHis') . ".pdf");
    exit;
}

?>