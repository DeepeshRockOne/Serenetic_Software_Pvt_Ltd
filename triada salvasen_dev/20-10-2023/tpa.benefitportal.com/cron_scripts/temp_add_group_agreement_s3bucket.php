<?php
ini_set('memory_limit', '-1');
ini_set('max_execution_time', '-1');
include_once dirname(__DIR__) . "/cron_scripts/connect.php";
include_once dirname(__DIR__) . '/includes/function.class.php';

require dirname(__DIR__) . '/libs/awsSDK/vendor/autoload.php';

use Aws\S3\S3Client;  
use Aws\Exception\AwsException;

$functionsList = new functionsList();

$total = $success = $fail = 0;
$groupSql  = "SELECT c.id,cs.signature_file,cs.id as customer_settings_id
			FROM customer c
			JOIN customer_settings cs ON(cs.customer_id = c.id)
			WHERE c.type = 'Group' AND c.status = 'Active' AND c.is_deleted = 'N' AND (cs.agent_contract_file IS NULL OR cs.agent_contract_file = '')";
$groupResult = $pdo->select($groupSql);

$total = !empty($groupResult) ? count($groupResult) : 0;

if(!empty($groupResult)){
	foreach ($groupResult as $key => $value) {
		$groupContractFileName = $functionsList->saveGroupContract($value['id'],$value['signature_file'],'temp_script');

		if(!empty($groupContractFileName)){
			$cs_update_params["agent_contract_file"] = $groupContractFileName;

			$cs_upd_where = array(
				'clause' => 'id = :id',
				'params' => array(
					':id' => $value['customer_settings_id'],
				),
			);

			$pdo->update('customer_settings', $cs_update_params, $cs_upd_where);
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