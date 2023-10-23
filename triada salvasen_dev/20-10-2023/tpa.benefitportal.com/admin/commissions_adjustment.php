<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$validate = new Validation();

$pay_period = checkIsset($_REQUEST["pay_period"]);
$commission_duration = checkIsset($_REQUEST["commission_duration"]);
$admin_id = $_SESSION['admin']['id'];

	$resCommDet = array();
	if(!empty($pay_period) && !empty($commission_duration)){
		$selCommDet = "SELECT GROUP_CONCAT(DISTINCT(customer_id)) as agentIds,
					GROUP_CONCAT(DISTINCT(order_id)) as orderIds
					FROM commission 
					WHERE pay_period=:pay_period AND commission_duration=:duration AND status IN('Pending')";
		$paramsComm = array(":pay_period"=>$pay_period,":duration" => $commission_duration);
		$resCommDet = $pdo->selectOne($selCommDet,$paramsComm);
	}else{
		redirect("payment_commissions.php",true);
	}


	if($commission_duration == "weekly"){
		$startPayPeriod=date('m/d/Y', strtotime('-6 days', strtotime($pay_period)));
		$endPayPeriod=date('m/d/Y', strtotime($pay_period));
	}else{
		$startPayPeriod=date('m/01/Y', strtotime($pay_period));
		$endPayPeriod=date('m/d/Y', strtotime($pay_period));
	}

	$agentIds = checkIsset($resCommDet["agentIds"]);
	$orderIds = checkIsset($resCommDet["orderIds"]);

	if(!empty($agentIds)){
		$selAgents = "SELECT c.id,c.rep_id AS agentRepId,
 									IF(cst.company_name != '',cst.company_name,CONCAT(c.fname,' ',c.lname)) AS agentName
									FROM customer AS c JOIN customer_settings AS cst ON (c.id = cst.customer_id)
									WHERE c.id IN(".$agentIds.") ORDER BY agentName ASC";
		$resAgents = $pdo->select($selAgents);
	}

$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache);
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache);

$template = "commissions_adjustment.inc.php";
include_once 'layout/iframe.layout.php';
?>