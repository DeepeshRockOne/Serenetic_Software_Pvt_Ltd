<?php
  include_once 'layout/start.inc.php';

  $state=isset($_POST['state'])?$_POST['state']:array();	
  $check_state = isset($_POST['check_state']) ? $_POST['check_state'] : 'N';
  $data="";

  $StateArr = array();
  if(!empty($allStateRes)){
    foreach ($allStateRes as $key => $value) {
      $StateArr[$value['name']]=$value;
    }
  }

  if($check_state == 'N'){
    foreach($StateArr AS $k=>$v){
      $states = $v;
      $data.= '<option value="'.$states['name'].'">'.$states['short_name'].', '.$states['name'].'</option>';
    }
  }else{
    $states = $StateArr[$state];

    $data.= '<option value="'.$states['name'].'">'.$states['short_name'].', '.$states['name'].'</option>';
  }
   
	$result = array();	
	$result['optionsHtml'] = $data;
  $result['status'] = "success"; 
  
  header('Content-type: application/json');
	echo json_encode($result);
  dbConnectionClose();
   exit;	
?>