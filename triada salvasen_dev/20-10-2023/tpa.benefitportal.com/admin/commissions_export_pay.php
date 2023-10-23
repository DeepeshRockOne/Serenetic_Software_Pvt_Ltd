<?php
ini_set('memory_limit', '-1');
ini_set('max_execution_time', '-1');
include_once dirname(__FILE__) . '/layout/start.inc.php';
require_once dirname(__DIR__) . '/includes/function.class.php';
include_once dirname(__DIR__) . "/includes/commission.class.php";

$commObj = new Commission();

$functionsList = new functionsList();

$action = checkIsset($_REQUEST["action"]);
$admin_id = $_SESSION['admin']['id']; 

$REAL_IP_ADDRESS = get_real_ipaddress();
if ($action == "payCommFile") {

  $csv_line = "\n";
  $csv_seprator = ",";
  $field_seprator = '"';
  $resText = array();

  $header1 = '';
  $header2 = '';
  $rowData = '';
  $payPeriod = "";
  $commType = "";
  $totalAmt = 0;

  $admin_id = $_SESSION['admin']['id'];
  $historyIds = $_REQUEST['historyIds'];
  $fileType = $_REQUEST['fileType'];
  $agentIds = !empty($_REQUEST['agentIds']) ? $_REQUEST['agentIds'] : '';
  if (!empty($historyIds)) {

    if ($fileType == "NACHA") {
      $file_name = 'Weekly_Batch_File_' . date('Ymd_His') . '.txt';
    } else {
      $file_name = 'Weekly_Batch_File_' . date('Ymd_His') . '.csv';
    }

    if ($fileType == "CSV") {
      $header1 .= "Admin" . $csv_seprator . $_SESSION["admin"]["fname"] . ' ' . $_SESSION["admin"]["lname"] . ' (' . $_SESSION["admin"]["display_id"] . ')' . $csv_line .
        "Created Date" . $csv_seprator . date("m/d/Y") . $csv_line .
        "Report" . $csv_seprator . "NACHA Payments" . $csv_line . $csv_line;

      $rowData .= "PAYEE_ID" . $csv_seprator .
        "PAYEE_AGENCY" . $csv_seprator .
        "PAYEE_PRINCIPAL_AGENT" . $csv_seprator .
        "PAYEE_ROUTING" . $csv_seprator .
        "PAYEE_ACCOUNT" . $csv_seprator .
        "PAYEE_AMOUNT" . $csv_line;
    }

    if ($fileType == "NACHA") {
      $setting_keys = array(
        'immediate_destination',
        'immediate_destination_name',
        'immediate_origin',
        'immediate_origin_name',
        'company_entry_description',
        'originating_dfi_id',
      );
      $app_setting_res = get_app_settings($setting_keys);
      if (count($app_setting_res) !=6) {
        $response =  array(
          'status' => 'fail'
        );

        die(json_encode($response));
      }
    }

    $selPaymentRow = "SELECT 
      w.id as walletId,wh.id as historyId,wh.pay_period,wh.commission_duration,wh.amount as total_amount,
      c.id,c.rep_id,CONCAT(c.fname,' ',c.lname) as name,cs.company_name as agencyName, c.country_name,
        dda.bank_name, dda.bank_id, dda.routing_number, dda.bank_branch, dda.account_type, dda.account_number, 
        dda.account_name,w.agent_id, w.balance, wh.is_overpay_balance
      FROM commission_wallet w
      JOIN commission_wallet_history wh ON(w.id=wh.wallet_id AND w.agent_id=wh.agent_id)
      JOIN customer c on(c.id = w.agent_id)
      LEFT JOIN customer_settings cs ON(c.id=cs.customer_id)
      JOIN direct_deposit_account dda  ON(c.id = dda.customer_id AND dda.status='Active')
      WHERE wh.id IN (" . makeSafe($historyIds) . ") AND w.balance>0 AND wh.is_paid='N' AND wh.is_reversed='N'
      GROUP BY wh.id ORDER by wh.amount DESC";
    $resPaymentRow = $pdo->select($selPaymentRow);


    $selPaymentRowByAgent = "SELECT 
      SUM(wh.amount) as total_amount, c.id, 'N' as is_overpay
      FROM commission_wallet w
      JOIN commission_wallet_history wh ON(w.id=wh.wallet_id AND w.agent_id=wh.agent_id)
      JOIN customer c on(c.id = w.agent_id)
      LEFT JOIN customer_settings cs ON(c.id=cs.customer_id)
      JOIN direct_deposit_account dda  ON(c.id = dda.customer_id AND dda.status='Active')
      WHERE wh.id IN (" . makeSafe($historyIds) . ") AND w.balance>0 AND wh.is_paid='N' AND wh.is_reversed='N'
      GROUP BY c.id";
    $resPaymentRowByAgent = $pdo->select($selPaymentRowByAgent);

    $walletBalanceByAgent = array_column($resPaymentRowByAgent, null, 'id');

    $resTex = array();
    $exportArr = array();
    if (!empty($resPaymentRow)) {

      $insSql = array(
        'admin_id' => $admin_id,
        'ach_file' => $file_name,
        'file_type' => $fileType,
        'ip_address' => !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'],
      );
      $ach_id = $pdo->insert("ach_file_export", $insSql);
      foreach ($resPaymentRow as $row) {

        if ($walletBalanceByAgent[$row['id']] && $walletBalanceByAgent[$row['id']]['total_amount'] > 0) {

          $deposit_details = array(
            'bank_name' => $row['bank_name'],
            'bank_id' => $row['bank_id'],
            'routing_number' => $row['routing_number'],
            'bank_branch' => $row['bank_branch'],
            'account_type' => $row['account_type'],
            'account_number' => $row['account_number'],
            'account_name' => $row['account_name'],
          );

          $tmp_row = $row;
          unset($tmp_row["total_amount"]);

          $payPeriod = $row["pay_period"];
          $commType = $row["commission_duration"];
          $totalAmt += $row["total_amount"];
          if (!isset($exportArr[$row["id"]]["total_amount"])) {
            $exportArr[$row["id"]] = $tmp_row;
            $exportArr[$row["id"]]["total_amount"] = $row["total_amount"];
          } else {
            $exportArr[$row["id"]]["total_amount"] += $row["total_amount"];
          }

          if ($walletBalanceByAgent[$row['id']]["total_amount"] > $row['balance']) {
            $extParams = array("ach_id" => $ach_id, "deposit_detail" => json_encode($deposit_details), 'is_full' => 'Y', "is_overpay_balance" => $row['is_overpay_balance']);
          } else {
            $extParams = array("ach_id" => $ach_id, "deposit_detail" => json_encode($deposit_details), "is_overpay_balance" => $row['is_overpay_balance']);
          }

          $message = "Debited For " . ucfirst($row["commission_duration"]) . " Commissions Payout";

          $walletHistoryId = $commObj->applyToAgentWallet('Debit', $row["agent_id"], $row["commission_duration"], $row["pay_period"], ($row['total_amount'] * -1), $message, $extParams);

          $checkWalletSql = "SELECT id,balance FROM commission_wallet WHERE agent_id=:agent_id";
          $checkWalletParam = array(":agent_id" => $row["agent_id"]);
          $checkWalletRes = $pdo->selectOne($checkWalletSql, $checkWalletParam);

          if (($row['balance'] - $walletBalanceByAgent[$row['id']]['total_amount']) < 0 && $walletBalanceByAgent[$row['id']]['is_overpay'] == 'N') {
            $walletBalanceByAgent[$row['id']]['is_overpay'] = 'Y';
            $updateArr = array("is_wallet_transfer" => 'Y');
            $updateWhere = array("clause" => "agent_id =:agent_id AND type='Credit' AND is_paid='N' AND is_reversed='N' AND is_wallet_transfer='N'", "params" => array(":agent_id" => $row["agent_id"]));
            $pdo->update("commission_wallet_history", $updateArr, $updateWhere);
            $commObj->applyToAgentWallet('Credit', $row["agent_id"], $row["commission_duration"], $row["pay_period"], ($row['balance'] - $walletBalanceByAgent[$row['id']]['total_amount']), 'Overpay commission after payout', array('is_overpay_balance' => 'Y'));
          }

          $transInsId = $functionsList->transaction_insert(0, 'Debit', 'Commission Payout', 'Commission Debit For Payout', $walletHistoryId);

          $updateParam = array(
            'is_paid' => "Y",
            'ach_id' => $ach_id,
          );
          $updateWhere = array(
            'clause' => "id = :id",
            'params' => array(
              ':id' => makeSafe($row['historyId'])
            )
          );
          $pdo->update("commission_wallet_history", $updateParam, $updateWhere);
        }
      }


      //************* Activity Code Start *************
      $description['ac_message'] = array(
        'ac_red_1' => array(
          'href' => $ADMIN_HOST . '/admin_profile.php?id=' . md5($_SESSION['admin']['id']),
          'title' => $_SESSION['admin']['display_id'],
        ),
        'ac_message_1' => '  created ' . $fileType . ' file for commission payment',
      );
      activity_feed(3, $_SESSION['admin']['id'], 'Admin', 0, 'commission', "Commission Payables", $_SESSION['admin']['fname'], $_SESSION['admin']['lname'], json_encode($description));
      //************* Activity Code End *************

      if ($commType == "weekly") {
        $startPayPeriod = date('m/d/Y', strtotime('-6 days', strtotime($payPeriod)));;
        $endPayPeriod = date('m/d/Y', strtotime($payPeriod));
      } else {
        $startPayPeriod = date('m/01/Y', strtotime($payPeriod));
        $endPayPeriod = date('m/d/Y', strtotime($payPeriod));
      }

      $mail_data = array();

      $trriger_id = '';
      if ($commType == 'weekly') {
        $trriger_id = 24;
      } else {
        $trriger_id = 25;
      }
      if ($agentIds != '') {
        $agEmails = array_unique(explode(',', makeSafe($agentIds)));
        foreach ($agEmails as $id) {
          $cust_row = $pdo->selectOne("SELECT id,CONCAT(fname,' ',lname) as AgentName,email from customer where id=:id and type='Agent' AND is_deleted='N'", array(":id" => $id));
          if (!empty($cust_row['id'])) {
            $mail_data['AgentName'] = $cust_row['AgentName'];
            $mail_data['link'] = $AGENT_HOST;
            $mail_data['CommissionDate'] = $startPayPeriod . ' - ' . $endPayPeriod;
            trigger_mail($trriger_id, $mail_data, $cust_row['email']);
          }
        }
      }
      if ($fileType == "CSV") {

        $header2 .= "Pay Period" . $csv_seprator .
          $field_seprator . $startPayPeriod . " - " . $endPayPeriod . $field_seprator . $csv_line .
          $field_seprator .  "Total Payment" . $field_seprator . $csv_seprator .
          $field_seprator . displayAmount($totalAmt, 2) . $field_seprator . $csv_line . $csv_line;

        $content = "";
        $content .= $header1;
        $content .= $header2;
        if (count($exportArr) > 0) {
          foreach ($exportArr as $ekey => $erow) {
            $rowData .= $erow["rep_id"] . $csv_seprator .
              $field_seprator . $erow["agencyName"] . $field_seprator . $csv_seprator .
              $field_seprator . $erow["name"] . $field_seprator . $csv_seprator .
              $field_seprator . $erow["routing_number"] . $field_seprator . $csv_seprator .
              $field_seprator . $erow["account_number"] . $field_seprator . $csv_seprator .
              $field_seprator . displayAmount($erow["total_amount"], 2) . $field_seprator . $csv_line;
          }
        }
        $content .= $rowData;
        $file_upload = file_put_contents($ACH_COMM_DIR . '' . $file_name, $content);
       
        $response =  array(
          'status' => 'success',
          'file' => "data:application/vnd.ms-excel;base64," . base64_encode($content),
          'file_name' => $file_name
        );

        die(json_encode($response));
      } else {
        if (count($exportArr) > 0) {

          $content = generate_ach_batch_file($exportArr, 'USA', 'Weekly', date('Y-m-d'));
          $fp = fopen($ACH_COMM_DIR . $file_name, "wb");
          fwrite($fp, $content);
          fclose($fp);

          $response =  array(
            'status' => 'success',
            'file' => "data:application/octet-stream;base64," . base64_encode($content),
            'file_name' => $file_name
          );

          die(json_encode($response));
        } else {
          $response =  array(
            'status' => 'fail'
          );
          die(json_encode($response));
        }
      }
    } else {
      $response =  array(
        'status' => 'fail'
      );
      die(json_encode($response));
    }
  }
}else if($action == "reverseCommFile"){
    $res=array();
    $achId = $_REQUEST['achId'];

    $selPaidRows="SELECT
                        cw.id as walletId,
                        cs.id as historyId,
                        cs.agent_id,
                        cw.balance,
                        cs.amount as total_amount,
                        cs.reinstate_ach_id,
                        cs.pay_period,
                        cs.commission_duration,
                        cs.deposit_detail,
                        cs.is_wallet_transfer,
                        cs.is_overpay_balance,
                        cs.pay_period,
                        c.id as agentId,
                        c.rep_id as agentDispId
        FROM commission_wallet_history cs
        JOIN commission_wallet cw ON (cs.agent_id= cw.agent_id)
        JOIN customer c on(c.id = cs.agent_id) 
        WHERE cs.ach_id=:ach_id AND cs.type='Debit' GROUP BY cs.id
        ORDER BY cs.id DESC";
    $resPaidRows =$pdo->select($selPaidRows,array(":ach_id"=>$achId));
    if(!empty($resPaidRows)){
      foreach ($resPaidRows as $key => $row) {
        
        $extParams = array("reinstate_ach_id" => $achId, "deposit_detail" => $row["deposit_detail"], "is_wallet_transfer" => $row["is_wallet_transfer"], "is_overpay_balance" => $row["is_overpay_balance"]);
        $message = "Reinstate Payout For ".ucfirst($row["commission_duration"])." Commissions";

        $walletHistoryId = $commObj->applyToAgentWallet('Credit',$row["agent_id"],$row["commission_duration"],$row["pay_period"],($row['total_amount'] * -1),$message,$extParams);

        $transInsId=$functionsList->transaction_insert(0,'Credit','Commission Payout','Commission Credit',$walletHistoryId);

        $updateArr = array("is_reversed" => "Y");
        $updateWhere = array("clause" => "id=:id", "params" => array(":id" => $row["historyId"]));
        $pdo->update("commission_wallet_history", $updateArr, $updateWhere);

        $selHistory = "SELECT id FROM commission_wallet_history WHERE ach_id=:ach_id AND is_paid='Y' AND type='Credit' AND pay_period=:pay_period AND agent_id=:agent_id";
        $resHistory = $pdo->selectOne($selHistory, array(":ach_id" => $achId, ":pay_period" => $row["pay_period"], ":agent_id" => $row["agent_id"]));
        if (!empty($resHistory)) {
          $updateArr = array("wallet_history_id" => $walletHistoryId);
          $updateWhere = array("clause" => "wallet_history_id=:history_id", "params" => array(":history_id" => $resHistory["id"]));
          $pdo->update("commission_credit_balance", $updateArr, $updateWhere);
        }

        // Activity Feed Code Start
          $description['ac_message'] =array(
            'ac_red_1'=>array(
              'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
              'title'=>$_SESSION['admin']['display_id'],
            ),
            'ac_message_1' =>' reversed paid commissions '.getCustomDate($row['pay_period']).' for ',
            'ac_red_2'=>array(
                'href'=>$ADMIN_HOST.'/agent_detail_v1.php?id='.md5($row['agentId']),
                'title'=> $row['agentDispId'],
              ),
          );
           activity_feed(3, $_SESSION['admin']['id'], 'Admin',$row['agentId'], 'Agent',"Commission Payables", $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
        // Activity Feed Code Ends
      }

      $updateParams = array(
        'reversal_date' => date("Y-m-d"),
      );
      $updateWhere = array(
        'clause' => "id = :id",
        'params' => array(
          ':id' => makeSafe($achId)
        )
      );
      $pdo->update("ach_file_export", $updateParams, $updateWhere);

      $res['status']='success';
    }else{
      $res['status'] = 'fail';
    }
    echo json_encode($res);
    exit;
  }else if($action == "reversePaidComm"){
    $res=array();
    $historyId = $_REQUEST["historyId"];

    $selHistory = "SELECT * FROM commission_wallet_history WHERE id=:id AND is_paid='N' AND is_reversed='N'";
    $resHistory = $pdo->selectOne($selHistory,array(":id"=>$historyId));

    if(!empty($resHistory)){
      $pay_period = $resHistory["pay_period"];
      $commission_duration = $resHistory["commission_duration"];
      $agentId = $resHistory["agent_id"];

      $selCredit = "SELECT 
                    cb.id,cb.credit,cb.pmpm_credit,
                    cb.paid_to_debit,cb.paid_to_agent,cb.pmpm_to_debit,cb.pmpm_to_agent,
                    cb.debit_history_id,cb.wallet_history_id,cw.is_paid,cw.amount as paidToAgent
                    FROM commission_credit_balance cb 
                    LEFT JOIN commission_wallet_history cw ON(cw.id=cb.wallet_history_id)
                    WHERE cb.agent_id=:agent_id AND cb.wallet_history_id=:id";
      $resCredit = $pdo->selectOne($selCredit,array(":agent_id"=>$agentId,":id" => $resHistory["id"]));

      if(!empty($resCredit)){

        if(abs($resCredit["credit"]) > 0){
          $message = "Reversed Commission Payment For ".ucfirst($commission_duration)." Commissions";
          $commObj->agentCommissionBalance("addCredit",$commission_duration,$agentId,$pay_period,$resCredit["credit"],0,$message,array("transaction_type" => "Reversed_Commission_Payment"));
        }
        if(abs($resCredit["pmpm_credit"]) > 0){
          $message = "Reversed Commission Payment For ".ucfirst($commission_duration)." Commissions";
          $commObj->agentCommissionBalance("addPMPMCredit",$commission_duration,$agentId,$pay_period,$resCredit["pmpm_credit"],0,$message,array("transaction_type" => "Reversed_Commission_Payment"));
        }

        $totalDebitPaid = $resCredit["paid_to_debit"] + $resCredit["pmpm_to_debit"];
        if(abs($totalDebitPaid) > 0){
              $message = "Reversed Commission Payment For ".ucfirst($commission_duration)." Commissions";
              $commObj->agentCommissionBalance('addDebit',$commission_duration,$agentId,$pay_period,$totalDebitPaid,0,$message,array("transaction_type" => "Reversed_Commission_Payment"));
        }

        $paidToAgent = $resCredit["paidToAgent"];
        if(abs($paidToAgent) > 0){
            $message = "Reversed Approved Commission Payment for ".ucfirst($commission_duration)." Commissions";
            $walletHistoryId = $commObj->applyToAgentWallet('Debit',$agentId,$commission_duration,$pay_period,($paidToAgent * -1),$message);
        }

        $updateArr = array("is_reversed" => "Y");
        $updateWhere = array("clause" => "id=:id", "params" => array(":id" => $resHistory["id"]));
        $pdo->update("commission_wallet_history", $updateArr, $updateWhere);

        // Update commission code start
          $updParams = array("status" => "Pending","credit_balance_id" => 0);
          $updWhere = array(
            'clause' => "pay_period = :pay_period AND status = 'Approved'
            AND commission_duration=:duration 
            AND customer_id = :customer_id AND credit_balance_id IN(".$resCredit['id'].")",
            'params' => array(
              ':duration' => makeSafe($commission_duration),
              ':pay_period' => makeSafe($pay_period),
              ':customer_id' => makeSafe($agentId)
            )
          );
          $pdo->update("commission", $updParams, $updWhere);
        // Update commission code ends

        // Activity Feed Code Start
          $resAgent = $pdo->selectOne("SELECT id as agentId,CONCAT(fname,' ',lname) as name,rep_id as agentDispId from customer WHERE id=:id",array(":id"=>$agentId));

          $description['ac_message'] =array(
            'ac_red_1'=>array(
              'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
              'title'=>$_SESSION['admin']['display_id'],
            ),
            'ac_message_1' =>' reversed ready for payment commissions for',
            'ac_red_2'=>array(
                'href'=>$ADMIN_HOST.'/agent_detail_v1.php?id='.md5($resAgent['agentId']),
                'title'=> $resAgent['agentDispId'],
              ),
          );
          activity_feed(3, $_SESSION['admin']['id'], 'Admin',$resAgent['agentId'], 'Agent',"Commission Payables", $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
        // Activity Feed Code Ends

        $res["status"] = "success";   
      }
    }else{
      $res["status"] = "fail";
    }
    echo json_encode($res);
    exit;
  }
?>