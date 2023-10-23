<?php
include_once 'layout/start.inc.php';
$response = array();

$rule_id = checkIsset($_POST['id']);

$sql="SELECT gtr.id,gtr.name as reason
FROM termination_reason gtr 
WHERE gtr.id=:id";
$res=$pdo->selectOne($sql,array(":id"=>$rule_id));

if(!empty($res)){

	$params = array('is_deleted' => 'Y');
  	$where = array(
	    'clause' => 'id = :id ', 
	    'params' => array(':id' => $res['id'])
  	);
  	$pdo->update("termination_reason", $params, $where);
	

	$message = ' Deleted reason : <br> '.$res['reason'];
    $response['msg'] = 'Termination Reason Deleted successfully!';

    $activityFeedDesc['ac_message'] =array(
		'ac_red_1'=>array(
			'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
			'title'=>$_SESSION['admin']['display_id'],
		),
		'ac_message_1' =>$message,
    ); 
    activity_feed(3,$_SESSION['admin']['id'], 'Admin', $_SESSION['admin']['id'], 'Admin', 'Termination Reason',$_SESSION['admin']['name'],"",json_encode($activityFeedDesc));
    $response['status'] = 'success';

}else{
	$res["status"] = "fail";
}
echo json_encode($res);
dbConnectionClose();
exit;
?>