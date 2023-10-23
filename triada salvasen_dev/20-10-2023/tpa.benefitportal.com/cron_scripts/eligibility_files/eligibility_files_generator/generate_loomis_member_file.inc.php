<?php
// Loomis Member eligibility file
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
        $ExistsMembersData[$row['Employee ID Number']] = $row;
    }
    $ExistsMemberIds = array_column($csvData,'Employee ID Number');
    $ExistsMemberIds = array_unique($ExistsMemberIds);
  }
}

$prdArr = !empty($prd_ids) ? explode(",", $prd_ids) : array();

$selSql = "SELECT p.participants_id,p.employee_id,AES_DECRYPT(p.ssn,'" . $CREDIT_CARD_ENC_KEY . "') as ssn,p.fname,p.lname,p.mname,p.address,p.address2,
            p.city,p.state,p.zip,p.marital_status,p.gender,p.birth_date,p.employee_number,pp.effective_date,pp.termination_date,p.hire_date
           FROM participants p
           JOIN participants_products pp ON (p.id=pp.participants_id AND pp.is_deleted='N')
           WHERE pp.product_code in('".implode("','",$prdArr)."') AND p.is_deleted='N' AND p.person_code=1 $incr 
           GROUP BY p.id 
           ORDER BY p.id DESC";
$participantsRows = $pdo->select($selSql,$sch_params);
// pre_print($participantsRows);
  
  $RECORD_CODE = array(
    'Employee' => 1,
    'Spouse' => 2,
    'Child' => 3,
  );
  $GENDERS=array(
    'Male' => 'M',
    'Female' => 'F',
  );
  $MARITAL_STATUS=array(
    'Married' => 'M',
    'Single' => 'S',
  );

$csv_line = "\n";
$csv_seprator = ",";
$field_seprator = '"';
$header_row = "";
$content = ""; // we are not going to add header row at this time
$eligibility_content = ""; // we are not going to add header row at this time

$header_row = 'Employee ID Number'. $csv_seprator
          .'Social Security #' . $csv_seprator
          .'First name' . $csv_seprator
          .'Middle name' . $csv_seprator
          .'Last name' . $csv_seprator
          .'Address 1' . $csv_seprator
          .'Address 2' . $csv_seprator
          .'Address 3' . $csv_seprator
          .'City' . $csv_seprator
          .'State' . $csv_seprator
          .'Zip' . $csv_seprator
          .'Marital Status' . $csv_seprator
          .'Gender' . $csv_seprator
          .'Date of Birth' . $csv_seprator
          .'Identification number' . $csv_seprator
          .'Account #' . $csv_seprator
          .'Over all effective date' . $csv_seprator
          .'Over all term date' . $csv_seprator
          .'Location #' . $csv_seprator
          .'Location effective date' . $csv_seprator
          .'Hire date' . $csv_line;

$content .= $header_row;
if($generate_change_file){
  $eligibility_content .= $header_row;
}

if (!empty($participantsRows)) {
  foreach ($participantsRows as $row) {

    $row['fname'] = valid_csv_cell_value($row['fname']);
    $row['mname'] = valid_csv_cell_value($row['mname']);
    $row['lname'] = valid_csv_cell_value($row['lname']);
    $row['address'] = isset($row['address'])?valid_csv_cell_value($row['address']):'';
    $row['address2'] = isset($row['address2'])?valid_csv_cell_value($row['address2']):'';
 
     //  check if member is already exists or new member
      $addMember = false;
      if($generate_change_file){
        if(in_array($row['employee_id'],$ExistsMemberIds)){
          $is_Updated = compareLoomisMemberRow($ExistsMembersData[$row['employee_id']],$row);
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

    $birth_date = !empty($row["birth_date"]) ? date("Ymd",strtotime($row["birth_date"])) : '-';
    $effective_date = !empty($row["effective_date"]) ? date("Ymd",strtotime($row["effective_date"])) : '-';
    $termination_date = !empty($row["termination_date"]) ? date("Ymd",strtotime($row["termination_date"])) : '-';
    $hire_date = !empty($row["hire_date"]) ? date("Ymd",strtotime($row["hire_date"])) : '-';
   
    $tmp_row = '';
    $tmp_row .= $row['employee_id'] . $csv_seprator .//Employee ID Number
        $field_seprator.$row['ssn'] .$field_seprator. $csv_seprator .//ssn
        $field_seprator.$row['fname'] .$field_seprator. $csv_seprator .//fname
        $field_seprator.$row['mname'] .$field_seprator. $csv_seprator .//mname
        $field_seprator.$row['lname'] .$field_seprator. $csv_seprator .//lname
        $field_seprator.$row['address'] .$field_seprator. $csv_seprator .//address
        $field_seprator.$row['address2'] .$field_seprator. $csv_seprator .//address2
        $field_seprator. "" .$field_seprator. $csv_seprator .//address2
        $field_seprator.$row['city'] .$field_seprator. $csv_seprator .//city
        $field_seprator. getname("states_c",$row['state'],"short_name","name") .$field_seprator. $csv_seprator .//state
        $field_seprator.$row['zip'] .$field_seprator. $csv_seprator .//zip
        $field_seprator. checkIsset($MARITAL_STATUS[$row['marital_status']]) .$field_seprator. $csv_seprator .//marital_status
        $field_seprator. checkIsset($GENDERS[$row['gender']]) .$field_seprator. $csv_seprator .//gender
        $field_seprator. $birth_date .$field_seprator. $csv_seprator .//birth_date
        $field_seprator.$row['employee_number'] .$field_seprator. $csv_seprator .//employee_number
        $field_seprator. '2' .$field_seprator. $csv_seprator .//Account
        $field_seprator. $effective_date .$field_seprator. $csv_seprator .//marital_status
        $field_seprator. $termination_date .$field_seprator. $csv_seprator .//marital_status
         $field_seprator. '' .$field_seprator. $csv_seprator .//Location
          $field_seprator. '' .$field_seprator. $csv_seprator .//Location Effective Date
        $field_seprator. $hire_date .$field_seprator. $csv_line; //Hire Date

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