<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Communications";
$breadcrumbes[2]['title'] = "Text Message (SMS)";
$breadcrumbes[2]['link'] = 'sms_dashboard.php';
$breadcrumbes[3]['title'] = "Broadcaster";
$breadcrumbes[3]['link'] = 'sms_broadcaster.php';
$breadcrumbes[4]['title'] = "+ Broadcaster";
$breadcrumbes[4]['class'] = "add_sms_broadcast.php";

$summernote = "Y";

$broadcasterId = checkIsset($_GET['broadcaster_id']);
$is_clone = checkIsset($_GET['is_clone']) ? $_GET['is_clone'] : "N";
$broadcasterRes = array();
$broadcasterScheduleRes = array();
$broadcasterMessageRes = array();

$future_check_box = 'N';
$div_counter_in_php = 0; 
$msgCnt = 1; 

if(!empty($broadcasterId)){

  $broadcasterRes = $pdo->selectOne("SELECT id, brodcast_name, display_id, from_address, subject, user_type, email_template_id, mail_content, is_for_specific, admin_level, specific_user_ids, product_ids, product_status, lead_tags, is_schedule_in_future, status FROM broadcaster WHERE is_deleted='N' AND type='sms' AND md5(id) = :broadcaster_id", array(":broadcaster_id" => $broadcasterId));

  if(empty($broadcasterRes)){
    redirect("sms_broadcaster.php");
  }


  $broadcasterScheduleRes = $pdo->select("SELECT id,schedule_date,schedule_hour,time_zone FROM broadcaster_schedule_settings WHERE broadcaster_id = :broadcasterId AND is_deleted = 'N'", array(":broadcasterId" => $broadcasterRes['id']));

    if($is_clone == 'Y'){
      $broadcasterScheduleRes = array();
    }
    if($broadcasterRes['is_schedule_in_future'] == 'Y') {
      $div_counter_in_php = count($broadcasterScheduleRes);
    }

   $broadcasterMessageRes = $pdo->select("SELECT id,broadcaster_id,message FROM broadcaster_message WHERE broadcaster_id = :broadcasterId AND is_deleted = 'N'", array(":broadcasterId" => $broadcasterRes['id']));

   $msgCnt = count($broadcasterMessageRes);

    $description['ac_message'] = array(
      'ac_red_1'=>array(
        'href'=>$ADMIN_HOST.'/admin_profile.php?id='.$_SESSION['admin']['id'],
        'title'=>$_SESSION['admin']['display_id'],
      ),
      'ac_message_1' =>' Read SMS Broadcaster ',
      'ac_red_2'=>array(
          'href'=>$ADMIN_HOST.'/add_sms_broadcast.php?broadcaster_id='.md5($broadcasterRes['id']),
          'title'=>$broadcasterRes['display_id'],
      ),
    ); 

    activity_feed(3, $_SESSION['admin']['id'], 'Admin', $broadcasterRes['id'], 'broadcaster','Read SMS Broadcaster', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
}

  	$userGroup = checkIsset($broadcasterRes['user_type']);
  	$broadcastName = checkIsset($broadcasterRes['brodcast_name']);
  	$broadcastStatus = checkIsset($broadcasterRes['status']);

  	$specificUser = checkIsset($broadcasterRes['is_for_specific']);
    $specificUserIdsArr = !empty($broadcasterRes['specific_user_ids']) ? explode(",", $broadcasterRes['specific_user_ids']) : array();
  	
  	$adminLevelArr = !empty($broadcasterRes['admin_level']) ? explode(",", $broadcasterRes['admin_level']) : array(); 
 
    $productIdsArr = !empty($broadcasterRes['product_ids']) ? explode(",", $broadcasterRes['product_ids']) : array();
    $leadTagsArr = !empty($broadcasterRes['lead_tags']) ? explode(",", $broadcasterRes['lead_tags']) : array();
    
    $future_check_box = checkIsset($broadcasterRes['is_schedule_in_future']);

    if($is_clone == 'Y'){
      $future_check_box = 'N';
      $broadcastName = "";
    }




$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css');
$exJs = array('thirdparty/multiple-select-master/jquery.multiple.select.js','thirdparty/masked_inputs/jquery.inputmask.bundle.js');


$template = 'add_sms_broadcast.inc.php';
include_once 'layout/end.inc.php';
?>
