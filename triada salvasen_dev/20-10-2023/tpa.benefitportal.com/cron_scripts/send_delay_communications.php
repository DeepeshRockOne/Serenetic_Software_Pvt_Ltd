<?php
ini_set('memory_limit', '-1');
ini_set('max_execution_time', '-1');
include_once dirname(__DIR__) . "/cron_scripts/connect.php";
include_once dirname(__DIR__) . '/includes/trigger.class.php';
$TriggerMailSms = new TriggerMailSms();
error_reporting(E_ALL);
$cr_sql = "SELECT * FROM communication_delay_requests WHERE (schedule_date >= (NOW() - INTERVAL 1 HOUR) AND schedule_date <= NOW()) AND status='Pending' AND is_sent = 'N' ORDER BY id ASC";
$cr_res = $pdo->select($cr_sql);
if(!empty($cr_res)) {
    $cnt = 0;
    foreach ($cr_res as $cr_row) {
        
        $action = $cr_row['action'];
        $user_id = $cr_row['user_id'];
        $user_type = $cr_row['user_type'];
        $specific = $cr_row['specifically'];
        $products = json_decode($cr_row['products'],true);
        $extra=json_decode($cr_row['extra'],true);
        $extra['is_cron'] = 'Y';
        $extra['request_date'] = date('Y-m-d',strtotime($cr_row['created_at']));

        $TriggerMailSms->trigger_action_mail($action,$user_id,$user_type,$specific,$products,$extra);

        $upd_data = array();
        $upd_data['status'] = "Sent";
        $upd_data['sent_at'] = 'msqlfunc_NOW()';
        $upd_data['is_sent'] = 'Y';
        $upd_where = array(
            'clause' => 'id=:id',
            'params' => array(
                ':id' => $cr_row['id'],
            ),
        );
        $pdo->update('communication_delay_requests', $upd_data, $upd_where);
        $cnt++;

        if($cnt >= 5) {
            $cnt=0;
            sleep(1);
        }
    }
}
dbConnectionClose();
?>