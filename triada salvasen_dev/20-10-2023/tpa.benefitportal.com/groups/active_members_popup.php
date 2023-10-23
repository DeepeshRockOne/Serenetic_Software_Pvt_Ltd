<?php
include_once __DIR__ . '/includes/connect.php';

$coverage_id = $_GET['id'];
$group_id = $_GET['group_id'];

$sqlMember="SELECT c.rep_id,c.fname,c.lname,c.joined_date
  FROM customer c 
  JOIN customer_settings cs ON (c.id=cs.customer_id)
  WHERE c.status='Active' AND c.sponsor_id=:group_id AND cs.group_coverage_period_id =:coverage_id AND c.is_deleted='N'";
$resMember=$pdo->select($sqlMember,array(":group_id"=>$group_id,":coverage_id"=>$coverage_id));

$total_member = count($resMember);

if(isset($_GET['action_type']) && $_GET['action_type'] == 'export_excel') {
	$csv_line = "\n";
    $csv_seprator = ",";
    $field_seprator = '"';
    $content = "";

    $content .= $field_seprator . 'MEMBER ID' . $field_seprator . $csv_seprator 	.$field_seprator . 'ADDED DATE' . $field_seprator . $csv_seprator
    		.$field_seprator . 'MEMBER NAME' . $field_seprator . $csv_seprator . $csv_line;
    if(!empty($resMember)){
    	foreach ($resMember as $key => $value) {
    		$content .= $field_seprator . $value['rep_id'] . $field_seprator . $csv_seprator 	.$field_seprator . date('m/d/Y',strtotime($value['joined_date'])) . $field_seprator . $csv_seprator
    		.$field_seprator . $value['fname'] .' '. $value['lname'] . $field_seprator . $csv_seprator . $csv_line;
    	}
    }
    $csv_filename = "GROUP_ACTIVE_MEMBERS-".date('mdY').".csv";
    header('Content-Type: application/excel');
    header('Content-disposition: attachment;filename=' . $csv_filename);
    echo $content;
    exit();
}

$template = 'active_members_popup.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>
