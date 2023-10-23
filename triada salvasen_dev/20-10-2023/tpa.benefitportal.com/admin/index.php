<?php
include_once __DIR__ . '/includes/connect.php';
$remember='off';
$response = array();
if (!isset($_SESSION['pass_check'])) {
  $_SESSION['pass_check'] = 0;
}
$email = '';
$previous_page = isset($_REQUEST['previous_page']) ? $_REQUEST['previous_page'] : '';
$REAL_IP_ADDRESS = get_real_ipaddress();
if (isset($_SESSION['admin']['id'])) {
  if(!empty($previous_page)){
     if (isset($_POST['submit']) && $_POST['submit'] == 'login'){
        $response['status'] = 'previous_page';
        $response['redirect_url'] = $previous_page;
        echo json_encode($response);
        exit();
    }
    redirect($previous_page);
  }else{
    if(!empty(get_admin_dashboard($_SESSION['admin']['id'])) && get_admin_dashboard($_SESSION['admin']['id']) == 'Support Dashboard'){
      if (isset($_POST['submit']) && $_POST['submit'] == 'login'){
        $response['status'] = 'support_dashboard';
        echo json_encode($response);
        exit();
      }
      redirect('support_dashboard.php');
    } else {
      if (isset($_POST['submit']) && $_POST['submit'] == 'login'){
        $response['status'] = 'login_success';
        echo json_encode($response);
        exit();
      }
      redirect('dashboard.php');
    }
  }
}

if (isset($_COOKIE['email'])) {
  $email = $_COOKIE['email'];
}

if (isset($_COOKIE['password'])) {
  $password = $_COOKIE['password'];
}
if (isset($_COOKIE['remember'])) {
  $remember = $_COOKIE['remember'];
}

$validate = new Validation();

if (isset($_POST['submit']) && $_POST['submit'] == 'resend_otp') {
  $email = trim($_POST['email']);
  $password = $_POST['password'];

  $selSql = "SELECT id,fname,lname,email,is_2fa,send_otp_via,via_email,via_sms,phone
            FROM admin
            WHERE (email=:email OR display_id=:email) AND status='Active' AND is_active ='Y' AND is_deleted='N' AND AES_DECRYPT(password,'" . $CREDIT_CARD_ENC_KEY . "')=:u_password";
  $params = array(":email" => makeSafe($email), ":u_password" => $password);
  $adminRow = $pdo->selectOne($selSql, $params);

  if(!empty($adminRow)) {
    /*-- OTP Send Code ---*/
    $globalSett = get_global_user_setting('Admin',array('is_2fa'));

    $otp_via = '';
    $send_via = '';
    if($adminRow['is_2fa'] == "Y"){
      $otp_via = $adminRow['send_otp_via'];
      $send_via = !empty($adminRow['via_email']) ? $adminRow['via_email'] : $adminRow['email'];
      if($otp_via == 'sms'){
        $send_via = !empty($adminRow['via_sms']) ? $adminRow['via_sms'] : $adminRow['phone'];
      }
    }else if(checkIsset($globalSett['is_2fa']) == "Y"){
      $otp_via = 'email';
      $send_via = $adminRow['email'];
    }
    if(isset($_SESSION['admin_otp']['admin_id']) && $_SESSION['admin_otp']['admin_id'] == $adminRow['id']) {
      $email_otp = $_SESSION['admin_otp']['otp'];
    } else {
      $email_otp = generateOTP();
    }

    $trigger_id = getname('triggers','T902','id','display_id');
    $params = array();
    $params['fname'] = $adminRow["fname"];
    $params['Code'] = $email_otp;
    $smart_tags = get_user_smart_tags($adminRow['id'],'admin');
    if($smart_tags){
        $params = array_merge($params,$smart_tags);
    }
    if($otp_via == 'sms'){
      trigger_sms($trigger_id, $send_via, $params);
    }else{
      trigger_mail($trigger_id, $params, $send_via);
    }
    $_SESSION['admin_otp'] = array(
      "admin_id"=> $adminRow['id'],
      "otp"=> $email_otp,
      "otp_via"=>$otp_via,
      "send_to"=>$send_via,
    );
    $response['status'] = "success";
  } else {
    $response['status'] = "fail";
    setNotifySuccess('Please try again!');
  }
  echo json_encode($response);
  exit();
}

