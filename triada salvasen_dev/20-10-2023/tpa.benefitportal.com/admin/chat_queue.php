<?php

include_once dirname(__FILE__) . '/layout/start.inc.php';

$breadcrumbes[0]['title'] = '<i class="icon-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Dashboard';
$breadcrumbes[1]['link'] = 'support_dashboard.php';
$breadcrumbes[2]['title'] = 'Chat Queue';

$template = 'chat_queue.inc.php';
include_once 'layout/end.inc.php';
?>