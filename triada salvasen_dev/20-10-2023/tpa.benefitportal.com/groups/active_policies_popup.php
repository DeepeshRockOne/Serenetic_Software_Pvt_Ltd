<?php
include_once __DIR__ . '/includes/connect.php';

$coverage_id = $_GET['id'];
$group_id = $_GET['group_id'];

$sqlPolicy="SELECT COUNT(c.id) AS enrolled,p.name,p.id,p.product_code 
  FROM customer c 
  JOIN customer_settings cs ON (c.id=cs.customer_id)
  JOIN website_subscriptions ws ON (c.id = ws.customer_id)
  JOIN prd_main p ON (p.id = ws.product_id AND p.type='Normal')
  WHERE c.status='Active' AND ws.status='Active' AND c.sponsor_id=:group_id AND cs.group_coverage_period_id =:coverage_id AND c.is_deleted='N' GROUP BY ws.product_id";
$resPolicy=$pdo->select($sqlPolicy,array(":group_id"=>$group_id,":coverage_id"=>$coverage_id));

$total_policy = 0;

if(!empty($resPolicy)) {
	foreach ($resPolicy as $key => $value) {
		$total_policy += $value['enrolled'];
	}
}

if(isset($_GET['action_type']) && $_GET['action_type'] == 'export_excel') {
	$csv_line = "\n";
    $csv_seprator = ",";
    $field_seprator = '"';
    $content = "";

    $content .= $field_seprator . 'PRODUCT NAME' . $field_seprator . $csv_seprator 	.$field_seprator . 'MEMBER ID' . $field_seprator . $csv_seprator
    		.$field_seprator . 'POLICY ID' . $field_seprator . $csv_seprator . $csv_line;

   	$sqlPolicy="SELECT c.rep_id,p.name,p.id,p.product_code,ws.website_id 
	  FROM customer c 
	  JOIN customer_settings cs ON (c.id=cs.customer_id)
	  JOIN website_subscriptions ws ON (c.id = ws.customer_id)
	  JOIN prd_main p ON (p.id = ws.product_id AND p.type='Normal')
	  WHERE c.status='Active' AND ws.status='Active' AND c.sponsor_id=:group_id AND cs.group_coverage_period_id =:coverage_id AND c.is_deleted='N' GROUP BY ws.id";
	$exResPolicy=$pdo->select($sqlPolicy,array(":group_id"=>$group_id,":coverage_id"=>$coverage_id));
    if(!empty($exResPolicy)){
    	foreach ($exResPolicy as $key => $value) {
    		$content .= $field_seprator . str_replace(',', ' ', $value['name']) . $field_seprator . $csv_seprator 	.$field_seprator . $value['rep_id'] . $field_seprator . $csv_seprator
    		.$field_seprator . $value['website_id'] . $field_seprator . $csv_seprator . $csv_line;
    	}
    }
    $csv_filename = "GROUP_ACTIVE_POLICIES-".date('mdY').".csv";
    header('Content-Type: application/excel');
    header('Content-disposition: attachment;filename=' . $csv_filename);
    echo $content;
    exit();
}

$template = 'active_policies_popup.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>
