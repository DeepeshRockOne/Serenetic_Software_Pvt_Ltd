<?php
  include_once dirname(__DIR__) . "/includes/functions.php";
/* This class contains commssion functions */


class Commission {

  private $pdo;

  public function __construct() {
    global $pdo;

    $this->pdo = $pdo;
  }

  /*--------------------- Get Weekly pay period Code START ------------*/
    public function getWeeklyPayPeriod($date = "") {
      global $pdo;
      $weekDayRes = $pdo->selectOne("SELECT commission_day FROM commission_periods_settings WHERE commission_type='weekly'");
      $commDay = !empty($weekDayRes["commission_day"]) ? $weekDayRes["commission_day"] : "Sunday";

      $date = !empty($date) ? date("Y-m-d",strtotime($date)) : date('Y-m-d');
      
      if (date('l', strtotime($date)) == $commDay) {
        $payPeriod = $date;
      } else {
        $payPeriod = date('Y-m-d', strtotime("next $commDay",strtotime($date)));
      }
      return $payPeriod;
    }
  /*--------------------- Get Weekly pay period Code ENDS -------------*/

  /*--------------------- Get Monthly pay period Code START ------------*/  
    public function getMonthlyPayPeriod($date = "") {
      $date = !empty($date) ? date("Y-m-d",strtotime($date)) : date('Y-m-d');
      $payPeriod = date("Y-m-d", strtotime("last day of this month",strtotime($date)));
      return $payPeriod;
    }
  /*--------------------- Get Monthly pay period Code ENDS -------------*/  

  /*-------------------------- Reverse Order Commission Code START ---------*/
    public function reverseOrderCommissions($order_id,$extra_params = array()){
        $request_params = array();
        $request_params['order_id'] = $order_id;
        $request_params['extra_params'] = $extra_params;
        add_commission_request('reverse_commissions',$request_params);
    }
  /*-------------------------- Reverse Order Commission Code ENDS ----------*/

