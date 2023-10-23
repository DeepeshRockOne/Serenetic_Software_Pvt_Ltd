<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
include_once __DIR__ . '/../includes/function.class.php';
$functionsList = new functionsList();

$breadcrumbes[0]['title'] = '<i class="material-icons">home</i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Plan Periods';
$breadcrumbes[1]['link'] = 'coverage_periods.php';
$breadcrumbes[2]['title'] = '+ Plan Periods';

group_has_access(7);

$group_id = $_SESSION['groups']['id'];
$coverage_id = !empty($_GET['coverage']) ? $_GET['coverage'] : '';
$tmp_coverage_id = !empty($_GET['coverage']) ? $_GET['coverage'] : '';
$is_clone = !empty($_GET['clone']) ? $_GET['clone'] : 'N';

$sqlCoverage = "SELECT * FROM group_coverage_period where is_deleted='N' AND md5(id)=:id";
$resCoverage = $pdo->selectOne($sqlCoverage,array(":id"=>$coverage_id));

if(!empty($resCoverage)){
	$coverage_period_name=$resCoverage['coverage_period_name'];
	$display_id=$resCoverage['display_id'];
	$status=$resCoverage['status'];
	$coverage_period_start=date('m/d/Y',strtotime($resCoverage['coverage_period_start']));
	$coverage_period_end=date('m/d/Y',strtotime($resCoverage['coverage_period_end']));

	if($is_clone == 'Y'){
		$coverage_id = 0;
	}else{
		$description['ac_message'] = array(
	        'ac_red_1' => array(
	            'href'=>$ADMIN_HOST.'/groups_details.php?id='.md5($_SESSION['groups']['id']),
	        	'title'=>$_SESSION['groups']['rep_id'],
	        ),
	        'ac_message_1' => ' read Plan Period ',
	        'ac_red_2' => array(
	            'title' => $display_id,
	        ),
	    );
	    $desc = json_encode($description);
	   
	    activity_feed(3, $_SESSION['groups']['id'], 'Group', $resCoverage['id'], 'group_coverage_period', 'Group Read Plan Period', $_SESSION['groups']['fname'], $_SESSION['groups']['lname'], $desc);
	}
	
}

if(empty($coverage_id) || $is_clone =='Y'){
	$display_id=$functionsList->generateCoverageCode();	
}


$exJs = array('thirdparty/masked_inputs/jquery.inputmask.bundle.js',);

$template = 'add_coverage_periods.inc.php';
$layout = 'main.layout.php';
include_once 'layout/end.inc.php';
?>
