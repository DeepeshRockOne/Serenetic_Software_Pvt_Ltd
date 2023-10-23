<?php
include_once (__DIR__) . '/layout/start.inc.php';

$user_data = get_user_data($_SESSION['groups']);
audit_log($user_data, $_SESSION['groups']['id'], "Group", "Log out", '', '', 'logout');

$description['ac_message'] =array(
    'ac_red_1'=>array(
      'href'=>$ADMIN_HOST.'/groups_details.php?id='.md5($_SESSION['groups']['id']),
      'title'=>$_SESSION['groups']['rep_id'],
    ),
    'ac_message_1' =>' Logged out account.',
    );
  $desc = json_encode($description);
  activity_feed(3, $_SESSION['groups']['id'], 'Group', $_SESSION['groups']['id'], 'customer', 'Logged Group Account', $_SESSION['groups']['fname'], $_SESSION['groups']['lname'], $desc);

admin_has_access();

if(isset($_SESSION['sb-session']) && $_SESSION['sb-session']['app_user_id'] == $_SESSION['groups']['id'] && $_SESSION['sb-session']['app_user_type'] == 'Group') {
    unset($_SESSION['sb-session']);
}

if(isset($_SESSION['groups']['admin_switch']) && isset($_GET['admin']) && $_GET['admin'] == "yes"){
  unset($_SESSION['groups']);
  redirect($HOST . '/admin/');
  exit;
} else{

 $update_params = array('is_login' => 'N');
 $update_where = array('clause' => 'customer_id = :id', 'params' => array(':id' => $_SESSION['groups']['id']));
 $pdo->update("customer_settings", $update_params, $update_where);

unset($_SESSION['groups']);
unset($_SESSION['GROUP_INFO']);

$previous_page = isset($_REQUEST['previous_page']) ? $_REQUEST['previous_page'] : '';
if(!empty($previous_page)){
  redirect("index.php?previous_page=".urlencode($previous_page));
}else{
  redirect('index.php');  
}
}
?>
