<?php
include_once 'layout/start.inc.php';
 
$response=array();
$id=$_POST['id'];
$product_id=$_POST['product_id'];
$response['status']="fail";

$sql="SELECT id FROM prd_department_description where id=:id AND product_id=:product_id";
$res=$pdo->selectOne($sql,array(":id"=>$id,":product_id"=>$product_id));

if($res){
	$updParams=array("is_deleted"=>'Y');
	$updWhere=array(
		'clause'=>'id=:id',
		'params'=>array(":id"=>$id)
	);
	$pdo->update("prd_department_description",$updParams,$updWhere);
	$response['status']="success";
}

header('Content-Type: application/json');
echo json_encode($response);
exit;
?>