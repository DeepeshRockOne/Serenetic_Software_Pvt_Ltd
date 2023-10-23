<?php
include_once (__DIR__) . '/includes/connect.php';
$validate = new Validation();
$response = array();
$email = $_POST['fp_email'];
$validate->email(array('required' => true, 'field' => 'fp_email', 'value' => $email), array('required' => 'Email is required', 'invalid' => 'Valid Email is required'));
if (empty($validate->getError('fp_email'))) {
    $check_email_exist = "SELECT id,fname,lname FROM customer WHERE email = :email AND type='Customer' AND is_deleted='N'";
    $check_email_where = array(":email" => makeSafe($email));
    $customer_row = $pdo->selectOne($check_email_exist, $check_email_where);
    if (!empty($customer_row)) {
        $key = md5($customer_row['fname'] . $customer_row['lname'] . sha1(time()));
        $update_params = array(
            'pwd_reset_key' => $key,
            'pwd_reset_at' => 'msqlfunc_NOW()'
        );
        $update_where = array(
            'clause' => 'id = :id',
            'params' => array(
                ':id' => $customer_row['id']
            )
        );
        $pdo->update('customer_settings', $update_params, $update_where);

        $link = $HOST . '/member/password_recovery.php?key=' . $key;
        $params = array();
        $params['fname'] = $customer_row['fname'];
        $params['lname'] = $customer_row['lname'];
        $params['link'] = $link;

        $smart_tags = get_user_smart_tags($customer_row['id'],'member');
                
        if($smart_tags){
            $params = array_merge($params,$smart_tags);
        }

        try {
            $param['USER_IDENTITY'] = array('display_id' => $customer_row['id'], 'location' => ($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']));
            trigger_mail(43, $params, $email, '', 3);
        } catch (Exception $e) {
        }
        setNotifySuccess("Instructions for resetting your password has been sent to your Email {$email}.");
        $response['status'] = 'success';
    } else {
        $validate->setError('fp_email', 'Email Id does not match/exist');
    }
}
$errors = $validate->getErrors();
if (!empty($errors)) {
    $response['errors'] = $errors;
    $response['status'] = 'fail';
}
echo json_encode($response);
dbConnectionClose();
exit;
?>