<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Reporting";
$breadcrumbes[1]['link'] = 'set_reports.php';

$category_res = $pdo->select("SELECT * FROM $REPORT_DB.rps_category WHERE is_deleted='N' AND portal='Admin' ORDER BY order_by");
$report_res = $pdo->select("SELECT * FROM $REPORT_DB.rps_reports WHERE is_deleted='N' ORDER BY order_by");

if(!empty($category_res)) {
	foreach ($category_res as $ckey => $category_row) {
		foreach ($report_res as $rkey => $report_row) {
			if($report_row['category_id'] == $category_row['id']) {
				if(!isset($category_res[$ckey]['reports'])) {
					$category_res[$ckey]['reports'] = array();
				}
				$category_res[$ckey]['reports'][] = $report_row;
			}
		}
	}
}

$desc = array();
$desc['ac_message'] =array(
    'ac_red_1'=> array(
        'href'=> $ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
        'title'=> $_SESSION['admin']['display_id'],
    ),
    'ac_message_1' =>' read reporting module',
);
$desc = json_encode($desc);
activity_feed(3,$_SESSION['admin']['id'],'Admin',$_SESSION['admin']['id'],'Admin','Read Reporting Module.',"","",$desc);

$selectize=true;

$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css'.$cache);
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js'.$cache);
$template = 'set_reports.inc.php';
include_once 'layout/end.inc.php';
?>