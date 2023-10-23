<?php
ini_set('memory_limit', '-1');
ini_set('max_execution_time', '-1');
include_once dirname(__DIR__) . "/cron_scripts/connect.php";
include_once dirname(__DIR__) . "/cron_scripts/eligibility_file_functions.php";
include_once dirname(__DIR__) . '/includes/function.class.php';

require dirname(__DIR__) . '/libs/awsSDK/vendor/autoload.php';

use Aws\S3\S3Client;  
use Aws\Exception\AwsException;

$functionsList = new functionsList();

$total = $success = $fail = 0;
$agentSql  = "SELECT c.id,cs.signature_file,cs.id as customer_settings_id
			FROM customer c
			JOIN customer_settings cs ON(cs.customer_id = c.id)
			WHERE c.type = 'Agent' AND c.status = 'Active' AND c.is_deleted = 'N' AND (cs.agent_contract_file IS NULL OR cs.agent_contract_file = '')";
$agentResult = $pdo->select($agentSql);

$total = !empty($agentResult) ? count($agentResult) : 0;

if(!empty($agentResult)){
	foreach ($agentResult as $key => $value) {
		$agentContractFileName = $functionsList->saveAgentContract($value['id'],$value['signature_file'],'temp_script');

		if(!empty($agentContractFileName)){
			$cs_update_params["agent_contract_file"] = $agentContractFileName;

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