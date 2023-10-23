<?php
ini_set('memory_limit', '-1');
ini_set('max_execution_time',0);
require_once dirname(__DIR__) . "/cron_scripts/connect.php";
require_once dirname(__DIR__) . '/includes/enrollment_dates.class.php';
include_once dirname(__DIR__) . '/libs/twilio-php-master/Twilio/autoload.php';
include_once dirname(__DIR__) . '/includes/trigger.class.php';
include_once dirname(__DIR__) . '/includes/member_setting.class.php';

$enrollDate = new enrollmentDate();
$TriggerMailSms = new TriggerMailSms();
$memberSetting = new memberSetting();
$BROWSER = 'System';
$OS = 'System';
$REQ_URL = 'System';
$today_date = date('Y-m-d');

$cur_ws_sql = "SELECT ce.id as ce_id,ws.id 
                FROM customer_enrollment ce 
                JOIN website_subscriptions ws ON(ws.id = ce.website_id)
                WHERE 
                ce.new_plan_id > 0 AND 
                ce.process_status IN('Pending') AND 
                DATE(ce.tier_change_date)=:tier_change_date";
$cur_ws_res = $pdo->select($cur_ws_sql, array(":tier_change_date" => $today_date));
if(!empty($cur_ws_res)) {
    foreach ($cur_ws_res as $cur_ws_row) {

        $new_ws_sql = "SELECT ce.id as ce_id,ws.eligibility_date,ws.next_purchase_date,ws.id,ws.start_coverage_period,ws.next_attempt_at,ws.total_attempts
                        FROM customer_enrollment ce 
                        JOIN website_subscriptions ws ON(ws.id = ce.website_id)
                        WHERE
                        ce.parent_coverage_id=:parent_coverage_id AND 
                        ce.process_status='Pending'";
        $new_ws_row = $pdo->selectOne($new_ws_sql, array(":parent_coverage_id" => $cur_ws_row['ce_id']));

        if(!empty($new_ws_row)) {
            $member_setting = $memberSetting->get_status_by_change_benefit_tier($new_ws_row['eligibility_date'],$today_date);
            /*---- updating in to customer_enrollment for current plan -------*/
            $update_cur_ce_data = array(
                "process_status" => "Active",
            );
            $update_cur_ce_where = array("clause" => "id=:id", "params" => array(":id" => $cur_ws_row['ce_id']));
            $pdo->update("customer_enrollment", $update_cur_ce_data, $update_cur_ce_where);

            $update_cur_ws_data = array(
                "status" => $member_setting['old_policy_status'],
                "updated_at" => "msqlfunc_NOW()",
            );
            $update_cur_ws_where = array("clause" => "id=:id", "params" => array(":id" => $cur_ws_row['id']));
            $pdo->update("website_subscriptions", $update_cur_ws_data, $update_cur_ws_where);

            $term_cd_data = array(
                "status"=>$member_setting['old_dependent_status'],
                "updated_at" => "msqlfunc_NOW()",
            );
            $term_cd_where = array(
                'clause' => "website_id=:website_id AND status != 'Termed'",
                'params' => array(':website_id' => $cur_ws_row['id'])
            );
            $pdo->update("customer_dependent", $term_cd_data, $term_cd_where);

            
            /*---- updating in to customer_enrollment for new plan -------*/
            $update_new_ce_data = array(
                "process_status" => "Active",
            );
            $update_new_ce_where = array("clause"=>"id=:id","params"=>array(":id"=>$new_ws_row['ce_id']));
            $pdo->update("customer_enrollment", $update_new_ce_data, $update_new_ce_where);

            $update_new_ws_data = array(
                "status" => $member_setting['policy_status'],
                "updated_at" => "msqlfunc_NOW()",
            );
            if((strtotime($new_ws_row['next_purchase_date']) <= strtotime($today_date) && $new_ws_row['total_attempts'] == 0)) {
                $update_new_ws_data['total_attempts'] = 0;
                $update_new_ws_data['next_attempt_at'] = NULL;
                $update_new_ws_data['next_purchase_date'] = date('Y-m-d');
            }
            $update_new_ws_where = array("clause" => "id=:id", "params" => array(":id" => $new_ws_row['id']));
            $pdo->update("website_subscriptions", $update_new_ws_data, $update_new_ws_where);
        }
    }
}

