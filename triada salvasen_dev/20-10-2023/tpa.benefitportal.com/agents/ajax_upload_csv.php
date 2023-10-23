<?php
include_once 'layout/start.inc.php';
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 300000);

$validate = new Validation();
$res = array();
$agent_id = $_SESSION['agents']['id'];

$save_as = $_POST['save_as'];
$csv_file = $_FILES['csv_file'];
$csv_filename = $csv_file['name'];
$csv_tmpname = $csv_file['tmp_name'];

$lead_type = $_POST['lead_type'];
$tag_from = $_POST['tag_from'];
$existing_tag = isset($_POST['existing_tag']) ? $_POST['existing_tag'] : '';
$new_tag = isset($_POST['new_tag']) ? $_POST['new_tag'] : '';
$company_name = isset($_POST['company_name_field']) ? $_POST['company_name_field'] : '';
$fname = isset($_POST['fname_field']) ? $_POST['fname_field'] : '';
$lname = isset($_POST['lname_field']) ? $_POST['lname_field'] : '';
$email = isset($_POST['email_field']) ? $_POST['email_field'] : '';
$cell_phone = isset($_POST['cell_phone_field']) ? $_POST['cell_phone_field'] : '';
$state = isset($_POST['state_field']) ? $_POST['state_field'] : '';
$lead_report = "Y";

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
        if ($email == "" && $cell_phone == "") {
            $validate->string(array('required' => true, 'field' => 'email_field', 'value' => $email), array('required' => 'Email or phone is required'));
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
            'agent_id' => $agent_id,
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
            'is_report_send' => ($lead_report == 'Y' ? "Y" : "N"),
            'created_at' => 'msqlfunc_NOW()',
            'updated_at' => 'msqlfunc_NOW()'
        );
        $csv_agent_lead_id = $pdo->insert('csv_agent_leads', $file_data);

        move_uploaded_file($csv_tmpname, $CSV_DIR . '/' . $_SESSION['stored_file_name']);

        if (count($field_row) <= 51) {
            csv_agent_lead_import($csv_agent_lead_id);
        }

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
