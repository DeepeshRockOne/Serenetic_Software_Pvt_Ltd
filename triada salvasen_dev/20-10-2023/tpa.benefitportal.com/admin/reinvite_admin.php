<?php

include_once dirname(__FILE__) . '/layout/start.inc.php';
$validate = new Validation();
has_access(3);
$id = $_GET['id'];
  
$select = "SELECT id,display_id,fname,lname,email,type, invite_key,status, HOUR(TIMEDIFF(NOW(), invite_at)) as invite_time_diff FROM admin WHERE md5(id)=:id ";
$param = array(":id" => $id);
$data = $pdo->selectOne($select, $param);

if ($data) {
    if ($data['status'] == 'Pending') {
        $fname = $data['fname'];
        $lname = $data['lname'];
        $access_level = $data['type'];
        $email = trim($data['email']);
        $display_id = $data['display_id'];
        if($data['invite_time_diff'] > 168)
            $link = "Application link has expired";
        else
            $link = $ADMIN_HOST . '/sign_up.php?key=' . $data['invite_key'];
    } else {
        setNotifySuccess('Registration process for this user has been completed.');
        echo '<script type="text/javascript">window.parent.location.href=window.parent.location.href;</script>';
        exit;
    }
} else {
    setNotifySuccess('User not found.');
    echo '<script type="text/javascript">window.parent.location.href=window.parent.location.href;</script>';
    exit;
}

if (isset($_POST['save'])) {
    $email = checkIsset($_POST['email']);
    $validate->email(array('required' => true, 'field' => 'email', 'value' => $email), array('required' => 'Email is required', 'invalid' => 'Invalid Email'));

    if (!$validate->getError('email')) {
        $sql = "SELECT id,email FROM admin WHERE email = :email AND md5(id)!=:id";
        $whr = array(
            ':email' => makeSafe($email), ':id' => $_GET['id']);
        $row = $pdo->selectOne($sql, $whr);
        if (count($row) > 0) {
            if ($row['id'] > 0) {
                $validate->setError('email', 'This email is already associated with another admin account');
            }
        }
    }

    if ($validate->isValid()) {
        $key = md5($fname . $lname . sha1(time()));
        $admin_type = strtolower(str_replace(" ", "", $access_level));
        // Admin - New Invite
        $trigger_id = 1;
        $link = $ADMIN_HOST . '/sign_up.php?key=' . $key;


        $params['fname'] = $fname;
        $params['lname'] = $lname;
        $params['link'] = $link;
        $params['email'] = $email;

        $update_params = array(
            'email' => makeSafe($email),
            'invite_key' => $key,
            'invite_at' => 'msqlfunc_NOW()',
            'updated_at' => 'msqlfunc_NOW()'
        );

        $update_where = array(
            'clause' => 'md5(id) = :id',
            'params' => array(
                ':id' => makeSafe($id)
            )
        );
        $update_status = $pdo->update('admin', $update_params, $update_where);
        $user_data = get_user_data($_SESSION['admin']);
        audit_log($user_data, $data['id'], 'Admin', 'Re-invited');
        try {
            $params['USER_IDENTITY'] = array('rep_id' => $display_id, 'cust_type' => 'Admin', 'location' => ($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']));

            $smart_tags = get_user_smart_tags($data['id'],'admin');
                
            if($smart_tags){
                $params = array_merge($params,$smart_tags);
            }
            
            trigger_mail($trigger_id, $params, $email,'',3);

            $desc = array();
            $desc['ac_message'] = array(
                'ac_red_1' => array(
                    'href' => 'admin_profile.php?id=' . md5($_SESSION['admin']['id']),
                    'title' => $_SESSION['admin']['display_id'],
                ),
                'ac_message_1' => ' Re-invite Admin',
                'ac_red_2' => array(
                    'href' => 'admin_profile.php?id=' . md5($data['id']),
                    'title' => $display_id,
                ),
            );
            $desc = json_encode($desc);
            activity_feed(3,$_SESSION['admin']['id'],'Admin',$data['id'],'Admin','Admin Re-Invite Admin',$_SESSION['admin']['fname'],$_SESSION['admin']['lname'],$desc);
        } catch (Exception $e) {
            echo $e;
            exit;
        }
        setNotifySuccess('You have successfully resent invitation to ' . $fname . ' ' . $lname . ' at ' . $email);
        redirect('admins.php',true);
    }
}

$exJs = array('thirdparty/clipboard/clipboard.min.js');

$errors = $validate->getErrors();
$layout = "iframe.layout.php";
$template = "reinvite_admin.inc.php";
include_once 'layout/end.inc.php';
?>
