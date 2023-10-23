<?php
include_once 'layout/start.inc.php';
ini_set('memory_limit','-1');
ini_set('max_execution_time','-1');

$res = array("status" => "fail","message" => "Something went wrong");
$file_id = checkIsset($_REQUEST["file_id"]); 
$action = checkIsset($_REQUEST["action"]); 

if(!empty($file_id) && !empty($action)){
    $fileSql = "SELECT * FROM participants_csv WHERE md5(id)=:id AND is_deleted='N'";
    $fileWhere = array(":id" => $file_id);
    $fileRow = $pdo->selectOne($fileSql,$fileWhere);

    if(!empty($fileRow)){
        if($action == "cancelRequest"){
            if($fileRow['status'] == "Processed") {
                $res['status'] = "fail";
                $res['message'] = "Import request already completed.";
            } else if($fileRow['status'] == "Cancel") {
                $res['status'] = "fail";
                $res['message'] = "Import request already cancelled.";
            } else {
                $csvRowUpd = array(
                    'status' => 'Cancel',
                );
                $csvRowWhere = array(
                    "clause" => "id=:id",
                    "params" => array(":id" => $fileRow['id'])
                );
                $pdo->update('participants_csv', $csvRowUpd, $csvRowWhere);

                $desc = array();
                $desc['ac_message'] = array(
                    'ac_red_1' => array(
                        'href' => 'admin_profile.php?id='.md5($_SESSION['admin']['id']),
                        'title' => $_SESSION['admin']['display_id'],
                    ),
                    'ac_message_1' => 'Cancelled CSV Participants Import'
                );
                $desc['participants_type'] = "Participants Type : ".$fileRow['participants_type'];
                $desc['participants_tag'] = "Participants Tag : ".$fileRow['participants_tag'];
                $desc['total_participants'] = "Total Participants : ".$fileRow['total_participants'];
                $desc = json_encode($desc);
                activity_feed(3,$_SESSION['admin']['id'],'Admin',$fileRow['id'],'participants_csv', 'Cancelled CSV Participants Import','','', $desc);

                $res['status'] = "success";
                $res['message'] = "Import request cancelled.";
            }
        }elseif($action == "deleteRequest"){
            $csvRowUpd = array(
                'is_deleted' => 'Y',
            );
            $csvRowWhere = array(
                "clause" => "id=:id",
                "params" => array(":id" => $fileRow['id'])
            );
            $pdo->update('participants_csv', $csvRowUpd, $csvRowWhere);

            $csvLogUpd = array(
                'is_deleted' => 'Y',
            );
            $csvLogWhere = array(
                "clause" => "file_id=:id",
                "params" => array(":id" => $fileRow['id'])
            );
            $pdo->update('participants_csv_log', $csvLogUpd, $csvLogWhere);

            $desc = array();
            $desc['ac_message'] = array(
                'ac_red_1' => array(
                    'href' => 'admin_profile.php?id='.md5($_SESSION['admin']['id']),
                    'title' => $_SESSION['admin']['display_id'],
                ),
                'ac_message_1' => 'Deleted CSV Participants Import'
            );
            $desc['participants_type'] = "Participants Type : ".$fileRow['participants_type'];
            $desc['participants_tag'] = "Participants Tag : ".$fileRow['participants_tag'];
            $desc['total_participants'] = "Total Participants : ".$fileRow['total_participants'];
            $desc = json_encode($desc);
            activity_feed(3,$_SESSION['admin']['id'],'Admin',$fileRow['id'],'participants_csv', 'Deleted CSV Participants Import','','', $desc);

            $res['status'] = "success";
            $res['message'] = "Import request deleted.";
        }
    }
}

echo json_encode($res);
dbConnectionClose();
exit;
?>
