<?php

include_once __DIR__ . '/includes/connect.php';

$previous_page = isset($_REQUEST['previous_page']) ? $_REQUEST['previous_page'] : '';
$REAL_IP_ADDRESS = get_real_ipaddress();
if (isset($_SESSION['agents']['id'])) {
  if(!empty($previous_page)){
    redirect($previous_page);
  }else{
    redirect($AGENT_HOST.'/dashboard.php');
  }
}

$show_popup = "n";
if(isset($_GET['error']) && $_GET['error'] == 'pwd_set'){
  setNotifyError("Password is already set");
}
$response = array();
$validate = new Validation();
$rep_id = (isset($_GET['t'])?$_GET['t']:'');

if (isset($_POST['submit']) && $_POST['submit'] == 'resend_otp') {
  $rep_id = trim($_POST['rep_id']);
  $selSql = "SELECT c.id,c.fname,c.lname,c.user_name,c.email,c.type,c.cell_phone,cs.is_2fa,cs.send_otp_via,cs.via_email,cs.via_sms
            FROM customer c
            JOIN customer_settings cs ON(cs.customer_id=c.id)
            WHERE c.rep_id=:rep_id";
  $params = array(":rep_id" => makeSafe($rep_id));
  $custRow = $pdo->selectOne($selSql, $params);

  if(empty($custRow)) {
    $selSql1 = "SELECT id,displayDirectEnroll,additionalAccess,fname,lname,email,agent_id as id,is_2fa,send_otp_via,via_email,via_sms,'' as cell_phone
          FROM sub_agent WHERE account_manager_id=:rep_id AND status='Active' AND is_deleted='N'";
    $params1 = array(":rep_id" => makeSafe($rep_id));
    $custRow = $pdo->selectOne($selSql1, $params1);

  }
  if(!empty($custRow)) {
    /*-- OTP Send Code ---*/
    if(isset($_SESSION['agent_otp']['agent_id']) && $_SESSION['agent_otp']['agent_id'] == $custRow['id']) {
      $email_otp = $_SESSION['agent_otp']['otp'];
    } else {
      $email_otp = generateOTP();
    }

    $otp_via = '';
    $send_via = '';
    if($custRow['is_2fa'] == "Y"){
      $otp_via = $custRow['send_otp_via'];
      $send_via = !empty($custRow['via_email']) ? $custRow['via_email'] : $custRow['email'];
      if($otp_via == 'sms'){
        $send_via = !empty($custRow['via_sms']) ? $custRow['via_sms'] : $custRow['cell_phone'];
      }
    }

    $trigger_id = getname('triggers','T616','id','display_id');
    $params = array();
    $params['fname'] = $custRow["fname"];
    $params['Code'] = $email_otp;
    $smart_tags = get_user_smart_tags($custRow['id'],'agent');
    if($smart_tags){
        $params = array_merge($params,$smart_tags);
    }
    if($otp_via == 'sms'){
      trigger_sms($trigger_id, $send_via, $params);
    }else{
      trigger_mail($trigger_id, $params, $send_via);
    }
    $_SESSION['agent_otp'] = array(
      "agent_id"=> $custRow['id'],
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

if (isset($_POST['submit']) && $_POST['submit'] == 'login') {
  $rep_id = trim($_POST['rep_id']);
  $r_password = $_POST['r_password'];
  $verify_otp = $_POST['verify_otp'];
  $otp = $_POST['otp'];

  $validate->string(array('required' => true, 'field' => 'rep_id', 'value' => $rep_id), array('required' => 'Agent ID is required'));
  $validate->string(array('required' => true, 'field' => 'r_password', 'value' => $r_password), array('required' => 'Password is required'));
  if($verify_otp == "yes") {
    $validate->string(array('required' => true, 'field' => 'otp', 'value' => $otp), array('required' => 'Enter 6 - Digit Code'));
  }
  if(!$validate->getError('rep_id')){
    $agent_status = $pdo->selectOne("SELECT id,status,upline_sponsors FROM customer WHERE rep_id=:rep_id AND is_deleted='N'",array(':rep_id' => $rep_id));
    if(isset($agent_status['status']) && $agent_status['status'] == 'Suspended'){
      $allow_login = $pdo->selectOne("SELECT allow_login_to_suspended FROM agent_settings");
      if(isset($allow_login['allow_login_to_suspended']) && $allow_login['allow_login_to_suspended'] == 'N'){
          $validate->setError('general', 'Your account is Suspended');
      }
    }
  }

  if ($validate->isValid()) 
  {
    if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
        $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
    } else {
      $_SERVER['REMOTE_ADDR'] = !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'];
    }
    $agent_ip_address = $_SERVER['REMOTE_ADDR'];

    $selSqlPopup = "SELECT status FROM customer WHERE rep_id=:rep_id AND is_deleted='N'";
    $paramsPopup = array(":rep_id" => makeSafe($rep_id));
    $custRowPopup = $pdo->selectOne($selSqlPopup, $paramsPopup);
    $cust_status = '';
    $display_popup="true";

    $selSql = "SELECT c.id,c.fname,c.lname,c.user_name,c.email,c.type,c.cell_phone,cs.agent_coded_level,cs.agent_coded_id,c.rep_id,c.display_id,c.sponsor_id,c.upline_sponsors,c.status,c.created_at,c.company_id, AES_DECRYPT(c.password,'" . $CREDIT_CARD_ENC_KEY . "') as stored_password,c.access_type,c.feature_access,business_name,cs.not_show_license_expired,cs.not_show_license_expiring,
    cs.not_show_eo_expired,cs.not_show_eo_expiring,cs.is_2fa,cs.is_ip_restriction,cs.allowed_ip,cs.displayDirectEnroll,cs.additionalAccess,is_2fa,send_otp_via,via_email,via_sms
            FROM customer c
            JOIN customer_settings cs ON (c.id=cs.customer_id)
            WHERE c.rep_id=:rep_id AND c.type IN ('Agent') AND c.status in('Active','Pending Contract','Pending Documentation','Pending Approval','Contracted','Suspended') AND c.is_deleted='N'";
    $params = array(":rep_id" => makeSafe($rep_id));
    $custRow = $pdo->selectOne($selSql, $params);
    if (count($custRow) == 0) {
      //Checking For Sub Agent(Account Managers)
      $selSql1 = "SELECT id,displayDirectEnroll,additionalAccess,fname,lname,passcode,email,agent_id,status,created_at, AES_DECRYPT(password,'" . $CREDIT_CARD_ENC_KEY . "') as stored_password ,feature_access,access_type,is_2fa,send_otp_via,via_email,via_sms,allowed_ip,is_ip_restriction
            FROM sub_agent WHERE account_manager_id=:rep_id AND status='Active' AND is_deleted='N'";
      $params1 = array(":rep_id" => makeSafe($rep_id));
      $custRow1 = $pdo->selectOne($selSql1, $params1);

      if (count($custRow1) > 0) {

        if (empty($custRow1['stored_password'])) {
          
        } else if ($r_password == $custRow1['stored_password']) {

          $parent_id = $custRow1['agent_id'];
          $selSql2 = "SELECT c.id,c.fname,c.lname,c.user_name,c.email,c.type,c.cell_phone,cs.agent_coded_level,cs.agent_coded_id,c.rep_id,c.display_id,c.sponsor_id,c.upline_sponsors,c.status,c.created_at,c.company_id, AES_DECRYPT(c.password,'" . $CREDIT_CARD_ENC_KEY . "') as stored_password,c.access_type,c.feature_access,business_name,cs.not_show_license_expired,cs.not_show_license_expiring,
        cs.not_show_eo_expired,cs.not_show_eo_expiring,cs.is_2fa,cs.is_ip_restriction,cs.allowed_ip,cs.displayDirectEnroll,cs.additionalAccess,is_2fa,send_otp_via,via_email,via_sms
                FROM customer c
                JOIN customer_settings cs ON (c.id=cs.customer_id)
                WHERE c.id=:id AND c.type IN ('Agent') AND c.status in('Active','Pending Contract','Pending Documentation','Pending Approval','Contracted','Suspended') AND c.is_deleted='N'";
          $params2 = array(":id" => makeSafe($parent_id));
          $custRow2 = $pdo->selectOne($selSql2, $params2);

          //Sub Agent(Account Managers) is_ip_restriction
          if($custRow1['is_ip_restriction'] == "Y") {
            $allowed_ip = (!empty($custRow1["allowed_ip"]) ? explode(",", $custRow1["allowed_ip"]) : array());
            if(!in_array($agent_ip_address,$allowed_ip)) {
              $validate->setError("general", "Login for this agent is not allowed from this device, please contact admin.");
            }
          }

          if ($validate->isValid()) {
            if($verify_otp == "yes") {
              if(isset($_SESSION['agent_otp']) && $_SESSION['agent_otp']['agent_id'] == $custRow2['id'] && $_SESSION['agent_otp']['otp'] == $otp) {
              } else {
                $validate->setError("otp", "Code you entered is not valid. Please re-enter."); 
              }
            } else {
              $otp_via = '';
              $send_via = '';
              //Sub Agent(Account Managers) is_2fa
              if($custRow1['is_2fa'] == "Y"){
                $otp_via = $custRow1['send_otp_via'];
                $send_via = !empty($custRow1['via_email']) ? $custRow1['via_email'] : $custRow1['email'];
                if($otp_via == 'sms'){
                  $send_via = !empty($custRow1['via_sms']) ? $custRow1['via_sms'] : $custRow2['cell_phone'];
                }
              }
              if($custRow1['is_2fa'] == "Y") {
                  /*-- OTP Send Code ---*/
                  $email_otp = generateOTP();
                  $trigger_id = getname('triggers','T616','id','display_id');
                  $params = array();
                  $params['fname'] = $custRow1["fname"];
                  $params['Code'] = $email_otp;
                  if($otp_via == 'sms'){
                    trigger_sms($trigger_id, $send_via, $params);
                  }else{
                    trigger_mail($trigger_id, $params, $send_via);
                  }
                  $_SESSION['agent_otp'] = array(
                    "agent_id"=> $custRow2['id'],
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

          if ($validate->isValid()) {
            $_SESSION['agents'] = $custRow2;
            $_SESSION['agents']['custom_email'] = $custRow2['user_name'] . '@chatbot.com';

            $_SESSION['agents']['is_sub_agent'] = "Y";
            $_SESSION['agents']['sub_agent_id'] = $custRow1['id'];
            $_SESSION['agents']['displayDirectEnroll'] = $custRow1['displayDirectEnroll'];
            $_SESSION['agents']['additionalAccess'] = $custRow1['additionalAccess'];
            $_SESSION['agents']['passcode'] = $custRow1['passcode'];
            $_SESSION['agents']['agent_coded_level'] = $custRow2['agent_coded_level'];
            $_SESSION['agents']['feature_access'] = $custRow1['feature_access'];
            $_SESSION['agents']['access'] = $custRow1['feature_access'] != "" ? explode(",",$custRow1['feature_access']) : array();
            $_SESSION['agents']['access_type'] = $custRow1['access_type'];
            $_SESSION['agents']['rep_id'] = $custRow2['rep_id'];
            $_SESSION['agents']['display_id'] = $custRow2['display_id'];
            $_SESSION['agents']['timezone'] = $_REQUEST['timezone'];

            $cs_sql = "SELECT CONCAT(c.fname,' ',c.lname) as name,c.rep_id,c.email,c.cell_phone,cs.display_in_member,c.public_name,c.public_phone,c.public_email,cs.is_branding,cs.brand_icon ,cs.not_show_license_expired,cs.not_show_license_expiring,cs.not_show_eo_expired,cs.not_show_eo_expiring,cs.displayDirectEnroll,cs.additionalAccess
                          FROM customer c 
                          JOIN customer_settings cs ON(cs.customer_id = c.id)
                          WHERE c.id=:id";
            $cs_where = array(":id" => $_SESSION['agents']['id']);
            $cs_row = $pdo->selectOne($cs_sql, $cs_where);
            if(!empty($cs_row)) {
                $_SESSION['agents']['public_name'] = $cs_row['public_name'];
                $_SESSION['agents']['public_phone'] = $cs_row['public_phone'];
                $_SESSION['agents']['public_email'] = $cs_row['public_email'];
                $_SESSION['agents']['display_in_member'] = $cs_row['display_in_member'];
                $_SESSION['agents']['is_branding'] = $cs_row['is_branding'];
                $_SESSION['agents']['brand_icon'] = $cs_row['brand_icon'];
                $_SESSION['agents']['not_show_license_expired'] = $cs_row['not_show_license_expired'];
                $_SESSION['agents']['not_show_license_expiring'] = $cs_row['not_show_license_expiring'];
                $_SESSION['agents']['not_show_eo_expired'] = $cs_row['not_show_eo_expired'];
                $_SESSION['agents']['not_show_eo_expiring'] = $cs_row['not_show_eo_expiring'];
            }

            /*--- parent agent public data ---*/
            $spon_sql = "SELECT CONCAT(c.fname,' ',c.lname) as name,c.rep_id,c.email,c.cell_phone,cs.display_in_member,c.public_name,c.public_phone,c.public_email,cs.is_branding,cs.brand_icon,cs.displayDirectEnroll,cs.additionalAccess 
                      FROM customer c 
                      JOIN customer_settings cs ON(cs.customer_id = c.id)
                      WHERE c.id=:id";
            $spon_where = array(":id" => $_SESSION['agents']['sponsor_id']);
            $spon_row = $pdo->selectOne($spon_sql, $spon_where);
            if(!empty($spon_row)) {
                $_SESSION['agents']['sponsor_name'] = $spon_row['name'];
                $_SESSION['agents']['sponsor_rep_id'] = $spon_row['rep_id'];
                $_SESSION['agents']['sponsor_email'] = $spon_row['email'];
                $_SESSION['agents']['sponsor_cell_phone'] = $spon_row['cell_phone'];
                $_SESSION['agents']['sponsor_public_name'] = $spon_row['public_name'];
                $_SESSION['agents']['sponsor_public_phone'] = $spon_row['public_phone'];
                $_SESSION['agents']['sponsor_public_email'] = $spon_row['public_email'];
                $_SESSION['agents']['sponsor_display_in_member'] = $spon_row['display_in_member'];
                $_SESSION['agents']['sponsor_is_branding'] = $spon_row['is_branding'];
                $_SESSION['agents']['sponsor_brand_icon'] = $spon_row['brand_icon'];
                $_SESSION['agents']['agent_services_cell_phone'] = get_app_settings('agent_services_cell_phone');
                $_SESSION['agents']['agent_services_email'] = get_app_settings('agent_services_email');
            } else {
                $_SESSION['agents']['sponsor_display_in_member'] = 'Y';
                $_SESSION['agents']['agent_services_cell_phone'] = get_app_settings('agent_services_cell_phone');
                $_SESSION['agents']['agent_services_email'] = get_app_settings('agent_services_email');
            }
            /*---/parent agent public data ---*/

            $user_data = get_user_data($_SESSION['agents']);
            $audit_log_id = audit_log($user_data, $_SESSION['agents']['sub_agent_id'], "Sub Agent", "Log In", '', '', 'login');
            $_SESSION['agents']['audit_log_id'] = $audit_log_id;

            $description['ac_message'] =array(
              'ac_red_1'=>array(
                  'href' => 'agent_detail_v1.php?id='.md5($_SESSION['agents']['id']),
                  'title' => $_SESSION['agents']['rep_id'],
              ),
              'ac_message_1' =>' Logged Account Manager ',
              'ac_red_2'=>array(
                'href'=> 'javascript:void(0);',
                'title'=>$custRow1['fname'].' '.$custRow1['lname'],
              ),
              );
            $desc = json_encode($description);
            activity_feed(3, $_SESSION['agents']['id'], 'Agent', $_SESSION['agents']['id'], 'customer', 'Logged Account Manager', $_SESSION['agents']['fname'], $_SESSION['agents']['lname'], $desc);

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
          $validate->setError("general", "Agent ID or the password you entered is not valid. Please re-enter.");
        }
      } else {
        $validate->setError("general", "Agent ID or the password you entered is not valid. Please re-enter.");
      }
    } else {
      //For Agent
      if (count($custRow) > 0) {
        
        if (empty($custRow['stored_password'])) {
          
        } else if ($r_password == $custRow['stored_password']) {
          if($custRow['is_ip_restriction'] == "Y") {
            $allowed_ip = (!empty($custRow["allowed_ip"]) ? explode(",", $custRow["allowed_ip"]) : array());
            if(!in_array($agent_ip_address,$allowed_ip)) {
              $validate->setError("general", "Login for this agent is not allowed from this device, please contact admin.");
            }
          }

          if ($validate->isValid()){
            if($verify_otp == "yes") {
              if(isset($_SESSION['agent_otp']) && $_SESSION['agent_otp']['agent_id'] == $custRow['id'] && $_SESSION['agent_otp']['otp'] == $otp) {

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
                  $send_via = !empty($custRow['via_sms']) ? $custRow['via_sms'] : $custRow['cell_phone'];
                }
              }
              if($custRow['is_2fa'] == "Y") {
                  /*-- OTP Send Code ---*/
                  $email_otp = generateOTP();
                  $trigger_id = getname('triggers','T616','id','display_id');
                  $params = array();
                  $params['fname'] = $custRow["fname"];
                  $params['Code'] = $email_otp;
                  $smart_tags = get_user_smart_tags($custRow['id'],'agent');
                  if($smart_tags){
                      $params = array_merge($params,$smart_tags);
                  }
                  if($otp_via == 'sms'){
                    trigger_sms($trigger_id, $send_via, $params);
                  }else{
                    trigger_mail($trigger_id, $params, $send_via);
                  }
                  $_SESSION['agent_otp'] = array(
                    "agent_id"=> $custRow['id'],
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
            $_SESSION['agents'] = $custRow;
            $_SESSION['agents']['custom_email'] = $custRow['user_name'] . '@chatbot.com';

            $_SESSION['agents']['is_sub_agent'] = "N";

            $_SESSION['agents']['agent_coded_level'] = $custRow['agent_coded_level'];
            $_SESSION['agents']['access'] = $custRow['feature_access'] != "" ? explode(",",$custRow['feature_access']) : array();
            $_SESSION['agents']['access_type'] = $custRow['access_type'];
            $_SESSION['agents']['rep_id'] = $custRow['rep_id'];
            $_SESSION['agents']['display_id'] = $custRow['display_id'];
            $_SESSION['agents']['timezone'] = $_REQUEST['timezone'];

            $cs_sql = "SELECT CONCAT(c.fname,' ',c.lname) as name,c.rep_id,c.email,c.cell_phone,cs.display_in_member,c.public_name,c.public_phone,c.public_email,cs.is_branding,cs.brand_icon ,cs.not_show_license_expired,cs.not_show_license_expiring,cs.not_show_eo_expired,cs.not_show_eo_expiring,cs.displayDirectEnroll,cs.additionalAccess
                          FROM customer c 
                          JOIN customer_settings cs ON(cs.customer_id = c.id)
                          WHERE c.id=:id";
            $cs_where = array(":id" => $_SESSION['agents']['id']);
            $cs_row = $pdo->selectOne($cs_sql, $cs_where);
            if(!empty($cs_row)) {
                $_SESSION['agents']['public_name'] = $cs_row['public_name'];
                $_SESSION['agents']['public_phone'] = $cs_row['public_phone'];
                $_SESSION['agents']['public_email'] = $cs_row['public_email'];
                $_SESSION['agents']['display_in_member'] = $cs_row['display_in_member'];
                $_SESSION['agents']['is_branding'] = $cs_row['is_branding'];
                $_SESSION['agents']['brand_icon'] = $cs_row['brand_icon'];
                $_SESSION['agents']['not_show_license_expired'] = $cs_row['not_show_license_expired'];
                $_SESSION['agents']['not_show_license_expiring'] = $cs_row['not_show_license_expiring'];
                $_SESSION['agents']['not_show_eo_expired'] = $cs_row['not_show_eo_expired'];
                $_SESSION['agents']['not_show_eo_expiring'] = $cs_row['not_show_eo_expiring'];
            }

            /*--- parent agent public data ---*/
            $spon_sql = "SELECT CONCAT(c.fname,' ',c.lname) as name,c.rep_id,c.email,c.cell_phone,cs.display_in_member,c.public_name,c.public_phone,c.public_email,cs.is_branding,cs.brand_icon,cs.displayDirectEnroll,cs.additionalAccess
                      FROM customer c 
                      JOIN customer_settings cs ON(cs.customer_id = c.id)
                      WHERE c.id=:id";
            $spon_where = array(":id" => $_SESSION['agents']['sponsor_id']);
            $spon_row = $pdo->selectOne($spon_sql, $spon_where);
            if(!empty($spon_row)) {
                $_SESSION['agents']['sponsor_name'] = $spon_row['name'];
                $_SESSION['agents']['sponsor_rep_id'] = $spon_row['rep_id'];
                $_SESSION['agents']['sponsor_email'] = $spon_row['email'];
                $_SESSION['agents']['sponsor_cell_phone'] = $spon_row['cell_phone'];
                $_SESSION['agents']['sponsor_public_name'] = $spon_row['public_name'];
                $_SESSION['agents']['sponsor_public_phone'] = $spon_row['public_phone'];
                $_SESSION['agents']['sponsor_public_email'] = $spon_row['public_email'];
                $_SESSION['agents']['sponsor_display_in_member'] = $spon_row['display_in_member'];
                $_SESSION['agents']['sponsor_is_branding'] = $spon_row['is_branding'];
                $_SESSION['agents']['sponsor_brand_icon'] = $spon_row['brand_icon'];
                $_SESSION['agents']['agent_services_cell_phone'] = get_app_settings('agent_services_cell_phone');
                $_SESSION['agents']['agent_services_email'] = get_app_settings('agent_services_email');
            } else {
                $_SESSION['agents']['sponsor_display_in_member'] = 'Y';
                $_SESSION['agents']['agent_services_cell_phone'] = get_app_settings('agent_services_cell_phone');
                $_SESSION['agents']['agent_services_email'] = get_app_settings('agent_services_email');
            }
            /*---/parent agent public data ---*/
            
            $real_ip_address = !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'];
            $updateStr = array("last_login" => 'msqlfunc_NOW()','last_login_ip'=>$real_ip_address, 'is_login' => 'Y', 'last_login_track' => 'msqlfunc_NOW()');
            $where = array("clause" => 'id=:id', 'params' => array(':id' => $_SESSION['agents']['id']));
            $pdo->update("customer_settings", $updateStr, $where);

            $user_data = get_user_data($_SESSION['agents']);
            $audit_log_id = audit_log($user_data, $_SESSION['agents']['id'], "Agent", "Log In", '', '', 'login');
            $_SESSION['agents']['audit_log_id'] = $audit_log_id;

            $description['ac_message'] =array(
              'ac_red_1'=>array(
                'href'=>$ADMIN_HOST.'/agent_detail_v1.php?id='.md5($_SESSION['agents']['id']),
                'title'=>$_SESSION['agents']['rep_id'],
              ),
              'ac_message_1' =>' Logged into account.',
              );
            $desc = json_encode($description);
            activity_feed(3, $_SESSION['agents']['id'], 'Agent', $_SESSION['agents']['id'], 'customer', 'Logged Agent Account', $_SESSION['agents']['fname'], $_SESSION['agents']['lname'], $desc);

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
          $validate->setError("general", "Agent ID or the password you entered is not valid. Please re-enter.");
        }
      } else {
        $validate->setError("general", "Agent ID or the password you entered is not valid. Please re-enter.");
      }
    }

    if(!empty($custRowPopup['status']) && (in_array($custRowPopup['status'],array('Terminated'))) && $display_popup=="true") {
       $cust_status = $custRowPopup['status'];
       $response['customer_status'] = $cust_status;
    }
  }

  if(count($validate->getErrors()) > 0){
      $response['status'] = "error";
      $response['errors'] = $validate->getErrors();
  }
  echo json_encode($response);
  exit();
}


$setting_keys = array(
  'agent_services_cell_phone',
  'agent_services_email',
);
$app_setting_res = get_app_settings($setting_keys);
$service = 'Agent Services';
$cell_phone = '';
$service_email = '';
if(!empty($app_setting_res)){
  $cell_phone = format_telephone($app_setting_res['agent_services_cell_phone']);
  $service_email = $app_setting_res['agent_services_email'];
}

$exStylesheets = array('thirdparty/colorbox/colorbox.css');
$exJs = array(
  'agents/js/notification.js',
  'thirdparty/colorbox/jquery.colorbox.js',
  'thirdparty/bower_components/moment/moment.js',
  'thirdparty/bower_components/moment/moment-timezone-with-data.min.js'
);

$errors = $validate->getErrors();
$template = 'index.inc.php';
$layout = 'single.layout.php';
include_once 'layout/end.inc.php';
?>
