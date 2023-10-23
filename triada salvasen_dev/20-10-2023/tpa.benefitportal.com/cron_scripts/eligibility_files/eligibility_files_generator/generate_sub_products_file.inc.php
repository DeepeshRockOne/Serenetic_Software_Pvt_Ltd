<?php
//  MEC File
$sch_params = array();
$incr = '';
$generate_change_file = false;
$generate_error_file = false;


if($file_type == "full_file"){
  $sch_params[":to_date"] = date("Y-m-d",strtotime($to_date));
   $incr .= " AND w.eligibility_date <= :to_date)";
}else if($file_type == "add_change_file"){
  $sch_params[":since_date"] = date("Y-m-d",strtotime($since_date));
  $sch_params[":to_date"] = date("Y-m-d",strtotime($to_date));
  $incr .= " AND (DATE(c.created_at) >= :since_date OR w.term_date_set >= :since_date OR w.eligibility_date >= :since_date)";
  $incr .= " AND (DATE(c.created_at) <= :to_date OR w.term_date_set <= :to_date OR w.eligibility_date <= :to_date)";
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
      if(in_array($row['RELATIONSHIP'],array("Primary"))) {
          $ExistsMembersData[$row['MEMBER_NUMBER']] = $row;
      }
    }
    $ExistsMemberIds = array_column($csvData,'MEMBER_NUMBER');
    $ExistsMemberIds = array_unique($ExistsMemberIds);
    $ExistsDependents = $csvData;
    $ExistsDependentsData = array();
    foreach ($ExistsDependents as $key => $row) {
      if(!in_array($row['RELATIONSHIP'],array("Spouse",'Child'))) {
          $ExistsDependentsData[$row['MEMBER_NUMBER']] = $row;
      }
    }
  }
}

$invalid_ssn_arr = array("123456789","111111111");

$csv_line = "\n";
$csv_seprator = ",";
$field_seprator = '"';
$header_row = "";
$content = "";
$eligibility_content = "";
$header_row = "PLAN_NUMBER" . $csv_seprator .
        "MEMBER_NUMBER" . $csv_seprator .
        "PRIMARY_MEMBER#" . $csv_seprator .
        "BENEFIT_TIER" . $csv_seprator .
        "FIRST_NAME" . $csv_seprator .
        "LAST_NAME" . $csv_seprator .
        "ADDRESS" . $csv_seprator .
        "ADDRESS_2" . $csv_seprator .
        "CITY" . $csv_seprator .
        "STATE" . $csv_seprator .
        "ZIP_CODE" . $csv_seprator .
        "PHONE" . $csv_seprator .
        "EMAIL" . $csv_seprator .
        "GENDER" . $csv_seprator .
        "DOB" . $csv_seprator .
        "RELATIONSHIP" . $csv_seprator .
        "EFFECTIVE_DATE" . $csv_seprator .
        "TERMINATION_DATE" . $csv_seprator .
        "ACTIVE_MEMBER_SINCE_DATE" . $csv_seprator .
        "SUBPRODUCT_ID" . $csv_seprator .
        "PRODUCT_ID" . $csv_seprator .
        "PRODUCT_NAME" . $csv_seprator .
        "ENROLLING_AGENT" . $csv_line;

// Header Added to both files Main File and Eligibility File 
  $content .= $header_row;
  $error_content .= $header_row;
  if($generate_change_file){
    $eligibility_content .= $header_row;
  }
  
  $selSql = "SELECT c.rep_id,c.fname,c.lname,c.address,c.address_2,c.city,c.zip,c.state,c.cell_phone,c.email,c.gender,c.birth_date,w.eligibility_date,w.termination_date,p.name,sp.product_code as sub_product_id,p.product_code as product_id,p.name as product_name,w.active_date,s.rep_id as enrolling_agent,w.website_id,ppt.title as benefit_tier,ce.website_id as cust_enrollment_id,w.created_at as added_date,p.id as p_id,ppc.plan_code_value as group_code,ce.first_eligibility_date,ce.most_recent_date 
    FROM customer c 
    JOIN customer s ON (s.id=c.sponsor_id)
    JOIN website_subscriptions w ON (w.customer_id=c.id)
    JOIN customer_enrollment ce ON (ce.website_id=w.id)
    JOIN sub_products sp ON (FIND_IN_SET(sp.id,ce.sub_product) AND sp.is_deleted='N')
    JOIN prd_main p ON (p.id=w.product_id)
    JOIN prd_plan_type ppt ON (w.prd_plan_type_id=ppt.id)
    LEFT JOIN prd_plan_code ppc ON(ppc.product_id = p.id AND ppc.is_deleted = 'N' AND code_no = 'GC')
    WHERE c.is_deleted='N' AND c.status in ('Active','Inactive') AND c.type='Customer' AND w.status NOT in('Pending','Post Payment') AND sp.id IN ($prd_ids) $incr GROUP BY w.id";
  $mecRows = $pdo->select($selSql,$sch_params);


