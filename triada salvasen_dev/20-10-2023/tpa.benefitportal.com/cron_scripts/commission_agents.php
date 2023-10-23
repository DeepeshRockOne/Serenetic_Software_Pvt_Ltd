<?php
ini_set('memory_limit', '-1');
ini_set('max_execution_time', '-1');
include_once dirname(__DIR__) . "/includes/connect.php";
include_once dirname(__DIR__) . "/includes/commission.class.php";
include_once dirname(__DIR__) . '/includes/function.class.php';
 
  $commObj = new Commission();
  $functionsList = new functionsList();

  /*---------- System script status code start -----------*/
    $cronSql = "SELECT is_running,next_processed,last_processed FROM system_scripts WHERE script_code=:script_code";
    $cronWhere = array(":script_code" => "commission");
    $cronRow = $pdo->selectOne($cronSql,$cronWhere);

      if(!empty($cronRow['is_running']) && $cronRow['is_running'] == "Y") {
         echo "Script already running...";
         exit();
      }else{
        /*---- update running status -----*/
        $cronWhere = array(
                          "clause" => "script_code=:script_code", 
                          "params" => array(
                              ":script_code" => 'commission'
                          )
                      );
        $pdo->update('system_scripts',array("is_running" => "Y","last_processed"=>"msqlfunc_NOW()"),$cronWhere);
      }
  /*---------- System script status code ends -----------*/

  $today = date('Y-m-d');
  $agentRes = array();
  $CODED_ARR = array();

  $weeklyPayPeriod = $commObj->getWeeklyPayPeriod($today);
  $monthlyPayPeriod = $commObj->getMonthlyPayPeriod($today);
  
  $stepFeePrdIds = $functionsList->getHealthyStepFeePrdIds();

  $commOdr = array();
  $commGenCount = 0;

  /*---------- Fetch Todays Approved Orders Code START -----------*/
    $odrSql = "SELECT o.id,ws.customer_id,IF(o.is_renewal='L',od.is_renewal,o.is_renewal) as is_renewal,
      od.product_id,od.plan_id,od.website_id,od.prd_plan_type_id,od.qty,od.unit_price,od.renew_count as covPeriod,
      c.sponsor_id,c.upline_sponsors,
      s.type as sponsor_type,CONCAT_WS(' ',s.fname,s.lname) as sponsor_name,s.business_name as sponsor_business_name,
      p.name,p.product_code,p.parent_product_id,
      IF(p.parent_product_id > 0,p.parent_product_id,p.id) as globalProductId,
      od.prd_plan_type_id as planType,
      SUM(px.price) as planPrice,
      SUM(px.commission_amount) as planCommAmt,
      SUM(px.non_commission_amount) as planNonCommAmt,
      od.id as order_detail_id

      FROM orders o
      JOIN order_details od ON (od.order_id=o.id AND od.is_deleted='N')
      JOIN website_subscriptions ws ON (ws.id=od.website_id)
      JOIN customer c ON (c.id=ws.customer_id)
      JOIN customer s ON (s.id=c.sponsor_id)
      JOIN prd_main p ON (p.id=od.product_id)
      JOIN prd_matrix px ON (FIND_IN_SET(px.id,od.plan_id))
      WHERE o.created_at >= (NOW() - INTERVAL 70 MINUTE) AND c.type IN ('Customer','Group')
      AND s.type in('Agent','Group') AND od.is_refund='N' 
      AND o.status IN('Payment Approved','Completed','Pending Settlement')
      AND p.product_type!='ServiceFee'
      GROUP BY od.id";
    $odrRes = $pdo->select($odrSql);
    echo "Orders Found:" . count($odrRes) . "<br>";
  /*---------- Fetch Todays Approved Orders Code ENDS ------------*/
  // pre_print($odrRes);
  if(count($odrRes) > 0){
    foreach ($odrRes as $order) {

      $orderId = $order['id'];
      $customerId = $order['customer_id'];
      $order_detail_id = $order['order_detail_id'];
      $productId = $order['product_id'];
      $planId = $order['plan_id'];

      $parentProductId = $order['parent_product_id'];
      $sponsorType = $order['sponsor_type'];

      $renewCount = $order["covPeriod"] - 1;
      
      /*---------- Check Order Status While Generating Commission Code START ------*/
        $chkOdrStatusSql = "SELECT id FROM orders WHERE id=:id AND status NOT IN('Payment Approved','Completed','Pending Settlement')";
        $chkOdrStatusRes = $pdo->selectOne($chkOdrStatusSql,array(":id" => $order['id']));

        if($chkOdrStatusRes){
          continue;
        }
      /*---------- Check Order Status While Generating Commission Code ENDS -------*/

      $selTransactions = "SELECT id FROM transactions WHERE order_id=:odrID AND transaction_status IN('Payment Approved','Pending Settlement') ORDER BY id DESC";
      $resTransactions = $pdo->selectOne($selTransactions,array(":odrID" => $order["id"]));
      $order["transaction_tbl_id"] = 0;
      if(!empty($resTransactions["id"])){
        $order["transaction_tbl_id"] = $resTransactions["id"]; 
      }

      /*----------  Get Order Commission Count Code START ------*/
        if(!isset($commOdr[$order['id']])){
          $commGenCount = $commObj->getCommOdrId($order['id']);
          $commGenCount = !empty($commGenCount) ? $commGenCount : 1;
          $commOdr[$order['id']] = true;
        }
      /*----------  Get Order Commission Count Code ENDS ------*/

      /*---------- Generating PMPM Commissions Code START -------------------------*/
        $pmpmExtra = array(
          "globalProductId" => $order['globalProductId'],
          "planType" => $order['planType'],
          "planPrice" => $order['planPrice'],
          "planCommAmt" => $order['planCommAmt'],
          "planNonCommAmt" => $order['planNonCommAmt'],
          "sponsor_id" => $order['sponsor_id'],
          "upline_sponsors" => $order['upline_sponsors'],
        );
        $pmpmCommissions = $commObj->getPmpmCommissionRules($order['product_id'],$order['customer_id'],$order['id'],$order['is_renewal'],$renewCount,$pmpmExtra);
          if(!empty($pmpmCommissions)){
            foreach ($pmpmCommissions as $key => $pmpmComm){
              $agentRow = $pdo->selectOne("SELECT id,customer_id as agentId,agent_coded_id as agentCodedId
                                        FROM customer_settings 
                                        WHERE customer_id=:agentId",array(":agentId" => $pmpmComm['agentId']));
              $pmpmSql = array(
                  "customer_id" => $pmpmComm['agentId'],
                  "website_id" => $order['website_id'],
                  "rule_id" => $pmpmComm['ruleId'],
                  "product_id" => $order['product_id'],
                  "plan_id" => $order['plan_id'],
                  "prd_plan_type_id" => $order['prd_plan_type_id'],
                  "parent_product_id" => $order['parent_product_id'],
                  "payer_id" => $order['customer_id'],
                  "payer_type" => "Customer",

                  "commissionable_unit_price" => $pmpmComm['commissionable_amount'] > 0 ? $pmpmComm['commissionable_amount'] : $order['planCommAmt'],
                  "amount" => $pmpmComm['amount'],
                  "percentage" => $pmpmComm['original_percentage'],

                  "order_id" => $order['id'],
                  "order_detail_id" => $order['order_detail_id'],
                  "type" => 'Retail Sales',
                  "level" => $agentRow['agentCodedId'],
                  "original_percentage" => $pmpmComm['original_amount'],
                  "original_amount" => $pmpmComm['original_percentage'],

                  "sub_type" => 'PMPM',
                  "is_pmpm_comm" => 'Y',
                  "status" => "Pending",
                  "created_at" => "msqlfunc_NOW()",
                  "comm_odr_id" => $commGenCount,
                  "transaction_id" => $order["transaction_tbl_id"],
              );

              $checkPmpmSql = "SELECT id FROM commission WHERE sub_type='PMPM' AND customer_id=:custId AND order_id=:odrId AND order_detail_id=:order_detail_id AND is_reversed='N'";
              $checkPmpmParams = array(":custId" => $agentRow["agentId"],":odrId" => $order['id'],":order_detail_id" => $order['order_detail_id']);
              $checkPmpmRes = $pdo->selectOne($checkPmpmSql,$checkPmpmParams);

              if(empty($checkPmpmRes)){
                if ($order['is_renewal'] == "Y") {
                  $pmpmSql["pay_period"] = $monthlyPayPeriod;
                  $pmpmSql["commission_duration"] = "monthly";
                  $pmpmSql["balance_type"] = "addCredit";

                  $pmpmCommId = $pdo->insert("commission", $pmpmSql);
                  
                  /*---------- Payable Insert Code START ------*/
                    $payable_params=array(
                        'payable_type'=>'PMPM_Commission_Monthly',
                        'type'=>'PMPM',
                        'commission_id'=>$pmpmCommId,
                        'transaction_tbl_id'=>$order["transaction_tbl_id"],
                        'order_detail_id'=>$order_detail_id,
                      );
                      $payable= $functionsList->payable_insert($pmpmSql['order_id'],$pmpmSql['payer_id'],$pmpmSql['product_id'],$pmpmSql['plan_id'],$payable_params);
                  /*-----------Payable Insert Code END---------*/
                  
                  $commObj->agentCommissionBalance("addCredit","monthly",$agentRow['agentId'],$monthlyPayPeriod,$pmpmComm['amount'],$pmpmCommId);
                }else{
                  $pmpmSql["pay_period"] = $weeklyPayPeriod;
                  $pmpmSql["commission_duration"] = "weekly";
                  $pmpmSql["balance_type"] = "addCredit";

                  $pmpmCommId = $pdo->insert("commission", $pmpmSql);
                  
                  /*---------- Payable Insert Code START ------*/
                    $payable_params=array(
                        'payable_type'=>'PMPM_Commission',
                        'type'=>'PMPM',
                        'commission_id'=>$pmpmCommId,
                         'transaction_tbl_id'=>$order["transaction_tbl_id"],
                         'order_detail_id'=>$order_detail_id,
                      );
                      $payable= $functionsList->payable_insert($pmpmSql['order_id'],$pmpmSql['payer_id'],$pmpmSql['product_id'],$pmpmSql['plan_id'],$payable_params);
                  /*-----------Payable Insert Code END---------*/
                    $commObj->agentCommissionBalance("addCredit","weekly",$agentRow['agentId'],$weeklyPayPeriod,$pmpmComm["amount"],$pmpmCommId);
                }
              }
            }
          }
      /*---------- Generating PMPM Commissions Code ENDS -------------------------*/

      /*---------- If Commission Already Generated then Skip Product Record Code START ----*/
        $checkCommSql = "SELECT id FROM commission 
        WHERE order_id=:odrId AND order_detail_id=:order_detail_id AND is_reversed='N' AND sub_type NOT IN ('PMPM')";
        $checkCommParams = array(":odrId" => $order['id'], ":order_detail_id" => $order['order_detail_id']);

        $checkCommRes = $pdo->selectOne($checkCommSql,$checkCommParams);

        if (count($checkCommRes) > 0) {
          echo "<br><b>Commission already generated : ".$order["name"]."</b><hr><br>";
          continue;
        }
      /*---------- If Commission Already Generated then Skip Product Record Code ENDS  ----*/

      /*---------- Get Agent Pay Level Code START ---------------------------------*/
       // Coded Levels for Agents
        $codedSql = "SELECT id,level FROM agent_coded_level";
        $codedRow = $pdo->select($codedSql);

        if($codedRow){
          foreach ($codedRow as $cr) {
            $CODED_ARR[$cr['id']] = $cr['level'];
          }
        }

        if($order['sponsor_type'] == 'Agent'){
           $sponsorId = $order['sponsor_id'];
            // Pay Levels for Agents
              $payLevels = $commObj->getAgentPayLevelsArr($order['sponsor_id']);
              $tmpPayLevels = $payLevels;

              if(in_array($order['product_id'],$stepFeePrdIds)) {
                  $isFoundAgent = false;
                  foreach ($tmpPayLevels as $key => $value) {
                      if($isFoundAgent == false && $value > 0) {
                          $payLevels[$key] = $value;
                          $isFoundAgent = true;
                          $sqlSponsor = "SELECT agent_coded_level FROM customer_settings WHERE id= :sponsor_id";
                          $resCoded = $pdo->selectOne($sqlSponsor, array(':sponsor_id' => $value));
                          if(isset($resCoded['agent_coded_level']) && $resCoded['agent_coded_level'] == 'LOA'){
                            $isFoundAgent = false;
                          }
                      } else {
                          $payLevels[$key] = 0;
                      }
                  }
              }
        }

        if ($order['sponsor_type'] == 'Group') {
          $sqlSponsor = "SELECT sponsor_id FROM customer WHERE id= :sponsor_id";
          $resSponsor = $pdo->selectOne($sqlSponsor, array(':sponsor_id' => $order['sponsor_id']));
          $order['sponsor_id'] = $resSponsor['sponsor_id'];
           // Pay Levels for Agents
              $payLevels = $commObj->getAgentPayLevelsArr($order['sponsor_id']);
              $tmpPayLevels = $payLevels;

              if(in_array($order['product_id'],$stepFeePrdIds)) {
                  $isFoundAgent = false;
                  foreach ($tmpPayLevels as $key => $value) {
                      if($isFoundAgent == false && $value > 0) {
                          $payLevels[$key] = $value;
                          $isFoundAgent = true;
                          $sqlSponsorLevel = "SELECT agent_coded_level FROM customer_settings WHERE id= :sponsor_id";
                          $resCoded = $pdo->selectOne($sqlSponsorLevel, array(':sponsor_id' => $value));
                          if(isset($resCoded['agent_coded_level']) && $resCoded['agent_coded_level'] == 'LOA'){
                            $isFoundAgent = false;
                          }
                      } else {
                          $payLevels[$key] = 0;
                      }
                  }
              }
        }
      /*---------- Get Agent Pay Level Code ENDS  ---------------------------------*/

      /*---------- Generating Commissions Code START ------------------------------*/
        if(count($payLevels) > 0){

          /*--------------- Get Commission Rule of Products Code START -------*/
            $commRules = $commObj->getCommissionRules($order["sponsor_id"], $order["id"],$order["order_detail_id"],$order["product_id"],$stepFeePrdIds, $order["sponsor_type"]);
          /*--------------- Get Commission Rule of Products Code ENDS --------*/

          $totalPrevPaid = 0;
        
          if (!empty($commRules)){
            foreach ($payLevels as $level => $agentId) {

              /*----------- Calculate Commission Amount Code START ----------*/
                $amount = 0;
                $percentage = 0;
                $originalPercentage = 0;
                $originalAmount = 0;
                
                if($commRules['commission_on'] == "Agent Level") {
                  $commAmtArr = json_decode($commRules['commission_json'], true);
                }else{
                  $commAmtArr = json_decode($commRules['commission_json'], true);
                  $commAmtArr = $commAmtArr[$order["prd_plan_type_id"]];
                }

                if (!isset($commAmtArr[$CODED_ARR[$level]]) || $commAmtArr[$CODED_ARR[$level]] == 0 || $agentId <= 1) {
                  continue;
                }

                if (!empty($commAmtArr)) {

                  if ($commAmtArr[$CODED_ARR[$level]]['amount_type'] == 'Percentage') {
                    $originalPercentage = $commAmtArr[$CODED_ARR[$level]]['amount'];
                    
                    $percentage = $originalPercentage - $totalPrevPaid;
                    $totalPrevPaid = $totalPrevPaid + $percentage;
                    $orderTotal = ($order['planCommAmt'] * $order['qty']);
                    $amount = ($orderTotal) * ($percentage / 100);
                  } else {
                    $originalAmount = $commAmtArr[$CODED_ARR[$level]]['amount'];
                    $amount = ($order['qty'] * $commAmtArr[$CODED_ARR[$level]]['amount']);
                  }
              /*----------- Calculate Commission Amount Code ENDS -----------*/

                if($amount > 0) {
                    if (in_array($agentId, array(1))) {
                      continue;
                    }

                    $insCommSql = array(
                        "customer_id" => $agentId,
                        "website_id" => $order['website_id'],
                        "rule_id" => $commRules['id'],
                        "product_id" => $order['product_id'],
                        "plan_id" => $order['plan_id'],
                        "prd_plan_type_id" => $order['prd_plan_type_id'],
                        "parent_product_id" => $order['parent_product_id'],
                        "payer_id" => $order['customer_id'],
                        "payer_type" => "Customer",
                        
                        "commissionable_unit_price" => $order['planCommAmt'],
                        "amount" => $amount,
                        "percentage" => $percentage,
                        
                        "type" => 'Retail Sales',
                        "order_id" => $order['id'],
                        "order_detail_id" => $order['order_detail_id'],
                        "level" => $level,
                        "original_percentage" => $originalPercentage,
                        "original_amount" => $originalAmount,
                        "status" => "Pending",
                        "created_at" => "msqlfunc_NOW()",
                        "comm_odr_id" => $commGenCount,
                        "transaction_id" => $order["transaction_tbl_id"],
                    );

                    // Advance commission Agent fee code start
                      $advanceRuleRow = array();
                      $advanceOn = $commObj->checkAdvanceCommissionOn($agentId);

                      if(checkIsset($advanceOn) == "Y" && $order['is_renewal'] == "Y"){
                          $advanceRuleRow = $commObj->getAdvanceCommissionRules($order['product_id'],$order['customer_id'],$agentId,array("globalProductId" => $order['globalProductId']));
                     
                        if(!empty($advanceRuleRow) && $advanceRuleRow["charged_to"] == "Agents"){
                          
                          $advanceFeeArr = array(
                            "transaction_tbl_id" => $order["transaction_tbl_id"],
                            "unit_price" => $order["unit_price"],
                            'order_detail_id'=>$order_detail_id,
                          );
                          $commObj->generateAdvanceFeeCommission($insCommSql, $commRules, $advanceRuleRow,$stepFeePrdIds,$order["is_renewal"],$renewCount,$advanceFeeArr);
                        }
                      }
                    // Advance commission Agent fee code ends


                    // Graded commission code start
                      $gradedRuleRow = array();
                      $gradedOn = $commObj->checkGradedCommissionOn($agentId);
                      if(checkIsset($gradedOn) == "Y"){
                        $gradedRuleRow = $commObj->getGradedCommissionRules($order['product_id'],$order['customer_id'],$agentId,array("globalProductId" => $order['globalProductId']));

                        if(!empty($gradedRuleRow) && $gradedRuleRow["charged_to"] == "Agents"){
                     
                          $commObj->generateGradedFeeCommission($insCommSql, $commRules, $gradedRuleRow,$stepFeePrdIds,$order["is_renewal"],$renewCount,array("transaction_tbl_id"=>$order["transaction_tbl_id"],'order_detail_id'=>$order_detail_id,));
                        }
                      }
                   
                      if(!empty($gradedRuleRow) && ($gradedRuleRow["from_renewal"] >= $order["covPeriod"] && $order["covPeriod"] <= $gradedRuleRow["to_renewal"])){
                        $insCommSql['is_graded_comm'] = 'Y';                    
                        $insCommSql['earned_amount'] = $amount;                    
                        $insCommSql['graded_rule_id'] = $gradedRuleRow["gradedFeeId"];                    
                        $insCommSql['graded_percentage'] = $gradedRuleRow["graded_percentage"];                    
                        $amount = ($amount) * ($gradedRuleRow["graded_percentage"] / 100);
                        $insCommSql['amount'] = $amount;  
                      }else{
                          $insCommSql["amount"] = $amount;
                          $insCommSql['graded_percentage'] = 100;
                          $insCommSql['earned_amount'] = $amount;
                      }
                    // Graded commission code ends

                    /*---------- generating renewal order commission ---------*/
                      if ($order['is_renewal'] == "Y") {

                        $insCommSql["sub_type"] = 'Renewals';

                        /*---------- checking for advance commission if already paid for this customer ---------*/
                          $advanceComm = $commObj->checkAdvanceCommissionPaid($order,$agentId);
                          $advance_commission_id = 0;
                          $advance_table = "";
                            if($advanceComm['status'] == true){
                              $advance_commission_id = $advanceComm['id'];
                              $insCommSql["advance_commission_id"] = $advance_commission_id;
                              $insCommSql["advance_comm_amount"] = $insCommSql['amount'];
                            }
                        /*----------------------------------------------------------------------------*/

                        
                     
                        /*---------- generating order commission on monthly pay period ---------*/
                          if($commRules["renewal_commission_duration"] == "monthly") {
                            $insCommSql["pay_period"] = $monthlyPayPeriod;
                            $insCommSql["commission_duration"] = "monthly";
                            $insCommSql["balance_type"] = "addCredit";
                            $monthly_commission_id=$pdo->insert("commission", $insCommSql);
                            
                            $commObj->agentCommissionBalance("addCredit","monthly",$agentId,$monthlyPayPeriod,$insCommSql["amount"],$monthly_commission_id);


                              //********* Payable Insert Code Start ********************
                                $payable_params=array(
                                  'payable_type'=>'Commission_Monthly',
                                  'type'=>'Commission',
                                  'commission_id'=>$monthly_commission_id,
                                   'transaction_tbl_id'=>$order["transaction_tbl_id"],
                                   'order_detail_id'=>$order_detail_id,
                                );
                                $payable= $functionsList->payable_insert($insCommSql['order_id'],$insCommSql['payer_id'],$insCommSql['product_id'],$insCommSql['plan_id'],$payable_params);
                              //********* Payable Insert Code End   ********************

                            $checkNotificationSql = "SELECT id FROM agent_notification_settings WHERE agent_id =:agentId AND is_deleted='N' AND is_commissions_notifications='Y'";
                            $checkNotificationRes = $pdo->selectOne($checkNotificationSql, array(":agentId" => $agentId));

                            if ($checkNotificationRes) {
                              addAgentNotification($agentId, 8, "{AGENT}/commissions.php?pay_period=" . $monthlyPayPeriod, 0, 'N', 0);
                            }
                        /*---------- generating order commission on weekly pay period ---------*/
                          } elseif ($commRules["renewal_commission_duration"] == "weekly") {
                            $insCommSql["pay_period"] = $weeklyPayPeriod;
                            $insCommSql["commission_duration"] = "weekly";
                            $insCommSql["balance_type"] = "addCredit";
                            $weekly_commission_id=$pdo->insert("commission", $insCommSql);
                        

                            $commObj->agentCommissionBalance("addCredit","weekly",$agentId,$weeklyPayPeriod,$insCommSql["amount"],$weekly_commission_id);

                              //********* Payable Insert Code Start ********************
                                $payable_params=array(
                                  'payable_type'=>'Commission',
                                  'type'=>'Commission',
                                  'commission_id'=>$weekly_commission_id,
                                   'transaction_tbl_id'=>$order["transaction_tbl_id"],
                                   'order_detail_id'=>$order_detail_id,
                                );
                                $payable= $functionsList->payable_insert($insCommSql['order_id'],$insCommSql['payer_id'],$insCommSql['product_id'],$insCommSql['plan_id'],$payable_params);
                              //********* Payable Insert Code End   ********************

                            $checkNotificationSql = "SELECT id FROM agent_notification_settings WHERE agent_id =:agentId AND is_deleted='N' AND is_commissions_notifications='Y'";
                            $checkNotificationRes = $pdo->selectOne($checkNotificationSql, array(":agentId" => $agentId));

                            if ($checkNotificationRes) {
                              addAgentNotification($agentId, 8, "{AGENT}/commissions.php?pay_period=" . $weeklyPayPeriod, 0, 'N', 0);
                            }
                          }

                        /*---------- updating advance commission order counts ---------*/
                          if ($advance_commission_id > 0){
                            $updateSql = array("month_count" => "msqlfunc_month_count + 1");
                            $updateWhere = array("clause" => "id=:id", "params" => array(":id" => $advance_commission_id));
                            $pdo->update("commission", $updateSql, $updateWhere);
                          }
                        /*----------------------------------------------------------------------------*/
                    /*---------- generating New order commission ---------*/
                      } else {

                        $insCommSql["sub_type"] = 'New';

                        if ($commRules["new_business_commission_duration"] == "monthly") {
                          $insCommSql["pay_period"] = $monthlyPayPeriod;
                          $insCommSql["commission_duration"] = "monthly";
                          $insCommSql["balance_type"] = "addCredit";
                          $monthly_commission_id = $pdo->insert("commission", $insCommSql);

                          $commObj->agentCommissionBalance("addCredit","monthly",$agentId,$monthlyPayPeriod,$insCommSql["amount"],$monthly_commission_id);

                            //********* Payable Insert Code Start ********************
                              $payable_params=array(
                                'payable_type'=>'Commission_Monthly',
                                'type'=>'Commission',
                                'commission_id'=>$monthly_commission_id,
                                 'transaction_tbl_id'=>$order["transaction_tbl_id"],
                                 'order_detail_id'=>$order_detail_id,
                              );
                              $payable= $functionsList->payable_insert($insCommSql['order_id'],$insCommSql['payer_id'],$insCommSql['product_id'],$insCommSql['plan_id'],$payable_params);
                            //********* Payable Insert Code End   ********************

                          $checkNotificationSql = "SELECT id FROM agent_notification_settings WHERE agent_id =:agentId AND is_deleted='N' AND is_commissions_notifications='Y'";
                          $checkNotificationRes = $pdo->selectOne($checkNotificationSql, array(":agentId" => $agentId));

                          if ($checkNotificationRes) {
                            addAgentNotification($agentId, 8, "{AGENT}/commissions.php?pay_period=" . $monthlyPayPeriod, 0, 'N', 0);
                          }
                        } elseif ($commRules["new_business_commission_duration"] == "weekly") {
                          $insCommSql["pay_period"] = $weeklyPayPeriod;
                          $insCommSql["commission_duration"] = "weekly";
                          $insCommSql["balance_type"] = "addCredit";
                          $weekly_commission_id = $pdo->insert("commission", $insCommSql);

                          $commObj->agentCommissionBalance("addCredit","weekly",$agentId,$weeklyPayPeriod,$insCommSql["amount"],$weekly_commission_id);

                            //********* Payable Insert Code Start ********************
                              $payable_params=array(
                                'payable_type'=>'Commission',
                                'type'=>'Commission',
                                'commission_id'=>$weekly_commission_id,
                                 'transaction_tbl_id'=>$order["transaction_tbl_id"],
                                 'order_detail_id'=>$order_detail_id,
                              );
                              $payable= $functionsList->payable_insert($insCommSql['order_id'],$insCommSql['payer_id'],$insCommSql['product_id'],$insCommSql['plan_id'],$payable_params);
                            //********* Payable Insert Code End   ********************

                          $checkNotificationSql = "SELECT id FROM agent_notification_settings WHERE agent_id =:agentId AND is_deleted='N' AND is_commissions_notifications='Y'";
                          $checkNotificationRes = $pdo->selectOne($checkNotificationSql, array(":agentId" => $agentId));

                          if ($checkNotificationRes) {
                            addAgentNotification($agentId, 8, "{AGENT}/commissions.php?pay_period=" . $weeklyPayPeriod, 0, 'N', 0);
                          }
                        }

                        /*--------------- Generate Advance commission Code Start ------------------*/
                          $advanceOn = $commObj->checkAdvanceCommissionOn($agentId);
                          if(checkIsset($advanceOn) == "Y"){
                            $advanceRuleRow = $commObj->getAdvanceCommissionRules($order['product_id'],$order['customer_id'],$agentId,array("globalProductId" => $order['globalProductId']));
                            if(!empty($advanceRuleRow)){
                              $commObj->generateAdvanceCommission($insCommSql, $commRules, $advanceRuleRow,$stepFeePrdIds,array("transaction_tbl_id"=>$order["transaction_tbl_id"],'order_detail_id'=>$order_detail_id,));
                              if(checkIsset($advanceRuleRow["charged_to"]) == "Agents"){
                                $advanceFeeArr = array(
                                  "transaction_tbl_id" => $order["transaction_tbl_id"],
                                  "unit_price" => $order["unit_price"],
                                  'order_detail_id'=>$order_detail_id,
                                );

                                $commObj->generateAdvanceFeeCommission($insCommSql, $commRules, $advanceRuleRow,$stepFeePrdIds,$order["is_renewal"],$renewCount,$advanceFeeArr);
                              }
                            }
                          }
                        /*--------------- Generate Advance commission Code Ends -------------------*/

                        /*---------- Fetch Agent details to send email ---------*/
                          if ($agentId > 0) {
                            $agentDetail = $pdo->selectOne("SELECT id,fname,email,lname,user_name FROM customer WHERE id=:id", array(":id" => $agentId));
                            if ($agentDetail) {
                              $send_mail_email = $agentDetail["email"];
                              $agent_res[$order['sponsor_id']] = array(
                                  'id' => $agentDetail["id"],
                                  'sponsor_name' => (!empty($order['sponsor_business_name']) ? $order["sponsor_business_name"] : ($order["sponsor_name"])),
                              );
                            }
                          }
                        /*-------------------------------------------------------*/
                      }
                    /*------------------------------------------------*/
                }
              } 
            }
          }
        }
      /*---------- Generating Commissions Code ENDS --------------------------*/
    }
  } else {
    echo "Orders Not Found";
  }
  
  /*---------- SEND AGENT COMMISSION EARNED EMAIL  -----------*/
    if(!empty($agent_res)) {
        foreach ($agent_res as $key => $agent_row) {
            $agentDetail = $pdo->selectOne("SELECT fname,email,lname,user_name,type from customer where id=:id", array(":id" => $agent_row['id']));
            if ($agentDetail) {
                $send_mail_email = $agentDetail["email"];
                $mail_data = array();
                $mail_data["fname"] = $agentDetail["fname"];
                $mail_data["lname"] = $agentDetail["lname"];
                $mail_data["email"] = $agentDetail["email"];
                $mail_data["username"] = $agentDetail["user_name"];

                $smart_tags = get_user_smart_tags($agent_row['id'],'agent');
                
                if($smart_tags){
                    $mail_data = array_merge($mail_data,$smart_tags);
                }

                if (!empty($send_mail_email)) {
                  $link = $HOST;
                  if ($agentDetail["type"] == "Agent") {
                    $link = $HOST . "/agents";
                  } else if ($agentDetail["type"] == "Group") {
                    $link = $HOST . "/groups";
                  }
                  $mail_data["link"] = $mail_data["invite_link"] = "<a href='<?=$link?>'>Here</a>";
                  $mail_data["sponsor_name"] = $agent_row['sponsor_name'];
                  trigger_mail(26, $mail_data, $send_mail_email);
                }
            }
        }
    }
  /*----------------------------------------------------------*/


  /*--------- System script status code start ----------*/
    $cronSql = "SELECT last_processed FROM system_scripts WHERE script_code=:script_code";
    $cronWhere = array(":script_code" => "commission");
    $cronRow = $pdo->selectOne($cronSql,$cronWhere);

    $cronUpdParams = array("is_running" => "N",
                          "next_processed"=>date("Y-m-d H:i:s",strtotime("+30 minutes", strtotime($cronRow['last_processed'])))
                          );
    $cronWhere = array(
                "clause" => "script_code=:script_code", 
                "params" => array(
                    ":script_code" => 'commission'
                )
            );
    $pdo->update('system_scripts',$cronUpdParams,$cronWhere);
  /*---------- System script status code ends -----------*/


  echo "<br>Completed";
  dbConnectionClose();

?>
