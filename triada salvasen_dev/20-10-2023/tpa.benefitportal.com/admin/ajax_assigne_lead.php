<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
if (isset($_POST['lead_ids'])) {
    $sponsor_id = $_POST['sponsor_id'];
    if ($sponsor_id > 0 && count($_POST['lead_ids']) > 0) {

        $sponsor_query = "SELECT id,rep_id,CONCAT(fname,' ',lname) as name,type FROM customer WHERE id=:id";
        $sponsor_row = $pdo->selectOne($sponsor_query, array(':id' => $sponsor_id));

        foreach ($_POST['lead_ids'] as $lead_id) {

            $lead_sql = "SELECT l.*,s.rep_id as sponsor_rep_id,CONCAT(s.fname,' ',s.lname) as sponsor_name,s.type as sponsor_type
                      FROM leads l 
                      LEFT JOIN customer s ON(s.id = l.sponsor_id)
                      WHERE md5(l.id)=:id and l.is_deleted='N'";
            $lead_row = $pdo->selectOne($lead_sql, array(':id' => $lead_id));

            if (!empty($sponsor_row) && !empty($lead_row) && $lead_row['sponsor_id'] != $sponsor_id) {
                $up_params = array(
                    'sponsor_id' => $sponsor_id,
                    'updated_at' => 'mysqlfunc_NOW()'
                );
                $up_where = array(
                    'clause' => 'id=:id',
                    'params' => array(
                        ':id' => $lead_row['id']
                    )
                );
                $pdo->update('leads', $up_params, $up_where);

                $desc = array();

                if($lead_row['sponsor_id'] > 0) {
                    $desc['ac_message'] = array(
                        'ac_red_1'=>array(
                            'href'=> 'admin_profile.php?id='.md5($_SESSION['admin']['id']),
                            'title'=>$_SESSION['admin']['display_id'],
                        ),
                        'ac_message_1' =>' Assigned Lead '.($lead_row['fname'].' '.$lead_row['lname']).'(',
                        'ac_red_2'=>array(
                            'href'=>  'lead_details.php?id='.md5($lead_row['id']),
                            'title'=> $lead_row['lead_id'],
                        ),
                        'ac_message_2'=>') <br/> Old Sponsor : '.($lead_row['sponsor_name']).' (',
                        'ac_red_3'=>array(
                            'href'=> 'agent_detail_v1.php?id='.md5($lead_row['sponsor_id']),
                            'title'=> $lead_row['sponsor_rep_id'],
                        ),
                        'ac_message_3'=>') <br/> New Sponsor : '.($sponsor_row['name']).' (',
                        'ac_red_4'=>array(
                            'href'=> 'agent_detail_v1.php?id='.md5($sponsor_row['id']),
                            'title'=> $sponsor_row['rep_id'],
                        ),
                        'ac_message_4'=>')',
                    );
                    $desc = json_encode($desc);
                    activity_feed(3, $_SESSION['admin']['id'], 'Admin', $lead_row['id'], 'Lead', 'Assigned Lead', $_SESSION['admin']['fname'], $_SESSION['admin']['lname'], $desc);
                } else {
                    $desc['ac_message'] = array(
                        'ac_red_1'=>array(
                            'href'=> 'admin_profile.php?id='.md5($_SESSION['admin']['id']),
                            'title'=>$_SESSION['admin']['display_id'],
                        ),
                        'ac_message_1' =>' Assigned Lead '.($lead_row['fname'].' '.$lead_row['lname']).'(',
                        'ac_red_2'=>array(
                            'href'=>  'lead_details.php?id='.md5($lead_row['id']),
                            'title'=> $lead_row['lead_id'],
                        ),
                        'ac_message_2'=>') To '.($sponsor_row['name']).' (',
                        'ac_red_3'=>array(
                            'href'=> 'agent_detail_v1.php?id='.md5($sponsor_row['id']),
                            'title'=> $sponsor_row['rep_id'],
                        ),
                        'ac_message_3'=>')',
                    );
                    $desc = json_encode($desc);
                    activity_feed(3, $_SESSION['admin']['id'], 'Admin', $lead_row['id'], 'Lead', 'Assigned Lead', $_SESSION['admin']['fname'], $_SESSION['admin']['lname'], $desc);
                }
            }
        }
        $res['status'] = 'success';
        $res['msg'] = 'Leads Assigned Successfully';
    } else {
        $res['msg'] = 'Lead not found';
        $res['status'] = 'fail';
    }
} else {
    $lead_id = $_POST['id'];
    $sponsor_id = $_POST['sponsor_id'];

    $res = array();
    $lead_sql = "SELECT l.*,s.rep_id as sponsor_rep_id,CONCAT(s.fname,' ',s.lname) as sponsor_name,s.type as sponsor_type
                  FROM leads l 
                  LEFT JOIN customer s ON(s.id = l.sponsor_id)
                  WHERE md5(l.id)=:id and l.is_deleted='N'";
    $lead_row = $pdo->selectOne($lead_sql, array(':id' => $lead_id));
    
    $sponsor_query = "SELECT id,rep_id,CONCAT(fname,' ',lname) as name,type FROM customer WHERE id=:id";
    $sponsor_row = $pdo->selectOne($sponsor_query, array(':id' => $sponsor_id));

    if (!empty($sponsor_row) && !empty($lead_row) && $lead_row['sponsor_id'] != $sponsor_id) {

        $update_params = array(
            'sponsor_id' => $sponsor_id,
            'updated_at' => 'mysqlfunc_NOW()'
        );
        $update_where = array(
            'clause' => 'id = :id',
            'params' => array(
                ':id' => makeSafe($lead_row['id'])
            )
        );
        $pdo->update("leads", $update_params, $update_where);

        if($lead_row['sponsor_id'] > 0) {
            $desc['ac_message'] = array(
                'ac_red_1'=>array(
                    'href'=> 'admin_profile.php?id='.md5($_SESSION['admin']['id']),
                    'title'=>$_SESSION['admin']['display_id'],
                ),
                'ac_message_1' =>' Assigned Lead '.($lead_row['fname'].' '.$lead_row['lname']).'(',
                'ac_red_2'=>array(
                    'href'=>  'lead_details.php?id='.md5($lead_row['id']),
                    'title'=> $lead_row['lead_id'],
                ),
                'ac_message_2'=>') Old Sponsor : '.($lead_row['sponsor_name']).' (',
                'ac_red_3'=>array(
                    'href'=> 'agent_detail_v1.php?id='.md5($lead_row['sponsor_id']),
                    'title'=> $lead_row['sponsor_rep_id'],
                ),
                'ac_message_3'=>') New Sponsor : '.($sponsor_row['name']).' (',
                'ac_red_4'=>array(
                    'href'=> 'agent_detail_v1.php?id='.md5($sponsor_row['id']),
                    'title'=> $sponsor_row['rep_id'],
                ),
                'ac_message_4'=>')',
            );
            $desc = json_encode($desc);
            activity_feed(3, $_SESSION['admin']['id'], 'Admin', $lead_row['id'], 'Lead', 'Assigned Lead', $_SESSION['admin']['fname'], $_SESSION['admin']['lname'], $desc);
        } else {
            $desc['ac_message'] = array(
                'ac_red_1'=>array(
                    'href'=> 'admin_profile.php?id='.md5($_SESSION['admin']['id']),
                    'title'=>$_SESSION['admin']['display_id'],
                ),
                'ac_message_1' =>' Assigned Lead '.($lead_row['fname'].' '.$lead_row['lname']).'(',
                'ac_red_2'=>array(
                    'href'=>  'lead_details.php?id='.md5($lead_row['id']),
                    'title'=> $lead_row['lead_id'],
                ),
                'ac_message_2'=>') To '.($sponsor_row['name']).' (',
                'ac_red_3'=>array(
                    'href'=> 'agent_detail_v1.php?id='.md5($sponsor_row['id']),
                    'title'=> $sponsor_row['rep_id'],
                ),
                'ac_message_3'=>')',
            );
            $desc = json_encode($desc);
            activity_feed(3, $_SESSION['admin']['id'], 'Admin', $lead_row['id'], 'Lead', 'Assigned Lead', $_SESSION['admin']['fname'], $_SESSION['admin']['lname'], $desc);
        }      

        $res['status'] = 'success';
        $res['msg'] = 'Leads Assigned Successfully';

    } else {
        $res['status'] = 'error';
        $res['msg'] = 'Lead not found';
    }
}
header('Content-type: application/json');
echo json_encode($res);
dbConnectionClose();
exit;
?>