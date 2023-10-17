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

$first_name_error = $last_name_error = $gender_error = $email_error = $phone_error = $terms_condition_error = "";

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

        if ($first_name == '') {
            $first_name_error = "First name is required.";
        } else if (!ctype_alnum($first_name)) {
            $first_name_error = "First name should be alphanumeric.";
        } else {
            $first_name_error = "";
        }

        if ($last_name == '') {
            $last_name_error = "Last name is required.";
        } else if (!ctype_alnum($last_name)) {
            $last_name_error = "Last name should be alphanumeric.";
        } else {
            $last_name_error = "";
        }

        if ($gender != 'Male' && $gender != 'Female') {
            $gender_error = "Please select gender.";
        } else {
            $gender_error = "";
        }

        if ($email == '') {
            $email_error = "Email is required.";
        } else if(!preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i", $email)){
            $email_error = "You do not entered valid email.";
        } else {
            $email_error = "";
        }

        if ($phone == '') {
            $phone_error = "Phone number is required.";
        } else if (!is_numeric($phone)) {
            $phone_error = "Numbers only.";
        } else {
            $phone_error = "";
        }

        if ($terms_condition == '') {
            $terms_condition_error = "Please select terms and condition.";
        } else {
            $terms_condition_error = "";
        }

        if ($first_name_error == '' && $last_name_error == '' && $gender_error == '' && $email_error == '' && $phone_error == '' && $terms_condition_error == '') {
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
