<?php

include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once dirname(__FILE__) . "/../includes/template_function.php";
has_access(10);

$breadcrumbes[0]['title'] = '<i class="icon-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Emailar Dashboard';
$breadcrumbes[1]['link'] = 'emailer_dashboard.php';
$breadcrumbes[2]['title'] = 'Trigger Template';
$breadcrumbes[2]['link'] = 'trigger_template.php';
$breadcrumbes[3]['title'] = 'View Trigger Template';

$id = $_GET['id'];

if (isset($_GET['id']) && $_GET['id'] != '') {
  $template_data = generate_trigger_template($id);
} else {
  redirect('trigger_template.php');
}


$template = "view_template.inc.php";
$layout = "main.layout.php";
include_once 'layout/end.inc.php';
?>
