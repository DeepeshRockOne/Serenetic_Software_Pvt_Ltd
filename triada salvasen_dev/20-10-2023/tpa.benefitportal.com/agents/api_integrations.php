<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
	
$breadcrumbes[0]['title'] = '<i class="material-icons">home</i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Resources';
$breadcrumbes[2]['title'] = 'API Integrations';
$breadcrumbes[0]['link'] = 'api_integrations.php';

$description['ac_message'] = array(
    'ac_red_1' => array(
        'href' => 'agent_detail_v1.php?id=' . md5($_SESSION['agents']['id']),
        'title' => $_SESSION['agents']['rep_id'],
    ),
    'ac_message_1' => ' read API Integrations'
);
$desc = json_encode($description);
activity_feed(3, $_SESSION['agents']['id'], 'Agent', $_SESSION['agents']['id'], 'Agent', 'Agent Read API Integrations.', $_SESSION['agents']['fname'], $_SESSION['agents']['lname'], $desc);

$template = 'api_integrations.inc.php';
include_once 'layout/end.inc.php';
?>

