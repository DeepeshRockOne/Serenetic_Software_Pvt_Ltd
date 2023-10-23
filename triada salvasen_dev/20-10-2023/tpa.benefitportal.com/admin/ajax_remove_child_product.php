<?php
include_once 'layout/start.inc.php';

$response=array();
$id=$_POST['id'];
$product_id=$_POST['product_id'];
$response['status']="fail";

$sql="SELECT id FROM prd_sub_products where id=:id AND product_id=:product_id";
$res=$pdo->selectOne($sql,array(":id"=>$id,":product_id"=>$product_id));

if($res){
	$pdo->delete("DELETE FROM prd_sub_products where id=:id",array(":id"=>$res['id']));
	$response['status']="success";
}

header('Content-Type: application/json');
echo json_encode($response);
dbConnectionClose();
exit;
?>