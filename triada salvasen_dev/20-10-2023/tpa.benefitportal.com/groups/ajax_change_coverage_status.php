<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$status = $_REQUEST['status'];
$id=$_POST['id'];
$res = array();

$query = "SELECT id,status,coverage_period_name,display_id FROM group_coverage_period WHERE md5(id) =:id and is_deleted='N'";
$srow = $pdo->selectOne($query,array(':id'=>$id));

if (!empty($srow)) {
  $update_params = array(
    'status' => makeSafe($status)
  );
  $update_where = array(
    'clause' => 'id = :id',
    'params' => array(
      ':id' => makeSafe($srow['id'])
    )
  );
  $pdo->update("group_coverage_period", $update_params, $update_where);


  $old_status = $srow['status'];
  $new_status = $status;

  $description['ac_message'] =array(
    'ac_red_1'=>array(
      'href'=>$ADMIN_HOST.'/groups_details.php?id='.md5($_SESSION['groups']['id']),
      'title'=>$_SESSION['groups']['rep_id'],
    ),
    'ac_message_1' =>' Updated Plan Status '.$srow['coverage_period_name'].'(',
    'ac_red_2'=>array(
      'title'=> $srow['display_id'],
    ),
    'ac_message_2'=>') status from '.$old_status.' to '.$new_status,
  );

  $desc=json_encode($description);
  activity_feed(3,$_SESSION['groups']['id'], 'Group' , $srow['id'], 'group_coverage_period', 'Status Updated',$_SESSION['groups']['fname']." ".$_SESSION['groups']['lname'],"",$desc, "", "");
  $res['status'] = 'success';
  $res['msg'] = 'Status Changed Successfully';

} else {
  $res['status'] = 'error';
  $res['msg'] = 'Something went wrong';
}

header('Content-type: application/json');
echo json_encode($res);
dbConnectionClose();
exit;
?>

