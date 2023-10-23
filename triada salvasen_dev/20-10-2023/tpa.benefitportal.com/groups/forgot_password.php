<?php
include_once (__DIR__) . '/includes/connect.php';
if (isset($_SESSION['groups']['id'])) {
    redirect('dashboard.php');
}
$validate = new Validation();

if (isset($_POST['cancel'])) {
    redirect('index.php');
}

if (isset($_POST['submit']) && $_POST['submit'] == 'forget') {
    $email = checkIsset($_POST['email']);
    $is_acc_manager = false;
    $validate->email(array('required' => true, 'field' => 'email', 'value' => $email), array('required' => 'Email is required', 'invalid' => 'This email was not found in our system.'));

    if (!$validate->getError('email')) {
        $selSql = "SELECT id,fname,lname,user_name,status,rep_id,email,type FROM customer WHERE email=:email AND type IN('Group') AND status != 'Invited' AND is_deleted = 'N'";
        $params = array(":email" => makeSafe($email));
        $custRow = $pdo->selectOne($selSql, $params);
        if (count($custRow) == 0) {
            $selSql = "SELECT id,fname,lname,status,email FROM sub_group WHERE email=:email AND status != 'Invited' AND is_deleted = 'N'";
            $params = array(":email" => makeSafe($email));
            $custRow = $pdo->selectOne($selSql, $params);
            if (count($custRow) == 0) {  
                $validate->setError('email', 'Email does not match/exist');
            }else{
                $is_acc_manager = true;
                $fname = $custRow['fname'];
                $lname = $custRow['lname'];
                $id = $custRow['id'];
            }
        } else {
          $fname = $custRow['fname'];
          $lname = $custRow['lname'];
          $id = $custRow['id'];  
        } 
    }  

    if ($validate->isValid()) {
        if($custRow['status'] == 'Terminated' || $custRow['status'] == 'Inactive') {
            setNotifyError("Your Account is  ".$custRow['status']);
            redirect('index.php',true);
            exit;
        }

        $key = md5($fname . $lname . sha1(time()));
        $update_params = array(
            'pwd_reset_key' => $key,
            'pwd_reset_at' => 'msqlfunc_NOW()'
        );
        
        if($is_acc_manager){
            $update_where = array(
                'clause' => 'id=:id',
                'params' => array(
                    ':id' => $id
                )
            );
            $pdo->update('sub_group', $update_params, $update_where);
        }else{
            $update_where = array(
                'clause' => 'customer_id=:id',
                'params' => array(
                    ':id' => $id
                )
            );
            $pdo->update('customer_settings', $update_params, $update_where);
        }

        $link = $GROUP_HOST . '/password_recovery.php?key=' . $key;

        $params = array();
        $params['fname'] = $fname;
        $params['lname'] = $lname;
        $params['link'] = $link;

        if(!$is_acc_manager){
            $smart_tags = get_user_smart_tags($id,'group');
            if($smart_tags){
                $params = array_merge($params,$smart_tags);
            }
        }
        $trigger_id = 63;
        trigger_mail($trigger_id,$params,$email);

        if(!$is_acc_manager){
            $desc = array();
            $desc['description'] = '<span class="text-action">'.$custRow['rep_id'].'</span> forgot password and requested for update password';
            activity_feed(3,$custRow['id'],$custRow['type'],$custRow['id'],$custRow['type'],'Requested For update Password','','',json_encode($desc));
        }

        setNotifySuccess("Password recovery email sent.");
        redirect('index.php',true);
    }
}
$errors = $validate->getErrors();
$template = 'forgot_password.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>