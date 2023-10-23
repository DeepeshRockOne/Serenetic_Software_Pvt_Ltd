<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$agent_id = isset($_GET['agent_id']) ? $_GET['agent_id'] : '';
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
}else{
$breadcrumbes[2]['title'] = "Commission Agent";
$breadcrumbes[2]['link'] = 'agents_commissions.php';
$breadcrumbes[3]['title'] = "Commissions";
}


$resAgent = $pdo->selectOne("SELECT a.id as agentId,a.rep_id as agentDispId,CONCAT(a.fname,' ',a.lname) as agentName,ss.company_name FROM customer a JOIN customer_settings ss ON(ss.customer_id=a.id) WHERE a.id=1");

$selCommAgent = "SELECT a.id as agentId,a.rep_id as agentDispId,CONCAT(a.fname,' ',a.lname) as agentName
				FROM customer a
				JOIN commission c ON(a.id=c.customer_id)
				WHERE c.commission_duration='weekly' GROUP BY c.customer_id";
$resCommAgent = $pdo->select($selCommAgent);

$selCommAgency = "SELECT a.id as agentId,a.rep_id as agentDispId,ss.company_name as agentName
        FROM customer a
        JOIN customer_settings ss ON(ss.customer_id=a.id)
        JOIN commission c ON(a.id=c.customer_id)
        WHERE ss.account_type = 'Business' AND c.commission_duration='weekly' GROUP BY c.customer_id";
$resCommAgency = $pdo->select($selCommAgency);

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



$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css');
$exJs = array('thirdparty/multiple-select-master/multiple-select-old/jquery.multiple.select.js');

$template = 'payment_commissions.inc.php';
include_once 'layout/end.inc.php';
?>