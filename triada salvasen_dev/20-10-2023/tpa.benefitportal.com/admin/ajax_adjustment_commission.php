<?php  
  include_once dirname(__FILE__) . '/layout/start.inc.php';
  include_once dirname(__DIR__) . "/includes/commission.class.php";

  $commObj = new Commission();
  $validate = new Validation();
  $res = array();
  $res['status'] = 'fail';

  $commission_type = 'Adjustment';

  $commission_duration = checkIsset($_REQUEST["commission_duration"]);
  $pay_period = checkIsset($_REQUEST["pay_period"]);
  
  $agent_id = checkIsset($_REQUEST["agent_id"]);
  $adjustment_type = checkIsset($_REQUEST["adjustment_type"]);
  $orderIds = checkIsset($_REQUEST["orderIds"],'arr');

  $transaction_type = checkIsset($_REQUEST["transaction_type"]);
  $amount = checkIsset($_REQUEST["amount"]);
  $note = checkIsset($_REQUEST["note"]);


  $validate->string(array('required' => true, 'field' => 'agent_id', 'value' => $agent_id), array('required' => 'Agent ID is required'));
  $validate->string(array('required' => true, 'field' => 'adjustment_type', 'value' => $adjustment_type), array('required' => 'Adjustment Type is required'));

  if(!empty($adjustment_type) && $adjustment_type=="orderSpec"){
    if(empty($orderIds)){
      $validate->setError("orderIds","Please Select Orders");
    }
  }
  $validate->string(array('required' => true, 'field' => 'transaction_type', 'value' => $transaction_type), array('required' => 'Transaction Type is required'));

  $validate->string(array('required' => true, 'field' => 'amount', 'value' => $amount), array('required' => 'Amount is required', 'invalid' => 'Only Digit allow'));
  $validate->string(array('required' => true, 'field' => 'note', 'value' => $note), array('required' => 'Note is required'));
  $validate->string(array('required' => true, 'field' => 'agent_id', 'value' => $pay_period), array('required' => 'Pay period required'));

  if ($validate->isValid()) {
    $status = 'Pending';
    $amount = round($amount,2);
    
    
    if($transaction_type == "Debit"){
      $amount = $amount > 0 ? ($amount * -1) : $amount;
      $balance_type = "revCredit";
    }else{
      $balance_type = "addCredit";
    }

    if($adjustment_type=="orderSpec" && !empty($orderIds)){
      foreach ($orderIds as $order) {
        $commGenCount = $commObj->getAdjustCommOdrId($pay_period,$commission_duration);
        $commGenCount = !empty($commGenCount) ? $commGenCount : 1;

        $insCommParams = array(
          'commission_duration' => makeSafe($commission_duration),
          'balance_type' => makeSafe($balance_type),
          'order_id' => makeSafe($order),
          'customer_id' => makeSafe($agent_id),
          'type' => $commission_type,
          'amount' => $amount,
          'status' => $status,
          'pay_period' => date('Y-m-d', strtotime($pay_period)),
          'admin_id' => $_SESSION['admin']['id'],
          'note' => $note,
          'created_at' => date('Y-m-d', strtotime($pay_period)).' '.date('H:i:s'),
          'comm_odr_id' => $commGenCount
        );
        $commId = $pdo->insert("commission", $insCommParams);

          if($transaction_type == "Debit"){
            
            $extra_params = array();
            $extra_params['transaction_type'] = $balance_type.'Adjustment';
            $message = 'Adjustment';
            $commObj->agentCommissionBalance($balance_type,$commission_duration,$agent_id,$pay_period,$amount,$commId,$message,$extra_params);
          }else{
            $extra_params = array();
            $extra_params['transaction_type'] = $balance_type.'Adjustment';
            $message = 'Adjustment';
            $commObj->agentCommissionBalance($balance_type,$commission_duration,$agent_id,$pay_period,$amount,$commId,$message,$extra_params);
          }
      }
    }else{

      $commGenCount = $commObj->getAdjustCommOdrId($pay_period,$commission_duration);
      $commGenCount = !empty($commGenCount) ? $commGenCount : 1;

      $insCommParams = array(
        'commission_duration' => makeSafe($commission_duration),
        'balance_type' => makeSafe($balance_type),
        'customer_id' => makeSafe($agent_id),
        'type' => $commission_type,
        'amount' => $amount,
        'status' => $status,
        'pay_period' => date('Y-m-d', strtotime($pay_period)),
        'admin_id' => $_SESSION['admin']['id'],
        'note' => $note,
        'created_at' => date('Y-m-d', strtotime($pay_period)).' '.date('H:i:s'),
        'comm_odr_id' => $commGenCount
      );
      $commId = $pdo->insert("commission", $insCommParams);
        if($transaction_type == "Debit"){
          $extra_params = array();
          $extra_params['transaction_type'] = $balance_type.'Adjustment';
          $message = 'Adjustment';
          $commObj->agentCommissionBalance($balance_type,$commission_duration,$agent_id,$pay_period,$amount,$commId,$message,$extra_params);
        }else{
          $extra_params = array();
          $extra_params['transaction_type'] = $balance_type.'Adjustment';
          $message = 'Adjustment';
          $commObj->agentCommissionBalance($balance_type,$commission_duration,$agent_id,$pay_period,$amount,$commId,$message,$extra_params);
        }
    }

    //************* Activity Code Start *************
      $resAgent = $pdo->selectOne("SELECT id,CONCAT(fname,' ',lname) as name,rep_id from customer WHERE id=:id",array(":id"=>$agent_id));
      $description['ac_message'] =array(
        'ac_red_1'=>array(
          'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
          'title'=>$_SESSION['admin']['display_id'],
        ),
        'ac_message_1' =>'  created commission adjustment in '.date('m/d/Y', strtotime($pay_period)).' for '.$resAgent['rep_id'].' : '.$transaction_type.' - '. displayAmount($amount,2).' - '.$note,
      );
      activity_feed(3, $_SESSION['admin']['id'], 'Admin',$agent_id, 'Agent',"Created Commissions Adjustment", $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
    //************* Activity Code End *************
    $res["status"] = "success";
  }else{
    $res["errors"] = $validate->getErrors();
    $res["status"] = "fail";
  }

header('Content-Type: application/json');
echo json_encode($res);
dbConnectionClose();
exit;
?>