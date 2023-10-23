<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$validate = new Validation();
$response = array();
// unset($_SESSION['temp_membership_fee']);
// unset($_SESSION['temp_fees']);
$is_back = !empty($_POST['is_back']) ? $_POST['is_back'] : false;
$fee_id = isset($_POST['fee_id']) ? $_POST['fee_id'] : "";
$membership_id = isset($_POST['membership_id']) ? $_POST['membership_id'] : 0;
$is_clone = isset($_POST['is_clone']) ? $_POST['is_clone'] : "";
$fee_name = isset($_POST['fee_name']) ? $_POST['fee_name'] : "";
$display_fee_id = isset($_POST['display_fee_id']) ? $_POST['display_fee_id'] : "";

$fee_type= isset($_POST['fee_type']) ? $_POST['fee_type'] : "";

$product_array = isset($_POST['products']) ? $_POST['products'] : array();

$effective_date = !empty($_POST['effective_date']) ? date('Y-m-d',strtotime($_POST['effective_date'])) : '';
$termination_date = !empty($_POST['termination_date']) ? date('Y-m-d',strtotime($_POST['termination_date'])) : NULL;


$initial_purchase = isset($_POST['initial_purchase']) ? $_POST['initial_purchase'] : "";
$is_fee_on_renewal = isset($_POST['is_fee_on_renewal']) ? $_POST['is_fee_on_renewal'] : "";

$fee_renewal_type = isset($_POST['fee_renewal_type']) ? $_POST['fee_renewal_type'] : "";
$fee_renewal_count = isset($_POST['fee_renewal_count']) ? $_POST['fee_renewal_count'] : "";

$is_fee_commissionable = isset($_POST['is_fee_commissionable']) ? $_POST['is_fee_commissionable'] : "";
$non_commissionable_amount = isset($_POST['NC_amount']) ? $_POST['NC_amount'] : "";
$commissionable_amount = isset($_POST['C_amount']) ? $_POST['C_amount'] : "";
$differ_by_state = isset($_POST['differ_by_state']) ? $_POST['differ_by_state'] : "";
$retail_price = !empty($_POST['retail_price']) ? $_POST['retail_price'] : "";

$fee_price = isset($_POST['fee_price']) ? $_POST['fee_price'] : "";

/*--- This Removed Because we get change from troy : Remove products because it would automatically include the products selected above.  We just need to select states this association applies to. 

/*$association_product = isset($_POST['association_product']) ? $_POST['association_product'] : array();
$association_state_array = isset($_POST['association_state']) ? $_POST['association_state'] : array();
$association_state = array();
if(count($association_product) > 0){
  foreach ($association_product as $key => $productArr) {
    foreach ($productArr as $key1 => $product_id) {
      if (empty($association_state[$product_id])) {
        $stateArray =(!empty($association_state_array[$key])) ? $association_state_array[$key] : array();
          }else{
            $prevSelectedState=explode(",", $association_state[$product_id]);
            $currentSelectedState=(!empty($association_state_array[$key])) ? $association_state_array[$key] : array();
            $stateArray =array_unique(array_merge($prevSelectedState,$currentSelectedState));
            
          }
          asort($stateArray);
          $stateString=(!empty($stateArray)) ? implode(",", $stateArray) : '';
          $association_state[$product_id] = $stateString;
    }
  }
}*/

$association_state = array();
$association_state_array = isset($_POST['association_state']) ? $_POST['association_state'] : array();
$stateArray =(!empty($association_state_array['-1'])) ? $association_state_array['-1'] : array();
if(!empty($product_array) && !empty($stateArray)) {
    asort($product_array);
    $stateString=(!empty($stateArray)) ? implode(",", $stateArray) : '';
    foreach ($product_array as $key => $product) {       
        $association_state[$product] = $stateString;
    }
}

$validate->string(array('required' => true, 'field' => 'fee_name', 'value' => $fee_name), array('required' => 'Fee Name is required'));
$validate->string(array('required' => true, 'field' => 'display_fee_id', 'value' => $display_fee_id), array('required' => 'Fee ID is required'));

if (!empty($display_fee_id)) {
  $incr="";
  $sch_params=array();
  $sch_params[':display_fee_id']=$display_fee_id;
  if (!empty($fee_id) && $is_clone == 'N') {
    $incr.=" AND id!=:id";
    $sch_params[':id']=$fee_id;
  } 
  $selectVendor = "SELECT id FROM prd_main WHERE product_code=:display_fee_id $incr AND is_deleted='N' ";
  $resultVendor = $pdo->selectOne($selectVendor, $sch_params);
  if ($resultVendor) {
    $validate->setError("fee_display_id", "This Fee ID is already associated with another Fee");
  }
}
$validate->string(array('required' => true, 'field' => 'fee_type', 'value' => $fee_type), array('required' => 'Select Fee Type'));

