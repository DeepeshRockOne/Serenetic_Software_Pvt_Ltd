<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$agent_id = checkIsset($_GET['agent_id']);
if(empty($agent_id)){
$module_access_type = has_access(60);
}else{
$module_access_type = has_access(90);
}
$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Payment";
if(empty($agent_id)){
$breadcrumbes[2]['title'] = "Commissions";
$breadcrumbes[2]['link'] = 'payment_commissions.php';
$breadcrumbes[3]['title'] = 'Weekly Commission Period ('.date("m/d/Y",strtotime($_GET['pay_period'])).')';
}else{
$breadcrumbes[2]['title'] = "Commission Agent";
$breadcrumbes[2]['link'] = 'agents_commissions.php';
$breadcrumbes[3]['title'] = "Commissions";
$breadcrumbes[3]['link'] = 'payment_commissions.php?agent_id='.$agent_id;
$breadcrumbes[4]['title'] = 'Weekly Commission Period ('.date("m/d/Y",strtotime($_GET['pay_period'])).')';
}

$pay_period = checkIsset($_GET['pay_period']);

if(empty($pay_period)){
	redirect("payment_commissions.php");
}

$is_ajax = isset($_GET['is_ajax']) ? $_GET['is_ajax'] : '';
if (isset($_GET["is_ajax"])) {
    include 'tmpl/weekly_commissions_status.inc.php';
    exit;
}

// Read Commisssions Page activity code start
  $description['ac_message'] =array(
    'ac_red_1'=>array(
      'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
      'title'=>$_SESSION['admin']['display_id'],
    ),
    'ac_message_1' =>' read commissions page ',
  ); 

  activity_feed(3, $_SESSION['admin']['id'], 'Admin',0, 'commission','Read Commission', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
// Read Commisssions Page activity code ends


$template = 'weekly_commissions_status.inc.php';
include_once 'layout/end.inc.php';
?>

