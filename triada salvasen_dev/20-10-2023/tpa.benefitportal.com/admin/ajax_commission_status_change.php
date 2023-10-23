<?php
include_once 'layout/start.inc.php';
$res=array();
$status = checkIsset($_POST['status']);
$id = checkIsset($_POST['commission']);

$sqlCommissionRule="SELECT id,rule_code,status FROM commission_rule where md5(id)=:id";
$resCommissionRule=$pdo->selectOne($sqlCommissionRule,array(":id"=>$id));

if(!empty($resCommissionRule) && !empty($status) && isset($id)){
    $updateSql = array('status' => makeSafe($status));
    $where = array("clause" => 'md5(id)=:id', 'params' => array(':id' => makeSafe($id)));
    $pdo->update("commission_rule", $updateSql, $where);
    
    $description['ac_message'] =array(
	    'ac_red_1'=>array(
	      'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
	      'title'=>$_SESSION['admin']['display_id'],
	    ),
    	'ac_message_1' =>' Update Commission Status ',
	    'ac_red_2'=>array(
	      'href'=>$ADMIN_HOST.'/add_commission_rule.php?commission='.md5($resCommissionRule['id']),
	      'title'=>$resCommissionRule['rule_code'],
	    ),
  	); 

  	$description['key_value']['desc_arr']['status']='From '.$resCommissionRule['status'].' To '. $status;

  	activity_feed(3, $_SESSION['admin']['id'], 'Admin', $resCommissionRule['id'], 'commission','Commission Status Change ', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));

    $res['status'] = "success";
    $res['msg']="Commission status changed successfully";

} else {
  $res['status'] = "fail";
}

header('Content-Type: application/json');
echo json_encode($res);
dbConnectionClose();
exit;

?>