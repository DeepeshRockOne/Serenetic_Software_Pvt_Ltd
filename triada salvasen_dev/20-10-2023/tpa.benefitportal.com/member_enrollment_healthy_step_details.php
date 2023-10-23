<?php 
include_once __DIR__ . '/includes/connect.php'; 

$healthy_steps = !empty($_GET['healthy_steps']) ? explode("_", $_GET['healthy_steps']) : 0;
$resDetails = array();


if(!empty($healthy_steps)){
	$healthy_steps_id = implode(",", $healthy_steps);

	$incr = " AND pmpi.product_id in ($healthy_steps_id)";
	$sqlDetails = "SELECT p.id,p.name as healthy_step_name,pmpi.description AS healthy_step_description FROM prd_member_portal_information pmpi 
		JOIN prd_main p ON (pmpi.product_id = p.id AND p.is_deleted='N')
	where pmpi.is_deleted='N' $incr order by p.name desc";
	$resDetails = $pdo->select($sqlDetails);
}

$template = 'member_enrollment_healthy_step_details.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>