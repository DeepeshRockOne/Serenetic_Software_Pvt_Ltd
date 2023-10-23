<?php
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 300000);
include_once 'layout/start.inc.php';
include_once __DIR__ . '/../includes/participants.class.php';
$participantsObj = new Participants();

$validate = new Validation();
$res = array();

$save_as = $_POST['save_as'];
$csv_file = isset($_FILES['csv_file']) ? $_FILES['csv_file'] : array();
$csv_filename = isset($csv_file['name']) ? $csv_file['name'] : "";
$csv_tmpname = isset($csv_file['tmp_name']) ? $csv_file['tmp_name'] : "";

$participants_type = checkIsset($_POST['participants_type']);
$agent_id = checkIsset($_POST['agent_id']);
$tag_from = checkIsset($_POST['tag_from']);
$existing_tag = checkIsset($_POST['existing_tag']);
$new_tag = checkIsset($_POST['new_tag']);
$fields = checkIsset($_POST["fields"],'arr');

$PARTICIPANTS_IMPORT_ARR = array(
    'Information' => array(
        array(
            'label' => 'Reseller',
            'info' => 'Reseller Number',
            'field_name' => 'reseller_number',
            'is_required' => 'Y'
        ),
        array(
            'label' => 'GroupNumber',
            'info' => 'GroupNumber',
            'field_name' => 'client_code',
            'is_required' => 'N'
        ),
        array(
            'label' => 'SSN',
            'info' => 'Legal SSN Number',
            'field_name' => 'ssn',
            'is_required' => 'N'
        ),
        array(
            'label' => 'FirstName',
            'info' => 'Firstname of Participants',
            'field_name' => 'fname',
            'is_required' => 'Y'
        ),
        array(
            'label' => 'LastName',
            'info' => 'Lastname of Participants',
            'field_name' => 'lname',
            'is_required' => 'Y'
        ),
        array(
            'label' => 'MI',
            'info' => 'MI (One Letter)',
            'field_name' => 'mname',
            'is_required' => 'N'
        ),
        array(
            'label' => 'DOB',
            'info' => 'Birth Date of Participants (MM/DD/YYYY)',
            'field_name' => 'birth_date',
            'is_required' => 'Y'
        ),
        array(
            'label' => 'Gender',
            'info' => 'M or F (M = Male and F = Female)',
            'field_name' => 'gender',
            'is_required' => 'Y'
        ),
        array(
            'label' => 'PrimaryID',
            'info' => 'PrimaryID',
            'field_name' => 'employee_id',
            'is_required' => 'Y'
        ),
        array(
            'label' => 'PersonCode',
            'info' => 'Person Code',
            'field_name' => 'person_code',
            'is_required' => 'Y'
        ),
        array(
            'label' => 'Address',
            'info' => 'Legal Address of Participants',
            'field_name' => 'address',
            'is_required' => 'Y'
        ),
        array(
            'label' => 'Address2',
            'info' => 'Legal Address2 of Participants',
            'field_name' => 'address2',
            'is_required' => 'N'
        ),
        array(
            'label' => 'City',
            'info' => 'Legal City of Participants',
            'field_name' => 'city',
            'is_required' => 'Y'
        ),
        array(
            'label' => 'State',
            'info' => 'Legal State of Participants',
            'field_name' => 'state',
            'is_required' => 'Y'
        ),
        array(
            'label' => 'Zip',
            'info' => 'Legal Zipcode of Participants',
            'field_name' => 'zip',
            'is_required' => 'Y'
        ),
        array(
            'label' => 'Phone',
            'info' => 'Phone',
            'field_name' => 'cell_phone',
            'is_required' => 'N'
        ),
        array(
            'label' => 'Email',
            'info' => 'Email',
            'field_name' => 'email',
            'is_required' => 'N'
        ),
        array(
            'label' => 'IsDisabled',
            'info' => 'IsDisabled (Yes/No)',
            'field_name' => 'is_disabled',
            'is_required' => 'N'
        ),
        array(
            'label' => 'DisabilityStatusEffectiveDate',
            'info' => 'Disability Status Effective Date (MM/DD/YYYY)',
            'field_name' => 'disability_effective_date',
            'is_required' => 'N'
        ),
        array(
            'label' => 'Tobacco User',
            'info' => 'Tobacco User (Yes/No)',
            'field_name' => 'tobacco_user',
            'is_required' => 'N'
        ),
        array(
            'label' => 'ProductCode',
            'info' => 'Product Code',
            'field_name' => 'product_code',
            'is_required' => 'Y'
        ),
        array(
            'label' => 'ProductName',
            'info' => 'Product Name',
            'field_name' => 'plan_identifier',
            'is_required' => 'Y'
        ),
        array(
            'label' => 'Coverage Tier',
            'info' => 'Coverage Tier (EE,ES,EC,EF)',
            'field_name' => 'plan_coverage_tier',
            'is_required' => 'Y'
        ),
        array(
            'label' => 'EffectiveDate',
            'info' => 'Effective Date (MM/DD/YYYY)',
            'field_name' => 'effective_date',
            'is_required' => 'Y'
        ),
        array(
            'label' => 'Termination Date',
            'info' => 'Termination Date (MM/DD/YYYY)',
            'field_name' => 'termination_date',
            'is_required' => 'N'
        ),
        array(
            'label' => 'Relationship',
            'info' => 'Relationship',
            'field_name' => 'relationship',
            'is_required' => 'Y'
        ),
    )
);

    if (!empty($csv_file['name'])) {
        $allowed_ext = array('csv');
        $Ext = array_reverse(explode(".", $csv_filename));
        $file_ext = strtolower($Ext[0]);
        $allowed_extensions = '*.csv';
        $allowed_file_size = '78643200';
        $size_in_mb = "75";
        $vmFileSize = $csv_file['size'];

        if (!in_array($file_ext, $allowed_ext)) {
            $validate->setError('csv_file', "Incorrect file format. Only .CSV file extensions accepted.");
        } else if ($vmFileSize > $allowed_file_size) {
            $validate->setError('csv_file', "Maximum " . $size_in_mb . " MB file size allowed");
        }
    } else {
        $validate->setError("csv_file", "Upload CSV Data File");
    }

    if ($save_as != 'upload_csv') {
        $validate->string(array('required' => true, 'field' => 'participants_type', 'value' => $participants_type), array('required' => 'Select any option'));
     $validate->string(array('required' => true, 'field' => 'agent_id', 'value' => $agent_id), array('required' => 'Select Agent'));
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
                    $check_exist_tag_sql = "SELECT id FROM participants WHERE participants_tag=:tag AND is_deleted='N'";
                    $check_exist_tag_res = $pdo->selectOne($check_exist_tag_sql, array(":tag" => $new_tag));
                    if (!empty($check_exist_tag_res["id"])) {
                        $validate->setError("new_tag", "This Tag is already exists");
                    }
                }

            }
        }
    }

        if(!empty($fields)){
            foreach ($fields as $key => $field) {
                $validate->string(array('required' => true, 'field' => $key, 'value' => $field), array('required' => 'Please select any option'));
            }
        }else{
            $validate->setError("fields", "Fields are required");
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
        $html = "";
        ob_start();
        include_once 'tmpl/participants_import.inc.php';
        $html = ob_get_contents();
        ob_clean();
        $res['html'] = $html;
        $name = basename($csv_filename);
        $_SESSION['tmp_file_name'] = $PARTICIPANTS_CSV_DIR . time() . $name;
        $_SESSION['stored_file_name'] = time() . $name;
    } else {
        $field_row = csvToArraywithFieldsMain($csv_tmpname);
        $file_data = array(
            'agent_id' => $agent_id,
            'participants_tag' => ($tag_from == "new" ? $new_tag : $existing_tag),
            'participants_type' => $participants_type,
            'status' => 'Pending',
            'file_name' => trim($_SESSION['stored_file_name']),
            'csv_columns' => json_encode($fields),
            'total_participants' => count($field_row),
            'admin_id' => $_SESSION['admin']['id'],
            'created_at' => 'msqlfunc_NOW()',
        );
        $participants_csv_id = $pdo->insert('participants_csv', $file_data);

        move_uploaded_file($csv_tmpname, $PARTICIPANTS_CSV_DIR . '/' . $_SESSION['stored_file_name']);

        if (count($field_row) <= 51) {
            $participantsObj->participants_csv_import($participants_csv_id);
        }

        $description['ac_message'] =array(
          'ac_red_1'=>array(
            'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
            'title'=>$_SESSION['admin']['display_id'],
          ),
          'ac_message_1' =>' acknowledged they have read and agreed to the terms and conditions in loading participants into the system.',
        );
        activity_feed(3, $_SESSION['admin']['id'], 'Admin',$participants_csv_id, 'participants_csv',"Participants Import", $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));

        $res['msg'] = "Participants added Successfully";

        if (isset($_SESSION['tmp_file_name']) && isset($_SESSION['stored_file_name'])) {
            unset($_SESSION['tmp_file_name']);
            unset($_SESSION['stored_file_name']);
        }
        setNotifySuccess('Participants added successfully.');
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
