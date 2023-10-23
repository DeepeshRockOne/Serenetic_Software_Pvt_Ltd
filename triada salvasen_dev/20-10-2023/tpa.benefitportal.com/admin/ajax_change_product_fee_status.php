<?php
	include_once 'layout/start.inc.php';
	$id=$_POST['id'];
	$new_status=$_POST['status'];

	$response=array();

	$sqlFee="SELECT id,status FROM prd_main where id=:id and product_type IN('Product','AdminFee') AND type='Fees' AND is_deleted='N'";
	$resFee=$pdo->selectOne($sqlFee,array(":id"=>$id));

	if(!empty(($resFee))){
		$old_status = $resFee['status'];
		$insParams = array("status"=>$new_status);
		$updWhere=array(
	        'clause'=>'id=:id',
	        'params'=>array(":id"=>$resFee['id'])
	    );
	    $pdo->update("prd_main",$insParams,$updWhere);
	    $response['status']='Success';
	    $response['message']='Fee Status Updated';
	}else{
		$response['status']='Fail';
	}

	header('Content-Type: application/json');
	echo json_encode($response);
	dbConnectionClose();
	exit;
?>