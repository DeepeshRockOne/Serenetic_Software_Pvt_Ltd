<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$lead_tag = $_REQUEST['lead_tag'];
$id=$_POST['id'];
$res = array();
$query = "SELECT * FROM leads WHERE md5(id) =:id and is_deleted='N'";
$srow = $pdo->selectOne($query,array(':id'=>$id));

if (!empty($srow)) {
  $update_params = array(
    'opt_in_type' => makeSafe($lead_tag)
  );
  $update_where = array(
    'clause' => 'id = :id',
    'params' => array(
      ':id' => makeSafe($srow['id'])
    )
  );
  
  $pdo->update("leads", $update_params, $update_where);  

  $old_lead_tag = $srow['opt_in_type'];
  $new_lead_tag = $lead_tag;

  $description['ac_message'] =array(
    'ac_red_1'=>array(
        'href'=>$ADMIN_HOST.'/groups_details.php?id='.md5($_SESSION['groups']['id']),
        'title'=>$_SESSION['groups']['rep_id'],
    ),
    'ac_message_1' =>' updated Lead '.($srow['fname'].' '.$srow['lname']).'(',
    'ac_red_2'=>array(
        'href'=> $ADMIN_HOST.'/lead_details.php?id='.$id,
        'title'=> $srow['lead_id'],
    ),
    'ac_message_2'=>') Lead Tag from '.$old_lead_tag.' to '.$new_lead_tag,
  );

  $desc=json_encode($description);

  activity_feed(3,$srow['id'],'Lead',$_SESSION['groups']['id'],'Group','Lead Tag Updated',$_SESSION['groups']['fname'],$_SESSION['groups']['lname'],$desc);

  $res['status'] = 'success';
  $res['msg'] = 'Tag Changed Successfully';

} else {
  $res['status'] = 'error';
  $res['msg'] = 'Something went wrong';
}
header('Content-type: application/json');
echo json_encode($res);
dbConnectionClose();
exit;
?>

