<?php
include_once __DIR__ . '/includes/connect.php';
$validate = new validation();
$response = array();

$email = $_POST['login_email'];
$password = $_POST['login_password'];
$validate->string(array('required' => true, 'field' => 'login_email', 'value' => $email), array('required' => 'Email ID you entered is not valid.'));
$validate->string(array('required' => true, 'field' => 'login_password', 'value' => $password), array('required' => 'The Password you entered is incomplete.'));

if (empty($validate->getError('login_email'))) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $validate->setError("login_email", "Valid email is required");
    }
}
if ($validate->isValid()) {
    $sql = "SELECT c.id,c.sponsor_id,c.fname,c.lname,c.rep_id,s.user_name,AES_DECRYPT(c.password,'" . $CREDIT_CARD_ENC_KEY . "') as stored_password 
        FROM customer c
        JOIN customer s ON (s.id=c.sponsor_id)
        WHERE c.email=:email AND c.is_deleted='N' AND c.type='Customer'";
    $row = $pdo->selectOne($sql, array(':email' => $email));
    
    if (!empty($row['stored_password']) && $password == $row['stored_password']) {
        
        $response['customer_sponsor_id'] = $row['sponsor_id'];
        $response['customer_id'] = $row['id'];
        $response['status'] = 'success';
    } else {
        $validate->setError("login_email", "Email ID or the password you entered is not valid.");
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