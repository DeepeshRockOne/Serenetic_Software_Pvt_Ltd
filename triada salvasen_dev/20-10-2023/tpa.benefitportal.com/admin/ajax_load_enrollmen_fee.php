<?php
include_once 'layout/start.inc.php';
 
$product_id=$_POST['product_id'];

$sqlFee="SELECT id,name FROM prd_main where status='Active' AND is_deleted='N' and product_type = 'Enrollment Fee' AND type='Fees'";
$resFee=$pdo->select($sqlFee);

$data = '<option value="Create New Enrollment Fee">Create New Application Fee</option>';
$sqlProduct="SELECT enrollment_fee_ids FROM prd_main where id=:id";
$resProduct=$pdo->selectOne($sqlProduct,array(":id"=>$product_id));

if($resProduct){
  $fee_ids_list = $resProduct['enrollment_fee_ids'];
  $fee_ids = !empty($fee_ids_list) ? explode(",", $fee_ids_list) : array();
  if(!empty($resFee)){
    foreach($resFee AS $k=>$v){
      $data.= '<option value="'.$v['id'].'" '.(!empty($fee_ids) && in_array($v['id'],$fee_ids)?'selected="selected"':'').' >'.$v['name'].'</option>';
    }
  }
}
  
$result = array();	
$result['html'] = $data;
$result['status'] = "success"; 
  
header('Content-type: application/json');
echo json_encode($result);
dbConnectionClose(); 
exit;
?>