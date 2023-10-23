<?php

/*
 * Class for Participants settings
 */

class Participants {

  public function get_participants_id(){
    global $pdo;
    $participants_id = rand(1000000, 9999999);
  
    $sql = "SELECT id FROM participants WHERE participants_id ='P" . $participants_id . "' OR participants_id ='" . $participants_id . "'";
    $res = $pdo->selectOne($sql);
    
    if(!empty($res['id'])) {
      return $this->get_participants_id();
    } else {
      return "P" . $participants_id;
    }
  }
  public function participants_csv_import($participants_csv_id){
    global $pdo,$PARTICIPANTS_CSV_DIR,$CREDIT_CARD_ENC_KEY;

    $csvSql = "SELECT * FROM participants_csv WHERE status='Pending' AND id=:id AND is_deleted='N'";
    $csvWhere = array(":id" => $participants_csv_id);
    $fileRow = $pdo->selectOne($csvSql,$csvWhere);

    if (!empty($fileRow)) {

        $csvWhere = array(
            "clause" => "id=:id",
            "params" => array(
                ":id" => $fileRow['id']
            )
        );
        $pdo->update('participants_csv',array('is_running' => 'Y'), $csvWhere);

        $agentSql = "SELECT id,type,sponsor_id,rep_id,CONCAT(fname,' ',lname) as agent_name,business_name as group_name FROM customer WHERE id=:id";
        $agentRow = $pdo->selectOne($agentSql, array(":id" => $fileRow['agent_id']));

        $adminRow = array();
        if(!empty($fileRow['admin_id'])) {
            $adminRow = $pdo->selectOne('SELECT id,display_id FROM admin WHERE id=:id',array(":id" => $fileRow['admin_id']));
        } 

        $participantsTag = $fileRow['participants_tag'];
        $csvFile = $PARTICIPANTS_CSV_DIR . $fileRow['file_name'];
        $csvFileData = csvToArraywithFieldsMain($csvFile);

        $processedCntTotal = 0;
        $importCntTotal = 0;
        $lastTag = '';
        $tagId = '';
        
        $fields = json_decode($fileRow["csv_columns"],true);

        foreach ($csvFileData as $row) {

            //Checking script cancelled or not
                if ($processedCntTotal == 0 || $processedCntTotal % 100 == 0) {
                    $tmp_file_row = $pdo->selectOne("SELECT status FROM participants_csv WHERE id=:id",array(":id" => $fileRow['id']));
                    if(!empty($tmp_file_row['status']) && $tmp_file_row['status'] == "Cancel") {
                        break;    
                    }
                }
        

            $processedCntTotal++;

             if ($processedCntTotal < $fileRow['total_processed']) {
                continue; //continue after lead count is greater than previously imported participants
            }

            if (($processedCntTotal - $fileRow['total_processed']) == 15000) {
                break;// stopping and only importing 15000 participants at a time from csv
            }

            if ($processedCntTotal % 100 == 0) {
                sleep(1); //pause 1 second after 100 participants are imported
            }

            $row = array_map('trim', $row);
            $error_reporting_arr = array('file_id' => $fileRow['id'],'reason' => '','csv_columns' => json_encode($row));

            $reseller_number = $row[$fields['reseller_number']];
            $client_code = $row[$fields['client_code']]; // GroupNumber
            $ssn = phoneReplaceMain($row[$fields['ssn']]);
            
            $fname = $row[$fields['fname']];
            $lname = $row[$fields['lname']];
            $mname = $row[$fields['mname']];
            $birth_date = $row[$fields['birth_date']];
            
            $gender = !empty($row[$fields['gender']]) ? strtolower($row[$fields['gender']]) : "";
            if($gender == "m" || $gender == "male"){
                $gender = "Male";
            }else if($gender == "f" || $gender == "female"){
                $gender = "Female";
            }else{
                $gender = "";
            }

            $employee_id = $row[$fields['employee_id']]; // Primary ID
            $person_code = $row[$fields['person_code']];

            $address = $row[$fields['address']];
            $address2 = $row[$fields['address2']];
            $city = $row[$fields['city']];
            $state = $row[$fields['state']];
            $zip = $row[$fields['zip']];
            $cell_phone = phoneReplaceMain($row[$fields['cell_phone']]); // Phone
            $email = $row[$fields['email']];
            $is_disabled = !empty($row[$fields['is_disabled']]) ? strtolower($row[$fields['is_disabled']]) : "";
            if($is_disabled == "y" || $is_disabled == "yes"){
                $is_disabled = "Y";
            }else if($is_disabled == "n" || $is_disabled == "no"){
                $is_disabled = "N";
            }else{
                $is_disabled = "";
            }

            $disability_effective_date = $row[$fields['disability_effective_date']];
            $tobacco_user = !empty($row[$fields['tobacco_user']]) ? strtolower($row[$fields['tobacco_user']]) : "";
            if($tobacco_user == "y" || $tobacco_user == "yes"){
                $tobacco_user = "Y";
            }else if($tobacco_user == "n" || $tobacco_user == "no"){
                $tobacco_user = "N";
            }else{
                $tobacco_user = "";
            }

            $product_code = $row[$fields['product_code']];
            $plan_identifier = $row[$fields['plan_identifier']]; // Product Name
            $plan_coverage_tier = $row[$fields['plan_coverage_tier']];
            $effective_date = $row[$fields['effective_date']];
            $termination_date = $row[$fields['termination_date']];
            $relationship = $row[$fields['relationship']];

            $is_error = false;
            $errors = array();
            if(empty($reseller_number)){
                array_push($errors, "Reseller Number is empty");
                $is_error = true;
            }
            if(empty($fname)){
                array_push($errors, "First name is empty");
                $is_error = true;
            }
            if(empty($lname)){
                array_push($errors, "Last name is empty");
                $is_error = true;
            }
            if(!empty($mname) && strlen($mname) > 1){
                array_push($errors, "MI Value must be exact One Character");
                $is_error = true;
            }
            if(empty($birth_date)){
                array_push($errors, "DOB is empty");
                $is_error = true;
            }else if(!strtotime($birth_date)){
                array_push($errors, "Valid DOB Required");
                $is_error = true;
            }else{
                $validFormat1 = validateDate($birth_date,"m/d/Y");
                $validFormat2 = validateDate($birth_date,"n/j/Y");
                
                if (!$validFormat1 && !$validFormat2) {
                    array_push($errors, "Valid DOB Format(MM/DD/YYYY) Required");
                    $is_error = true;
                }else if(isFutureDateMain(date("Y-m-d",strtotime($birth_date)))){
                    array_push($errors, "DOB should not Future Date");
                    $is_error = true;
                }
            }

            if(empty($gender)) {
                array_push($errors, "Gender is empty");
                $is_error = true;
            }

            if(empty($person_code)){
                array_push($errors, "Person Code is empty");
                $is_error = true;
            }else if(!is_numeric($person_code)){
                array_push($errors, "Person Code should be numeric");
                $is_error = true;
            }

            $participants_id = 0;
            if(empty($employee_id)){
                array_push($errors, "PrimaryID is empty");
                $is_error = true;
            }else if(!empty($person_code)){
                $tmp_persion_code = $person_code;
                $person_code = sprintf("%02d",$person_code);
                $selEmployee = "SELECT id FROM participants WHERE employee_id=:employee_id AND (person_code=:person_code or person_code=:tmp_persion_code) AND is_deleted='N'";
                $resEmployee = $pdo->selectOne($selEmployee,array(":employee_id" => $employee_id,":person_code" => $person_code,":tmp_persion_code"=>$tmp_persion_code));
                $participants_id = !empty($resEmployee["id"]) ? $resEmployee["id"] : 0; 
            }

            if(empty($address)){
                array_push($errors, "Address is empty");
                $is_error = true;
            }

            if(empty($city)){
                array_push($errors, "City is empty");
                $is_error = true;
            }
            if(empty($state)){
                array_push($errors, "State is empty");
                $is_error = true;
            }else{
                $stateArr = explode("-", $state);
                $stateRes=$pdo->selectOne("SELECT name,short_name FROM states_c WHERE country_id = '231' AND short_name=:short_name",array(":short_name"=>trim($stateArr[0])));

                if(empty($stateRes)){
                    array_push($errors, "State is not valid");
                    $is_error = true;
                }else{
                    $state = $stateRes["name"];
                }
            }
            if(empty($zip)){
                array_push($errors, "Zipcode is empty");
                $is_error = true;
            }
            if(!empty($cell_phone) && strlen($cell_phone) < 10){
                array_push($errors, "Invalid Phone Number (10 Digit Required)");
                $is_error = true;
            }

            if(!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)){
                $error_reporting_arr['reason'] = "Valid email required";
                $pdo->insert("participants_csv_log", $error_reporting_arr);
                $is_error = true;
            }

            if(!empty($disability_effective_date) && !strtotime($disability_effective_date)){
                array_push($errors, "Valid DisabilityStatusEffective Date Required");
                $is_error = true;
            }

            if(empty($product_code)){
                array_push($errors, "Product Code is empty");
                $is_error = true;
            }
            if(empty($plan_identifier)){
                array_push($errors, "Product Name is empty");
                $is_error = true;
            }
            if(empty($plan_coverage_tier)){
                array_push($errors, "Coverage Tier is empty");
                $is_error = true;
            }else if(!in_array(strtoupper($plan_coverage_tier), array('EE','ES','EC','EF'))){
                array_push($errors, "Valid Coverage Tier Required (EE or ES or EC or EF)");
                $is_error = true;
            }

            if(empty($effective_date)){
                array_push($errors, "Effective Date is empty");
                $is_error = true;
            }else if(!strtotime($effective_date)){
                array_push($errors, "Valid Effective Date Required");
                $is_error = true;
            }

            if(!empty($termination_date) && !strtotime($termination_date)){
                array_push($errors, "Valid Termination Date Required");
                $is_error = true;
            }

            if(empty($relationship)) {
                array_push($errors, "Relationship is empty");
                $is_error = true;
            }
            
            if($errors){
                $error_reporting_arr['reason'] = implode(',', $errors);
                $pdo->insert("participants_csv_log", $error_reporting_arr);
            }

            if(!$is_error){

                $importCntTotal++;

                $participants_disp_id = $this->get_participants_id();
                $participants_data = array(
                    "file_id" => $fileRow['id'],
                    "sponsor_id" => $fileRow['agent_id'],
                    "participants_type" => $fileRow['participants_type'],
                    "participants_tag" => $participantsTag,
                    
                    "reseller_number" => $reseller_number,
                    "client_code" => $client_code,
                    "employee_id" => $employee_id,
                    "fname" => $fname,
                    "lname" => $lname,
                    "birth_date" => date("Y-m-d",strtotime($birth_date)),
                    "gender" => $gender,
                    "email" => $email,
                    "person_code" => $person_code,
                    "address" => $address,
                    "city" => $city,
                    "state" => $state,
                    "zip" => $zip,
                    "admin_id" => $fileRow['admin_id'],
                );

                if (!empty($ssn)) {
                    $ssn_last_four_digit = substr($ssn,-4,4);
                    $participants_data['ssn'] = "msqlfunc_AES_ENCRYPT('" . $ssn . "','" . $CREDIT_CARD_ENC_KEY . "')";
                    $participants_data['last_four_ssn'] = $ssn_last_four_digit;
                }

                if(!empty($mname)){
                    $participants_data["mname"] = $mname;
                }
               
                if(!empty($address2)){
                    $participants_data["address2"] = $address2;
                }
               
                if(!empty($cell_phone)){
                    $participants_data["cell_phone"] = $cell_phone;
                }
                
                if(!empty($is_disabled)){
                    $participants_data["is_disabled"] = $is_disabled;
                }
                if(!empty($disability_effective_date) && strtotime($disability_effective_date) > 0){
                    $participants_data["disability_effective_date"] = date("Y-m-d",strtotime($disability_effective_date));
                }
               
                if(!empty($tobacco_user)){
                    $participants_data["tobacco_user"] = $tobacco_user;
                }

                if(!empty($participants_id)){
                    $participants_where = array(
                        "clause" => "id=:id",
                        "params" => array(
                            ":id" => $participants_id
                        )
                    );
                    $pdo->update('participants',$participants_data, $participants_where);
                    
                }else{
                    $participants_data["status"] = "New";
                    $participants_data["participants_id"] = $participants_disp_id;
                    $participants_id = $pdo->insert("participants",$participants_data);
                }

                $participantsProducts = array(
                    "participants_id" => $participants_id,
                    "product_code" => $product_code,
                    "plan_identifier" => $plan_identifier,
                    "plan_coverage_tier" => $plan_coverage_tier,
                    "effective_date" => date("Y-m-d",strtotime($effective_date)),
                    "relationship" => $relationship,
                );
                
                $participantsProducts["termination_date"] = !empty($termination_date) ? date("Y-m-d",strtotime($termination_date)) : NULL;

                $productSql = "SELECT id FROM participants_products WHERE product_code=:product_code AND participants_id=:participants_id AND is_deleted='N'";
                $productRes = $pdo->selectOne($productSql, array(":participants_id" => $participants_id,":product_code" => $product_code));

                if(!empty($productRes["id"])){
                    $productsWhere = array(
                        "clause" => "id=:id",
                        "params" => array(
                            ":id" => $productRes["id"]
                        )
                    );
                    $pdo->update('participants_products',$participantsProducts,$productsWhere);
                }else{
                    $pdo->insert('participants_products',$participantsProducts);
                }

                if(!empty($adminRow)) {
                    $desc = array();
                    $desc['ac_message'] = array(
                        'ac_red_1' => array(
                            'href' => 'participants_details.php?id=' . md5($participants_id),
                            'title' => $participants_disp_id,
                        ),
                        'ac_message_1' => ' added by Admin ',
                        'ac_red_2' => array(
                            'href' => 'admin_profile.php?id=' . md5($adminRow['id']),
                            'title' => $adminRow['display_id'],
                        ),                                
                        'ac_message_2' => ' To ',
                        'ac_red_3' => array(
                            'href' => 'agent_detail_v1.php?id=' . md5($agentRow['id']),
                            'title' => $agentRow['rep_id'],
                        ),                                
                        'ac_message_3' => ' via upload using '.$fileRow['participants_tag'],
                    );
                    $desc = json_encode($desc);
                    activity_feed(3,$participants_id,'participants', $agentRow['id'],'Agent','Participants added by Admin', $fname, $lname, $desc);
                }
            }

            if ($processedCntTotal % 100 == 0) {
                $csvRowUpdate = array(
                    'total_processed' => $processedCntTotal,
                    'import_participants' => $fileRow['import_participants'] + $importCntTotal,
                );
                $csvRowWhere = array(
                    "clause" => "id=:id",
                    "params" => array(":id" => $fileRow['id'])
                );
                $pdo->update('participants_csv', $csvRowUpdate, $csvRowWhere);
            }

        }

        $csvRowUpdate = array(
            "is_running" => 'N',
            'total_processed' => $processedCntTotal,
            'import_participants' => $fileRow['import_participants'] + $importCntTotal,
        );

        if ($fileRow['total_participants'] <= $processedCntTotal) {
            $csvRowUpdate['status'] = 'Processed';

            if(!empty($adminRow)) {
                $desc = array();
                $desc['ac_message'] = array(
                    'ac_red_1' => array(
                        'href' => 'admin_profile.php?id=' . md5($adminRow['id']),
                        'title' => $adminRow['display_id'],
                    ),
                    'ac_message_1' => ' CSV Participants Imported'
                );
                $desc['participants_type'] = "Participants Type : " . $fileRow['participants_type'];
                $desc['participants_tag'] = "Participants Tag : " . $fileRow['participants_tag'];
                $desc['total_participants'] = "Total Participants : " . $fileRow['total_participants'];
                $desc['import_participants'] = "Total Added Participants : " . $importCntTotal;
                $desc = json_encode($desc);
                activity_feed(3, $adminRow['id'], 'Admin', $fileRow['id'],'participants_csv','CSV Participants Imported', '', '', $desc);

                $desc = array();
                $desc['ac_message'] = array(
                    'ac_red_1' => array(
                        'href' => 'agent_detail_v1.php?id=' . md5($agentRow['id']),
                        'title' => $agentRow['rep_id'],
                    ),
                    'ac_message_1' => 'CSV Participants Imported'
                );
                $desc['participants_type'] = "Participants Type : " . $fileRow['participants_type'];
                $desc['participants_tag'] = "Participants Tag : " . $fileRow['participants_tag'];
                $desc['total_participants'] = "Total Participants : " . $fileRow['total_participants'];
                $desc['import_participants'] = "Total Added Participants : " . $importCntTotal;
                $desc = json_encode($desc);
                activity_feed(3, $agentRow['id'], $agentRow['type'], $fileRow['id'],'participants_csv','CSV Participants Imported', '', '', $desc);
            }
        }

        $csvRowWhere = array(
            "clause" => "id=:id",
            "params" => array(":id" => $fileRow['id'])
        );
        $pdo->update('participants_csv', $csvRowUpdate, $csvRowWhere);
    }
  }
  public function get_participants_tags($agent_id = 0){
    global $pdo;
    $tag = array();
    $str = '';
    if(!empty($agent_id)) {
      $str .= ' AND sponsor_id = '.$agent_id;
    }
  
    $participantsTag = "SELECT participants_tag
          FROM participants
          WHERE is_deleted='N' ".$str."
            GROUP BY participants_tag
            ORDER BY participants_tag ASC";
    $participantsRes = $pdo->select($participantsTag);

    if(!empty($participantsRes)){
      foreach ($participantsRes as $row) {
        $tag[] = array("tag" => $row["participants_tag"]);
      }
    }
    return $tag;
  }
}
?>