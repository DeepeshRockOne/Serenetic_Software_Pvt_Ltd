<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
  
$status = checkIsset($_POST['status']);
$id=checkIsset($_POST['id']); 
$res = array();

$query = "SELECT id, status, type,product_code,product_type FROM prd_main WHERE md5(id) =:id ";
$srow = $pdo->selectOne($query,array(':id'=>$id));

if (!empty($srow)) {
  $id=$srow['id'];
  $setting_type=$srow['product_type'];
  $update_params = array('status' => makeSafe($status));
  $update_where = array(
    'clause' => 'id = :id',
    'params' => array(':id' => makeSafe($id))
  );
  
  $pdo->update("prd_main", $update_params, $update_where);

  //************* Activity Code Start *************
    $description['ac_message'] =array(
      'ac_red_1'=>array(
        'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
        'title'=>$_SESSION['admin']['display_id'],
      ),
      'ac_message_1' =>' Update '. $setting_type .' Fee Status',
      'ac_red_2'=>array(
        //'href'=> $RE_HOST,
        'title'=> $srow['product_code'],
      ),
    ); 

    $description['key_value']['desc_arr']['status']='From '.$srow['status'].' To '. $status;

    activity_feed(3, $_SESSION['admin']['id'], 'Admin', $id, $setting_type,"Admin Change ".$setting_type." Fee Status ", $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
  //************* Activity Code End *************
 
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

