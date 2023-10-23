<?php
include_once 'layout/start.inc.php';
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 300000);

$ENROLEE_IMPORT_ARR = array(
    'ENROLLEE_ID' => array(
        'label' => 'Enrollee ID',
        'file_label' => 'ENROLLEE_ID',
        'field_name' => 'enrollee_id_field',
        'is_required' => 'Y'
    ),
    'ANNUAL_INCOME' => array(
        'label' => 'Annual Earnings/Income',
        'file_label' => 'ANNUAL_INCOME',
        'field_name' => 'annual_earnings_field',
        'is_required' => 'Y'
    ),
    'COMPANY_NAME' => array(
        'label' => 'Company Name',
        'file_label' => 'COMPANY_NAME',
        'field_name' => 'company_name_field',
        'is_required' => 'Y'
    ),
    'ENROLLEE_TYPE' => array(
        'label' => 'Enrollee Type',
        'file_label' => 'ENROLLEE_TYPE',
        'field_name' => 'employee_type_field',
        'is_required' => 'Y'
    ),
    'RELATIONSHIP_DATE (HIRE DATE)' => array(
        'label' => 'Relationship Date',
        'file_label' => 'RELATIONSHIP_DATE (HIRE DATE)',
        'field_name' => 'hire_date_field',
        'is_required' => 'Y'
    ),
    'FIRST_NAME' => array(
        'label' => 'Enrollee First Name',
        'file_label' => 'FIRST_NAME',
        'field_name' => 'fname_field',
        'is_required' => 'Y'
    ),
    'LAST_NAME' => array(
        'label' => 'Enrollee Last Name',
        'file_label' => 'LAST_NAME',
        'field_name' => 'lname_field',
        'is_required' => 'Y'
    ),
    'ADDRESS' => array(
        'label' => 'Address',
        'file_label' => 'ADDRESS',
        'field_name' => 'address_field',
        'is_required' => 'Y'
    ),
    'ADDRESS_2' => array(
        'label' => 'Address 2 (suite, apt)',
        'file_label' => 'ADDRESS_2',
        'field_name' => 'address2_field',
        'is_required' => 'N'
    ),
    'CITY' => array(
        'label' => 'City',
        'file_label' => 'CITY',
        'field_name' => 'city_field',
        'is_required' => 'Y'
    ),
    'STATE' => array(
        'label' => 'State',
        'file_label' => 'STATE',
        'field_name' => 'state_field',
        'is_required' => 'Y'
    ),
    'ZIPCODE' => array(
        'label' => 'Zip Code',
        'file_label' => 'ZIPCODE',
        'field_name' => 'zipcode_field',
        'is_required' => 'Y'
    ),
    'GENDER' => array(
        'label' => 'Gender',
        'file_label' => 'GENDER',
        'field_name' => 'gender_field',
        'is_required' => 'Y'
    ),
    'BIRTHDATE' => array(
        'label' => 'DOB',
        'file_label' => 'BIRTHDATE',
        'field_name' => 'dob_field',
        'is_required' => 'Y'
    ),
    'SSN' => array(
        'label' => 'SSN',
        'file_label' => 'SSN',
        'field_name' => 'ssn_field',
        'is_required' => 'N'
    ),
    'EMAIL' => array(
        'label' => 'Email',
        'file_label' => 'EMAIL',
        'field_name' => 'email_field',
        'is_required' => 'Y'
    ),
    'PHONE' => array(
        'label' => 'Phone',
        'file_label' => 'PHONE',
        'field_name' => 'phone_field',
        'is_required' => 'Y'
    ),
    'CLASS_NAME' => array(
        'label' => 'Class',
        'file_label' => 'CLASS_NAME',
        'field_name' => 'class_name_field',
        'is_required' => 'Y'
    ),
    'COVERAGE_PERIOD' => array(
        'label' => 'Plan Period',
        'file_label' => 'COVERAGE_PERIOD',
        'field_name' => 'coverage_period_field',
        'is_required' => 'Y'
    ),
    'PreTax_Deductions' => array(
        'label' => 'PreTax Deductions',
        'file_label' => 'PreTax_Deductions',
        'field_name' => 'pre_tax_deductions_field',
        'is_required' => 'N'
    ),
    'PostTax_Deductions' => array(
        'label' => 'PostTax Deductions',
        'file_label' => 'PostTax_Deductions',
        'field_name' => 'post_tax_deductions_field',
        'is_required' => 'N'
    ),
    'W4_FILING_STATUS' => array(
        'label' => 'W4 Filling Status',
        'file_label' => 'W4_FILING_STATUS',
        'field_name' => 'w4_filing_status_field',
        'is_required' => 'N'
    ),
    'W4_NO_OF_ALLOWANCES' => array(
        'label' => 'W4 No Of Allowances',
        'file_label' => 'W4_NO_OF_ALLOWANCES',
        'field_name' => 'w4_no_of_allowances_field',
        'is_required' => 'N'
    ),
    'W4_TWO_JOBS' => array(
        'label' => 'W4 Two Jobs',
        'file_label' => 'W4_TWO_JOBS',
        'field_name' => 'w4_two_jobs_field',
        'is_required' => 'N'
    ),
    'W4_Dependents_Amount' => array(
        'label' => 'W4 Dependents Amount',
        'file_label' => 'W4_Dependents_Amount',
        'field_name' => 'w4_dependents_amount_field',
        'is_required' => 'N'
    ),
    'W4_4a_Other_Income' => array(
        'label' => 'W4 4a Other Income',
        'file_label' => 'W4_4a_Other_Income',
        'field_name' => 'w4_4a_other_income_field',
        'is_required' => 'N'
    ),
    'W4_4b_Deductions' => array(
        'label' => 'W4 4b Deductions',
        'file_label' => 'W4_4b_Deductions',
        'field_name' => 'w4_4b_deductions_field',
        'is_required' => 'N'
    ),
    'W4_Additional_Withholding' => array(
        'label' => 'W4 Additional Withholding',
        'file_label' => 'W4_Additional_Withholding',
        'field_name' => 'w4_additional_withholding_field',
        'is_required' => 'N'
    ),
    'State_Filing_Status' => array(
        'label' => 'State Filing Status',
        'file_label' => 'State_Filing_Status',
        'field_name' => 'state_filing_status_field',
        'is_required' => 'N'
    ),
    'State_Dependents' => array(
        'label' => 'State Dependents',
        'file_label' => 'State_Dependents',
        'field_name' => 'state_dependents_field',
        'is_required' => 'N'
    ),
    'State_Additional_Withholdings' => array(
        'label' => 'State Additional Withholdings',
        'file_label' => 'State_Additional_Withholdings',
        'field_name' => 'state_additional_withholdings_field',
        'is_required' => 'N'
    )

);

