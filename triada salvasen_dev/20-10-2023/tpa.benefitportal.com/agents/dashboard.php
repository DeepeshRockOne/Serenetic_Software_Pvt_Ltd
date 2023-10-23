<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$breadcrumbes[0]['title'] = '<i class="material-icons">home</i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Dashboard';
$page_title = "Dashboard";

$is_loa_agent = false;
if($_SESSION['agents']['agent_coded_level'] == "LOA") {
	$is_loa_agent = true;
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
	'thirdparty/jquery-match-height/js/jquery.matchHeight.js',
	'thirdparty/raphael/raphael-min.js',
	'thirdparty/morrisjs/morris.min.js',
);
$template = 'dashboard.inc.php';
include_once 'layout/end.inc.php';
?>