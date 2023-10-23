<?php
ini_set('memory_limit', '-1');
ini_set('max_execution_time', '-1');
include_once dirname(__DIR__) . "/cron_scripts/connect.php";
include_once dirname(__DIR__) . '/includes/function.class.php';

$functionsList = new functionsList();

$total = $success = $fail = 0;
$adminSql  = "SELECT * FROM admin WHERE status = 'Active' AND is_deleted = 'N' AND (admin_contract_file IS NULL OR admin_contract_file = '')";
$adminResult = $pdo->select($adminSql);

$total = !empty($adminResult) ? count($adminResult) : 0;

if(!empty($adminResult)){
	foreach ($adminResult as $key => $value) {
		$adminContractFileName = $functionsList->saveAdminContract($value['id']);

		if(!empty($adminContractFileName)){
			$update_params["admin_contract_file"] = $adminContractFileName;

			$upd_where = array(
				'clause' => 'id = :id',
				'params' => array(
					':id' => $value['id'],
				),
			);

			$pdo->update('admin', $update_params, $upd_where);
			$success++;
		} else {
			$fail++;
		}
	}
}

echo "<br>Total = " . $total . " | Success = " . $success . " | Fail = " . $fail;
echo "<br>Completed";
dbConnectionClose();
exit;