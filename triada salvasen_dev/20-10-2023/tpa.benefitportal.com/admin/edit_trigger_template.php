<?php 

include_once dirname(__FILE__) . '/layout/start.inc.php';
has_access(10);

$breadcrumbes[0]['title'] = '<i class="fa fa-home"></i>';
$breadcrumbes[0]['link'] = 'dashboard.php';
$breadcrumbes[1]['title'] = 'Emailar Dashboard';
$breadcrumbes[1]['link'] = 'emailer_dashboard.php';
$breadcrumbes[2]['title'] = 'Trigger Template';
$breadcrumbes[2]['link'] = 'trigger_template.php';
$breadcrumbes[3]['title'] = 'Edit Trigger Template';

$validate = new Validation();

$id = $_GET['id'];
$template_query = "SELECT t.*, ti.trg_image_id, ti.id as ti_id, ta.trg_address_id, ta.id as ta_id
                   FROM trigger_template t
                   LEFT JOIN trigger_template_images ti ON(t.id=ti.template_id)
                   LEFT JOIN trigger_template_address ta ON(t.id=ta.template_id)
                   WHERE t.id=:id";
$where = array(
    ':id' => $id
);
$template_val = $pdo->select($template_query, $where);

$templateArray = array();
$template_data_id = 0;
$template_image_id = 0;
$template_image_src = '';
$template_add_src = '';
$template_add_id = 0;


if (count($template_val) > 0) {
  $i = 0;

  foreach ($template_val as $value) {

    if ($value['id'] != $template_data_id) {
      $templateArray['id'] = $value['id'];
      $title = $value['title'];
      $company_id = $value['company_id'];
      $trg_footer_check = $value['trg_footer_id'];
      $trg_img_check = array();
      $trg_address_check = array();
      $old_trg_img_check = array();
      $old_trg_address_check = array();
      $type=$value['type'];
      if($type=='custom'){
        $content=$value['content'];
      }
    }
    if ($value['trg_image_id'] != $template_image_id) {
      $trg_img_check[$value['trg_image_id']] = $value['trg_image_id'];
      $old_trg_img_check[$value['trg_image_id']] = $value['ti_id'];
    }
    
    if ($value['trg_address_id'] != $template_add_id) {
      $trg_address_check[$value['trg_address_id']] = $value['trg_address_id'];
      $old_trg_address_check[$value['trg_address_id']] = $value['ta_id'];
    }
    $i++;
    $template_data_id = $value['id'];
    $template_image_id = $value['trg_image_id'];
    $template_add_id = $value['trg_address_id'];
    $template_image_src = $value['src'];
    $template_add_id = $value['address'];
  }
}

