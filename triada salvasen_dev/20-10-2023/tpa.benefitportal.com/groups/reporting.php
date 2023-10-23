<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$breadcrumbes[0]['title'] = '<i class="material-icons">home</i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Reporting';

$category_res = $pdo->select("SELECT * FROM $REPORT_DB.rps_category WHERE is_deleted='N' AND portal='Group' ORDER BY order_by");
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

$description['ac_message'] = array(
    'ac_red_1' => array(
        'href' => 'agent_detail_v1.php?id=' . md5($_SESSION['groups']['id']),
        'title' => $_SESSION['groups']['rep_id'],
    ),
    'ac_message_1' => ' read Reporting'
);
$desc = json_encode($description);
activity_feed(3, $_SESSION['groups']['id'], 'Group', $_SESSION['groups']['id'], 'Group', 'Group Read Reporting.', $_SESSION['groups']['fname'], $_SESSION['groups']['lname'], $desc);

$template = 'reporting.inc.php';
include_once 'layout/end.inc.php';
?>

