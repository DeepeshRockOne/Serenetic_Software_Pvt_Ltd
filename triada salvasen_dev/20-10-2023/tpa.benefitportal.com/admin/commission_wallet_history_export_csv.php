<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$agentId = checkIsset($_REQUEST["agentId"]);
$admin_id = $_SESSION['admin']['id']; 

$csv_line = "\n";
$csv_seprator = ",";
$field_seprator = '"';
   
$header1 = '';
$header2 = '';
$rowData = '';

if(!empty($agentId)){

  $selAgent = "SELECT 
              CONCAT(c.fname,' ',c.lname) as agentName,c.rep_id as agentDispId,
              w.balance
              FROM customer c
              JOIN commission_wallet w ON(c.id=w.agent_id)
              WHERE md5(c.id)=:agentId";
  $resAgent = $pdo->selectOne($selAgent,array(":agentId" => $agentId));

  $header1 .= "Admin" . $csv_seprator . $_SESSION["admin"]["fname"].' '.$_SESSION["admin"]["lname"].' ('.$_SESSION["admin"]["display_id"].')'. $csv_line .
            "Created Date" . $csv_seprator . date("m/d/Y") . $csv_line .
            "Report" . $csv_seprator . "Commission Wallet History" . $csv_line . $csv_line;

  $header2 .= "Payee" . $csv_seprator . 
              $field_seprator . $resAgent["agentName"] ." (".$resAgent["agentDispId"].")"  . $field_seprator . $csv_line .
              $field_seprator .  "Balance" . $field_seprator . $csv_seprator .
              $field_seprator . displayAmount($resAgent["balance"],2) . $field_seprator . $csv_line . $csv_line;

  $rowData .= "TRANSACTION_DATE" . $csv_seprator .
          "DESCRIPTION" . $csv_seprator .
          "PAY_PERIOD" . $csv_seprator .
          "DEPOSIT_ACCT" . $csv_seprator .
          "CREDIT" . $csv_seprator .
          "DEBIT" . $csv_seprator .
          "BALANCE" . $csv_line;

    $incr = "";
    $sch_params = array();

    if ($agentId != "") {
      $sch_params[':agentId'] = makeSafe($agentId);
      $incr.=" AND md5(cs.agent_id) = :agentId";
    }

    $selCommHistory = "SELECT cs.id,cs.created_at as transDate,cs.pay_period,cs.message,
        cs.commission_duration,cs.pay_period,
        if(cs.type='Credit',cs.amount,'-') as creditAmt,
        if(cs.type='Debit',cs.amount,'-') as debitAmt,
        cs.current_balance, cs.type,cs.deposit_detail,cs.wallet_id
         FROM commission_wallet_history cs
         WHERE cs.id > 0 $incr ORDER BY cs.created_at DESC";
    $resCommHistory = $pdo->select($selCommHistory,$sch_params);

    if(!empty($resCommHistory)){
      foreach ($resCommHistory as $history) { 
          $pay_period = $history["pay_period"];

          if($history["commission_duration"] == "weekly"){
            $startPayPeriod=date('m/d/Y', strtotime('-6 days', strtotime($pay_period)));;
            $endPayPeriod=date('m/d/Y', strtotime($pay_period));
          }else{
            $startPayPeriod=date('m/01/Y', strtotime($pay_period));
            $endPayPeriod=date('m/d/Y', strtotime($pay_period));
          }

          $history['account_number']= '';
          if(!empty($history['deposit_detail'])){
            $deposit_detail = json_decode($history['deposit_detail'],true);
            if($deposit_detail['account_number']){
                $history['account_number'] = $deposit_detail['account_number'];
            }
          }
          $accountNumber = !empty($history['account_number']) ? '*' . substr($history['account_number'], -4) : '-';
          $payPeriod = $startPayPeriod .' - '. $endPayPeriod;
          $creditAmt = $history["creditAmt"] != '-' ? displayAmount($history["creditAmt"],2) : "-";
          $debitAmt = $history["debitAmt"] != '-' ? '('.displayAmount(abs($history["debitAmt"]),2).')' : "-";

          $rowData .= date("m/d/Y",strtotime($history["transDate"])) . $csv_seprator .
              $field_seprator . $history["message"] . $field_seprator . $csv_seprator . 
              $field_seprator . $payPeriod . $field_seprator . $csv_seprator . 
              $field_seprator . $accountNumber . $field_seprator . $csv_seprator . 
              $field_seprator . $creditAmt . $field_seprator . $csv_seprator .
              $field_seprator . $debitAmt . $field_seprator . $csv_seprator .
              $field_seprator . displayAmount($history["current_balance"],2) . $field_seprator . $csv_line;
      }
    }

   

    $content = "";
    $content .= $header1;
    $content .= $header2;
    $content .= $rowData;
    $file_name='Commission_Wallet_History_' . date('Ymd_His') . '.csv';
    header('Content-Type: application/excel');
    header('Content-disposition: attachment;filename=' . $file_name);
    echo $content;
    exit;
}
?>