$validate = new Validation();
$res = array();
$group_id = $_SESSION['groups']['id'];

$save_as = $_POST['save_as'];
$csv_file = $_FILES['csv_file'];
$csv_filename = $csv_file['name'];
$csv_tmpname = $csv_file['tmp_name'];

$module_name = "enrollee";
$import_action = "add_enrollee";

$tag_from = isset($_POST['tag_from']) ? $_POST['tag_from'] : '';
$existing_tag = isset($_POST['existing_tag']) ? $_POST['existing_tag'] : '';
$new_tag = isset($_POST['new_tag']) ? $_POST['new_tag'] : '';
$lead_report = "Y";

$enrollee_id = !empty($_POST['enrollee_id_field']) ? $_POST['enrollee_id_field'] : '';
$annual_earnings = !empty($_POST['annual_earnings_field']) ? $_POST['annual_earnings_field'] : '';
$company_name = !empty($_POST['company_name_field']) ? $_POST['company_name_field'] : '';
$employee_type = !empty($_POST['employee_type_field']) ? $_POST['employee_type_field'] : '';
$hire_date = !empty($_POST['hire_date_field']) ? $_POST['hire_date_field'] : '';
$termination_date = !empty($_POST['termination_date_field']) ? $_POST['termination_date_field'] : '';  
$fname = !empty($_POST['fname_field']) ? $_POST['fname_field'] : '';
$lname = !empty($_POST['lname_field']) ? $_POST['lname_field'] : '';
$address = !empty($_POST['address_field']) ? $_POST['address_field'] : '';
$address2 = !empty($_POST['address2_field']) ? $_POST['address2_field'] : '';
$city = !empty($_POST['city_field']) ? $_POST['city_field'] : '';
$state = !empty($_POST['state_field']) ? $_POST['state_field'] : '';

