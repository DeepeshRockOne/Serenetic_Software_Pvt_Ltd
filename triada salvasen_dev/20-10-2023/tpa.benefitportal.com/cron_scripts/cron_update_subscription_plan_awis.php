<?php
include_once dirname(__DIR__) . "/cron_scripts/connect.php";
require_once dirname(__DIR__) . '/includes/authorize_payment.php';
require_once dirname(__DIR__) . '/includes/enrollment_dates.class.php';
$enrollDate = new enrollmentDate();
$BROWSER = 'System';
$OS = 'System';
$REQ_URL = 'System';

$today_date = date('Y-m-d');
$UpdatedSubscriptionPlans = array();

/*--------------- Update Subscription Plans ---------------------*/
$cur_ce_sql = "SELECT ce.* FROM customer_enrollment ce
           WHERE ce.customer_id > 0 AND ce.new_plan_id > 0 AND ce.process_status IN('Pending') AND DATE(ce.tier_change_date)=:tier_change_date";
$cur_ce_result = $Rpdo->select($cur_ce_sql, array(":tier_change_date" => $today_date));

if (!empty($cur_ce_result)) {
    foreach ($cur_ce_result as $cur_ce_row) {
        
        $new_ce_sql = "SELECT * FROM customer_enrollment WHERE plan_id=:plan_id AND parent_coverage_id=:parent_coverage_id AND plan_status='Pending' AND process_status='Pending'";
        $new_ce_row = $Rpdo->selectOne($new_ce_sql, array(":plan_id" => $cur_ce_row['new_plan_id'], ":parent_coverage_id" => $cur_ce_row['id']));

        if (!empty($new_ce_row)) {

            $old_ws_sql = "SELECT ws.*,pm.plan_type,p.title as plan_type_title FROM website_subscriptions ws
                            JOIN prd_matrix pm ON (pm.id=ws.plan_id)
                            JOIN prd_plan_type p ON (p.id=pm.plan_type)
                            WHERE ws.id=:id";
            $old_ws_row = $Rpdo->selectOne($old_ws_sql, array(":id" => $cur_ce_row['subscription_id']));

            $new_ws_sql = "SELECT ws.*,pm.plan_type,p.title as plan_type_title FROM website_subscriptions ws
                            JOIN prd_matrix pm ON (pm.id=ws.plan_id)
                            JOIN prd_plan_type p ON (p.id=pm.plan_type)
                            WHERE ws.id=:id";
            $new_ws_row = $Rpdo->selectOne($new_ws_sql, array(":id" => $new_ce_row['subscription_id']));         
           

            /*---- updating in to customer_enrollment for new plan -------*/
            $update_new_ce_data = array(
                "plan_status" => "Active",
                "process_status" => "Active",
                "updated_at" => "msqlfunc_NOW()",
            );
            $update_new_ce_where = array("clause" => "subscription_id=:subscription_id", "params" => array(":subscription_id" => $new_ce_row['subscription_id']));
            $pdo->update("customer_enrollment", $update_new_ce_data, $update_new_ce_where);

            /*---- updating in to customer_enrollment for current plan -------*/
            $update_cur_ce_data = array(
                "process_status" => "Active",
                "plan_status" => "Terminated",
                "updated_at" => "msqlfunc_NOW()",
            );
            $update_cur_ce_where = array("clause" => "subscription_id=:subscription_id", "params" => array(":subscription_id" => $cur_ce_row['subscription_id']));
            $pdo->update("customer_enrollment", $update_cur_ce_data, $update_cur_ce_where);

            /*---- updating status of current website subscription if backdating only ---*/
            $update_cur_ws_data = array(
                "status" => 'Inactive Member Request',
                "updated_at" => "msqlfunc_NOW()",
            );
            $update_cur_ws_where = array("clause" => "id=:id", "params" => array(":id" => $cur_ce_row['subscription_id']));
            $pdo->update("website_subscriptions", $update_cur_ws_data, $update_cur_ws_where);

            /*---- updating status of new website subscription if backdating only ---*/
            $website_res = $pdo->select("SELECT id,status FROM website_subscriptions WHERE id = :id",array(":id" => $new_ce_row['subscription_id']));

            if(!empty($website_res) && count($website_res)){
                foreach ($website_res as $web_key => $web_value) {
                    if($web_value['status'] == 'Pending'){
                        $update_new_ws_data = array(
                            "status" => 'Active',
                            "updated_at" => "msqlfunc_NOW()",
                        );
                        $update_new_ws_where = array("clause" => "id=:id", "params" => array(":id" => $web_value['id']));
                        $pdo->update("website_subscriptions", $update_new_ws_data, $update_new_ws_where);
                    }
                }
            }

            //term dependent for this products start
            $get_customer_enrollment = $pdo->select("SELECT id,termination_date FROM customer_enrollment WHERE subscription_id=:subscription_id",array(":subscription_id"=>$cur_ce_row['subscription_id']));
            foreach($get_customer_enrollment as $ci) {
                $term_cd_data = array(
                    "terminationDate" => $ci['termination_date'],
                    "status"=>"Termed",
                    "updated_at" => "msqlfunc_NOW()",
                );
                $term_cd_where = array(
                    'clause' => "cust_enrollment_id=:cust_enrollment_id and status!='Termed'",
                    'params' => array(':cust_enrollment_id' => $ci['id'])
                );
                $pdo->update("customer_dependent", $term_cd_data, $term_cd_where);
            }
            //term dependent for this products end
            $UpdatedSubscriptionPlans[] = $cur_ce_row;
        }
    }
}
/*--------------- Update Subscription Plans ---------------------*/


