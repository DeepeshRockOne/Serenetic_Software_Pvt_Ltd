<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$billing_id = $_POST['billing'];
$group_id=$_POST['id'];


$res = array();

$query = "SELECT c.id,concat(c.fname,' ',c.lname) as name,c.rep_id FROM customer c
JOIN customer_billing_profile cbp ON (cbp.customer_id = c.id)
WHERE md5(c.id) =:id and c.is_deleted='N' AND md5(cbp.id)=:billing_id";
$srow = $pdo->selectOne($query,array(':id'=>$group_id,":billing_id"=>$billing_id));

if (!empty($srow)) {
  $params = array(
    'is_default' => 'N',
  );
  $where = array(
    'clause' => 'customer_id=:customer_id',
    'params' => array(
      ':customer_id' => makesafe($srow['id']),
    ),
  );
  $pdo->update('customer_billing_profile', $params, $where);

  $update_params = array(
    'is_default' => 'Y'
  );
  $update_where = array(
    'clause' => 'md5(id) = :id',
    'params' => array(
      ':id' => makeSafe($billing_id)
    )
  );
  
  $pdo->update("customer_billing_profile", $update_params, $update_where);
  
    

  $description['ac_message'] =array(
    'ac_red_1'=>array(
      'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
      'title'=>$_SESSION['admin']['display_id'],
    ),
    'ac_message_1' =>' Default Group Billing Profile Set',
  );

  $desc=json_encode($description);
  activity_feed(3,$_SESSION['admin']['id'], 'Admin' , $srow['id'], 'Group', 'Billing Profile Default',$_SESSION['admin']['name'],"",$desc, "", "");
  $res['status'] = 'success';
  $res['msg'] = 'Billing Profile Default Successfully';

} else {
  $res['status'] = 'error';
  $res['msg'] = 'Something went wrong';
}

header('Content-type: application/json');
echo json_encode($res);
dbConnectionClose();
exit;
?>

