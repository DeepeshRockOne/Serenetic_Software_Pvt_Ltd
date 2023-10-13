<?php
    require_once("connection.php");

    $first_name = $last_name = $gender = $email = $phone = $terms_condition = "";

    $first_name_error = $last_name_error = $gender_error = $email_error = $phone_error = $password_error = $confirm_password_error = $terms_condition_error = "";

    //registration (insert)
    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        if (isset($_POST['reg_submit'])) {
            $first_name = $conn->real_escape_string($_POST['first_name']);
            $last_name = $conn->real_escape_string($_POST['last_name']);
            $gender = isset($_POST['gender']) ? $_POST['gender'] : '';
            $email = $conn->real_escape_string($_POST['email']);
            $phone = $conn->real_escape_string($_POST['phone']);
            $password = $conn->real_escape_string($_POST['password']);
            $enc_password = md5($password);
            $confirm_password = $conn->real_escape_string($_POST['confirm_password']);
            $terms_condition = isset($_POST['terms_condition']) ? $_POST['terms_condition'] : '';

            if ($first_name == '') {
                $first_name_error = "First name is required.";
            } else if (!ctype_alpha($first_name)) {
                $first_name_error = "First name should be alphabatic.";
            } else {
                $first_name_error = "";
            }

            if ($last_name == '') {
                $last_name_error = "Last name is required.";
            } else if (!ctype_alpha($last_name)) {
                $last_name_error = "Last name should be alphabatic.";
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

            if ($password == '') {
                $password_error = "Password is required.";
            } else if (strlen($password) < 5) {
                $password_error = "Password length should be minimum 5.";
            } else {
                $password_error = "";
            }

            if ($confirm_password == '') {
                $confirm_password_error = "Confirm password is required.";
            } else if ($password != $confirm_password) {
                $confirm_password_error = "Password and Confirm password should be same.";
            } else {
                $confirm_password_error = "";
            }

            if ($terms_condition == '') {
                $terms_condition_error = "Please select terms and condition.";
            } else {
                $terms_condition_error = "";
            }

            if ($first_name_error == '' && $last_name_error == '' && $gender_error == '' && $email_error == '' && $phone_error == '' && $password_error == '' && $confirm_password_error == '' && $terms_condition_error == '') {
                $sql = "INSERT INTO registration (first_name, last_name, gender, email, phone, password, terms_condition) VALUES ('".$first_name."', '".$last_name."', '".$gender."', '".$email."', '".$phone."', '".$enc_password."', '".$terms_condition."')";              

                if ($conn->query($sql) === true) {
                    header("location:view_reg_records.php?view_reg_records=true&registration_success=true");
                }
            }
        }
    }

    //view register records
    if (isset($_REQUEST['view_reg_records']) && $_REQUEST['view_reg_records'] == true) {
        $sql = "SELECT * FROM registration";
        $res = $conn->query($sql);

        $data = array();

        while($fetch = $res->fetch_object()) {
            $data[] = $fetch;
        }
    }

    //edit registered record
    if (isset($_REQUEST['edit_id']) && $_REQUEST['edit_id'] != '') {
    }

    //delete registered record
    if (isset($_REQUEST['delete_id']) && $_REQUEST['delete_id'] != '') {
        $delete_id = $_REQUEST['delete_id'];
        $sql = "SELECT * FROM registration WHERE id = $delete_id";
        $res = $conn->query($sql);

        if ($res->num_rows == 1) {
            $sql = "DELETE FROM registration WHERE id = $delete_id";

            if ($conn->query($sql)) {
                header("location:view_reg_records.php?view_reg_records=true&reg_delete_success=true");
            }
        } else {
            header("location:view_reg_records.php?view_reg_records=true&record_not_found=true");
        }
    }
?>