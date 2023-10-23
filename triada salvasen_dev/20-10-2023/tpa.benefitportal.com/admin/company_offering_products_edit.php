<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$id = (isset($_GET['id'])?$_GET['id']:'');

if(!empty($id)){

	$sql="SELECT id,company_name FROM prd_company WHERE md5(id)=:id";
	$res=$pdo->selectOne($sql,array(":id"=>$id));

	if($res){
	  $name=$res['company_name'];
	} 

	$description['ac_message'] =array(
		'ac_red_1'=>array(
			'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
			'title'=>$_SESSION['admin']['display_id'],
		),
		'ac_message_1' =>' Read Companies Offering Product ',
		'ac_red_2'=>array(
			//'href'=> '',
			'title'=>$res['company_name'],
		),
	); 
	activity_feed(3, $_SESSION['admin']['id'], 'Admin', $res['id'], 'companies_offering_products','Admin Read Companies Offering Products', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
}


$template = 'company_offering_products_edit.inc.php';
$layout="iframe.layout.php";
include_once 'layout/end.inc.php';
?>