<?php
include_once 'layout/start.inc.php';
$res = array();

$del_id = checkIsset($_POST['id']);

$sql="SELECT id,product_code FROM sub_products WHERE is_deleted='N' AND md5(id)=:id";
$res=$pdo->selectOne($sql,array(":id"=>$del_id));

if(!empty($res)){
	$params = array('is_deleted' => 'Y');
  	$where = array(
	    'clause' => 'id = :id ', 
	    'params' => array(':id' => $res['id'])
  	);
  	$pdo->update("sub_products", $params, $where);
	$res["status"] = "success";

	$description['ac_message'] =array(
		'ac_red_1'=>array(
			'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
			'title'=>$_SESSION['admin']['display_id'],
		),
		'ac_message_1' =>' Deleted Sub Product ',
		'ac_red_2'=>array(
			//'href'=> '',
			'title'=>$res['product_code'],
		),
	); 
	activity_feed(3, $_SESSION['admin']['id'], 'Admin', $res['id'], 'sub_product','Admin Deleted Sub Product', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));

}else{
	$res["status"] = "fail";
}
echo json_encode($res);
dbConnectionClose();
exit;
?>