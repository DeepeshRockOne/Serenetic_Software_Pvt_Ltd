<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

    $is_ajax_submit = checkIsset($_POST['is_ajax_submit']);
    $fname = checkIsset($_POST['fname']);
    $lname = checkIsset($_POST['lname']);
    $email = checkIsset($_POST['email']);
    $password = checkIsset($_POST['password']);
    $cpassword = checkIsset($_POST['cpassword']);
    $passcode = checkIsset($_POST['passcode']) == 'Y' ? $_POST['passcode'] : 'N' ;
    $group_id = checkIsset($_POST['group_id']);
    $features = array_unique(checkIsset($_POST['feature'],'arr'));
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

    $validate = new Validation();

    if($is_ajax_submit){
        
        $validate->string(array('required' => true, 'field' => 'fname', 'value' => $fname), array('required' => 'First Name is required'));
        $validate->string(array('required' => true, 'field' => 'lname', 'value' => $lname), array('required' => 'Last Name is required'));
        $validate->email(array('required' => true, 'field' => 'email', 'value' => $email), array('required' => 'Email is required', 'invalid' => 'Valid email is required'));

        if ($email != "" && !empty($email) && empty($_POST['sub_id'])) {
            $selectEmail = "SELECT email FROM sub_group WHERE email = :email and is_deleted='N'";
            $where_select_email = array(':email' => $email);
            $resultEmail = $pdo->selectOne($selectEmail, $where_select_email);
            if ($resultEmail) {
                $validate->setError("email", "This email is already exists.");
            }
        }

        if($is_2fa == 'Y'){
            if($send_via == ''){
                $validate->setError('send_via','Please select any method.');
            }else{
                if($send_via == 'sms'){
                    $validate->phoneDigit(array('required'=>true,'field'=>'via_mobile','value'=>$via_mobile),array('required'=>'Phone number is required.','invalid'=>'Enter valid phone number.'));
                }else{
                    $validate->email(array('required' => true, 'field' => 'via_email', 'value' => $via_email), array('required' => 'Email Address is required.', 'invalid' => 'Please enter valid Email Address.'));
                }
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

        if(empty($_POST['sub_id']) || !empty($password)){
            $validate->string(array('required' => true, 'field' => 'password', 'value' => $password, 'min' => 6, 'max' => 20), array('required' => 'Password is required', 'invalid' => 'Please enter valid password'));
            $validate->string(array('required' => true, 'field' => 'cpassword', 'value' => $cpassword, 'min' => 6, 'max' => 20), array('required' => 'Confirm Password is required','invalid' => 'Please enter valid password'));

            if (!$validate->getError('cpassword') && !$validate->getError('password')) {
                if ($password != $cpassword) {
                    $validate->setError('cpassword', 'Both Password must be same');
                }
            }
            if(!$validate->getError('cpassword') && !$validate->getError('fname') && !$validate->getError('lname') && !$validate->getError('fname') && !$validate->getError('email') && !$validate->getError('password')) {
                if(empty($features)){
                    $validate->setError("features","Please Select Any One Option");
                }
            } 
        }

        if($validate->isValid()){
            if (count($features) > 0) {
                $features = implode(',', array_unique($features));
            } else {
                $features = "";
            }
            
            $group_id = getname('customer',$_REQUEST['group_id'],'id','md5(id)');
            $ins_param = array(
                'admin_id' => 0,
                'group_id' => $group_id,
                'fname' => $fname,
                'lname' => $lname,
                'email' => $email,
                'access_type' => 'limited',
                'feature_access' => $features,
                'status' => 'Active',
                'passcode' => $passcode,
                'updated_at' => 'msqlfunc_NOW()',
                'created_at' => 'msqlfunc_NOW()'
            );
            $ins_param['is_2fa'] = $is_2fa;
            if($send_via != ""){
                $ins_param['send_otp_via']=$send_via;
                if($send_via == 'sms'){
                    $ins_param['via_sms'] = $via_mobile; 
                }else{
                    $ins_param['via_email'] = $via_email;
                }
            }

            $ins_param['is_ip_restriction'] = $is_ip_restriction;
            if($is_ip_restriction == 'Y'){
                $ins_param['allowed_ip'] = implode(',',array_values($allowed_ip_res));
            }else{
                $ins_param['allowed_ip'] = "";
            }

            $account_manger = array('name'=>$fname.' '.$lname);
            if(!empty($_POST['operation']) && $_POST['operation'] == 'edit_sub_group' && !empty($_POST['sub_id'])){
                $sub_id = $_POST['sub_id'];
                unset($ins_param['created_at']);
                unset($ins_param['admin_id']);

                if(!empty($password)){
                    $ins_param['password'] = "msqlfunc_AES_ENCRYPT('" . $password . "','" . $CREDIT_CARD_ENC_KEY . "')";
                }
                
                $upd_where = array(
                    'clause' => 'md5(id) = :id',
                    'params' => array(
                        ':id' => $sub_id
                    )
                );
                $old_data = $pdo->update("sub_group",$ins_param,$upd_where,true);
                setNotifySuccess("You have updated an Account Manager!",true);
                unset($ins_param['group_id']);
                unset($ins_param['updated_at']);
                activity_feed_account_manager($group_id,'Updated Account Manager Details In Group',array('old_data'=>$old_data,'new_data'=>$ins_param,'name'=>$fname.' '.$lname));
                // redirect('groups_account_managers.php?group_id='.$_GET["group_id"]);
                $response['status'] = "success";
            }else{
                $ins_param ['password'] =  "msqlfunc_AES_ENCRYPT('" . $password . "','" . $CREDIT_CARD_ENC_KEY . "')";

                $pdo->insert('sub_group',$ins_param);
                activity_feed_account_manager($group_id,'',$account_manger);
                setNotifySuccess("You have successfully made an Account Manager update.");
            }
            // redirect('groups_account_managers.php?group_id='.$_GET["group_id"]);
                $response['status'] = "success";
        }
        if(count($validate->getErrors()) > 0 ){
            $response['status'] = "error";
            $response['errors'] = $validate->getErrors();
        }
        echo json_encode($response);
        exit();
    }


    $parentAccessSql="SELECT * FROM group_feature_access";
    $parentAcceesRes=$pdo->select($parentAccessSql);
    $features_arr = array();
    $featureAccessSql="SELECT id, title, IF(parent_id = 0, id, parent_id) as parent_id 
    FROM group_feature_access 
    ORDER BY parent_id, id";
    
    $featureAccessRes=$pdo->select($featureAccessSql);
    if (!empty($featureAccessRes)) {
        foreach ($featureAccessRes as $feature) {
            if (!isset($features_arr[$feature['parent_id']])) {
                $features_arr[$feature['parent_id']] = $feature;
                $features_arr[$feature['parent_id']]['child'] = array();
            } else {
                $features_arr[$feature['parent_id']]['child'][] = $feature;
            }
        }
    }
    $sub_group = array();
    if(!empty($_GET['edit']) && $_GET['edit'] == md5('edit') && !empty($_GET['sa_id'])){
        $sub_group = $pdo->selectOne("SELECT sa.id as id,sa.id AS name,sa.id as sid,sa.fname,sa.lname,sa.email,sa.access_type,sa.feature_access,sa.status,sa.passcode,sa.is_2fa,sa.send_otp_via,sa.via_email,sa.via_sms,sa.is_ip_restriction,sa.allowed_ip 
        from sub_group sa
        LEFT JOIN customer_settings cs ON(cs.customer_id= sa.group_id) 
        where md5(sa.id)=:id",array(':id'=>$_GET['sa_id']));

        $acl_name = '';
        if (!empty($sub_group)) {
            $selected_acl = explode(',', $sub_group['feature_access']);
            $acl_name = $sub_group['name'];
        }
        $acl = [];
        $acl_names = [];
        $acl_features = [];
        $sql_acl = "SELECT sa.id as id,sa.id AS name,sa.id as sid,sa.fname,sa.lname,sa.email,
        sa.access_type,sa.feature_access,sa.status,sa.passcode 
        from sub_group sa
        LEFT JOIN customer_settings cs ON(cs.customer_id= sa.group_id) 
        where md5(sa.id)=:id";
        $acls = $pdo->select($sql_acl,array(":id"=>$_GET['sa_id']));

        foreach($acls as $acll){
            $acl_names[] = $acll['name'];
            $acl[$acll['id']] = $acll['name'];
            $acl_features[$acll['name']] = explode(',', $acll['feature_access']);
        }
    }

    if(!empty($_GET['delete']) &&  $_GET['delete'] == md5('delete') && !empty($_GET['sa_id'])){

        $upd_param = array("is_deleted"=>'Y',"updated_at"=>'msqlfunc_NOW()');
        $upd_where = array(
            "clause"=>'md5(id)=:id',
            "params"=>array(
                ":id"=>$_GET['sa_id']
                )
        );
        $acc_name = $pdo->selectOne("SELECT CONCAT(fname,lname) as name from sub_group where is_deleted='N' and md5(id)=:id",array(':id'=>$_GET['sa_id']));
        $pdo->update("sub_group",$upd_param,$upd_where);
        $group_id = getname('customer',$_GET['group_id'],'id','md5(id)');
        activity_feed_account_manager($group_id,$msg='Deleted Account Manager In Group',array('name'=>$acc_name['name']));
        header('Content-Type: application/json');
        echo json_encode(array("status"=>'success'));
        exit;
    }

    function activity_feed_account_manager($group_id,$msg='',$extra = array()){
        global $pdo,$ADMIN_HOST;
        if($msg == ''){
            $msg = "Added Account Manager In Group";
        }
        $group_name = $pdo->selectOne("SELECT id, CONCAT(fname,' ',lname) as name ,rep_id from customer where id=:id",array(":id"=>$group_id));

        $description = array();
        $description['ac_message'] = array(
        'ac_red_1'=>array(
            'href'=>$ADMIN_HOST.'/groups_details.php?id='.md5($group_name['id']),
            'title'=>$group_name['rep_id'],
        ),
        'ac_message_1' =>$msg,
        );
        // pre_print($extra);
        if(isset($extra['old_data']['password']) || isset($extra['new_data']['password'])){
            $description['description_password']=  'Password Updated';
        }
        if(!empty($extra)){
            if(!empty($extra['name'])){
                $description['description'] = "Account Manager : ".$extra['name'];
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
                        $old_features = $pdo->selectOne("SELECT GROUP_CONCAT(title) as unselected from group_feature_access where ID IN(".implode(',',$old_features) .")");
                    }
                }
                if(!empty($NewVaArray['feature_access'])){
                    
                    $New_features = array_diff(
                        explode(',',$NewVaArray['feature_access']),
                        explode(',',checkIsset($oldVaArray['feature_access']))
                    );
                    if(!empty($New_features)){
                        $New_features = $pdo->selectOne("SELECT GROUP_CONCAT(title) as selected from group_feature_access where ID IN(".implode(',',$New_features)." ) " );
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
                      $description['key_value']['desc_arr'][$key1]='From '.$oldVaArray[$key1].' To '.$NewVaArray[$key1];
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
        activity_feed(3,$group_name['id'], 'Group' , $group_name['id'], 'Group Account Manager', 'Group Account Manager',$group_name['name'],"",$desc);
    }

$exJs = array('thirdparty/masked_inputs/jquery.maskedinput.min.js');
$template = 'groups_add_account_managers.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