  /*-------------------------- Agent Commission Balance Code START  ----------*/
    public function agentCommissionBalance($type, $commission_duration, $agent_id, $pay_period, $amount, $commission_id = 0, $message = '', $extra_params = array())
    {
      $pay_period = date("Y-m-d", strtotime($pay_period));
      $creditArr = array("addCredit","revCredit","addPMPMCredit","revPMPMCredit");
      $debitArr = array("addDebit","revDebit");

      if(!empty($type)){
          if(in_array($type, $creditArr)){
            $extra_params['message']=$message;
            $this->commissionCreditBalance($type, $commission_duration, $agent_id, $pay_period, $amount, $commission_id, $extra_params);
          }elseif (in_array($type, $debitArr)) {
              if (empty($message)) {
                  if ($type == "addDebit") {
                      $message = "Credited of " . ucfirst($commission_duration) . " Advance Commission";
                      $extra_params["transaction_type"] = checkIsset($extra_params["transaction_type"]) == "" ? "Advance_Generated" : $extra_params["transaction_type"];
                  } else {
                      $message = "Debited of " . ucfirst($commission_duration) . " Advance Commission Reversal";
                      $extra_params["transaction_type"] = checkIsset($extra_params["transaction_type"]) == "" ? "Advance_Reversed" : $extra_params["transaction_type"];
                  }
              }
              if (!empty($amount)) {
                  $this->commissionDebitBalance($type, $commission_duration, $agent_id, $pay_period, $amount, $message, $commission_id, $extra_params);
              }
          }
      }
    }
    public function commissionCreditBalance($type, $commission_duration, $agent_id, $pay_period, $amount, $commission_id = 0, $extra_params = array())
    {
        global $pdo;
        
        $earnedCredit = array("addCredit","revCredit");
        $pmpmCredit = array("addPMPMCredit","revPMPMCredit");

        $creditSql = "SELECT id,credit,pmpm_credit FROM commission_credit_balance WHERE agent_id=:agent_id AND pay_period=:pay_period AND commission_duration=:commission_duration AND status='Open'";
        $params = array(":agent_id" => $agent_id, ":pay_period" => $pay_period, ":commission_duration" => $commission_duration);
        $creditRes = $pdo->selectOne($creditSql, $params);

        if (empty($creditRes)) {
            $insCreditParams = array(
                "agent_id" => $agent_id,
                "commission_duration" => $commission_duration,
                "pay_period" => $pay_period,
                "status" => 'Open',
            );
            if(in_array($type,$earnedCredit)){
                $insCreditParams["credit"] = $amount;
                $earnedBalance = $amount;
                $pmpmBalance = 0;
            }else if(in_array($type,$pmpmCredit)){
                $insCreditParams["pmpm_credit"] = $amount;
                $earnedBalance = 0;
                $pmpmBalance = $amount;
            } else {
                $earnedBalance = 0;
                $pmpmBalance = 0;
            }
            $credit_id = $pdo->insert("commission_credit_balance", $insCreditParams);
        } else {
            $earnedBalance = $creditRes['credit'];
            $pmpmBalance = $creditRes['pmpm_credit'];
            $credit = 0;
            $pmpm_credit = 0;
            if(in_array($type,$earnedCredit)){
                $earnedBalance = $earnedBalance + $amount;
                $credit = $amount;
            }else if(in_array($type,$pmpmCredit)){
                $pmpmBalance = $pmpmBalance + $amount;
                $pmpm_credit = $amount;
            }

            $updateCreditParams = array(
                "credit" => "msqlfunc_credit + $credit",
                "pmpm_credit" => "msqlfunc_pmpm_credit + $pmpm_credit"
            );
            $updWhere = array("clause" => "id=:id",
                "params" => array(":id" => $creditRes['id']),
            );
            $pdo->update("commission_credit_balance", $updateCreditParams, $updWhere);

            $credit_id = $creditRes['id'];
        }

        // inserting wallet history
        $insParams = array(
          "credit_id" => $credit_id,
          "agent_id" => $agent_id,
          "commission_id" => $commission_id,
          "admin_id" => (isset($_SESSION['admin']['id']) ? $_SESSION['admin']['id'] : 0),
          "type" => $type,
          "amount" => $amount,
          "credit" => $earnedBalance,
          "pmpm_credit" => $pmpmBalance,
          "pay_period" => $pay_period,
          "commission_duration" => $commission_duration,
          "transaction_type" => (isset($extra_params['transaction_type'])?$extra_params['transaction_type']:''),
          "message" => (isset($extra_params['message'])?$extra_params['message']:''),
          "req_url" => (isset($_SERVER['REQUEST_URI'])?$_SERVER['REQUEST_URI']:''),
          "ip_address" => (isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : ''),
        );
        $pdo->insert("commission_credit_balance_history", $insParams);
    }
    public function commissionDebitBalance($type, $commission_duration, $agent_id, $pay_period, $amount, $message, $commission_id = 0, $extra_params = array())
    {
      global $pdo;
      $debitHistoryId = 0;
      if (!empty($amount)) {
        $debitBalSql = "SELECT id,agent_id,balance FROM commission_debit_balance WHERE agent_id=:agent_id";
        $debitBalParam = array(":agent_id" => $agent_id);
        $debitBalRow = $pdo->selectOne($debitBalSql, $debitBalParam);
  
        if (!empty($debitBalRow["id"])) {
          $debit_id = $debitBalRow["id"];
          $total_balance = $debitBalRow['balance'] + $amount;
          $updateArr = array("balance" => "msqlfunc_balance + $amount");
          $updateWhere = array("clause" => "agent_id=:agent_id", "params" => array(":agent_id" => $agent_id));
          $pdo->update("commission_debit_balance", $updateArr, $updateWhere);
        } else {
          $total_balance = $amount;
          $updateSql = array(
            "agent_id" => $agent_id,
            "balance" => $total_balance,
          );
          $debit_id = $pdo->insert("commission_debit_balance", $updateSql);
        }
  
        $debitType = "";
        if ($type == "addDebit") {
          $debitType = "Credit";
        } else if ($type == "revDebit") {
          $debitType = "Debit";
        }
  
        // inserting wallet history
        $insParams = array(
          "debit_id" => $debit_id,
          "agent_id" => $agent_id,
          "commission_id" => $commission_id,
          "admin_id" => isset($_SESSION['admin']['id']) ? $_SESSION['admin']['id'] : 0,
          "type" => $debitType,
          "amount" => $amount,
          "current_balance" => $total_balance,
          "pay_period" => $pay_period,
          "commission_duration" => $commission_duration,
          "message" => $message,
          "ip_address" => isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : '',
        );
        if (!empty($extra_params["transaction_type"])) {
          $insParams["transaction_type"] = $extra_params["transaction_type"];
        }
        $debitHistoryId = $pdo->insert("commission_debit_balance_history", $insParams);
      }
      return $debitHistoryId;
    }
    public function applyToAgentWallet($type, $agent_id, $commission_duration, $pay_period, $amount, $message, $extra_params = array())
    {
      global $pdo;
      $walletHistoryId = 0;
  
      if (!empty($amount)) {
        $checkWalletSql = "SELECT id,balance FROM commission_wallet WHERE agent_id=:agent_id";
        $checkWalletParam = array(":agent_id" => $agent_id);
        $checkWalletRes = $pdo->selectOne($checkWalletSql, $checkWalletParam);
  
        if ($checkWalletRes) {
          $walletId = $checkWalletRes["id"];
          if (isset($extra_params['is_full']) && $extra_params['is_full'] == 'Y') {
            $total_balance = 0;
          } else {
            $total_balance = $checkWalletRes['balance'] + $amount;
          }
          $updateArr = array("balance" => $total_balance);
          $updateWhere = array("clause" => "agent_id=:agent_id", "params" => array(":agent_id" => $agent_id));
          $pdo->update("commission_wallet", $updateArr, $updateWhere);
        } else {
          $total_balance = $amount;
          $insParams = array(
            "agent_id" => $agent_id,
            "balance" => $total_balance,
          );
          $walletId = $pdo->insert("commission_wallet", $insParams);
        }
  
        // inserting wallet history
        $historyParams = array(
          "wallet_id" => $walletId,
          "agent_id" => $agent_id,
          "admin_id" => isset($_SESSION['admin']['id']) ? $_SESSION['admin']['id'] : 0,
          "type" => $type,
          "amount" => $amount,
          "current_balance" => $total_balance,
          "pay_period" => $pay_period,
          "commission_duration" => $commission_duration,
          "message" => $message,
          "ip_address" => isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : '',
        );
        if (!empty($extra_params["ach_id"])) {
          $historyParams["ach_id"] = $extra_params["ach_id"];
        }
        if (!empty($extra_params["reinstate_ach_id"])) {
          $historyParams["reinstate_ach_id"] = $extra_params["reinstate_ach_id"];
        }
        if (!empty($extra_params["deposit_detail"])) {
          $historyParams["deposit_detail"] = $extra_params["deposit_detail"];
        }
        if (!empty($extra_params["is_wallet_transfer"])) {
          $historyParams["is_wallet_transfer"] = $extra_params["is_wallet_transfer"];
        }
        if (!empty($extra_params["is_overpay_balance"])) {
          $historyParams["is_overpay_balance"] = $extra_params["is_overpay_balance"];
        }
        $walletHistoryId = $pdo->insert("commission_wallet_history", $historyParams);
      }
      return $walletHistoryId;
    }
    /*-------------------------- Agent Commission Balance Code ENDS  ----------*/
  
