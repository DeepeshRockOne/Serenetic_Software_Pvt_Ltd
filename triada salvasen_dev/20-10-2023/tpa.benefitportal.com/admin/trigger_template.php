<?php

include_once dirname(__FILE__) . '/layout/start.inc.php';
has_access(10);

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Emailar Dashboard';
$breadcrumbes[1]['link'] = 'emailer_dashboard.php';
$breadcrumbes[2]['title'] = 'Trigger Template';

$where_c = '';
if (isset($_GET['id'])) {
  $template_id = makeSafe($_GET['id']);
  $where_c .= " AND id=" . $_GET["id"];
}

$strQuery = "SELECT * FROM trigger_template WHERE 1 $where_c AND is_deleted='N' ORDER BY id DESC";
$rows = $pdo->select($strQuery);

$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css');
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js');
$page_title = "Trigger Template";
$template = "trigger_template.inc.php";
$layout = "main.layout.php";
include_once 'layout/end.inc.php';
?>
