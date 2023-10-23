<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';


$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Reporting';


$template = 'reports_export.inc.php';
include_once 'layout/end.inc.php';
?>
