<?php

include_once dirname(__FILE__) . '/layout/start.inc.php';
has_access(10);

$referer = basename($_SERVER['HTTP_REFERER']);
$validate = new Validation();
$adminId = $_SESSION['admin']['id'];
$id = $_GET['id'];
$page = "";
if (isset($_GET['page'])) {
  $page = $_GET['page'];
}

$csql = "SELECT tc.id,tc.title,c.company_name 
        FROM trigger_category tc 
        LEFT JOIN company c ON (c.id = tc.company_id)";
$crs = $pdo->select($csql);

$templateSql = "SELECT id,title FROM trigger_template WHERE is_deleted='N' ";
$templatedata = $pdo->select($templateSql);

$company_sql = "SELECT id,company_name FROM company";
$company_res = $pdo->select($company_sql);

$sql = "SELECT * FROM triggers WHERE id = :id";
$sql_where = array(':id' => makeSafe($id));
$row = $pdo->selectOne($sql, $sql_where);

$template_id = $row['template_id'];
$category = $row['category_id'];
$company_id = $row['company_id'];
$title = stripslashes($row['title']);
$type = $row['type'];
$description = stripslashes($row['description']);
$email_subject = stripslashes($row['email_subject']);
$email_content = stripslashes($row['email_content']);
$sms_content = stripslashes($row['sms_content']);

if (isset($_POST['save'])) {
  $category = $_POST['category'];
  $company_id = $_POST['company_id'];
  $title = $_POST['title'];
  $description = $_POST['description'];
  $type = $_POST['type'];
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

  if ($type == 'Email' || $type == 'Both') {
    $validate->string(array('required' => true, 'field' => 'email_subject', 'value' => $email_subject), array('required' => 'Subject is required'));
    $validate->string(array('required' => true, 'field' => 'email_content', 'value' => $email_content), array('required' => 'Content is required'));
  }
  if ($type == 'SMS' || $type == 'Both') {
    $validate->string(array('required' => true, 'field' => 'sms_content', 'max' => 160, 'value' => $sms_content), array('required' => 'Content is required'));
  }
  if ($validate->isValid()) {
    $update_params = array(
        'category_id' => makeSafe($category),
        'template_id' => makeSafe($template_id),
        'title' => makeSafe($title),
        'type' => makeSafe($type),
        'description' => makeSafe($description),
        'update_at' => 'msqlfunc_NOW()'
    );
    if($company_id != ''){
        $update_params['company_id'] = $company_id;
    }
    if ($type == 'Email' || $type == 'Both') {
      $update_params['email_subject'] = $email_subject;
      $update_params['email_content'] = $email_content;
    }
    if ($type == 'SMS' || $type == 'Both') {
      $update_params['sms_content'] = $sms_content;
    }
    $update_where = array(
        'clause' => 'id = :id',
        'params' => array(
            ':id' => makeSafe($id)
        )
    );
    
    //Audit log code start 
    if ($row > 0) { 
    
    $update_params_new = $update_params;
    unset($update_params_new['update_at']);
    
    foreach($update_params_new as $key=>$up_params){
      $extra_column.=",".$key;
    }
    if($extra_column!=''){ 
      $extra_column=trim($extra_column,',');
      
      $select_customer_data="SELECT ".$extra_column." FROM triggers WHERE id = :id";
      $select_customer_where=array(':id'=>$id);
      
      $result_audit_customer_data=$pdo->selectOne($select_customer_data,$select_customer_where);
    } 

    $pdo->update("triggers", $update_params, $update_where);
    $user_data = get_user_data($_SESSION['admin']);
    
    audit_log_new($user_data, $_SESSION['admin']['id'], "Admin", "Trigger Email Update Rep Id is ".$id, $result_audit_customer_data, $update_params_new, 'trigger email update by admin');
  }
    //Audit log code end
    setNotifySuccess('Trigger updated successfully.');

    if ($page != "") {
      redirect('active_triggers.php', true);
    } else {
      redirect('triggers.php', true);
    }
  }
}

$errors = $validate->getErrors();
$template = "trigger_edit.inc.php";
$exJs = array('thirdparty/ckeditor/ckeditor.js');
$layout = "iframe.layout.php";
include_once 'layout/end.inc.php';
?>
