<?php
ini_set('memory_limit', '-1');
ini_set('max_execution_time', '-1');
include_once dirname(__DIR__) . "/cron_scripts/connect.php";
include_once dirname(__DIR__) . "/includes/policy_setting.class.php";
include_once dirname(__DIR__) . '/includes/function.class.php';

$policySetting = new policySetting();
$function_list = new functionsList();


/*
Script to check if member with active status does not have successful payment for current coverage period and coverage period is 45 days in past. If found then system need to automatically terminate all products based on failed payment and create eticket for failed payment.
*/

	$selPolicy = "SELECT c.rep_id,ws.product_code,ws.id,ws.customer_id,ws.eligibility_date,ws.start_coverage_period,ws.end_coverage_period,ws.status,ws.next_purchase_date,ws.next_attempt_at,ws.total_attempts,c.email,c.type,c.fname,c.lname,c.cell_phone,p.name AS product_name,ws.website_id,wsh.message 
		FROM website_subscriptions ws
        JOIN customer c ON(c.is_deleted='N' AND c.type='Customer' AND c.status='Active' AND ws.customer_id=c.id)
        JOIN customer s ON (c.sponsor_id=s.id AND s.type='Agent')
        JOIN prd_main p ON (p.is_deleted='N' AND ws.product_id=p.id)
        LEFT JOIN website_subscriptions_history wsh ON wsh.id = (SELECT MAX(id) AS id FROM website_subscriptions_history WHERE status='fail' AND website_id = ws.id)
        WHERE (CURDATE() NOT BETWEEN ws.start_coverage_period AND ws.end_coverage_period) 
        	AND ws.eligibility_date <= CURDATE() AND ws.start_coverage_period <= CURDATE() 
        	AND ws.end_coverage_period <= DATE_SUB(CURDATE(), INTERVAL 45 DAY)
      		AND ws.status='Active' AND ws.product_type!='Fees' AND ws.termination_date IS NULL 
      		GROUP BY ws.id ORDER BY c.id DESC";
	$resPolicy = $pdo->select($selPolicy);
	
	if(!empty($resPolicy)){
		foreach($resPolicy as $policy){
			$extra_params = array();
			$extra_params['location'] = "monthly_subscription_order";
			$extra_params['cancel_post_payment_order'] = true;
			$termination_reason = "Failed Billing";
			$policySetting->setTerminationDate($policy['id'],'',$termination_reason,$extra_params);

			//Generating e-tickets
			$tkt_customer_id = $policy['customer_id'];
			$customer_email = $policy['email'];
			$tkt_customer_type = $policy['type'];
			$failed_billing_reason = !empty($policy['message']) ? $policy['message'] : 'Failed Payment';

			$message = "<h4>Failed Payment</h4><br>";
			$message .= "<p>Name of Member : " . $policy['fname']. " " . $policy['lname'] . "</p><br>";
			$message .= "<p>Member ID : " . $policy['rep_id'] . "</p><br>";
			$message .= "<p>Product Name : " . $policy['product_name'] . "</p><br>";
			$message .= "<p>Email : " . $customer_email . "</p><br>";
			$message .= "<p>Phone : " . format_telephone($policy['cell_phone']) . "</p><br>";
			$message .= "<p>Failed Billing Reason : " . $failed_billing_reason . "</p></br>";
			$sessionArr = array('System'=>'System');
			
			$function_list->createNewTicket($sessionArr,4,"Failed Payment",0,$message,$tkt_customer_id,$tkt_customer_type,'',array(),'notes',$policy['id']);

			$descriptions['ac_message'] = array(
				'ac_red_1'=>array(
					'href'=>$ADMIN_HOST.'/members_details.php?id='.md5($tkt_customer_id),
					'title'=>$policy['rep_id'],
				),
				'ac_message_1'=> 'E-Ticket Opened For Policy ' .$policy['website_id'] 
			);

			activity_feed(3, $tkt_customer_id, $tkt_customer_type, $tkt_customer_id, $tkt_customer_type, 'E-Ticket Opened', $policy['fname'], $policy['lname'], json_encode($descriptions));

		}
	}

echo "Completed";        
dbConnectionClose();
?>