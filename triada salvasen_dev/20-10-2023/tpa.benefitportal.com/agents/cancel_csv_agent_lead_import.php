<?php
include_once 'layout/start.inc.php';
ini_set('memory_limit', '-1');
ini_set('max_execution_time',300000);
$id = isset($_GET['id'])?$_GET['id']:'';
$agent_id = $_SESSION['agents']['id'];

$file_where = array(":id" => $id,":agent_id" => $agent_id);
$file_row = $pdo->selectOne("SELECT * FROM csv_agent_leads WHERE md5(id)=:id AND agent_id=:agent_id",$file_where);
if(!empty($file_row)) {
    if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'delete') {
        $pdo->delete('DELETE FROM csv_agent_leads where id=:id',array(':id' => $file_row['id']));
        $pdo->delete('DELETE FROM agent_csv_log where agent_csv_id=:id',array(':id' => $file_row['id']));

        $file_name = $CSV_DIR . $file_row['file_name'];
        if(file_exists($file_name)) {
            unlink($file_name);
        }

        $desc = array();
        $desc['ac_message'] = array(
            'ac_red_1' => array(
                'href' => 'agent_detail_v1.php?id='.md5($_SESSION['agents']['id']),
                'title' => $_SESSION['agents']['rep_id'],
            ),
            'ac_message_1' => 'Deleted CSV Leads Import Request'
        );
        $desc['lead_type'] = "Lead Type : ".$file_row['lead_type'];
        $desc['lead_tag'] = "Lead Tag : ".$file_row['lead_tag'];
        $desc['total_leads'] = "Total Leads : ".$file_row['total_leads'];
        $desc = json_encode($desc);
        activity_feed(3,$_SESSION['agents']['id'],$_SESSION['agents']['type'],$file_row['id'],'csv_agent_leads', 'Deleted CSV Leads Import Request','','', $desc);

        $res['status'] = "success";
        $res['msg'] = "Import request deleted.";
        $res['file_name'] = $file_name;
    } else {
        if($file_row['status'] == "Processed") {
            $res['status'] = "fail";
            $res['msg'] = "Import request already completed.";

        } elseif($file_row['status'] == "Cancel") {
            $res['status'] = "fail";
            $res['msg'] = "Import request already cancelled.";

        } else {
            $csv_row_upd_data = array(
                'status' => 'Cancel',
                'updated_at' => 'msqlfunc_NOW()',
            );
            $csv_row_where = array(
                "clause" => "id=:id",
                "params" => array(":id" => $file_row['id'])
            );
            $pdo->update('csv_agent_leads', $csv_row_upd_data, $csv_row_where);

            $desc = array();
            $desc['ac_message'] = array(
                'ac_red_1' => array(
                    'href' => 'agent_detail_v1.php?id='.md5($_SESSION['agents']['id']),
                    'title' => $_SESSION['agents']['rep_id'],
                ),
                'ac_message_1' => 'Cancelled CSV Leads Import'
            );
            $desc['lead_type'] = "Lead Type : ".$file_row['lead_type'];
            $desc['lead_tag'] = "Lead Tag : ".$file_row['lead_tag'];
            $desc['total_leads'] = "Total Leads : ".$file_row['total_leads'];
            $desc = json_encode($desc);
            activity_feed(3,$_SESSION['agents']['id'],$_SESSION['agents']['type'],$file_row['id'],'csv_agent_leads', 'Cancelled CSV Leads Import','','', $desc);

            $res['status'] = "success";
            $res['msg'] = "Import request cancelled.";
        }
    }
} else {
    $res['status'] = "fail";
    $res['msg'] = "Import request not found.";
}

echo json_encode($res);
exit;
?>
