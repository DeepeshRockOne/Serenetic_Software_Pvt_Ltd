<?php
include_once __DIR__ . '/includes/connect.php'; 

$SITE_FAVICON_TEXT = "Registration";
$title = "Registration";

$template = 'index.inc.php';
$layout = 'main.layout.php';

$first_name = $last_name = $gender = $email = $phone = $password = $terms_condition = "";

$first_name_error = $last_name_error = $gender_error = $email_error = $phone_error = $password_error = $confirm_password_error = $terms_condition_error = "";

//registration (insert)
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if (isset($_POST['reg_submit'])) {
        $first_name = htmlspecialchars($_POST['first_name']);
        $last_name = htmlspecialchars($_POST['last_name']);
        $gender = isset($_POST['gender']) ? $_POST['gender'] : '';
        $email = htmlspecialchars($_POST['email']);
        $phone = htmlspecialchars($_POST['phone']);
        $password = htmlspecialchars($_POST['password']);
        $enc_password = md5($password);
        $confirm_password = htmlspecialchars($_POST['confirm_password']);
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
            try{
                $sql = "INSERT INTO registration (first_name, last_name, gender, email, phone, password, terms_condition) VALUES (:first_name, :last_name, :gender, :email, :phone, :enc_password, :terms_condition)";

                $pdo_statement = $pdo_conn->prepare($sql);

                $params = array(':first_name'=>$first_name, ':last_name'=>$last_name, ':gender'=>$gender, ':email'=>$email, ':phone'=>$phone, ':enc_password'=>$enc_password, ':terms_condition'=>$terms_condition);

                $result = $pdo_statement->execute($params);

                $pdo_statement = null;

                if (!empty($result)) {
                    header("location:view_reg_records.php?view_reg_records=true&registration_success=true");
                }
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }
    }
}

include_once 'layout/end.inc.php';
