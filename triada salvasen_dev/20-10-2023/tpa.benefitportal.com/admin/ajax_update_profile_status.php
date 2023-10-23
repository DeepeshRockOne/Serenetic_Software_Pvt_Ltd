<?php

include_once 'layout/start.inc.php';

$validate = new Validation();

$id= checkIsset($_REQUEST['id']);
$status = checkIsset($_REQUEST['status']);
$old_status = checkIsset($_REQUEST['old_status']);
$type = checkIsset($_REQUEST['type']);
$res = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	
		if ($status != "") {
				
				$params = array();
				$params['status'] = $status;
       			$params['updated_at'] = date('Y-m-d H:i:s', time());
				
				$where = array(
					'clause' => 'md5(id)=:id',
					'params' => array(':id' => makesafe($id)),
				);
				
				$pdo->update('admin', $params, $where);

				$res['status'] = 'success';
				$res['msg'] = 'Profile status updated successfully';
				// $extra['old_type'] = $old_status;
				// $extra['new_type'] = $status;

				$extra['status_update'] = $old_status." to ".$status;
				$extra['from'] = ' from ';
				$extra['user_display_id'] = $_SESSION['admin']['display_id'];
				$res_enity = $pdo->selectOne("SELECT fname,lname,display_id,id from admin where md5(id)=:id",array(":id"=>$id));
				$extra['en_fname'] = $res_enity['fname'];
				$extra['en_lname'] = $res_enity['lname'];
				$extra['en_display_id'] = $res_enity['display_id'];

				$description['description'] = $_SESSION['admin']['display_id']." updated ".$res_enity['fname'].' '.$res_enity['lname'].' '.$res_enity['display_id'].' admin status from '.$old_status." to ".$status;
				activity_feed(3, $_SESSION['admin']['id'], 'Admin',  $res_enity['id'], 'update_old_to_new','Admin Status Updated', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description),'',json_encode($extra));
				
		} else if($type != "") {

				$params = array();
				$params['type'] = $type;
       			$params['updated_at'] = date('Y-m-d H:i:s', time());
				
				$where = array(
					'clause' => 'md5(id)=:id',
					'params' => array(':id' => makesafe($id)),
				);
				
				$pdo->update('admin', $params, $where);

// setNotifySuccess("Profile access updated successfully");
				$res['status'] = 'success';

				$res['msg'] = "Profile access updated successfully";
				
		}
		else {
			$res['status'] = 'fail';
			$res['error'] = "Something went wrong...!!!";
		}
}
if (count(checkIsset($res['error'],"arr")) > 0) {
	$res['error'] = $validate->getError('image');
	$res['status'] = 'fail';
}
header('Content-Type:application/json');
echo json_encode($res);
dbConnectionClose();
exit;
?>