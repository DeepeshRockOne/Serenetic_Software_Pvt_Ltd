<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "E-Tickets";
$breadcrumbes[1]['link'] = "etickets.php";
$breadcrumbes[2]['title'] = "Manage E-Tickets";
$breadcrumbes[2]['link'] = 'manage_etickets.php';

$template = 'manage_etickets.inc.php';
include_once 'layout/end.inc.php';
?>
