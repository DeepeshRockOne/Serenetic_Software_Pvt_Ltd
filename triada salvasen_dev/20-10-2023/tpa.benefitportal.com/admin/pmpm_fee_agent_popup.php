<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$pmpm_fee_id = isset($_GET['id']) ? $_GET['id'] : 0;

$products = $pdo->select("SELECT c.rep_id,concat(c.fname,' ',c.lname) as agent_name,c.status 
						  FROM customer c
						  JOIN pmpm_commission_rule_assign_agent pcraa on(c.id = pcraa.agent_id)
						  WHERE pcraa.rule_id = :id AND pcraa.is_deleted = 'N' group by c.id order by c.fname ASC",array(':id' => $pmpm_fee_id));

// $agent_details = $pdo->selectOne("SELECT c.rep_id,CONCAT(c.fname,' ',c.lname) as name 
// 								  FROM customer c
// 								  JOIN pmpm_commission pc on c.id = pc.agent_id
// 								  WHERE pc.id = :id",array(':id' => $pmpm_id));


$template = 'pmpm_fee_agent_popup.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>