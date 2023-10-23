<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';
has_access(15);

$validate = new Validation();
$id = isset($_GET['id'])?$_GET['id']:'';

if(!empty($id)){
  $selSql = "SELECT *  FROM sub_products WHERE md5(id)=:id AND is_deleted='N'";
  $params = array(':id' => $id);
  $row = $pdo->selectOne($selSql, $params);
  if(!empty($row)){
    $carrier_id = $row['carrier_id'];
    $product_code = $row['product_code'];
    $product_name = $row['product_name'];
    $status = $row['status'];
  }

  	$description['ac_message'] =array(
		'ac_red_1'=>array(
			'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
			'title'=>$_SESSION['admin']['display_id'],
		),
		'ac_message_1' =>' Read Sub Product ',
		'ac_red_2'=>array(
			//'href'=> '',
			'title'=>$product_code,
		),
	); 
	activity_feed(3, $_SESSION['admin']['id'], 'Admin', $row['id'], 'sub_product','Admin Read Sub Product', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));

}
 
$sql = "SELECT id,name,display_id FROM prd_fees WHERE setting_type='Carrier' AND status='Active' AND is_deleted='N' ";
$carrierRows = $pdo->select($sql);


 
$errors = $validate->getErrors();
$template = "sub_product_edit.inc.php";
$layout = "iframe.layout.php";
include_once 'layout/end.inc.php';
?>