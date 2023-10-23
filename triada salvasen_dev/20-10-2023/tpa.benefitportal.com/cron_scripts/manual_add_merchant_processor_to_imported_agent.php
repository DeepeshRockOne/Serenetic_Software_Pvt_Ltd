<?php
include_once dirname(__DIR__) . "/cron_scripts/connect.php";

//Add agent into payment_master_assigned_agent
$merchant_processors = $pdo->select("SELECT id,created_at from payment_master where is_deleted='N' AND is_assigned_to_all_agent='Y'");
$agents = $pdo->selectOne("SELECT GROUP_CONCAT(id) as ids from customer where is_deleted='N' AND type='Agent'");
if(!empty($merchant_processors) && !empty($agents['ids'])){
	$agentIds = explode(',',$agents['ids']);
	foreach($merchant_processors as $processor){
		foreach($agentIds as $agent_id){
			$assigned_processor = $pdo->selectOne("SELECT id from payment_master_assigned_agent where payment_master_id=:id AND agent_id=:agent_id and is_deleted='N'",array(":id"=>$processor['id'],":agent_id"=>$agent_id));
			if(empty($assigned_processor['id'])){
				// echo "AgentId : ".$agent_id."<br>";
				// echo "ProcessorId : ".$processor['id'];
				// echo "<br><br>";
				$ins_param = array(
					"agent_id"=>$agent_id,
					"payment_master_id"=>$processor['id'],
					"created_at"=>$processor['created_at'],
				);
				$pdo->insert("payment_master_assigned_agent",$ins_param);
			}
		}
	}
}
echo "Completed";
dbConnectionClose();
exit;
?>