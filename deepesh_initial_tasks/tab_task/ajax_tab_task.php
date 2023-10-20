<?php
include_once __DIR__ . '/includes/connect.php';
header("Content-type:application/json");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['tab_submit']) && $_POST['tab_submit']) {
        $tab_focus = array();

        $first_name = htmlspecialchars($_POST['first_name']);
        $last_name = htmlspecialchars($_POST['last_name']);
        $gender = isset($_POST['gender']) ? $_POST['gender'] : '';
        $email = htmlspecialchars($_POST['email']);
        $phone = htmlspecialchars($_POST['phone']);
        $password = htmlspecialchars($_POST['password']);
        $enc_password = md5($password);
        $confirm_password = htmlspecialchars($_POST['confirm_password']);
        $terms_condition = isset($_POST['terms_condition']) ? $_POST['terms_condition'] : '';

        $validation_1 = new Validation();
        $validation_2 = new Validation();
        $validation_3 = new Validation();
        $validation_4 = new Validation();
        $validation_errors = array();

        $validation_1->string(array('field'=>'first_name', 'value'=>$first_name), array('required'=>'First name is required.'));

        $validation_1->string(array('field'=>'last_name', 'value'=>$last_name), array('required'=>'Last name is required.'));

        $validation_2->string(array('field'=>'gender', 'value'=>$gender), array('required'=>'Gender is required.'));

        $validation_2->email(array('required' => true, 'field'=>'email', 'value'=>$email), array('required'=>'Email is required.', 'invalid'=>'Please enter valid email.'));

        $validation_3->string(array('field'=>'password', 'value'=>$password, 'min'=>5), array('required'=>'Password is required.', 'min'=>'Password should have minimum 5 characters.'));

        $validation_3->string(array('field'=>'confirm_password', 'value'=>$confirm_password), array('required'=>'Confirm password is required.'));

        if ($password != $confirm_password && $confirm_password != '') {
            $validation_3->setError('confirm_password', 'Password and Confirm password should be same.');
        }

        $validation_4->phone(array('required' => true, 'field'=>'phone', 'value'=>$phone), array('required'=>'Phone number is required.', 'invalid'=>'Please enter valid phone number.'));

        $validation_4->string(array('field'=>'terms_condition', 'value'=>$terms_condition), array('required'=>'Terms and condition is required.'));

        $validation_errors_1 = $validation_1->getErrors();
        $validation_errors_2 = $validation_2->getErrors();
        $validation_errors_3 = $validation_3->getErrors();
        $validation_errors_4 = $validation_4->getErrors();

        $validation_errors = array_merge($validation_errors_1, $validation_errors_2, $validation_errors_3, $validation_errors_4);

        if (empty($tab_focus) && !$validation_1->isValid()) {
            $tab_focus["tab_focus"] = 1;
        } else if (empty($tab_focus) && !$validation_2->isValid()) {
            $tab_focus["tab_focus"] = 2;
        } else if (empty($tab_focus) && !$validation_3->isValid()) {
            $tab_focus["tab_focus"] = 3;
        } else if (empty($tab_focus) && !$validation_4->isValid()) {
            $tab_focus["tab_focus"] = 4;
        }

        if (!empty($validation_errors)) {
            $validation_errors['tab_focus'] = $tab_focus;
        }

        if ($validation_1->isValid() && $validation_2->isValid() && $validation_3->isValid() && $validation_4->isValid()) {
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