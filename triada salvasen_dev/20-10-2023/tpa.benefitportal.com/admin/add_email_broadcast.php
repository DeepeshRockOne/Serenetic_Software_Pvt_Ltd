<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = "Communications";
$breadcrumbes[2]['title'] = "Email";
$breadcrumbes[2]['link'] = 'emailer_dashboard.php';
$breadcrumbes[3]['title'] = "Broadcaster";
$breadcrumbes[3]['link'] = 'emailer_broadcaster.php';
$breadcrumbes[4]['title'] = "+ Broadcaster";
$breadcrumbes[4]['class'] = "add_email_broadcast.php";

$summernote = "Y";
$company_id = 3;
$is_clone = isset($_GET['is_clone']) ? $_GET['is_clone'] : "N";
$templateSql = "SELECT id,title FROM trigger_template WHERE is_deleted='N' ";
$templatedata = $pdo->select($templateSql);
$email_attachment_id = 0;
$attachmentRow = array();
$broadcast_from_address = $emailer_settings[$company_id]['tg_from_mailid'];

$company_arr = get_active_global_products_for_filter();
$setting_keys = array(
          'default_email_from',
          'default_from_name',
        );
$app_setting_res = get_app_settings($setting_keys);

// admin values start
$acl_features = array();
$sql_acl = "SELECT id,name,dashboard,feature_access 
            FROM access_level where feature_access !='' 
            ORDER BY name ASC"; 
$acls = $pdo->select($sql_acl);

if(!empty($acls)){
    foreach($acls as $acll){
        $acl_names[] = $acll['name'];
        $acl[$acll['id']] = $acll['name'];
        $acl_features[$acll['name']] = explode(',', $acll['feature_access']);
    }
}

$sel_admins = "SELECT id,display_id,CONCAT(fname,' ', lname) as name FROM admin WHERE is_active='Y' AND is_deleted='N' AND status = 'Active'";
$res_admins = $pdo->select($sel_admins);
//  admin values end

// Agent, Member and Group value start
$sel_customer = "SELECT id,rep_id,CONCAT(fname,' ', lname) as name,type FROM customer WHERE is_deleted='N' AND status NOT IN ('Customer Abandon','Pending Quote','Invited') AND type IN ('Agent','Group','Customer')";
$res_customer = $pdo->select($sel_customer);

$agent_res = array();
$group_res = array();
$member_res = array();
if(!empty($res_customer) && count($res_customer) > 0) {
	$agent_count = 0;
	$group_count = 0;
	$member_count = 0;
	foreach ($res_customer as $key => $value) {
		if($value['type'] == 'Agent') {
			$agent_res[$agent_count] = $value;
			$agent_count++;
		} else if($value['type'] == 'Group') {
			$group_res[$group_count] = $value;
			$group_count++;
		} else {
			$member_res[$member_count] = $value;
			$member_count++;
		}
	}
}
// Agent, Member and Group value end

// Leads Value start
$lead_tag_sql = "SELECT id,opt_in_type,lead_id,CONCAT(fname,' ',lname) as lead_name FROM leads  WHERE is_deleted='N' AND status NOT IN ('Converted',' ','NULL')";
$lead_res = $pdo->select($lead_tag_sql);

$lead_tag_res = array();
if(!empty($lead_res) && count($lead_res) > 0){
  foreach ($lead_res as $key => $value) {
    if(!in_array($value['opt_in_type'], $lead_tag_res)){
      array_push($lead_tag_res, $value['opt_in_type']);
    }
  }
}
// Leads Value End

$agent_levels = $pdo->select("SELECT id,level from agent_coded_level WHERE is_active = 'Y' ORDER BY id desc");
$states = $pdo->select("SELECT id,name from states_c WHERE country_id = 231 AND is_deleted = 'N'");

