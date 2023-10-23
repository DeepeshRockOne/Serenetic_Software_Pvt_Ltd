<?php

include_once (__DIR__) . '/includes/connect.php';

if (!empty($_REQUEST['id'])) {
  $sql = "SELECT id,company_id,fname,lname,user_name,email,type,cell_phone,rep_id,sponsor_id,status,created_at FROM customer WHERE (md5(id) = '" . $_REQUEST['id'] . "' OR temp_login_key='".$_REQUEST['id']."')";
  $sub_row = $pdo->selectOne($sql);
  // pre_print($sub_row );
    if(!empty($_REQUEST['is_admin'])) {
        $admin_sql = "SELECT id,fname,lname,CONCAT(fname,' ',lname) as name,email,type,phone,chat_password,updated_at,feature_access,photo,created_at FROM admin WHERE md5(id) = '" . $_REQUEST['id'] . "'";
        $admin_sub_row = $pdo->selectOne($admin_sql);

        if($admin_sub_row){
            $_SESSION['admin'] = null;
            unset($_SESSION['admin']);
            $_SESSION['admin'] = $admin_sub_row;
            // $_SESSION['agents']['admin_switch'] = 'yes';
            redirect($HOST . "/admin/dashboard.php");
        }
    }
 
    if ($sub_row) {
        if ($sub_row['type'] == "Affiliates") {
          $_SESSION['member'] = null;
          unset($_SESSION['member']);
          $_SESSION['member'] = $sub_row;
          // $_SESSION['member']['admin_switch'] = 'yes';
          redirect($HOST . "/acenter/dashboard.php");
         // redirect($HOST . "/affiliates/dashboard.php");
          // redirect("https://myhealthpass.com/affiliates/dashboard.php");
        } else if ($sub_row['type'] == "Ambassadors") {
          $_SESSION['member'] = null;
          unset($_SESSION['member']);
          $_SESSION['member'] = $sub_row;
          // $_SESSION['member']['admin_switch'] = 'yes';
          redirect($HOST . "/acenter/dashboard.php");
         // redirect($HOST . "/affiliates/dashboard.php");
          // redirect("https://myhealthpass.com/affiliates/dashboard.php");
        } else if ($sub_row['type'] == "Call Center" || $sub_row['type'] == "Fronter" || $sub_row['type'] == "Call Center Manager") {
            // echo "string"; exit;
            $sql = "SELECT id,fname,lname,user_name,email,type,agent_coded_level,cell_phone,rep_id,call_center_access,sponsor_id,status, created_at,company_id,twilio_account_id, AES_DECRYPT(password,'" . $CREDIT_CARD_ENC_KEY . "') as stored_password,company_id,access_type,feature_access
                FROM customer WHERE md5(id) = '" . $_REQUEST['id'] . "'";
            $sub_row = $pdo->selectOne($sql);

            $_SESSION['call_center'] = null;
            unset($_SESSION['call_center']);
            $_SESSION['call_center'] = $sub_row;
            $_SESSION['call_center']['access'] = (array) json_decode($sub_row['feature_access']);
            $_SESSION['call_center']['access_type'] = $sub_row['access_type'];
            // $_SESSION['call_center']['admin_switch'] = 'yes';

            if($_SESSION['call_center']['access_type'] == "" || $_SESSION['call_center']['access_type'] == 'full_access') {
                $access_type_and_access = get_call_center_access_type_and_access($sub_row['sponsor_id']);
                $_SESSION['call_center']['access_type'] = $access_type_and_access['access_type'];
                $_SESSION['call_center']['access'] = $access_type_and_access['access'];
            }

            $updateStr = array("last_login" => 'msqlfunc_NOW()', 'is_login' => 'Y', 'last_login_track' => 'msqlfunc_NOW()');
            $where = array("clause" => 'id=:id', 'params' => array(':id' => $sub_row['id']));
            $pdo->update("customer", $updateStr, $where);

            setcookie('WebIMUser', $sub_row['fname'] . " " . $sub_row['lname'], time() + 60 * 60 * 24, "/", $COOKIE_DOMAIN);
            setcookie('WebIMCustId', $sub_row['id'], time() + 60 * 60 * 24, "/", $COOKIE_DOMAIN);
            setcookie('WebIMCustType', $sub_row['type'], time() + 60 * 60 * 24, "/", $COOKIE_DOMAIN);
            // echo $CALL_CENTER_HOST; exit;
            redirect($CALL_CENTER_HOST . "/dashboard.php");
        } else if ($sub_row['type'] == "Supporter") {
            $_SESSION['supporter'] = null;
            unset($_SESSION['supporter']);
            $_SESSION['supporter'] = $sub_row;
            // $_SESSION['organization']['admin_switch'] = 'yes';
            redirect($MYHEALTH_HOST . "/fundraiser");
        } else if ($sub_row['type'] == "Organization") {
            $_SESSION['organization'] = null;
            unset($_SESSION['organization']);
            $_SESSION['organization'] = $sub_row;
            // $_SESSION['organization']['admin_switch'] = 'yes';
            redirect($MYHEALTH_HOST . "/fundraiser");
        } else if ($sub_row['type'] == "Group") {
            $_SESSION['groups'] = null;
            unset($_SESSION['groups']);
            $_SESSION['groups'] = $sub_row;
            // $_SESSION['groups']['admin_switch'] = 'yes';
            redirect($HOST . "/groups/dashboard.php");
        } else if ($sub_row['type'] == "Customer") {
            if(!empty($_REQUEST['customer'])) {
                $_SESSION['customer'] = null;
                unset($_SESSION['customer']);
                $_SESSION['customer'] = $sub_row;
                redirect($HOST . "/member/dashboard.php");
                // redirect($HOST. '/member/index.php?switch=Y&id=' . md5($sub_row['id']));
            } else {
                if(!empty($_REQUEST['company'])){
                    if($_REQUEST['company'] == '1' || $_REQUEST['company']=='2'){        
                // if($sub_row['company_id']=='1' || $sub_row['company_id']=='2' || $sub_row['company_id']=='4'){
                      $_SESSION['customer'] = null;
                      unset($_SESSION['customer']);
                      //$_SESSION['customer']['admin_switch'] = 'yes';
                      $companySql = "SELECT * FROM company WHERE id=:id";
                      $whr = array(":id"=>$sub_row['company_id']);
                      $companyRows = $pdo->selectOne($companySql,$whr);
                      redirect($companyRows['site_url'].'/customer/index.php?switch=Y&id='.md5($sub_row['id']));  
                    }else{
                      setNotifyError("Member Not Found!");
                    }
                }
            }
        } else if ($sub_row['type'] == "Agent") {
                  $_SESSION['agents'] = null;
                  unset($_SESSION['agent']);
                  $_SESSION['agents'] = $sub_row;
                  // $_SESSION['agents']['admin_switch'] = 'yes';
                  
                  setcookie('WebIMUser', $sub_row['fname'] . " " . $sub_row['lname'], time() + 60 * 60 * 24, "/", $COOKIE_DOMAIN);
                  setcookie('WebIMCustId', $sub_row['id'], time() + 60 * 60 * 24, "/", $COOKIE_DOMAIN);
                  setcookie('WebIMCustType', $sub_row['type'], time() + 60 * 60 * 24, "/", $COOKIE_DOMAIN);

                  redirect($AGENT_HOST . "/dashboard.php");
        } else {
           $_SESSION['customer'] = null;
          unset($_SESSION['customer']);
          $_SESSION['customer'] = $sub_row;
          // $_SESSION['customer']['admin_switch'] = 'yes';
          redirect($HOST . "/member/dashboard.php");
        }
    } else {
        redirect($HOST);
    }
}
?>