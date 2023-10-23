<?php
ini_set('memory_limit', '-1');
ini_set('max_execution_time',0);
require_once dirname(__DIR__) . "/cron_scripts/connect.php";
include_once dirname(__DIR__) . '/includes/trigger.class.php';
$TriggerMailSms = new TriggerMailSms();

$today_date = date('Y-m-d');

// check trigger action to send trigger
$triggerSel = "SELECT t.id
                FROM triggers t
                WHERE t.trigger_action='member_enrollment' AND t.specifically='effective_date' 
                AND t.is_deleted='N' AND t.status='Active' GROUP BY t.id";
$triggerRes = $pdo->selectOne($triggerSel,$triggerParams);

if(!empty($triggerRes)){

    $wsSql = "SELECT ws.customer_id,ws.product_id,ws.eligibility_date
                    FROM website_subscriptions ws
                    WHERE ws.status='Active' AND DATE(ws.eligibility_date)=:eligibility_date";
    $wsRes = $pdo->select($wsSql, array(":eligibility_date" => $today_date));

    if(!empty($wsRes)) {
        foreach ($wsRes as $wsRow) {
            $products = array($wsRow["product_id"]=>date("Y-m-d"));
            $TriggerMailSms->trigger_action_mail('member_enrollment',$wsRow["customer_id"],'member','effectiveDate',$products);
        }
    }
}
echo "completed";
dbConnectionClose();
