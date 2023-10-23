<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$id = checkIsset($_POST["id"]);
$status = checkIsset($_POST['status']);
 
$res = array();

$query = "SELECT id, status,product_code FROM sub_products WHERE is_deleted='N' AND md5(id) =:id";
$srow = $pdo->selectOne($query,array(':id'=>$id));

if(!empty($srow)){
  $update_params = array(
    'status' => makeSafe($status),
  );
  $update_where = array(
    'clause' => 'id = :id',
    'params' => array(
      ':id' => makeSafe($srow['id'])
    )
  );


  //************* Activity Code Start *************
    $oldVaArray = $srow;
    $NewVaArray = $update_params;
    unset($oldVaArray['id']);

    $checkDiff=array_diff_assoc($NewVaArray, $oldVaArray);
     
    if(!empty($checkDiff)){
      $activityFeedDesc['ac_message'] =array(
        'ac_red_1'=>array(
          'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
          'title'=>$_SESSION['admin']['display_id']),
        'ac_message_1' =>' Updated Sub Product ',
        'ac_red_2'=>array(
          //'href'=> '',
          'title'=>$srow['product_code'],
        ),
      ); 
      
      if(!empty($checkDiff)){
        foreach ($checkDiff as $key1 => $value1) {
          $activityFeedDesc['key_value']['desc_arr'][$key1]='From '.$oldVaArray[$key1].' To '.$NewVaArray[$key1];
        } 
      }

      activity_feed(3, $_SESSION['admin']['id'], 'Admin', $srow['id'], 'sub_product','Admin Updated Sub Product', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($activityFeedDesc));
    }
  //************* Activity Code End   *************

  $pdo->update("sub_products", $update_params, $update_where);
  
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

