<?php
ini_set('memory_limit', '-1');
ini_set('max_execution_time', '-1');
include_once 'layout/start.inc.php';
include_once dirname(__DIR__) . "/includes/commission.class.php";
$commObj = new Commission();
$validate = new Validation();

$res=array();
$res['status']='Fail';

$commission_duration = checkIsset($_REQUEST['commission_duration']);
$agentIds = $_REQUEST['agentIds'];
$agentsArr = !empty($agentIds) ? explode(",", $agentIds) : array();
$pay_period = $_REQUEST['pay_period'];

$earnedApply = checkIsset($_REQUEST['earnedApply']);
$pmpmApply = !empty($_REQUEST['pmpmApply']) ? "applyToAgent" : "applyToDebit";
  if($earnedApply == "applyCustomAmt"){
    $pmpmApply = "applyCustomAmt";
  }

$debitBalance = checkIsset($_REQUEST['debitBalance'],'arr');
$earnedCredit = checkIsset($_REQUEST['earnedCredit'],'arr');
$pmpmCredit = checkIsset($_REQUEST['pmpmCredit'],'arr');

$earnedPrdCredit = checkIsset($_REQUEST['earnedPrdCredit'],'arr');
$pmpmPrdCredit = checkIsset($_REQUEST['pmpmPrdCredit'],'arr');
$earnedPrdApply = checkIsset($_REQUEST['earnedPrdApply'],'arr');
$pmpmPrdApply = checkIsset($_REQUEST['pmpmPrdApply'],'arr');

  $validate->string(array('required' => true, 'field' => 'pay_period', 'value' => $pay_period), array('required' => 'Please select Pay Period'));
  $validate->string(array('required' => true, 'field' => 'earnedApply', 'value' => $earnedApply), array('required' => 'Please apply commission type'));
  if(empty($agentsArr)){
       $validate->string(array('required' => true, 'field' => 'agentIds', 'value' => $agentIds), array('required' => 'Please select Agent'));
  }

