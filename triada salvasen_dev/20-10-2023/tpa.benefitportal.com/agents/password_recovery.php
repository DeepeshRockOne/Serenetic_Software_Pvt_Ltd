<?php
include_once (__DIR__) . '/includes/connect.php';
if (isset($_SESSION['agents']['id'])) {
  redirect('dashboard.php');
}
$validate = new Validation();

if (isset($_POST['cancel'])) {
  redirect('index.php');
}

$error_key = '';

if (isset($_GET['key']) && !empty($_GET['key'])) {
    $key = $_GET['key'];
    $query = "SELECT c.id,rep_id,fname,lname,type,email,user_name,cell_phone,sponsor_id,TIMESTAMPDIFF(HOUR,pwd_reset_at,now()) as difference,pwd_reset_key FROM customer c LEFT JOIN customer_settings cs ON(cs.customer_id=c.id) WHERE pwd_reset_key= :pwd_reset_key";
    $where = array(':pwd_reset_key' => makeSafe($key));
    $row = $pdo->selectOne($query, $where);
} else {
    $error_key = 'Invalid key';
}

if (empty($row) || $row['difference'] > 48) {
  $error_key = "Invalid Key";
}

if($error_key == "Invalid Key"){
    setNotifyError('Password reset link has expired');
    redirect('index.php');
}
if (isset($_POST['submit'])) {
    $password = $_POST['password'];
    $conf_password = $_POST['conf_password'];
  
    $validate->string(array('required' => true, 'field' => 'password', 'value' => $password), array('required' => 'Password is required'));
    $validate->string(array('required' => true, 'field' => 'conf_password', 'value' => $conf_password), array('required' => 'Confirm Password is required'));

    if (!$validate->getError('password')) {
        if (strlen($password) < 8 || strlen($password) > 20) {
          $validate->setError('password', 'Password must be 8-20 characters');
        } else if ((!preg_match('`[A-Z]`', $password) || !preg_match('`[a-z]`', $password)) // at least one alpha
           || !preg_match('`[0-9]`', $password)) {
          // at least one digit
          $validate->setError('password', 'Valid Password is required');
        } else if (!ctype_alnum($password)) {
          $validate->setError('password', 'Special character not allowed');
        } else if (preg_match('`[?/$\*+]`', $password)) {
          $validate->setError('password', 'Password not valid');
        } else if (preg_match('`[,"]`', $password)) {
          $validate->setError('password', 'Password not valid');
        } else if (preg_match("[']", $password)) {
          $validate->setError('password', 'Password not valid');
        }
    }

    if (!$validate->getError('conf_password') && !$validate->getError('password')) {
        if ($password != $conf_password) {
            $validate->setError('conf_password', 'Both Password must be same');
        }
    }

    if ($validate->isValid()) {
        $update_params = array(
            'password' => "msqlfunc_AES_ENCRYPT('" . $password . "','" . $CREDIT_CARD_ENC_KEY . "')" ,        
            'updated_at' => 'msqlfunc_NOW()'
        );
        $update_where = array(
            'clause' => 'id = :id',
            'params' => array(
                ':id' => $row['id']
            )
        );
        $pdo->update('customer', $update_params, $update_where);

        $cs_param = array(
            'pwd_reset_key' => '',
            'pwd_reset_at' => '',
        );
        $cs_where = array(
            'clause' => 'customer_id = :id',
            'params' => array(
                ':id' => $row['id']
            )
        );
        $pdo->update('customer_settings', $cs_param, $cs_where);

        $params = array();
        $params['fname'] = $row['fname'];
        $params['lname'] = $row['lname'];
        $params['link'] = $AGENT_HOST;

        $smart_tags = get_user_smart_tags($row['id'],'agent');
                
        if($smart_tags){
            $params = array_merge($params,$smart_tags);
        }

        $trigger_id = 22;
        trigger_mail($trigger_id,$params,$row['email']);

        $desc = array();
        $desc['description'] = '<span class="text-action">'.$row['rep_id'].'</span> updated password';
        activity_feed(3,$row['id'],$row['type'],$row['id'],$row['type'],'Updated Password','','',json_encode($desc));

        setNotifySuccess('Password successfully updated.');
        redirect('index.php');
    }  
}

$errors = $validate->getErrors();
$template = 'password_recovery.inc.php';
$exJs = array('js/password_validation.js'.$cache,
'thirdparty/masked_inputs/jquery.inputmask.bundle.js');
$layout = 'single.layout.php';
include_once 'layout/end.inc.php';
?>
