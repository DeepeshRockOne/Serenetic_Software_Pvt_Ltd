<?php

include_once dirname(__DIR__) . "/cron_scripts/connect.php";
include_once dirname(__DIR__) . "/includes/upload_paths.php";
require_once dirname(__DIR__) . '/libs/PHPMailer/PHPMailerAutoload.php';

$sqlTriggers = "SELECT * FROM triggers WHERE user_group = 'member' AND trigger_action = 'renewal_payment' AND status = 'Active' AND is_deleted = 'N'";
$resTriggers = $pdo->select($sqlTriggers);

if(!empty($resTriggers)){
	foreach ($resTriggers as $key => $value) {
		$today = date('Y-m-d');
		$days_prior = $value['days_prior'];
		$today_date = date('Y-m-d',strtotime("$today +$days_prior day"));
		$effective_date = date('Y-m-d',strtotime($value['effective_date']));
		
		$memberSql = "SELECT c.id,w.plan_id,w.id as wid,w.renew_count,c.sponsor_id,w.last_order_id,o.subscription_ids,o.id as orderId,c.sponsor_id,o.grand_total,o.is_renewal,o.original_order_date,c.upline_sponsors,c.level,w.issued_state,c.email,c.cell_phone,w.next_purchase_date,c.zip as zip_code
					  FROM website_subscriptions w
					  JOIN customer c ON (c.id=w.customer_id)
					  JOIN transactions t ON(t.customer_id=w.customer_id AND t.transaction_type='New Order')
					  JOIN orders o ON(o.id=t.order_id AND DATE(o.created_at) >= :effective_date)
					  WHERE c.status IN ('Active') AND c.type='Customer' AND
					  (
					    (DATE(w.next_purchase_date) = :today_date AND w.total_attempts=0)
					  )
					  AND w.status in('Active') AND w.is_onetime='N' GROUP BY c.id";
  		$memberRes = $pdo->select($memberSql,[':today_date' => $today_date,':effective_date' => $effective_date]);

		if(!empty($memberRes)){
			foreach ($memberRes as $k => $autorow) {
				$renewalDetails = getRenewalDetails($autorow['id'],$autorow);

				if(!$renewalDetails){
					continue;
				}

				$mail_data = array();
		        
		        $smart_tags = get_user_smart_tags($autorow['id'],'member');
		        if($smart_tags){
		        	if(!empty($renewalDetails) && !empty($renewalDetails['next_purchase_date'])){
		        		$smart_tags['NextBillingDate'] = date('m/d/Y',strtotime($renewalDetails['next_purchase_date']));
		        	}
		        	if(!empty($renewalDetails) && !empty($renewalDetails['grand_total'])){
		        		$smart_tags['NextBillAmount'] = displayAmount($renewalDetails['grand_total'],2);
		        	}
		            $mail_data = array_merge($mail_data,$smart_tags);
		        }

		        if(!empty($value['cc_email_specific'])) {
		        	$mail_data['EMAILER_SETTING']['cc_email'] = $value['cc_email_specific'];
		        }

		        if(!empty($value['bcc_email_specific'])) {
		        	$mail_data['EMAILER_SETTING']['bcc_email'] = $value['bcc_email_specific'];
		        }

        		if($value ['type'] == 'Email' || $value['type'] == 'Both'){
        			trigger_mail($value['id'],$mail_data,$autorow['email']);
	    		}

	    		if($value['type'] == 'SMS' || $value['type'] == 'Both'){
	    			trigger_sms($value['id'],$autorow['cell_phone'],$mail_data,true);
	    		}
			}
		}
	}
}

echo "<br>Completed";
dbConnectionClose();
exit;
?>