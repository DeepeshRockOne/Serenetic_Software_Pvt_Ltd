<?php
$sch_params = array();
$incr = '';
$generate_change_file = false;
$generate_error_file = false;

$invalid_ssn_arr = array("123456789","111111111");

$csv_line = "\n";
$csv_seprator = ",";
$field_seprator = '"';
$header_row = "";
$content = "";
$eligibility_content = "";

$txt_line = "\r\n";
$txt_seprator = "";
$txt_field_seprator = "";
$txt_header_row = "";
$txt_content = "";
$txt_eligibility_content = "";

    if($file_key == "HEALTHY_STEP_ACCESS") {

        if($file_type == "full_file") {
            $sch_params[":to_date"] = date("Y-m-d",strtotime($to_date));
            $incr .= " AND w.eligibility_date <= :to_date";

        }else if($file_type == "schedule_change_file" || $file_type == "add_change_file") {
            
            if($file_type == "add_change_file") {
                $sch_params[":since_date"] = date("Y-m-d",strtotime($since_date));
                $sch_params[":to_date"] = date("Y-m-d",strtotime($to_date));
                $incr .= " AND (DATE(c.created_at) >= :since_date OR w.term_date_set >= :since_date OR w.eligibility_date >= :since_date)";
                $incr .= " AND (DATE(c.created_at) <= :to_date OR w.term_date_set <= :to_date OR w.eligibility_date <= :to_date)"; 
            }

            $file = (__DIR__) . "/" . $file_key . "_Main_File.csv";
            if(!file_exists($file)){
                fopen($file,"w");  
            }

            $csvData = csvToArraywithFields($file);
            if(!empty($csvData)) {
                $generate_change_file = true;
            }
           
            if($generate_change_file){
                $ExistsMembers = $csvData;
                $ExistsMembersData = array();
                foreach ($ExistsMembers as $key => $row) {
                    $ExistsMembersData[$row['memberCustomerIdentifier']] = $row;
                }
                $ExistsMemberIds = array_column($csvData,'memberCustomerIdentifier');
                $ExistsMemberIds = array_unique($ExistsMemberIds);
            }
        }

        $organizationCustomerIdentifier = 2002690;
        $programCustomerIdentifier = 201056;
        $header_row = 
            $field_seprator . "organizationCustomerIdentifier" . $field_seprator . $csv_seprator .
            $field_seprator . "programCustomerIdentifier" . $field_seprator . $csv_seprator .
            $field_seprator . "memberCustomerIdentifier" . $field_seprator . $csv_seprator .
            $field_seprator . "memberStatus" . $field_seprator . $csv_seprator .
            $field_seprator . "firstname" . $field_seprator . $csv_seprator .
            $field_seprator . "middleName" . $field_seprator . $csv_seprator .
            $field_seprator . "lastname" . $field_seprator . $csv_seprator .
            $field_seprator . "postalCode" . $field_seprator . $csv_seprator .
            $field_seprator . "emailAddress" . $field_seprator . $csv_seprator .
            $field_seprator . "streetLine1" . $field_seprator . $csv_seprator .
            $field_seprator . "streetLine2" . $field_seprator . $csv_seprator .
            $field_seprator . "city" . $field_seprator . $csv_seprator .
            $field_seprator . "state" . $field_seprator . $csv_seprator .
            $field_seprator . "phoneNumber" . $field_seprator . $csv_line;
        
        //Header Added to both files Main File and Eligibility File 
        $content .= $header_row;
        $error_content .= $header_row;
        if($generate_change_file){
            $eligibility_content .= $header_row;
        }
      
        $selSql = "SELECT 
        c.rep_id as memberCustomerIdentifier,
        IF(w.termination_date IS NULL OR (w.termination_date != w.eligibility_date AND DATE(NOW()) < DATE(w.termination_date)),'OPEN','SUSPEND') as memberStatus,
        c.fname as firstname,
        '' as middleName,
        c.lname as lastname,
        c.zip as postalCode,
        c.email as emailAddress,
        c.address as streetLine1,
        c.address_2 as streetLine2,
        c.city as city,
        c.state as state,
        c.cell_phone as phoneNumber,
  
        ce.id as cust_enrollment_id,
        ce.first_eligibility_date,
        ce.most_recent_date 
        FROM customer c 
        JOIN customer s ON (s.id=c.sponsor_id)
        JOIN website_subscriptions w ON (w.customer_id=c.id)
        JOIN customer_enrollment ce ON (ce.website_id=w.id)
        JOIN prd_main p ON (p.id=w.product_id)
        WHERE 
        c.is_deleted='N' AND 
        c.status in ('Active','Inactive') AND 
        c.type='Customer' AND 
        w.status not in('Pending','Post Payment') AND 
        (p.id IN ($prd_ids) OR p.parent_product_id IN($prd_ids)) 
        $incr 
        GROUP BY c.id
        ORDER BY w.eligibility_date ASC";
        $policy_res = $pdo->select($selSql,$sch_params);


        if($policy_res) {
            foreach ($policy_res as $key => $row) {
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
                if(!(!empty($row['first_eligibility_date']) && strtotime($row['first_eligibility_date']) > 0)){
                    $req_data['first_eligibility_date'] = "msqlfunc_NOW()";
                }
                $pdo->update("customer_enrollment",$req_data,$req_where);

                //CLEAN CSV CELL
                foreach ($row as $row_key => $row_value) {
                    if($row[$row_key] != '' && !in_array($row_key,array('emailAddress'))){
                        $row[$row_key] = clean_csv_cell($row[$row_key]);
                    }                    
                }

               //  check if member is already exists or new member
                $addMember = false;
                if($generate_change_file){
                    if(in_array($row['memberCustomerIdentifier'],$ExistsMemberIds)){
                        $is_Updated = compare_HEALTHY_STEP_ACCESS_ROW($ExistsMembersData[$row['memberCustomerIdentifier']],$row);
                        //if member detail is updates then add member to eligibility file
                        if($is_Updated === true) {
                            $addMember = true;
                        } else {
                            $addMember = false;
                        }
                    } else {
                        //if new member then add member to eligibility file
                        $addMember = true;
                    }
                }

                $tmp_row = '';
                $tmp_row .= 
                $field_seprator . $organizationCustomerIdentifier . $field_seprator . $csv_seprator . 
                $field_seprator . $programCustomerIdentifier . $field_seprator . $csv_seprator . 
                $field_seprator . $row["memberCustomerIdentifier"] . $field_seprator . $csv_seprator . 
                $field_seprator . $row["memberStatus"] . $field_seprator . $csv_seprator . 
                $field_seprator . $row["firstname"] . $field_seprator . $csv_seprator . 
                $field_seprator . $row["middleName"] . $field_seprator . $csv_seprator . 
                $field_seprator . $row["lastname"] . $field_seprator . $csv_seprator . 
                $field_seprator . $row["postalCode"] . $field_seprator . $csv_seprator . 
                $field_seprator . $row["emailAddress"] . $field_seprator . $csv_seprator . 
                $field_seprator . $row["streetLine1"] . $field_seprator . $csv_seprator . 
                $field_seprator . $row["streetLine2"] . $field_seprator . $csv_seprator . 
                $field_seprator . $row["city"] . $field_seprator . $csv_seprator . 
                $field_seprator . (isset($allStateShortName[$row["state"]])?$allStateShortName[$row["state"]]:$row["state"]) . $field_seprator . $csv_seprator . 
                $field_seprator . $row["phoneNumber"] . $field_seprator . $csv_seprator;
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
            }
        }
        
        if($content != '' && ($file_type == "schedule_change_file" || $file_type == "add_change_file")){
            $file_update = file_put_contents($file,$content);
            if($generate_change_file){
                $content = $eligibility_content;
            }
        }

        if(!$generate_error_file) {
            $error_content = '';
        }
    }

    if($file_key == "HEALTHY_STEP_AUGEO") {

        if($file_type == "full_file") {
            $sch_params[":to_date"] = date("Y-m-d",strtotime($to_date));
            $incr .= " AND (DATE(c.created_at) <= :to_date OR w.term_date_set <= :to_date OR w.eligibility_date <= :to_date)";

        }else if($file_type == "schedule_change_file" || $file_type == "add_change_file") {
            
            if($file_type == "add_change_file") {
                $sch_params[":since_date"] = date("Y-m-d",strtotime($since_date));
                $sch_params[":to_date"] = date("Y-m-d",strtotime($to_date));
                $incr .= " AND (DATE(c.created_at) >= :since_date OR w.term_date_set >= :since_date OR w.eligibility_date >= :since_date)";
                $incr .= " AND (DATE(c.created_at) <= :to_date OR w.term_date_set <= :to_date OR w.eligibility_date <= :to_date)";              
            }

            $file = (__DIR__) . "/" . $file_key . "_Main_File.csv";
            if(!file_exists($file)){
                fopen($file,"w");  
            }
            $csvData = csvToArraywithFields($file);
            if(!empty($csvData)) {
                $generate_change_file = true;
            }
           
            if($generate_change_file){
                $ExistsMembers = $csvData;
                $ExistsMembersData = array();
                foreach ($ExistsMembers as $key => $row) {
                    $ExistsMembersData[$row['MEMBER_ID']] = $row;
                }
                $ExistsMemberIds = array_column($csvData,'MEMBER_ID');
                $ExistsMemberIds = array_unique($ExistsMemberIds);
            }
        }

        $file_columns = array(
            array('lable' => 'GROUP_NO', 'key' => 'GROUP_NO', 'data_type' => 'text', 'length' => '4'),
            array('lable' => 'MEMBER_ID', 'key' => 'MEMBER_ID', 'data_type' => 'text', 'length' => '12'),
            array('lable' => 'FIRST_NAME', 'key' => 'FIRST_NAME', 'data_type' => 'text', 'length' => '20'),
            array('lable' => 'MIDDLE_INITIAL', 'key' => 'MIDDLE_INITIAL', 'data_type' => 'text', 'length' => '1'),
            array('lable' => 'LAST_NAME', 'key' => 'LAST_NAME', 'data_type' => 'text', 'length' => '20'),
            array('lable' => 'ADDRESS1', 'key' => 'ADDRESS1', 'data_type' => 'text', 'length' => '40'),
            array('lable' => 'ADDRESS2', 'key' => 'ADDRESS2', 'data_type' => 'text', 'length' => '40'),
            array('lable' => 'CITY', 'key' => 'CITY', 'data_type' => 'text', 'length' => '25'),
            array('lable' => 'STATE', 'key' => 'STATE', 'data_type' => 'text', 'length' => '2'),
            array('lable' => 'ZIPCODE', 'key' => 'ZIPCODE', 'data_type' => 'text', 'length' => '10'),
            array('lable' => 'PHONE_H', 'key' => 'PHONE_H', 'data_type' => 'text', 'length' => '10'),
            array('lable' => 'PHONE_W', 'key' => 'PHONE_W', 'data_type' => 'text', 'length' => '10'),
            array('lable' => 'ACTIVATION_DTE', 'key' => 'ACTIVATION_DTE', 'data_type' => 'date', 'length' => '6'),
            array('lable' => 'CANCEL_DTE', 'key' => 'CANCEL_DTE', 'data_type' => 'date', 'length' => '6'),
            array('lable' => 'STATUS', 'key' => 'STATUS', 'data_type' => 'text', 'length' => '1'),
            array('lable' => 'EMAIL_ADDR', 'key' => 'EMAIL_ADDR', 'data_type' => 'text', 'length' => '60'),
            array('lable' => 'OFFER_TYPE', 'key' => 'OFFER_TYPE', 'data_type' => 'text', 'length' => '5'),
            array('lable' => 'FULFILLMENT_FLG', 'key' => 'FULFILLMENT_FLG', 'data_type' => 'text', 'length' => '1'),
        );
        $header_row = '';
        foreach ($file_columns as $clm_key => $column_data) {
            if($clm_key > 0) {
                $header_row .= $csv_seprator;
            }
            $header_row .= $field_seprator . $column_data['lable'] . $field_seprator;
        }
        $header_row .= $csv_line;
        
        //Header Added to both files Main File and Eligibility File 
        $content .= $header_row;
        $error_content .= $header_row;
        if($generate_change_file){
            $eligibility_content .= $header_row;
        }
      
        $selSql = "SELECT 
        '5100' as GROUP_NO,
        SUBSTR(c.rep_id,2) as MEMBER_ID,
        c.fname as FIRST_NAME,
        '' as MIDDLE_INITIAL,
        c.lname as LAST_NAME,
        c.address as ADDRESS1,
        c.address_2 as ADDRESS2,
        c.city as CITY,
        c.state as STATE,
        c.zip as ZIPCODE,
        c.cell_phone as PHONE_H,
        '' as PHONE_W,
        w.eligibility_date as ACTIVATION_DTE,
        w.termination_date as CANCEL_DTE,
        IF(w.termination_date IS NULL OR (w.termination_date != w.eligibility_date AND DATE(NOW()) < DATE(w.termination_date)),'N','C') as STATUS,
        c.email as EMAIL_ADDR,
        '' as OFFER_TYPE,
        'E' as FULFILLMENT_FLG,

        ce.id as cust_enrollment_id,
        ce.first_eligibility_date,
        ce.most_recent_date 
        FROM customer c 
        JOIN customer s ON (s.id=c.sponsor_id)
        JOIN website_subscriptions w ON (w.customer_id=c.id)
        JOIN customer_enrollment ce ON (ce.website_id=w.id)
        JOIN prd_main p ON (p.id=w.product_id)
        WHERE 
        c.is_deleted='N' AND 
        c.status in ('Active','Inactive') AND 
        c.type='Customer' AND 
        w.status not in('Pending','Post Payment') AND 
        (p.id IN ($prd_ids) OR p.parent_product_id IN($prd_ids)) 
        $incr 
        GROUP BY c.id
        ORDER BY w.eligibility_date ASC";
        $policy_res = $pdo->select($selSql,$sch_params);


        if($policy_res) {
            foreach ($policy_res as $key => $row) {
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
                if(!(!empty($row['first_eligibility_date']) && strtotime($row['first_eligibility_date']) > 0)){
                    $req_data['first_eligibility_date'] = "msqlfunc_NOW()";
                }
                $pdo->update("customer_enrollment",$req_data,$req_where);

                //CLEAN CSV CELL
                foreach ($row as $row_key => $row_value) {
                    if($row[$row_key] != '' && !in_array($row_key,array('EMAIL_ADDR'))){
                        $row[$row_key] = clean_csv_cell($row[$row_key]);
                    }                    
                }

               //  check if member is already exists or new member
                $addMember = false;
                $is_Updated = false;
                if($generate_change_file){
                    if(in_array($row['MEMBER_ID'],$ExistsMemberIds)){
                        if($row['STATUS'] == "N" && $ExistsMembersData[$row['MEMBER_ID']]['STATUS'] == "C") {
                            $row['STATUS'] = "R"; //R=Re-activation memID
                            $addMember = true;
                        } else {
                            $is_Updated = compare_HEALTHY_STEP_AUGEO_ROW($ExistsMembersData[$row['MEMBER_ID']],$row);
                            //if member detail is updates then add member to eligibility file
                            if($is_Updated === true) {
                                $row['STATUS'] = "U"; //U=Update(pass entire record)
                                $addMember = true;
                            } else {
                                $addMember = false;
                            }
                        }


                    } else {
                        //if new member then add member to eligibility file
                        $addMember = true;
                    }
                }

                $tmp_csv_row = '';
                $tmp_txt_row = '';
                foreach ($file_columns as $clm_key => $column_data) {
                    if($clm_key > 0) {
                        $tmp_csv_row .= $csv_seprator;
                    }

                    $column_key = $column_data['key'];
                    if($column_data['data_type'] == "date") {
                        if(!empty($row[$column_key]) && abs(strtotime($row[$column_key])) > 0) {
                            $row[$column_key] = date("mdy",strtotime($row[$column_key]));
                        } else {
                            $row[$column_key] = '';
                        }
                    }
                    if($column_key == "STATE") {
                        $row[$column_key] = (isset($allStateShortName[$row[$column_key]])?$allStateShortName[$row[$column_key]]:$row[$column_key]);
                    }

                    $tmp_csv_row .= $field_seprator . $row[$column_key] . $field_seprator;
                    $tmp_txt_row .= substr(str_pad($row[$column_key],$column_data['length']),0,$column_data['length']);
                }
                $tmp_csv_row .= $csv_line;
                $tmp_txt_row .= $txt_line;

                if(!$is_error){
                    $content .= $tmp_csv_row;
                    $txt_content .= $tmp_txt_row;
                    if($addMember) {
                        $eligibility_content .= $tmp_csv_row;
                        $txt_eligibility_content .= $tmp_txt_row;
                    }
                }

                if($is_error) {
                    $generate_error_file = true;
                    $error_content .= $tmp_csv_row;
                }
            }
        }
        
        if($content != '' && ($file_type == "schedule_change_file" || $file_type == "add_change_file")){
            $file_update = file_put_contents($file,$content);
            if($generate_change_file){
                $content = $eligibility_content;
                $txt_content = $txt_eligibility_content;
            }
        }

        if(!$generate_error_file) {
            $error_content = '';
        }
    }
?>