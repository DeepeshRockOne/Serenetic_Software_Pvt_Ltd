<?php   
include_once dirname(__FILE__) . '/layout/start.inc.php';
  

$validate = new Validation();

if (isset($_POST["type"])) {

  $trg_img_check = $_POST['trg_image_check'];
  $trg_address_check = $_POST['trg_address_check'];
  $trg_footer_check =  $_POST['selected_footer_id']; 
  $template_data = $_POST['template_data'];
  $title = $_POST['title'];
  $company_id = $_POST['company_id'];
  $type=$_POST['template_type'];

  $res=array();

  if($type=="custom"){
    $content = $_POST['content'];
  }

  $validate->string(array('required' => true, 'field' => 'title', 'value' => $title), array('required' => 'Template title is required'));
  $validate->string(array('required' => true, 'field' => 'company_id', 'value' => $company_id), array('required' => 'Company name is required'));
  $validate->string(array('required' => true, 'field' => 'template_type', 'value' => $type), array('required' => 'Please select any option'));

  if($type=="default"){
    if (count($trg_img_check) == 0) {
      $validate->setError('trg_image_check', 'Please select at least one logo');
    }
  }


  if($type=="custom"){
    $validate->string(array('required' => true, 'field' => 'content', 'value' => $content), array('required' => 'Enter Custom Conent'));
  }

  if($validate->isValid()){
   
    $params = array(
        'company_id' => $company_id,
        'title' => $title,
        'type' =>$type,
        'created_at' => 'msqlfunc_NOW()'
    );

    if($type=="default"){
      $params['trg_footer_id']=$trg_footer_check;
      $params['content']=$template_data;
    }
    if($type=="custom"){
      $params['content']=$content;
    }
    $trg_template_id = $pdo->insert('trigger_template', $params);
    /* Code for audit log*/
    $user_data = get_user_data($_SESSION['admin']);
    audit_log_new($user_data, $_SESSION['admin']['id'], "Admin", "Trigger Template Insert By ID is :".$trg_template_id, '', $params, 'Trigger Template created by admin');

    /* End Code for audit log*/ 

    if (count($trg_img_check) > 0) {
      foreach ($trg_img_check as $value) {
        $image_params = array(
            'template_id' => makeSafe($trg_template_id),
            'trg_image_id' => makeSafe($value),
            'created_at' => 'msqlfunc_NOW()'
        );
        $trg_image_id=$pdo->insert('trigger_template_images', $image_params);
        /* Code for audit log*/
    $user_data = get_user_data($_SESSION['admin']);
    audit_log_new($user_data, $_SESSION['admin']['id'], "Admin", "Trigger Template Image Insert By ID is :".$trg_image_id, '', $image_params, 'trigger template image created by admin');

    /* End Code for audit log*/
      }
    }

    if (count($trg_address_check) > 0) {
      foreach ($trg_address_check as $value) {
        $address_params = array(
            'template_id' => makeSafe($trg_template_id),
            'trg_address_id' => makeSafe($value),
            'created_at' => 'msqlfunc_NOW()'
        );
        $trg_address_id=$pdo->insert('trigger_template_address', $address_params);
        /* Code for audit log*/
    $user_data = get_user_data($_SESSION['admin']);
    audit_log_new($user_data, $_SESSION['admin']['id'], "Admin", "Trigger Template Address Insert By ID is :".$trg_address_id, '', $address_params, 'trigger template address created by admin');

    /* End Code for audit log*/
      }
    }

    setNotifySuccess('Trigger template Add successfully.');
    $res['msg']='Trigger template added successfully';
    $res['status']='success'; 

  }else{
    $res["status"]="fail";
    $res['error']=$validate->getErrors();
  }
  echo json_encode($res);
  dbConnectionClose();
  exit;
}

?>
