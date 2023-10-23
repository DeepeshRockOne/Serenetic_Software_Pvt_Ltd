<?php
include_once (__DIR__) . '/includes/connect.php';
include_once (__DIR__) . '/../includes/function.class.php';
include_once '../includes/chat.class.php';
$LiveChat = new LiveChat();
$functionsList = new functionsList();
if (!isset($_SESSION['pass_check'])) {
  $_SESSION['pass_check'] = 0;
}
if (isset($_SESSION['admin']['id'])) {
  //redirect('index.php');
}

$validate = new Validation();  

$id = $_POST['admin_id'];
$key = $_POST['key'];
$fname = $_POST['fname'];
$lname = $_POST['lname'];
$email = trim($_POST['email']);
$password = $_POST['password'];
$cpassword = $_POST['password_chk'];
$agree = isset($_POST['agree']) ? $_POST['agree'] : '';
$phone = $_POST['phone'];

if (isset($_POST['key']) && !empty($_POST['key'])) {
  $query = 'SELECT id,fname,lname,display_id,email,type,status,TIMESTAMPDIFF(HOUR,invite_at,now()) as difference,invite_at FROM admin WHERE invite_key=:invite_key';
  $where = array(':invite_key' => $key);
  $row = $pdo->selectOne($query, $where);
  if (count($row) <= 0) {
    setNotifyError('Invalid Link');
    $response['status'] = "Invalid";
    redirect('index.php');
  } elseif ($row['difference'] > 168) {
    setNotifyError('Admin Registration link has expired');
    $response['status'] = "Expired";
    redirect('index.php');
  } else {
    $email = $row['email'];
  }
} else {
  setNotifyError('Invalid Link');
  $response['status'] = "Invalid";
  redirect('index.php');
}


$validate->string(array('required' => true, 'field' => 'fname', 'value' => $fname), array('required' => 'First Name is required'));
$validate->string(array('required' => true, 'field' => 'lname', 'value' => $lname), array('required' => 'Last Name is required'));
//$validate->email(array('required' => true, 'field' => 'email', 'value' => $email), array('required' => 'Email is required', 'invalid' => 'Please enter valid email'));
$validate->digit(array('required' => true, 'field' => 'phone1', 'value' => $phone, 'min' => 10, 'max' => 20), array('required' => 'Phone Number is required', 'invalid' => 'Valid Phone Number is required'));
$validate->string(array('required' => true, 'field' => 'password', 'value' => $password, 'min' => 8, 'max' => 20), array('required' => 'Password is required', 'invalid' => 'Please enter valid password'));
$validate->string(array('required' => true, 'field' => 'password_chk', 'value' => $cpassword, 'min' => 8, 'max' => 20), array('required' => 'Confirm Password is required', 'invalid' => 'Please enter valid confirm password'));
$validate->string(array('required' => true, 'field' => 'agree', 'value' => $agree), array('required' => 'Please check this box'));

/* if (!$validate->getError('email')) {
  $checkSql = "select * from admin where email=:email";
  $params = array(":email" => $email);
  $checkRow = $pdo->selectOne($checkSql, $params);
  //print_r($checkRow); exit;
  if (count($checkRow) > 0) {
  if ($checkRow['type'] == 'Reps') {
  $is_rep_found = true;
  $validate->setError("email", "");
  } else {
  $validate->setError("email", "Email Already Registered");
  $validate->setError('email', 'Email already registered');
  }
  }
  } */

if (!$validate->getError('password') && !empty($password)) {
  // if (strlen($password) < 6 || strlen($password) > 12) {
  //   $validate->setError('password', 'Passwords must be 6-12 characters');
  // }
  if (strlen($password) < 8 || strlen($password) > 20) {
        $validate->setError('password', 'Password must be 8-20 characters');
      } else if ((!preg_match('`[A-Z]`', $password) || !preg_match('`[a-z]`', $password)) // at least one alpha
                || !preg_match('`[0-9]`', $password)) { // at least one digit
        $validate->setError('password', 'Valid Password is required');
      } else if(!ctype_alnum($password)){
        $validate->setError('password', 'Special character not allowed');
      } else if (preg_match('`[?/$\*+]`', $password)) { 
        $validate->setError('password', 'Password is not valid');
      } else if (preg_match('`[,"]`', $password)) { 
        $validate->setError('password', 'Password is not valid');
      } else if (preg_match("[']", $password)) { 
        $validate->setError('password', 'Password is not valid');
      }
  // if (!ctype_alnum($password) // numbers & digits only
  //         || (!preg_match('`[A-Z]`', $password) && !preg_match('`[a-z]`', $password)) // at least one alpha
  //         || !preg_match('`[0-9]`', $password)) {
  //   // at least one digit
  //   $validate->setError('password', 'Valid Password is required');
  // }
}
// if ($validate->getError('password') && !$validate->getError('password_chk')) {
  if(empty($cpassword) && !empty($password)){
    $validate->setError('password_chk', 'Confirm Password is required');
  }else if(!empty($cpassword) && empty($password)){
    $validate->setError('password', 'Password is required');
  }else if ($password != $cpassword) {
    $validate->setError('password_chk', 'Both password must be same');
  }
// }

if ($validate->isValid()) {

  /* admin contract save in s3 bucket start (task => EL8-1219) */
    $adminContractFileName = $functionsList->saveAdminContract($id);
  /* admin contract save in s3 bucket end (task => EL8-1219) */
  
  //$display_id = get_inferno_id("admin");
  $update_params = array(
      'fname' => makeSafe($fname),
      'lname' => makeSafe($lname),
      'phone' => makeSafe($phone),
      'password' => "msqlfunc_AES_ENCRYPT('" . $password . "','" . $CREDIT_CARD_ENC_KEY . "')",
      'updated_at' => 'msqlfunc_NOW()',
      'status' => 'Active',
      'invite_key' => "",
  );
  if(!empty($adminContractFileName)){
    $update_params["admin_contract_file"] = $adminContractFileName;
  }
  $update_where = array(
      'clause' => 'id = :id',
      'params' => array(
          ':id' => makeSafe($id)
      )
  );

  $pdo->update('admin', $update_params, $update_where);

  $live_chat_use = $LiveChat->addLiveChatUser($id,'Admin');

  $customer_id = $id;

     

  $admin_row = $pdo->selectOne("SELECT * FROM admin WHERE id=:id",array(":id" => $id));
  
  $desc = array();
  $desc['ac_message'] = array(
      'ac_red_1' => array(
          'href' => 'admin_profile.php?id=' . md5($admin_row['id']),
          'title' => $admin_row['display_id'],
      ),
      'ac_message_1' => ' invite accepted'
  );
  $desc = json_encode($desc);
  activity_feed(3,$admin_row['id'],'Admin',$admin_row['id'],'Admin','Admin Invite Accepted','','',$desc);


  $response['status'] = 'success';
  setNotifySuccess("Registered sucessfully");
} else {
  if (count($validate->getErrors()) > 0) {
    $response['status'] = "fail";
  } else {
    $response['status'] = "success";
    // setNotifySuccess("Registration completed sucessfully");
  }
}


header('Content-Type: application/json');
$errors = $validate->getErrors();
$response['errors'] = $errors;
echo json_encode($response);
dbConnectionClose();

?>