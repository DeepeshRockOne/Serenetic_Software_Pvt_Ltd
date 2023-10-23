<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$pmpm_id = isset($_GET['id']) ? $_GET['id'] : 0;

$products = $pdo->select("SELECT p.name,p.product_code,p.id,p.status 
						  FROM prd_main p
						  JOIN pmpm_commission_rule_assign_product pcrap on(p.id = pcrap.product_id)
						  WHERE pcrap.commission_id = :id AND pcrap.is_deleted = 'N' AND p.is_deleted = 'N' group by p.id order by p.name ASC",array(':id' => $pmpm_id));

$agent_details = $pdo->selectOne("SELECT c.rep_id,CONCAT(c.fname,' ',c.lname) as name 
								  FROM customer c
								  JOIN pmpm_commission pc on c.id = pc.agent_id
								  WHERE pc.id = :id",array(':id' => $pmpm_id));


$template = 'pmpm_prd_popup.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>