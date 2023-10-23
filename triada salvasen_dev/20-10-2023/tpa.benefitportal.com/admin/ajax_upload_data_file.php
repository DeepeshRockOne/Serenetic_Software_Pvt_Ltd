<?php
include_once 'layout/start.inc.php';
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 300000);

$validate = new Validation();
$res = array();

$save_as = $_POST['save_as'];
$csv_file = isset($_FILES['csv_file']) ? $_FILES['csv_file'] : "";
$csv_filename = isset($csv_file['name']) ? $csv_file['name'] : "";
$csv_tmpname = isset($csv_file['tmp_name']) ? $csv_file['tmp_name'] : "";

$module_name = isset($_POST['module_name']) ? $_POST['module_name'] : "";
$import_action = isset($_POST['import_action']) ? $_POST['import_action'] : "";

$validate->string(array('required' => true, 'field' => 'module_name', 'value' => $module_name), array('required' => 'Select any option'));
$validate->string(array('required' => true, 'field' => 'import_action', 'value' => $import_action), array('required' => 'Select any option'));

if ($save_as == 'upload_csv') {
    if ($csv_file['name'] != '') {
        $allowed_ext = array('csv');
        $Ext = array_reverse(explode(".", $csv_filename));
        $file_ext = strtolower($Ext[0]);
        $allowed_extensions = '*.csv';
        $allowed_file_size = '10485760';
        $size_in_mb = "10";
        $vmFileSize = $csv_file['size'];

        if (!in_array($file_ext, $allowed_ext)) {
            $validate->setError('csv_file', "Incorrect file format. Only .CSV file extensions accepted.");
        } else if ($vmFileSize > $allowed_file_size) {
            $validate->setError('csv_file', "Maximum " . $size_in_mb . " MB file size allowed");
        }
    } else {
        $validate->setError("csv_file", "Upload Data File");
    }
} else {
    if(!$validate->getError('module_name') && !$validate->getError('import_action')){
        $fields = $_POST;

        foreach ($fields as $key => $field) {
            $validate->string(array('required' => true, 'field' => $key, 'value' => $field), array('required' => 'Please select any option'));
        }
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
        array_unshift($row,'');
        $res['csv_data'] = $row;
        $html = "";

        $fields = array();
        if($module_name == 'members' && $import_action == 'add_members'){
            $MEMBER_IMPORT_ARR = array(
                'ENROLLING AGENT/GROUP' => array(
                    array(
                        'label' => 'Agent/Group ID',
                        'file_label' => 'ENROLLING_AGENT_GROUP_ID',
                        'info' => 'Unique identifier of agent/group this member falls underneath',
                        'field_name' => 'enrolling_agent_group_id',
                        'is_required' => 'Y'
                    ),
                ),
                'MEMBER'=> array(
                    array(
                        'label' => 'Relation',
                        'file_label' => 'RELATION',
                        'info' => 'Relationship to primary member (Primary, Spouse, Child)',
                        'field_name' => 'relation',
                        'is_required' => 'Y'
                    ),
                    array(
                        'label' => 'Household ID',
                        'file_label' => 'HOUSEHOLD_ID',
                        'info' => 'Unique identifier connecting dependents to primary',
                        'field_name' => 'account_id',
                        'is_required' => 'Y'
                    ),
                    array(
                        'label' => 'First Name',
                        'file_label' => 'FIRST_NAME',
                        'info' => 'Legal first name of enrollee',
                        'field_name' => 'fname',
                        'is_required' => 'Y'
                    ),
                    array(
                        'label' => 'Last Name',
                        'file_label' => 'LAST_NAME',
                        'info' => 'Legal last name of enrollee',
                        'field_name' => 'lname',
                        'is_required' => 'Y'
                    ),
                    array(
                        'label' => 'Phone Number',
                        'file_label' => 'PHONE_NUMBER',
                        'info' => 'Phone number of primary member',
                        'field_name' => 'phone',
                        'is_required' => 'Y'
                    ),
                    array(
                        'label' => 'Email Address',
                        'file_label' => 'EMAIL_ADDRESS',
                        'info' => 'Email address of primary member (must be unique)',
                        'field_name' => 'email',
                        'is_required' => 'Y'
                    ),
                    array(
                        'label' => 'Address',
                        'file_label' => 'ADDRESS',
                        'info' => 'Legal address of primary member',
                        'field_name' => 'address',
                        'is_required' => 'Y'
                    ),
                    array(
                        'label' => 'Address 2 (suite, apt)',
                        'file_label' => 'ADDRESS_2',
                        'info' => 'Address 2 (suite, apt) of primary member',
                        'field_name' => 'address2',
                        'is_required' => 'Y'
                    ),
                    array(
                        'label' => 'City',
                        'file_label' => 'CITY',
                        'info' => 'Legal city of primary member',
                        'field_name' => 'city',
                        'is_required' => 'Y'
                    ),
                    array(
                        'label' => 'State',
                        'file_label' => 'STATE',
                        'info' => 'Legal state of primary member',
                        'field_name' => 'state',
                        'is_required' => 'Y'
                    ),
                    array(
                        'label' => 'Zip Code',
                        'file_label' => 'ZIP_CODE',
                        'info' => 'Legal zip code of primary member',
                        'field_name' => 'zip',
                        'is_required' => 'Y'
                    ),
                    array(
                        'label' => 'Date of Birth',
                        'file_label' => 'DOB',
                        'info' => 'Legal date of birth of enrollee',
                        'field_name' => 'birth_date',
                        'is_required' => 'Y'
                    ),
                    array(
                        'label' => 'Gender',
                        'file_label' => 'GENDER',
                        'info' => 'Legal gender of enrollee',
                        'field_name' => 'gender',
                        'is_required' => 'Y'
                    ),
                    array(
                        'label' => 'Social Security Number',
                        'file_label' => 'SSN',
                        'info' => 'Legal SSN/ITIN number of enrollee"',
                        'field_name' => 'ssn',
                        'is_required' => 'Y'
                    ),
                    
                    array(
                        'label' => 'Billing Type',
                        'file_label' => 'BILLING_TYPE',
                        'info' => 'Renewal payment method (credit card or ACH)',
                        'field_name' => 'billing_type',
                        'is_required' => 'Y'
                    ),
                    array(
                        'label' => 'ACH Bank Name',
                        'file_label' => 'BANK_NAME',
                        'info' => 'Bank Name',
                        'field_name' => 'bankname',
                        'is_required' => 'Y'
                    ),
                    array(
                        'label' => 'ACH Account Type',
                        'file_label' => 'ACH_ACCOUNT_TYPE',
                        'info' => 'Type of ACH account (checking account or savings account)',
                        'field_name' => 'ach_account_type',
                        'is_required' => 'Y'
                    ),
                    array(
                        'label' => 'ACH Routing #',
                        'file_label' => 'ACH_ROUTING',
                        'info' => 'ABA routing number of ACH account',
                        'field_name' => 'ach_routing_number',
                        'is_required' => 'Y'
                    ),
                    array(
                        'label' => 'ACH Account #',
                        'file_label' => 'ACH_ACCOUNT_NUMBER',
                        'info' => 'Account number of ACH account (5-17 integers)',
                        'field_name' => 'ach_account_number',
                        'is_required' => 'Y'
                    ),
                    array(
                        'label' => 'CC Type',
                        'file_label' => 'CC_TYPE',
                        'info' => 'Type of card (American Express, Discover, MasterCard, VISA)',
                        'field_name' => 'card_type',
                        'is_required' => 'Y'
                    ),
                    array(
                        'label' => 'CC #',
                        'file_label' => 'CC_NUMBER',
                        'info' => '16 digit credit card number',
                        'field_name' => 'cc_number',
                        'is_required' => 'Y'
                    ),
                    array(
                        'label' => 'CC Cvv #',
                        'file_label' => 'CC_CVV',
                        'info' => 'Three digit security code on back of CC (4-digit on front for AmEx)',
                        'field_name' => 'cvv',
                        'is_required' => 'Y'
                    ),
                    array(
                        'label' => 'CC Exp. Date',
                        'file_label' => 'CC_EXPIRATION',
                        'info' => 'Expiration date of card (MM/YY)',
                        'field_name' => 'cc_expiry',
                        'is_required' => 'Y'
                    ),
                    array(
                        'label' => 'Billing Name',
                        'file_label' => 'BILLING_NAME',
                        'info' => 'Name on account at bank or with credit card issuer',
                        'field_name' => 'billing_name',
                        'is_required' => 'Y'
                    ),
                    array(
                        'label' => 'Billing Address',
                        'file_label' => 'BILLING_ADDRESS',
                        'info' => 'Address on account at bank or with credit card issuer',
                        'field_name' => 'billing_address',
                        'is_required' => 'Y'
                    ),
                    array(
                        'label' => 'Billing City',
                        'file_label' => 'BILLING_CITY',
                        'info' => 'City on account at bank or with credit card issuer',
                        'field_name' => 'billing_city',
                        'is_required' => 'Y'
                    ),
                    array(
                        'label' => 'Billing State',
                        'file_label' => 'BILLING_STATE',
                        'info' => 'State on account at bank or with credit card issuer',
                        'field_name' => 'billing_state',
                        'is_required' => 'Y'
                    ),
                    array(
                        'label' => 'Billing Zip Code',
                        'file_label' => 'BILLING_ZIPCODE',
                        'info' => 'Zip Code on account at bank or with credit card issue',
                        'field_name' => 'billing_zip',
                        'is_required' => 'Y'
                    ),
                    /*array(
                        'label' => 'Enrollee Class',
                        'file_label' => 'CLASS',
                        'info' => 'Indicator of class this enrollee is assigned',
                        'field_name' => 'enrollee_class',
                        'is_required' => 'Y'
                    ),
                    array(
                        'label' => 'Enrollee Type',
                        'file_label' => 'ENROLLEE_TYPE',
                        'info' => 'Indicator of agent/group enrollee type (Existing, New, Renew)',
                        'field_name' => 'enrollee',
                        'is_required' => 'Y'
                    ),
                    array(
                        'label' => 'Relationship Date',
                        'file_label' => 'RELATIONSHIP_DATE',
                        'info' => 'Date enrollee type relationship began with group (MM/DD/YYYY)',
                        'field_name' => 'relationship_date',
                        'is_required' => 'Y'
                    ),
                    array(
                        'label' => 'Location ID',
                        'file_label' => 'LOCATION_ID',
                        'info' => 'Indicator of what company/location this enrollee is part of (if applicable)',
                        'field_name' => 'group_company',
                        'is_required' => 'Y'
                    ),*/
                ),
                'PRODUCT' => array(
                    array(
                        'label' => 'Product Category',
                        'file_label' => 'PRODUCT_CATEGORY',
                        'info' => 'Product Category',
                        'field_name' => 'product_category',
                        'is_required' => 'Y'
                    ),
                    array(
                        'label' => 'Product ID',
                        'file_label' => 'PRODUCT_ID',
                        'info' => 'Unique identifier of product in this system',
                        'field_name' => 'product_id',
                        'is_required' => 'Y'
                    ),
                    array(
                        'label' => 'Benefit Tier',
                        'file_label' => 'BENEFIT_TIER',
                        'info' => 'Benefit tier election for product ID',
                        'field_name' => 'benefit_tier',
                        'is_required' => 'Y'
                    ),
                    // array(
                    //     'label' => 'Policy ID',
                    //     'file_label' => 'POLICY_ID',
                    //     'info' => 'Unique identifier of this policy to member',
                    //     'field_name' => 'policy_id',
                    //     'is_required' => 'Y'
                    // ),
                    // array(
                    //     'label' => 'Product Added Date',
                    //     'file_label' => 'PRODUCT_ADDED_DATE',
                    //     'info' => 'Date product was added to enrollee',
                    //     'field_name' => 'product_added_date',
                    //     'is_required' => 'Y'
                    // ),
                    array(
                        'label' => 'Effective Date',
                        'file_label' => 'EFFECTIVE_DATE',
                        'info' => 'Date product is active for enrollee (MM/DD/YYYY)',
                        'field_name' => 'effective_date',
                        'is_required' => 'Y'
                    ),
                    // array(
                    //     'label' => 'Termination Date',
                    //     'file_label' => 'TERMINATION_DATE',
                    //     'info' => 'Date product is inactive for enrollee (MM/DD/YYYY)',
                    //     'field_name' => 'termination_date',
                    //     'is_required' => 'Y'
                    // ),
                    // array(
                    //     'label' => 'Next Billing Date',
                    //     'file_label' => 'NEXT_BILLING_DATE',
                    //     'info' => 'Date product is set to bill next (MM/DD/YYYY)',
                    //     'field_name' => 'next_billing_date',
                    //     'is_required' => 'Y'
                    // ),
                    array(
                        'label' => 'Active Member Since Date',
                        'file_label' => 'ACTIVE_MEMBER_SINCE_DATE',
                        'info' => 'Date identifier if product has benefit restrictions by date (MM/DD/YYYY)',
                        'field_name' => 'active_member_since',
                        'is_required' => 'Y'
                    ),
                    array(
                        'label' => 'Height',
                        'file_label' => 'HEIGHT',
                        'info' => 'Height of enrollee in feet and inches (5’11”)',
                        'field_name' => 'height',
                        'is_required' => 'Y'
                    ),
                    array(
                        'label' => 'Weight',
                        'file_label' => 'WEIGHT',
                        'info' => 'Weight of enrollee in pounds (125)',
                        'field_name' => 'weight',
                        'is_required' => 'Y'
                    ),
                    array(
                        'label' => 'Smoke',
                        'file_label' => 'SMOKE',
                        'info' => 'Indicator if enrollee smokes (Yes or No)',
                        'field_name' => 'smoke',
                        'is_required' => 'Y'
                    ),
                    array(
                        'label' => 'Tobacco',
                        'file_label' => 'TOBACCO',
                        'info' => 'Indicator if enrollee uses tobacco (Yes or No)',
                        'field_name' => 'tobacco',
                        'is_required' => 'Y'
                    ),
                    array(
                        'label' => 'Employed',
                        'file_label' => 'EMPLOYMENT_STATUS',
                        'info' => 'Indicator if enrollee is employed (Yes or No)',
                        'field_name' => 'employed',
                        'is_required' => 'Y'
                    ),
                    array(
                        'label' => 'Annual Salary',
                        'file_label' => 'ANNUAL_SALARY',
                        'info' => 'Whole number of enrollee annual salary ($45,000)',
                        'field_name' => 'annual_salary',
                        'is_required' => 'Y'
                    ),
                    array(
                        'label' => 'Weekly Hours/Week',
                        'file_label' => 'HOURS_WORKED_PER_WK',
                        'info' => 'Number of hours enrollee works per week',
                        'field_name' => 'weekly_hours',
                        'is_required' => 'Y'
                    ),
                    array(
                        'label' => 'Pay Frequency',
                        'file_label' => 'PAY_FREQUENCY',
                        'info' => 'Indicator of enrollee frequency of pay (Annual, Monthly, Semi-Annual, Bi-Weekly, Hourly)',
                        'field_name' => 'pay_frequency',
                        'is_required' => 'Y'
                    ),
                    array(
                        'label' => 'US Citizen',
                        'file_label' => 'US_CITIZEN',
                        'info' => 'Indicator if enrollee is a United States Citizen (Yes or No)',
                        'field_name' => 'us_citizen',
                        'is_required' => 'Y'
                    ),
                    array(
                        'label' => 'Has Spouse',
                        'file_label' => 'HAS_SPOUSE',
                        'info' => 'Indicator if enrollee has spouse',
                        'field_name' => 'has_spouse',
                        'is_required' => 'Y'
                    ),
                    array(
                        'label' => 'No. of Children',
                        'file_label' => 'NUMBER_OF_CHILDREN',
                        'info' => 'Indicator of how many children enrollee has',
                        'field_name' => 'no_of_children',
                        'is_required' => 'Y'
                    ),
                    // array(
                    //     'label' => 'Monthly Benefit Amount',
                    //     'file_label' => 'MONTHLY_BENEFIT_AMOUNT',
                    //     'info' => 'Whole number of enrollee monthly benefit amount (100)',
                    //     'field_name' => 'monthly_benefit_amount',
                    //     'is_required' => 'N'
                    // ),
                    array(
                        'label' => 'Benefit Amount',
                        'file_label' => 'BENEFIT_AMOUNT',
                        'info' => 'Whole number of enrollee benefit amount ($45,000)',
                        'field_name' => 'benefit_amount',
                        'is_required' => 'N'
                    ),
                    array(
                        'label' => 'In Patient Benefit',
                        'file_label' => 'IN_PATIENT_BENEFIT',
                        'info' => 'Whole number of enrollee in patient benefit amount ($45,000)',
                        'field_name' => 'in_patient_benefit',
                        'is_required' => 'N'
                    ),
                    array(
                        'label' => 'Out Patient Benefit',
                        'file_label' => 'OUT_PATIENT_BENEFIT',
                        'info' => 'Whole number of enrollee out patient benefit amount ($45,000)',
                        'field_name' => 'out_patient_benefit',
                        'is_required' => 'N'
                    ),
                    array(
                        'label' => 'Monthly Income',
                        'file_label' => 'MONTHLY_INCOME',
                        'info' => 'Whole number of enrollee Monthly Income ($45,000)',
                        'field_name' => 'monthly_income',
                        'is_required' => 'N'
                    ),
                    array(
                        'label' => 'Beneficiary Type',
                        'file_label' => 'BENEFICIARY_TYPE',
                        'info' => 'Indicator of beneficiary type (Principal or Contingent)',
                        'field_name' => 'beneficiary_type',
                        'is_required' => 'Y'
                    ),
                    array(
                        'label' => 'Beneficiary Name',
                        'file_label' => 'BENEFICIARY_NAME',
                        'info' => 'Legal full name of beneficiary',
                        'field_name' => 'beneficiary_name',
                        'is_required' => 'Y'
                    ),
                    array(
                        'label' => 'Beneficiary Address',
                        'file_label' => 'BENEFICIARY_ADDRESS',
                        'info' => 'Legal full address of beneficiary',
                        'field_name' => 'beneficiary_address',
                        'is_required' => 'Y'
                    ),
                    array(
                        'label' => 'Beneficiary Email',
                        'file_label' => 'BENEFICIARY_EMAIL',
                        'info' => 'Email address of beneficiary',
                        'field_name' => 'beneficiary_email',
                        'is_required' => 'Y'
                    ),
                    array(
                        'label' => 'Beneficiary Phone',
                        'file_label' => 'BENEFICIARY_PHONE',
                        'info' => 'Phone number of beneficiary',
                        'field_name' => 'beneficiary_phone',
                        'is_required' => 'Y'
                    ),
                    array(
                        'label' => 'Beneficiary SSN',
                        'file_label' => 'BENEFICIARY_SSN',
                        'info' => 'Social Security Number of beneficiary',
                        'field_name' => 'beneficiary_ssn',
                        'is_required' => 'Y'
                    ),
                    array(
                        'label' => 'Beneficiary Relation',
                        'file_label' => 'BENEFICIARY_RELATION',
                        'info' => 'Identifier of relationship to enrollee (Child, Spouse, Parent, Grandparent, Friend, Other)',
                        'field_name' => 'beneficiary_relation',
                        'is_required' => 'Y'
                    ),
                    array(
                        'label' => 'Beneficiary Percentage',
                        'file_label' => 'BENEFICIARY_PERCENTAGE',
                        'info' => 'Percentage of benefit amount to beneficiary',
                        'field_name' => 'beneficiary_percentage',
                        'is_required' => 'Y'
                    ),
                    array(
                        'label' => 'Waive Plan',
                        'file_label' => 'WAIVE_COVERAGE',
                        'info' => 'Indicator if user waived Plan for this product category (Yes/No)',
                        'field_name' => 'waive_coverage',
                        'is_required' => 'Y'
                    ),
                    array(
                        'label' => 'Waive Plan Reason',
                        'file_label' => 'WAIVE_COVERAGE_REASON',
                        'info' => 'Indicator for reason user waived Plan (Group Plan, Individual Plan, Medicare, Do not want, Other)',
                        'field_name' => 'waive_coverage_reason',
                        'is_required' => 'Y'
                    ),
                    array(
                        'label' => 'Plan Period ID',
                        'file_label' => 'PLAN_PERIOD_ID',
                        'info' => 'Must match Plan Period ID in the system',
                        'field_name' => 'plan_period_id',
                        'is_required' => 'Y'
                    ),
                )
            );

            $customUestionArr = array(
                    'label' => array('Custom Question 1','Custom Question 2','Custom Question 2','Custom Question 4','Custom Question 5','Custom Question 6','Custom Question 7','Custom Question 8','Custom Question 8','Custom Question 9','Custom Question 10'),
                    'file_label' => array('CUSTOM_QUESTION_1','CUSTOM_QUESTION_2','CUSTOM_QUESTION_3','CUSTOM_QUESTION_4','CUSTOM_QUESTION_5','CUSTOM_QUESTION_6','CUSTOM_QUESTION_7','CUSTOM_QUESTION_8','CUSTOM_QUESTION_9','CUSTOM_QUESTION_10'),
            );
            ob_start();
            include_once 'tmpl/member_import.inc.php';
            $html = ob_get_contents();
            ob_clean();
            // $fields = $MEMBER_IMPORT_ARR;
        }else if($module_name == 'members' && $import_action == 'member_add_products'){

        }else if($module_name == 'members' && $import_action == 'term_products'){
            
        }else if($module_name == 'agents' && $import_action == 'add_agents'){
            ob_start();
            include_once 'tmpl/agent_import.inc.php';
            $html = ob_get_contents();
            ob_clean();
        }else if($module_name == 'agents' && $import_action == 'add_license'){
            ob_start();
            include_once 'tmpl/agent_license_import.inc.php';
            $html = ob_get_contents();
            ob_clean();            
        }else if($module_name == 'agents' && $import_action == 'add_appointment'){
            ob_start();
            include_once 'tmpl/agent_appointment_import.inc.php';
            $html = ob_get_contents();
            ob_clean();        
        }else if($module_name == 'agents' && $import_action == 'add_direct_deposit'){
            ob_start();
            include_once 'tmpl/agent_direct_deposit_account_import.inc.php';
            $html = ob_get_contents();
            ob_clean();
        }else if($module_name == 'agents' && $import_action == 'add_e_o_coverage'){
            ob_start();
            include_once 'tmpl/agent_e_o_coverage_import.inc.php';
            $html = ob_get_contents();
            ob_clean();
        }else if($module_name == 'products' && $import_action == 'add_products'){
            ob_start();
            include_once 'tmpl/product_import.inc.php';
            $html = ob_get_contents();
            ob_clean();
        }
        $res['html'] = $html;
        $name = basename($csv_filename);
        // move_uploaded_file($csv_tmpname, $CSV_DIR . '/' . time() . $name);
        $_SESSION['tmp_file_name'] = $CSV_DIR . time() . $name;
        $_SESSION['stored_file_name'] = time() . $name;
    } else {
        $displayTitleArr=array(
            'add_members'=>'Member Import',
            'member_add_products'=>'Member Add Product Import',
            'term_products'=>'Member Term Product Import',
            'add_agents'=>'Agent Import',
            'add_license'=>'Agent License Import',
            'add_appointment'=>'Agent Appointment Import',
            'add_direct_deposit'=>'Agent Direct Deposit Import',
            'add_e_o_coverage'=>'Agent E&O Coverage Import',
            'add_products'=>'Product Import',
        );

        $field_row = csvToArraywithFieldsMain($csv_tmpname);
        $fields = $_POST;
        $file_data = array(
            'admin_id' => $_SESSION['admin']['id'],
            'module_type' =>$module_name,
            'import_type' =>$import_action,
            'display_title' =>isset($displayTitleArr[$import_action])?$displayTitleArr[$import_action]:'',
            'status' => 'Pending',
            'csv_columns' => json_encode($fields),
            'file_name' => trim($_SESSION['stored_file_name']),
            'total_records' => count($field_row),
            'created_at' => 'msqlfunc_NOW()',
            'updated_at' => 'msqlfunc_NOW()'
        );
        $csv_agent_lead_id = $pdo->insert('import_requests', $file_data);

        move_uploaded_file($csv_tmpname, $CSV_DIR . '/' . $_SESSION['stored_file_name']);


        $res['msg'] = "Request added Successfully";

        if (isset($_SESSION['tmp_file_name']) && isset($_SESSION['stored_file_name'])) {
            unset($_SESSION['tmp_file_name']);
            unset($_SESSION['stored_file_name']);
        }
        setNotifySuccess('Request added successfully.');
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
