<?php
ini_set('memory_limit', '-1');
ini_set('max_execution_time',0);
include_once dirname(__DIR__) . "/cron_scripts/connect.php";

$yesterday = date("Y-m-d",strtotime("-1 days"));

$sql = "SELECT res.service,res.status,res.SmsCount,res.error_code,res.error_message FROM 
(
SELECT sl.service,sl.error_code,'' AS error_message,COUNT(DISTINCT s.id) AS SmsCount,s.status
        FROM sms_log s 
        JOIN sms_log_details sl ON(sl.log_id=s.id) 
        WHERE DATE(s.created_at)= '" . $yesterday . "' AND s.status='Success'
        GROUP BY sl.service
        UNION
        SELECT sl.service,sl.error_code,se.error_message,COUNT(DISTINCT s.id) AS SmsCount,s.status
        FROM sms_log s 
        JOIN sms_log_details sl ON(sl.log_id=s.id) 
        JOIN sms_error_codes se ON(se.code=sl.error_code AND se.service=sl.service)
        WHERE DATE(s.created_at)= '" . $yesterday . "' AND s.status='Fail'
        GROUP BY sl.service,sl.error_code
) AS res
WHERE res.service IS NOT NULL
ORDER BY res.status DESC";
$res = $pdo->select($sql);
// pre_print($res);

$sendEmailSummary = array();
if(!empty($res)){
    foreach($res as $value){
        $sendEmailSummary[] = array(
        'SMS Service' => $value['service'],
        'SMS Count' => $value['SmsCount'],
        'Status' => $value['status'],
        'Error Code' => $value['error_code'],
        'Error Reason' => $value['error_message'],
        );
    }
}

if(!empty($sendEmailSummary)){
    $DEFAULT_SMS_FAIL_EMAIL = array("punit.ladani@serenetic.in","karan.shukla@serenetic.in","dharmesh.nakum@serenetic.in","kamlesh.kanani@serenetic.in");
    trigger_mail_to_email($sendEmailSummary,$DEFAULT_SMS_FAIL_EMAIL, $SITE_NAME ." : SMS Count");
}

echo "Completed";
dbConnectionClose();
?>