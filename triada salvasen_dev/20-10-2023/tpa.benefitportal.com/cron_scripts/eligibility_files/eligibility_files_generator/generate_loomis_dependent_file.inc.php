<?php
// Loomis Dependent eligibility file
$sch_params = array();
$incr = '';
$generate_change_file = false;
if($file_type == "full_file"){
  $sch_params[":to_date"] = date("Y-m-d",strtotime($to_date));
   $incr .= " AND pp.effective_date <= :to_date";
}else if($file_type == "schedule_change_file" || $file_type == "add_change_file"){

  if($file_type == "add_change_file"){
    $sch_params[":since_date"] = date("Y-m-d",strtotime($since_date));
    $sch_params[":to_date"] = date("Y-m-d",strtotime($to_date));
    $incr .= " AND (DATE(p.created_at) >= :since_date OR pp.termination_date >= :since_date OR pp.effective_date >= :since_date)";
    $incr .= " AND (DATE(p.created_at) <= :to_date OR pp.termination_date <= :to_date OR pp.effective_date <= :to_date)";
  } 

  $file = (__DIR__) . "/" .$file_key . "_Main_File.csv";

  if(!file_exists($file)){
    fopen($file,"w");  
  }

  $csvData = csvToArraywithFields($file);
  if(!empty($csvData)){
    $generate_change_file = true;
  }
  if($generate_change_file){
    $ExistsMembers = $csvData;
    $ExistsMembersData = array();
    foreach ($ExistsMembers as $key => $row) {
        $ExistsMembersData[$row["Employee ID Number"].'-'.$row["Dep. Sequence"]] = $row;
    }
    $ExistsMemberIds = array_column($csvData,'Employee ID Number');
    $ExistsMemberIds = array_unique($ExistsMemberIds);
  }
}

$prdArr = !empty($prd_ids) ? explode(",", $prd_ids) : array();

$selSql = "SELECT m.employee_id,AES_DECRYPT(m.ssn,'" . $CREDIT_CARD_ENC_KEY . "') as ssn,AES_DECRYPT(p.ssn,'" . $CREDIT_CARD_ENC_KEY . "') as dep_ssn,p.fname,p.lname,p.mname,
            p.gender,p.birth_date,p.person_code
           FROM participants p
           JOIN participants_products pp ON (p.id=pp.participants_id AND pp.is_deleted='N')
           LEFT JOIN participants m ON(m.employee_id=p.employee_id AND m.person_code=1)
           WHERE pp.product_code in('".implode("','",$prdArr)."') AND p.is_deleted='N' AND p.person_code!=1 $incr 
           GROUP BY p.id 
           ORDER BY p.person_code ASC";
$participantsRows = $pdo->select($selSql,$sch_params);
  
  $GENDERS=array(
    'Male' => 'M',
    'Female' => 'F',
  );
 

$csv_line = "\n";
$csv_seprator = ",";
$field_seprator = '"';
$header_row = "";
$content = ""; // we are not going to add header row at this time
$eligibility_content = ""; // we are not going to add header row at this time

$header_row = 'Employee ID Number'. $csv_seprator
          .'Employee Social Security' . $csv_seprator
          .'Dep. Sequence' . $csv_seprator
          .'Dep. SSN' . $csv_seprator
          .'First name' . $csv_seprator
          .'Middle name' . $csv_seprator
          .'Last name' . $csv_seprator
          .'Gender' . $csv_seprator
          .'Date of Birth' . $csv_seprator
          .'Relationship to Employee' . $csv_line;

$content .= $header_row;
if($generate_change_file){
  $eligibility_content .= $header_row;
}

if (!empty($participantsRows)) {
  foreach ($participantsRows as $row) {

    $row['fname'] = valid_csv_cell_value($row['fname']);
    $row['mname'] = valid_csv_cell_value($row['mname']);
    $row['lname'] = valid_csv_cell_value($row['lname']);

    $depSequence = ($row["person_code"] - 1);

    if(strlen($depSequence) == 1){
          $depSequence = "0".$depSequence;
    }
  
     //  check if member is already exists or new member
      $addMember = false;
      if($generate_change_file){
        if(in_array($row['employee_id'],$ExistsMemberIds)){
          $is_Updated = compareLoomisDependentRow($ExistsMembersData[$row['employee_id'].'-'.$depSequence],$row);
          //  if member detail is updates then add member to eligibility file
          if($is_Updated === true) {
            $addMember = true;
          } else {
            $addMember = false;
          }
        }else{
          //  if new member then add member to eligibility file
          $addMember = true;
        }
      } 

      $relationShip = "Other";
      if($row["person_code"] == 2){
        $relationShip = "Spouse";
      }else if($row["person_code"] > 2){
        $relationShip = "Child";
      }

    $birth_date = !empty($row["birth_date"]) ? date("Ymd",strtotime($row["birth_date"])) : '-';

    $tmp_row = '';
    $tmp_row .= $row['employee_id'] . $csv_seprator .
        $field_seprator.$row['ssn'] .$field_seprator. $csv_seprator .
        $field_seprator.$depSequence .$field_seprator. $csv_seprator .
        $field_seprator.$row['dep_ssn'] .$field_seprator. $csv_seprator .
        $field_seprator.$row['fname'] .$field_seprator. $csv_seprator .
        $field_seprator.$row['mname'] .$field_seprator. $csv_seprator .
        $field_seprator.$row['lname'] .$field_seprator. $csv_seprator .
        $field_seprator. checkIsset($GENDERS[$row['gender']]) .$field_seprator. $csv_seprator .
        $field_seprator. $birth_date .$field_seprator. $csv_seprator .
        $field_seprator. $relationShip .$field_seprator. $csv_line;

    $content .= $tmp_row;
    if($addMember) {
      $eligibility_content .= $tmp_row;
    }
  }
}

// pre_print($content);
if($content != '' && ($file_type == "schedule_change_file" || $file_type == "add_change_file")){
  $file_update = file_put_contents($file,$content);
  if($generate_change_file){
    $content = $eligibility_content;
  }
}
?>