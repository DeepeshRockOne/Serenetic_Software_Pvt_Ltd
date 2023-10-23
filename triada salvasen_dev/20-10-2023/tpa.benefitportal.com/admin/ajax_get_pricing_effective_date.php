<?php
include_once 'layout/start.inc.php';

$plan_id=$_POST['plan'];
$product_id = $_POST['product_id'];
$result = array();
 
$sqlPrdMatrixTerm="SELECT pricing_termination_date from prd_matrix where product_id=:product_id and is_deleted='N' AND plan_type=:plan_type order by pricing_termination_date desc limit 1";
$resPrdMatrixTerm=$pdo->selectOne($sqlPrdMatrixTerm,array(":product_id"=>$product_id,":plan_type"=>$plan_id));

$sqlProduct="SELECT * FROM prd_main WHERE id=:product_id";
$params=array(":product_id"=>$product_id);
$resProduct=$pdo->selectOne($sqlProduct,$params);

$productActiveEffectiveDate = '';
if($resPrdMatrixTerm){
	$productActiveEffectiveDate = date('m/d/Y',strtotime($resPrdMatrixTerm['pricing_termination_date'].'+1 day'));
}else{
	$productActiveEffectiveDate = date('m/d/Y',strtotime($resProduct['create_date']));
}
if (!empty($productActiveEffectiveDate)) {
		
	$result['effectiveDate'] = $productActiveEffectiveDate;
	$result['status'] = "success";

	
}
header('Content-type: application/json');
echo json_encode($result);
dbConnectionClose();
exit;
?>