if($validate->isValid()){

    $incr='';
    $sch_params=array();

    $pay_period = date("Y-m-d",strtotime($pay_period));

    if(!is_array($agentsArr)){
      $agentsArr = !empty($agentsArr) ? explode(",", $agentsArr) : array();
    }

    if(!empty($agentsArr)){
      foreach ($agentsArr as $agentId){

        if(!empty($pay_period)){
            $incr .= " AND cb.pay_period=:payPeriod";
            $sch_params[":payPeriod"] = $pay_period;
        }

        if(!empty($agentId)){
            $incr .= " AND cb.agent_id=:agent_id";
            $sch_params[":agent_id"] = $agentId;
        }

        if(!empty($commission_duration)){
            $incr .= " AND cb.commission_duration=:commission_duration";
            $sch_params[":commission_duration"] = $commission_duration;
        }

        $selCommissionBalance = "SELECT 
            cb.id as creditRowId,cb.credit,cb.pmpm_credit,cb.pay_period,db.balance as debit,
            a.rep_id as agentDispId,a.id as agentId,CONCAT(a.fname,' ',a.lname) as agentName
            FROM commission_credit_balance cb
            JOIN customer a ON(cb.agent_id=a.id) 
            LEFT JOIN commission_debit_balance db ON(db.agent_id=cb.agent_id)
            WHERE cb.status='Open'" . $incr . " GROUP BY cb.agent_id";
        $resCommissionBalance = $pdo->selectOne($selCommissionBalance,$sch_params);
          
        if(!empty($resCommissionBalance)){

        // Apply Credit Debit Operations Code Start
          $prdCustomArr = array();
          $creditRowId = 0;
          $debitHistoryId = 0;
          $walletHistoryId = 0;
          $paidToDebit = 0;
          $paidToAgent = 0;
          
          $pmpmToDebit = 0;
          $pmpmToAgent = 0;

          $totalDebitPayment = 0;
          $totalAgentPayment = 0;

          $earnedCreditAmt = $earnedCredit[$agentId];
          $pmpmCreditAmt = $pmpmCredit[$agentId];
          $agentDebitBalance = $resCommissionBalance["debit"];
          $updDebitBal = $resCommissionBalance["debit"];
          $creditRowId = $resCommissionBalance['creditRowId'];

          if($earnedApply == 'applyCustomAmt'){
            $selEarnedDebit = 0;
            $selEarnedCredit = 0;
            $selPmpmDebit = 0;
            $selPmpmCredit = 0;
        
            if(!empty($earnedPrdCredit)){
                foreach ($earnedPrdCredit as $prdId => $amount){
                    $prdCustomArr[$prdId]['prdId'] =  $prdId;
                    $prdCustomArr[$prdId]['earnedAmt'] =  $amount;
                    
                    if(!empty($earnedPrdApply[$prdId])){
                        $prdCustomArr[$prdId]['earnedApply'] =  'Credit';
                        $selEarnedCredit += $amount;
                    }else{
                        $prdCustomArr[$prdId]['earnedApply'] =  'Debit';
                        $selEarnedDebit += $amount;
                    }
                }
            }
            if(!empty($pmpmPrdCredit)){
                foreach ($pmpmPrdCredit as $prdId => $amount){
                    $prdCustomArr[$prdId]['prdId'] =  $prdId;
                    $prdCustomArr[$prdId]['pmpmAmt'] =  $amount;
                    
                    if(!empty($pmpmPrdApply[$prdId])){
                        $prdCustomArr[$prdId]['pmpmApply'] =  'Credit';
                        $selPmpmCredit += $amount;
                    }else{
                        $prdCustomArr[$prdId]['pmpmApply'] =  'Debit';
                        $selPmpmDebit += $amount;
                    }
                }
            }

            if(!empty($selPmpmDebit)){
              $updDebitBal = $agentDebitBalance - $selPmpmDebit;

              // if debit balance less than the credit balance then remaining credit balance will be paid to agent
              if($updDebitBal < 0){
                $pmpmToAgent += abs($updDebitBal);
                $selPmpmDebit = $agentDebitBalance;
              }
              $pmpmToDebit += $selPmpmDebit;
            }
            if(!empty($selPmpmCredit)){
              // if negative amount then it will be added to debit balance
              // if($selPmpmCredit < 0){
              //   $pmpmToDebit += $selPmpmCredit;
              // }else{
              //   $pmpmToAgent += $selPmpmCredit;
              // }
              $pmpmToAgent += $selPmpmCredit;
            }
              
            if(!empty($selEarnedDebit)){

              // if debit balance less than the credit balance then remaining credit balance will be paid to agent
              if($updDebitBal < 0){
                $paidToAgent += $selEarnedDebit;
              }else{
                $applyDebitBal = $updDebitBal - $selEarnedDebit;

                // if debit balance less than the credit balance then remaining credit balance will be paid to agent
                if($applyDebitBal < 0){
                  $paidToAgent += abs($applyDebitBal);
                  $selEarnedDebit = $updDebitBal;
                }
                $paidToDebit += $selEarnedDebit;
              }
            }
            if(!empty($selEarnedCredit)){
              // if credit balance negative then it will be added to debit balance
              // if($selEarnedCredit < 0){
              //   $paidToDebit += $selEarnedCredit;
              // }else{
              //   $paidToAgent += $selEarnedCredit;
              // }
              $paidToAgent += $selEarnedCredit;
            }

            $totalDebitPayment = $pmpmToDebit + $paidToDebit;
            $totalAgentPayment = $pmpmToAgent + $paidToAgent;
          }else{
            if($pmpmApply == 'applyToDebit'){
              $updDebitBal = $agentDebitBalance - $pmpmCreditAmt;

              // if debit balance less than the credit balance then remaining credit balance will be paid to agent
              if($updDebitBal < 0){
                $pmpmToAgent = abs($updDebitBal);
                $pmpmCreditAmt = $agentDebitBalance;
              }
              $pmpmToDebit = $pmpmCreditAmt;
            }else if($pmpmApply == 'applyToAgent'){
              // if negative amount then it will be added to debit balance
              // if($pmpmCreditAmt < 0){
              //   $pmpmToDebit = $pmpmCreditAmt;
              // }else{
              //   $pmpmToAgent = $pmpmCreditAmt;
              // }
              $pmpmToAgent = $pmpmCreditAmt;
            }
              
            if($earnedApply == 'applyToDebit'){

              // if debit balance less than the credit balance then remaining credit balance will be paid to agent
              if($updDebitBal < 0){
                $paidToAgent = $earnedCreditAmt;
              }else{
                $applyDebitBal = $updDebitBal - $earnedCreditAmt;

                // if debit balance less than the credit balance then remaining credit balance will be paid to agent
                if($applyDebitBal < 0){
                  $paidToAgent = abs($applyDebitBal);
                  $earnedCreditAmt = $updDebitBal;
                }
                $paidToDebit = $earnedCreditAmt;
              }
            }else if($earnedApply == 'applyToAgent'){
              // if credit balance negative then it will be added to debit balance
              // if($earnedCreditAmt < 0){
              //   $paidToDebit = $earnedCreditAmt;
              // }else{
              //   $paidToAgent = $earnedCreditAmt;
              // }
              $paidToAgent = $earnedCreditAmt;
            }

            $totalDebitPayment = $pmpmToDebit + $paidToDebit;
            $totalAgentPayment = $pmpmToAgent + $paidToAgent;
          }

          if(!empty($totalDebitPayment)){
            if($totalDebitPayment < 0){
              $message = "Credited For ".ucfirst($commission_duration)." Commissions Payout";
              $debitHistoryId = $commObj->commissionDebitBalance('addDebit',$commission_duration,$agentId,$pay_period,abs($totalDebitPayment),$message,0,array("transaction_type" => "Commission_Payment"));
            }else if($totalDebitPayment > 0){
              $message = "Debited For ".ucfirst($commission_duration)." Commissions Payout";
              $debitHistoryId = $commObj->commissionDebitBalance('revDebit',$commission_duration,$agentId,$pay_period,($totalDebitPayment * -1),$message,0,array("transaction_type" => "Commission_Payment"));
            }
          }

          if(!empty($totalAgentPayment)){
            $message = "Credited of ".ucfirst($commission_duration)." Commission";
            $walletHistoryId = $commObj->applyToAgentWallet('Credit',$agentId,$commission_duration,$pay_period,$totalAgentPayment,$message);
          }
        // Apply Credit Debit Operations Code Ends

        // Update Commission Code Start
          $selCommSql = "SELECT cm.pay_period,cm.customer_id,c.email,CONCAT(c.fname,' ',c.lname) as agent_name,c.type as cust_type
            FROM commission cm
            JOIN customer c ON(c.id = cm.customer_id)
            WHERE cm.commission_duration=:commission_duration AND cm.pay_period = :pay_period 
            AND cm.status IN('Pending') AND cm.credit_balance_id=0 
            AND cm.customer_id=:agentId AND cm.is_deleted = 'N'
            GROUP BY cm.customer_id";
          $wparam = array(
                          ":pay_period" => $pay_period,
                          ":commission_duration" => $commission_duration,
                          ":agentId" => $agentId);
          $resCommRow = $pdo->selectOne($selCommSql,$wparam);

          if(!empty($resCommRow)){
            $updParams = array(
              'status' => "Approved",
              'credit_balance_id' => $creditRowId,
              'updated_at' => 'msqlfunc_NOW()'
            );
            $updWhere = array(
              'clause' => "commission_duration=:commission_duration AND pay_period = :pay_period AND status = 'Pending' AND credit_balance_id=0 AND customer_id=:agentId AND is_deleted = 'N'",
              'params' => array(
                ':pay_period' => makeSafe($pay_period),
                ':agentId' => makeSafe($agentId),
                ':commission_duration' => makeSafe($commission_duration)
              )
            );
            $pdo->update("commission", $updParams, $updWhere);
          }
        // Update Commission Code Ends


        // Update Credit balance pay period Code Start
          $updParams = array(
            "status"=>"Paid",
            "action"=>$earnedApply,
            "pmpm_action"=>$pmpmApply,
            "admin_id"=>$_SESSION['admin']['id'],
            "debit_history_id"=>$debitHistoryId,
            "wallet_history_id"=>$walletHistoryId,
            "paid_to_debit"=>$paidToDebit,
            "paid_to_agent"=>$paidToAgent,
            "pmpm_to_debit"=>$pmpmToDebit,
            "pmpm_to_agent"=>$pmpmToAgent,
            "paid_date"=> "msqlfunc_NOW()",
            "extra" => !empty($prdCustomArr) ? json_encode($prdCustomArr) : ''
          );
          $updWhere = array(
            "clause" => "id=:id",
            "params" => array(":id" => $creditRowId)
          );
          $pdo->update("commission_credit_balance",$updParams,$updWhere);
        // Update Credit balance pay period Code Ends
              

        // Activity Fee Code Start
          $description['ac_message'] =array(
            'ac_red_1'=>array(
              'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
              'title'=>$_SESSION['admin']['display_id'],
            ),
            'ac_message_1' =>' approved commissions in '. getCustomDate($resCommissionBalance['pay_period']).' for ',
             'ac_red_2'=>array(
                'href'=>$ADMIN_HOST.'/agent_detail_v1.php?id='.md5($resCommissionBalance['agentId']),
                'title'=> $resCommissionBalance['agentDispId'],
              ),
          );
          activity_feed(3, $_SESSION['admin']['id'], 'Admin',$resCommissionBalance['agentId'], 'Agent',"Approved Commissions", $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
        // Activity Fee Code Ends
        }
      }
      $res['status']='Success';
    }

    if($res['status'] == 'Success'){
        $response['status'] = 'success';
        $response['message'] = "Commission applied successfully";
    }else{
        $response['status'] = 'fail';    
        $response['message'] = "Commission applied failed";
    }

}else{
    $errors = $validate->getErrors();
    $response['status'] = 'error';
    $response['errors'] = $errors;
}

header('Content-Type: application/json');
echo json_encode($response);
dbConnectionClose();
exit;
?>