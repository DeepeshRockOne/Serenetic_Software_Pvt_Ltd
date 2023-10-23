<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$agent_id = $_SESSION['agents']['id'];

$theme = isset($_POST['theme']) ? $_POST['theme'] : "";

$theme_color = $pdo->selectOne('SELECT theme_color from customer_settings where customer_id = :customer_id',array(':customer_id' => $agent_id));

$response = array();

if(!empty($theme) && $theme_color['theme_color'] != $theme){

	$update_params = array('theme_color' => $theme);
	$upd_where = array(
		'clause' => 'customer_id = :id',
		'params' => array(
			':id' => $agent_id,
		),
	);

	$pdo->update('customer_settings',$update_params,$upd_where);

	$description['ac_message'] =array(
	    'ac_red_1'=>array(
	      'href' => 'agent_detail_v1.php?id='.md5($_SESSION['agents']['id']),
	      'title' => $_SESSION['agents']['rep_id'],
	    ),
	    'ac_message_1' =>' Changed theme color',
	  );

  $desc=json_encode($description);
  activity_feed(3,$_SESSION['agents']['id'],'Agent',$_SESSION['agents']['id'],'Agent','Changed theme color',"","",$desc,"","");

  $response['status'] = 'success';
  $response['message'] = "Theme changed successfully";

}

$response['status'] = 'success';
$response['message'] = "Theme changed successfully";
echo json_encode($response);
dbConnectionClose();
exit();
?>