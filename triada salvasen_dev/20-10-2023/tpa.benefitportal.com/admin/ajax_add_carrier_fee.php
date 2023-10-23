<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$validate = new Validation();
$response = array();
$product_type = 'Carrier'; 
$is_clone = checkIsset($_POST['is_clone']);
$carrier_id = checkIsset($_POST['carrier_id']);
$product_id = checkIsset($_POST['fee_id']);
$carrier_fee_id = !empty($_POST['carrier_fee_id']) ? explode(",", $_POST['carrier_fee_id']) : array();
$fee_name = checkIsset($_POST['fee_name']);
$display_fee_id = checkIsset($_POST['display_fee_id']);
$fee_type= checkIsset($_POST['fee_type']);
$products = checkIsset($_POST['products']);
 
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
$group_matrix = !empty($_POST['group_matrix'])?$_POST['group_matrix']:array();

$validate->string(array('required' => true, 'field' => 'fee_name', 'value' => $fee_name), array('required' => 'Fee Name is required'));
$validate->string(array('required' => true, 'field' => 'display_fee_id', 'value' => $display_fee_id), array('required' => 'Fee ID is required'));
   
if (!empty($display_fee_id)) {
  $incr="";
  $sch_params=array();
  $sch_params[':product_code']=$display_fee_id;
  if (!empty($product_id)) {
    $incr.=" AND md5(id)!=:id";
    $sch_params[':id']=$product_id;
  } 
  $selectVendor = "SELECT id FROM prd_main WHERE product_code=:product_code $incr AND is_deleted='N' ";
  $resultVendor = $pdo->selectOne($selectVendor, $sch_params);
  if ($resultVendor) {
    $validate->setError("fee_id", "This Fee ID is already associated with another Fee");
  }
}

$validate->string(array('required' => true, 'field' => 'fee_type', 'value' => $fee_type), array('required' => 'Select Fee Type'));

if(empty($products)){
  $validate->setError("products","Select Product");
}

$validate->string(array('required' => true, 'field' => 'effective_date', 'value' => $effective_date), array('required' => 'Effective date is required'));  

if (!empty($termination_date) && !empty($effective_date)) {
  if ((strtotime($effective_date)) > (strtotime($termination_date))) {
    $validate->setError("termination_date", "Termination date must be greater than or equal to effective date.");
  }
}
if (!empty($products) && !$validate->getError('effective_date')) {
  $fincr = "";
  $fparams = array();
  if (!empty($products)) {
    $fincr .= " AND pa.product_id IN(" . implode(',', $products) . ")";
  }
  if (!empty($carrier_id)) {
    $fincr .= " AND md5(pf.id) = :carrier_id";
    $fparams[":carrier_id"] = $carrier_id;
  }
  if (!empty($product_id) && $is_clone == 'N') {
    $fincr .= " AND md5(pa.fee_id) != :id";
    $fparams[":id"] = $product_id;
  }

  $selTermPrd = "SELECT pm.pricing_effective_date AS effective_date,pm.pricing_termination_date as termination_date,p.product_code as assignedProductCode
    FROM prd_fees pf 
    JOIN prd_assign_fees pa ON(pf.id=pa.prd_fee_id AND pa.is_deleted='N')
    JOIN prd_matrix pm ON(pa.fee_id=pm.product_id AND pm.is_deleted='N') 
    JOIN prd_main p ON(p.id=pa.product_id AND p.is_deleted='N')
    WHERE pf.setting_type='Carrier' AND pf.is_deleted='N' 
    AND(
      ('".$effective_date."' BETWEEN pm.pricing_effective_date AND pm.pricing_termination_date) OR
      (pm.pricing_effective_date BETWEEN '".$effective_date."' AND '".$termination_date."') 
      OR ('".$effective_date."' >= pm.pricing_effective_date AND pm.pricing_termination_date IS NULL) 
      OR (pm.pricing_effective_date >= '".$effective_date."' AND '".$termination_date."'='') 
    )
    $fincr";
  $resTermPrd = $pdo->selectOne($selTermPrd, $fparams);

  if (!empty($resTermPrd)) {
    $validate->setError("products", "Product " . $resTermPrd["assignedProductCode"] . " is conflicted with another fee for this Carrier");
  }
}
if($is_benefit_tier=='Y'){
  if(!empty($fee_price_plan)){
    foreach ($fee_price_plan as $key => $value) {
      $validate->string(array('required' => true, 'field' => 'fee_plan_price', 'value' => $value), array('required' => 'Fee is required'));
    }
  }else{
    $validate->setError("pricing_model","Fee is required");
  }
}else{
  $validate->string(array('required' => true, 'field' => 'fee_price', 'value' => $fee_price), array('required' => 'Fee is required'));
  if($fee_price <= 0){
    $validate->setError("fee_price","Fee is required");
  }
}
    
