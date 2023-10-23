<?php
include_once 'layout/start.inc.php';
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 300000);

$validate = new Validation();
$res = array();

$save_as = $_POST['save_as'];
$csv_file = $_FILES['csv_file'];
$csv_filename = $csv_file['name'];
$csv_tmpname = $csv_file['tmp_name'];

$lead_type = checkIsset($_POST['lead_type']);
$tag_from = checkIsset($_POST['tag_from']);
$existing_tag = checkIsset($_POST['existing_tag']);
$new_tag = checkIsset($_POST['new_tag']);
$lead_report = "Y";
$agent_id = checkIsset($_POST['agent_id']);
$group_id = checkIsset($_POST['group_id']);

$company_name = checkIsset($_POST['company_name_field']);
$fname = checkIsset($_POST['fname_field']);
$lname = checkIsset($_POST['lname_field']);
$email = checkIsset($_POST['email_field']);
$cell_phone = checkIsset($_POST['cell_phone_field']);
$address = checkIsset($_POST['address_field']);
$address2 = checkIsset($_POST['address2_field']);
$city = checkIsset($_POST['city_field']);
$state = checkIsset($_POST['state_field']);
$zipcode = checkIsset($_POST['zipcode_field']);

$gender = checkIsset($_POST['gender_field']);
$dob = checkIsset($_POST['dob_field']);
$ssn = checkIsset($_POST['ssn_field']);

$enrollee_id = checkIsset($_POST['enrollee_id_field']);
$annual_earnings = checkIsset($_POST['annual_earnings_field']);
$employee_type = checkIsset($_POST['employee_type_field']);
$hire_date = checkIsset($_POST['hire_date_field']);
$termination_date = checkIsset($_POST['termination_date_field']); 

$class_name = checkIsset($_POST['class_name_field']);
$coverage_period = checkIsset($_POST['coverage_period_field']);


