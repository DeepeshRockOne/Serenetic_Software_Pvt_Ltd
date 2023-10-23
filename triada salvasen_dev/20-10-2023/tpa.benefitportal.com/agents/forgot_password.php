<?php
include_once (__DIR__) . '/includes/connect.php';
if (isset($_SESSION['agents']['id'])) {
    redirect('dashboard.php');
}
$validate = new Validation();

if (isset($_POST['cancel'])) {
    redirect('index.php');
}

if (isset($_POST['submit']) && $_POST['submit'] == 'forget') {
    $rep_id = trim($_POST['rep_id']);
    
    $validate->string(array('required' => true, 'field' => 'rep_id', 'value' => $rep_id), array('required' => 'Agent ID is required'));

    if (!$validate->getError('rep_id')) {
        $selSql = "SELECT id,fname,lname,user_name,status,rep_id,email,type FROM customer WHERE rep_id=:rep_id AND type IN('Agent') AND status != 'Invited' AND is_deleted = 'N'";
        $params = array(":rep_id" => makeSafe($rep_id));
        $custRow = $pdo->selectOne($selSql, $params);
        if (count($custRow) == 0) {
          $validate->setError('rep_id', 'Agent ID does not match/exist');
        } else {
          $email = $custRow['email'];
          $fname = $custRow['fname'];
          $lname = $custRow['lname'];
          $res_user_name = $custRow['user_name'];
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
        $update_where = array(
            'clause' => 'customer_id=:id',
            'params' => array(
                ':id' => $id
            )
        );
        $pdo->update('customer_settings', $update_params, $update_where);

        $link = $AGENT_HOST . '/password_recovery.php?key=' . $key;

        $params = array();
        $params['fname'] = $fname;
        $params['lname'] = $lname;
        $params['link'] = $link;
        $trigger_id = 21;

        $smart_tags = get_user_smart_tags($id,'agent');
                
        if($smart_tags){
            $params = array_merge($params,$smart_tags);
        }

        trigger_mail($trigger_id,$params,$email);

        $desc = array();
        $desc['description'] = '<span class="text-action">'.$custRow['rep_id'].'</span> forgot password and requested for update password';
        activity_feed(3,$custRow['id'],$custRow['type'],$custRow['id'],$custRow['type'],'Requested For update Password','','',json_encode($desc));

        setNotifySuccess("Password recovery email sent.");
        redirect('index.php',true);
    }
}
$errors = $validate->getErrors();
$template = 'forgot_password.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';
?>