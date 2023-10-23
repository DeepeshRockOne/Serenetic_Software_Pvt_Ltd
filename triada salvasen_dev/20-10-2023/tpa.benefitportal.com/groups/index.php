<?php
include_once __DIR__ . '/includes/connect.php';


$previous_page = isset($_REQUEST['previous_page']) ? $_REQUEST['previous_page'] : '';
$REAL_IP_ADDRESS = get_real_ipaddress();
if (isset($_SESSION['groups']['id'])) {

  if(empty($_SESSION['groups']['user_name'])){
    $_SESSION['groups']['user_name'] = getname('customer',$_SESSION['groups']['id'],'user_name');
  }
  if(!empty($previous_page)){
    redirect($previous_page);
  }else{
    redirect($GROUP_HOST.'/dashboard.php');
  }
}
$show_popup = "n";
if(isset($_GET['error']) && $_GET['error'] == 'pwd_set'){
  setNotifyError("Password is already set");
}

if(isset($_POST['submit']) && $_POST['submit'] == 'resend_otp' ){
  $email = trim($_POST['email']);
  $r_password = $_POST['r_password'];

  $sel_sql="SELECT c.id,c.fname,c.lname,c.email,cs.is_2fa,cs.send_otp_via,cs.via_email,cs.via_sms,c.cell_phone
            FROM customer c 
            LEFT JOIN customer_settings cs ON ( cs.customer_id=c.id )
            WHERE (c.email=:email OR c.display_id=:email) AND c.status='Active' AND c.type='Group' AND AES_DECRYPT(c.password,'" . $CREDIT_CARD_ENC_KEY . "')=:u_password";
  $params = array(":email" => makeSafe($email), ":u_password" => $r_password);
  $groupRow = $pdo->selectOne($sel_sql, $params);
  if(empty($groupRow)){
    $selSql1="SELECT id,fname,lname,email,group_id as id,is_2fa,send_otp_via,via_sms,via_email,'' as cell_phone 
      FROM sub_group WHERE (email=:email) AND status='Active'";
    $params1 = array(":email" => makeSafe($email));
    $groupRow = $pdo->selectOne($selSql1, $params1);
  }
  if(!empty($groupRow)){
    $globalSett = get_global_user_setting('Group',array('is_2fa'));
    $otp_via='';
    $send_via='';
    if($groupRow['is_2fa'] == 'Y'){
      $otp_via=$groupRow['send_otp_via'];
      $send_via=!empty($groupRow['via_email']) ? $groupRow['via_email'] : $groupRow['email'];
      if($otp_via == 'sms'){
        $send_via = !empty($groupRow['via_sms']) ? $groupRow['via_sms'] : $groupRow['cell_phone'];
      }
    }elseif (checkIsset($globalSett['is_2fa']) == 'Y' ) {
      $otp_via='email';
      $send_via = $groupRow['email'];
    }
    if(isset($_SESSION['group_otp']['group_id']) && $_SESSION['group_otp']['group_id'] == $groupRow['id'] ){
      $email_otp=$_SESSION['group_otp']['otp'];
    }else{
      $email_otp= generateOTP();
    }
    $trigger_id = getname('triggers','T902','id','display_id');
    $params = array();
    $params['fname'] = $groupRow['fname'];
    $params['Code'] = $email_otp;
    $smart_tags = get_user_smart_tags($groupRow['id'],'group');
    if($smart_tags){
      $params = array_merge($params,$smart_tags);
    }
    if($otp_via == 'sms'){
      trigger_sms($trigger_id,$send_via,$params);
    }else{
      trigger_mail($trigger_id,$params,$send_via);
    }
    $_SESSION['group_otp']=array(
      'group_id'=>$groupRow['id'],
      'otp'=>$email_otp,
      'otp_via'=>$otp_via,
      'send_to'=>$send_via
    );
    $response['status']="success1";
  }else{
    $response['status'] = "fail";
    setNotifySuccess('Please try again!');
  }
  echo json_encode($response);
  exit();
}

$validate = new Validation();

