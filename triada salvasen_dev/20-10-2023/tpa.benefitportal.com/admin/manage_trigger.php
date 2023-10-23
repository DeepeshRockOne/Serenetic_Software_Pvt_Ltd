<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Communications';
$breadcrumbes[2]['title'] = 'Triggers';
$breadcrumbes[2]['link'] = 'triggers.php';
$breadcrumbes[3]['title'] = '+ Trigger';
$breadcrumbes[3]['link'] = 'manage_trigger.php';

$summernote = "Y";

$from_email = get_app_settings('default_email_from');
$from_name = get_app_settings('default_from_name');

$triggerId = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

$triggerRes =array();
$triProductRes =array();
$email_attachment_id = 0;
$attachmentRow = array();

if(!empty($triggerId)){
    $triggerSql = "SELECT * FROM triggers WHERE md5(id)=:id AND is_deleted='N'";
    $triggerParams = array(":id" => $triggerId);
    $triggerRes = $pdo->selectOne($triggerSql,$triggerParams);

    // Read trigger code start
    if($triggerRes){

      if($action == 'Clone'){
        $attachmentSql = "SELECT *
                      FROM trigger_attachment
                      WHERE is_deleted = 'N' AND trigger_id = :id ORDER BY id";
        $attachmentRow = $pdo->select($attachmentSql, array(":id" => $triggerRes['id']));
        $temp_ids = array();
        if($attachmentRow){
          foreach ($attachmentRow as $k => $v) {
            unset($v['id']);
            unset($v['trigger_id']);
            $insert_id = $pdo->insert('trigger_attachment',$v);
            array_push($temp_ids, $insert_id);
          }
          $email_attachment_id = implode(',', $temp_ids);
          $attachmentSql = "SELECT *
                          FROM trigger_attachment
                          WHERE is_deleted = 'N' AND id IN(".implode(',', $temp_ids).") ORDER BY id";
          $attachmentRow = $pdo->select($attachmentSql);
        }
      }else{
        $attachmentSql = "SELECT *
                      FROM trigger_attachment
                      WHERE is_deleted = 'N' AND trigger_id = :id ORDER BY id";
        $attachmentRow = $pdo->select($attachmentSql, array(":id" => $triggerRes['id']));
        $temp_ids = array();
        if($attachmentRow){
          foreach ($attachmentRow as $k => $v) {
            array_push($temp_ids, $v['id']);
          }
          $email_attachment_id = implode(',', $temp_ids);
        }
      }

      $description['ac_message'] =array(
        'ac_red_1'=>array(
          'href'=>$ADMIN_HOST.'/admin_profile.php?id='.$_SESSION['admin']['id'],
          'title'=>$_SESSION['admin']['display_id'],
        ),
        'ac_message_1' =>' read trigger ',
        'ac_red_2'=>array(
            'href'=>$ADMIN_HOST.'/manage_trigger.php?id='. $triggerId,
            'title'=>$triggerRes['display_id'],
        ),
      ); 
      activity_feed(3, $_SESSION['admin']['id'], 'Admin', $triggerRes['id'], 'triggers','Read Trigger', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
    }

    $triProductSql = "SELECT GROUP_CONCAT(product_id) as product_ids FROM trigger_products WHERE md5(trigger_id)=:id AND is_deleted='N'";
    $triProductParams = array(":id" => $triggerId);
    $triProductRes = $pdo->selectOne($triProductSql,$triProductParams);
}

    $triProductsArr = !empty($triProductRes['product_ids']) ? explode(',', $triProductRes['product_ids']) : array();

    
    $title = (!empty($triggerRes['title']) && $action != 'Clone') ? $triggerRes['title'] : '';
    $company_id = !empty($triggerRes['company_id']) ? $triggerRes['company_id'] : '';
    $type = !empty($triggerRes['type']) ? $triggerRes['type'] : '';


    $user_group = !empty($triggerRes['user_group']) ? $triggerRes['user_group'] : '';

    $trigger_action = !empty($triggerRes['trigger_action']) ? $triggerRes['trigger_action'] : '';
    $specifically = !empty($triggerRes['specifically']) ? $triggerRes['specifically'] : '';
    $days_prior = !empty($triggerRes['days_prior']) ? $triggerRes['days_prior'] : '';
    $effective_date = !empty($triggerRes['effective_date']) && isValidDate($triggerRes['effective_date']) ? date('m/d/Y',strtotime($triggerRes['effective_date'])) : '';
    
    $trigger_delay_type = !empty($triggerRes['trigger_delay_type']) ? $triggerRes['trigger_delay_type'] : '';
    $numbers_to_delay = !empty($triggerRes['numbers_to_delay']) ? $triggerRes['numbers_to_delay'] : '';
    $time_units = !empty($triggerRes['time_units']) ? $triggerRes['time_units'] : '';
    $delay_until_date = strtotime($triggerRes['delay_until_date']) > 0 ? date('m/d/Y',strtotime($triggerRes['delay_until_date'])) : '';
   
    $template_id = !empty($triggerRes['template_id']) ? $triggerRes['template_id'] : '';
    $from_email = !empty($triggerRes['from_email']) ? $triggerRes['from_email'] : $from_email;
    $from_name = !empty($triggerRes['from_name']) ? $triggerRes['from_name'] : $from_name;
    $email_subject = !empty($triggerRes['email_subject']) ? $triggerRes['email_subject'] : '';
    $email_content = !empty($triggerRes['email_content']) ? stripslashes($triggerRes['email_content']) : '';
    
    $to_email_specific = !empty($triggerRes['to_email_specific']) ? $triggerRes['to_email_specific'] : '';
    $cc_email_specific = !empty($triggerRes['cc_email_specific']) ? $triggerRes['cc_email_specific'] : '';
    $bcc_email_specific = !empty($triggerRes['bcc_email_specific']) ? $triggerRes['bcc_email_specific'] : '';
    $to_email_user = !empty($triggerRes['to_email_user']) ? $triggerRes['to_email_user'] : '';
    $cc_email_user = !empty($triggerRes['cc_email_user']) ? $triggerRes['cc_email_user'] : '';
    $bcc_email_user = !empty($triggerRes['bcc_email_user']) ? $triggerRes['bcc_email_user'] : '';

    $to_phone_specific = !empty($triggerRes['to_phone_specific']) ? $triggerRes['to_phone_specific'] : '';  
    $to_phone_user = !empty($triggerRes['to_phone_user']) ? $triggerRes['to_phone_user'] : '';  
    $sms_content = !empty($triggerRes['sms_content']) ? stripslashes($triggerRes['sms_content']) : '';  


// get company code start
$companyRes = $pdo->select("SELECT id,company_name FROM prd_company WHERE is_deleted = 'N' ORDER BY company_name ASC");

// get template code start
$templateSql = "SELECT id,title FROM trigger_template WHERE is_deleted='N' ";
$templateRes = $pdo->select($templateSql);

// get produts code start
$prdSql = "SELECT p.id,p.name,p.category_id,pc.title,p.type,p.product_code
                  FROM prd_main as p
                  JOIN prd_category as pc ON (p.category_id = pc.id) 
                  WHERE p.is_deleted = 'N' ORDER BY pc.title,p.name ASC";
$prdRes = $pdo->select($prdSql);
if(!empty($prdRes)){
  $companyArr = array();
  foreach ($prdRes as $key => $row) {
    if (!array_key_exists($row['title'], $companyArr)) {
      $companyArr[$row['title']] = array();
    }
    array_push($companyArr[$row['title']], $row);
  }
}

$exStylesheets = array('thirdparty/multiple-select-master/multiple-select.css');
$exJs = array("thirdparty/ajax_form/jquery.form.js",'thirdparty/multiple-select-master/multiple-select-old/jquery.multiple.select.js','thirdparty/masked_inputs/jquery.inputmask.bundle.js', 'thirdparty/ckeditor/ckeditor.js');

$page_title = "Email";
$template = 'manage_trigger.inc.php';
$layout = 'main.layout.php';
include_once 'layout/end.inc.php';
?>
