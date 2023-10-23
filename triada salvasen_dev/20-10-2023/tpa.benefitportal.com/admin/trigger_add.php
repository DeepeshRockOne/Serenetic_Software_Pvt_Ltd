<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
has_access(10);
   
$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Emailer Dashboard';
$breadcrumbes[1]['link'] = 'emailer_dashboard.php';
$breadcrumbes[2]['title'] = 'Triggers';
$breadcrumbes[2]['link'] = 'triggers.php';
$breadcrumbes[3]['title'] = 'Add Trigger';
$breadcrumbes[3]['class'] = 'Active';

$page_title = "Add Trigger";

$validate = new Validation();
$adminId = $_SESSION['admin']['id'];

$csql = "SELECT tc.id,tc.title,c.company_name 
          FROM trigger_category tc
          LEFT JOIN company c ON (c.id = tc.company_id)";
$crs = $pdo->select($csql);

$templateSql = "SELECT id,title FROM trigger_template WHERE is_deleted='N' ";
$templatedata = $pdo->select($templateSql);

$company_sql = "SELECT * FROM company";
$company_res = $pdo->select($company_sql);

$category = $_GET['category_id'];
$company_id = $_GET['company_id'];
$REAL_IP_ADDRESS = get_real_ipaddress();
if (isset($_POST['save'])) {
  $category = $_POST['category'];
  $company_id = $_POST['company_id'];
  $title = $_POST['title'];
  $type = $_POST['type'];
  $description = $_POST['description'];
  $email_subject = $_POST['email_subject'];
  $email_content = $_POST['email_content'];
  $sms_content = $_POST['sms_content'];
  $template_id = $_POST['template'];
	  
  $validate->string(array('required' => true, 'field' => 'template', 'value' => $template_id), array('required' => 'Please select Template'));
  $validate->string(array('required' => true, 'field' => 'category', 'value' => $category), array('required' => 'Please select category'));
  $validate->string(array('required' => true, 'field' => 'company_id', 'value' => $company_id), array('required' => 'Please select company'));
  $validate->string(array('required' => true, 'field' => 'title', 'value' => $title), array('required' => 'Title is required'));
  $validate->string(array('required' => true, 'field' => 'description', 'value' => $description), array('required' => 'Description is required'));
  $validate->string(array('required' => true, 'field' => 'type', 'value' => $type), array('required' => 'Please select type'));
  
  if($type == 'Email' || $type == 'Both'){
    $validate->string(array('required' => true, 'field' => 'email_subject', 'value' => $email_subject), array('required' => 'Subject is required'));
    $validate->string(array('required' => true, 'field' => 'email_content', 'value' => $email_content), array('required' => 'Content is required'));
  }
  if($type == 'SMS' || $type == 'Both'){
    $validate->string(array('required' => true, 'field' => 'sms_content', 'max' => 160, 'value' => $sms_content), array('required' => 'Content is required'));
  }

  if($validate->isValid()){
      $triggerIns = array(
        'category_id' => makeSafe($category),
        'template_id' => makeSafe($template_id),
        'title' => makeSafe($title),
        'description' => makeSafe($description),
        'type' => makeSafe($type),
        'is_visible' => 'Y',
        'ip_address' => !empty($REAL_IP_ADDRESS['original_ip_address']) ? $REAL_IP_ADDRESS['original_ip_address']:$REAL_IP_ADDRESS['ip_address'],
        'admin_id' => $adminId,
        'create_at' => 'msqlfunc_NOW()',
        'update_at' => 'msqlfunc_NOW()'
      );
      
      if($company_id != ''){
          $triggerIns['company_id'] = $company_id;
      }
      if($type == 'Email'|| $type == 'Both' ){
        $triggerIns['email_subject'] = $email_subject;
        $triggerIns['email_content'] = $email_content;
      }
      
      if($type == 'SMS' || $type == 'Both'){
        $triggerIns['sms_content'] = $sms_content;
      }
      
      $newid = $pdo->insert("triggers",$triggerIns);
      /* Code for audit log*/
        $user_data = get_user_data($_SESSION['admin']);
        audit_log_new($user_data, $_SESSION['admin']['id'], "Admin", "New Trigger Insert And ID is :".$newid, '', $triggerIns, 'new trigger created by admin');
      /* End Code for audit log*/
 
    
	  setNotifySuccess('Trigger added successfully.');
	  redirect('triggers.php');
  }
}

$errors = $validate->getErrors();

$template = "trigger_add.inc.php";
$exJs=array('thirdparty/ckeditor/ckeditor.js');
include_once 'layout/end.inc.php';
?>
