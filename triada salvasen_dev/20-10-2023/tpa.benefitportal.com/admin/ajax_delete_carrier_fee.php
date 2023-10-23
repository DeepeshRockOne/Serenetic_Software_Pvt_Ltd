<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$res = array(); 
$id = checkIsset($_POST['id']);

$query = "SELECT id,display_id FROM prd_fees WHERE md5(id) =:id ";
$drow = $pdo->selectOne($query,array(':id'=>$id));

if(!empty($drow)){
  $id = $drow['id'];
  $updParam = array('is_deleted'=>'Y');
  $updWhere = array(
    'clause' => 'id = :id',
    'params' => array(':id' => $id)
  );
  $pdo->update('prd_fees', $updParam, $updWhere);

  $query = "SELECT id, status, type FROM prd_main WHERE prd_fee_id =:id ";
  $prow = $pdo->select($query,array(':id'=>$drow['id']));

  if(!empty($prow)){
    $updParam = array('is_deleted'=>'Y');
    $updWhere = array(
      'clause' => 'prd_fee_id = :id',
      'params' => array(':id' => $id)
    );
    $pdo->update('prd_main', $updParam, $updWhere);

    $insert_params = array('is_deleted' => 'Y');
    $update_where = array(
      'clause' => 'prd_fee_id=:fee_id',
      'params' => array(":fee_id" => $id)
    );
    $pdo->update('prd_assign_fees', $insert_params, $update_where);


    if(!empty($prow)){
      foreach ($prow as $key => $value) {
        $plan_params = array('is_deleted' => 'Y');
        $update_plan_where = array(
          'clause' => 'product_id = :product_id ',
          'params' => array(':product_id' => $value['id'])
        );
        $pdo->update("prd_matrix",$plan_params, $update_plan_where);
      }
    }

  }
  
  //************* Activity Code Start *************
    $description['ac_message'] =array(
      'ac_red_1'=>array(
        'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
        'title'=>$_SESSION['admin']['display_id'],
      ),
      'ac_message_1' =>' Deleted Carrier ',
      'ac_red_2'=>array(
        //'href'=> '',
        'title'=>$drow['display_id'],
      ),
    ); 
    activity_feed(3, $_SESSION['admin']['id'], 'Admin', $id, 'carrier','Admin Deleted Carrier', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
  //************* Activity Code End *************

  $res['status']="success";
  $res['message']="Fee Deleted Successfully";
}else{
  $res['status']="fail";
  $res['message']="Fee Not Found";
}



header('Content-Type: application/json');
echo json_encode($res);
dbConnectionClose();
exit;
?>