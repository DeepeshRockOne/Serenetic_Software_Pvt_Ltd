<?php
include_once 'layout/start.inc.php';
$res = array();

$category_id = checkIsset($_POST['id']);

$sql="SELECT id,title FROM prd_category WHERE is_deleted='N' AND md5(id)=:id";
$res=$pdo->selectOne($sql,array(":id"=>$category_id));

if(!empty($res)) {
 
	$params = array('is_deleted' => 'Y');
  	$where = array(
	    'clause' => 'id = :id ', 
	    'params' => array(':id' => $res['id'])
  	);
  	$pdo->update("prd_category", $params, $where);
	$res["status"] = "success";

	$description['ac_message'] =array(
		'ac_red_1'=>array(
			'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
			'title'=>$_SESSION['admin']['display_id'],
		),
		'ac_message_1' =>' Deleted Product Categorie ',
		'ac_red_2'=>array(
			//'href'=> '',
			'title'=>$res['title'],
		),
	); 
	activity_feed(3, $_SESSION['admin']['id'], 'Admin', $res['id'], 'product_categories','Admin Deleted Product Categories', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));

}else{
	$res["status"] = "fail";
}
echo json_encode($res);
dbConnectionClose();
exit;
?>