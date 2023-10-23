<?php
include_once __DIR__ . '/includes/connect.php';
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once "../includes/reporting_function.php";
$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Dashboard";
$breadcrumbes[1]['class'] = "Active";

if(!empty(get_admin_dashboard($_SESSION['admin']['id'])) && get_admin_dashboard($_SESSION['admin']['id']) == 'Support Dashboard'){
  redirect('support_dashboard.php');
}

$selProduct = "SELECT p.id,p.name,p.category_id,pc.title,p.type,p.product_code
                FROM prd_main as p
                LEFT JOIN prd_category as pc ON (p.category_id = pc.id AND pc.is_deleted='N') 
                WHERE p.parent_product_id = 0 AND p.type!='Fees' AND p.name != '' AND p.is_deleted = 'N' ORDER BY pc.title,p.name ASC";
$resProduct = $pdo->select($selProduct);

$companyArr = array();
if(!empty($resProduct)){
  foreach ($resProduct as $row) {
    if (!array_key_exists($row['title'], $companyArr)) {
      $companyArr[$row['title']] = array();
    }
    array_push($companyArr[$row['title']], $row);
  }
}

$exJs = array(
'thirdparty/bower_components/flot/excanvas.min.js',
'thirdparty/bower_components/flot/jquery.flot.js',
'thirdparty/bower_components/flot/jquery.flot.pie.js',
'thirdparty/bower_components/flot/jquery.flot.resize.js',
'thirdparty/bower_components/flot/jquery.flot.time.js',	
'thirdparty/bower_components/flot/jquery.flot.stack.js',
'thirdparty/bower_components/flot/jquery.flot.crosshair.js',
'thirdparty/bower_components/flot.tooltip/js/jquery.flot.tooltip.min.js',
'thirdparty/simscroll/jquery.slimscroll.min.js',
'thirdparty/highcharts/js/highcharts.js',
'thirdparty/highcharts/js/accessibility.js',
'thirdparty/jquery-match-height/js/jquery.matchHeight.js'
);

$page_title = "Dashboard";
$template = 'dashboard.inc.php';
$layout = 'main.layout.php';
include_once 'layout/end.inc.php';
?>