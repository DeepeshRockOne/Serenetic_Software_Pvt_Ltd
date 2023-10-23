<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$resArr = array();
$query = checkIsset($_POST['query']); 
$i=0;

if ($query != "") {
	
	$selSql = "SELECT id, fname, lname, lead_id, email, hire_date, birth_date, gender, zip,group_classes_id,
group_coverage_id,termination_date,employee_type
				FROM leads 
				WHERE sponsor_id=:sponsor_id AND (lead_id LIKE '%" . $query . "%' OR fname LIKE '%" . $query . "%' OR lname LIKE '%" . $query . "%' OR email LIKE '%" . $query . "%') and status!='Converted'";
	$whereSql=array(':sponsor_id'=>$_SESSION['groups']['id']);
	$rows = $pdo->select($selSql,$whereSql);  
	if (!empty($rows)) {
		foreach ($rows as $i => $row) {      
			$resArr[$i]['label'] = $row['fname']." ".$row['lname']." (".$row['lead_id'].")";
			$resArr[$i]['id'] = $row['id'];
			$resArr[$i]['fname'] = $row['fname'];
			$resArr[$i]['lname'] = $row['lname'];
			$resArr[$i]['email'] = $row['email'];
			$resArr[$i]['hire_date'] = $row['hire_date']!=""?date('m/d/Y',strtotime($row['hire_date'])):'';
			$resArr[$i]['birth_date'] = $row['birth_date']!=""? date('m/d/Y',strtotime($row['birth_date'])):'';
			$resArr[$i]['gender'] = $row['gender']!=""?$row['gender']:'';
			$resArr[$i]['zip'] = $row['zip']!=""?$row['zip']:'';
			$resArr[$i]['employee_type'] = $row['employee_type']!=""?$row['employee_type']:'';
			$resArr[$i]['termination_date'] = $row['termination_date']!=""?date('m/d/Y',strtotime($row['termination_date'])):'';
			$resArr[$i]['group_coverage_id'] = $row['group_coverage_id']!=""?$row['group_coverage_id']:'';
			$resArr[$i]['group_classes_id'] = $row['group_classes_id']!=""?$row['group_classes_id']:'';
			$i++;
		}
	}
}

header('Content-Type: application/json');
echo json_encode($resArr);
dbConnectionClose();
exit();
