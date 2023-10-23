<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$validate = new Validation();
$response = array();

$productFees = !empty($_POST['productFees']) ? json_decode($_POST['productFees'],true) : array();
$groupEnrollmentPrd = checkIsset($_POST['groupEnrollmentPrd']);
$product_type = $groupEnrollmentPrd == 'N' ? 'Product' : 'AdminFee';
$is_clone = checkIsset($_POST['is_clone']);
$product_id = checkIsset($_POST['product_id']);
$fee_id = checkIsset($_POST['fee_id']);

$fee_name = checkIsset($_POST['fee_name']);
$display_fee_id = checkIsset($_POST['display_fee_id']);
$fee_type= checkIsset($_POST['fee_type']);

 
$effective_date = !empty($_POST['effective_date']) ? date('Y-m-d',strtotime($_POST['effective_date'])) : '';
$termination_date = !empty($_POST['termination_date']) ? date('Y-m-d',strtotime($_POST['termination_date'])) : NULL;

$initial_purchase = checkIsset($_POST['initial_purchase']);
$is_fee_on_renewal = checkIsset($_POST['is_fee_on_renewal']);

$fee_renewal_type = checkIsset($_POST['fee_renewal_type']);
$fee_renewal_count = checkIsset($_POST['fee_renewal_count']);

$is_benefit_tier = checkIsset($_POST['is_benefit_tier']);
$pricing_model = checkIsset($_POST['pricing_model']);

$fee_method = checkIsset($_POST['fee_method']);
$percentage_type = checkIsset($_POST['percentage_type']);

$fee_price = $_POST['fee_price'];
$fee_price_plan = !empty($_POST['plan'])?$_POST['plan']:array();


$validate->string(array('required' => true, 'field' => 'fee_name', 'value' => $fee_name), array('required' => 'Fee Name is required'));
$validate->string(array('required' => true, 'field' => 'display_fee_id', 'value' => $display_fee_id), array('required' => 'Fee ID is required'));
   
if (!empty($display_fee_id)) {
  $incr="";
  $sch_params=array();
  $sch_params[':product_code']=$display_fee_id;
  if (!empty($fee_id)) {
    $incr.=" AND id!=:id";
    $sch_params[':id']=$fee_id;
  } 
  $selectSql = "SELECT id FROM prd_main WHERE product_code=:product_code $incr AND is_deleted='N' ";
  $resultRes = $pdo->selectOne($selectSql, $sch_params);
  if ($resultRes) {
    $validate->setError("fee_id", "This Fee ID is already associated with another Fee" . $resultRes['id']);
  }
}

$validate->string(array('required' => true, 'field' => 'fee_type', 'value' => $fee_type), array('required' => 'Select Fee Type'));
if($groupEnrollmentPrd == 'N'){
  $validate->string(array('required' => true, 'field' => 'effective_date', 'value' => $effective_date), array('required' => 'Effective date is required'));  
    
  if (!empty($termination_date) && !empty($effective_date)) {
    if ((strtotime($effective_date)) > (strtotime($termination_date))) {
      $validate->setError("termination_date", "Termination date must be greater than or equal to effective date.");
    }
  }
  if(!$validate->getError('effective_date')){
    foreach($productFees as $key => $data){
      if($fee_id == $data['keyID']){
        continue;
      }
      $feesEffectDate = !empty($data['pricing_effective_date']) ? date('Y-m-d',strtotime($data['pricing_effective_date'])) : '';
      $feesTermDate = !empty($data['pricing_termination_date']) ? date('Y-m-d',strtotime($data['pricing_termination_date'])) : '';

      if(($effective_date >= $feesEffectDate) && ($effective_date <= $feesTermDate)){
        $validate->setError("effective_date", "Product fee is conflicted with another fee for this Product");
      } else if(($feesEffectDate >= $effective_date) && ($feesEffectDate <= $termination_date)){
        $validate->setError("effective_date", "Product fee is conflicted with another fee for this Product");
      } else if(($effective_date >= $feesEffectDate) && (empty($feesTermDate))){
        $validate->setError("effective_date", "Product fee is conflicted with another fee for this Product");
      } else if(($feesEffectDate >= $effective_date) && (empty($termination_date))){
        $validate->setError("effective_date", "Product fee is conflicted with another fee for this Product");
      }
    }
  }
}
if($is_benefit_tier=='Y'){
  if(!empty($fee_price_plan)){
    foreach ($fee_price_plan as $key => $value) {
      $validate->string(array('required' => true, 'field' => 'fee_plan_price', 'value' => $value), array('required' => 'Fee is required'));
    }
  }else{
    $validate->setError("fee_plan_price","Fee is required");
  }
}else{
  $validate->string(array('required' => true, 'field' => 'fee_price', 'value' => $fee_price), array('required' => 'Fee is required'));
  if($fee_price <= 0){
    $validate->setError("fee_price","Fee is required");
  }
}
    
