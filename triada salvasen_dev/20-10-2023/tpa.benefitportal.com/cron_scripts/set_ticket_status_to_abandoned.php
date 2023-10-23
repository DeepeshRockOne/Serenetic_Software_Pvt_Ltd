<?php
include_once dirname(__DIR__) . "/cron_scripts/connect.php";
include_once dirname(__DIR__) . '/libs/twilio-php-master/Twilio/autoload.php';
ini_set('memory_limit', '-1');
ini_set('max_execution_time',0);

/* --------- NOTE : STATUS CHANGE RULE -------------
If current ticket status is New, Open, or Reassigned and last updated date is more than 72 hours, and last update was done by admin, change ticket status to “Abandoned (User)”.

If current ticket status is New, Open, or Reassigned and last updated date is more than 72 hours, and last update was done by requester, change ticket status to “Abandoned (Admin)”.

If current ticket status is working and last updated date is more than 5 days, and last update was done by admin, change ticket status to “Abandoned (User)”.

If current ticket status is working and last updated date is more than 5 days, and last update was done by requester, change ticket status to “Abandoned (Admin)”.
*---------/NOTE : STATUS CHANGE RULE -------------*/

$DEFAULT_ORDER_EMAIL = array("shailesh@cyberxllc.com");
trigger_mail_to_email("Set E-Ticket Status to Abandoned", $DEFAULT_ORDER_EMAIL,$DEFAULT_SITE_NAME." : Set E-Ticket Status to Abandoned");

$today = date('Y-m-d');
$three_days_ago_date = date('Y-m-d',strtotime('-3 days'));
$five_days_ago_date = date('Y-m-d',strtotime('-5 days'));

echo "Today date: ".$today;
echo "<br/>";
echo "Three days ago date: ".$three_days_ago_date;
echo "<br/>";
echo "Five days ago date: ".$five_days_ago_date;
echo "<br/>";
$ticket_sql = "SELECT t.id,t.tracking_id,t.updated_at,t.status,CONCAT(a.fname,' ',a.lname) as admin_name,a.display_id,t.assigned_admin_id as admin_id
            FROM s_ticket t
            LEFT JOIN admin a ON(a.id = t.assigned_admin_id)
            WHERE
            ((t.status IN('New','Open','Reassigned') AND DATE(t.updated_at) < :three_days_ago_date) OR
            (t.status IN('Working') AND DATE(t.updated_at) < :five_days_ago_date))
            GROUP BY t.id
            ORDER BY t.id";
$ticket_where = array(":three_days_ago_date" => $three_days_ago_date,":five_days_ago_date" => $five_days_ago_date);
$ticket_res = $pdo->select($ticket_sql,$ticket_where);
if(!empty($ticket_res)) {
    foreach ($ticket_res as $key => $ticket_row) {
        $last_message_row = $pdo->selectOne("SELECT m.user_id,m.user_type,m.updated_at FROM s_ticket_message m WHERE m.ticket_id=:ticket_id ORDER BY m.id DESC",array(":ticket_id" => $ticket_row['id']));

        if(!empty($last_message_row)) {
            if(strtotime($ticket_row['updated_at']) <= strtotime($last_message_row['updated_at'])) {
                if($last_message_row['user_type'] != "Admin") {
                    continue;
                } else {
                    $ticket_status = "Abandoned (User)";    
                }
            } else {
                $ticket_status = "Abandoned (User)";
            }
        } else {
            $ticket_status = "Abandoned (User)";
        }

        $upd_param = array(
            "status" => $ticket_status
        );
        $upd_where = array(
            'clause' => 'id=:id',
            'params' => array(":id" => $ticket_row['id'])
        );
        $pdo->update('s_ticket',$upd_param,$upd_where);

        /*--- Activity Feed -----*/
        if(!empty($ticket_row['admin_id']) && !empty($ticket_row['display_id'])) {
            $desc = array();
            $desc['ac_message'] = array(
                'ac_message_1' =>' System updated E-Ticket (',
                'ac_red_2'=>array(
                    'href'=> $ADMIN_HOST.'/open_conversation_preview.php?s_ticket_id='.md5($ticket_row['id']).'&view=1',
                    'title'=> $ticket_row['tracking_id'],
                ),
                'ac_message_2' =>') <br>',
            );
            $desc['key_value']['desc_arr']['Status'] = ' Updated from '.$ticket_row['status'].' to '.$ticket_status;
            activity_feed(3,$ticket_row['admin_id'],'Admin',$ticket_row['admin_id'],'Admin','E-Ticket',"","",json_encode($desc));
        }
        /*---/Activity Feed -----*/
    }
}
echo "Completed";
dbConnectionClose();
?>