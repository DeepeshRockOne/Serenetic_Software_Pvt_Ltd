<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
group_has_access(8);
$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Resources";
$breadcrumbes[2]['title'] = "Communications";
$breadcrumbes[2]['link'] = "communications_queue.php";
$breadcrumbes[3]['title'] = "Email Broadcaster";
$breadcrumbes[3]['link'] = 'add_email_broadcast.php';

$group_id = $_SESSION["groups"]["id"];
$summernote = "Y";
$company_id = 3;
$is_clone = isset($_GET['is_clone']) ? $_GET['is_clone'] : "N";
$templateSql = "SELECT id,title FROM trigger_template WHERE is_deleted='N' ";
$templatedata = $pdo->select($templateSql);
$email_attachment_id = 0;
$attachmentRow = array();
$broadcast_from_address = $emailer_settings[$company_id]['tg_from_mailid'];

// product code start
  $company_arr = get_active_global_products_for_filter($group_id,false,false,true);
// product code ends

// Member value code
  $member_res = array();
  $member_count = 0;
  $selCustomer = "SELECT id,rep_id,CONCAT(fname,' ', lname) as name,type FROM customer WHERE is_deleted='N' AND status NOT IN ('Customer Abandon','Pending Quote','Invited') AND type IN ('Customer') AND sponsor_id = :sponsor_id";
  $resCustomer = $pdo->select($selCustomer,array(":sponsor_id"=>$group_id));

  if(!empty($resCustomer)){
    foreach ($resCustomer as $key => $row) {
        $member_res[$member_count] = $row;
        $member_count++;
    }
  }
// Member value code end

// Leads(Enrollee) Value Start
  $lead_tag_sql = "SELECT id,opt_in_type,lead_id,CONCAT(fname,' ',lname) as lead_name FROM leads  WHERE is_deleted='N' AND status NOT IN ('Converted',' ','NULL') AND sponsor_id=:sponsor_id";
  $lead_res = $pdo->select($lead_tag_sql,array(":sponsor_id" => $group_id));

  $lead_tag_res = array();
  if(!empty($lead_res) && count($lead_res) > 0){
    foreach ($lead_res as $key => $value) {
      if(!in_array($value['opt_in_type'], $lead_tag_res)){
        array_push($lead_tag_res, $value['opt_in_type']);
      }
    }
  }
// Leads(Enrollee) Value End

