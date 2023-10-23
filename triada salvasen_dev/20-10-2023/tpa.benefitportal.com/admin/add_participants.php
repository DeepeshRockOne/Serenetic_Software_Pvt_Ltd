<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once __DIR__ . '/../includes/participants.class.php';
$participantsObj = new Participants();
has_access(89);
$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'User Groups';
$breadcrumbes[1]['link'] = 'javascript:void(0);';
$breadcrumbes[2]['title'] = 'Participants';
$breadcrumbes[2]['link'] = 'participants_listing.php';
$breadcrumbes[3]['title'] = '+ Participant(s)';
$breadcrumbes[3]['link'] = 'javascript:void(0);';

$participants_tag_res = $participantsObj->get_participants_tags();

$agent_sql = "SELECT id,rep_id,CONCAT(fname,' ',lname) as name 
				FROM customer 
				WHERE id=1 AND type IN('Agent')";
$agent_res = $pdo->selectOne($agent_sql);


$exStylesheets = array(
	'thirdparty/multiple-select-master/multiple-select.css'.$cache
);
$exJs = array(
	'thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache,
	'thirdparty/ajax_form/jquery.form.js'.$cache,
	'thirdparty/vue-js/vue.min.js'
);
$template = 'add_participants.inc.php';
include_once 'layout/end.inc.php';
?>