    /*------------------------ Get Agent Debit Balance Code Start -----------------------*/
      public function getAgentDebitBalance($agentId,$extra_params=array()){
        global $pdo;
          $debitBalance = 0;
  
          $selDebit = "SELECT db.agent_id,db.balance as debitBalance
                    FROM commission_debit_balance db
                    WHERE db.agent_id=:agentId GROUP BY db.agent_id";
          $resDebit = $pdo->selectOne($selDebit,array(":agentId" => $agentId));
  
          if(!empty($resDebit)){
            $debitBalance = $resDebit['debitBalance'];
          }
          return $debitBalance;
      }
    /*------------------------ Get Agent Debit Balance Code Ends -------------------------*/
  
    /*-------------------------- Agent Commission Balance Code ENDS  ----------*/
    public function applyWalletToDebitBalance($agent_id, $walletId, $debit_id, $walletBalance, $debitBalance, $walletHistoryId)
    {
      global $pdo;
      if (!empty($agent_id) && !empty($walletId) && $walletBalance != '') {
  
        $debit_balance_amount = $debitBalance;
        $wallet_balance_amount = $walletBalance;
        if ($walletBalance >= 0) {
          if ($walletBalance > $debitBalance) {
            $remaning_wallet_amount = $walletBalance - $debitBalance;
          } else {
            $debit_balance_amount = $walletBalance;
          }
        } else {
          $debit_balance_amount = $wallet_balance_amount = $walletBalance;
        }
  
        $pay_period = "";
        $commission_duration = "";
        if ($walletHistoryId > 0) {
          $wallet_history_query = "SELECT id,pay_period,commission_duration FROM commission_wallet_history WHERE id =:id";
          $wallet_history_row = $pdo->selectOne($wallet_history_query, array(':id' => $walletHistoryId));
          if (!empty($wallet_history_row)) {
            $pay_period = $wallet_history_row['pay_period'];
            $commission_duration = $wallet_history_row['commission_duration'];
  
            $updateArr = array("is_wallet_transfer" => 'Y');
            $updateWhere = array("clause" => "id=:id", "params" => array(":id" => $walletHistoryId));
            $pdo->update("commission_wallet_history", $updateArr, $updateWhere);
          }
        } else {
  
          $updateArr = array("is_wallet_transfer" => 'Y');
          $updateWhere = array("clause" => "agent_id =:agent_id AND type='Credit' AND is_paid='N' AND is_reversed='N' AND is_wallet_transfer='N'", "params" => array(":agent_id" => $agent_id));
          $pdo->update("commission_wallet_history", $updateArr, $updateWhere);
  
          $pay_period = $this->getWeeklyPayPeriod();
          $commission_duration = "weekly";
        }
  
        if ($wallet_balance_amount < 0) {
          $this->commissionDebitBalance('addDebit', $commission_duration, $agent_id, $pay_period, abs($debit_balance_amount), 'Wallet balance applied to debit balance', 0, array('transaction_type' => 'Credit_Wallet_Transfer'));
        } else {
          $this->commissionDebitBalance('revDebit', $commission_duration, $agent_id, $pay_period, ($debit_balance_amount * -1), 'Wallet balance applied to debit balance', 0, array('transaction_type' => 'Debit_Wallet_Transfer'));
        }
  
        if ($walletBalance > $debitBalance) {
          $this->applyToAgentWallet('Debit', $agent_id, $commission_duration, $pay_period, ($wallet_balance_amount * -1), 'Wallet balance applied to debit balance');
  
          $this->applyToAgentWallet('Credit', $agent_id, $commission_duration, $pay_period, $remaning_wallet_amount, 'Credited of remaining balance after wallet to debit balance transfer',array('is_overpay_balance'=>'Y'));
        } else {
          $this->applyToAgentWallet('Debit', $agent_id, $commission_duration, $pay_period, ($wallet_balance_amount * -1), 'Wallet balance applied to debit balance');
          // if ($walletBalance < 0) {
          //   $this->applyToAgentWallet('Debit', $agent_id, $commission_duration, $pay_period, ($wallet_balance_amount * -1), 'Wallet balance applied to debit balance');
          // } else {
          //   $this->applyToAgentWallet('Debit', $agent_id, $commission_duration, $pay_period, ($wallet_balance_amount * -1), 'Wallet balance applied to debit balance');
          // }
        }
        return true;
      }
      return false;
    }

  /*------------------------ Get Commission Export Order Code Start ------------------------*/
    public function getCommOdrId($odrId){
      global $pdo;
      $selOdrCnt = "SELECT MAX(comm_odr_id) as commOdrId FROM commission WHERE order_id=:odrId";
      $resOdrCnt = $pdo->selectOne($selOdrCnt,array(":odrId"=>$odrId));
      $commOdrId = (!empty($resOdrCnt["commOdrId"]) ? $resOdrCnt["commOdrId"]+1 : 0);
      return $commOdrId;
    }
    public function getAdjustCommOdrId($pay_period,$commission_duration){
      global $pdo;
      $selOdrCnt = "SELECT MAX(comm_odr_id) as commOdrId FROM commission 
      WHERE pay_period=:pay_period AND commission_duration=:commission_duration AND type='Adjustment'";
      $resOdrCnt = $pdo->selectOne($selOdrCnt,array(":pay_period"=>$pay_period,":commission_duration" => $commission_duration));
      $commOdrId = (!empty($resOdrCnt["commOdrId"]) ? $resOdrCnt["commOdrId"]+1 : 0);
      return $commOdrId;
    }
  /*------------------------ Get Commission Export Order Code Ends -------------------------*/


}
?>