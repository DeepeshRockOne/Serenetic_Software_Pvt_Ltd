<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$breadcrumbes[0]['title'] = '<i class="material-icons">home</i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Plan Periods';

group_has_access(7);
$group_id = $_SESSION['groups']['id'];

$sqlCoveragePeriod = "SELECT gc.*,md5(gc.id) as id,if(gco.is_contribution='Y',1,0) AS product_contribution FROM group_coverage_period gc
				LEFT JOIN  group_coverage_period_offering as gco ON(gco.group_id=gc.group_id AND gco.group_coverage_period_id=gc.id AND gco.is_contribution='Y' AND gco.is_deleted='N')
				WHERE gc.is_deleted='N' AND gc.group_id=:group_id group by gc.id";
$resCoveragePeriod = $pdo->select($sqlCoveragePeriod,array(":group_id"=>$group_id));

$description['ac_message'] =array(
	'ac_red_1'=>array(
	  'href'=>$ADMIN_HOST.'/groups_details.php?id='.md5($_SESSION['groups']['id']),
	  'title'=>$_SESSION['groups']['rep_id'],
	),
	'ac_message_1' => 'read Resource',
	'ac_red_2'=>array(
		'href'=>$GROUP_HOST.'/coverage_periods.php',
		'title'=>'Plan Periods'
	),
); 

activity_feed(3, $_SESSION['groups']['id'], 'Group', $_SESSION['groups']['id'], 'Group','Group Read Resources', $_SESSION['groups']['fname'],$_SESSION['groups']['lname'],json_encode($description));

$template = 'coverage_periods.inc.php';
$layout = 'main.layout.php';
include_once 'layout/end.inc.php';
?>