$zipcode = !empty($_POST['zipcode_field']) ? $_POST['zipcode_field'] : '';
$gender = !empty($_POST['gender_field']) ? $_POST['gender_field'] : '';
$dob = !empty($_POST['dob_field']) ? $_POST['dob_field'] : '';
$ssn = !empty($_POST['ssn_field']) ? $_POST['ssn_field'] : '';
$email = !empty($_POST['email_field']) ? $_POST['email_field'] : '';
$phone = !empty($_POST['phone_field']) ? $_POST['phone_field'] : '';

$class_name = !empty($_POST['class_name_field']) ? $_POST['class_name_field'] : '';
$coverage_period = !empty($_POST['coverage_period_field']) ? $_POST['coverage_period_field'] : '';

$pre_tax_deductions = !empty($_POST['pre_tax_deductions_field']) ? $_POST['pre_tax_deductions_field'] : '';
$post_tax_deductions = !empty($_POST['post_tax_deductions_field']) ? $_POST['post_tax_deductions_field'] : '';
$w4_filing_status = !empty($_POST['w4_filing_status_field']) ? $_POST['w4_filing_status_field'] : '';
$w4_no_of_allowances = !empty($_POST['w4_no_of_allowances_field']) ? $_POST['w4_no_of_allowances_field'] : '';
$w4_two_jobs = !empty($_POST['w4_two_jobs_field']) ? $_POST['w4_two_jobs_field'] : '';
$w4_dependents_amount = !empty($_POST['w4_dependents_amount_field']) ? $_POST['w4_dependents_amount_field'] : '';
$w4_4a_other_income = !empty($_POST['w4_4a_other_income_field']) ? $_POST['w4_4a_other_income_field'] : '';
$w4_4b_deductions = !empty($_POST['w4_4b_deductions_field']) ? $_POST['w4_4b_deductions_field'] : '';
$w4_additional_withholding = !empty($_POST['w4_additional_withholding_field']) ? $_POST['w4_additional_withholding_field'] : '';
$state_filing_status = !empty($_POST['state_filing_status_field']) ? $_POST['state_filing_status_field'] : '';
$state_dependents = !empty($_POST['state_dependents_field']) ? $_POST['state_dependents_field'] : '';
$state_additional_withholdings = !empty($_POST['state_additional_withholdings_field']) ? $_POST['state_additional_withholdings_field'] : '';

