<?php
include_once __DIR__ . '/layout/start.inc.php';

$processor_id = $_GET['id'];
$processor_res = $pdo->selectOne("SELECT name,is_ach_accepted,is_cc_accepted,monthly_threshold_sale FROM payment_master WHERE md5(id) = :id",array(":id" => $processor_id));

$payment_type = '';
if(($processor_res['is_ach_accepted'] == 'Y') && ($processor_res['is_cc_accepted'] == 'Y')){
	$payment_type = 'ACH & CC';
} else {
	if(($processor_res['is_ach_accepted'] == 'Y') || ($processor_res['is_cc_accepted'] == 'Y')){
		if($processor_res['is_ach_accepted'] == 'Y'){
    		$payment_type = 'ACH';
  		}
  		if($processor_res['is_cc_accepted'] == 'Y'){
    		$payment_type = 'CC';
  		}
	}
}

function get_total_transaction($type,$payment_master_id,$monthly_threshold_sale,$date_range = array()){

	global $pdo;

	$incr = '';
	$sch_params = array();

	if(!empty($payment_master_id)){
		$incr .= " AND md5(payment_master_id) = :payment_master_id";
		$sch_params[':payment_master_id'] = $payment_master_id;
	}

	if(!empty($type)){
		if($type == 'All'){
			$incr .= " AND transaction_type IN ('New Order','Renewal Order')";
		} else if ($type == 'New Business'){
			$incr .= " AND transaction_type IN ('New Order')";
		} else if ($type == 'Renewals'){
			$incr .= " AND transaction_type IN ('Renewal Order')";
		} else if ($type == 'Chargebacks'){
			$incr .= " AND transaction_type IN ('Chargeback')";
		} else if ($type == 'Refunds'){
			// $incr .= " AND transaction_type IN ('Payment Returned','Void Order','Refund Order')";
			$incr .= " AND transaction_type IN ('Refund Order')";
		}
	}

	if(!empty($date_range['from_date'])){
		$incr .= " AND date(created_at) >= :from_date";
		$sch_params[':from_date'] = date("Y-m-d",strtotime($date_range['from_date']));	
	}

	if(!empty($date_range['to_date'])){
		$incr .= " AND date(created_at) <= :to_date";
		$sch_params[':to_date'] = date("Y-m-d",strtotime($date_range['to_date']));	
	}

	$monthly_order_total_sql = "SELECT count(order_id) as total_count, sum(credit) as total_credit, sum(debit) as total_debit 
		FROM transactions 
		WHERE id > 0 " .  $incr ;
    $monthly_order_total = $pdo->selectOne($monthly_order_total_sql, $sch_params);

    $total_count = 0;
    $total_amount = 0;
    $parcentage = 0;
    $lable_class = '';
    if(!empty($monthly_order_total)){
      $total_count = $total_count + $monthly_order_total['total_count'];
      $total_amount = $total_amount + $monthly_order_total['total_credit'] - $monthly_order_total['total_debit'];
    }

    if($total_count < 0){
      $total_count = abs($total_count);
    }

    if($total_amount < 0){
      $total_amount = abs($total_amount);
      $lable_class = 'text-red';
    }

    if(!empty($total_amount) && !empty($monthly_threshold_sale) && ($total_amount > 0) && ($monthly_threshold_sale > 0)){
    	$parcentage = ($total_amount * 100) / $monthly_threshold_sale;
    }

    $return_array = array(
    	'total_count' => $total_count,
    	'total_amount' => $total_amount,
    	'lable_class' => $lable_class,
    	'parcentage' => displaypercentage($parcentage,2)
    );

   	return $return_array;
}

$template = 'transaction_popup.inc.php';
include_once 'layout/iframe.layout.php';
include_once 'tmpl/' . $template;
?>