if ($validate->isValid()) {
  
  // insert fee code start
  $insert_params = array(
    'name' => $fee_name,
    'product_code' => $display_fee_id,
    'product_type' => $product_type,
    'type' => 'Fees',
    'fee_type' => $fee_type,
    'initial_purchase' => $initial_purchase,
    'is_fee_on_renewal' => $is_fee_on_renewal,
    'fee_renewal_type' => $fee_renewal_type,
    'fee_renewal_count' => 0,
    'is_benefit_tier' => $is_benefit_tier,
  );

  if($is_fee_on_renewal=="Y"){ 
    if($fee_renewal_type=="Renewals"){
      $insert_params['fee_renewal_count']=$fee_renewal_count;
    }
      $insert_params['payment_type']= "Recurring";
  }else{
       $insert_params['payment_type']= "Single";
  }

  $insert_params['pricing_model'] = ($is_benefit_tier == 'Y' ? $pricing_model : "FixedPrice");

 
  if(!empty($carrier_id)){
    $getsql = "SELECT id FROM prd_fees WHERE md5(id)=:id";
    $params = array(':id' => $carrier_id);
    $carrier = $pdo->selectOne($getsql, $params);
    if($carrier){
      $insert_params['prd_fee_id']=$carrier['id'];
    }
  }

  $insert_params_key =  implode(",", array_keys($insert_params));
  $productSql = "SELECT id,$insert_params_key FROM prd_main WHERE md5(id)=:product_id AND is_deleted='N'";
  $productRow = $pdo->selectOne($productSql, array(":product_id" => $product_id));

  if(!empty($productRow)){
    $product_id = $productRow['id'];
    $update_where = array(
      'clause' => 'id = :id',
      'params' => array(':id' => $product_id)
    );
    $update_status = $pdo->update('prd_main', $insert_params, $update_where);

    //************* Activity Code Start *************
      $oldVaArray = $productRow;
      $NewVaArray = $insert_params;
      $activityFeedDesc=array();
      unset($oldVaArray['id']);
      $checkDiff=array_diff_assoc($NewVaArray, $oldVaArray);

      $activityFeedDesc['ac_message'] =array(
        'ac_red_1'=>array(
          'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
          'title'=>$_SESSION['admin']['display_id']),
        'ac_message_1' =>' Updated Carrier Fee ',
        'ac_red_2'=>array(
          //'href'=>'',
          'title'=>$display_fee_id,
        ),
      ); 

      if(!empty($checkDiff)){
        foreach ($checkDiff as $key1 => $value1) {
          $activityFeedDesc['key_value']['desc_arr'][$key1]='From '.$oldVaArray[$key1].' To '.$NewVaArray[$key1];
        } 
      }
    //************* Activity Code End   *************

    $response['message']= 'Fee Updated successfully';
  } else {
    $insert_params['status']='Active';
    $insert_params['term_back_to_effective'] = 'Y';
    $product_id = $pdo->insert("prd_main", $insert_params);
    array_push($carrier_fee_id, $product_id);

    //************* Activity Code Start *************
      $description['ac_message'] =array(
        'ac_red_1'=>array(
          'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
          'title'=>$_SESSION['admin']['display_id'],
        ),
        'ac_message_1' =>' Created Carrier Fee ',
        'ac_red_2'=>array(
          //'href'=>  '',
          'title'=>$display_fee_id,
        ),
      ); 
      activity_feed(3, $_SESSION['admin']['id'], 'Admin', $product_id, 'carrier','Admin Created Carrier Fee', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
    //************* Activity Code End *************

    $response['message']= 'Fee Inserted successfully';
  }
  
  $response['carrier_fee_id']=implode(",", $carrier_fee_id);

  // prd assign fee table code start
    if(!empty($product_id) && !empty($products)){

      if(!empty($productRow)){
        $assignOldSql = "SELECT GROUP_CONCAT(DISTINCT  (CONCAT(pm.name,'(',pm.product_code,')')))  as product_name 
                         FROM prd_assign_fees pa 
                         JOIN prd_main pm ON (pm.id=pa.product_id)
                         WHERE pa.is_deleted='N' AND pa.fee_id=:fee_id ";
        $assignOldRow = $pdo->selectOne($assignOldSql, array(":fee_id" => $product_id));
      }

      foreach ($products as $key => $value) {
        $insert_params = array(
          'product_id' => $value,
          'fee_id' => $product_id,
          'is_deleted' => 'N',
        );
       
        if(!empty($carrier)){
          $insert_params['prd_fee_id']=$carrier['id'];
        }
        
        $assignSql = "SELECT id FROM prd_assign_fees where product_id=:product_id AND fee_id=:fee_id ";
        $assignRow = $pdo->selectOne($assignSql, array(":product_id" => $value,":fee_id" => $product_id));
        
        if(!empty($assignRow)){
          $update_where = array(
            'clause' => 'id=:id',
            'params' => array(":id" => $assignRow['id'])
          );
          $pdo->update('prd_assign_fees', $insert_params, $update_where);

        } else {
          $prd_assign_fees = $pdo->insert("prd_assign_fees", $insert_params);
        }
      }
    }

    if(!empty($product_id)){
      $ass_incr="";
      if(!empty($products)){
        $ass_products = "'" . implode("','", makeSafe($products)) . "'";
        $ass_incr = " AND product_id not in (".$ass_products.")";
      }
      $insert_params = array('is_deleted' => 'Y');

      $update_where = array(
        'clause' => 'fee_id=:fee_id'.$ass_incr,
        'params' => array(
          ":fee_id" => $product_id,
        )
      );
      $update_status = $pdo->update('prd_assign_fees', $insert_params, $update_where);

      if(!empty($productRow)){
        $assignNewSql = "SELECT GROUP_CONCAT(DISTINCT  (CONCAT(pm.name,'(',pm.product_code,')')))  as product_name 
                         FROM prd_assign_fees pa 
                         JOIN prd_main pm ON (pm.id=pa.product_id)
                         WHERE pa.is_deleted='N' AND pa.fee_id=:fee_id ";
        $assignNewRow = $pdo->selectOne($assignNewSql, array(":fee_id" => $product_id));
        
        //************* Activity Code Start *************
          $assignCheckDiff=array_diff_assoc($assignNewRow, $assignOldRow);
          if(!empty($assignCheckDiff)){ 
            foreach ($assignCheckDiff as $key1 => $value1) {
              $activityFeedDesc['key_value']['desc_arr'][$key1]='From '.$assignOldRow[$key1].' To '.$assignNewRow[$key1]; 
            }
          }
        //************* Activity Code End   *************
      }
    }
  // prd assign fee table code ends

  if($is_benefit_tier != '' && !empty($productRow)){
    $feeOldPlanSql = "SELECT 
                        GROUP_CONCAT(DISTINCT  
                          (CONCAT
                            ( 
                              IF(px.plan_type>0,pt.title,'Fee Price') 
                              ,' => ',
                              IF(px.price_calculated_on='FixedPrice',CONCAT('$',px.price),CONCAT(px.price,'%'))
                            )
                          )
                        ) as price,px.price_calculated_on,px.price_calculated_type
                      FROM prd_matrix px
                      LEFT JOIN prd_plan_type pt ON (pt.id=px.plan_type AND pt.is_active='Y')
                      WHERE px.product_id = :product_id AND px.is_deleted='N'";
    $feeOldPlanRow = $pdo->selectOne($feeOldPlanSql, array(":product_id" => $product_id));
  }

  $feeMatrixIdArr = array();

  if($is_benefit_tier == 'Y'){
    if($pricing_model == "FixedPrice"){
      if(!empty($fee_price_plan)){
        foreach ($fee_price_plan as $key => $plan_price){

          $selPrd = "SELECT p.id as prdId,pm.id as prdMatId,pm.plan_type FROM prd_main p JOIN prd_matrix pm ON(p.id=pm.product_id) WHERE p.id IN(".implode(",", $products).") AND pm.plan_type=:planType AND pm.is_deleted='N'";
          $resPrd = $pdo->select($selPrd,array(":planType" => $key));

          $feePlanSql = "SELECT id,product_id FROM prd_matrix WHERE product_id = :product_id AND plan_type = :plan_type AND is_deleted='N'";
          $feePlanRow = $pdo->selectOne($feePlanSql, array(":product_id" => $product_id,":plan_type" => $key));

          $plan_params = array(
            'product_id' => $product_id,
            'plan_type' => $key,  
            'price_calculated_on' => $fee_method,  
            'price_calculated_type' => $percentage_type,
            'pricing_effective_date' => $effective_date,
            'pricing_termination_date' => $termination_date,
            'price' => $plan_price,
            'is_deleted'=> 'N'
          );
          $plan_params['payment_type'] = ($is_fee_on_renewal == "Y" ? "Recurring" : "Single");
          $plan_params['pricing_model'] = $pricing_model;

          if(!empty($feePlanRow["id"])){
            $fee_plan_price = $feePlanRow['id'];

            $update_plan_where = array(
              'clause' => 'id = :id and product_id = :product_id',
              'params' => array(
                ':id' => $feePlanRow['id'],
                ':product_id' => $feePlanRow['product_id']
              )
            );
            $pdo->update("prd_matrix",$plan_params, $update_plan_where);
          } else { 
            $fee_plan_price = $pdo->insert("prd_matrix", $plan_params);
          }
          
          // insert products fee pricing matrix id for selected products
          if(!empty($resPrd)){
            foreach ($resPrd as $key => $value) {
             $assignSql = "SELECT id FROM prd_fee_pricing_model WHERE prd_matrix_id=:prd_matrix_id AND prd_matrix_fee_id=:fee_id AND is_deleted='N'";
              $assignRow = $pdo->selectOne($assignSql, array(":prd_matrix_id" => $value['prdMatId'],":fee_id" => $fee_plan_price));

              if(empty($assignRow['id'])){
                $insert_params = array(
                  'product_id' => $value['prdId'],
                  'prd_matrix_id' => $value['prdMatId'],
                  'fee_product_id' =>  $product_id,
                  'prd_matrix_fee_id' => $fee_plan_price,
                );
                $pdo->insert("prd_fee_pricing_model", $insert_params);
              }
            }
          }
          array_push($feeMatrixIdArr, $fee_plan_price);
        }
      }
    }else{
      if(!empty($fee_price_plan)){
        foreach ($fee_price_plan as $key => $plan_price){

          $keyArr = explode("_",$key);
          $feePrdMatrixId = $keyArr[0];
          $prdMatrixId = $keyArr[1];

          $resPrds = array();
          if(!empty($prdMatrixId)){
            $selPrds = "SELECT p.id as prdId,pm.id as matId,pm.plan_type FROM prd_main p JOIN prd_matrix pm ON(p.id=pm.product_id) WHERE pm.id=:matId AND pm.is_deleted='N'";
            $resPrds = $pdo->selectOne($selPrds,array(":matId" =>$prdMatrixId));
          }
          
          $resFeePrd = array();
          if($feePrdMatrixId > 0){
            $selFeePrd = "SELECT p.id as prdId,pm.id as matId,pm.plan_type FROM prd_main p JOIN prd_matrix pm ON(p.id=pm.product_id) WHERE pm.id=:matId AND pm.is_deleted='N'";
            $resFeePrd = $pdo->selectOne($selFeePrd,array(":matId" =>$feePrdMatrixId));
          }

          if(!empty($resPrds)){
            if(!empty($resFeePrd['matId'])){
              $fee_plan_price = $resFeePrd['matId'];
              $plan_params = array(
                'price_calculated_on' => $fee_method,  
                'price_calculated_type' => $percentage_type,
                'pricing_effective_date' => $effective_date,
                'pricing_termination_date' => $termination_date,
                'price' => $plan_price,
                'is_deleted'=> 'N',
                'matrix_group' => $group_matrix[$key]
              );

              $plan_params['payment_type'] = ($is_fee_on_renewal == "Y" ? "Recurring" : "Single");
              $plan_params['pricing_model'] = $pricing_model;

              $update_plan_where = array(
                'clause' => 'id = :id',
                'params' => array(
                  ':id' => $fee_plan_price,
                )
              );
              $pdo->update("prd_matrix",$plan_params, $update_plan_where);
            }else{
              $plan_params = array(
                'product_id' => $product_id,
                'plan_type' => checkIsset($resPrds['plan_type']),  
                'price_calculated_on' => $fee_method,  
                'price_calculated_type' => $percentage_type,
                'pricing_effective_date' => $effective_date,
                'pricing_termination_date' => $termination_date,
                'price' => $plan_price,
                'is_deleted'=> 'N',
                'matrix_group' => $group_matrix[$key]
              );

              $plan_params['payment_type'] = ($is_fee_on_renewal == "Y" ? "Recurring" : "Single");
              $plan_params['pricing_model'] = $pricing_model;

              $fee_plan_price = $pdo->insert("prd_matrix", $plan_params);
            }

            $assignSql = "SELECT id FROM prd_fee_pricing_model where prd_matrix_id=:prd_matrix_id AND prd_matrix_fee_id=:fee_id AND is_deleted='N'";
            $assignRow = $pdo->selectOne($assignSql, array(":prd_matrix_id" => $resPrds['matId'],":fee_id" => $fee_plan_price));
            
            if(empty($assignRow)){
              $insert_params = array(
                'product_id' => $resPrds['prdId'],
                'prd_matrix_id' => $resPrds['matId'],
                'fee_product_id' =>  $product_id,
                'prd_matrix_fee_id' => $fee_plan_price,
              );
              $pdo->insert("prd_fee_pricing_model", $insert_params);
            }
            array_push($feeMatrixIdArr, $fee_plan_price);
          }
        }
      }
    }
  }else{
    $feePlanSql = "SELECT id,product_id FROM prd_matrix WHERE product_id=:product_id and plan_type = :plan_type AND is_deleted='N'";
    $feePlanRow = $pdo->selectOne($feePlanSql, array(":product_id" => $product_id,":plan_type" => 0));
    
    $plan_params = array(
      'product_id' => $product_id,
      'plan_type' => 0,  
      'price_calculated_on' => $fee_method,  
      'price_calculated_type' => $percentage_type,  
      'pricing_effective_date' => $effective_date,
      'pricing_termination_date' => $termination_date,
      'price' => $fee_price,
      'is_deleted'=>'N'
    );

    $plan_params['payment_type'] = ($is_fee_on_renewal == "Y" ? "Recurring" : "Single");
    $plan_params['pricing_model'] = "FixedPrice";

    if(!empty($feePlanRow)) {
      $fee_plan_price = $feePlanRow['id'];

      $update_plan_where = array(
        'clause' => 'id = :id and product_id = :product_id',
        'params' => array(
            ':id' => $feePlanRow['id'],
            ':product_id' => $feePlanRow['product_id']
        )
      );
      $pdo->update("prd_matrix",$plan_params, $update_plan_where);
    } else {
      $fee_plan_price = $pdo->insert("prd_matrix", $plan_params);
    }
      array_push($feeMatrixIdArr, $fee_plan_price);
  }

  if(!empty($product_id)){
    $incr = '';
    if(!empty($feeMatrixIdArr)){
      $incr .=' AND id NOT IN('.implode(",", $feeMatrixIdArr).')';
    }

    $feePlanSql = "SELECT id FROM prd_matrix where product_id = :product_id AND is_deleted='N'";
    $feePlanRow = $pdo->select($feePlanSql, array(":product_id" => $product_id));

    if(!empty($feePlanRow)){
      $plan_params = array(
        'is_deleted' => 'Y',
      );
      $update_plan_where = array(
        'clause' => 'is_deleted="N" AND product_id = :product_id '.$incr,
        'params' => array(':product_id' => $product_id)
      );
      $pdo->update("prd_matrix",$plan_params, $update_plan_where);
    }

    if($is_benefit_tier != 'Y'){
       $plan_fee_params = array(
          'is_deleted' => 'Y',
        );
        $update_plan_fee_where = array(
          'clause' => 'is_deleted="N" AND fee_product_id = :product_id',
          'params' => array(':product_id' => $product_id)
        );
        $pdo->update("prd_fee_pricing_model",$plan_fee_params, $update_plan_fee_where);
    }else{
      $feePricingSql = "SELECT id FROM prd_fee_pricing_model WHERE fee_product_id=:product_id AND is_deleted='N'";
      $feePricingRes = $pdo->selectOne($feePricingSql, array(":product_id" => $product_id));

      if(!empty($feePricingRes['id'])){
        if(!empty($feeMatrixIdArr)){
          $fee_incr =' AND prd_matrix_fee_id NOT IN('.implode(",", $feeMatrixIdArr).')';
        }
      
        $plan_fee_params = array(
          'is_deleted' => 'Y',
        );
        $update_plan_fee_where = array(
          'clause' => 'is_deleted="N" AND fee_product_id = :product_id '.$fee_incr,
          'params' => array(':product_id' => $product_id)
        );
        $pdo->update("prd_fee_pricing_model",$plan_fee_params, $update_plan_fee_where);
      }
    }

    if(!empty($productRow)){
      $feeNewPlanSql = "SELECT 
                          GROUP_CONCAT(DISTINCT  
                            (CONCAT
                              ( 
                                IF(px.plan_type>0,pt.title,'Fee Price') 
                                ,' => ',
                                IF(px.price_calculated_on='FixedPrice',CONCAT('$',px.price),CONCAT(px.price,'%'))
                              )
                            )
                          ) as price,px.price_calculated_on,px.price_calculated_type
                        FROM prd_matrix px
                        LEFT JOIN prd_plan_type pt ON (pt.id=px.plan_type AND pt.is_active='Y')
                        WHERE px.product_id = :product_id AND px.is_deleted='N'";
      $feeNewPlanRow = $pdo->selectOne($feeNewPlanSql, array(":product_id" => $product_id));

      //************* Activity Code Start *************
        $planCheckDiff=array_diff_assoc($feeOldPlanRow, $feeNewPlanRow);
        if(!empty($planCheckDiff)){ 
          foreach ($planCheckDiff as $key1 => $value1) {
            $activityFeedDesc['key_value']['desc_arr'][$key1]='From '.$feeOldPlanRow[$key1].' To '.$feeNewPlanRow[$key1]; 
          }
        }
      //************* Activity Code End   *************
    }
  }

  if($productRow){
    //************* Activity Code Start *************       
      if(!empty($activityFeedDesc) && !empty($activityFeedDesc['key_value']['desc_arr'])){
          
        activity_feed(3, $_SESSION['admin']['id'], 'Admin', $productRow['id'], 'carrier','Admin Updated Carrier Fee', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($activityFeedDesc));
      }
    //************* Activity Code End   *************
  }
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