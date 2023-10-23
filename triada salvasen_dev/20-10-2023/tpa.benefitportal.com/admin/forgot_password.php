<?php
include_once (__DIR__) . '/includes/connect.php';
if(isset($_SESSION['admin']['id'])){
    redirect('dashboard.php');
}
$validate = new Validation();

if(isset($_POST['cancel'])){
    redirect('index.php');
}

if(isset($_POST['submit']) && $_POST['submit'] == 'forget'){
  $email = checkIsset($_POST['email']);
  
  $validate->email(array('required' => true, 'field' => 'email', 'value' => $email),array('required' => 'Email is required', 'invalid' => 'Valid Email is required'));
  
  if(!$validate->getError('email')){
      $selSql = "SELECT * FROM admin WHERE email = :email and status='Active'";
      $param = array(":email" => makeSafe($email));
      $custRow = $pdo->selectOne($selSql,$param);
      if (count($custRow) == 0) {
        $validate->setError('email', 'Email Id does not match/exist');
      } else {
        $fname = $custRow['fname'];
        $lname = $custRow['lname'];
        $id = $custRow['id'];  
      }     
  }

  if($validate->isValid()) {
    $key = md5($fname . $lname .sha1(time()));
    $update_params =  array(
      'pwd_reset_key' => $key,
      'pwd_reset_at'  => 'msqlfunc_NOW()'
    );
    $update_where = array(
        'clause' => 'id = :id',
        'params' => array(
            ':id' => $id
        )
    );
    $pdo->update('admin', $update_params,$update_where);
    
    $link = $ADMIN_HOST . '/password_recovery.php?key=' . $key;

    $params = array();
    $params['fname'] = $fname;
    $params['lname'] = $lname;
    $params['link']  = $link;
    $trigger_id = 2;

    $smart_tags = get_user_smart_tags($id,'admin');
                
    if($smart_tags){
        $params = array_merge($params,$smart_tags);
    }
    trigger_mail($trigger_id, $params, $email);

    $desc = array();
    $desc['description'] = '<span class="text-action">'.$custRow['display_id'].'</span> forgot password and requested for update password';
    activity_feed(3,$custRow['id'],'Admin',$custRow['id'],'Admin','Requested For update Password','','',json_encode($desc));

    setNotifySuccess("Password recovery email sent.");
    redirect('index.php',true); 
  }  

}
$errors = $validate->getErrors();
$template = 'forgot_password.inc.php';
$layout = 'iframe.layout.php';
include_once 'layout/end.inc.php';

?>