if (isset($_POST['submit']) && $_POST['submit'] == 'login') {

  $verify_otp= isset($_POST['verify_otp']) ? $_POST['verify_otp'] : '';
  $otp = isset($_POST['otp']) ? $_POST['otp'] : '';
  $email = trim($_POST['email']);
  $r_password = $_POST['r_password'];

  $validate->string(array('required' => true, 'field' => 'email', 'value' => $email), array('required' => 'Email is required'));
  $validate->string(array('required' => true, 'field' => 'r_password', 'value' => $r_password), array('required' => 'Password is required'));

  if ($validate->getError('email')) {
    $validate->setError('email', 'Email ID you entered is not valid . Please re-enter.');
  } elseif ($validate->getError('r_password')) {
    $validate->setError('r_password', 'The Password you entered is incomplete. Please re-enter.');
  }

  if(!$validate->getError('email')){
  $group_status = $pdo->selectOne("SELECT id,status,upline_sponsors FROM customer WHERE (email=:email OR rep_id=:email) AND is_deleted='N'",array(':email' => $email));
    if(isset($group_status['status']) && $group_status['status'] == 'Suspended'){
        $validate->setError('r_password', 'Your account is Suspended');
    }
  }

  if ($validate->isValid()){
    if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
        $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
    } else {
      $_SERVER['REMOTE_ADDR'] = !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'];
    }
    $group_ip_address = $_SERVER['REMOTE_ADDR'];

    $selSqlPopup = "SELECT status FROM customer WHERE (email=:email OR rep_id=:email) AND type='Group' AND is_deleted='N'";
    $paramsPopup = array(":email" => makeSafe($email));
    $custRowPopup = $pdo->selectOne($selSqlPopup, $paramsPopup);
    $cust_status = '';
    $display_popup="true";

    if($verify_otp=='yes'){
      $validate->string(array('required' => true, 'field' => 'otp', 'value' => $otp),array('required' => 'Enter 6 - Digit Code'));
    }

    $selSql = "SELECT c.id,c.fname,c.lname,c.user_name,c.email,c.type,c.cell_phone,c.rep_id,c.display_id,c.sponsor_id,c.upline_sponsors,c.status,c.created_at,c.company_id, AES_DECRYPT(c.password,'" . $CREDIT_CARD_ENC_KEY . "') as stored_password,c.access_type,c.feature_access,business_name,cs.tpa_for_billing,cs.is_2fa,cs.via_sms,cs.via_email,cs.send_otp_via,cs.is_ip_restriction,cs.allowed_ip
            FROM customer c
            JOIN customer_settings cs ON (c.id=cs.customer_id)
            WHERE (c.email=:email OR c.rep_id=:email) AND AES_DECRYPT(c.password,'" . $CREDIT_CARD_ENC_KEY . "')=:u_password AND c.type IN ('Group') AND c.status in('Active','Pending Contract','Pending Documentation','Pending Approval','Contracted','Suspended') AND c.is_deleted='N'";
    $params = array(":email" => makeSafe($email),":u_password" => $r_password);
    $custRow = $pdo->selectOne($selSql, $params);

    if (count($custRow) == 0) {

      $selSql1 = "SELECT id,fname,lname,email,group_id,status,created_at, AES_DECRYPT(password,'" . $CREDIT_CARD_ENC_KEY . "') as stored_password ,feature_access,access_type,is_2fa,send_otp_via,via_sms,via_email,allowed_ip,is_ip_restriction
            FROM sub_group WHERE (email=:email) AND status='Active'";
      $params1 = array(":email" => makeSafe($email));
      $custRow1 = $pdo->selectOne($selSql1, $params1);
      if (count($custRow1) > 0) {

        if (empty($custRow1['stored_password'])) {
          
        } else if ($r_password == $custRow1['stored_password']) {

          $parent_id = $custRow1['group_id'];
          $selSql2 = "SELECT c.id,c.fname,c.lname,c.user_name,c.email,c.type,c.cell_phone,c.rep_id,c.display_id,c.sponsor_id,c.status,c.created_at,c.company_id, AES_DECRYPT(c.password,'" . $CREDIT_CARD_ENC_KEY . "') as stored_password,cs.tpa_for_billing,cs.is_2fa,cs.send_otp_via,cs.via_sms,cs.via_email,cs.is_ip_restriction,cs.allowed_ip
                        FROM customer c
                        JOIN customer_settings cs ON (c.id=cs.customer_id)
                        WHERE c.id=:id AND c.type IN ('Group') AND c.status in ('Active','Pending Contract','Pending Documentation','Pending Approval','Contracted','Suspended') AND c.is_deleted='N'";
          $params2 = array(":id" => makeSafe($parent_id));
          $custRow2 = $pdo->selectOne($selSql2, $params2);

          if($custRow1['is_ip_restriction'] == 'Y'){
            $allowed_ip=(!empty($custRow1['allowed_ip']) ? explode(",",$custRow1['allowed_ip']) : array());
            if(!in_array($group_ip_address,$allowed_ip)){
              $validate->setError("general", "Login for this group is not allowed from this device, please contact admin.");
            }
          }
          if($validate->isValid()){
            if($verify_otp == "yes"){
              if(isset($_SESSION['group_otp']) && $_SESSION['group_otp']['group_id'] == $custRow2['id'] && $_SESSION['group_otp']['otp'] == $otp){
              }else{
                $validate->setError("otp", "Code you entered is not valid. Please re-enter."); 
              }
            }else{
              $otp_via = '';
              $send_via = '';
              if($custRow1['is_2fa'] == "Y"){
                $otp_via = $custRow1['send_otp_via'];
                $send_via = !empty($custRow1['via_email']) ? $custRow1['via_email'] : $custRow1['email'];
                if($otp_via == 'sms'){
                  $send_via = !empty($custRow1['via_sms']) ? $custRow1['via_sms'] : $custRow1['cell_phone'];
                }
              }else if(checkIsset($globalSett['is_2fa']) == "Y"){
                $otp_via = 'email';
                $send_via = $custRow1['email'];
              }
              if($custRow1['is_2fa'] == "Y" || checkIsset($globalSett['is_2fa']) == "Y"){
                $email_otp = generateOTP();
                $trigger_id = getname('triggers','T902','id','display_id');
                $params = array();
                $params['fname'] = $custRow1["fname"];
                $params['Code'] = $email_otp;
                $smart_tags = get_user_smart_tags($custRow1['id'],'group');
                if($smart_tags){
                  $params = array_merge($params,$smart_tags);
                }
                if($otp_via == 'sms'){
                  trigger_sms($trigger_id, $send_via, $params);
                }else{
                  trigger_mail($trigger_id, $params, $send_via);
                }
                $_SESSION['group_otp'] = array(
                  "group_id"=> $custRow2['id'],
                  "otp"=> $email_otp,
                  "otp_via"=>$otp_via,
                  "send_to"=>$send_via,
                );
                $response['otp_via'] = $otp_via;
                $response['status'] = "otp_send";
                echo json_encode($response);
                exit();
                /*-----/ OTP send Code /------*/
              }
            }
          }
          if($validate->isValid()){
            $_SESSION['groups'] = $custRow2;
            $_SESSION['groups']['custom_email'] = $custRow2['user_name'] . '@chatbot.com';

            $_SESSION['groups']['is_sub_group'] = "Y";
            $_SESSION['groups']['sub_group_id'] = $custRow1['id'];
            $_SESSION['groups']['access'] = explode(",",$custRow1['feature_access']);
            $_SESSION['groups']['access_type'] = $custRow1['access_type'];
            $_SESSION['groups']['rep_id'] = $custRow2['rep_id'];
            $_SESSION['groups']['display_id'] = $custRow2['display_id'];
            $_SESSION['groups']['timezone'] = $_REQUEST['timezone'];

            $cs_sql = "SELECT CONCAT(c.fname,' ',c.lname) as name,c.rep_id,c.email,c.cell_phone,cs.display_in_member,c.public_name,c.public_phone,c.public_email,cs.is_branding,cs.brand_icon,cs.tpa_for_billing
                          FROM customer c 
                          JOIN customer_settings cs ON(cs.customer_id = c.id)
                          WHERE c.id=:id";
            $cs_where = array(":id" => $_SESSION['groups']['id']);
            $cs_row = $pdo->selectOne($cs_sql, $cs_where);
            if(!empty($cs_row)) {
                $_SESSION['groups']['public_name'] = $cs_row['public_name'];
                $_SESSION['groups']['public_phone'] = $cs_row['public_phone'];
                $_SESSION['groups']['public_email'] = $cs_row['public_email'];
                $_SESSION['groups']['display_in_member'] = $cs_row['display_in_member'];
                $_SESSION['groups']['is_branding'] = $cs_row['is_branding'];
                $_SESSION['groups']['brand_icon'] = $cs_row['brand_icon'];
            }

            /*--- parent agent public data ---*/
            $spon_sql = "SELECT CONCAT(c.fname,' ',c.lname) as name,c.rep_id,c.email,c.cell_phone,cs.display_in_member,c.public_name,c.public_phone,c.public_email,cs.is_branding,cs.brand_icon 
                      FROM customer c 
                      JOIN customer_settings cs ON(cs.customer_id = c.id)
                      WHERE c.id=:id";
            $spon_where = array(":id" => $_SESSION['groups']['sponsor_id']);
            $spon_row = $pdo->selectOne($spon_sql, $spon_where);
            if(!empty($spon_row)) {
                $_SESSION['groups']['sponsor_name'] = $spon_row['name'];
                $_SESSION['groups']['sponsor_rep_id'] = $spon_row['rep_id'];
                $_SESSION['groups']['sponsor_email'] = $spon_row['email'];
                $_SESSION['groups']['sponsor_cell_phone'] = $spon_row['cell_phone'];
                $_SESSION['groups']['sponsor_public_name'] = $spon_row['public_name'];
                $_SESSION['groups']['sponsor_public_phone'] = $spon_row['public_phone'];
                $_SESSION['groups']['sponsor_public_email'] = $spon_row['public_email'];
                $_SESSION['groups']['sponsor_display_in_member'] = $spon_row['display_in_member'];
                $_SESSION['groups']['sponsor_is_branding'] = $spon_row['is_branding'];
                $_SESSION['groups']['sponsor_brand_icon'] = $spon_row['brand_icon'];
                $_SESSION['groups']['group_services_cell_phone'] = get_app_settings('group_services_cell_phone');
                $_SESSION['groups']['group_services_email'] = get_app_settings('group_services_email');
                $_SESSION['groups']['enrollment_display_name'] = get_app_settings('enrollment_display_name');
            } else {
                $_SESSION['groups']['sponsor_display_in_member'] = 'Y';
                $_SESSION['groups']['group_services_cell_phone'] = get_app_settings('group_services_cell_phone');
                $_SESSION['groups']['group_services_email'] = get_app_settings('group_services_email');
                $_SESSION['groups']['enrollment_display_name'] = get_app_settings('enrollment_display_name');
            }
            /*---/parent agent public data ---*/

            $user_data = get_user_data($_SESSION['groups']);
            $audit_log_id = audit_log($user_data, $_SESSION['groups']['sub_group_id'], "Sub Group", "Log In", '', '', 'login');
            $_SESSION['groups']['audit_log_id'] = $audit_log_id;

            $description['ac_message'] =array(
              'ac_red_1'=>array(
                'href'=>$ADMIN_HOST.'/groups_details.php?id='.md5($_SESSION['groups']['id']),
                'title'=>$_SESSION['groups']['rep_id'],
              ),
              'ac_message_1' =>' Logged into account.',
              );
            $desc = json_encode($description);
            activity_feed(3, $_SESSION['groups']['id'], 'Group', $_SESSION['groups']['id'], 'customer', 'Logged Group Account', $_SESSION['groups']['fname'], $_SESSION['groups']['lname'], $desc);
            if(!empty($previous_page)){
              // redirect($previous_page);
              $response['redirect_url'] = $previous_page;
              $response['status'] = "previous_page";
            }else{
              if (isset($_SESSION['unsubscribe_email'])) {
                // redirect($GROUP_HOST.'/unsubscribe_email.php?email=' . $_SESSION['unsubscribe_email']);
                $response['status'] = "unsubscribe_email";
                $response['unsubscribe_email'] = $_SESSION['unsubscribe_email'];
              } else {
                // redirect($GROUP_HOST.'/dashboard.php');
                  $response['status'] = "login_success";
              }
            }
          }
        } else {
          $validate->setError("r_password", "Email ID or the password you entered is not valid. Please re-enter.");
        }
      } else {
        $validate->setError("r_password", "Email ID or the password you entered is not valid. Please re-enter.");
      }
    } else {

      if (count($custRow) > 0) {

        if($custRow['is_ip_restriction'] == 'Y' || checkIsset($globalSett['is_ip_restriction'])=='Y'){
          $allowed_ip=(!empty($custRow['allowed_ip']) ? explode(",",$custRow['allowed_ip']) : array());
          $globalIP = (!empty($globalSett['allowed_ip']) ? explode(",",$custRow['allowed_ip']) :array());
          $allowed_ip=array_merge($allowed_ip,$globalIP);
          if(!in_array($group_ip_address,$allowed_ip)){
              $validate->setError("general", "Login for this group is not allowed from this device, please contact admin.");
          }
        }
        if($validate->isValid()){
          if($verify_otp == "yes"){
            if(isset($_SESSION['group_otp']) && $_SESSION['group_otp']['group_id'] == $custRow['id'] && $_SESSION['group_otp']['otp'] == $otp ){
            }else{
              $validate->setError("otp", "Code you entered is not valid. Please re-enter.");
            }
          }else{
            $otp_via = '';
            $send_via = '';
            if($custRow['is_2fa'] == "Y"){
              $otp_via = $custRow['send_otp_via'];
              $send_via = !empty($custRow['via_email']) ? $custRow['via_email'] : $custRow['email'];
              if($otp_via == 'sms'){
                $send_via = !empty($custRow['via_sms']) ? $custRow['via_sms'] : $custRow['cell_phone'];
              }
            }else if(checkIsset($globalSett['is_2fa']) == "Y"){
              $otp_via = 'email';
              $send_via = $custRow['email'];
            }
            if($custRow['is_2fa'] == "Y" || checkIsset($globalSett['is_2fa']) == "Y"){
              $email_otp = generateOTP();
              $trigger_id = getname('triggers','T902','id','display_id');
              $params = array();
              $params['fname'] = $custRow["fname"];
              $params['Code'] = $email_otp;
              $smart_tags = get_user_smart_tags($custRow['id'],'group');
              if($smart_tags){
                $params = array_merge($params,$smart_tags);
              }
              if($otp_via == 'sms'){
                trigger_sms($trigger_id, $send_via, $params);
              }else{
                trigger_mail($trigger_id, $params, $send_via);
              }
              $_SESSION['group_otp'] = array(
                "group_id"=> $custRow['id'],
                "otp"=> $email_otp,
                "otp_via"=>$otp_via,
                "send_to"=>$send_via,
              );
              $response['otp_via'] = $otp_via;
              $response['status'] = "otp_send";
              echo json_encode($response);
              exit();
            }
          }
        }
        if($validate->isValid()){
          if (empty($custRow['stored_password'])) {
            
          } else if ($r_password == $custRow['stored_password']) {
            
            $_SESSION['groups'] = $custRow;
            $_SESSION['groups']['custom_email'] = $custRow['user_name'] . '@chatbot.com';

            $_SESSION['groups']['is_sub_group'] = "N";

            $_SESSION['groups']['access'] = $custRow['feature_access'] != "" ? explode(",",$custRow['feature_access']) : array();
            $_SESSION['groups']['access_type'] = $custRow['access_type'];
            $_SESSION['groups']['rep_id'] = $custRow['rep_id'];
            $_SESSION['groups']['display_id'] = $custRow['display_id'];
            $_SESSION['groups']['timezone'] = $_REQUEST['timezone'];

            $cs_sql = "SELECT CONCAT(c.fname,' ',c.lname) as name,c.rep_id,c.email,c.cell_phone,cs.display_in_member,c.public_name,c.public_phone,c.public_email,cs.is_branding,cs.brand_icon 
                          FROM customer c 
                          JOIN customer_settings cs ON(cs.customer_id = c.id)
                          WHERE c.id=:id";
            $cs_where = array(":id" => $_SESSION['groups']['id']);
            $cs_row = $pdo->selectOne($cs_sql, $cs_where);
            if(!empty($cs_row)) {
                $_SESSION['groups']['public_name'] = $cs_row['public_name'];
                $_SESSION['groups']['public_phone'] = $cs_row['public_phone'];
                $_SESSION['groups']['public_email'] = $cs_row['public_email'];
                $_SESSION['groups']['display_in_member'] = $cs_row['display_in_member'];
                $_SESSION['groups']['is_branding'] = $cs_row['is_branding'];
                $_SESSION['groups']['brand_icon'] = $cs_row['brand_icon'];
            }
            
            /*--- parent agent public data ---*/
            $spon_sql = "SELECT CONCAT(c.fname,' ',c.lname) as name,c.rep_id,c.email,c.cell_phone,cs.display_in_member,c.public_name,c.public_phone,c.public_email,cs.is_branding,cs.brand_icon 
                      FROM customer c 
                      JOIN customer_settings cs ON(cs.customer_id = c.id)
                      WHERE c.id=:id";
            $spon_where = array(":id" => $_SESSION['groups']['sponsor_id']);
            $spon_row = $pdo->selectOne($spon_sql, $spon_where);
            if(!empty($spon_row)) {
                $_SESSION['groups']['sponsor_name'] = $spon_row['name'];
                $_SESSION['groups']['sponsor_rep_id'] = $spon_row['rep_id'];
                $_SESSION['groups']['sponsor_email'] = $spon_row['email'];
                $_SESSION['groups']['sponsor_cell_phone'] = $spon_row['cell_phone'];
                $_SESSION['groups']['sponsor_public_name'] = $spon_row['public_name'];
                $_SESSION['groups']['sponsor_public_phone'] = $spon_row['public_phone'];
                $_SESSION['groups']['sponsor_public_email'] = $spon_row['public_email'];
                $_SESSION['groups']['sponsor_display_in_member'] = $spon_row['display_in_member'];
                $_SESSION['groups']['sponsor_is_branding'] = $spon_row['is_branding'];
                $_SESSION['groups']['sponsor_brand_icon'] = $spon_row['brand_icon'];
                $_SESSION['groups']['group_services_cell_phone'] = get_app_settings('group_services_cell_phone');
                $_SESSION['groups']['group_services_email'] = get_app_settings('group_services_email');
                $_SESSION['groups']['enrollment_display_name'] = get_app_settings('enrollment_display_name');
            } else {
                $_SESSION['groups']['sponsor_display_in_member'] = 'Y';
                $_SESSION['groups']['group_services_cell_phone'] = get_app_settings('group_services_cell_phone');
                $_SESSION['groups']['group_services_email'] = get_app_settings('group_services_email');
                $_SESSION['groups']['enrollment_display_name'] = get_app_settings('enrollment_display_name');
            }
            /*---/parent agent public data ---*/
            $real_ip_address = !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'];
            $updateStr = array("last_login" => 'msqlfunc_NOW()','last_login_ip'=>$real_ip_address, 'is_login' => 'Y', 'last_login_track' => 'msqlfunc_NOW()');
            $where = array("clause" => 'id=:id', 'params' => array(':id' => $_SESSION['groups']['id']));
            $pdo->update("customer_settings", $updateStr, $where);

            $user_data = get_user_data($_SESSION['groups']);
            $audit_log_id = audit_log($user_data, $_SESSION['groups']['id'], "Group", "Log In", '', '', 'login');
            $_SESSION['groups']['audit_log_id'] = $audit_log_id;

            $description['ac_message'] =array(
              'ac_red_1'=>array(
                'href'=>$ADMIN_HOST.'/groups_details.php?id='.md5($_SESSION['groups']['id']),
                'title'=>$_SESSION['groups']['rep_id'],
              ),
              'ac_message_1' =>' Logged into account.',
              );
            $desc = json_encode($description);
            activity_feed(3, $_SESSION['groups']['id'], 'Group', $_SESSION['groups']['id'], 'customer', 'Logged Group Account', $_SESSION['groups']['fname'], $_SESSION['groups']['lname'], $desc);
            if(!empty($previous_page)){
              // redirect($previous_page);
              $response['redirect_url'] = $previous_page;
              $response['status'] = "previous_page";
            }else{
              if (isset($_SESSION['unsubscribe_email'])) {
                $response['status'] = "unsubscribe_email";
                $response['unsubscribe_email'] = $_SESSION['unsubscribe_email'];
              } else {
                // redirect($GROUP_HOST.'/dashboard.php');
                $response['status'] = "login_success";
              }
            }
          } else {
            $validate->setError("r_password", "Email ID or the password you entered is not valid. Please re-enter.");
          }
        }
      } else {
        $validate->setError("r_password", "Email ID or the password you entered is not valid. Please re-enter.");
      }
    }

    if(!empty($custRowPopup['status']) && (in_array($custRowPopup['status'],array('Terminated'))) && $display_popup=="true")
    {
       $cust_status = $custRowPopup['status'];
       $setting_keys = array(
        'group_services_cell_phone',
        'group_services_email',
        'enrollment_display_name',
      );
      $app_setting_res = get_app_settings($setting_keys);
      $service = 'Group Services';
      $cell_phone = '';
      $service_email = '';
      $display_name = '';
      if(!empty($app_setting_res)){
        $cell_phone = format_telephone($app_setting_res['group_services_cell_phone']);
        $service_email = $app_setting_res['group_services_email'];
        $display_name = $app_setting_res['enrollment_display_name'];
      }
    }
  }
  if(count($validate->getErrors()) > 0){
      $response['status'] = "error";
      $response['errors'] = $validate->getErrors();
  }
  echo json_encode($response);
  exit();
}
$exStylesheets = array('thirdparty/colorbox/colorbox.css');
$exJs = array('thirdparty/colorbox/jquery.colorbox.js','thirdparty/bower_components/moment/moment.js','thirdparty/bower_components/moment/moment-timezone-with-data.min.js');
$errors = $validate->getErrors();
$template = 'index.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>
