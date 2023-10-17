<?php
include_once __DIR__ . '/includes/connect.php'; 

$SITE_FAVICON_TEXT = "View Registred Records";
$title = "View Registred Records";

$template = 'view_reg_records.inc.php';
$layout = 'main.layout.php';

if (isset($_REQUEST['edit_id']) && $_REQUEST['edit_id'] != '') {
    $SITE_FAVICON_TEXT = "Edit Registred Record";
    $title = "Edit Registred Record";

    $template = 'edit_reg_records.inc.php';
    $layout = 'main.layout.php';
}

$first_name = $last_name = $gender = $email = $phone = $terms_condition = "";

//view register records
if (isset($_REQUEST['view_reg_records']) && $_REQUEST['view_reg_records'] == true) {
    $data = array();

    try {
        $sql = "SELECT * FROM registration";

        $pdo_statement = $pdo_conn->prepare($sql);
        $pdo_statement->execute();

        $rows = $pdo_statement->rowCount();

        if ($rows > 0) {
            while ($row = $pdo_statement->fetch(PDO::FETCH_ASSOC)) {
                $data[] = $row;
            }
        }
        
        $pdo_statement = null;
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}

//edit registered record
if (isset($_REQUEST['edit_id']) && $_REQUEST['edit_id'] != '') {
    $edit_id = $_REQUEST['edit_id'];
    $data = array();

    try {
        $sql = "SELECT * FROM registration WHERE id = :edit_id";
        $pdo_statement = $pdo_conn->prepare($sql);

        $params = array(':edit_id'=>$edit_id);
        $pdo_statement->execute($params);

        $rows = $pdo_statement->rowCount();

        if ($rows == 1) {
            while($rows = $pdo_statement->fetch(PDO::FETCH_ASSOC)) {
                $data[] = $rows;
            }

            $pdo_statement = null;
        } else {
            header("location:view_reg_records.php?view_reg_records=true&record_not_found=true");
        }
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}

//update registered record
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if (isset($_POST['reg_update']) && isset($_POST['update_id']) && $_POST['update_id'] != '') {
        //when we want to update record, empty $data variable which set when user press edit button
        //because of $data variable fiedls are visible filled up when validation is occured
        $data = array();

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
            try {
                date_default_timezone_set('Asia/Calcutta');
                $updated_at = date('Y-m-d H:i:s');

                $sql = "UPDATE registration SET first_name = :first_name, last_name = :last_name, gender = :gender, email = :email, phone = :phone, terms_condition = :terms_condition, updated_at = :updated_at WHERE id = :update_id";
                $pdo_statement = $pdo_conn->prepare($sql);

                $params = array(':first_name'=>$first_name, ':last_name'=>$last_name, ':gender'=>$gender,  ':email'=>$email, ':phone'=>$phone, ':terms_condition'=>$terms_condition, ':updated_at'=>$updated_at, ':update_id'=>$update_id);
                $result = $pdo_statement->execute($params);
//$pdo_statement->debugDumpParams(); exit;
                $pdo_statement = null;

                if (!empty($result)) {
                    header("location:view_reg_records.php?view_reg_records=true&record_update_success=true");
                } else {
                    header("location:view_reg_records.php?view_reg_records=true&record_not_updated=true");
                }
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }
    }
}

//delete registered record
if (isset($_REQUEST['delete_id']) && $_REQUEST['delete_id'] != '') {
    $delete_id = $_REQUEST['delete_id'];

    try {
        $sql = "SELECT * FROM registration WHERE id = :delete_id";
        $pdo_statement = $pdo_conn->prepare($sql);

        $params = array(':delete_id'=>$delete_id);
        $pdo_statement->execute($params);

        $rows = $pdo_statement->rowCount();

        if ($rows == 1) {
            try {
                $sql = "DELETE FROM registration WHERE id = :delete_id";
                $pdo_statement = $pdo_conn->prepare($sql);

                $result = $pdo_statement->execute($params);

                $pdo_statement = null;

                if (!empty($result)) {
                    header("location:view_reg_records.php?view_reg_records=true&reg_delete_success=true");
                }
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        } else {
            header("location:view_reg_records.php?view_reg_records=true&record_not_found=true");
        }
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}

include_once 'layout/end.inc.php';
