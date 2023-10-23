<?php
include_once dirname(__DIR__) . "/cron_scripts/connect.php";
include_once dirname(__DIR__) . '/libs/twilio-php-master/Twilio/autoload.php';
ini_set('memory_limit', '-1');
ini_set('max_execution_time',0);
/*
* NOTE :
* Abandoned: Set status if lead is working and has not converted in 3 days since updated status
*/

$DEFAULT_ORDER_EMAIL = array("shailesh@cyberxllc.com");
trigger_mail_to_email("Set Lead Status to Abandoned", $DEFAULT_ORDER_EMAIL,$DEFAULT_SITE_NAME." : Set Lead Status to Abandoned");

$three_days_ago_date = date('Y-m-d',strtotime('-3 days'));
echo "Three days ago date: ".$three_days_ago_date;
echo "<br/>";
$lead_sql = "SELECT l.id,l.lead_id,l.fname,l.lname,l.lname,c.invite_at,l.lead_type
            FROM leads l
            JOIN customer c ON(c.id = l.customer_id AND c.is_deleted='N' AND c.status!='Post Payment')
            WHERE 
            l.is_deleted='N' AND 
            l.status IN('Working') AND 
            DATE(c.invite_at) < :three_days_ago_date AND 
            c.invite_at IS NOT NULL
            ORDER BY l.id";
$lead_where = array(":three_days_ago_date" => $three_days_ago_date);
$lead_res = $pdo->select($lead_sql,$lead_where);
if(!empty($lead_res)) {
    foreach ($lead_res as $key => $lead_row) {
        $upd_lead_data = array(
            'status' => 'Abandoned',
            'updated_at' => 'msqlfunc_NOW()'
        );
        $upd_lead_where = array(
            'clause' => 'id = :id',
            'params' => array(
                ':id' => $lead_row['id'],
            ),
        );
        $pdo->update('leads',$upd_lead_data,$upd_lead_where);

        $desc = array();
        $desc['ac_message'] = array(
            'ac_red_1' => array(
                'href' => 'lead_details.php?id=' . md5($lead_row['id']),
                'title' => $lead_row['lead_id'],
            ),
            'ac_message_1' => ' status updated from Working to Abandoned',
        );
        //$desc['InvitedAt'] = "Invited At : ".date('m/d/Y',strtotime($lead_row['invite_at']));     
        $desc['ChangedBy'] = "Status updated by ".$DEFAULT_SITE_NAME.", Due to lead has not converted in 3 days since invited";
        $desc = json_encode($desc);
        activity_feed(3,$lead_row['id'],'Lead',$lead_row['id'],'leads','Status Updated','','',$desc);

        
        //pre_print($lead_row,false);
    }
}
echo "Completed";
dbConnectionClose();
?>