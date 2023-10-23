<?php 
include_once dirname(__FILE__) . '/layout/start.inc.php';
has_access(10);

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Emailar Dashboard';
$breadcrumbes[1]['link'] = 'emailer_dashboard.php';
$breadcrumbes[2]['title'] = 'Trigger Template';
$breadcrumbes[2]['link'] = 'trigger_template.php';
$breadcrumbes[3]['title'] = 'Add Trigger Template';

$strQuery_images = "SELECT * FROM trigger_images ORDER BY id DESC";
$rows_images = $pdo->select($strQuery_images);
foreach ($rows_images as $value) {
  $imgArray[$value['id']] = $value;
}

$strQuery_address = "SELECT * FROM trigger_address ORDER BY id DESC";
$rows_address = $pdo->select($strQuery_address);
foreach ($rows_address as $value) {
  $addressArray[$value['id']] = $value;
}

$strQuery_footer = "SELECT * FROM trigger_footer ORDER BY id DESC";
$rows_footer = $pdo->select($strQuery_footer);
foreach ($rows_footer as $value) {
  $footerArray[$value['id']] = $value;
}

$company_sql = "SELECT * FROM company where id=3 ";
$company_res = $pdo->select($company_sql);

$exJs = array('thirdparty/simscroll/jquery.slimscroll.min.js');

$page_title = 'Add Trigger Template';
$template = "add_trigger_template.inc.php";
$layout = "main.layout.php";
include_once 'layout/end.inc.php';
?>
