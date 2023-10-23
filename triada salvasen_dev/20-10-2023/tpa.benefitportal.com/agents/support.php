<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
require_once dirname(__DIR__) . '/includes/chat.class.php';
$LiveChat = new LiveChat();

agent_has_access(23);
$breadcrumbes[0]['title'] = '<i class="material-icons">home</i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Resources';
$breadcrumbes[2]['title'] = 'Support';
$breadcrumbes[0]['link'] = 'support.php';

$agentPortal = array();

$allPortalRsources = $pdo->select("SELECT * from portal_resources where is_deleted='N' AND portal_type = 'agent' ORDER BY FIELD(module_name,'dashboard','Enroll','Website','Book of Business','My Production','Resources')");

if(!empty($allPortalRsources)){
    foreach($allPortalRsources as $value){
        array_push($agentPortal,$value);
    }
}

$description['ac_message'] = array(
    'ac_red_1' => array(
        'href' => 'agent_detail_v1.php?id=' . md5($_SESSION['agents']['id']),
        'title' => $_SESSION['agents']['rep_id'],
    ),
    'ac_message_1' => ' read Support Page'
);
$desc = json_encode($description);
activity_feed(3, $_SESSION['agents']['id'], 'Agent', $_SESSION['agents']['id'], 'Agent', 'Agent Read Support Page.', $_SESSION['agents']['fname'], $_SESSION['agents']['lname'], $desc);
$template = 'support.inc.php';
include_once 'layout/end.inc.php';
?>

