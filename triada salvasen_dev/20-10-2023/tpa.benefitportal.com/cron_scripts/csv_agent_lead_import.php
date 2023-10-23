<?php
include_once dirname(__DIR__) . "/cron_scripts/connect.php";
include_once dirname(__DIR__) . '/libs/twilio-php-master/Twilio/autoload.php';
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 30000);
/*
* NOTE :
* WHEN YOU CHANGE ON THIS FILE, PLEASE ALSO DO CHANGE IN BELOW LISTED FILES IF REQUIRED
* smarteapp.com\agents\ajax_upload_csv.php 
* smarteapp.com\includes\functions.php csv_agent_lead_import method
*    
*/
//$DEFAULT_ORDER_EMAIL = array("shailesh@cyberxllc.com");
//trigger_mail_to_email("CSV Agent Lead Import Processed", $DEFAULT_ORDER_EMAIL,"smartE : CSV Agent Lead Import");

$REAL_IP_ADDRESS = get_real_ipaddress();
$check_already_running = $pdo->selectOne("SELECT * FROM csv_agent_leads WHERE status='Pending' AND is_running='Y'");
if (!empty($check_already_running)) {
    exit("Already Running");
}

$csv_sql = "SELECT * FROM csv_agent_leads WHERE status='Pending' AND is_running='N' LIMIT 0,3";
$csv_res = $pdo->select($csv_sql);
if(empty($csv_res)) {
    exit("No import request found");
}

