<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$display = $_POST['display'];
$id=$_POST['id'];
$res = array();

$query = "SELECT id,customer_id,display_group_welcome FROM customer_settings WHERE customer_id =:id";
$srow = $pdo->selectOne($query,array(':id'=>$id));

if (!empty($srow)) {
  $update_params = array(
    'display_group_welcome' => $display
  );
  $update_where = array(
    'clause' => 'id = :id',
    'params' => array(
      ':id' => makeSafe($srow['id'])
    )
  );
  $pdo->update("customer_settings", $update_params, $update_where);


  $old_display = $srow['display_group_welcome'];
  $new_display = $display;

  $description['ac_message'] =array(
    'ac_red_1'=>array(
      'href'=>$ADMIN_HOST.'/groups_details.php?id='.md5($_SESSION['groups']['id']),
      'title'=>$_SESSION['groups']['rep_id'],
    ),
    'ac_message_1' =>' Updated Group Display Welcome ',
    'ac_message_2'=>'  from '.$old_display.' to '.$new_display,
  );

  $desc=json_encode($description);
  activity_feed(3,$_SESSION['groups']['id'], 'Group' , $srow['id'], 'customer_settings', 'Welcome Display Updated',$_SESSION['groups']['fname']." ".$_SESSION['groups']['lname'],"",$desc, "", "");
  $res['status'] = 'success';
} else {
  $res['status'] = 'error';
}

header('Content-type: application/json');
echo json_encode($res);
dbConnectionClose();
exit;
?>

