<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$breadcrumbes[0]['title'] = '<i class="material-icons">home</i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Communications';
$breadcrumbes[2]['title'] = 'Communications Approvals';
$breadcrumbes[0]['link'] = 'communications_approval.php';

$template = 'communications_approval.inc.php';
include_once 'layout/end.inc.php';
?>
