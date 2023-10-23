<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$id = checkIsset($_POST["id"]);
$status = checkIsset($_POST['status']);
 
$res = array();

$query = "SELECT id,status,display_id,setting_type FROM prd_fees WHERE md5(id) =:id";
$srow = $pdo->selectOne($query,array(':id'=>$id));

if ($srow) {
  $id = $srow['id'];
  $setting_type = $srow['setting_type'];

  if($setting_type = 'Vendor' && $status == 'Active'){
    $prd_assign_fees = $pdo->selectOne("SELECT id FROM prd_assign_fees WHERE prd_fee_id = :id and is_deleted = 'N'",array(':id' => $srow['id']));

    if(empty($prd_assign_fees)){
      $res['status'] = 'error';
      $res['msg'] = 'Please add fees';
      header('Content-type: application/json');
      echo json_encode($res);
      exit;
    }
  }

  $update_params = array(
    'status' => makeSafe($status),
  );
  $update_where = array(
    'clause' => 'id = :id',
    'params' => array(
      ':id' => makeSafe($id)
    )
  );
  $pdo->update("prd_fees", $update_params, $update_where);
  
  if($srow['setting_type']=='Carrier'){
    $RE_HOST= $ADMIN_HOST.'/manage_carrier.php?carrier_id='.md5($id);
  }else{
    $RE_HOST= $ADMIN_HOST.'/manage_vendor.php?vendor_id='.md5($id);
  }

  $description['ac_message'] =array(
    'ac_red_1'=>array(
      'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
      'title'=>$_SESSION['admin']['display_id'],
    ),
    'ac_message_1' =>' Update '. $setting_type .' Status ',
    'ac_red_2'=>array(
      'href'=> $RE_HOST,
      'title'=> $srow['display_id'],
    ),
  ); 

  $description['key_value']['desc_arr']['status']='From '.$srow['status'].' To '. $status;

  activity_feed(3, $_SESSION['admin']['id'], 'Admin', $id, $setting_type,"Admin Change ".$setting_type." Status ", $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));

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

