<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Commissions";
$breadcrumbes[1]['link'] = "commission_builder.php";
$breadcrumbes[2]['title'] = "Builder";
$breadcrumbes[3]['title'] = "Manage Commissions";
$page_title = "Manage Commissions";

$template = "manage_commission.inc.php";
include_once 'layout/end.inc.php';
?>