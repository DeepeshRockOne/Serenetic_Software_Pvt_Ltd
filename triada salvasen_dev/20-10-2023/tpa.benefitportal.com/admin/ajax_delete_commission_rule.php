<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$commission = $_POST["commission"];
$res = array();

$query = "SELECT id,status,rule_code FROM commission_rule WHERE md5(id) = :id";
$srow = $pdo->selectOne($query,array(":id"=>$commission));

if (!empty($srow)) {
  $update_params = array(
      'is_deleted' => 'Y',
      'status' => 'Inactive',
  );
  $update_where = array(
      'clause' => 'md5(id) = :delete_id',
      'params' => array(
          ':delete_id' => makeSafe($commission)
      )
  );
  $pdo->update("commission_rule", $update_params, $update_where);
  $update_where = array(
      'clause' => 'md5(parent_rule_id)=:delete_id',
      'params' => array(
          ':delete_id' => makeSafe($commission)
      )
  );
  $pdo->update("commission_rule", $update_params, $update_where);
      
  $res['status'] = 'success';
  setNotifySuccess("Commission Rule Deleted Successfully");

  $description['ac_message'] =array(
    'ac_red_1'=>array(
      'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
      'title'=>$_SESSION['admin']['display_id'],
    ),
    'ac_message_1' =>' Deleted Commission ',
    'ac_red_2'=>array(
        'href'=>$ADMIN_HOST.'/add_commission_rule.php?commission='.$commission,
        'title'=>$srow['rule_code'],
    ),
  ); 

  activity_feed(3, $_SESSION['admin']['id'], 'Admin', $srow['id'], 'commission','Deleted Commission', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
} else {
  $res['status'] = 'fail';
}
header('Content-Type: application/json');
echo json_encode($res);
dbConnectionClose();
exit;
?>
