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
  $update_params = array(
    'is_deleted' => 'Y'
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
      'href' => $ADMIN_HOST.'groups_details.php?id='.md5($group_id),
      'title' => $_SESSION['groups']['rep_id'],
    ),
    'ac_message_1' =>' Deleted Billing Profile'
  );

  $desc=json_encode($description);
  activity_feed(3,$group_id,'Group',$srow['id'],'Group','Billing Profile Deleted',"","",$desc,"","");
  $res['status'] = 'success';
  $res['msg'] = 'Billing Profile Deleted Successfully';

} else {
  $res['status'] = 'error';
  $res['msg'] = 'Something went wrong';
}

header('Content-type: application/json');
echo json_encode($res);
dbConnectionClose();
exit;
?>

