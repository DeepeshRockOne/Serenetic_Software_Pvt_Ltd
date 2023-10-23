<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$res = array(); 
$id = checkIsset($_POST['id']);

$query = "SELECT id, status, type ,product_code,product_type FROM prd_main WHERE md5(id) =:id ";
$drow = $pdo->selectOne($query,array(':id'=>$id));

if(!empty($drow)){
  $id = $drow['id'];
  $setting_type=$drow['product_type'];
  
  $updParam = array('is_deleted'=>'Y');
  $updWhere = array(
    'clause' => 'id = :id',
    'params' => array(':id' => $id)
  );
  $pdo->update('prd_main', $updParam, $updWhere);
  
  $insert_params = array('is_deleted' => 'Y');
  $update_where = array(
    'clause' => 'fee_id=:fee_id',
    'params' => array(":fee_id" => $id)
  );
  $pdo->update('prd_assign_fees', $insert_params, $update_where);

  $insert_params = array('is_deleted' => 'Y');
  $update_where = array(
    'clause' => 'fee_product_id=:fee_id',
    'params' => array(":fee_id" => $id)
  );
  $pdo->update('prd_fee_pricing_model', $insert_params, $update_where);

  $plan_params = array('is_deleted' => 'Y');
  $update_plan_where = array(
    'clause' => 'product_id = :product_id ',
    'params' => array(':product_id' => $id)
  );
  $pdo->update("prd_matrix",$plan_params, $update_plan_where);

  //************* Activity Code Start *************
    $description['ac_message'] =array(
      'ac_red_1'=>array(
        'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
        'title'=>$_SESSION['admin']['display_id'],
      ),
      'ac_message_1' =>' Deleted '. $setting_type .' Fee ',
      'ac_red_2'=>array(
        //'href'=> '',
        'title'=>$drow['product_code'],
      ),
    ); 
    activity_feed(3, $_SESSION['admin']['id'], 'Admin', $id, 'carrier','Admin Deleted '. $setting_type .' Fee', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
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