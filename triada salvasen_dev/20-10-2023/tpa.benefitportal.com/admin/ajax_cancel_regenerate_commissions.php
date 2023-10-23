<?php
include_once 'layout/start.inc.php';

$res = array();

$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';

if(!empty($id)){
	$sql="SELECT id FROM regenerated_commission WHERE md5(id)=:id AND status='Pending'";
	$res=$pdo->selectOne($sql,array(":id"=>$id));
}

if(!empty($res)) {
	$params = array('is_cancelled' => 'Y','status'=>'Cancelled');
  	$where = array(
	    'clause' => 'id = :id ', 
	    'params' => array(':id' => $res['id'])
  	);
  	$pdo->update("regenerated_commission", $params, $where);
	$res["status"] = "success";
	
  // cancelled regenerate commissions code start
	 $description['ac_message'] =array(
      'ac_red_1'=>array(
        'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
        'title'=>$_SESSION['admin']['display_id'],
      ),
      'ac_message_1' =>' cancelled regenerate commissions',
    ); 

    activity_feed(3, $_SESSION['admin']['id'], 'Admin', $res['id'], 'regenerated_commission','Regenerate Commissions', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
  // cancelled regenerate commissions code ends
}else{
	$res["status"] = "fail";
}
echo json_encode($res);
dbConnectionClose();
exit;
?>