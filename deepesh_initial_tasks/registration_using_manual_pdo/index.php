<?php
include_once __DIR__ . '/includes/connect.php'; 

$SITE_FAVICON_TEXT = "Registration";
$title = "Registration";

$template = 'index.inc.php';
$layout = 'main.layout.php';

$first_name = $last_name = $gender = $email = $phone = $password = $terms_condition = "";

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

        $validation->string(array('field'=>'first_name', 'value'=>$first_name), array('required'=>'First name is required.'));

        if ($first_name != '' && !ctype_alnum($first_name)) {
            $validation->setError('first_name', 'First name should be alphanumeric.');
        }

        $validation->string(array('field'=>'last_name', 'value'=>$last_name), array('required'=>'Last name is required.'));

        $validation->string(array('field'=>'gender', 'value'=>$gender), array('required'=>'Gender is required.'));

        $validation->email(array('required' => true, 'field'=>'email', 'value'=>$email), array('required'=>'Email is required.', 'invalid'=>'Please enter valid email.'));

        $validation->phone(array('required' => true, 'field'=>'phone', 'value'=>$phone), array('required'=>'Phone number is required.', 'invalid'=>'Please enter valid phone number.'));

        $validation->string(array('field'=>'password', 'value'=>$password, 'min'=>5), array('required'=>'Password is required.', 'min'=>'Password should have minimum 5 characters.'));

        $validation->string(array('field'=>'confirm_password', 'value'=>$confirm_password), array('required'=>'Confirm password is required.'));

        if ($password != $confirm_password && $confirm_password != '') {
            $validation->setError('confirm_password', 'Password and Confirm password should be same.');
        }

        $validation->string(array('field'=>'terms_condition', 'value'=>$terms_condition), array('required'=>'Terms and condition is required.'));

        $validation_errors = $validation->getErrors();

        //echo "<pre>"; print_r($validation_errors);

        if ($validation->isValid()) {
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
