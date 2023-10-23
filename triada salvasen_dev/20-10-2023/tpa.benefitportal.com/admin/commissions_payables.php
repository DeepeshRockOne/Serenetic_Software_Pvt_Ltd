<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Payment";
$breadcrumbes[2]['title'] = "Commissions";
$breadcrumbes[2]['link'] = 'payment_commissions.php';
$breadcrumbes[3]['title'] = "Exports";
$breadcrumbes[3]['link'] = 'commissions_payables.php';

// Read Commisssions Payables activity code start
  $description['ac_message'] =array(
    'ac_red_1'=>array(
      'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
      'title'=>$_SESSION['admin']['display_id'],
    ),
    'ac_message_1' =>' read commission payables ',
  ); 

  activity_feed(3, $_SESSION['admin']['id'], 'Admin',0, 'commission','Read Commission', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
// Read Commisssions Payables activity code ends

$template = 'commissions_payables.inc.php';
include_once 'layout/end.inc.php';
?>


