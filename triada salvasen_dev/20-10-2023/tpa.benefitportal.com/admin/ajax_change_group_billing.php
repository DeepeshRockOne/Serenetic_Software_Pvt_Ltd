<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$billing_type = $_REQUEST['billing_type'];
$group_id=$_POST['id'];
$admin_id = $_SESSION['admin']['id'];
$res = array();

$query = "SELECT c.id, cgs.billing_type, concat(c.fname,' ',c.lname) as name,c.rep_id FROM customer c
JOIN customer_group_settings cgs ON (cgs.customer_id = c.id)
WHERE md5(c.id) =:id and c.is_deleted='N'";
$srow = $pdo->selectOne($query,array(':id'=>$group_id));

if (!empty($srow)) {
  $update_params = array(
    'billing_type' => $billing_type
  );
  $update_where = array(
    'clause' => 'customer_id = :customer_id',
    'params' => array(
      ':customer_id' => makeSafe($srow['id'])
    )
  );
  
  $pdo->update("customer_group_settings", $update_params, $update_where);
  
  $billingArr=array(
    'individual'=>'Individual',
    'list_bill'=>'List Bill', 
    'TPA'=>'TPA',
  );
  
  $old_type = $billingArr[$srow['billing_type']];
  $new_type = $billingArr[$billing_type];
  
  if($billing_type=="list_bill"){
    $updateSql = array("product_billing_type"=>'list_bill');
    $updateWhere = array("clause" => "agent_id=:id", "params" => array(":id" => $srow['id']));
    $pdo->update("agent_product_rule", $updateSql, $updateWhere);

    $gr_members = $pdo->select("SELECT ws.id FROM website_subscriptions ws JOIN customer c ON(c.id = ws.customer_id) WHERE c.sponsor_id = :sponsor_id",array(":sponsor_id" => $srow['id']));
    if($gr_members){
      foreach ($gr_members as $member) {
        $updateSql = array("payment_type"=>'list_bill');
        $updateWhere = array("clause" => "id=:id", "params" => array(":id" => $member['id']));
        $pdo->update("website_subscriptions", $updateSql, $updateWhere);
      }
    }
  }

  $description['ac_message'] =array(
    'ac_red_1'=>array(
      'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
      'title'=>$_SESSION['admin']['display_id'],
    ),
    'ac_message_1' =>' updated Group '.$srow['name'].'(',
    'ac_red_2'=>array(
      'href'=> $ADMIN_HOST.'/groups_details.php?id='.$group_id,
      'title'=> $srow['rep_id'],
    ),
    'ac_message_2'=>') Billing Type from '.$old_type.' to '.$new_type,
  );

  $desc=json_encode($description);
  activity_feed(3,$_SESSION['admin']['id'], 'Admin' , $srow['id'], 'Group', 'Billing Type Updated',$_SESSION['admin']['name'],"",$desc, "", "");
  $res['status'] = 'success';
  $res['msg'] = 'Billing Type Changed Successfully';

} else {
  $res['status'] = 'error';
  $res['msg'] = 'Something went wrong';
}

header('Content-type: application/json');
echo json_encode($res);
dbConnectionClose();
exit;
?>