$broadcaster_res = array();
$future_check_box = 'N';
$div_counter_in_php = 0;
$specific_agent_level = array(); 
$agent_status = array(); 
$enrolling_agent = array(); 
$member_state = array();
$specific_user_ids_array = ""; 
if(isset($_GET['broadcaster_id']) && !empty($_GET['broadcaster_id'])){
  $broadcaster_id = $_GET['broadcaster_id'];
  $broadcaster_res = $pdo->selectOne("SELECT id, brodcast_name, display_id, from_address, subject, user_type, email_template_id, mail_content, is_for_specific,specific_agent_level,agent_status,enrolling_agent_ids,tree_agent_ids,states, admin_level, specific_user_ids, product_ids, product_status, lead_tags, is_schedule_in_future, status FROM broadcaster WHERE type='email' AND md5(id) = :broadcaster_id", array(":broadcaster_id" => $broadcaster_id));

  if(!empty($broadcaster_res)){

    $user_type = !empty($broadcaster_res['user_type']) ? $broadcaster_res['user_type'] : '';
    $specific_user_group = !empty($broadcaster_res['is_for_specific']) ? $broadcaster_res['is_for_specific'] : 'N';
    $admin_level_array = !empty($broadcaster_res['admin_level']) ? explode(",", $broadcaster_res['admin_level']) : array();
    $specific_user_ids_array = !empty($broadcaster_res['specific_user_ids']) ? explode(',',$broadcaster_res['specific_user_ids']) : "";
    $product_ids_array = !empty($broadcaster_res['product_ids']) ? explode(",", $broadcaster_res['product_ids']) : array();
    $product_status = !empty($broadcaster_res['product_status']) ? $broadcaster_res['product_status'] : "";
    $lead_tags_array = !empty($broadcaster_res['lead_tags']) ? explode(",", $broadcaster_res['lead_tags']) : array();
    $future_check_box = $broadcaster_res['is_schedule_in_future'];
    $specific_agent_level = $broadcaster_res['specific_agent_level'] ? explode(',', $broadcaster_res['specific_agent_level']) : array();
    $agent_status = $broadcaster_res['agent_status'] ? explode(',', $broadcaster_res['agent_status']) : array();
    $enrolling_agent = !empty($broadcaster_res['enrolling_agent_ids']) ? explode(',', $broadcaster_res['enrolling_agent_ids']) : array();
    $tree_agent_ids = !empty($broadcaster_res['tree_agent_ids']) ? explode(',', $broadcaster_res['tree_agent_ids']) : array();
    $member_state = !empty($broadcaster_res['states']) ? explode(',', $broadcaster_res['states']) : array();
    
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
        'href'=>$ADMIN_HOST.'/admin_profile.php?id='.$_SESSION['admin']['id'],
        'title'=>$_SESSION['admin']['display_id'],
      ),
      'ac_message_1' =>' Read Email Broadcaster ',
      'ac_red_2'=>array(
          'href'=>$ADMIN_HOST.'/add_email_broadcast.php?broadcaster_id='.md5($broadcaster_res['id']),
          'title'=>$broadcaster_res['display_id'],
      ),
    ); 

    activity_feed(3, $_SESSION['admin']['id'], 'Admin', $broadcaster_res['id'], 'email_broadcaster','Read Email Broadcaster', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));

  } else {
    setNotifyError("No record Found!");
    redirect("add_email_broadcast.php");
  }
}

$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css', 'thirdparty/malihu_scroll/css/jquery.mCustomScrollbar.css');
$exJs = array("thirdparty/ajax_form/jquery.form.js",'thirdparty/multiple-select-master/jquery.multiple.select.js','thirdparty/masked_inputs/jquery.inputmask.bundle.js', 'thirdparty/malihu_scroll/js/jquery.mCustomScrollbar.concat.min.js', 'thirdparty/ckeditor/ckeditor.js');

$template = 'add_email_broadcast.inc.php';
include_once 'layout/end.inc.php';
?>
