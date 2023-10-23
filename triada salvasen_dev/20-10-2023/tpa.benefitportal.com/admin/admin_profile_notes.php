<?php
  include_once __DIR__ . '/includes/connect.php';

 //$id = $_GET['id'];
  $customer_id = $_GET['id'];
   //echo $customer_id; 
  $admin_id = $_SESSION['admin']['id'];
  
  $select_admin = 'SELECT * FROM `admin` WHERE id= :id';
  $where = array(':id' => makeSafe($customer_id));
  $rowadmin = $pdo->selectOne($select_admin, $where);
  $profile_image = $rowadmin['photo'];
  //print_r($profile_image);
  
  $incr="";
  if(isset($_GET['notesearch']))
  {
    $incr=" and (title like '%".addslashes($_GET['notesearch'])."%' OR description like '%".addslashes($_GET['notesearch'])."%')";
  }

  $query = "SELECT * FROM note WHERE user_type = 'Admin' AND customer_id = :customer_id $incr ORDER BY id desc";
  $params = array(
    ':customer_id' => makeSafe($customer_id)
    );

  $selSql = $pdo->select($query,$params);
    $lead_id = $_GET['lead_id'];

  $validate = new Validation();
  
  if(isset($_POST['title_'.$lead_id])){    
  	$customer_id = $_GET['id'];
  	
    $title = $_POST['title_'.$lead_id];
    $description = $_POST['description_'.$lead_id];
    
    $validate->string(array('required' => true, 'field' => 'title_'.$lead_id, 'value' => $title), array('required' => 'Title is required'));
    $validate->string(array('required' => true, 'field' => 'description_'.$lead_id, 'value' => $description), array('required' => 'Description is required'));
    
    $res = array();
    if($validate->isValid()){
      $update_params = array (
        'title' => addslashes($title),
        'description' => addslashes($description)
      );
      $where = array(
        'clause' => 'id = :id ',
        'params' => array(
          ':id' => $lead_id
          )
        );
                     
      $pdo->update('note',$update_params,$where);
      
      $res['status'] = 'success';
      $res['msg'] = 'Note saved successfully';
      $res['data']['title_'.$lead_id]= $title;
      $res['data']['description_'.$lead_id]= $description;
  }
  else {
    $res['status'] = 'fail';
    $res['errors'] = $validate->getErrors();
  }
  
  header('Content-Type: application/json');
  echo json_encode($res); exit;
  }
	
  $template= 'admin_profile_notes.inc.php';
  include_once 'tmpl/'.$template;
?>