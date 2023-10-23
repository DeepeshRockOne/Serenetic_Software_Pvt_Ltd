<?php
include_once 'layout/start.inc.php';
ini_set('memory_limit', '-1');
ini_set('max_execution_time',300000);
$id = isset($_GET['id'])?$_GET['id']:'';

$file_where = array(":id" => $id);
$file_row = $pdo->selectOne("SELECT * FROM import_requests WHERE md5(id)=:id AND is_deleted='N'",$file_where);
if(!empty($file_row)) {
    if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'delete') {
        // $pdo->delete('DELETE FROM import_requests where id=:id',array(':id' => $file_row['id']));
        // $pdo->delete('DELETE FROM import_csv_log where file_id=:id',array(':id' => $file_row['id']));

        $file_name = $CSV_DIR . $file_row['file_name'];
        // if(file_exists($file_name)) {
        //     unlink($file_name);
        // }

        $pdo->update("import_requests",array("is_deleted"=>'Y'),array(
            'clause' => "id=:id AND is_deleted='N'",
            'params' => array(
                ":id" => $file_row['id']
            )
        ));

        $pdo->update("import_csv_log",array("is_deleted"=>'Y'),array(
            'clause' => "file_id=:id AND is_deleted='N'",
            'params' => array(
                ":id" => $file_row['id']
            )
        ));

        $desc = array();
        $desc['ac_message'] = array(
            'ac_red_1' => array(
                'href' => 'admin_profile.php?id='.md5($_SESSION['admin']['id']),
                'title' => $_SESSION['admin']['display_id'],
            ),
            'ac_message_1' => 'Deleted Import Request'
        );
        $desc['lead_type'] = "Module : ".$file_row['module_type'];
        $desc['lead_tag'] = "Import Type : ".$file_row['import_type'];
        $desc['total_leads'] = "Total Records : ".$file_row['total_records'];
        $desc = json_encode($desc);
        activity_feed(3,$_SESSION['admin']['id'],'Admin',$file_row['id'],'import_requests', 'Deleted Import Request','','', $desc);

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
            $pdo->update('import_requests', $csv_row_upd_data, $csv_row_where);

            $desc = array();
            $desc['ac_message'] = array(
                'ac_red_1' => array(
                    'href' => 'admin_profile.php?id='.md5($_SESSION['admin']['id']),
                    'title' => $_SESSION['admin']['display_id'],
                ),
                'ac_message_1' => 'Cancelled Import Request'
            );
            $desc['lead_type'] = "Module : ".$file_row['module_type'];
            $desc['lead_tag'] = "Import Type : ".$file_row['import_type'];
            $desc['total_leads'] = "Total Records : ".$file_row['total_records'];
            $desc = json_encode($desc);
            activity_feed(3,$_SESSION['admin']['id'],'Admin',$file_row['id'],'import_requests', 'Cancelled Import Request','','', $desc);

            $res['status'] = "success";
            $res['msg'] = "Import request cancelled.";
        }
    }
} else {
    $res['status'] = "fail";
    $res['msg'] = "Import request not found.";
}

echo json_encode($res);
dbConnectionClose();
exit;
?>
