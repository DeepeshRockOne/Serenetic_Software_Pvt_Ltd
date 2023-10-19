<?php
include_once __DIR__ . '/includes/connect.php';
header("Content-type:application/json");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_POST['tab_submit']) && $_POST['tab_submit'] == "tab_submit=true") {

        $form1_data = $form2_data = $form3_data = $form4_data = "";

        $first_name = $last_name = $gender = $email = $phone = $password = $confirm_password = $terms_condition = "";

        $form1_data = explode('&', $_POST['form_1']);
        $form2_data = explode('&', $_POST['form_2']);
        $form3_data = explode('&', $_POST['form_3']);
        $form4_data = explode('&', $_POST['form_4']);

        if (!empty($form1_data)) {
            foreach ($form1_data as $pair) {
                list($key, $value) = explode('=', $pair);
    
                if ($key == 'first_name') {
                    $first_name = htmlspecialchars(urldecode($value));
                }
                if ($key == 'last_name') {
                    $last_name = htmlspecialchars(urldecode($value));
                }
            }
        }

        if (!empty($form2_data)) {
            foreach ($form2_data as $pair) {
                list($key, $value) = explode('=', $pair);
    
                if ($key == 'gender') {
                    $gender = htmlspecialchars(urldecode($value));
                }
                if ($key == 'email') {
                    $email = htmlspecialchars(urldecode($value));
                }
            }
        }
    
        if (!empty($form3_data)) {
            foreach ($form3_data as $pair) {
                list($key, $value) = explode('=', $pair);
    
                if ($key == 'password') {
                    $password = htmlspecialchars(urldecode($value));
                }
                if ($key == 'confirm_password') {
                    $confirm_password = htmlspecialchars(urldecode($value));
                }
            }
        }
    
        if (!empty($form4_data)) {
            foreach ($form4_data as $pair) {
                list($key, $value) = explode('=', $pair);
    
                if ($key == 'phone') {
                    $phone = htmlspecialchars(urldecode($value));
                }
                if ($key == 'terms_condition') {
                    $terms_condition = htmlspecialchars(urldecode($value));
                }
            }
        }

        $enc_password = md5($password);

        $validation->string(array('field'=>'first_name', 'value'=>$first_name), array('required'=>'First name is required.'));

        $validation->string(array('field'=>'last_name', 'value'=>$last_name), array('required'=>'Last name is required.'));

        $validation->string(array('field'=>'gender', 'value'=>$gender), array('required'=>'Gender is required.'));

        $validation->email(array('required' => true, 'field'=>'email', 'value'=>$email), array('required'=>'Email is required.', 'invalid'=>'Please enter valid email.'));

        $validation->string(array('field'=>'password', 'value'=>$password, 'min'=>5), array('required'=>'Password is required.', 'min'=>'Password should have minimum 5 characters.'));

        $validation->string(array('field'=>'confirm_password', 'value'=>$confirm_password), array('required'=>'Confirm password is required.'));

        if ($password != $confirm_password && $confirm_password != '') {
            $validation->setError('confirm_password', 'Password and Confirm password should be same.');
        }

        $validation->phone(array('required' => true, 'field'=>'phone', 'value'=>$phone), array('required'=>'Phone number is required.', 'invalid'=>'Please enter valid phone number.'));

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

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['tab_next_clicked']) && $_POST['tab_next_clicked'] != '') {
            $form1_data = $form2_data = $form3_data = $form4_data = "";

            $first_name = $last_name = $gender = $email = $phone = $password = $confirm_password = $terms_condition = "";

            $tab_next_clicked = explode('=', $_POST['tab_next_clicked']);
            $tab_next_clicked = explode('_', $tab_next_clicked[0]);
            $tab_next_clicked = $tab_next_clicked[1];

            if ($tab_next_clicked != '') {
                switch ($tab_next_clicked) {
                    case '1':
                        $form1_data  = explode('&', $_POST['form_'.$tab_next_clicked]);
                        break;
                    case '2':
                        $form2_data  = explode('&', $_POST['form_'.$tab_next_clicked]);
                        break;
                    case '3':
                        $form3_data  = explode('&', $_POST['form_'.$tab_next_clicked]);
                        break;
                }
            }

            if (!empty($form1_data)) {
                foreach ($form1_data as $pair) {
                    list($key, $value) = explode('=', $pair);
        
                    if ($key == 'first_name') {
                        $first_name = htmlspecialchars(urldecode($value));
                    }
                    if ($key == 'last_name') {
                        $last_name = htmlspecialchars(urldecode($value));
                    }
                }

                $validation->string(array('field'=>'first_name', 'value'=>$first_name), array('required'=>'First name is required.'));

                $validation->string(array('field'=>'last_name', 'value'=>$last_name), array('required'=>'Last name is required.'));
            }
    
            if (!empty($form2_data)) {
                foreach ($form2_data as $pair) {
                    list($key, $value) = explode('=', $pair);
        
                    if ($key == 'gender') {
                        $gender = htmlspecialchars(urldecode($value));
                    }
                    if ($key == 'email') {
                        $email = htmlspecialchars(urldecode($value));
                    }
                }

                $validation->string(array('field'=>'gender', 'value'=>$gender), array('required'=>'Gender is required.'));

                $validation->email(array('required' => true, 'field'=>'email', 'value'=>$email), array('required'=>'Email is required.', 'invalid'=>'Please enter valid email.'));
            }
        
            if (!empty($form3_data)) {
                foreach ($form3_data as $pair) {
                    list($key, $value) = explode('=', $pair);
        
                    if ($key == 'password') {
                        $password = htmlspecialchars(urldecode($value));
                    }
                    if ($key == 'confirm_password') {
                        $confirm_password = htmlspecialchars(urldecode($value));
                    }
                }

                $validation->string(array('field'=>'password', 'value'=>$password, 'min'=>5), array('required'=>'Password is required.', 'min'=>'Password should have minimum 5 characters.'));

                $validation->string(array('field'=>'confirm_password', 'value'=>$confirm_password), array('required'=>'Confirm password is required.'));

                if ($password != $confirm_password && $confirm_password != '') {
                    $validation->setError('confirm_password', 'Password and Confirm password should be same.');
                }
            }

            $validation_errors = $validation->getErrors();

            echo json_encode($validation_errors);
            exit;
        }
    }
}