if($mecRows) {
  foreach ($mecRows as $key => $row) {
      $is_error = false;

      $req_where = array(
          "clause"=>"website_id=:id",
          "params"=>array(
            ":id"=>$row['cust_enrollment_id'],
          )
        );
        $req_data = array(
          'most_recent_date' => "msqlfunc_NOW()",
        );

        if(!$row['first_eligibility_date']){
          $req_data['first_eligibility_date'] = "msqlfunc_NOW()";
        }

        $pdo->update("customer_enrollment",$req_data,$req_where);

      $plan_code_details = $pdo->select("SELECT plan_code_value FROM prd_plan_code WHERE product_id = :product_id AND is_deleted = 'N' AND code_no = 'PC' AND plan_code_value != ''",array(':product_id' => $row['p_id']));

      $dependent_sql = "SELECT *,ssn as SSN,created_at as added_date FROM customer_dependent WHERE id>0 AND website_id=:id GROUP BY display_id ORDER BY eligibility_code";
      $dependet_where = array(":id" => $row['cust_enrollment_id']);
      $dependent_Row = $pdo->select($dependent_sql,$dependet_where);

      if ($row['gender'] == 'Male') {
        $row['gender'] = 'M';
      } elseif ($row['gender'] == 'Female') {
        $row['gender'] = 'F';
      }

      if($row["address"] != ''){
        $row["address"] = clean_csv_cell($row["address"]);
      }

      if($row["address_2"] != ''){
        $row["address_2"] = clean_csv_cell($row["address_2"]);
      }

      if($row["fname"] != ''){
          $row["fname"] = clean_csv_cell($row["fname"]);
      }

      if($row["lname"] != ''){
          $row["lname"] = clean_csv_cell($row["lname"]);     
      } 

       //  check if member is already exists or new member
      $addMember = false;
      if($generate_change_file){
        if(in_array($row['rep_id']."01",$ExistsMemberIds)){
          $is_Updated = compareHMARow($ExistsMembersData[$row['rep_id']."01"],$row);
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

      $plan_code_count = 0;
      $tmp_row = '';
      $tmp_row .= $row['website_id'] . $csv_seprator . //Plan ID
          $field_seprator . $row['rep_id'] . $field_seprator . $csv_seprator . //Employer Id
          $field_seprator . $row["rep_id"] . $field_seprator . $csv_seprator . //Member ID
          $field_seprator . strtoupper($row["benefit_tier"]) . $field_seprator . $csv_seprator . //Last Name
          $field_seprator . strtoupper($row["fname"]) . $field_seprator . $csv_seprator . //First Name
          $field_seprator . strtoupper($row["lname"]) . $field_seprator . $csv_seprator . //Middle Name
          $field_seprator . strtoupper($row["address"]) . $field_seprator . $csv_seprator . //Gender
          $field_seprator . strtoupper($row["address_2"]) . $field_seprator . $csv_seprator . //Gender
          $field_seprator . strtoupper($row["city"]) . $field_seprator . $csv_seprator . //City
          $field_seprator . getname("states_c", $row['state'], "short_name", "name") . $field_seprator . $csv_seprator . //State
          $field_seprator . $row["zip"] . $field_seprator . $csv_seprator . //Zipcode
          $field_seprator . $row["cell_phone"] . $field_seprator . $csv_seprator . //Phone
          $field_seprator . $row["email"] . $field_seprator . $csv_seprator . //Email
          $field_seprator . $row["gender"] . $field_seprator . $csv_seprator . //Email
          $field_seprator .   ($row["birth_date"] != '' ? ''.date('m/d/Y', strtotime($row["birth_date"])). '' : '') . $field_seprator . $csv_seprator . //Birth_date
          $field_seprator . "Primary" . $field_seprator . $csv_seprator . //Member Type
          
          //$field_seprator .   ($row["added_date"] != '' ? ''.date('m/d/Y', strtotime($row["added_date"])). '' : '') . $field_seprator . $csv_seprator . //Birth_date
          
          $field_seprator .   ($row["eligibility_date"] != '' ? ''.date('m/d/Y', strtotime($row["eligibility_date"])). '' : '') . $field_seprator . $csv_seprator . //Birth_date
          $field_seprator .   ($row["termination_date"] != '' ? ''.date('m/d/Y', strtotime($row["termination_date"])). '' : '') . $field_seprator . $csv_seprator . //Birth_date
          $field_seprator .   ($row["active_date"] != '' ? ''.date('m/d/Y', strtotime($row["active_date"])). '' : '') . $field_seprator . $csv_seprator . //Birth_date
          $field_seprator . $row['sub_product_id'] . $field_seprator . $csv_seprator . //rider_1
          $field_seprator . $row['product_id'] . $field_seprator . $csv_seprator . //rider_1
          $field_seprator . $row['product_name'] . $field_seprator . $csv_seprator . //rider_1
          $field_seprator . $row['enrolling_agent'] . $field_seprator . $csv_seprator.
          $field_seprator . $row['group_code'] . $field_seprator . $csv_seprator;
          if($plan_code_details){
            foreach ($plan_code_details as $k => $v) {
              $tmp_row .= $field_seprator . $v['plan_code_value'] . $field_seprator . $csv_seprator;
              $plan_code_count++;
            }
          }
          for ($i=0; $i <= (10 - $plan_code_count); $i++) { 
            $tmp_row .= $field_seprator . "" . $field_seprator . $csv_seprator;  
          }

          $tmp_row .= $csv_line;


      if(!$is_error){
        $content .= $tmp_row;

        if($addMember) {
          $eligibility_content .= $tmp_row;
        }
      }

      if($is_error) {
        $generate_error_file = true;
        $error_content .= $tmp_row;
      }
    
      if($dependent_Row){
        foreach ($dependent_Row as $key => $value) {
          $is_dep_error = false;
          
          if ($value['gender'] == 'Male'){
            $value['gender'] = 'M';
          }else if($value['gender'] == 'Female'){
            $value['gender'] = 'F';
          }
          if($value["fname"] != ''){
            $value["fname"] = clean_csv_cell($value["fname"]);  
          }
          if($value["lname"] != ''){
              $value["lname"] = clean_csv_cell($value["lname"]);     
          } 
          if($value["mname"] != ''){
              $value["mname"] = clean_csv_cell($value["mname"]);     
          }
          if($value['zip_code'] != ''){
            $zipcode = $value["zip_code"];
          }else{
            $zipcode = $row["zip"];
          } 
          if($value["phone"] != ''){
            $phone = $value["phone"];
          }else{
            $phone = $row["cell_phone"];
          }
          if($value['email'] != ''){
            $email = $value['email'];
          }else{
            $email = $row["email"];
          }

          
          if($value["eligibility_date"] != ''){
            $value["eligibility_date"] = $value["eligibility_date"];
          }else{
            $value["eligibility_date"] = $row["eligibility_date"];
          }
          if($value["terminationDate"] != '0000-00-00' && !empty($value["terminationDate"])){
            $value["terminationDate"] = $value["terminationDate"];
          }else{
            $value["terminationDate"] = $row["termination_date"];
          }
          
          if($value['relation'] == "Wife" || $value['relation'] == "Husband"){
            $value['relation'] = "Spouse";
          }
          if($value['relation'] == "Daughter" || $value['relation'] == "Son"){
             $value['relation'] = "Child";
          }

           //  if member id exists then we compare the dependents details
          $addDependent = false;
          if($generate_change_file){
            if(in_array($row['rep_id'].''.$eligibility_code,$ExistsMemberIds)){
              $is_Updated = dependentsHMARow($ExistsDependentsData[$row['rep_id'].''.$eligibility_code],$value);
              if($is_Updated === true) {
                $addDependent = true;
              }else{
                $addDependent = false;
              }
            }else{
              $addDependent = true;
            }
          }
          $plan_code_count = 0;  
          $tmp_row = '';
          $tmp_row .= $row['website_id'] . $csv_seprator . //Plan ID
            $field_seprator . $value['display_id'] . $field_seprator . $csv_seprator . //Employer Id
            $field_seprator . $row["rep_id"] . $field_seprator . $csv_seprator . //Member ID
            $field_seprator . $row["benefit_tier"] . $field_seprator . $csv_seprator . //Last Name
            $field_seprator . $value["fname"] . $field_seprator . $csv_seprator . //First Name
            $field_seprator . $value["lname"] . $field_seprator . $csv_seprator . //Middle Name
            $field_seprator . $value["address"] . $field_seprator . $csv_seprator . //Gender
            $field_seprator . "" . $field_seprator . $csv_seprator . //Gender
            $field_seprator . $value["city"] . $field_seprator . $csv_seprator . //City
            $field_seprator . getname("states_c", $value['state'], "short_name", "name") . $field_seprator . $csv_seprator . //State
            $field_seprator . $zipcode . $field_seprator . $csv_seprator . //Zipcode
            $field_seprator . $phone . $field_seprator . $csv_seprator . //Phone
            $field_seprator . $email . $field_seprator . $csv_seprator . //Email
            $field_seprator . $value["gender"] . $field_seprator . $csv_seprator . //Email
            $field_seprator .   ($value["birth_date"] != '' ? ''.date('m/d/Y', strtotime($value["birth_date"])). '' : '') . $field_seprator . $csv_seprator . //Birth_date
            $field_seprator . $value['relation'] . $field_seprator . $csv_seprator . //Member Type
            //$field_seprator .   ($value["added_date"] != '' ? ''.date('m/d/Y', strtotime($value["added_date"])). '' : '') . $field_seprator . $csv_seprator . //Birth_date
            $field_seprator .   ($value["eligibility_date"] != '' ? ''.date('m/d/Y', strtotime($value["eligibility_date"])). '' : '') . $field_seprator . $csv_seprator . //Birth_date
            $field_seprator .   ($value["terminationDate"] != '' ? ''.date('m/d/Y', strtotime($value["terminationDate"])). '' : '') . $field_seprator . $csv_seprator . //Birth_date
            $field_seprator .   ($value["active_since"] != '' ? ''.date('m/d/Y', strtotime($value["active_since"])). '' : '') . $field_seprator . $csv_seprator . //Birth_date
            $field_seprator . $row['sub_product_id'] . $field_seprator . $csv_seprator . //rider_1
            $field_seprator . $row['product_id'] . $field_seprator . $csv_seprator . //rider_1
            $field_seprator . $row['product_name'] . $field_seprator . $csv_seprator . //rider_1
            $field_seprator . $row['enrolling_agent'] . $field_seprator . $csv_seprator . //rider_1
            $field_seprator . $row['group_code'] . $field_seprator . $csv_seprator;
            if($plan_code_details){
              foreach ($plan_code_details as $k => $v) {
                $tmp_row .= $field_seprator . $v['plan_code_value'] . $field_seprator . $csv_seprator;
                $plan_code_count++;
              }
            }
            for ($i=0; $i <= (10 - $plan_code_count); $i++) { 
              $tmp_row .= $field_seprator . "" . $field_seprator . $csv_seprator;  
            }

            $tmp_row .= $csv_line;

            if(!$is_dep_error){
              $content .= $tmp_row;

              if($addDependent) {
                $eligibility_content .= $tmp_row;
              }
            }

            if($is_dep_error) {
              $generate_error_file = true;
              $error_content .= $tmp_row;
            }
        }
      }
     // Dependent rows ends
  }
}


if($content != '' && $file_type == "add_change_file"){
  $file_update = file_put_contents($file,$content);
  if($generate_change_file){
    $content = $eligibility_content;
  }
}

if(!$generate_error_file){
  $error_content = '';
}
?>