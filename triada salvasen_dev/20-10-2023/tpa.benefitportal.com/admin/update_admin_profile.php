<?php

include_once dirname(__FILE__) . '/layout/start.inc.php';
$res = array();
$id = $_GET['id'];
$query = "SELECT  fname,lname,email,display_id,id FROM `admin`  WHERE md5(id) = :id ";
$where = array(':id' => makeSafe($id));
$tmp_res = $pdo->selectOne($query, $where);

if (count($tmp_res) > 0) {

  $validate = new Validation();

  $fname = $_POST['fname'];
  $lname = $_POST['lname'];
  $email = checkIsset($_POST['email']);
  $mobile_number = phoneReplaceMain($_POST['mobile_number']);
  $password = $_POST['password']; 
  $c_password = $_POST['c_password']; 
  $is_2fa = !empty($_POST['is_2fa']) ? $_POST['is_2fa'] : 'N';
  $is_ip_restriction = !empty($_POST['is_ip_restriction']) ? $_POST['is_ip_restriction'] : 'N';
  $allowed_ip_res = !empty($_POST['allowed_ip_res']) ? $_POST['allowed_ip_res'] : array();
  if($is_ip_restriction == "N") {
      $allowed_ip_res = array();
  }
  $send_via = checkIsset($_POST['send_via']);
  $via_mobile = checkIsset($_POST['via_mobile'])!='' ? phoneReplaceMain($_POST['via_mobile']) : '';
  $via_email = checkIsset($_POST['via_email']);

    $validate->string(array('required' => true, 'field' => 'fname', 'value' => $fname), array('required' => 'First name is required'));
    $validate->string(array('required' => true, 'field' => 'lname', 'value' => $lname), array('required' => 'Last name is required'));
    $validate->email(array('required' => true, 'field' => 'email', 'value' => $email), array('required' => 'Email is required.', 'invalid' => 'Please enter valid email'));

    $validate->phoneDigit(array('required' => true, 'field' => 'mobile_number', 'value' => $mobile_number), array('required' => 'Mobile number is required', 'invalid' => 'Enter valid mobile number'));
    
    if (!$validate->getError('email')) {
      $selectEmail = "SELECT id, email FROM admin WHERE is_deleted='N' AND email = :email AND md5(id)!=:id ";
      $where_select_email = array(':email' => makeSafe($email),':id'=>$id);
      $resultEmail = $pdo->selectOne($selectEmail, $where_select_email);
      
      if ($resultEmail) {
        $validate->setError("email", "This email is already associated with another admin account");
      }
    }

    if($is_ip_restriction == "Y") {
        foreach ($allowed_ip_res as $key => $allowed_ip) {
            $validate->string(array('required' => true, 'field' => 'ip_address_'.$key, 'value' => $allowed_ip), array('required' => 'IP Address is required'));
            if (!empty($allowed_ip) && !filter_var($allowed_ip, FILTER_VALIDATE_IP)) {
                $validate->setError('ip_address_'.$key, 'IP Address not valid');
            }
        }
    }

    if($is_2fa == 'Y'){
      if($send_via == ''){
        $validate->setError('send_via', 'Please select any method.');
      }else{
        if($send_via == 'sms'){
          $validate->phoneDigit(array('required' => true, 'field' => 'via_mobile', 'value' => $via_mobile), array('required' => 'Phone number is required', 'invalid' => 'Enter valid phone number'));
        }else{
          $validate->email(array('required' => true, 'field' => 'via_email', 'value' => $via_email), array('required' => 'Email Address is required.', 'invalid' => 'Please enter valid Email Address'));
        }
      }
    }
if ($password != '') {
    if (!$validate->getError('password')) {
        if (strlen($password) < 6 || strlen($password) > 20) {
            $validate->setError('password', 'Passwords must be 6-20 characters');
        } else if ((!preg_match('`[A-Z]`', $password) || !preg_match('`[a-z]`', $password)) // at least one alpha
             || !preg_match('`[0-9]`', $password)) {
            // at least one digit
            $validate->setError('password', 'Password is not valid');
        } else if (!ctype_alnum($password)) {
            $validate->setError('password', 'Special character not allowed');
        }
    }

    if (!$validate->getError('password')) {
      if($password !== $c_password){
        $validate->setError('c_password', 'Password is not match');
      }
    }
}

  if ($validate->isValid()) {

    $new_update_details =array(
      'fname' => checkIsset($fname),
      'lname' => checkIsset($lname),
      'email' => checkIsset($email),
      'phone' => checkIsset($mobile_number),
      'is_2fa' => isset($_POST['is_2fa']) ? 'Selected' : 'Unselected',
      'send_otp_via' => checkIsset($send_via),
      'via_sms' => checkIsset($via_mobile),
      'via_email' => checkIsset($via_email),
      'is_ip_restriction' => isset($_POST['is_ip_restriction']) ? 'Selected' : 'Unselected',
      'allowed_ip' => implode(',',array_values($allowed_ip_res)),
    );
    $updateParams = array(
        'updated_at' => 'msqlfunc_NOW()',
        'fname' => makeSafe($fname),
        'lname' => makeSafe($lname),
        'email' => makesafe($email),
        'phone' => makeSafe($mobile_number),
    );
    $updateParams['is_2fa'] = $is_2fa;
    if($send_via !=''){
      $updateParams['send_otp_via'] = $send_via;
      if($send_via == 'sms'){
        $updateParams['via_sms'] = $via_mobile;
      }else{
        $updateParams['via_email'] = $via_email;
      }
    }
    $updateParams['is_ip_restriction'] = $is_ip_restriction;
    if($is_ip_restriction == "Y") {
        $updateParams['allowed_ip'] = implode(',',array_values($allowed_ip_res));
    } else {
        $updateParams['allowed_ip'] = "";
    }
    if($password !=''){
      $updateParams['password']="msqlfunc_AES_ENCRYPT('" . $password . "','" . $CREDIT_CARD_ENC_KEY . "')";
    }
    $update_where = array(
        'clause' => 'md5(id) = :id',
        'params' => array(
            ':id' => makeSafe($id)
        )
    );
    
    /* Code for audit log*/
   $extra_column = '';
  $update_params_new=$updateParams;
  unset($update_params_new['updated_at']);
  foreach($update_params_new as $key_audit=>$up_params)
  {
    $extra_column.=",".$key_audit;
  }
  if($extra_column!='')
  {
   $extra_column=trim($extra_column,',');
   $select_admin_data="SELECT ".$extra_column." FROM admin WHERE md5(id)=:id";
   $select_admin_where=array(':id'=>$id);
   $result_audit_customer_data=$pdo->selectOne($select_admin_data,$select_admin_where);
  } 
  
  /* End Code for audit log*/

    $updateData = $pdo->update('admin', $updateParams, $update_where,true);
    
    /* Code for audit log*/
    $user_data = get_user_data($_SESSION['admin']);
    audit_log_new($user_data, $_SESSION['admin']['id'], "Admin", "Admin Details Updated Id is ".$id, $result_audit_customer_data, $update_params_new, 'admin details update by admin');

    $description = array();
    $ac_message_1 = '';
   if($id == md5($_SESSION['admin']['id'])){
     $ac_message_1= ' updated personal information : ';

     $description['ac_message'] = array(
      'ac_red_1'=>array(
          'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
          'title'=>$_SESSION['admin']['display_id'],
      ),
      'ac_message_1' =>$ac_message_1 .'<br>',
    );
      
    }else{
      $ac_message_1 = ' updated following information Admin '.$tmp_res['fname'].' '.$tmp_res['lname'];

      $description['ac_message'] = array(
        'ac_red_1'=>array(
            'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
            'title'=>$_SESSION['admin']['display_id'],
        ),
        'ac_message_1' =>$ac_message_1 .'(',
        'ac_red_2'=>array(
            'href'=> $ADMIN_HOST.'/admin_profile.php?id='.$id,
            'title'=> $tmp_res['display_id'],
        ),
        'ac_message_2' =>')<br>',
      );
    }
    $flg = "false";
    if(!empty($updateData)){
        foreach($updateData as $key2 => $val){
            if(array_key_exists($key2,$new_update_details)){
                    if(in_array($val,array('Y','N'))){
                        $val = $val == 'Y' ? "selected" : "unselected";
                    }
                    $tmp_key2 = str_replace('_',' ',$key2);
                    if(in_array($key2,array('is_2fa'))){
                        $tmp_key2 = "Two-Factor Authentication (2FA)";
                    }
                    if(in_array($key2,array('is_ip_restriction'))){
                        $tmp_key2 = "IP Address Restriction";
                    }
                    $description['key_value']['desc_arr'][$tmp_key2] = ' Updated From '.$val." To ".$new_update_details[$key2].".<br>";
                    $flg = "true";
            }else{
                $description['description2'][] = ucwords(str_replace('_',' ',$val));
                $flg = "true";
            }
        }
    }
    if($password !=''){
        $description['description_password'] = 'Password updated.';
        $flg = "true";
    }

    if($flg == "true"){
      $desc=json_encode($description);
      activity_feed(3, $_SESSION['admin']['id'], 'Admin', $tmp_res['id'], 'Admin', 'Account Update','','',$desc);
    }
  /* End Code for audit log*/
    
    
    $res['status'] = "success";
    $res['msg'] = "Profile updated Successfully";
  } else {
    $res['status'] = "fail";
    $res['errors'] = $validate->getErrors();
  }
} else {
  $res['status'] = "invalid";
  $res['msg'] = "Record not found";
}

header('Content-Type: application/json');
echo json_encode($res);
dbConnectionClose();
exit;
?>