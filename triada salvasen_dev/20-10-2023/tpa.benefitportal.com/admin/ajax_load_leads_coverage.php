<?php
	include_once dirname(__FILE__) . '/layout/start.inc.php';

	$class_id = $_POST['class'];

	$sqlCoverage = "SELECT gc.id,gc.coverage_period_name FROM group_coverage_period gc 
					JOIN group_coverage_period_offering gco ON (gc.id = gco.group_coverage_period_id AND gco.is_deleted='N')
					WHERE gco.class_id=:class_id AND gco.status='Active' group by gc.id";
	$resCoverage = $pdo->select($sqlCoverage,array(":class_id"=>$class_id));
	ob_start();
		?>
		<option value=""></option>
		<?php if(!empty($resCoverage)){
			foreach ($resCoverage as $key => $value) { ?>
				<option value="<?= $value['id'] ?>"><?= $value['coverage_period_name'] ?></option>	
			<?php } ?>
		<?php } ?>

	<?php
	  
	$result = array();	
	$result['html'] = ob_get_clean();
	$result['status'] = "success"; 
	  
	header('Content-type: application/json');
	echo json_encode($result);
	dbConnectionClose(); 
	exit;
?>