if(empty($product_array)){
  $validate->setError("products","Select Product");
}

$validate->string(array('required' => true, 'field' => 'effective_date', 'value' => $effective_date), array('required' => 'Effective date is required'));

if (!empty($termination_date) && !empty($effective_date)) {
  if ((strtotime($effective_date)) > (strtotime($termination_date))) {
    $validate->setError("termination_date", "Termination date must be greater than or equal to effective date.");
  }
}
if (!empty($product_array) && !$validate->getError('effective_date')) {
  $fincr = "";
  $fparams = array();
  if (!empty($product_array)) {
    $fincr .= " AND pa.product_id IN(" . implode(',', $product_array) . ")";
  }
  if(!empty($membership_id)){
    $fincr .= " AND pf.id = :membershipId"; 
    $fparams[":membershipId"] = $membership_id;
  }
  if (!empty($fee_id) && $is_clone == 'N') {
    $fincr .= " AND pa.fee_id != :id";
    $fparams[":id"] = $fee_id;
  }

  $selTermPrd = "SELECT pm.pricing_effective_date AS effective_date,pm.pricing_termination_date as termination_date,p.product_code as assignedProductCode
    FROM prd_fees pf 
    JOIN prd_assign_fees pa ON(pf.id=pa.prd_fee_id AND pa.is_deleted='N')
    JOIN prd_matrix pm ON(pa.fee_id=pm.product_id AND pm.is_deleted='N') 
    JOIN prd_main p ON(p.id=pa.product_id AND p.is_deleted='N')
    WHERE pf.setting_type='membership' AND pf.is_deleted='N' 
    AND(
      ('".$effective_date."' BETWEEN pm.pricing_effective_date AND pm.pricing_termination_date) OR
      (pm.pricing_effective_date BETWEEN '".$effective_date."' AND '".$termination_date."') 
      OR ('".$effective_date."' >= pm.pricing_effective_date AND pm.pricing_termination_date IS NULL) 
      OR (pm.pricing_effective_date >= '".$effective_date."' AND '".$termination_date."'='') 
    )
    $fincr";
  $resTermPrd = $pdo->selectOne($selTermPrd, $fparams);

  if(!empty($resTermPrd)){
    $validate->setError("products", "Product " . $resTermPrd["assignedProductCode"] . " is conflicted with another fee for this Memberships");
  }
}
$validate->string(array('required' => true, 'field' => 'is_fee_commissionable', 'value' => $is_fee_commissionable), array('required' => 'Please choose any options'));

$validate->string(array('required' => true, 'field' => 'is_fee_on_renewal', 'value' => $is_fee_on_renewal), array('required' => 'Please choose any options'));

$validate->string(array('required' => true, 'field' => 'differ_by_state', 'value' => $differ_by_state), array('required' => 'Please choose any options'));

