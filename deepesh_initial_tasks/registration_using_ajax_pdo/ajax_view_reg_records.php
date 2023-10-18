<?php
include_once __DIR__ . '/includes/connect.php';
header("Content-type:application/json");

$SITE_FAVICON_TEXT = "View Registred Records";
$title = "View Registred Records";

$template = 'view_reg_records.inc.php';
$layout = 'main.layout.php';

//view registered record
if (isset($_REQUEST['view_reg_records_after_update']) && $_REQUEST['view_reg_records_after_update']) {
    $query = "SELECT * FROM registration";
    $data = array();

    $result = $pdo->select($query);

    if (count($result) > 0) {
        $data = $result;
    }
    echo json_encode($data);
    exit;
}

//edit registered record
if (isset($_REQUEST['edit_id']) && $_REQUEST['edit_id'] != '') {
    $edit_id = $_REQUEST['edit_id'];
    $data = array();

    $query = "SELECT * FROM registration WHERE id = :edit_id";
    $params = array(':edit_id'=>$edit_id);

    $result = $pdo->select($query, $params);

    if (count($result) == 1) {
        $data = $result;

        echo json_encode($data);
        exit;
    } else {
        $record_not_found = array('record_not_found'=>true);
        echo json_encode($record_not_found);
        exit;
    }
}

//update registered record
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if (isset($_POST['reg_update']) && isset($_POST['update_id']) && $_POST['update_id'] != '') {
        $update_id = $_POST['update_id'];
        $first_name = htmlspecialchars($_POST['first_name']);
        $last_name = htmlspecialchars($_POST['last_name']);
        $gender = isset($_POST['gender']) ? $_POST['gender'] : '';
        $email = htmlspecialchars($_POST['email']);
        $phone = htmlspecialchars($_POST['phone']);
        $terms_condition = isset($_POST['terms_condition']) ? $_POST['terms_condition'] : '';

        $validation->string(array('field'=>'first_name', 'value'=>$first_name), array('required'=>'First name is required.'));

        if ($first_name != '' && !ctype_alnum($first_name)) {
            $validation->setError('first_name', 'First name should be alphanumeric.');
        }

        $validation->string(array('field'=>'last_name', 'value'=>$last_name), array('required'=>'Last name is required.'));

        $validation->string(array('field'=>'gender', 'value'=>$gender), array('required'=>'Gender is required.'));

        $validation->email(array('required' => true, 'field'=>'email', 'value'=>$email), array('required'=>'Email is required.', 'invalid'=>'Please enter valid email.'));

        $validation->phone(array('required' => true, 'field'=>'phone', 'value'=>$phone), array('required'=>'Phone number is required.', 'invalid'=>'Please enter valid phone number.'));

        $validation->string(array('field'=>'terms_condition', 'value'=>$terms_condition), array('required'=>'Terms and condition is required.'));

        $validation_errors = $validation->getErrors();

        if ($validation->isValid()) {
            //date_default_timezone_set('Asia/Calcutta');
            $updated_at = 'msqlfunc_NOW()';

            $table = "registration";

            $params = array(
                'first_name' => $first_name,
                'last_name' => $last_name,
                'gender' => $gender,
                'email' => $email,
                'phone' => $phone,
                'terms_condition' => $terms_condition,
                'updated_at' => $updated_at
            );

            $warr = array(
                'clause' => 'id = :update_id',
                'params' => array(
                    ':update_id' => $update_id
                )
            );

            $pdo->update($table, $params, $warr);

            echo json_encode(array('update_success'=>true));
            exit;

            //header("location:view_reg_records.php?view_reg_records=true&record_update_success=true");
        } else {
            echo json_encode($validation_errors);
            exit;
        }
    }
}

//delete registered record
if (isset($_REQUEST['delete_id']) && $_REQUEST['delete_id'] != '') {
    $delete_id = $_REQUEST['delete_id'];

    $query = "SELECT * FROM registration WHERE id = :delete_id";
    $params = array(':delete_id'=>$delete_id);

    $result = $pdo->select($query, $params);

    if (count($result) == 1) {
        $query = "DELETE FROM registration WHERE id = :delete_id";

        $result = $pdo->delete($query, $params);
        $result = 1;

        if ($result == 1) {
            $reg_delete_success = array('reg_delete_success'=>true);
            echo json_encode($reg_delete_success);
            exit;
        }
    } else {
        $record_not_found = array('record_not_found'=>true);
        echo json_encode($record_not_found);
        exit;
    }
}