$lead_tag = ($tag_from == 'existing') ? $existing_tag : $new_tag;

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

        foreach($ENROLEE_IMPORT_ARR as $enrolleeData){
            if($enrolleeData['is_required'] == 'Y'){
                $validate->string(array('required' => true, 'field' => $enrolleeData['field_name'], 'value' => ${str_replace('_field','',$enrolleeData['field_name'])}), array('required' => $enrolleeData['label'].' is required'));
            }
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
        $row = array_filter($data,function($item){
            return htmlspecialchars($item);
        });
        $res['csv_data'] = $row;

        $html = "";

        $fields = array();
        if($module_name == 'enrollee' && $import_action == 'add_enrollee'){
            ob_start();
            include_once 'tmpl/enrollee_import.inc.php';
            $html = ob_get_contents();
            ob_clean();
        }
        $res['html'] = $html;

        $name = basename($csv_filename);
        move_uploaded_file($csv_tmpname, $CSV_DIR . '/' . time() . $name);
        $_SESSION['tmp_file_name'] = $CSV_DIR . time() . $name;
        $_SESSION['stored_file_name'] = time() . $name;
    } else {
        $field_row = csvToArraywithFieldsMain($csv_tmpname);
        $file_data = array(
            'agent_id' => $group_id,
            'lead_tag' => $lead_tag,
            'lead_type' => 'Member' ,
            'status' => (count($field_row) <= 51 ? 'Processed' : 'Pending'),
            'file_name' => trim($_SESSION['stored_file_name']),
            'enrollee_id_field' => !empty($enrollee_id) ? $enrollee_id : '',
            'annual_earnings_field' => !empty($annual_earnings) ? $annual_earnings : '',
            'company_name_field' => !empty($company_name) ? $company_name : '',
            'employee_type_field' => !empty($employee_type) ? $employee_type : '',
            'hire_date_field' => !empty($hire_date) ? $hire_date : '',
            'termination_date_field' => !empty($termination_date) ? $termination_date : '',
            'fname_field' => !empty($fname) ? $fname : '',
            'lname_field' => !empty($lname) ? $lname : '',
            'address_field' => !empty($address) ? $address : '',
            'address2_field' => !empty($address2) ? $address2 : '',
            'city_field' => !empty($city) ? $city : '',
            'state_field' => !empty($state) ? $state : '',
            'zip_field' => !empty($zipcode) ? $zipcode : '',
            'gender_field' => !empty($gender) ? $gender : '',
            'dob_field' => !empty($dob) ? $dob : '',
            'ssn_field' => !empty($ssn) ? $ssn : '',
            'cell_phone_field' => !empty($phone) ? $phone : '',
            'email_field' => !empty($email) ? $email : '',
            'class_name_field' => !empty($class_name) ? $class_name : '',
            'coverage_period_field' => !empty($coverage_period) ? $coverage_period : '',
            'pre_tax_deductions_field' => !empty($pre_tax_deductions) ? $pre_tax_deductions : '',
            'post_tax_deductions_field' => !empty($post_tax_deductions) ? $post_tax_deductions : '',
            'w4_filing_status_field' => !empty($w4_filing_status) ? $w4_filing_status : '',
            'w4_no_of_allowances_field' => !empty($w4_no_of_allowances) ? $w4_no_of_allowances : '',
            'w4_two_jobs_field' => !empty($w4_two_jobs) ? $w4_two_jobs : '',
            'w4_dependents_amount_field' => !empty($w4_dependents_amount) ? $w4_dependents_amount : '',
            'w4_4a_other_income_field' => !empty($w4_4a_other_income) ? $w4_4a_other_income : '',
            'w4_4b_deductions_field' => !empty($w4_4b_deductions) ? $w4_4b_deductions : '',
            'w4_additional_withholding_field' => !empty($w4_additional_withholding) ? $w4_additional_withholding : '',
            'state_filing_status_field' => !empty($state_filing_status) ? $state_filing_status : '',
            'state_dependents_field' => !empty($state_dependents) ? $state_dependents : '',
            'state_additional_withholdings_field' => !empty($state_additional_withholdings) ? $state_additional_withholdings : '',
            'total_leads' => count($field_row),
            'is_report_send' => ($lead_report == 'Y' ? "Y" : "N"),
            'created_at' => 'msqlfunc_NOW()',
            'updated_at' => 'msqlfunc_NOW()'
        );
        $csv_agent_lead_id = $pdo->insert('csv_agent_leads', $file_data);

        move_uploaded_file($csv_tmpname, $CSV_DIR . '/' . $_SESSION['stored_file_name']);

        if (count($field_row) <= 51) {
            csv_agent_lead_import($csv_agent_lead_id);
        }

        $res['msg'] = "Enrollee added Successfully";

        if (isset($_SESSION['tmp_file_name']) && isset($_SESSION['stored_file_name'])) {
            unset($_SESSION['tmp_file_name']);
            unset($_SESSION['stored_file_name']);
        }
        setNotifySuccess('Enrollee added successfully.');
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
