<?php
	include_once 'layout/start.inc.php';
	$agent_id = checkIsset($_REQUEST["agent_id"]);
	$pay_period = checkIsset($_REQUEST["pay_period"]);
	$commission_duration = checkIsset($_REQUEST["commission_duration"]);

	if(empty($agent_id)){
		echo "<script>parent.setNotifyError('Please select Agent')";
		exit;
	}

	$resOdr = array();

	$sqlOdr = "SELECT o.id as odrId,o.display_id as odrDispId
					FROM commission cs
					JOIN orders o ON(cs.order_id=o.id)
					WHERE cs.pay_period=:pay_period AND cs.commission_duration=:duration 
					AND cs.status IN('Pending') AND cs.customer_id=:agent_id GROUP BY o.id";
	$paramsOdr = array(":pay_period"=>$pay_period,":duration" => $commission_duration,":agent_id" => $agent_id);
	$resOdr = $pdo->select($sqlOdr,$paramsOdr);

    $html = "";
    if(!empty($resOdr)){
    	foreach ($resOdr as $order) {
    		$html .= "<option value=".$order['odrId'].">".$order["odrDispId"]."</option>";
    	}
    }
	echo $html;
	dbConnectionClose();
	exit;
?>
