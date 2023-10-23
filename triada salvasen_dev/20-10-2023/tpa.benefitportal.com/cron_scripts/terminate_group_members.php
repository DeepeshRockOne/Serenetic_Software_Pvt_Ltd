<?php
ini_set('memory_limit', '-1');
ini_set('max_execution_time',0);
include_once dirname(__DIR__) . "/cron_scripts/connect.php";
require_once dirname(__DIR__) . '/includes/policy_setting.class.php';
require_once dirname(__DIR__) . '/includes/member_setting.class.php';
$policySetting = new policySetting();
$memberSetting = new memberSetting();
/**
 * If Groups have terminated status assigned we terminate all active members policies at end of current active coverage period.
 * 
 */
//Select terminated Groups and their members
$selectGroup = $pdo->select("SELECT s.id as group_id,s.rep_id as g_rep_id,c.rep_id as rep_id,c.id as customer_id,ws.end_coverage_period,ws.id as website_id,ws.website_id as policy_id,ws.product_id,ws.plan_id,ws.last_order_id,scs.term_reason,ws.eligibility_date
                            FROM customer c 
                            JOIN customer s ON (c.sponsor_id=s.id) 
                            JOIN customer_settings scs ON(scs.customer_id=s.id)
                            JOIN website_subscriptions ws ON(ws.customer_id=c.id)
                            WHERE s.type='Group' AND ws.status NOT IN('Inactive') AND
                            c.type='customer' AND 
                            s.status='Terminated' AND 
                            c.is_deleted='N'
                            ");

if(!empty($selectGroup)){
    
    foreach($selectGroup as $row){
        $termination_date = date('Y-m-d', strtotime($row['end_coverage_period']));
        $extra_params = array();
        $extra_params['location'] = "terminate_group_members";
        $termination_reason = $row['term_reason'];
        $policySetting->setTerminationDate($row['website_id'],$termination_date,$termination_reason,$extra_params);
        
        //For Activity Feed
        $af_desc = array();
        $af_desc['ac_message'] =array(
            'ac_red_1'=>array(
                'href'=> $ADMIN_HOST.'/groups_details.php?id='.md5($row['group_id']),
                'title'=> $row['g_rep_id'],
            ),
            'ac_message_1' => ' set termination date ',
            'ac_red_2'=>array(
                'href'=> $ADMIN_HOST.'/members_details.php?id='.md5($row['customer_id']),
                'title'=>$row['rep_id'],
            ),
            'ac_message_2' =>' <br/> Plan : '.$row['policy_id'].' <br/> Termination date : '.displayDate($row['end_coverage_period']).' <br/>Termination Reason : '. $row['term_reason'],
        );
        activity_feed(3,$row['group_id'], 'Group',$row['customer_id'], 'customer', 'Group '. ucwords('set termination date'),'','',json_encode($af_desc));
        //For Acivity Feed
    }
}
echo "Completed";
dbConnectionClose();
exit;
?>