if($is_fee_commissionable == 'Y'){

  $validate->string(array('required' => true, 'field' => 'fee_price', 'value' => $fee_price), array('required' => 'Price is required'));
  $validate->string(array('required' => true, 'field' => 'NC_amount', 'value' => $non_commissionable_amount), array('required' => 'Non commissionable amount is required'));
  $validate->string(array('required' => true, 'field' => 'C_amount', 'value' => $commissionable_amount), array('required' => 'Commissionable amount is required'));
}else{
  $validate->string(array('required' => true, 'field' => 'retail_price', 'value' => $retail_price), array('required' => 'Price is required'));
}
    
    
if ($validate->isValid()) {
  if($is_fee_commissionable == 'N'){
    $fee_price = $retail_price;
  }
  $insParams=array(
    'type'=>'Fees',
    'name'=>$fee_name,
    'product_code'=>$display_fee_id,
    'product_type'=>'Membership',
    'fee_type'=>$fee_type,
    'initial_purchase'=>$initial_purchase,
    'is_fee_on_renewal'=>$is_fee_on_renewal,
    'fee_renewal_type'=>$fee_renewal_type,
    'fee_renewal_count'=>0,
    'is_assign_by_state'=>$differ_by_state,
    'is_fee_on_commissionable'=>$is_fee_commissionable,
    'pricing_model'=>"FixedPrice",
    'admin_id' => $_SESSION['admin']['id'],
    'update_date' => 'msqlfunc_NOW()',
  );

  if($is_fee_on_renewal=="Y"){ 
    if($fee_renewal_type=="Renewals"){
      $insParams['fee_renewal_count']=$fee_renewal_count;
    }
    $insParams['payment_type']= "Recurring";
  }else{
    $insParams['payment_type']= "Single";
  }

  if(!empty($fee_id) && $is_clone == 'N'){
      $updWhere=array(
        'clause'=>'id=:id',
        'params'=>array(":id"=>$fee_id)
      );
      $pdo->update("prd_main",$insParams,$updWhere);

    $oldValue = $pdo->selectOne("SELECT type,name,product_code,product_type,fee_type,initial_purchase,is_fee_on_renewal,fee_renewal_type,fee_renewal_count,payment_type,pricing_model,is_assign_by_state,is_fee_on_commissionable,admin_id,fee_renewal_type,fee_renewal_count FROM prd_main WHERE id = :id",array(':id' => $fee_id));
    $newValue = $insParams;

    unset($newValue['update_date']);
    $checkDiff=array_diff_assoc($newValue, $oldValue);

    if(!empty($checkDiff)){
      $description['ac_message'] =array(
        'ac_red_1'=>array(
          'href'=>$ADMIN_HOST.'/admin_profile.php?id='.$_SESSION['admin']['id'],
          'title'=>$_SESSION['admin']['display_id'],
        ),
        'ac_message_1' =>' updated membership fee ',
        'ac_red_2'=>array(
            'title'=>$display_fee_id,
        ),
      );

      foreach ($checkDiff as $key1 => $value1) {
          $activityFeedDesc['key_value']['desc_arr'][$key1]= 'From '.$oldValue[$key1].' To '.$newValue[$key1];
        } 

      activity_feed(3, $_SESSION['admin']['id'], 'Admin', $fee_id, 'prd_main','Admin updated membership fee', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($activityFeedDesc));
    }

    $response['message']= 'Fee Updated successfully';

  } else {
    $insParams['status']='Active';
    $insParams['create_date']='msqlfunc_NOW()';
    $insParams['term_back_to_effective'] = 'Y';
    $fee_id = $pdo->insert("prd_main", $insParams);

    if(isset($_SESSION['temp_fee_products'])){
      $temp_fee_products = $_SESSION['temp_fee_products'];
      array_push($temp_fee_products, $fee_id);
      $_SESSION['temp_fee_products'] = $temp_fee_products;
    }else{
      $_SESSION['temp_fee_products'] = array($fee_id);
    }
    
    $description['ac_message'] =array(
      'ac_red_1'=>array(
        'href'=>$ADMIN_HOST.'/admin_profile.php?id='.$_SESSION['admin']['id'],
        'title'=>$_SESSION['admin']['display_id'],
      ),
      'ac_message_1' =>' create membership fee ',
      'ac_red_2'=>array(
          // 'href'=>$ADMIN_HOST.'/memberships_mange.php?id='.md5($fee_id),
          'title'=>$display_fee_id,
      ),
    ); 

    activity_feed(3, $_SESSION['admin']['id'], 'Admin', $fee_id, 'prd_fees','create membership fee', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));

    $response['message']= 'Fee Inserted successfully';
  }

  $updParams=array(
    "is_deleted"=>'Y',
    'updated_at'=>'msqlfunc_NOW()',
  );
  $updWhere=array(
    'clause'=>'association_fee_id=:id',
    'params'=>array(":id"=>$fee_id)
  );

  $pdo->update("association_assign_by_state",$updParams,$updWhere);

  if(count($association_state) > 0 && $differ_by_state == 'Y'){
      foreach ($association_state as $product_id => $statesList) {
          
        $sqlAssociationState="SELECT * FROM association_assign_by_state where association_fee_id=:fee_id AND product_id=:product_id";
        $resAssociationState=$pdo->selectOne($sqlAssociationState,array(":fee_id"=>$fee_id,":product_id"=>$product_id));
          
        $states = '';
        if(!empty($statesList)){
          $states = $statesList;
        }

        if(!empty($states)){

          if($resAssociationState){
            $updParams=array(
              "states"=>$states,
              "is_deleted"=>'N',
              'updated_at'=>'msqlfunc_NOW()',
            );
            $updWhere=array(
              'clause'=>'id=:id',
              'params'=>array(":id"=>$resAssociationState['id'])
            );
            $pdo->update("association_assign_by_state",$updParams,$updWhere);
          }else{
            $insParams=array(
              'association_fee_id'=>$fee_id,
              'product_id'=>$product_id,
              "states"=>$states,
              "is_deleted"=>'N',
              'created_at'=>'msqlfunc_NOW()',
            );
            if($membership_id > 0){
              $insParams['prd_fee_id'] = $membership_id;
            }

            $pdo->insert("association_assign_by_state",$insParams);
          }
        }else{
          if($resAssociationState){
            $updParams=array(
              "is_deleted"=>'Y',
              "states"=>'',
              'updated_at'=>'msqlfunc_NOW()',
            );
            $updWhere=array(
              'clause'=>'id=:id',
              'params'=>array(":id"=>$resAssociationState['id'])
            );
            $pdo->update("association_assign_by_state",$updParams,$updWhere);
          }
        }
      }
  }


  if(count($product_array) > 0){
    if($membership_id > 0 && $fee_id > 0 && $is_clone == 'N'){
      
      $updParams = array("is_deleted" => 'Y');
      $updWhere=array(
        'clause'=>'fee_id=:fee_id',
        'params'=>array(':fee_id'=>$fee_id)
      );
      $pdo->update("prd_assign_fees",$updParams,$updWhere);
    
      foreach ($product_array as $key => $product) {
          $inser_fee_details = array(
            "product_id" => $product,
            "fee_id" => $fee_id,
            "prd_fee_id" => $membership_id,
            "created_at" => 'msqlfunc_NOW()'
          );

          $prd_assign_fees = $pdo->insert('prd_assign_fees',$inser_fee_details);
          if(isset($_SESSION['temp_fees'])){
            $temp_fees = $_SESSION['temp_fees'];
            array_push($temp_fees, $prd_assign_fees);
            $_SESSION['temp_fees'] = $temp_fees;
          }else{
            $_SESSION['temp_fees'] = array($prd_assign_fees);
          }  
      }
      
    }else{
      foreach ($product_array as $key => $product) {
        $inser_fee_details = array(
          "product_id" => $product,
          "fee_id" => $fee_id,
          "created_at" => 'msqlfunc_NOW()'
        );
        if($membership_id > 0){
          $inser_fee_details['prd_fee_id'] = $membership_id;
        }
        $prd_assign_fees = $pdo->insert('prd_assign_fees',$inser_fee_details);

        if(isset($_SESSION['temp_fees'])){
          $temp_fees = $_SESSION['temp_fees'];
          array_push($temp_fees, $prd_assign_fees);
          $_SESSION['temp_fees'] = $temp_fees;
        }else{
          $_SESSION['temp_fees'] = array($prd_assign_fees);
        }
      }
    }
  }
  
  //****  association fees id code end   *************

  $sqlMatrix="SELECT id FROM prd_matrix where product_id=:fee_id AND is_deleted='N'";
  $whereMatrix=array(":fee_id"=>$fee_id);
  $resMatrix=$pdo->selectOne($sqlMatrix,$whereMatrix);
  if($resMatrix){
    $matrixUpdParam=array(
      'price'=> $fee_price,
      'non_commission_amount'=>$non_commissionable_amount,
      'commission_amount'=>$commissionable_amount,
      'pricing_termination_date' => $termination_date,
      "pricing_effective_date"=>date('Y-m-d',strtotime($effective_date)),
      'update_date'=>'msqlfunc_NOW()',
    );
    $matrixUpdWhere=array(
      'clause'=>'id=:id',
      'params'=>array(":id"=>$resMatrix['id'])
    );
    $pdo->update("prd_matrix",$matrixUpdParam,$matrixUpdWhere);
  }else{
    $matrixIns = array(
      "product_id" => $fee_id,
      "price" => $fee_price,
      "plan_type" => 1,
      'non_commission_amount'=>$non_commissionable_amount,
      "commission_amount"=>$commissionable_amount,
      "pricing_effective_date"=>date('Y-m-d',strtotime($effective_date)),
      "create_date" => 'msqlfunc_NOW()',
      "update_date" => 'msqlfunc_NOW()',
    );
    if($termination_date){
      $matrixIns['pricing_termination_date'] = date('Y-m-d',strtotime($termination_date));
    }
    $pdo->insert("prd_matrix", $matrixIns);
  }

  // array_push($vendor_fee_id, $fee_id);
  $response['fee_id']=$fee_id;
  // $response['vendor_fee_id']=implode(",", $vendor_fee_id);

  // delete_vendor_fee($fee_id,$is_benefit_tier);
  $response['status']="success";
}else{
  $response['status'] = "fail";
  $response['errors'] = $validate->getErrors();  
}

header('Content-Type: application/json');
echo json_encode($response);
dbConnectionClose();
exit;
?>