$TerminatedSubscriptionPlans = array();
/*--------------- Terminate Subscription Plans ---------------------*/
$terminate_plan_ws_sql = "SELECT ws.* FROM website_subscriptions ws WHERE ws.termination_reason IS NOT NULL AND ws.termination_reason != '' AND ws.termination_date IS NOT NULL AND ws.termination_date != '0000-00-00' AND ws.termination_date < :termination_date AND ws.status NOT IN('Inactive Member Request','Inactive Failed Billing','Inactive','Terminated')";

$term_policy_ws_res = $Rpdo->select($terminate_plan_ws_sql, array(":termination_date" => $today_date));
$terminated_member_ids = array();
if (!empty($term_policy_ws_res)) {
    foreach ($term_policy_ws_res as $ws_row) {
        /*------- Change Status Website Subscriptions -------*/
        $ws_upd_data = array(
            "status" => 'Inactive Member Request',
        );
        $ws_where = array(
            "clause" => "id=:id",
            "params" => array(
                ":id" => $ws_row['id'],
            ),
        );
        $pdo->update("website_subscriptions", $ws_upd_data,$ws_where);

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

        $TerminatedSubscriptionPlans[] = $ce_row;
    }
}
if(!empty($terminated_member_ids)) {
    foreach ($terminated_member_ids as $key => $member_id) {
        
        if($member_id > 0) {
            send_policy_cancellation_mail_to_sponsor($member_id);
        }
    }
}
/*--------------- Terminate Subscription Plans ---------------------*/

$TerminatedDependents = array();
/*--------------- Terminate Dependents ---------------------*/
$terminate_dependents_cd_sql = "SELECT cd.* FROM customer_dependent cd WHERE cd.status != 'Termed' AND cd.is_deleted = 'N' AND cd.terminationDate =:termination_date";
$terminate_dependents_cd_result = $Rpdo->select($terminate_dependents_cd_sql, array(":termination_date" => $today_date));

if (!empty($terminate_dependents_cd_result)) {
    foreach ($terminate_dependents_cd_result as $cd_row) {
        /*------- Term Dependent -------*/
        $term_customer_dependent_data = array(
            "status" => "Termed",
            "terminationDate" => "msqlfunc_NOW()",
            "updated_at" => "msqlfunc_NOW()",
        );
        $term_customer_dependent_where = array(
            'clause' => 'id=:id',
            'params' => array(
                ':id' => $cd_row['id'],
            ),
        );
        $pdo->update("customer_dependent", $term_customer_dependent_data, $term_customer_dependent_where);
        /*-------/Term Dependent -------*/
        $TerminatedDependents[] = $cd_row;
    }
}
/*--------------- Terminate Dependents ---------------------*/

/*------------*/
$updated_ce_ids = array();
$ce_res = $Rpdo->select("SELECT ce.*,ws.product_id as ws_product_id,c.sponsor_id as sponsor_id,s.type as sponsor_type,ws.id as website_sub_id FROM  customer_enrollment ce 
                        JOIN website_subscriptions ws ON(ws.id = ce.subscription_id)
                        JOIN customer c ON(c.id = ce.customer_id)
                        JOIN customer s ON(s.id = c.sponsor_id)
                        WHERE ce.plan_status NOT IN('Terminated','Cancelled Update') AND ws.status IN ('Inactive Member Request','Inactive Failed Billing')");
if(!empty($ce_res)) {
    foreach ($ce_res as $key => $ce_row) {
        $termination_date=$enrollDate->getTerminationDate($ce_row['website_sub_id']);
        $upd_ce_data = array(
            "plan_status" => "Terminated", 
            "termination_date" => date('Y-m-d', strtotime($termination_date)),
            "term_date_set" => date('Y-m-d')
        );
        $upd_ce_where = array(
            "clause" => "id=:id",
            "params" => array(":id" => $ce_row['id']),
        );
        $pdo->update("customer_enrollment", $upd_ce_data, $upd_ce_where);

        $updated_ce_ids[] = $ce_row['id'];
    }
}
/*------------*/
$sendEmailSummary = array(
    '$UpdatedSubscriptionPlans' => $UpdatedSubscriptionPlans,
    '$TerminatedSubscriptionPlans' => $TerminatedSubscriptionPlans,
    '$TerminatedDependents' => $TerminatedDependents,
    '$updated_ce_ids' => $updated_ce_ids,
);
echo "<pre>";
$sendEmailSummary = "<pre>" . print_r($sendEmailSummary, true);
echo $sendEmailSummary;
//die();
$DEFAULT_EMAIL = array("dharmesh@cyberxllc.com");
trigger_mail_to_email($sendEmailSummary, $DEFAULT_EMAIL, "AWIS : Update Subscription Plans", $admin_trigger_param, 2);
dbConnectionClose();
?>