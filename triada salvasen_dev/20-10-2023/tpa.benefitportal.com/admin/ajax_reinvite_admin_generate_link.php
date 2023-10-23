<?php 
include_once __DIR__ . '/includes/connect.php';

if (isset($_GET['id'])) {

	$id = $_GET['id'];
	$ent_res = $pdo->selectOne("SELECT fname,lname,id,display_id from admin where md5(id) =:id",array(":id"=>$id));
	$key = md5($ent_res['fname'] . $ent_res['lname'] . sha1(time()));

	$update_params = array(
		'invite_key' => $key,
		'invite_at' => 'msqlfunc_NOW()',
		'updated_at' => 'msqlfunc_NOW()',
	);

	$update_where = array(
		'clause' => 'md5(id) = :id',
		'params' => array(
			':id' => makeSafe($id),
		),
	);

	$update_status = $pdo->update('admin', $update_params, $update_where);
	$extra['gen_link'] = 'Inactive to '. $ADMIN_HOST . '/sign_up.php?key='.$key;
	$extra['user_display_id'] = $_SESSION['admin']['display_id'];
	$description['description'] = $_SESSION['admin']['display_id'].' updated admin '.$ent_res['fname'].' '.$ent_res['lname'].'('.$ent_res['display_id'].')'.' Inactive to '. $ADMIN_HOST . '/sign_up.php?key='.$key;
	activity_feed(3, $_SESSION['admin']['id'], 'Admin',  $ent_res['id'], 'update_old_to_new','Admin Generated Link', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description),'',json_encode($extra));
  	$result['status'] = "success";

} else {
	$result['status'] = "fail";
}

header('Content-type: application/json');
echo json_encode($result); 
dbConnectionClose();
exit;	
?>