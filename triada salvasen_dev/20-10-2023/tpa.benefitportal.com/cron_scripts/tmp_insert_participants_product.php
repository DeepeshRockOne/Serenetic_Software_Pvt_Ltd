<?php
ini_set('memory_limit', '-1');
ini_set('max_execution_time', '-1');
include_once dirname(__DIR__) . "/cron_scripts/connect.php";

$selProduct = "SELECT p2.id AS newParticipantId,p1p.* 
			FROM deleted_participants del
			JOIN participants p1 ON(del.participant_id=p1.id AND p1.is_deleted='Y')
			JOIN `participants` p2 ON(p2.employee_id = p1.employee_id AND CONCAT('0',p1.person_code) = p2.person_code AND p1.id != p2.id AND p2.is_deleted='N')
			JOIN participants_products p1p ON(p1.id=p1p.participants_id AND p1p.product_code NOT IN(SELECT product_code FROM participants_products WHERE participants_id=p2.id))
			WHERE DATE(p1p.updated_at)='2021-06-19'
			GROUP BY p1p.id ORDER BY p2.id";

$resProduct = $pdo->select($selProduct);

if(!empty($resProduct)){
	foreach ($resProduct as $key => $row) {

		$participantsProducts = array(
            "participants_id" => $row["newParticipantId"],
            "product_code" => $row["product_code"],
            "plan_identifier" => $row["plan_identifier"],
            "plan_coverage_tier" => $row["plan_coverage_tier"],
            "plan_coverage_desc" => $row["plan_coverage_desc"],
            "org_effective_date" => $row["org_effective_date"],
            "event_type" => $row["event_type"],
            "event_description" => $row["event_description"],
            "event_date" => $row["event_date"],
            "org_effective_date" => $row["org_effective_date"],
            "effective_date" => $row["effective_date"],
            "termination_date" => $row["termination_date"],
            "relationship" => $row["relationship"],
            "updated_at" => 'msqlfunc_NOW()',
            "created_at" => 'msqlfunc_NOW()',
        );
		$pdo->insert("participants_products",$participantsProducts);
		
	}
}

pre_print("Completed");
dbConnectionClose();
?>