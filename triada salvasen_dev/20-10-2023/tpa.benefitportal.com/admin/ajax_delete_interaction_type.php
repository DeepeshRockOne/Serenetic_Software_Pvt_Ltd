<?php
include_once 'layout/start.inc.php';
$res = array();

$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';

if(!empty($id)){
	$sql="SELECT id,type,user_type FROM interaction WHERE is_deleted='N' AND md5(id)=:id";
	$res=$pdo->selectOne($sql,array(":id"=>$id));
}

	if(!empty($res)) {
		$params = array('is_deleted' => 'Y');
	  	$where = array(
		    'clause' => 'id = :id ', 
		    'params' => array(':id' => $res['id'])
	  	);
	  	$pdo->update("interaction", $params, $where);
		$res["status"] = "success";

		//************* Activity Code Start *************

		 $description['ac_message'] =array(
	      'ac_red_1'=>array(
	        'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
	        'title'=>$_SESSION['admin']['display_id'],
	      ),
	      'ac_message_1' =>' deleted '.$res['type'].' on '.ucfirst($res['user_type']).' interaction',
	    ); 

	    activity_feed(3, $_SESSION['admin']['id'], 'Admin', $res['id'], 'interaction','Deleted Interaction Type', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
	}else{
		$res["status"] = "fail";
	}
echo json_encode($res);
dbConnectionClose();
exit;
?>