if (count($csv_res) > 0) {
    foreach ($csv_res as $file_row) {
        $csv_where = array(
            "clause" => "id=:id",
            "params" => array(
                ":id" => $file_row['id']
            )
        );
        $pdo->update('csv_agent_leads',array('is_running' => 'Y'), $csv_where);
        
        $agent_sql = "SELECT id,type,sponsor_id,rep_id,CONCAT(fname,' ',lname) as agent_name,business_name as group_name FROM customer where id=:id";
        $agent_row = $pdo->selectOne($agent_sql, array(":id" => $file_row['agent_id']));

        $admin_row = array();
        if(!empty($file_row['import_lead_admin_id'])) {
            $admin_row = $pdo->selectOne('SELECT * FROM admin WHERE id=:id',array(":id" => $file_row['import_lead_admin_id']));
        }

        $lead_tag = $file_row['lead_tag'];
        $csv_file = $CSV_DIR . $file_row['file_name'];

        $csv_file_rows = csvToArraywithFields($csv_file);

        $existing_lead_count = $file_row['existing_leads'];
        $new_lead_count = $file_row['import_leads'];
        $total_processed_count = 0;
        $mobile_leads = $file_row['mobile_leads'];
        $landline_leads = $file_row['landline_leads'];
        $voip_leads = $file_row['voip_leads'];
        $unknown_leads = $file_row['unknown_leads'];

        $last_inserted_lead_tag = '';
        foreach ($csv_file_rows as $value) {

            //Checking script cancelled or not
            if(count($csv_file_rows) < 101) {
                $tmp_file_row = $pdo->selectOne("SELECT status FROM csv_agent_leads WHERE id=:id",array(":id" => $file_row['id']));
                if(!empty($tmp_file_row['status']) && $tmp_file_row['status'] == "Cancel") {
                    break;    
                }
            } else {
                if ($total_processed_count == 0 || $total_processed_count % 10 == 0) {
                    $tmp_file_row = $pdo->selectOne("SELECT status FROM csv_agent_leads WHERE id=:id",array(":id" => $file_row['id']));
                    if(!empty($tmp_file_row['status']) && $tmp_file_row['status'] == "Cancel") {
                        break;    
                    }
                }
            }
            

            $value = array_map('trim', $value);
            $trim_key = array_map('trim',array_keys($value));
            $value = array_combine($trim_key, $value);

            $total_processed_count++;

            if ($total_processed_count < $file_row['total_processed']) {
                continue; //continue after lead count is greater than previously imported leads
            }

            if (($total_processed_count - $file_row['total_processed']) == 500) {
                break;// stopping and only importing 500 leads at a time from csv
            }

            if ($total_processed_count % 10 == 0) {
                sleep(1); //pause 1 second after 10 leads are imported
            }

            $company_name = $file_row['company_name_field'];
            $fname = $file_row['fname_field'];
            $lname = $file_row['lname_field'];
            $cell_phone = $file_row['cell_phone_field'];
            $email = $file_row['email_field'];
            $state = $file_row['state_field'];
            $state_tag = $file_row['state_tag_field'];
            $email2 = $file_row['email2_field'];
            $school_district = $file_row['school_district_field'];
            $send_date = $file_row['send_date_field'];
            $active_since = $file_row['active_since_field'];
            $address = $file_row['address_field'];
            $address2 = $file_row['address2_field'];

            $pre_tax_deductions_field = $file_row['pre_tax_deductions_field'];
            $post_tax_deductions_field = $file_row['post_tax_deductions_field'];
            $w4_filing_status_field = $file_row['w4_filing_status_field'];
            $w4_no_of_allowances_field = $file_row['w4_no_of_allowances_field'];
            $w4_two_jobs_field = $file_row['w4_two_jobs_field'];
            $w4_dependents_amount_field = $file_row['w4_dependents_amount_field'];
            $w4_4a_other_income_field = $file_row['w4_4a_other_income_field'];
            $w4_4b_deductions_field = $file_row['w4_4b_deductions_field'];
            $w4_additional_withholding_field = $file_row['w4_additional_withholding_field'];
            $state_filing_status_field = $file_row['state_filing_status_field'];
            $state_dependents_field = $file_row['state_dependents_field'];
            $state_additional_withholdings_field = $file_row['state_additional_withholdings_field'];

            $error_reporting_arr = array(
                'agent_csv_id' => $file_row['id'],
            );
            if (!empty($value[$company_name])) {
                $error_reporting_arr['company_name'] = $value[$company_name];
            }
            if (!empty($value[$fname])) {
                $error_reporting_arr['fname'] = $value[$fname];
            }
            if (!empty($value[$lname])) {
                $error_reporting_arr['lname'] = $value[$lname];
            }
            if (!empty($value[$cell_phone])) {
                $error_reporting_arr['cell_phone'] = $value[$cell_phone];
            }
            if (!empty($value[$email])) {
                $error_reporting_arr['email'] = $value[$email];
            }
            if (!empty($value[$state])) {
                $error_reporting_arr['state'] = $value[$state];
            }
            if (!empty($value[$state_tag])) {
                $error_reporting_arr['state_tag'] = $value[$state_tag];
            }
            if (!empty($value[$email2])) {
                $error_reporting_arr['email2'] = $value[$email2];
            }
            if (!empty($value[$address])) {
                $error_reporting_arr['address'] = $value[$address];
            }
            if (!empty($value[$school_district])) {
                $error_reporting_arr['school_district'] = $value[$school_district];
            }
            if (!empty($value[$send_date])) {
                $error_reporting_arr['send_date'] = date("Y-m-d", strtotime($value[$send_date]));
            }
            if (!empty($value[$active_since])) {
                $error_reporting_arr['active_since'] = date("Y-m-d", strtotime($value[$active_since]));
            }
            if($agent_row['type']=='Group'){
                $enrollee_id = $file_row['enrollee_id_field'];
                $annual_earnings = $file_row['annual_earnings_field'];
                $employee_type = $file_row['employee_type_field'];
                $hire_date = $file_row['hire_date_field'];
                $termination_date = $file_row['termination_date_field'];
                $city = $file_row['city_field'];
                $zip = $file_row['zip_field'];
                $gender = $file_row['gender_field'];
                $dob = $file_row['dob_field'];
                $ssn = $file_row['ssn_field'];
                $class_name = $file_row['class_name_field'];
                $coverage_period = $file_row['coverage_period_field'];

                if (!empty($value[$enrollee_id])) {
                    $error_reporting_arr['enrollee_id'] = $value[$enrollee_id];
                }
                if (!empty($value[$annual_earnings])) {
                    $error_reporting_arr['annual_earnings'] = $value[$annual_earnings];
                }
                if (!empty($value[$employee_type])) {
                    $error_reporting_arr['employee_type'] = $value[$employee_type];
                }
                if (!empty($value[$hire_date])) {
                    $error_reporting_arr['hire_date'] = $value[$hire_date];
                }
                /*if (!empty($value[$termination_date])) {
                    $error_reporting_arr['termination_date'] = $value[$termination_date];
                }*/
                if (!empty($value[$city])) {
                    $error_reporting_arr['city'] = $value[$city];
                }
                if (!empty($value[$gender])) {
                    $error_reporting_arr['gender'] = $value[$gender];
                }
                if (!empty($value[$dob])) {
                    $error_reporting_arr['dob'] = $value[$dob];
                }
                if (!empty($value[$ssn])) {
                    $error_reporting_arr['ssn'] = $value[$ssn];
                }
                if (!empty($value[$class_name])) {
                    $error_reporting_arr['class_name'] = $value[$class_name];
                }
                if (!empty($value[$zip])) {
                    $error_reporting_arr['zip'] = $value[$zip];
                }
            }

            $is_error = false;

            if(empty($value[$fname])){
                $error_reporting_arr['reason'][] = "First name is empty";
                // $pdo->insert("agent_csv_log", $error_reporting_arr);
                $is_error = true;
            }
            if(empty($value[$lname])){
                $error_reporting_arr['reason'][] = "Last name is empty";
                // $pdo->insert("agent_csv_log", $error_reporting_arr);
                $is_error = true;
            }
            if(empty($value[$state])){
                $error_reporting_arr['reason'][] = "State is empty";
                // $pdo->insert("agent_csv_log", $error_reporting_arr);
                $is_error = true;
            } 
            if(empty($value[$email])) {
                $error_reporting_arr['reason'][] = "Email is empty";
                // $pdo->insert("agent_csv_log", $error_reporting_arr);
                $is_error = true;
            }else{
				if (!preg_match('/^([^@\s]+)@((?:[-a-z0-9]+\.)+[a-z]{2,})$/i', trim($value[$email]))) {
					$error_reporting_arr['reason'][] = "Valid Email is required";
					// $pdo->insert("agent_csv_log", $error_reporting_arr);
					$is_error = true;
				}
			}
            if(empty($value[$cell_phone])){
                $error_reporting_arr['reason'][] = "Phone Number is empty";
                // $pdo->insert("agent_csv_log", $error_reporting_arr);
                $is_error = true;
            }else{
                $value[$cell_phone] = str_replace(array(" ", "(", ")", "-", "+1"), array("", "", "", "", ""), trim($value[$cell_phone]));
                $value[$cell_phone] = str_replace("+","",$value[$cell_phone]);
                if(!is_numeric($value[$cell_phone])){
                    $error_reporting_arr['reason'][] = "Invalid Phone Number.";
					$is_error = true;
                }else{
                    if(strlen($value[$cell_phone]) > 10){
                        $error_reporting_arr['reason'][] = "Phone number Maximum length 10 required";
                        $is_error = true;
                        // $pdo->insert("agent_csv_log", $error_reporting_arr);
                    } else if(strlen($value[$cell_phone]) < 10){
                        $error_reporting_arr['reason'][] = "Phone number Minimum length 10 required";
                        $is_error = true;
                        // $pdo->insert("agent_csv_log", $error_reporting_arr);
                    }
                }
			}

            $existsGroupEmployee = 0;
            if($agent_row['type']=='Group'){

                if (empty($value[$enrollee_id])) {
                    $error_reporting_arr['reason'][] = "Enrollee ID is empty";
                    // $pdo->insert("agent_csv_log", $error_reporting_arr);
                    $is_error = true;
                }else if(!empty($value[$enrollee_id])){
                    $checkEmpId_sql = "SELECT id, employee_id,status FROM leads WHERE employee_id = :employee_id AND is_deleted='N' AND sponsor_id=:sponsor";
                    $whereEmpId = array(':employee_id' => makeSafe($value[$enrollee_id]),":sponsor"=>$file_row['agent_id']);
                    $resultEmpId_res = $pdo->selectOne($checkEmpId_sql, $whereEmpId);
                    if (count($resultEmpId_res)>0 && $resultEmpId_res['status'] != 'New') {
                        $error_reporting_arr['reason'][] = "Enrollee ID converted to ".$resultEmpId_res['status'];
                        $is_error = true;
                    }
                    if(count($resultEmpId_res)>0 && $resultEmpId_res['status'] == 'New'){
                        $existsGroupEmployee = $resultEmpId_res['id'];
                    }
                }
                /*if (empty($value[$annual_earnings])) {
                    $error_reporting_arr['reason'][] = "Annual Earning is empty";
                    // $pdo->insert("agent_csv_log", $error_reporting_arr);
                    $is_error = true;
                }*/
                if (empty($value[$employee_type])) {
                    $error_reporting_arr['reason'][] = "Employee Type is empty";
                    // $pdo->insert("agent_csv_log", $error_reporting_arr);
                    $is_error = true;
                }
                if (empty($value[$hire_date])) {
                    $error_reporting_arr['reason'][] = "Relationship Date is empty";
                    // $pdo->insert("agent_csv_log", $error_reporting_arr);
                    $is_error = true;
                }
                /*if(!empty($value[$employee_type]) && $value[$employee_type] == 'Renew'){
                    if (empty($value[$termination_date])) {
                        $error_reporting_arr['reason'][] = "Termination Date is empty";
                        // $pdo->insert("agent_csv_log", $error_reporting_arr);
                        $is_error = true;
                    }
                }*/
                if (empty($value[$city])) {
                    $error_reporting_arr['reason'][] = "City is empty";
                    // $pdo->insert("agent_csv_log", $error_reporting_arr);
                    $is_error = true;
                }
                if (empty($value[$gender])) {
                    $error_reporting_arr['reason'][] = "Gender is empty";
                    // $pdo->insert("agent_csv_log", $error_reporting_arr);
                    $is_error = true;
                }
                if (empty($value[$dob])) {
                    $error_reporting_arr['reason'][] = "Birth Date is empty";
                    // $pdo->insert("agent_csv_log", $error_reporting_arr);
                    $is_error = true;
                }
                /*if (!empty($value[$ssn])) {
                    $error_reporting_arr['reason'][] = "SSN is empty";
                    // $pdo->insert("agent_csv_log", $error_reporting_arr);
                    $is_error = true;
                }*/
                if (empty($value[$class_name])) {
                    $error_reporting_arr['reason'][] = "Class Name is empty";
                    // $pdo->insert("agent_csv_log", $error_reporting_arr);
                    $is_error = true;
                }
                if (empty($value[$zip])) {
                    $error_reporting_arr['reason'][] = "Zip is empty";
                    // $pdo->insert("agent_csv_log", $error_reporting_arr);
                    $is_error = true;
                }
                if(!empty($value[$zip])){
                    $zipRes=$pdo->selectOne("SELECT id,state_code FROM zip_code WHERE zip_code=:zip_code",array(":zip_code"=>$value[$zip]));

                    if(empty($zipRes)){
                        $error_reporting_arr['reason'][] = "Zip is not valid";
                        // $pdo->insert("agent_csv_log", $error_reporting_arr);
                        $is_error = true;
                    }else{

                      $stateRes=$pdo->selectOne("SELECT name,short_name FROM states_c WHERE country_id = '231' AND short_name=:short_name",array(":short_name"=>$zipRes['state_code']));

                      if(empty($stateRes)){
                        $error_reporting_arr['reason'][] = "Zip is not valid";
                        // $pdo->insert("agent_csv_log", $error_reporting_arr);
                        $is_error = true;
                      }else{
                        if((!empty($value[$state]) && trim($stateRes['short_name']) != trim($value[$state]))){
                            $error_reporting_arr['reason'][] = "Zip is not valid";
                            // $pdo->insert("agent_csv_log", $error_reporting_arr);
                            $is_error = true;
                        }
                      }
                    }
                }

                if(!empty($value[$pre_tax_deductions_field]) && !is_numeric($value[$pre_tax_deductions_field])){
                    $error_reporting_arr['reason'][] = "Valid Pre Tax Deduction is required";
                    $is_error = true;
                }
                if(!empty($value[$post_tax_deductions_field]) && !is_numeric($value[$post_tax_deductions_field])){
                    $error_reporting_arr['reason'][] = "Valid Post Tax Deduction is required";
                    $is_error = true;
                }
                if(!empty($value[$w4_filing_status_field]) && !in_array(strtolower($value[$w4_filing_status_field]),array('single','married'))){
                    $error_reporting_arr['reason'][] = "Valid filing status single/married is required";
                    $is_error = true;
                }
                if(!empty($value[$w4_no_of_allowances_field])){
                    if(is_numeric($value[$w4_no_of_allowances_field]) && (floor($value[$w4_no_of_allowances_field]) != $value[$w4_no_of_allowances_field])){
                        $error_reporting_arr['reason'][] = "Decimal value not acceptable for w4 no of allowances is required";
                        $is_error = true;
                    }else if(!is_numeric($value[$w4_no_of_allowances_field])){
                        $error_reporting_arr['reason'][] = "Valid w4 no of allowances is required";
                        $is_error = true;
                    }else if($value[$w4_no_of_allowances_field] > 12){
                        $error_reporting_arr['reason'][] = "Maximum 12 w4 no of allowances is allowed";
                        $is_error = true;
                    }
                }
                if(!empty($value[$w4_two_jobs_field]) && !in_array(strtolower($value[$w4_two_jobs_field]),array('yes','no'))){
                    $error_reporting_arr['reason'][] = "Valid answer yes/no is required for w4 two jobs";
                    $is_error = true;
                }
                if(!empty($value[$w4_dependents_amount_field])  && !is_numeric($value[$w4_dependents_amount_field])){
                    $error_reporting_arr['reason'][] = "Valid dependents amount is required";
                    $is_error = true;
                }
                if(!empty($value[$w4_4a_other_income_field])  && !is_numeric($value[$w4_4a_other_income_field])){
                    $error_reporting_arr['reason'][] = "Valid w4 4a other income amount is required";
                    $is_error = true;
                }
                if(!empty($value[$w4_4b_deductions_field])  && !is_numeric($value[$w4_4b_deductions_field])){
                    $error_reporting_arr['reason'][] = "Valid 4b deductions is required";
                    $is_error = true;
                }
                if(!empty($value[$w4_additional_withholding_field])  && !is_numeric($value[$w4_additional_withholding_field])){
                    $error_reporting_arr['reason'][] = "Valid additional withholding is required";
                    $is_error = true;
                }
                if(!empty($value[$state_filing_status_field]) && !in_array(strtolower($value[$state_filing_status_field]),array('single','married'))){
                    $error_reporting_arr['reason'][] = "Valid state filing status single/married is required";
                    $is_error = true;
                }
                if(!empty($value[$state_dependents_field])  && !is_numeric($value[$state_dependents_field])){
                    $error_reporting_arr['reason'][] = "Valid state dependents is required";
                    $is_error = true;
                }
                if(!empty($value[$state_additional_withholdings_field])  && !is_numeric($value[$state_additional_withholdings_field])){
                    $error_reporting_arr['reason'][] = "Valid state additional withholdings is required";
                    $is_error = true;
                }
            }

            if($is_error && !empty($error_reporting_arr['reason'])){
				$error_reporting_arr['reason'] = implode(',<br>',$error_reporting_arr['reason']);
				$pdo->insert("agent_csv_log", $error_reporting_arr);
			}

            if(!$is_error){
                if ($value[$email] != "" || $value[$cell_phone] != "") {                  
                    
                    $exist = false;

                    $tmp_cell_phone = '';
                    if(isset($value[$cell_phone])) {
                        $tmp_cell_phone = str_replace(array(" ", "(", ")", "-", "+1"), array("", "", "", "", ""), trim($value[$cell_phone]));    
                    }
                    

                    if ($tmp_cell_phone != '') {
                        $tmp_cell_phone = substr($tmp_cell_phone, 0, 10);
                    }

                    $is_unsubscribed = false;

                    $tmp_email = '';
                    if(isset($value[$email])) {
                        $tmp_email = trim($value[$email]);    
                    }                    
                    if (!$exist && $tmp_email != '') {
                        $sel_unsub_leads = "SELECT id FROM leads WHERE (is_email_unsubscribe = 'Y' OR is_sms_unsubscribe = 'Y' OR status IN ('Request Do Not Contact','Do Not Contact')) AND email = :email AND is_deleted='N'";
                        $where_unsub_leads = array(":email" => $tmp_email);
                        $res_unsub_leads = $pdo->selectOne($sel_unsub_leads, $where_unsub_leads);
                        if (!empty($res_unsub_leads)) {
                            $is_unsubscribed = true;
                            if (!$exist) {
                                $error_reporting_arr['reason'] = "Email on unsubscribed list";
                                $pdo->insert("agent_csv_log", $error_reporting_arr);
                            }
                            $exist = true;
                        }
                    }

                    if (!$exist && !$is_unsubscribed) {
                        if ($tmp_cell_phone != '') {
                            $sel_unsub_leads = "SELECT id FROM leads WHERE (is_email_unsubscribe = 'Y' OR is_sms_unsubscribe = 'Y' OR status IN ('Request Do Not Contact','Do Not Contact')) AND cell_phone = :cell_phone AND is_deleted='N'";
                            $where_unsub_leads = array(":cell_phone" => $tmp_cell_phone);
                            $res_unsub_leads = $pdo->selectOne($sel_unsub_leads, $where_unsub_leads);
                            if (!empty($res_unsub_leads)) {
                                $is_unsubscribed = true;
                                if (!$exist) {
                                    $error_reporting_arr['reason'][] = "Phone number on Do Not Call list";
                                    // $pdo->insert("agent_csv_log", $error_reporting_arr);
                                }
                                $exist = true;
                            }
                        }
                    }

                    if($agent_row['type']=='Group'){
                        $coverage_start_date = "";
                        $coverage_end_date = "";
                        if (!empty($value[$coverage_period])) {
                            $error_reporting_arr['coverage_period'] = $value[$coverage_period];
                            $coverage_arr = explode("-",$value[$coverage_period]);
                            $coverage_start_date = isset($coverage_arr[0]) ? $coverage_arr[0] : '';
                            $coverage_end_date = isset($coverage_arr[1]) ? $coverage_arr[1] : '';
                        }
                        $group_company_id=0;
                        $group_classes_id=0;
                        $group_coverage_id=0;

                        $sqlClass="SELECT id FROM group_classes where class_name = :class_name and group_id=:group_id";
                        $resClass=$pdo->select($sqlClass,array(":class_name"=>$value[$class_name],":group_id"=>$file_row['agent_id']));

                        if(empty($resClass)){
                            $error_reporting_arr['reason'][] = "Group Class Not Found";
                            // $pdo->insert("agent_csv_log", $error_reporting_arr);
                            $exist = true;
                        }else if(count($resClass) > 1){
                            $error_reporting_arr['reason'][] = "Multiple Group Class Found";
                            // $pdo->insert("agent_csv_log", $error_reporting_arr);
                            $exist = true;
                        }else{
                            $group_classes_id = $resClass[0]['id'];
                        }

                        $sqlCompany="SELECT id FROM group_company where name = :company_name and group_id=:group_id";
                        $resCompany=$pdo->select($sqlCompany,array(":company_name"=>$value[$company_name],":group_id"=>$file_row['agent_id']));
                        if($agent_row['group_name'] == $value[$company_name]){
                            $group_company_id = 0;
                        }else if(empty($resCompany)){
                            $error_reporting_arr['reason'][] = "Group Company Not Found";
                            // $pdo->insert("agent_csv_log", $error_reporting_arr);
                            $exist = true;
                        }else if(count($resCompany) > 1){
                            $error_reporting_arr['reason'][] = "Multiple Group Company Found";
                            // $pdo->insert("agent_csv_log", $error_reporting_arr);
                            $exist = true;
                        }else{
                            $group_company_id = $resCompany[0]['id'];
                        }
                        if(empty($coverage_start_date) || empty($coverage_end_date)){
                            $error_reporting_arr['reason'][] = "Group Coverage Period Not Found";
                            // $pdo->insert("agent_csv_log", $error_reporting_arr);
                            $exist = true;
                        }else{
                            $coverage_start_date = date('Y-m-d',strtotime(trim($coverage_start_date)));
                            $coverage_end_date = date('Y-m-d',strtotime(trim($coverage_end_date)));

                            $sqlCoverage="SELECT gc.id,gc.coverage_period_name FROM group_coverage_period gc 
                                JOIN group_coverage_period_offering gco ON (gc.id = gco.group_coverage_period_id AND gco.is_deleted='N')
                                WHERE gc.group_id=:group_id AND gco.class_id=:class_id AND gco.status='Active' AND gc.coverage_period_start =:start_date AND gc.coverage_period_end = :end_date group by gc.id";

                            $resCoverage=$pdo->select($sqlCoverage,array(":class_id"=>$group_classes_id,":group_id"=>$file_row['agent_id'],":start_date"=>$coverage_start_date,":end_date"=>$coverage_end_date));

                            if(empty($resCoverage)){
                                $error_reporting_arr['reason'][] = "Group Coverage Not Found";
                                // $pdo->insert("agent_csv_log", $error_reporting_arr);
                                $exist = true;
                            }else if(count($resCoverage) > 1){
                                $error_reporting_arr['reason'][] = "Multiple Group Coverage Found";
                                // $pdo->insert("agent_csv_log", $error_reporting_arr);
                                $exist = true;
                            }else{
                                $group_coverage_id = $resCoverage[0]['id'];
                            }
                        }
                        if($exist && !empty($error_reporting_arr['reason'])){
                            $error_reporting_arr['reason'] = implode(',<br>',$error_reporting_arr['reason']);
							$pdo->insert("agent_csv_log", $error_reporting_arr);
                        }
                    }

                    if (!$exist && !$is_unsubscribed) {
                        if ($tmp_email != '') {
                            $checkincr = "";
                            $selEmail = "SELECT id, email, cell_phone FROM leads WHERE sponsor_id=:sponsor AND email=:email AND lead_type=:lead_type AND is_deleted='N' AND id!=:id";
                            $whereEmail = array(":email" => $tmp_email,":lead_type" => $file_row['lead_type'],":sponsor" => $file_row['agent_id'],':id'=>$existsGroupEmployee);
                            $rowEmail = $pdo->selectOne($selEmail, $whereEmail);
                            if ($rowEmail && $file_row['lead_type'] != "Agent/Group") {
                                $error_reporting_arr['reason'][] = "Email Address attached to existing lead";
                                // $pdo->insert("agent_csv_log", $error_reporting_arr);
                                $exist = true;
                            } else {
                                if($file_row['lead_type'] == "Member") {
                                    $cust_selEmail = "SELECT id FROM customer WHERE email=:email AND type IN('Customer')";
                                    $cust_whereEmail = array(':email' => makeSafe($tmp_email));
                                    $cust_rowEmail = $pdo->selectOne($cust_selEmail, $cust_whereEmail);
                                    if ($cust_rowEmail) {
                                        $error_reporting_arr['reason'][] = "Email Address attached to existing Member";
                                        // $pdo->insert("agent_csv_log", $error_reporting_arr);
                                        $exist = true;
                                    }
                                } else {
                                    /*$cust_selEmail = "SELECT id FROM customer WHERE email=:email AND type IN('Agent','Group')";
                                    $cust_whereEmail = array(':email' => makeSafe($tmp_email));
                                    $cust_rowEmail = $pdo->selectOne($cust_selEmail, $cust_whereEmail);
                                    if ($cust_rowEmail) {
                                        $error_reporting_arr['reason'][] = "Email Address attached to existing Agent/Group";
                                        // $pdo->insert("agent_csv_log", $error_reporting_arr);
                                        $exist = true;
                                    }*/   
                                }
                            }
                        } elseif ($tmp_cell_phone != '') {
                            /*$selCell = "SELECT id,email,cell_phone FROM leads WHERE sponsor_id=:sponsor AND cell_phone=:cell_phone AND lead_type=:lead_type AND is_deleted='N'";
                            $whereCell = array(":cell_phone" => $tmp_cell_phone,":sponsor" => $file_row['agent_id'],":lead_type" => $file_row['lead_type']);
                            $rowCell = $pdo->selectOne($selCell, $whereCell);
                            if ($rowCell) {
                                $error_reporting_arr['reason'][] = "Phone Number attached to existing lead";
                                // $pdo->insert("agent_csv_log", $error_reporting_arr);
                                $exist = true;
                            }*/
                        }

                        if($exist && !empty($error_reporting_arr['reason'])){
							$error_reporting_arr['reason'] = implode(',<br>',$error_reporting_arr['reason']);
							$pdo->insert("agent_csv_log", $error_reporting_arr);
                        } 
                        if (!$exist) {
                            $lead_disp_id = get_lead_id();
                            $lead_data = array(
                                "sponsor_id" => $file_row['agent_id'],
                                "lead_profession_type" => "Individual",
                                "lead_type" => $file_row['lead_type'],
                                'name'=>trim($value[$fname]).' '.trim($value[$lname]),
                                "fname" => trim($value[$fname]),
                                "lname" => trim($value[$lname]),
                                "cell_phone" => ($tmp_cell_phone ? $tmp_cell_phone : ''),
                                "opt_in_type" => $file_row['lead_tag'],
                                "generate_type" => "CSV",
                                "status" => "New",
                                "sms_scheduled" => 'N',
                                "ip_address" => !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'],
                                "created_at" => "msqlfunc_NOW()",
                                "updated_at" => "msqlfunc_NOW()",
                            );

                            if($existsGroupEmployee == 0){
                                $lead_data['lead_id'] = $lead_disp_id;
                            }

                            if (!empty(trim($value[$company_name]))) {
                                $lead_data['company_name'] = trim($value[$company_name]);
                            }

                            if (!empty(trim($value[$email]))) {
                                $lead_data['email'] = trim($value[$email]);
                            }
                            if (!empty(trim($value[$email2]))) {
                                $lead_data['email2'] = trim($value[$email2]);
                            }
                            if (!empty(trim($value[$state]))) {
                                $sqlState="SELECT name FROM states_c WHERE name=:state OR short_name =:state";
                                $resState=$pdo->selectOne($sqlState,array(":state"=>$value[$state]));

                                if(!empty($resState)){
                                    $lead_data['state'] = $resState['name'];
                                }
                            }
                            if (!empty(trim($value[$address]))) {
                                $lead_data['address'] = trim($value[$address]);
                            }
                            if (!empty(trim($value[$address2]))) {
                                $lead_data['address2'] = trim($value[$address2]);
                            }
                            if (!empty(trim($value[$state_tag]))) {
                                $lead_data['state_tag'] = trim($value[$state_tag]);
                            }
                            if (!empty(trim($value[$school_district]))) {
                                $lead_data['school_district'] = trim($value[$school_district]);
                            }
                            if (!empty($value[$send_date])) {
                                $lead_data['send_date'] = date('Y-m-d', strtotime($value[$send_date]));
                            }
                            if (!empty($value[$active_since])) {
                                $lead_data['active_since'] = date('Y-m-d', strtotime($value[$active_since]));
                            }
                            if($agent_row['type']=='Group'){
                                if (!empty($value[$enrollee_id])) {
                                    $lead_data['employee_id'] = trim($value[$enrollee_id]);
                                }
                                if (!empty($value[$annual_earnings])) {
                                    $lead_data['income'] = trim($value[$annual_earnings]);
                                }
                                if (!empty($value[$employee_type])) {
                                    $lead_data['employee_type'] = trim($value[$employee_type]);
                                }
                                if (!empty($value[$hire_date])) {
                                    $lead_data['hire_date'] = date('Y-m-d', strtotime($value[$hire_date]));
                                }
                                /*if (!empty($value[$termination_date])) {
                                    $lead_data['termination_date'] = date('Y-m-d', strtotime($value[$termination_date]));
                                }*/
                                if (!empty($value[$city])) {
                                    $lead_data['city'] = trim($value[$city]);
                                }
                                if (!empty($value[$city])) {
                                    $lead_data['zip'] = trim($value[$zip]);
                                }
                                if (!empty($value[$gender])) {
                                    $lead_data['gender'] = trim($value[$gender]);
                                }
                                if (!empty($value[$dob])) {
                                    $lead_data['birth_date'] = date('Y-m-d', strtotime($value[$dob]));
                                }
                                if (!empty($value[$ssn])) {
                                    $ssn_last_four_digit=substr($value[$ssn],-4,4);
                                    $ssn="msqlfunc_AES_ENCRYPT('" . $value[$ssn] . "','" . $CREDIT_CARD_ENC_KEY . "')";
                                    $lead_data['is_ssn_itin'] = 'Y';
                                    $lead_data['ssn_itin_num'] = $ssn;
                                    $lead_data['last_four_ssn'] = $ssn_last_four_digit;
                                }
                                if(isset($group_company_id)){
                                    $lead_data['group_company_id']=$group_company_id;
                                }
                                if(!empty($group_classes_id)){
                                    $lead_data['group_classes_id']=$group_classes_id;
                                }
                                if(!empty($group_coverage_id)){
                                    $lead_data['group_coverage_id']=$group_coverage_id;
                                }

                                $tadDetails = [
                                    "pre_tax_deductions_field" => !empty($value[$pre_tax_deductions_field]) ? $value[$pre_tax_deductions_field] : 0,
                                    "post_tax_deductions_field" => !empty($value[$post_tax_deductions_field]) ? $value[$post_tax_deductions_field] : 0,
                                    "w4_filing_status_field" => !empty($value[$w4_filing_status_field]) ? $value[$w4_filing_status_field] : 'Single',
                                    "w4_no_of_allowances_field" => !empty($value[$w4_no_of_allowances_field]) ? $value[$w4_no_of_allowances_field] : 0,
                                    "w4_two_jobs_field" => !empty($value[$w4_two_jobs_field]) ? $value[$w4_two_jobs_field] : 'No',
                                    "w4_dependents_amount_field" => !empty($value[$w4_dependents_amount_field]) ? $value[$w4_dependents_amount_field] : 0,
                                    "w4_4a_other_income_field" => !empty($value[$w4_4a_other_income_field]) ? $value[$w4_4a_other_income_field] : 0,
                                    "w4_4b_deductions_field" => !empty($value[$w4_4b_deductions_field]) ? $value[$w4_4b_deductions_field] : 0,
                                    "w4_additional_withholding_field" => !empty($value[$w4_additional_withholding_field]) ? $value[$w4_additional_withholding_field] : 0,
                                    "state_filing_status_field" => !empty($value[$state_filing_status_field]) ? $value[$state_filing_status_field] : 'Single',
                                    "state_dependents_field" => !empty($value[$state_dependents_field]) ? $value[$state_dependents_field] : 0,
                                    "state_additional_withholdings_field" => !empty($value[$state_additional_withholdings_field]) ? $value[$state_additional_withholdings_field] : 0,
                                ];
                                $lead_data = array_merge($lead_data,$tadDetails);
                                
                            }

                            $updatedData = [];
                            if($existsGroupEmployee > 0){
                                $ins_lead_id = $existsGroupEmployee;

                                $csv_row_where = array(
                                    "clause" => "id=:id",
                                    "params" => array(":id" => $existsGroupEmployee)
                                );
                                $updatedData = $pdo->update('leads', $lead_data, $csv_row_where,true);

                                if(!empty($group_coverage_id)){

                                    $csv_coverage_where = array(
                                        "clause" => "lead_id=:id",
                                        "params" => array(":id" => $ins_lead_id)
                                    );
                                    $pdo->update('leads_assign_coverage', array('group_coverage_period_id' => $group_coverage_id), $csv_coverage_where);
                                }

                            }else{
                                $ins_lead_id = $pdo->insert("leads", $lead_data);

                                if(!empty($group_coverage_id)){
                                    $pdo->insert("leads_assign_coverage", array('lead_id' => $ins_lead_id,'group_coverage_period_id' => $group_coverage_id));
                                }
                            }

                            //update activity feed
                            $activity_description = [];
                            if(!empty($updatedData)){
                                $activity_description['description_customer'] = 'Lead information updated : <br>';
                                foreach($updatedData as $key => $data){
                                    if(array_key_exists($key,$lead_data)){
                                        $activity_description['key_value']['desc_arr'][$key] =  'updated from '.$data.' to '.$lead_data[$key];
                                        if($agent_row['rep_id'] == 'G56118' && $key=='income'){
                                            $activity_description['key_value']['desc_arr'][$key] = 'Salary updated';
                                        }
                                    }
                                }
                            }
                            //update activity feed

                            $message = $existsGroupEmployee > 0 ? ' updated' : ' added';
                            if(!empty($admin_row)) {
                                $desc = array();
                                $desc['ac_message'] = array(
                                    'ac_red_1' => array(
                                        'href' => 'lead_details.php?id=' . md5($ins_lead_id),
                                        'title' => $lead_disp_id,
                                    ),
                                    'ac_message_1' => $message.' by Admin ',
                                    'ac_red_2' => array(
                                        'href' => 'admin_profile.php?id=' . md5($admin_row['id']),
                                        'title' => $admin_row['display_id'],
                                    ),                                
                                    'ac_message_2' => ' To ',
                                    'ac_red_3' => array(
                                        'href' => 'agent_detail_v1.php?id=' . md5($agent_row['id']),
                                        'title' => $agent_row['rep_id'],
                                    ),                                
                                    'ac_message_3' => ' via upload using '.$file_row['lead_tag'],
                                );
                                $desc = json_encode($desc);

                                //update activity feed
                                if(!empty($updatedData) && $existsGroupEmployee > 0){
                                    activity_feed(3, $ins_lead_id, 'Lead', $ins_lead_id, 'Lead', 'Lead updated by Admin', trim($value[$fname]), trim($value[$lname]), json_encode($activity_description));
                                }else if(empty($updatedData) && $existsGroupEmployee == 0){
                                    activity_feed(3,$ins_lead_id,'Lead',$ins_lead_id,'Lead','Lead added by Admin', trim($value[$fname]), trim($value[$lname]), $desc);
                                }
                            } else if($agent_row['type']=='Group'){
                                $desc = array();
                                $desc['ac_message'] = array(
                                    'ac_red_1' => array(
                                        'href' => 'lead_details.php?id=' . md5($ins_lead_id),
                                        'title' => $lead_disp_id,
                                    ),
                                    'ac_message_1' => $message.' by Group ',
                                    'ac_red_2' => array(
                                        'href' => 'groups_details.php?id=' . md5($agent_row['id']),
                                        'title' => $agent_row['rep_id'],
                                    ),                                
                                    'ac_message_2' => ' via upload using '.$file_row['lead_tag'],
                                );
                                $desc = json_encode($desc);

                                //update activity feed
                                if(!empty($updatedData) && $existsGroupEmployee > 0){
                                    activity_feed(3, $ins_lead_id, 'Lead', $ins_lead_id, 'Lead', 'Group updated Enrollee', trim($value[$fname]), trim($value[$lname]), json_encode($activity_description));
                                }else if(empty($updatedData) && $existsGroupEmployee == 0){
                                    activity_feed(3, $ins_lead_id, 'Lead', $ins_lead_id, 'Lead', 'Group Created Enrollee', trim($value[$fname]), trim($value[$lname]), $desc);  
                                }
                            } else {
                                $desc = array();
                                $desc['ac_message'] = array(
                                    'ac_red_1' => array(
                                        'href' => 'lead_details.php?id=' . md5($ins_lead_id),
                                        'title' => $lead_disp_id,
                                    ),
                                    'ac_message_1' => $message.' by Agent ',
                                    'ac_red_2' => array(
                                        'href' => 'agent_detail_v1.php?id=' . md5($agent_row['id']),
                                        'title' => $agent_row['rep_id'],
                                    ),                                
                                    'ac_message_2' => ' via upload using '.$file_row['lead_tag'],
                                );
                                $desc = json_encode($desc);

                                //update activity feed
                                if(!empty($updatedData) && $existsGroupEmployee > 0){
                                    activity_feed(3, $ins_lead_id, 'Lead', $ins_lead_id, 'Lead', 'Lead updated Agent', trim($value[$fname]), trim($value[$lname]), json_encode($activity_description));
                                }else if(empty($updatedData) && $existsGroupEmployee == 0){
                                    activity_feed(3, $ins_lead_id, 'Lead', $ins_lead_id, 'Lead', 'Lead added by Agent', trim($value[$fname]), trim($value[$lname]), $desc); 
                                }
                            }

                            $new_lead_count++;

                            //if New Tag is inserted then
                            if($file_row['lead_tag'] != $last_inserted_lead_tag) {
                                $last_inserted_lead_tag = $file_row['lead_tag'];

                                $lead_tag_sql = "SELECT * FROM lead_tag_master WHERE lead_tag=:tag AND is_deleted='N'";
                                $lead_tag_row = $pdo->selectOne($lead_tag_sql, array(":tag" => $file_row['lead_tag']));

                                if (empty($lead_tag_row)) {
                                   
                                    $agent_tag_id = 0;

                                    /*$agent_master_sql = "SELECT * FROM agent_tag_master WHERE agent_tag=:tag AND is_deleted='N'";
                                    $agent_master_row = $pdo->selectOne($agent_master_sql, array(":tag" => $agent_row['agent_tag']));
                                    if ($agent_master_row) {
                                        $agent_tag_id = $agent_master_row['id'];
                                    }*/

                                    $tag_data = array(
                                        'lead_tag' => $file_row['lead_tag'],
                                        'agent_tag_id' => $agent_tag_id,
                                        'updated_at' => 'msqlfunc_NOW()',
                                        'created_at' => 'msqlfunc_NOW()'
                                    );
                                    $pdo->insert("lead_tag_master", $tag_data);
                                }
                            }
                        } else {
                            $existing_lead_count++;
                        }
                    }
                }
            }
            
            if ($total_processed_count % 100 == 0) {
                $csv_row_upd_data = array(
                    'existing_leads' => $existing_lead_count,
                    'import_leads' => $new_lead_count,
                    'mobile_leads' => $mobile_leads,
                    'landline_leads' => $landline_leads,
                    'voip_leads' => $voip_leads,
                    'unknown_leads' => $unknown_leads,
                    'total_processed' => $total_processed_count,
                    'updated_at' => 'msqlfunc_NOW()',
                );
                $csv_row_where = array(
                    "clause" => "id=:id",
                    "params" => array(":id" => $file_row['id'])
                );
                $pdo->update('csv_agent_leads', $csv_row_upd_data, $csv_row_where);
            }
        }

        $csv_row_upd_data = array(
            "is_running" => 'N',
            'total_processed' => $total_processed_count,
            'existing_leads' => $existing_lead_count,
            'import_leads' => $new_lead_count,
            'mobile_leads' => $mobile_leads,
            'landline_leads' => $landline_leads,
            'voip_leads' => $voip_leads,
            'unknown_leads' => $unknown_leads,
            'updated_at' => 'msqlfunc_NOW()',
        );

        if ($file_row['total_leads'] <= ($total_processed_count)) {
            $csv_row_upd_data['status'] = 'Processed';

            if(!empty($admin_row)) {
                $desc = array();
                $desc['ac_message'] = array(
                    'ac_red_1' => array(
                        'href' => 'admin_profile.php?id=' . md5($admin_row['id']),
                        'title' => $admin_row['display_id'],
                    ),
                    'ac_message_1' => ' CSV Leads Imported'
                );
                $desc['lead_type'] = "Lead Type : " . $file_row['lead_type'];
                $desc['lead_tag'] = "Lead Tag : " . $file_row['lead_tag'];
                $desc['total_leads'] = "Total Leads : " . $file_row['total_leads'];
                $desc['new_leads'] = "Total Added Leads : " . $new_lead_count;
                $desc['existing_leads'] = "Total Existing Leads : " . $existing_lead_count;
                $desc = json_encode($desc);
                activity_feed(3, $admin_row['id'], 'Admin', $file_row['id'],'csv_agent_leads','CSV Leads Imported', '', '', $desc);

                $desc = array();
                $desc['ac_message'] = array(
                    'ac_red_1' => array(
                        'href' => 'agent_detail_v1.php?id=' . md5($agent_row['id']),
                        'title' => $agent_row['rep_id'],
                    ),
                    'ac_message_1' => 'CSV Leads Imported'
                );
                $desc['lead_type'] = "Lead Type : " . $file_row['lead_type'];
                $desc['lead_tag'] = "Lead Tag : " . $file_row['lead_tag'];
                $desc['total_leads'] = "Total Leads : " . $file_row['total_leads'];
                $desc['new_leads'] = "Total Added Leads : " . $new_lead_count;
                $desc['existing_leads'] = "Total Existing Leads : " . $existing_lead_count;
                $desc = json_encode($desc);
                activity_feed(3, $agent_row['id'], $agent_row['type'], $file_row['id'],'csv_agent_leads','CSV Leads Imported', '', '', $desc);
            } else {
                $desc = array();
                $desc['ac_message'] = array(
                    'ac_red_1' => array(
                        'href' => 'agent_detail_v1.php?id=' . md5($agent_row['id']),
                        'title' => $agent_row['rep_id'],
                    ),
                    'ac_message_1' => 'CSV Leads Imported'
                );
                $desc['lead_type'] = "Lead Type : " . $file_row['lead_type'];
                $desc['lead_tag'] = "Lead Tag : " . $file_row['lead_tag'];
                $desc['total_leads'] = "Total Leads : " . $file_row['total_leads'];
                $desc['new_leads'] = "Total Added Leads : " . $new_lead_count;
                $desc['existing_leads'] = "Total Existing Leads : " . $existing_lead_count;
                $desc = json_encode($desc);
                activity_feed(3, $agent_row['id'], $agent_row['type'], $file_row['id'], 'csv_agent_leads', 'CSV Leads Imported', '', '', $desc);
            }
        }

        $csv_row_where = array(
            "clause" => "id=:id",
            "params" => array(":id" => $file_row['id'])
        );
        $pdo->update('csv_agent_leads', $csv_row_upd_data, $csv_row_where);
    }
    echo "All Processed..";
} else {
    echo "All Processed";
}
dbConnectionClose();
/*
* NOTE :
* WHEN YOU CHANGE ON THIS FILE, PLEASE ALSO DO CHANGE IN BELOW FILE IF REQUIRED
* smarteapp.com\agents\ajax_upload_csv.php
* smarteapp.com\includes\functions.php csv_agent_lead_import method
*    
*/
function csvToArraywithFields($filename)
{   
    if(file_exists($filename)) {
        $csv = array_map('str_getcsv', file($filename));
        $headers = $csv[0];
        unset($csv[0]);
        $rowsWithKeys = [];

        foreach ($csv as $row) {

            $newRow = [];
            foreach ($headers as $k => $key) {
                if (trim($key) != "") {
                    $newRow[$key] = $row[$k];
                }
            }
            $rowsWithKeys[] = $newRow;
        }    
    } else {
        $rowsWithKeys = array();        
    }
    return $rowsWithKeys;
}
?>