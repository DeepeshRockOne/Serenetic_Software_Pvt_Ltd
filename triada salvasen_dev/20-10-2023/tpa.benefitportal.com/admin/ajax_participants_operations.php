<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$res = array("status" => "fail","message" => "Something went wrong");
$ptId = checkIsset($_REQUEST["ptId"]);

$action = checkIsset($_REQUEST["action"]);
$status = checkIsset($_REQUEST["status"]);
$participants_tag = checkIsset($_REQUEST["participants_tag"]);
 
if(!empty($ptId)){
    if($action == "changeStatus"  && !empty($status)){
        $ptSql = "SELECT id,participants_id,status,CONCAT(fname,' ',lname) as name FROM participants WHERE md5(id) =:id and is_deleted='N'";
        $ptRow = $pdo->selectOne($ptSql,array(':id'=>$ptId));

        if(!empty($ptRow)){
            $updParams = array(
                'status' => makeSafe($status)
            );
            $updWhere = array(
                'clause' => 'id = :id',
                'params' => array(
                  ':id' => makeSafe($ptRow['id'])
                )
            );
            $pdo->update("participants", $updParams, $updWhere);  

            $oldStatus = $ptRow['status'];
            $newStatus = $status;

            $description['ac_message'] =array(
                'ac_red_1'=>array(
                    'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
                    'title'=>$_SESSION['admin']['display_id'],
                ),
                'ac_message_1' =>' updated Participants '.($ptRow['name']).'(',
                'ac_red_2'=>array(
                    'href'=> $ADMIN_HOST.'/participants_details.php?id='.$ptId,
                    'title'=> $ptRow['participants_id'],
                ),
                'ac_message_2'=>') status from '.$oldStatus.' to '.$newStatus,
            );

            $desc=json_encode($description);

            activity_feed(3,$_SESSION['admin']['id'],'Admin',$ptRow['id'],'Participants','Participants Status Updated',$_SESSION['admin']['fname'],$_SESSION['admin']['lname'],$desc);

            $res['status'] = 'success';
            $res['message'] = 'Status Changed Successfully';
        }
    }else if($action == "changeTag" && !empty($participants_tag)){
        $ptSql = "SELECT id,participants_tag,participants_id,CONCAT(fname,' ',lname) as name 
        FROM participants WHERE md5(id) =:id and is_deleted='N'";
        $ptRow = $pdo->selectOne($ptSql,array(':id'=>$ptId));

        if(!empty($ptRow)){
            $updParams = array(
                'participants_tag' => makeSafe($participants_tag)
            );
            $updWhere = array(
                'clause' => 'id = :id',
                'params' => array(
                  ':id' => makeSafe($ptRow['id'])
                )
            );
            $pdo->update("participants", $updParams, $updWhere);  

            $oldTag = $ptRow['participants_tag'];
            $newTag = $participants_tag;

            $description['ac_message'] =array(
                'ac_red_1'=>array(
                    'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
                    'title'=>$_SESSION['admin']['display_id'],
                ),
                'ac_message_1' =>' updated Participants '.($ptRow['name']).'(',
                'ac_red_2'=>array(
                    'href'=> $ADMIN_HOST.'/participants_details.php?id='.$ptId,
                    'title'=> $ptRow['participants_id'],
                ),
                'ac_message_2'=>') Participants Tag from '.$oldTag.' to '.$newTag,
            );

            $desc=json_encode($description);

            activity_feed(3,$_SESSION['admin']['id'],'Admin',$ptRow['id'],'Participants','Participants Tag Updated',$_SESSION['admin']['fname'],$_SESSION['admin']['lname'],$desc);

            $res['status'] = 'success';
            $res['message'] = 'Tag Changed Successfully';
        }
    }else if($action == "deleteParticipants"){
        $ptId = is_array($ptId) ? ("'" . implode("','", makeSafe($ptId)) . "'") : "'".$ptId."'";
        $ptSql = "SELECT id,participants_id,CONCAT(fname,' ',lname) as name 
            FROM participants WHERE md5(id) IN($ptId) and is_deleted='N'";
        $ptRow = $pdo->select($ptSql);
        if(!empty($ptRow)){
            foreach ($ptRow as $row) {
                $updParams = array(
                    'is_deleted' => 'Y',
                );
                $updWhere = array(
                    'clause' => 'id=:id',
                    'params' => array(
                        ':id' => $row['id']
                    )
                );
                $pdo->update('participants', $updParams, $updWhere);

                $updPrdWhere = array(
                    'clause' => 'participants_id=:id',
                    'params' => array(
                        ':id' => $row['id']
                    )
                );
                $pdo->update('participants_products', $updParams, $updPrdWhere);

                $desc = array();
                $desc['ac_message'] = array(
                    'ac_red_1' => array(
                        'href' => $ADMIN_HOST . '/admin_profile.php?id=' . md5($_SESSION['admin']['id']),
                        'title' => $_SESSION['admin']['display_id'],
                    ),
                    'ac_message_1' => ' deleted Participants  ' . ($row['name']) . '(',
                    'ac_red_2' => array(
                        'href' => 'javascript:void(0);',
                        'title' => $row['participants_id'],
                    ),
                    'ac_message_2' => ')'
                );
                $desc = json_encode($desc);
                activity_feed(3, $_SESSION['admin']['id'], 'Admin', $row['id'], 'Participants', 'Participants Deleted', $_SESSION['admin']['fname'], $_SESSION['admin']['lname'], $desc);   
            }
        }
        $res['status'] = 'success';
        $res['message'] = 'Participants deleted successfully';
    }
}

header('Content-type: application/json');
echo json_encode($res);
dbConnectionClose();
exit;
?>