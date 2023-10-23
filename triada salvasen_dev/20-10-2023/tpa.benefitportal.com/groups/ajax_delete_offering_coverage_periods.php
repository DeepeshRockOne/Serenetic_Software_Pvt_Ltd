<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$id=$_POST['id'];
$res = array();

$query = "SELECT id,open_enrollment_start,open_enrollment_end FROM group_coverage_period_offering WHERE md5(id) =:id and is_deleted='N'";
$srow = $pdo->selectOne($query,array(':id'=>$id));

if (!empty($srow)) {
  $update_params = array(
    'is_deleted' => 'Y'
  );
  $update_where = array(
    'clause' => 'id = :id',
    'params' => array(
      ':id' => makeSafe($srow['id'])
    )
  );
  $pdo->update("group_coverage_period_offering", $update_params, $update_where);

  $update_sub_where = array(
    'clause' => 'group_coverage_period_offering_id = :id',
    'params' => array(
      ':id' => makeSafe($srow['id'])
    )
  );
  $pdo->update("group_coverage_period_contributions", $update_params, $update_sub_where);


  $description['ac_message'] =array(
    'ac_red_1'=>array(
      'href'=>$ADMIN_HOST.'/groups_details.php?id='.md5($_SESSION['groups']['id']),
      'title'=>$_SESSION['groups']['rep_id'],
    ),
    'ac_message_1' =>' Deleted Plan Period Offering ',
    'ac_red_2'=>array(
      'title'=> date($DATE_FORMAT,strtotime($srow['open_enrollment_start'])).' - '. date($DATE_FORMAT,strtotime($srow['open_enrollment_end'])),
    ),
  );

  $desc=json_encode($description);
  activity_feed(3,$_SESSION['groups']['id'], 'Group' , $srow['id'], 'group_coverage_period_offering', 'Plan Period Offering Deleted',$_SESSION['groups']['fname']." ".$_SESSION['groups']['lname'],"",$desc, "", "");
  $res['status'] = 'success';
  $res['msg'] = 'Plan Offering Deleted Successfully';

} else {
  $res['status'] = 'error';
  $res['msg'] = 'Something went wrong';
}

header('Content-type: application/json');
echo json_encode($res);
dbConnectionClose();
exit;
?>