/*--------------- Terminate Policy ---------------------*/
$triggerSel = "SELECT t.id
                FROM triggers t
                WHERE t.trigger_action='member_cancellation' AND t.specifically='termination_date' 
                AND t.is_deleted='N' AND t.status='Active' GROUP BY t.id";
$triggerRes = $pdo->selectOne($triggerSel,$triggerParams);

$term_policy_ws_sql = "SELECT 
                            ws.id,
                            ws.termination_date,
                            ws.customer_id, 
                            ws.product_id, 
                            ws.plan_id, 
                            ws.termination_reason, 
                            ws.termination_date
                        FROM website_subscriptions ws WHERE ws.termination_reason IS NOT NULL AND ws.termination_reason != '' AND ws.termination_date IS NOT NULL AND ws.termination_date != '0000-00-00' AND ws.termination_date <= :termination_date AND ws.status NOT IN('Inactive')";

$term_policy_ws_res = $pdo->select($term_policy_ws_sql, array(":termination_date" => $today_date));
if (!empty($term_policy_ws_res)) {
    foreach ($term_policy_ws_res as $ws_row) {

        $member_setting = $memberSetting->get_status_by_term_date("",$ws_row['id'],$ws_row['termination_date']);

        /*------- Change Status Website Subscriptions -------*/
        $ws_upd_data = array(
            "status" => $member_setting['policy_status'],
        );
        $ws_where = array(
            "clause" => "id=:id",
            "params" => array(
                ":id" => $ws_row['id'],
            ),
        );
        $pdo->update("website_subscriptions", $ws_upd_data,$ws_where);

        if(!empty($triggerRes)){
            $TriggerMailSms->trigger_action_mail('member_cancellation',$ws_row['customer_id'],'member','terminationDate',array($ws_row['product_id'] => date("Y-m-d")));
        }

        /*--------- Website Subscriptions History ---------*/
        $web_history_data = array(
            'customer_id' => $ws_row['customer_id'],
            'website_id' => $ws_row['id'],
            'product_id' => $ws_row['product_id'],
            'plan_id' => $ws_row['plan_id'],
            'status' => 'Termed',
            'message' => 'Subscription Plan Terminated',
            'created_at' => 'msqlfunc_NOW()',
        );
        $pdo->insert("website_subscriptions_history", $web_history_data);

        if(!in_array($ws_row['termination_reason'],array('Benefit Amount Change','Benefit Tier Change','Policy Change'))) {
            $update_ce_data = array(
                "process_status" => "Active",
            );
            $update_ce_where = array(
                "clause" => "website_id=:website_id",
                "params" => array(":website_id" => $ws_row['id'])
            );
            $pdo->update("customer_enrollment",$update_ce_data,$update_ce_where);

            $term_cd_data = array(
                "terminationDate" => $ws_row['termination_date'],
                "status"=>$member_setting['policy_status'],
                "updated_at" => "msqlfunc_NOW()",
            );
            $term_cd_where = array(
                'clause' => "website_id=:website_id AND status!='Termed'",
                'params' => array(':website_id' => $ws_row['id'])
            );
            $pdo->update("customer_dependent",$term_cd_data,$term_cd_where);
        }
    }
}
/*---------------/Terminate Policy ---------------------*/

/*--------------- Terminate Dependents ---------------------*/
    $term_dep_ids = array();
    $term_cd_sql = "SELECT cd.id,cd.website_id,cd.terminationDate FROM customer_dependent cd WHERE cd.status NOT IN('Termed','Inactive') AND cd.is_deleted = 'N' AND cd.terminationDate=:termination_date";
    $term_cd_res = $pdo->select($term_cd_sql, array(":termination_date"=> $today_date));
    if (!empty($term_cd_res)) {
        foreach ($term_cd_res as $cd_row) {

            $member_setting = $memberSetting->get_status_by_term_date("",$cd_row['website_id'],$cd_row['terminationDate']);

            $cd_upd_data = array(
                "status" => $member_setting['policy_status'],
                "terminationDate" => "msqlfunc_NOW()",
                "updated_at" => "msqlfunc_NOW()",
            );
            $cd_upd_where = array(
                'clause' => 'id=:id',
                'params' => array(
                    ':id' => $cd_row['id'],
                ),
            );
            $pdo->update("customer_dependent",$cd_upd_data,$cd_upd_where);
        }
    }