if ($save_as == 'upload_csv') {
    if ($csv_file['name'] != '') {
        $allowed_ext = array('csv');
        $Ext = array_reverse(explode(".", $csv_filename));
        $file_ext = strtolower($Ext[0]);
        $allowed_extensions = '*.csv';
        $allowed_file_size = '26214400';
        $size_in_mb = "25";
        $vmFileSize = $csv_file['size'];

        if (!in_array($file_ext, $allowed_ext)) {
            $validate->setError('csv_file', "Only " . $allowed_extensions . " file format allowed");
        } else if ($vmFileSize > $allowed_file_size) {
            $validate->setError('csv_file', "Maximum " . $size_in_mb . " MB file size allowed");
        }
    } else {
        $validate->setError("csv_file", "Upload Lead CSV File");
    }
} else {
    $validate->string(array('required' => true, 'field' => 'lead_type', 'value' => $lead_type), array('required' => 'Select any option'));

    if(checkIsset($lead_type) == "Group Enrollee"){
        $validate->string(array('required' => true, 'field' => 'group_id', 'value' => $group_id), array('required' => 'Select Group'));
    }else{
        $validate->string(array('required' => true, 'field' => 'agent_id', 'value' => $agent_id), array('required' => 'Select Agent'));
    }

    $validate->string(array('required' => true, 'field' => 'tag_from', 'value' => $tag_from), array('required' => 'Select any option'));

    if (!$validate->getError('tag_from')) {
        if ($tag_from == "existing") {
            $validate->string(array('required' => true, 'field' => 'existing_tag', 'value' => $existing_tag), array('required' => 'Select any tag'));
        } else {
            $validate->string(array('required' => true, 'field' => 'new_tag', 'value' => $new_tag), array('required' => 'Please enter tag'));

            if (!$validate->getError('new_tag')) {
                if ($new_tag == 'Converted') {
                    $validate->setError("new_tag", "This Tag is Invalid");
                } else {
                    $check_exist_tag_sql = "SELECT * FROM lead_tag_master where lead_tag=:tag and is_deleted='N'";
                    $check_exist_tag_res = $pdo->selectOne($check_exist_tag_sql, array(":tag" => $new_tag));
                    if ($check_exist_tag_res) {
                        $validate->setError("new_tag", "This Tag is already exists");
                    }
                }

            }
        }
    }

    if ($csv_file['name'] != '') {
        $allowed_ext = array('csv');
        $Ext = array_reverse(explode(".", $csv_filename));
        $file_ext = strtolower($Ext[0]);
        $allowed_extensions = '*.csv';
        $allowed_file_size = '10485760';
        $size_in_mb = "10";
        $vmFileSize = $csv_file['size'];

        if (!in_array($file_ext, $allowed_ext)) {
            $validate->setError('csv_file', "Only " . $allowed_extensions . " file format allowed");
        } else if ($vmFileSize > $allowed_file_size) {
            $validate->setError('csv_file', "Maximum " . $size_in_mb . " MB file size allowed");
        }

        $validate->string(array('required' => true, 'field' => 'fname_field', 'value' => $fname), array('required' => 'First Name is required'));
        $validate->string(array('required' => true, 'field' => 'lname_field', 'value' => $lname), array('required' => 'Last Name is required'));
        $validate->string(array('required' => true, 'field' => 'email_field', 'value' => $email), array('required' => 'Email is required'));
        $validate->string(array('required' => true, 'field' => 'cell_phone_field', 'value' => $cell_phone), array('required' => 'Phone is required'));
        $validate->string(array('required' => true, 'field' => 'state_field', 'value' => $state), array('required' => 'State is required'));

        if($lead_type == "Group Enrollee"){
            $validate->string(array('required' => true, 'field' => 'enrollee_id_field', 'value' => $enrollee_id), array('required' => 'Enrollee ID is required'));
            $validate->string(array('required' => true, 'field' => 'annual_earnings_field', 'value' => $annual_earnings), array('required' => 'Annual Earning is required'));
            $validate->string(array('required' => true, 'field' => 'company_name_field', 'value' => $company_name), array('required' => 'Company Name is required'));
            $validate->string(array('required' => true, 'field' => 'employee_type_field', 'value' => $employee_type), array('required' => 'Employee Type is required'));
            $validate->string(array('required' => true, 'field' => 'hire_date_field', 'value' => $hire_date), array('required' => 'Relationship Date is required'));
            $validate->string(array('required' => true, 'field' => 'termination_date_field', 'value' => $termination_date), array('required' => 'Termination Date is required'));

            $validate->string(array('required' => true, 'field' => 'address_field', 'value' => $address), array('required' => 'Address is required'));
       
            $validate->string(array('required' => true, 'field' => 'city_field', 'value' => $city), array('required' => 'City is required'));
            $validate->string(array('required' => true, 'field' => 'zipcode_field', 'value' => $zipcode), array('required' => 'Zipcode is required'));
            $validate->string(array('required' => true, 'field' => 'gender_field', 'value' => $gender), array('required' => 'Gender is required'));
            $validate->string(array('required' => true, 'field' => 'dob_field', 'value' => $dob), array('required' => 'Birthdate is required'));
            $validate->string(array('required' => true, 'field' => 'ssn_field', 'value' => $ssn), array('required' => 'SSN is required'));
            $validate->string(array('required' => true, 'field' => 'class_name_field', 'value' => $class_name), array('required' => 'Class name is required'));
            $validate->string(array('required' => true, 'field' => 'coverage_period_field', 'value' => $coverage_period), array('required' => 'Plan Period is required'));
        }else{

        }
        
    } else {
        $validate->setError("csv_file", "Upload Lead CSV File");
    }
}


