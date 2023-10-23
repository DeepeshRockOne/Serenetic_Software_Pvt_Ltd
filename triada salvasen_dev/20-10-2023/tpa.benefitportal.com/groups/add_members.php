<?php
include_once __DIR__ . '/includes/connect.php';
group_has_access(2);
$breadcrumbes[0]['title'] = '<i class="material-icons">home</i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'User Groups';
$breadcrumbes[1]['link'] = 'member_listing.php';
$breadcrumbes[2]['title'] = 'Members';
$breadcrumbes[2]['link'] = 'add_members.php';

$template = 'add_members.inc.php';
$layout = 'main.layout.php';
include_once 'layout/end.inc.php';
?>