if (isset($_POST['type'])) {
  $id=$_POST['id'];
  $template_query = "SELECT t.*, ti.trg_image_id, ti.id as ti_id, ta.trg_address_id, ta.id as ta_id
                     FROM trigger_template t
                     LEFT JOIN trigger_template_images ti ON(t.id=ti.template_id)
                     LEFT JOIN trigger_template_address ta ON(t.id=ta.template_id)
                     WHERE t.id=:id";
  $where = array(
      ':id' => $id
  );
  $template_val = $pdo->select($template_query, $where);
  $templateArray = array();
  $template_data_id = 0;
  $template_image_id = 0;
  $template_image_src = '';
  $template_add_src = '';
  $template_add_id = 0;

  if (count($template_val) > 0) {
    $i = 0;

    foreach ($template_val as $value) {

      if ($value['id'] != $template_data_id) {
        $templateArray['id'] = $value['id'];
        $title = $value['title'];
        $company_id = $value['company_id'];
        $trg_footer_check = $value['trg_footer_id'];
        $trg_img_check = array();
        $trg_address_check = array();
        $old_trg_img_check = array();
        $old_trg_address_check = array();
        $type=$value['type'];
        if($type=='custom'){
          $content=$value['content'];
        }
      }
      if ($value['trg_image_id'] != $template_image_id) {
        $trg_img_check[$value['trg_image_id']] = $value['trg_image_id'];
        $old_trg_img_check[$value['trg_image_id']] = $value['ti_id'];
      }
      
      if ($value['trg_address_id'] != $template_add_id) {
        $trg_address_check[$value['trg_address_id']] = $value['trg_address_id'];
        $old_trg_address_check[$value['trg_address_id']] = $value['ta_id'];
      }
      $i++;
      $template_data_id = $value['id'];
      $template_image_id = $value['trg_image_id'];
      $template_add_id = $value['trg_address_id'];
      $template_image_src = $value['src'];
      $template_add_id = $value['address'];
    }
  }
  $trg_img_check = $_POST['trg_image_check'];
  $trg_address_check = $_POST['trg_address_check'];
  $trg_footer_check = $_POST['trg_footer_check'];
  $template_data = $_POST['template_data'];
  $title = $_POST['title'];
  $company_id = $_POST['company_id'];
  $type=$_POST['template_type'];
  
 
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

  if ($validate->isValid()) {

    $params = array(
        'company_id' => $company_id,
        'title' => makeSafe($title),
        'type' =>$type,
        'updated_at' => 'msqlfunc_NOW()'
    );
    if($type=="default"){
      $params['trg_footer_id']=makeSafe($trg_footer_check);
      $params['content']=makeSafe($template_data);
    }
    if($type=="custom"){
      $params['content']=$content;
    }
    $where = array(
        'clause' => 'id=:id',
        'params' => array(
            ':id' => makeSafe($id)
        )
    );
    
    /* Code for audit log*/
  
      $update_params_new=$params;
      unset($update_params_new['updated_at']);
      foreach($update_params_new as $key_audit=>$audit_params)
      {
        $extra_column.=",".$key_audit;
      }
      if($extra_column!='')
      {
        $extra_column=trim($extra_column,',');

        $select_customer_data="SELECT ".$extra_column." FROM trigger_template WHERE id=:id";
        $select_customer_where=array(':id'=>$id);

        $result_audit_customer_data=$pdo->selectOne($select_customer_data,$select_customer_where);
      } 

      /* End Code for audit log*/

        $pdo->update('trigger_template', $params, $where);   
      
      /* Code for audit log*/
      $user_data = get_user_data($_SESSION['admin']);
      audit_log_new($user_data, $_SESSION['admin']['id'], "Admin", "Trigger Template Updated And Id is ".$id, $result_audit_customer_data, $update_params_new, 'trigger template updated by admin');

/* End Code for audit log*/
   

    if (count($old_trg_img_check) > 0) {
      foreach ($old_trg_img_check as $key => $value) {
        if (isset($trg_img_check[$key])) {
          unset($trg_img_check[$key]);
       
        } else {
          
          /* Code for audit log*/
      
              $select_note_data="SELECT * FROM trigger_template_images WHERE id = :id";
              $select_note_where=array(':id' => makeSafe($value));
              $result_audit_note_data=$pdo->selectOne($select_note_data,$select_note_where);
              
          /* End Code for audit log*/

          $sql_delete = "DELETE FROM trigger_template_images WHERE id=:id";
          $pdo->delete($sql_delete,array(":id"=>$value));
          /* Code for audit log*/
              $user_data = get_user_data($_SESSION['admin']);
              audit_log_new($user_data, $_SESSION['admin']['id'], "Admin", "Trigger Template Image Deleted And Id is ".$value, $result_audit_note_data,'', 'trigger template imsage deleted by admin');
          /* End Code for audit log*/
        }
      }
    }
    
    if (count($trg_img_check) > 0) {
      foreach ($trg_img_check as $key => $value) {
        $image_params = array(
            'template_id' => makeSafe($id),
            'trg_image_id' => makeSafe($value),
            'created_at' => 'msqlfunc_NOW()'
        );
          $insert_id = $pdo->insert('trigger_template_images', $image_params);
        /* Code for audit log*/
          $user_data = get_user_data($_SESSION['admin']);
          audit_log_new($user_data, $_SESSION['admin']['id'], "Admin", "Trigger Template Image Insert By ID is :".$insert_id, '', $image_params, 'Trigger Template created by admin');
        /* End Code for audit log*/
      }
    }

    if (count($old_trg_address_check) > 0) {
      foreach ($old_trg_address_check as $key => $value) {
        if (isset($trg_address_check[$key])) {
          unset($trg_address_check[$key]);
        } else {
          /* Code for audit log*/
    
              $select_note_data="SELECT * FROM trigger_template_address WHERE id = :id";
              $select_note_where=array(':id' => makeSafe($value));
              $result_audit_note_data=$pdo->selectOne($select_note_data,$select_note_where);
              
          /* End Code for audit log*/  
          $sql_delete = "DELETE FROM trigger_template_address WHERE id=$value";
          $pdo->delete($sql_delete);
          /* Code for audit log*/
              $user_data = get_user_data($_SESSION['admin']);
              audit_log_new($user_data, $_SESSION['admin']['id'], "Admin", "Trigger Template Address Deleted And Id is ".$value, $result_audit_note_data,'', 'trigger template address deleted by admin');
          /* End Code for audit log*/
        }
      }
    }

    if (count($trg_address_check) > 0) {
      foreach ($trg_address_check as $key => $value) {
        $image_params = array(
            'template_id' => makeSafe($id),
            'trg_address_id' => makeSafe($value),
            'created_at' => 'msqlfunc_NOW()'
        );
        $insert_id_add = $pdo->insert('trigger_template_address', $image_params);
        /* Code for audit log*/
    $user_data = get_user_data($_SESSION['admin']);
    audit_log_new($user_data, $_SESSION['admin']['id'], "Admin", "Trigger Template Address Insert By ID is :".$insert_id_add, '', $add_note_params, 'trigger template address created by admin');

/* End Code for audit log*/
      }
    }
    setNotifySuccess('Trigger template updated successfully.');
    $res['msg']='Trigger template updated successfully.';
    $res['status']='success'; 
    //redirect('trigger_template.php');
  }else{
    $res["status"]="fail";
    $res['error']=$validate->getErrors();
  }
  echo json_encode($res);
  exit;
}



$strQuery_images = "SELECT * FROM trigger_images WHERE is_deleted = 'N' ORDER BY id DESC";
$rows_images = $pdo->select($strQuery_images);
foreach ($rows_images as $value) {
  $imgArray[$value['id']] = $value;
}

$strQuery_address = "SELECT * FROM trigger_address WHERE is_deleted = 'N' ORDER BY id DESC";
$rows_address = $pdo->select($strQuery_address);
foreach ($rows_address as $value) {
  $addressArray[$value['id']] = $value;
}

$strQuery_footer = "SELECT * FROM trigger_footer WHERE is_deleted = 'N' ORDER BY id DESC";
$rows_footer = $pdo->select($strQuery_footer);
foreach ($rows_footer as $value) {
  $footerArray[$value['id']] = $value;
}
$company_sql = "SELECT * FROM company where id=3 ";
$company_res = $pdo->select($company_sql);

$exJs = array('thirdparty/simscroll/jquery.slimscroll.min.js');
$page_title = "Edit Trigger Template";
$template = "edit_trigger_template.inc.php";
$layout = "main.layout.php";
include_once 'layout/end.inc.php';
?>