if ($validate->isValid()) {

    if ($save_as == 'upload_csv') {
        if (($handle = fopen($csv_tmpname, "r")) !== FALSE) {
            if (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $num = count($data);
                for ($c = 0; $c < $num; $c++) {
                    $data[$c] . "<br />\n";
                }
            }
            fclose($handle);
        }
        $row = $data;
        $res['csv_data'] = $row;
        $name = basename($csv_filename);
        move_uploaded_file($csv_tmpname, $CSV_DIR . '/' . time() . $name);
        $_SESSION['tmp_file_name'] = $CSV_DIR . time() . $name;
        $_SESSION['stored_file_name'] = time() . $name;
    } else {
        $field_row = csvToArraywithFieldsMain($csv_tmpname);
        $file_data = array(
            'lead_tag' => ($tag_from == "new" ? $new_tag : $existing_tag),
            'lead_type' => $lead_type,
            'status' => (count($field_row) <= 51 ? 'Processed' : 'Pending'),
            'file_name' => trim($_SESSION['stored_file_name']),
            'company_name_field' => !empty($company_name) ? $company_name : '',
            'fname_field' => !empty($fname) ? $fname : '',
            'lname_field' => !empty($lname) ? $lname : '',
            'cell_phone_field' => !empty($cell_phone) ? $cell_phone : '',
            'email_field' => !empty($email) ? $email : '',
            'state_field' => !empty($state) ? $state : '',
            'total_leads' => count($field_row),
            'import_lead_admin_id' => $_SESSION['admin']['id'],
            'is_report_send' => ($lead_report == 'Y' ? "Y" : "N"),
            'created_at' => 'msqlfunc_NOW()',
            'updated_at' => 'msqlfunc_NOW()'
        );

        if($lead_type == "Group Enrollee"){
            $file_data["agent_id"] = $group_id;

            $file_data["address_field"] = !empty($address) ? $address : '';
            $file_data["address2_field"] = !empty($address2) ? $address2 : '';
            $file_data["city_field"] = !empty($city) ? $city : '';
            $file_data["zip_field"] = !empty($zipcode) ? $zipcode : '';
            $file_data["gender_field"] = !empty($gender) ? $gender : '';
            $file_data["dob_field"] = !empty($dob) ? $dob : '';
            $file_data["ssn_field"] = !empty($ssn) ? $ssn : '';
            
            $file_data["enrollee_id_field"] = !empty($enrollee_id) ? $enrollee_id : '';
            $file_data["annual_earnings_field"] = !empty($annual_earnings) ? $annual_earnings : '';
            $file_data["employee_type_field"] = !empty($employee_type) ? $employee_type : '';
            $file_data["hire_date_field"] = !empty($hire_date) ? $hire_date : '';
            $file_data["termination_date_field"] = !empty($termination_date) ? $termination_date : '';
            
            $file_data["class_name_field"] = !empty($class_name) ? $class_name : '';
            $file_data["coverage_period_field"] = !empty($coverage_period) ? $coverage_period : '';
        }else{
            $file_data["agent_id"] = $agent_id;
        }

        $csv_agent_lead_id = $pdo->insert('csv_agent_leads', $file_data);

        move_uploaded_file($csv_tmpname, $CSV_DIR . '/' . $_SESSION['stored_file_name']);

        if (count($field_row) <= 51) {
            csv_agent_lead_import($csv_agent_lead_id);
        }

        $description['ac_message'] =array(
          'ac_red_1'=>array(
            'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
            'title'=>$_SESSION['admin']['display_id'],
          ),
          'ac_message_1' =>' acknowledged they have read and agreed to the terms and conditions in loading leads into the system.',
        );
        activity_feed(3, $_SESSION['admin']['id'], 'Admin',$csv_agent_lead_id, 'csv_agent_leads',"Lead Import", $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));

        $res['msg'] = "Leads added Successfully";

        if (isset($_SESSION['tmp_file_name']) && isset($_SESSION['stored_file_name'])) {
            unset($_SESSION['tmp_file_name']);
            unset($_SESSION['stored_file_name']);
        }
        setNotifySuccess('Leads added successfully.');
        $res['status'] = "success";
    }
} else {
    $res['status'] = "fail";
    $res['errors'] = $validate->getErrors();
}
echo json_encode($res);
dbConnectionClose();
exit;
?>
