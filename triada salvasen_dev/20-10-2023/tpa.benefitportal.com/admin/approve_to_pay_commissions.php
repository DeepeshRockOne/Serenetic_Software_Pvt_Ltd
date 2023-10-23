<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$incr = '';
$sch_params = array();

$type = checkIsset($_REQUEST['type']);
$selType = checkIsset($_REQUEST['selType']); // single agent or multiple agent
$commission_duration = checkIsset($_REQUEST['commission_duration']);
$pay_period = checkIsset($_REQUEST['pay_period']);
$agentIds = $_REQUEST['agentIds'];

if(!empty($commission_duration)){
  $incr .= " AND cb.commission_duration=:commission_duration";
  $sch_params[":commission_duration"] = $commission_duration;
}

if(!empty($pay_period)){
  $incr .= " AND cb.pay_period=:payPeriod";
  $sch_params[":payPeriod"] = $pay_period;
}

$selAgentBalance = "SELECT  a.id AS agentId,a.rep_id AS agentDispId,CONCAT(a.fname,' ',a.lname) AS agentName,
                    cb.credit as earnedCredit,cb.pmpm_credit as pmpmCredit,db.balance as debitBalance
                  FROM commission_credit_balance cb
                  JOIN customer a ON(cb.agent_id=a.id)
                  LEFT JOIN commission_debit_balance db ON(cb.agent_id=db.agent_id)
                  WHERE cb.status='Open' AND cb.agent_id IN ($agentIds)" . $incr . 
                  " GROUP BY a.id ORDER BY a.id";
$resAgentBalance = $pdo->select($selAgentBalance,$sch_params);


$prdIncr = '';
$prdParams = array();
$customCommArr = array();

if($selType == "singleAgent"){

  if(!empty($pay_period)){
    $prdIncr .= " AND cm.pay_period=:payPeriod";
    $prdParams[":payPeriod"] = $pay_period;
  }

  if(!empty($commission_duration)){
    $prdIncr .= " AND cm.commission_duration=:commission_duration";
    $prdParams[":commission_duration"] = $commission_duration;
  }

  $getCommPrdSql = "SELECT 
                    p.id as prdId,
                    p.name as prdName,
                    SUM(if(cm.is_pmpm_comm='N',cm.amount,0)) as earnedPrdCredit,
                    SUM(if(cm.is_pmpm_comm='Y',cm.amount,0)) as pmpmPrdCredit,cm.type
                  FROM commission cm
                  LEFT JOIN prd_main p ON(cm.product_id=p.id)
                  WHERE cm.type!='Adjustment' AND cm.customer_id IN ($agentIds) AND cm.status='Pending' AND cm.balance_type IN('addCredit','revCredit') " . $prdIncr . " GROUP BY cm.product_id 
                  HAVING SUM(cm.amount) != 0";
  $resGetCommPrd = $pdo->select($getCommPrdSql,$prdParams);


  $sqlAdjustComm = "SELECT 
                    CONCAT('C',cm.id) AS prdId,
                    SUBSTRING(cm.note,1,15) as prdName,
                    SUM(if(cm.is_pmpm_comm='N',cm.amount,0)) as earnedPrdCredit,
                    SUM(if(cm.is_pmpm_comm='Y',cm.amount,0)) as pmpmPrdCredit,cm.type
                  FROM commission cm
                  WHERE cm.type='Adjustment' AND cm.customer_id IN ($agentIds) AND cm.status='Pending' AND cm.balance_type IN('addCredit','revCredit') " . $prdIncr . " GROUP BY cm.id";
  $resAdjustComm = $pdo->select($sqlAdjustComm,$prdParams);

  $customCommArr = array_merge($resGetCommPrd,$resAdjustComm);

}


$template = "approve_to_pay_commissions.inc.php";
include_once 'layout/iframe.layout.php';
?>