if ($validate->isValid()) {
  
  $insert_params = array(
    'name' => $fee_name,
    'product_code' => $display_fee_id,
    'product_type' => $product_type,
    'type' => 'Fees',
    'fee_type' => $fee_type,
    'is_benefit_tier' => $is_benefit_tier,
    'status' => 'Inactive',
    'total_products'=>1,
    'prd_price'=>'',
  );

  if($groupEnrollmentPrd == 'N'){
    $insert_params['pricing_effective_date']=$effective_date;
    $insert_params['pricing_termination_date']=$termination_date;
    $insert_params['initial_purchase']=$initial_purchase;
    $insert_params['is_fee_on_renewal']=$is_fee_on_renewal;
    $insert_params['fee_renewal_type']=$fee_renewal_type;
    $insert_params['fee_renewal_count']= 0;

    if($is_fee_on_renewal=="Y"){ 
      if($fee_renewal_type=="Renewals"){
        $insert_params['fee_renewal_count']=$fee_renewal_count;
      }
        $insert_params['payment_type']= "Recurring";
    }else{
         $insert_params['payment_type']= "Single";
    }
  }
  $insert_params['pricing_model'] = ($is_benefit_tier == 'Y' ? $pricing_model : "FixedPrice");

  $insert_params['price']=array();
  if(!empty($fee_id)){
    $response['message']= 'Fee Updated successfully';
  } else {
    $fee_id = generateRandomFeeID();  
    $response['message']= 'Fee Inserted successfully';
  }
  
  $insert_params['keyID'] = $fee_id;
  $insert_params['id'] = '';
  $insert_params['price']=array();

  if($is_benefit_tier == 'Y'){
    if(!empty($fee_price_plan)){
      foreach ($fee_price_plan as $key => $plan_price) {

        if(empty($insert_params['prd_price']) || $plan_price < $insert_params['prd_price']){
          $insert_params['prd_price'] = $plan_price;
          $insert_params['price_calculated_on'] = $fee_method;
        }

        $plan_params = array(
          'product_id' => $fee_id,
          'plan_type' => $key,  
          'price_calculated_on' => $fee_method,  
          'price_calculated_type' => $percentage_type,  
          'price' => $plan_price,
          'is_deleted'=> 'N',
          'matrix_group'=>$key
        );

        $insert_params['price'][$key] = $plan_params;
        
      }
    }
  }else{
    $plan_params = array(
      'product_id' => $fee_id,
      'plan_type' => 0,  
      'price_calculated_on' => $fee_method,  
      'price_calculated_type' => $percentage_type,  
      'price' => $fee_price,
      'is_deleted'=>'N'
    );
    $insert_params['price'][0] = $plan_params;
    $insert_params['prd_price'] = $fee_price;
    $insert_params['price_calculated_on'] = $fee_method;
  }

  $productFees[$fee_id] = $insert_params;

  $response['status']="success";
  $response['productFees'] = json_encode($productFees);
  
}else{
  $response['status'] = "fail";
  $response['errors'] = $validate->getErrors();  
}

function generateRandomFeeID(){
  global $productFees;
  $fee_id = rand(1, 999999);
  
  if (array_key_exists($fee_id,$productFees)) {
    return generateRandomFeeID();
  } else {
    return "-".$fee_id;
  }
}
header('Content-Type: application/json');
echo json_encode($response);
dbConnectionClose();
exit;
?>