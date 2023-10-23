<?php

include_once 'includes/connect.php';

if(isset($_POST['business_id']) && !empty($_POST['business_id'])){
  $business_id=$_POST['business_id'];	

  $sic_code = array();

  $select_sic_code = 'SELECT * FROM `group_sic_code` WHERE business_id = :id';
  $where = array(':id' => makeSafe($business_id));
	$sic_code = $pdo->select($select_sic_code,$where);
   
	$data = '<option value=""></option>';

  foreach($sic_code AS $k=>$v){
      $data.= '<option value="'.$v['id'].'">'.$v['code'].' - '.$v['title'].'</option>';
  }
  
	$result = array();	
	$result['data'] = $data;
  $result['status'] = "success"; 

  header('Content-type: application/json');
	echo json_encode($result); 
  dbConnectionClose();
  exit;	
}
?>