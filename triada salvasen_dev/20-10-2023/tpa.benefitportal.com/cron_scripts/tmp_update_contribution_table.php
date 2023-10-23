<?php
ini_set('memory_limit', '-1');
ini_set('max_execution_time', '-1');
include_once dirname(__DIR__) . "/cron_scripts/connect.php";

$sel_group = "SELECT gcp.group_id,gcc.id,gcc.group_coverage_period_id AS gcc_group_coverage_period_id,
			gcp.id AS group_coverage_period_id,gcc.group_coverage_period_offering_id
			FROM group_coverage_period_contributions gcc
			JOIN group_coverage_period gcp ON(gcp.id!=gcc.group_coverage_period_id AND gcp.group_id=gcc.group_id)
			JOIN group_coverage_period_offering gco ON(gcp.id=gco.group_coverage_period_id AND gco.id=gcc.group_coverage_period_offering_id AND gco.is_deleted='N')
			WHERE gcc.is_deleted='N'";
$groupContribution  = $pdo->select($sel_group);
if(!empty($groupContribution)){
	foreach ($groupContribution as $contribution) {
		$update_params =[
			'group_coverage_period_id' => $contribution['group_coverage_period_id']
		];
		$update_where = array(
			'clause' => "id=:id AND group_id=:group_id",
			'params' => array(
				':id' => $contribution['id'],
				':group_id' => $contribution['group_id'],
			)
		);
		$pdo->update('group_coverage_period_contributions',$update_params,$update_where);
	}
}
dbConnectionClose();
?>