$broadcaster_res = array();
$future_check_box = 'N';
$div_counter_in_php = 0; 
if(isset($_GET['broadcaster_id']) && !empty($_GET['broadcaster_id'])){
  $broadcaster_id = $_GET['broadcaster_id'];
  $broadcaster_res = $pdo->selectOne("SELECT id, brodcast_name, display_id, from_address, subject, user_type, email_template_id, mail_content, is_for_specific, admin_level, specific_user_ids, product_ids, product_status, lead_tags, is_schedule_in_future, status FROM broadcaster WHERE type='email' AND md5(id) = :broadcaster_id", array(":broadcaster_id" => $broadcaster_id));

  if(!empty($broadcaster_res)){

    $user_type = !empty($broadcaster_res['user_type']) ? $broadcaster_res['user_type'] : '';
    $specific_user_group = !empty($broadcaster_res['is_for_specific']) ? $broadcaster_res['is_for_specific'] : 'N';
    $admin_level_array = !empty($broadcaster_res['admin_level']) ? explode(",", $broadcaster_res['admin_level']) : array();
    $specific_user_ids_array = !empty($broadcaster_res['specific_user_ids']) ? explode(",", $broadcaster_res['specific_user_ids']) : array();
    $product_ids_array = !empty($broadcaster_res['product_ids']) ? explode(",", $broadcaster_res['product_ids']) : array();
    $product_status = !empty($broadcaster_res['product_status']) ? $broadcaster_res['product_status'] : "";
    $lead_tags_array = !empty($broadcaster_res['lead_tags']) ? explode(",", $broadcaster_res['lead_tags']) : array();
    $future_check_box = $broadcaster_res['is_schedule_in_future'];

    $broadcaster_schedule_settings_res = $pdo->select("SELECT id,schedule_date,schedule_hour,time_zone FROM broadcaster_schedule_settings WHERE broadcaster_id = :broadcaster_id AND is_deleted = 'N'", array(":broadcaster_id" => $broadcaster_res['id']));
    if($is_clone == "Y"){
      $broadcaster_schedule_settings_res = array();
      $future_check_box = 'N';

      $attachmentSql = "SELECT *
                      FROM email_attachment
                      WHERE is_deleted = 'N' AND broadcast_id = :id ORDER BY id";
      $attachmentRow = $pdo->select($attachmentSql, array(":id" => $broadcaster_res['id']));
      $temp_ids = array();
      if($attachmentRow){
        foreach ($attachmentRow as $k => $v) {
          unset($v['id']);
          unset($v['broadcast_id']);
          $insert_id = $pdo->insert('email_attachment',$v);
          array_push($temp_ids, $insert_id);
        }
        $email_attachment_id = implode(',', $temp_ids);
        $attachmentSql = "SELECT *
                        FROM email_attachment
                        WHERE is_deleted = 'N' AND id IN(".implode(',', $temp_ids).") ORDER BY id";
        $attachmentRow = $pdo->select($attachmentSql);
      }


    }else{
      $attachmentSql = "SELECT *
                      FROM email_attachment
                      WHERE is_deleted = 'N' AND broadcast_id = :id ORDER BY id";
      $attachmentRow = $pdo->select($attachmentSql, array(":id" => $broadcaster_res['id']));
    }
    if($future_check_box == 'Y' && !empty($broadcaster_schedule_settings_res) && count($broadcaster_schedule_settings_res) > 0) {
      $div_counter_in_php = count($broadcaster_schedule_settings_res);
    }

    $description['ac_message'] = array(
      'ac_red_1'=>array(
        'href'=>$ADMIN_HOST.'/groups_details.php?id='.md5($_SESSION['groups']['id']),
        'title'=>$_SESSION['groups']['rep_id'],
      ),
      'ac_message_1' =>' Read Email Broadcaster ',
      'ac_red_2'=>array(
          'href'=>$GROUP_HOST.'/add_email_broadcast.php?broadcaster_id='.md5($broadcaster_res['id']),
          'title'=>$broadcaster_res['display_id'],
      ),
    ); 

    activity_feed(3, $_SESSION['groups']['id'], 'Group', $broadcaster_res['id'], 'email_broadcaster','Read Email Broadcaster', $_SESSION['groups']['fname'],$_SESSION['groups']['lname'],json_encode($description));

  } else {
    setNotifyError("No record Founnd!");
    redirect("add_email_broadcast.php");
  }
}

$enrollee_id = 0;
if(!empty($_GET['enrollee'])){
   $sqlEnrolee = "SELECT id FROM leads where md5(id) = :enrollee";
   $resEnrollee = $pdo->selectOne($sqlEnrolee,array(":enrollee"=>$_GET['enrollee']));

   if(!empty($resEnrollee)){
      $enrollee_id = $resEnrollee['id'];
   }
}

$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css', 'thirdparty/malihu_scroll/css/jquery.mCustomScrollbar.css', 'thirdparty/summernote-master/dist/summernote.css');
$exJs = array("thirdparty/ajax_form/jquery.form.js",'thirdparty/multiple-select-master/jquery.multiple.select.js','thirdparty/masked_inputs/jquery.inputmask.bundle.js', 'thirdparty/malihu_scroll/js/jquery.mCustomScrollbar.concat.min.js', 'thirdparty/summernote-master/dist/popper.js', 'thirdparty/summernote-master/dist/summernote.js');

$template = 'add_email_broadcast.inc.php';
include_once 'layout/end.inc.php';
?>
