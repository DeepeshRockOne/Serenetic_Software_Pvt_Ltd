<?php
ini_set('memory_limit', '-1');
ini_set('max_execution_time', '-1');
include_once dirname(__DIR__) . "/cron_scripts/connect.php";
$cr_sql = "SELECT cr.id,cr.trigger_id,cr.to_phone,cr.to_email,cr.type,cr.sms_params,cr.email_params FROM communication_requests cr WHERE cr.status='Pending' ORDER BY id ASC";
$cr_res = $pdo->select($cr_sql);
if(!empty($cr_res)) {
    $cnt = 0;
    foreach ($cr_res as $cr_row) {
        if(empty($cr_row['to_phone']) && empty($cr_row['to_email'])) {
            continue;
        }

        $log_id = 0;
        if($cr_row['type'] == "SMS") {
            $sms_params = array();
            if(!empty($cr_row['sms_params'])) {
                $sms_params = json_decode($cr_row['sms_params'],true);
            }
            $sms_params['return_log_id'] = 'Y';
            $log_id = trigger_sms($cr_row['trigger_id'],$cr_row['to_phone'], $sms_params);
        }

        if($cr_row['type'] == "Email") {
            $email_params = array();
            if(!empty($cr_row['email_params'])) {
                $email_params = json_decode($cr_row['email_params'],true);
            }
            $email_params['return_log_id'] = 'Y';
            $log_id = trigger_mail($cr_row['trigger_id'],$email_params,$cr_row['to_email'],""); 
        }

        $upd_data = array();
        $upd_data['status'] = "Sent";
        $upd_data['sent_at'] = date("Y-m-d H:i:s");
        $upd_data['log_id'] = $log_id;
        $upd_where = array(
            'clause' => 'id=:id',
            'params' => array(
                ':id' => $cr_row['id'],
            ),
        );
        $pdo->update('communication_requests', $upd_data, $upd_where);
        $cnt++;

        if($cnt >= 5) {
            $cnt=0;
            sleep(1);
        }
    }
}
dbConnectionClose();
?>