<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$group_id = $_SESSION['groups']['id'];

$theme = isset($_POST['theme']) ? $_POST['theme'] : "";

$theme_color = $pdo->selectOne('SELECT theme_color from customer_settings where customer_id = :customer_id',array(':customer_id' => $group_id));

$response = array();

if(!empty($theme) && $theme_color['theme_color'] != $theme){

	$update_params = array('theme_color' => $theme);
	$upd_where = array(
		'clause' => 'customer_id = :id',
		'params' => array(
			':id' => $group_id,
		),
	);

	$pdo->update('customer_settings',$update_params,$upd_where);

	$description['ac_message'] =array(
	    'ac_red_1'=>array(
	      'href' => 'groups_details.php?id='.md5($group_id),
	      'title' => $_SESSION['groups']['rep_id'],
	    ),
	    'ac_message_1' =>' Changed theme color',
	  );

  $desc=json_encode($description);
  activity_feed(3,$_SESSION['groups']['id'],'Group',$_SESSION['groups']['id'],'Group','Changed theme color',"","",$desc,"","");

  $response['status'] = 'success';
  $response['message'] = "Theme changed successfully";

}

$response['status'] = 'success';
$response['message'] = "Theme changed successfully";
echo json_encode($response);
dbConnectionClose();
exit();
?>