<?php
include_once __DIR__ . '/includes/connect.php';
header("Content-type:application/json");

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

        if ($validation->isValid()) {
            $table = "registration";

            $params = array(
                'first_name' => $first_name,
                'last_name' => $last_name,
                'gender' => $gender,
                'email' => $email,
                'phone' => $phone,
                'password' => $enc_password,
                'terms_condition' => $terms_condition
            );

            $inserted_id = $pdo->insert($table, $params);

            if (isset($inserted_id)) {
                echo json_encode(array('inseted_success'=>true));
                exit;
            }
        } else {
            echo json_encode($validation_errors);
            exit;
        }
    }
}