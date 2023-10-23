<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

    $is_ajax_submit = checkIsset($_POST['is_ajax_submit']);
    $fname = checkIsset($_POST['fname']);
    $lname = checkIsset($_POST['lname']);
    $email = checkIsset($_POST['email']);
    $password = checkIsset($_POST['password']);
    $cpassword = checkIsset($_POST['cpassword']);
    $passcode = checkIsset($_POST['passcode']) == 'Y' ? $_POST['passcode'] : 'N' ;
    $edit_enrollment = checkIsset($_POST['edit_enrollment']) == 'Y' ? $_POST['edit_enrollment'] : 'N' ;
    $agent_id = checkIsset($_POST['agent_id']);
    $features = array_unique(checkIsset($_POST['feature'],'arr'));
    $displayDirectEnroll = !empty($_POST['displayDirectEnroll']) ? implode(",", $_POST['displayDirectEnroll']) : '';
    $displayDirectEnrollArr = array();
    $additionalAccessArr = array();
    $additionalAccess = !empty($_POST['additionalAccess']) ? implode(",", $_POST['additionalAccess']) : '';
    if(!empty($features)){
        foreach ($features as $a => $b) {
            if ($b == 'undefined') {
                unset($features[$a]);
            }
        }
    }
    $is_2fa = !empty($_POST['is_2fa']) ? $_POST['is_2fa'] : 'N';
    $is_ip_restriction = !empty($_POST['is_ip_restriction']) ? $_POST['is_ip_restriction'] : 'N';
    $allowed_ip_res = !empty($_POST['allowed_ip_res']) ? $_POST['allowed_ip_res'] : array();
    if($is_ip_restriction == "N") {
        $allowed_ip_res = array();
    }
    $send_via = checkIsset($_POST['send_via']);
    $via_mobile = checkIsset($_POST['via_mobile'])!='' ? phoneReplaceMain($_POST['via_mobile']) : '';
    $via_email = checkIsset($_POST['via_email']);
    if($is_ajax_submit){
        $validate = new Validation();
        $validate->string(array('required' => true, 'field' => 'fname', 'value' => $fname), array('required' => 'First Name is required'));
        $validate->string(array('required' => true, 'field' => 'lname', 'value' => $lname), array('required' => 'Last Name is required'));
        $validate->email(array('required' => true, 'field' => 'email', 'value' => $email), array('required' => 'Email is required', 'invalid' => 'Valid email is required'));

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

        /*if ($email != "" && !empty($email) && empty($_POST['sub_id'])) {
            $selectEmail = "SELECT email FROM sub_agent WHERE email = :email and is_deleted='N'";
            $where_select_email = array(':email' => $email);
            $resultEmail = $pdo->selectOne($selectEmail, $where_select_email);
            if ($resultEmail) {
                $validate->setError("email", "This email is already exists.");
            }
        }

        if(!$validate->getError('email')){
            $selectEmail = "SELECT email FROM customer WHERE email = :email and type = 'Agent' and is_deleted='N'";
            $where_select_email = array(':email' => $email);
            $resultEmail = $pdo->selectOne($selectEmail, $where_select_email);
            if ($resultEmail) {
                $validate->setError("email", "This email is associated with other agent account.");
            }   
        }*/

        if(empty($_POST['sub_id']) || !empty($password)){
            $validate->string(array('required' => true, 'field' => 'password', 'value' => $password, 'min' => 6, 'max' => 20), array('required' => 'Password is required', 'invalid' => 'Please enter valid password'));
            $validate->string(array('required' => true, 'field' => 'cpassword', 'value' => $cpassword, 'min' => 6, 'max' => 20), array('required' => 'Confirm Password is required','invalid' => 'Please enter valid password'));

            if (!$validate->getError('cpassword') && !$validate->getError('password')) {
                if ($password != $cpassword) {
                    $validate->setError('cpassword', 'Both Password must be same');
                }
            }
        }
        if(empty($features)){
            $validate->setError("features","Please Select Any One Option");
        }

        if($validate->isValid()){
            if (count($features) > 0) {
                $features = implode(',', array_unique($features));
            } else {
                $features = "";
            }
            $agent_id = getname('customer',$_REQUEST['agent_id'],'id','md5(id)');
            $ins_param = array(
                'admin_id' => $_SESSION['admin']['id'],
                'agent_id' => $agent_id,
                'fname' => $fname,
                'lname' => $lname,
                'email' => $email,
                'access_type' => 'limited',
                'feature_access' => $features,
                'status' => 'Active',
                'passcode' => $passcode,
                'edit_enrollment' => $edit_enrollment,
                'displayDirectEnroll'=>$displayDirectEnroll,
                'additionalAccess'=>$additionalAccess,
                'updated_at' => 'msqlfunc_NOW()',
                'created_at' => 'msqlfunc_NOW()'
            );
            $ins_param['is_2fa'] = $is_2fa;
            if($send_via !=''){
            $ins_param['send_otp_via'] = $send_via;
            if($send_via == 'sms'){
                $ins_param['via_sms'] = $via_mobile;
            }else{
                $ins_param['via_email'] = $via_email;
            }
            }
            $ins_param['is_ip_restriction'] = $is_ip_restriction;
            if($is_ip_restriction == "Y") {
                $ins_param['allowed_ip'] = implode(',',array_values($allowed_ip_res));
            } else {
                $ins_param['allowed_ip'] = "";
            }
            $account_manger = array('name'=>$fname.' '.$lname);
            if(!empty($_POST['operation']) && $_POST['operation'] == 'edit_sub_agent' && !empty($_POST['sub_id'])){
                $sub_id = $_POST['sub_id'];
                unset($ins_param['created_at']);

                if(!empty($password)){
                    $ins_param['password'] = "msqlfunc_AES_ENCRYPT('" . $password . "','" . $CREDIT_CARD_ENC_KEY . "')";
                }
                
                $upd_where = array(
                    'clause' => 'md5(id) = :id',
                    'params' => array(
                        ':id' => $sub_id
                    )
                );
                $old_data = $pdo->update("sub_agent",$ins_param,$upd_where,true);
                setNotifySuccess("You have updated an Account Manager!",true);
                unset($ins_param['agent_id']);
                unset($ins_param['admin_id']);
                unset($ins_param['updated_at']);
                activity_feed_account_manager($agent_id,'Updated Account Manager Details In Agent',array('old_data'=>$old_data,'new_data'=>$ins_param,'name'=>$fname.' '.$lname));
                $response['status'] = "success";
            }else{
                $ins_param['account_manager_id'] = get_agent_account_manager_id();
                $ins_param['password'] =  "msqlfunc_AES_ENCRYPT('" . $password . "','" . $CREDIT_CARD_ENC_KEY . "')";

                $pdo->insert('sub_agent',$ins_param);
                activity_feed_account_manager($agent_id,'',$account_manger);
                setNotifySuccess("You have successfully made an Account Manager update.");
            }
            $response['status'] = "success";
        }
        if(count($validate->getErrors()) > 0){
            $response['status'] = "error";
            $response['errors'] = $validate->getErrors();
        }
        echo json_encode($response);
        exit();
    }

    
    $parentAccessSql="SELECT * FROM agent_feature_access";
    $parentAcceesRes=$pdo->select($parentAccessSql);
    $features_arr = get_agent_feature_access_options();
    $sub_agent = array();
    if(!empty($_GET['edit']) && $_GET['edit'] == md5('edit') && !empty($_GET['sa_id'])){
        
        $sub_agent = $pdo->selectOne("SELECT acl.id as id,acl.level AS name,sa.id as sid,sa.fname,sa.lname,sa.email,sa.access_type,sa.feature_access,sa.status,sa.passcode,sa.is_2fa,sa.send_otp_via,sa.via_email,sa.via_sms,sa.is_ip_restriction,sa.allowed_ip,sa.edit_enrollment
        from sub_agent sa
        LEFT JOIN customer_settings cs ON(cs.customer_id= sa.agent_id) 
        LEFT JOIN agent_coded_level acl ON(acl.id = cs.agent_coded_id) where md5(sa.id)=:id",array(':id'=>$_GET['sa_id']));

        $acl_name = '';
        if (!empty($sub_agent)) {
            $selected_acl = explode(',', $sub_agent['feature_access']);
            $acl_name = $sub_agent['name'];
        }
        $acl = [];
        $acl_names = [];
        $acl_features = [];
        $sql_acl = "SELECT acl.id as id,acl.level AS name,sa.id as sid,sa.fname,sa.lname,sa.email,
        sa.access_type,sa.feature_access,sa.status,sa.passcode 
        from sub_agent sa
        LEFT JOIN customer_settings cs ON(cs.customer_id= sa.agent_id) 
        LEFT JOIN agent_coded_level acl ON(acl.id = cs.agent_coded_id) 
        where md5(sa.id)=:id";
        $acls = $pdo->select($sql_acl,array(":id"=>$_GET['sa_id']));

        foreach($acls as $acll){
            $acl_names[] = $acll['name'];
            $acl[$acll['id']] = $acll['name'];
            $acl_features[$acll['name']] = explode(',', $acll['feature_access']);
        }

        $sqlDirectEnroll = "SELECT displayDirectEnroll,additionalAccess FROM sub_agent where md5(id) = :agent_id";
        $resDirectEnroll = $pdo->selectOne($sqlDirectEnroll,array(":agent_id"=>$_GET['sa_id']));

        $displayDirectEnrollArr = !empty($resDirectEnroll) && !empty($resDirectEnroll['displayDirectEnroll']) ? explode(",",$resDirectEnroll['displayDirectEnroll']) : array();
        $additionalAccessArr = !empty($resDirectEnroll) && !empty($resDirectEnroll['additionalAccess']) ? explode(",",$resDirectEnroll['additionalAccess']) : array();
    }

    if(!empty($_GET['delete']) &&  $_GET['delete'] == md5('delete') && !empty($_GET['sa_id'])){

        $upd_param = array("is_deleted"=>'Y',"updated_at"=>'msqlfunc_NOW()');
        $upd_where = array(
            "clause"=>'md5(id)=:id',
            "params"=>array(
                ":id"=>$_GET['sa_id']
                )
        );
        $acc_name = $pdo->selectOne("SELECT CONCAT(fname,lname) as name from sub_agent where is_deleted='N' and md5(id)=:id",array(':id'=>$_GET['sa_id']));
        $pdo->update("sub_agent",$upd_param,$upd_where);
        $agent_id = getname('customer',$_GET['agent_id'],'id','md5(id)');
        activity_feed_account_manager($agent_id,$msg='Deleted Account Manager In Agent',array('name'=>$acc_name['name']));
        header('Content-Type: application/json');
        echo json_encode(array("status"=>'success'));
        exit;
    }

    function activity_feed_account_manager($agent_id,$msg='',$extra = array()){
        global $pdo,$ADMIN_HOST;
        if($msg == ''){
            $msg = "Added Account Manager In Agent";
        }
        $agent_name = $pdo->selectOne("SELECT id, CONCAT(fname,' ',lname) as name ,rep_id from customer where id=:id",array(":id"=>$agent_id));
        $description = array();
        $description['ac_message'] = array(
        'ac_red_1'=>array(
            'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
            'title'=>$_SESSION['admin']['display_id'],
        ),
        'ac_message_1' =>$msg.' '.$agent_name['name'].' (',
        'ac_red_2'=>array(
            'href'=> $ADMIN_HOST.'/agent_detail_v1.php?id='.md5($agent_name['id']),
            'title'=> $agent_name['rep_id'],
        ),
        'ac_message_2' =>')<br>',
        );

        if(isset($extra['old_data']['password']) || isset($extra['new_data']['password'])){
            $description['description_password']=  'Password Updated';
        }
        if(!empty($extra)){
            if(!empty($extra['name'])){
                $description['description'] = "Account Manger : ".$extra['name'];
            }
            if(!empty($extra['old_data']) && !empty($extra['new_data'])){

                $oldVaArray = $extra['old_data'];
                $NewVaArray = $extra['new_data'];

                $old_features = $New_features = array();
                if(!empty($oldVaArray['feature_access'])){
                    $old_features = array_diff(
                        explode(',',$oldVaArray['feature_access']),
                        explode(',',checkIsset($NewVaArray['feature_access']))
                    );

                    if(!empty($old_features)){
                        $old_features = $pdo->selectOne("SELECT GROUP_CONCAT(title) as unselected from agent_feature_access where ID IN(".implode(',',$old_features) .")");
                    }
                }
                if(!empty($NewVaArray['feature_access'])){
                    
                    $New_features = array_diff(
                        explode(',',$NewVaArray['feature_access']),
                        explode(',',checkIsset($oldVaArray['feature_access']))
                    );
                    if(!empty($New_features)){
                        $New_features = $pdo->selectOne("SELECT GROUP_CONCAT(title) as selected from agent_feature_access where ID IN(".implode(',',$New_features)." ) " );
                    }
                }
                unset($NewVaArray['feature_access']);
                unset($oldVaArray['feature_access']);

                $checkDiff = array_diff_assoc($oldVaArray, $NewVaArray);
                if(!empty($checkDiff)){
                    foreach ($checkDiff as $key1 => $value1) {
                        if($key1 == 'passcode'){
                            $oldVaArray[$key1] = $oldVaArray[$key1] == 'Y' ? 'selected' : 'unselected';
                            $NewVaArray[$key1] = $NewVaArray[$key1] == 'Y' ? 'selected' : 'unselected';
                        }
                        $tmp_key2 = str_replace('_',' ',$key1);
                        if(in_array($key1,array('is_2fa'))){
                            $tmp_key2 = "Two-Factor Authentication (2FA)";
                        }
                        if(in_array($key1,array('is_ip_restriction'))){
                            $tmp_key2 = "IP Address Restriction";
                        }
                      $description['key_value']['desc_arr'][$tmp_key2]='From '.$oldVaArray[$key1].' To '.$NewVaArray[$key1];
                    } 
                }               
                if(!empty($old_features['unselected']) || !empty($old_features['unselected']) ){
                    $description['description_feature'] = "Feature Update : ";
                    if(!empty($old_features['unselected'])){
                        $description['unselected']='Unselected : '.$old_features['unselected'];
                    }
                    if(!empty($New_features['selected'])){
                        $description['selected']='Selected : '.$New_features['selected'];
                    }
                }
                
                
            }
        }
        $desc = json_encode($description);
        activity_feed(3,$agent_name['id'], 'Agent' , $agent_name['id'], 'Agent Account Manager', 'Agent Account Manager',$_SESSION['admin']['name'],"",$desc);
    }

    $displayDirectEnrollList = array("Agents","Leads","Members","Groups");
    $additionalAccessList = array(
        "benefit_tier"=> array("descriptions" => "Plan Tier: User has ability to update Plan tier of plan following granted access."),
        "policy_change" => array("descriptions" =>"Plan Change: User has ability to upgrade/downgrade plan following granted access."),
        "effective_date" => array("descriptions" =>"Effective Date: User has ability to change effective date of plan following granted access."),
        "termination_date" => array("descriptions" =>"Termination Date: User has ability to set and change termination date of plan following granted access."),
        "reversals_orders" => array("descriptions" =>"Reversals Orders: User has ability to perform reversals on orders following granted access.")
    );
$exJs = array('thirdparty/masked_inputs/jquery.maskedinput.min.js');
$template = 'agents_add_account_managers.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
