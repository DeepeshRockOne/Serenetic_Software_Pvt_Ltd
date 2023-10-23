<?php  
ini_set('memory_limit', '-1');
ini_set('max_execution_time', '-1');
  include_once dirname(__FILE__) . '/layout/start.inc.php';
  include_once dirname(__DIR__) . "/includes/commission.class.php";

  $commObj = new Commission();

  $res = array();
  $res['status'] = 'fail';

  $action = checkIsset($_REQUEST["action"]);
  $pay_period = checkIsset($_REQUEST["pay_period"]);
  $commission_duration = checkIsset($_REQUEST["commission_duration"]);
  $agentIds = checkIsset($_REQUEST["agentIds"],'arr');
  $status = checkIsset($_REQUEST["status"]);

  if(!empty($pay_period) && !empty($action) && !empty($agentIds)){

    if($action == "denyToPay"){
      if(!empty($agentIds)){
        $agents = is_array($agentIds) ? implode(",", $agentIds) : $agentIds;

        $selComm =  "SELECT 
                  cs.customer_id as agentId,
                  cs.pay_period,
                  a.rep_id as agentDispId,
                  SUM(IF(cs.is_pmpm_comm='N', cs.amount, 0)) AS earnedCredit,
                  SUM(IF(cs.is_pmpm_comm='Y', cs.amount, 0)) AS pmpmCredit,
                  SUM(IF(cs.is_advance='Y', cs.amount, 0)) AS totalDebit
                  FROM commission cs
                  LEFT JOIN customer a ON(cs.customer_id=a.id)
                  WHERE cs.pay_period=:pay_period AND cs.status = 'Pending' AND cs.is_deleted='N'
                  AND cs.commission_duration=:commission_duration
                  AND cs.customer_id IN (" . makeSafe($agents) . ") GROUP BY cs.customer_id";
        $commParams = array(":pay_period" => $pay_period,":commission_duration" => $commission_duration);
        $resComm = $pdo->select($selComm, $commParams);

        if(!empty($resComm)){
          foreach ($resComm as $key => $row) {

            // Update commission code start
              $updParams = array("status" => "Cancelled");
              $updWhere = array(
                'clause' => "pay_period = :pay_period AND status = 'Pending'
                AND commission_duration=:commission_duration 
                AND customer_id = :customer_id AND is_deleted = 'N'",
                'params' => array(
                  ':pay_period' => makeSafe($pay_period),
                  ':customer_id' => $row["agentId"],
                  ':commission_duration' => $commission_duration
                )
              );
              $pdo->update("commission", $updParams, $updWhere);
            // Update commission code ends

            // Adjust Credit/Debit Balance Code Start
              $earnedCredit = $row["earnedCredit"];
              $pmpmCredit = $row["pmpmCredit"];
              $totalDebit = $row["totalDebit"];

              if(abs($earnedCredit) > 0){
                if($earnedCredit < 0){
                  $message = "Credited of ".ucfirst($commission_duration)." Commission on Deny Commissions";
                  $commObj->agentCommissionBalance("addCredit",$commission_duration, $row["agentId"],$pay_period,abs($earnedCredit),0,$message,array("transaction_type" => "Deny_Commission"));
                }else{
                  $message = "Debited of ".ucfirst($commission_duration)." Commission on Deny Commissions";
                  $commObj->agentCommissionBalance("revCredit",$commission_duration, $row["agentId"],$pay_period,($earnedCredit * -1),0,$message,array("transaction_type" => "Deny_Commission"));
                }
              }
              if(abs($pmpmCredit) > 0){
                if($pmpmCredit < 0){
                  $message = "Credited of ".ucfirst($commission_duration)." Commission on Deny Commissions";
                  $commObj->agentCommissionBalance("addPMPMCredit",$commission_duration, $row["agentId"],$pay_period,abs($pmpmCredit),0,$message,array("transaction_type" => "Deny_Commission"));
                }else{
                  $message = "Debited of ".ucfirst($commission_duration)." Commission on Deny Commissions";
                  $commObj->agentCommissionBalance("revPMPMCredit",$commission_duration, $row["agentId"],$pay_period,($pmpmCredit * -1),0,$message,array("transaction_type" => "Deny_Commission"));
                }
              }
              if(abs($totalDebit) > 0){
                if($totalDebit < 0){
                  $message = "Credited of ".ucfirst($commission_duration)." Commission on Deny Commissions";
                  $commObj->agentCommissionBalance("addDebit",$commission_duration, $row["agentId"],$pay_period,abs($totalDebit),0,$message,array("transaction_type" => "Deny_Commission"));
                }else{
                  $message = "Debited of ".ucfirst($commission_duration)." Commission on Deny Commissions";
                  $commObj->agentCommissionBalance("revDebit",$commission_duration, $row["agentId"],$pay_period,($totalDebit * -1),0,$message,array("transaction_type" => "Deny_Commission"));
                }
              }
            // Adjust Credit/Debit Balance Code Ends

            // Activity Feed Code Start
              $description['ac_message'] = array(
                'ac_red_1'=>array(
                  'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
                  'title'=>$_SESSION['admin']['display_id'],
                ),
                'ac_message_1' =>' denied commissions in '.getCustomDate($row['pay_period']).' for ',
                'ac_red_2'=>array(
                    'href'=>$ADMIN_HOST.'/agent_detail_v1.php?id='.md5($row['agentId']),
                    'title'=> $row['agentDispId'],
                ),
              );
              activity_feed(3, $_SESSION['admin']['id'], 'Admin',$row['agentId'], 'Agent',"Denied Commission", $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
            // Activity Feed Code Ends
          }
          $res['status']='success';
        }
      }
    }else if($action == "revCompletedComm"){
      if($status == "Cancelled"){
        if(!empty($agentIds)){
          $agents = is_array($agentIds) ? implode(",", $agentIds) : $agentIds;
          // selecting Deleted commissions
            $selComm =  "SELECT 
                    cs.customer_id as agentId,
                    cs.pay_period,
                    a.rep_id as agentDispId,
                    SUM(IF(cs.is_pmpm_comm='N', cs.amount, 0)) AS earnedCredit,
                    SUM(IF(cs.is_pmpm_comm='Y', cs.amount, 0)) AS pmpmCredit,
                    SUM(IF(cs.is_advance='Y', cs.amount, 0)) AS totalDebit
                    FROM commission cs 
                    LEFT JOIN customer a ON(cs.customer_id=a.id)
                    WHERE cs.pay_period=:pay_period AND cs.status = 'Cancelled' 
                    AND cs.commission_duration=:commission_duration AND cs.is_deleted='N'
                    AND cs.customer_id IN (" . makeSafe($agents) . ") GROUP BY cs.customer_id";
            $commParams = array(":pay_period" => $pay_period,":commission_duration"=>$commission_duration);
            $resComm = $pdo->select($selComm, $commParams);
            
            if(!empty($resComm)){
              foreach ($resComm as $key => $row) {
                // Update commission code start
                  $updParams = array("status" => "Pending");
                  $updWhere = array(
                    'clause' => "pay_period = :pay_period AND status = 'Cancelled'
                    AND commission_duration=:commission_duration 
                    AND customer_id = :customer_id AND is_deleted='N'",
                    'params' => array(
                      ':pay_period' => makeSafe($pay_period),
                      ':customer_id' => makeSafe($row["agentId"]),
                      'commission_duration' => $commission_duration
                    )
                  );
                  $pdo->update("commission", $updParams, $updWhere);
                // Update commission code ends

                // Adjust Credit/Debit Balance Code Start
                  $earnedCredit = $row["earnedCredit"];
                  $pmpmCredit = $row["pmpmCredit"];
                  $totalDebit = $row["totalDebit"];

                  if(abs($earnedCredit) > 0){
                      if($earnedCredit > 0){
                        $message = "Credited of ".ucfirst($commission_duration)." Commission on Reversed Deny Commissions";
                      }else{
                        $message = "Debited of ".ucfirst($commission_duration)." Commission on Reversed Deny Commissions";
                      }
                      $commObj->agentCommissionBalance("addCredit",$commission_duration,$row["agentId"],$pay_period,$earnedCredit,0,$message,array("transaction_type" => "Reversed_Deny_Commission"));
                  }
                  if(abs($pmpmCredit) > 0){
                      if($pmpmCredit > 0){
                        $message = "Credited of ".ucfirst($commission_duration)." Commission on Reversed Deny Commissions";
                      }else{
                        $message = "Debited of ".ucfirst($commission_duration)." Commission on Reversed Deny Commissions";
                      }
                      $commObj->agentCommissionBalance("addPMPMCredit",$commission_duration,$row["agentId"],$pay_period,$pmpmCredit,0,$message,array("transaction_type" => "Reversed_Deny_Commission"));
                  }

                  if(abs($totalDebit) > 0){
                    if($totalDebit > 0){
                      $message = "Credited of ".ucfirst($commission_duration)." Commission on Reversed Deny Commissions";
                      $commObj->agentCommissionBalance("addDebit",$commission_duration,$row["agentId"],$pay_period,$totalDebit,0,$message,array("transaction_type" => "Reversed_Deny_Commission"));
                    }else{
                      $message = "Debited of ".ucfirst($commission_duration)." Commission on Reversed Deny Commissions";
                      $commObj->agentCommissionBalance("revDebit",$commission_duration,$row["agentId"],$pay_period,($totalDebit),0,$message,array("transaction_type" => "Reversed_Deny_Commission"));
                    }
                  }
                // Adjust Credit/Debit Balance Code Ends

                // Activity Feed Code Start
                  $description['ac_message'] =array(
                    'ac_red_1'=>array(
                      'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
                      'title'=>$_SESSION['admin']['display_id'],
                    ),
                    'ac_message_1' =>' reversed completed commissions in '.getCustomDate($row['pay_period']).' for ',
                     'ac_red_2'=>array(
                        'href'=>$ADMIN_HOST.'/agent_detail_v1.php?id='.md5($row['agentId']),
                        'title'=> $row['agentDispId'],
                      ),
                  );
                  activity_feed(3, $_SESSION['admin']['id'], 'Admin',$row['agentId'], 'Agent',"Reversed Completed Commissions", $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
                // Activity Feed Code Ends
              }
              $res['status']='success';
            }
        }
      }else if($status == "Approved"){
        if(!empty($agentIds)){
          $agents = is_array($agentIds) ? implode(",", $agentIds) : $agentIds;
           
          // selecting Approve commissions code start
          $selComm = "SELECT 
                  cs.customer_id AS agentId,
                  cs.pay_period,
                  a.rep_id as agentDispId,
                  GROUP_CONCAT(DISTINCT(cs.credit_balance_id)) AS creditRowIds
                  FROM commission cs 
                  LEFT JOIN customer a ON(cs.customer_id=a.id)
                  WHERE cs.pay_period=:pay_period AND cs.status = 'Approved' 
                  AND cs.commission_duration=:commission_duration AND cs.is_deleted='N'
                  AND cs.customer_id IN (" . makeSafe($agents) . ") GROUP BY cs.customer_id";
          $commParams = array(":pay_period" => $pay_period,":commission_duration" => $commission_duration);
          $resComm = $pdo->select($selComm, $commParams);
           
          if(!empty($resComm)){
            foreach ($resComm as $key => $row) {

              $selCredit = "SELECT 
                            cb.id,cb.credit,cb.pmpm_credit,
                            cb.paid_to_debit,cb.paid_to_agent,cb.pmpm_to_debit,cb.pmpm_to_agent,
                            cb.debit_history_id,cb.wallet_history_id,cw.is_paid,cw.amount as paidToAgent

                            FROM commission_credit_balance cb 
                            LEFT JOIN commission_wallet_history cw ON(cw.id=cb.wallet_history_id)
                            WHERE cb.id IN(".$row['creditRowIds'].")";
              $resCredit = $pdo->select($selCredit);
              if(!empty($resCredit)){
                foreach ($resCredit as $payRow) {

                  if(abs($payRow["credit"]) > 0){
                    $message = "Reversed Commission Payment For ".ucfirst($commission_duration)." Commissions";
                    $commObj->agentCommissionBalance("addCredit",$commission_duration,$row["agentId"],$pay_period,$payRow["credit"],0,$message,array("transaction_type" => "Reversed_Commission_Payment"));
                  }
                  if(abs($payRow["pmpm_credit"]) > 0){
                    $message = "Reversed Commission Payment For ".ucfirst($commission_duration)." Commissions";
                    $commObj->agentCommissionBalance("addPMPMCredit",$commission_duration,$row["agentId"],$pay_period,$payRow["pmpm_credit"],0,$message,array("transaction_type" => "Reversed_Commission_Payment"));
                  }
                  
                  $totalDebitPaid = $payRow["paid_to_debit"] + $payRow["pmpm_to_debit"];
                  if(abs($totalDebitPaid) > 0){
                        $message = "Reversed Commission Payment For ".ucfirst($commission_duration)." Commissions";
                        $commObj->agentCommissionBalance('addDebit',$commission_duration,$row["agentId"],$pay_period,$totalDebitPaid,0,$message,array("transaction_type" => "Reversed_Commission_Payment"));
                  }

                  $paidToAgent = $payRow["paidToAgent"];
                  if(abs($paidToAgent) > 0){
                    if(checkIsset($payRow["is_paid"]) == "Y"){
                      // $message = "Reversed Commission Payment For ".ucfirst($commission_duration)." Commissions";
                      // $commObj->agentCommissionBalance('addDebit',$commission_duration,$row["agentId"],$pay_period,$paidToAgent,0,$message,array("transaction_type" => "Credit_Reversed_Commission_Payment"));
                    }else{
                        $message = "Reversed Commission Payment For ".ucfirst($commission_duration)." Commissions";
                        $walletHistoryId = $commObj->applyToAgentWallet('Debit',$row["agentId"],$commission_duration,$pay_period,($paidToAgent * -1),$message);
                    }
                  }

                  $updateArr = array("is_reversed" => "Y");
                  $updateWhere = array("clause" => "id=:id", "params" => array(":id" => $payRow["wallet_history_id"]));
                  $pdo->update("commission_wallet_history", $updateArr, $updateWhere);
                }
              }
                
            // Update commission code start
              $updParams = array("status" => "Pending","credit_balance_id" => 0);
              $updWhere = array(
                'clause' => "pay_period = :pay_period AND status = 'Approved'
                AND commission_duration=:commission_duration AND is_deleted='N'
                AND customer_id = :customer_id AND credit_balance_id IN(".$row['creditRowIds'].")",
                'params' => array(
                  ':pay_period' => makeSafe($pay_period),
                  ':customer_id' => makeSafe($row["agentId"]),
                  ':commission_duration' => $commission_duration
                )
              );
              $pdo->update("commission", $updParams, $updWhere);
            // Update commission code ends

            // Activity Feed Code Start
              $description['ac_message'] =array(
                'ac_red_1'=>array(
                  'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
                  'title'=>$_SESSION['admin']['display_id'],
                ),
                'ac_message_1' =>' reversed completed commissions in '.getCustomDate($row['pay_period']).' for ',
                 'ac_red_2'=>array(
                    'href'=>$ADMIN_HOST.'/agent_detail_v1.php?id='.md5($row['agentId']),
                    'title'=> $row['agentDispId'],
                  ),
              );
              activity_feed(3, $_SESSION['admin']['id'], 'Admin',$row['agentId'], 'Agent',"Reversed Completed Commissions", $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
            // Activity Feed Code Ends
              
            }
            $res['status']='success';
          }
        }
      }
    }
    $res['status'] = 'success';
  }else{
    $res['status'] = 'fail';
  }

header('Content-Type: application/json');
echo json_encode($res);
dbConnectionClose();
exit;
?>