$validate = new Validation();
if ((isset($_POST['submit']) && $_POST['submit'] == 'login') || isset($_GET['login_key'])) {

  $globalSett = get_global_user_setting('Admin',array('is_2fa','is_ip_restriction','allowed_ip'));
  $verify_otp = isset($_POST['verify_otp']) ? $_POST['verify_otp'] : '';
  $otp = isset($_POST['otp']) ? $_POST['otp'] : '';
  $email = trim($_POST['email']);
  $password = $_POST['password'];
  $login_key = isset($_GET['login_key'])?$_GET['login_key']:'';
  //echo $password; exit;
  if (empty($login_key)) {
    $validate->string(array('required' => true, 'field' => 'email', 'value' => $email), array('required' => 'Email is required', 'invalid' => 'Enter valid email address'));
    $validate->string(array('required' => true, 'field' => 'password', 'value' => $password), array('required' => 'Password is required'));
  }

  if ($validate->isValid()) {

    if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
        $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
    }else {
      $_SERVER['REMOTE_ADDR'] = !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'];
    }
    $admin_ip_address = $_SERVER['REMOTE_ADDR'];
    
    if (!empty($login_key)) {
      $selSql = "select * from admin where status='Active' AND is_active ='Y' AND temp_login_key=:key AND is_deleted='N'";
      $params = array(":key" => $login_key);
      $custRow = $pdo->selectOne($selSql, $params);
    } else {
      $selSql = "select * from admin where (email=:email OR display_id=:email) AND is_deleted='N' and  status='Active' AND is_active ='Y' AND AES_DECRYPT(password,'" . $CREDIT_CARD_ENC_KEY . "')=:u_password";
      $params = array(":email" => makeSafe($email), ":u_password" => $password);
      $custRow = $pdo->selectOne($selSql, $params);
    }
    if($verify_otp == "yes") {
       $validate->string(array('required' => true, 'field' => 'otp', 'value' => $otp), array('required' => 'Enter 6 - Digit Code'));
    }

    if (count($custRow) > 0) {
      if($custRow['is_ip_restriction'] == "Y" || checkIsset($globalSett['is_ip_restriction']) == "Y") {
        $allowed_ip = (!empty($custRow["allowed_ip"]) ? explode(",", $custRow["allowed_ip"]) : array());
        $globalIP = (!empty($globalSett["allowed_ip"]) ? explode(",", $globalSett["allowed_ip"]) : array());
        $allowed_ip = array_merge($allowed_ip,$globalIP);
        if(!in_array($admin_ip_address,$allowed_ip)) {
          $validate->setError("general", "Login for this admin is not allowed from this device, please contact admin.");
        }
      }
      if ($validate->isValid()){
        if($verify_otp == "yes") {
          if(isset($_SESSION['admin_otp']) && $_SESSION['admin_otp']['admin_id'] == $custRow['id'] && $_SESSION['admin_otp']['otp'] == $otp) {
          } else {
            $validate->setError("otp", "Code you entered is not valid. Please re-enter."); 
          }
        } else {
          $otp_via = '';
          $send_via = '';
          if($custRow['is_2fa'] == "Y"){
            $otp_via = $custRow['send_otp_via'];
            $send_via = !empty($custRow['via_email']) ? $custRow['via_email'] : $custRow['email'];
            if($otp_via == 'sms'){
              $send_via = !empty($custRow['via_sms']) ? $custRow['via_sms'] : $custRow['phone'];
            }
          }else if(checkIsset($globalSett['is_2fa']) == "Y"){
            $otp_via = 'email';
            $send_via = $custRow['email'];
          }

          if($custRow['is_2fa'] == "Y" || checkIsset($globalSett['is_2fa']) == "Y") {
              /*-- OTP Send Code ---*/
              $email_otp = generateOTP();
              $trigger_id = getname('triggers','T902','id','display_id');
              $params = array();
              $params['fname'] = $custRow["fname"];
              $params['Code'] = $email_otp;
              $smart_tags = get_user_smart_tags($custRow['id'],'admin');
              if($smart_tags){
                  $params = array_merge($params,$smart_tags);
              }
              if($otp_via == 'sms'){
                trigger_sms($trigger_id, $send_via, $params);
              }else{
                trigger_mail($trigger_id, $params, $send_via);
              }
              
              $_SESSION['admin_otp'] = array(
                "admin_id"=> $custRow['id'],
                "otp"=> $email_otp,
                "otp_via"=>$otp_via,
                "send_to"=>$send_via,
              );
              $response['otp_via'] = $otp_via;
              $response['status'] = "otp_send";
              echo json_encode($response);
              exit();
              /*--/OTP Send Code ---*/
          }
        }
      }
      if ($validate->isValid()){
        if($custRow['feature_access'] == '' || $custRow['feature_access'] == NULL){
          $type = $custRow['type'];
          $sql_acl = "SELECT * FROM access_level where name='$type' ORDER BY name";
          $res_acls = $pdo->selectOne($sql_acl);
          if(!empty($res_acls)){
            $custRow['feature_access'] = $res_acls['feature_access'];
          }
        }

        $chatId = $pdo->selectOne("SELECT ac.id from assigned_admin_circle aac JOIN admin_circle ac ON(ac.id=aac.circle_id) WHERE admin_id=:id AND aac.is_deleted='N' AND ac.status='Active' AND ac.is_deleted='N'",array(":id"=>$custRow['id']));

        $_SESSION['admin']['id'] = $custRow['id'];
        $_SESSION['admin']['name'] = $custRow['fname'] . $custRow['lname'];
        $_SESSION['admin']['display_id'] = $custRow['display_id'];
        $_SESSION['admin']['fname'] = $custRow['fname'];
        $_SESSION['admin']['lname'] = $custRow['lname'];
        $_SESSION['admin']['email'] = $custRow['email'];
        $_SESSION['admin']['photo'] = $custRow['photo'];
        $_SESSION['admin']['type'] = $custRow['type'];
        $_SESSION['admin']['access'] = explode(",", $custRow['feature_access']);
        $_SESSION['admin']['chat_password'] = $custRow['chat_password'];
        $_SESSION['admin']['secret'] = base64_encode($password);
        $_SESSION['admin']['created_at'] = $custRow['created_at'];
        $_SESSION['admin']['updated_at'] = $custRow['updated_at'];
        $_SESSION['admin']['timezone'] = $_REQUEST['timezone'];
        $_SESSION['admin']['chat'] = 'false';
        if(!empty($chatId['id'])){
          $_SESSION['admin']['chat'] = 'true';
        }

        //Remember me
        $remember = isset($_POST['remember'])?$_POST['remember']:'';
        if (isset($_POST['remember']) && $_POST['remember'] == 'on') {
          setcookie("email", $custRow['email'], time() + (60 * 60 * 24 * 7));
          setcookie("password", $password, time() + (60 * 60 * 24 * 7));
          setcookie("remember", $remember, time() + (60 * 60 * 24 * 7));
        } else {
          setcookie("email", $custRow['email'], time() - (60 * 60 * 24 * 7));
          setcookie("password", $password, time() - (60 * 60 * 24 * 7));
          setcookie("remember", $remember, time() - (60 * 60 * 24 * 7));
        }
        // END

        $updateStr = array("last_login" => 'msqlfunc_NOW()','temp_login_key'=>null);
        $where = array("clause" => 'id=:id', 'params' => array(':id' => $_SESSION['admin']['id']));
        $pdo->update("admin", $updateStr, $where);

        $user_data = get_user_data($_SESSION['admin']);
        $audit_log_id = audit_log($user_data, $_SESSION['admin']['id'], "Admin", "Log In", '', '', 'login');

        $extra['user_display_id'] = $_SESSION['admin']['display_id'];
        $description['description'] = $_SESSION['admin']['display_id'].' logged into account';
        activity_feed(3, $_SESSION['admin']['id'], 'Admin', $_SESSION['admin']['id'], 'logged_account','Logged Admin Account', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description),'',json_encode($extra));

        if(!empty($previous_page)){
          $response['redirect_url'] = $previous_page;
          $response['status'] = "previous_page";
        }else{
          if(!empty(get_admin_dashboard($_SESSION['admin']['id'])) && get_admin_dashboard($_SESSION['admin']['id']) == 'Support Dashboard'){
            $response['status'] = "support_dashboard";
          } else {
            $response['status'] = "login_success";
          }
        }

        $_SESSION['admin']['audit_log_id'] = $audit_log_id;

        if(!empty($previous_page)){
          $response['redirect_url'] = $previous_page;
          $response['status'] = "previous_page";
        }else{
          if (isset($_SESSION['unsubscribe_email'])) {
            $response['status'] = "unsubscribe_email";
            $response['unsubscribe_email'] = $_SESSION['unsubscribe_email'];
          } else {
            $response['status'] = "login_success";
          }
        }
      }
    } else {
      //$validate->setError("error", "The email or  password you entered is incorrect.");
      $validate->setError("password", "The email/admin id or password you entered is incorrect.");  
    }
  }
  if(count($validate->getErrors()) > 0){
      $response['status'] = "error";
      $response['errors'] = $validate->getErrors();
  }
  echo json_encode($response);
  exit();
}
// }
$exStylesheets = array('thirdparty/colorbox/colorbox.css');
$exJs = array('thirdparty/colorbox/jquery.colorbox.js','thirdparty/bower_components/moment/moment.js','thirdparty/bower_components/moment/moment-timezone-with-data.min.js');

$errors = $validate->getErrors();
//print_r($errors);
$template = 'index.inc.php';
$layout = 'single.layout.php';
//$layout='temp.php';
include_once 'layout/end.inc.php';
?>