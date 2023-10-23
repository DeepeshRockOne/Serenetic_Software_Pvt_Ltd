<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$id = isset($_REQUEST['id'])?$_REQUEST['id']:0;
$report_row = $pdo->selectOne("SELECT * FROM $REPORT_DB.rps_reports WHERE md5(id)=:id",array(":id" => $id));

if(!empty($report_row)) {
	$desc = array();
	$desc['ac_message'] = array(
	    'ac_red_1' => array(
	        'href' => 'agent_detail_v1.php?id=' . md5($_SESSION['groups']['id']),
	        'title' => $_SESSION['groups']['rep_id'],
	    ),
	    'ac_message_1' =>' read reporting details for <span class="text-action">'.$report_row['report_name'].'</span>',
	);
	$desc = json_encode($desc);
	activity_feed(3, $_SESSION['groups']['id'], 'Group', $_SESSION['groups']['id'], 'Group', 'Read Reporting Details.', $_SESSION['groups']['fname'], $_SESSION['groups']['lname'], $desc);
}

$exJs = array('thirdparty/simscroll/jquery.slimscroll.min.js');
$template = 'report_information.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';