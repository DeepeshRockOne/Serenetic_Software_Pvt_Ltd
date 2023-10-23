<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
has_access(67);
$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Communications";
$breadcrumbes[2]['title'] = "Unsubscribes";
$breadcrumbes[2]['class'] = "unsubscribes_dashboard.php";
	
	// read unsubscribe lists activity fee code
		$description['ac_message'] =array(
		  'ac_red_1'=>array(
		    'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
		    'title'=>$_SESSION['admin']['display_id'],
		  ),
		  'ac_message_1' =>' read unsubscribe lists',
		); 

		activity_feed(3, $_SESSION['admin']['id'], 'Admin', 0, 'unsubscribes','Viewed Unsubscribes', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));

$template = 'unsubscribes_dashboard.inc.php';
include_once 'layout/end.inc.php';
?>
