<?php
include_once 'layout/start.inc.php';
$res = array();

$rule_id = checkIsset($_POST['id']);

$sql="SELECT lpo.id,lpo.group_id,c.rep_id 
FROM list_bill_options lpo 
JOIN customer c ON (c.id=lpo.group_id AND c.type='Group')
WHERE lpo.id=:id";
$res=$pdo->selectOne($sql,array(":id"=>$rule_id));

if(!empty($res)){

	$params = array('is_deleted' => 'Y');
  	$where = array(
	    'clause' => 'id = :id ', 
	    'params' => array(':id' => $res['id'])
  	);
  	$pdo->update("list_bill_options", $params, $where);
	$res["status"] = "success";

	$description['ac_message'] =array(
		'ac_red_1'=>array(
			'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
			'title'=>$_SESSION['admin']['display_id'],
		),
		'ac_message_1' =>' Deleted List Bill Option For',
		'ac_red_2'=>array(
			//'href'=> '',
			'title'=>$res['rep_id'],
		),
	); 
	activity_feed(3, $_SESSION['admin']['id'], 'Admin', $res['id'], 'list_bill_options','Admin Deleted List Bill Option', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));

}else{
	$res["status"] = "fail";
}
echo json_encode($res);
dbConnectionClose();
exit;
?>