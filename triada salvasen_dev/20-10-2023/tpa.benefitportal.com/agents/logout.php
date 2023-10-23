<?php

include_once (__DIR__) . '/layout/start.inc.php';
$user_data = get_user_data($_SESSION['agents']);
audit_log($user_data, $_SESSION['agents']['id'], "Agents", "Log out", '', '', 'logout');

$description['ac_message'] =array(
    'ac_red_1'=>array(
      'href'=>$ADMIN_HOST.'/agent_detail_v1.php?id='.md5($_SESSION['agents']['id']),
      'title'=>$_SESSION['agents']['rep_id'],
    ),
    'ac_message_1' =>' Logged out account.',
    );
  $desc = json_encode($description);
  activity_feed(3, $_SESSION['agents']['id'], 'Agent', $_SESSION['agents']['id'], 'customer', 'Logged Agent Account', $_SESSION['agents']['fname'], $_SESSION['agents']['lname'], $desc);

admin_has_access();

if(isset($_SESSION['sb-session']) && $_SESSION['sb-session']['app_user_id'] == $_SESSION['agents']['id'] && $_SESSION['sb-session']['app_user_type'] == 'Agent') {
    unset($_SESSION['sb-session']);
}

if(isset($_SESSION['agents']['admin_switch']) && isset($_GET['admin']) && $_GET['admin'] == "yes"){
  unset($_SESSION['agents']);
  redirect($HOST . '/admin/');
  exit;
} else{

 $update_params = array('is_login' => 'N');
 $update_where = array('clause' => 'customer_id = :id', 'params' => array(':id' => $_SESSION['agents']['id']));
 $pdo->update("customer_settings", $update_params, $update_where);
// close chat when cutsomer logout


//session_destroy();
unset($_SESSION['agents']);
unset($_SESSION['AGENT_INFO']);
// unset($_SESSION['account_type']);

$previous_page = isset($_REQUEST['previous_page']) ? $_REQUEST['previous_page'] : '';
if(!empty($previous_page)){
  redirect("index.php?previous_page=".urlencode($previous_page));
}else{
  redirect('index.php');  
}
}
?>