/*--------------- /Terminate Dependents ---------------------*/


/*--------------- Inactive Members ---------------------*/
    $cust_sql = "SELECT id,status,rep_id,fname,lname FROM customer WHERE status NOT IN('Inactive','Pending Validation','Post Payment') AND id IN(SELECT ws.customer_id 
        FROM website_subscriptions ws 
        JOIN prd_main pm ON (pm.id=ws.product_id)
        WHERE 1 GROUP BY ws.customer_id HAVING SUM(IF(pm.type !='Fees' AND ws.status != 'Inactive',1,0)) = 0)";
    $cust_res = $pdo->select($cust_sql);
    if (!empty($cust_res)) {
        foreach ($cust_res as $cust_row) {
            $cust_upd_data = array(
                "status" => 'Inactive',
            );
            $cust_upd_where = array(
                'clause' => 'id=:id',
                'params' => array(
                    ':id' => $cust_row['id'],
                ),
            );
            $pdo->update("customer",$cust_upd_data,$cust_upd_where);

            $af_desc = array();
            $af_desc['ac_message'] =array(
                'ac_message_1' =>'System updated Member '.$cust_row['fname'].' '.$cust_row['lname'].' (<span class="text-red">'.$cust_row['rep_id'].'</span>) status from '.$cust_row['status'].' to Inactive<br/>Reason: All Policy Terminated',
            );
            activity_feed(3,$cust_row['id'],'Customer',$cust_row['id'], 'customer', 'Status Updated','','',json_encode($af_desc));
        }
    }
/*---------------/Inactive Members ---------------------*/

/*--------------- Pending Policy ---------------------*/
$pending_policy_ws_sql = "SELECT ws.id,ws.website_id,ws.next_purchase_date
                        FROM website_subscriptions ws 
                        WHERE 
                        ws.parent_ws_id > 0 AND 
                        ws.eligibility_date <= :today_date AND 
                        ws.termination_date IS NULL AND
                        ws.status IN('Pending')";
$pending_policy_ws_res = $pdo->select($pending_policy_ws_sql, array(":today_date" => $today_date));
if (!empty($pending_policy_ws_res)) {
    foreach ($pending_policy_ws_res as $ws_row) {

        $ws_upd_data = array(
            "status" => 'Active',
        );
        if(strtotime($ws_row['next_purchase_date']) <= strtotime(date('Y-m-d'))) {
            $ws_upd_data['total_attempts'] = 0;
            $ws_upd_data['next_attempt_at'] = NULL;
            $ws_upd_data['next_purchase_date'] = date('Y-m-d');
        }
        $ws_where = array(
            "clause" => "id=:id",
            "params" => array(
                ":id" => $ws_row['id'],
            ),
        );
        $pdo->update("website_subscriptions", $ws_upd_data,$ws_where);

        $update_ce_data = array(
            "process_status" => "Active",
        );
        $update_ce_where = array(
            "clause" => "website_id=:website_id",
            "params" => array(":website_id" => $ws_row['id'])
        );
        $pdo->update("customer_enrollment",$update_ce_data,$update_ce_where);

        $cd_data = array(
            "status"=>'Active',
            "updated_at" => "msqlfunc_NOW()",
        );
        $cd_where = array(
            'clause' => "website_id=:website_id AND status!='Termed'",
            'params' => array(':website_id' => $ws_row['id'])
        );
        $pdo->update("customer_dependent",$cd_data,$cd_where);
    }
}
dbConnectionClose();
/*---------------/Pending Policy ---------------------*/