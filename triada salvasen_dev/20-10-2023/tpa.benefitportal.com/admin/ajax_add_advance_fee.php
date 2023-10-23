<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$validate = new Validation();
$response = array();

// variable init code start

  $ruleType = checkIsset($_POST['ruleType']);
  $agentId = checkIsset($_POST['agentId']);
  $advFeeId = !empty($_POST['advFeeId']) ? $_POST['advFeeId'] : 0;
  $advRuleId = !empty($_POST['advRuleId']) ? $_POST['advRuleId'] : 0;
  $advFeeIds = !empty($_POST['advFeeIds']) ? explode(",",$_POST['advFeeIds']) : array();

  $is_clone = checkIsset($_POST['is_clone']);

  $products = checkIsset($_POST['products']);
  $advance_month = isset($_POST['advance_month']) ? $_POST['advance_month'] : "";
  $display_id = checkIsset($_POST['display_id']);
  $effective_date = !empty($_POST['effective_date']) ? date('Y-m-d',strtotime($_POST['effective_date'])) : '';
  $termination_date = !empty($_POST['termination_date']) ? date('Y-m-d',strtotime($_POST['termination_date'])) : NULL;
  $price_calculated_on= checkIsset($_POST['price_calculated_on']);
  $price_calculated_type = checkIsset($_POST['price_calculated_type']);

  $is_fee_on_new_business = checkIsset($_POST['is_fee_on_new_business']);
  $is_fee_on_renewal = checkIsset($_POST['is_fee_on_renewal']);
  $fee_renewal_type = checkIsset($_POST['renewal_type']);

  $fee_renewal_count = checkIsset($_POST['number_of_renewals']);
  $processing_fee = $_POST['processing_fee'];

  $range_service_fee = isset($_POST['range_service_fee']) ? $_POST['range_service_fee'] : array();
  $order_min_total = isset($_POST['order_min_total']) ? $_POST['order_min_total'] : array();
  $order_max_total = isset($_POST['order_max_total']) ? $_POST['order_max_total'] : array();

  $pricingModel = ($price_calculated_on == "VariableAmount") ? "VariableAmount" : "FixedPrice";
// variable init code ends

// validation code start
  $validate->string(array('required' => true, 'field' => 'display_id', 'value' => $display_id), array('required' => 'Fee ID is required'));
  if (!empty($display_id)) {
    $feeIncr="";
    $feeParams=array();
    $feeParams[':product_code']=$display_id;
    if (!empty($advFeeId)) {
      $feeIncr.=" AND md5(id)!=:id";
      $feeParams[':id']=$advFeeId;
    } 
    $selFee = "SELECT id FROM prd_main WHERE product_code=:product_code $feeIncr AND is_deleted='N' ";
    $resFee = $pdo->selectOne($selFee, $feeParams);
    if ($resFee) {
      $validate->setError("fee_id", "This Fee ID is already associated with another Fee");
    }
  }
  $validate->string(array('required' => true, 'field' => 'price_calculated_on', 'value' => $price_calculated_on), array('required' => 'Select Fee Type'));
  $validate->string(array('required' => true, 'field' => 'is_fee_on_new_business', 'value' => $is_fee_on_new_business), array('required' => 'Please select options'));
  $validate->string(array('required' => true, 'field' => 'is_fee_on_renewal', 'value' => $is_fee_on_renewal), array('required' => 'Please select options'));
  
  if(empty($products)){
    $validate->setError("products","Please select Product");
  }
  
  $validate->string(array('required' => true, 'field' => 'effective_date', 'value' => $effective_date), array('required' => 'Effective date is required'));  

  if (!empty($termination_date) && !empty($effective_date)) {
    if((strtotime($effective_date)) > (strtotime($termination_date))){
      $validate->setError("termination_date", "Termination date must be greater than or equal to effective date.");
    }
  }

  // assigned products validation code start
  if(!empty($products) && !$validate->getError('effective_date')){
    if($ruleType == 'Global'){
      $globalIncr = "";
      $globalParams = array();

      if(!empty($products)){
        $globalIncr .= " AND pa.product_id IN(".implode(',',$products).")";
      }
      if(!empty($advFeeId) && $is_clone == 'N'){
        $globalIncr .= " AND md5(pa.fee_id) != :id";
        $globalParams[":id"] = $advFeeId;
      }
      $selGlobPrd = "SELECT pm.pricing_effective_date AS effective_date,pm.pricing_termination_date as termination_date,p.product_code as assignedProductCode
                      FROM prd_fees pf 
                      JOIN prd_assign_fees pa ON(pf.id=pa.prd_fee_id AND pa.is_deleted='N')
                      JOIN prd_matrix pm ON(pa.fee_id=pm.product_id AND pm.is_deleted='N') 
                      JOIN prd_main p ON(p.id=pa.product_id AND p.is_deleted='N')
                      WHERE pf.setting_type='ServiceFee' AND pf.rule_type='Global' AND pf.is_deleted='N'
                      AND(
                        ('".$effective_date."' BETWEEN pm.pricing_effective_date AND pm.pricing_termination_date) OR
                        (pm.pricing_effective_date BETWEEN '".$effective_date."' AND '".$termination_date."') 
                        OR ('".$effective_date."' >= pm.pricing_effective_date AND pm.pricing_termination_date IS NULL) 
                        OR (pm.pricing_effective_date >= '".$effective_date."' AND '".$termination_date."'='') 
                      ) $globalIncr";
      $resGlobPrd = $pdo->selectOne($selGlobPrd,$globalParams);
      if(!empty($resGlobPrd)){
        $validate->setError("products", "Product " . $resGlobPrd["assignedProductCode"] . " is conflicted with another fee for this Advance Commission Rule");
      }

    }else{
      $varyIncr = "";
      $varySchParams = array();
      if(!empty($products)){
        $varyIncr .= " AND pa.product_id IN(".implode(',',$products).")";
      }
      if(!empty($agentId)){
        $varyIncr .= " AND md5(pf.agent_id) = :agentId"; 
        $varySchParams[":agentId"] = $agentId;
      }
      if(!empty($advFeeId) && $is_clone == 'N'){
        $varyIncr .= " AND md5(pa.fee_id) != :id";
        $varySchParams[":id"] = $advFeeId;
      }

      $selVaryPrd = "SELECT pm.pricing_effective_date AS effective_date,pm.pricing_termination_date as termination_date,p.product_code as assignedProductCode
                      FROM prd_fees pf 
                      JOIN prd_assign_fees pa ON(pf.id=pa.prd_fee_id AND pa.is_deleted='N')
                      JOIN prd_matrix pm ON(pa.fee_id=pm.product_id AND pm.is_deleted='N') 
                      JOIN prd_main p ON(p.id=pa.product_id AND p.is_deleted='N')
                      WHERE pf.setting_type='ServiceFee' AND pf.rule_type='Variation' 
                      AND pf.is_deleted='N' 
                      AND(
                        ('".$effective_date."' BETWEEN pm.pricing_effective_date AND pm.pricing_termination_date) OR
                        (pm.pricing_effective_date BETWEEN '".$effective_date."' AND '".$termination_date."') 
                        OR ('".$effective_date."' >= pm.pricing_effective_date AND pm.pricing_termination_date IS NULL) 
                        OR (pm.pricing_effective_date >= '".$effective_date."' AND '".$termination_date."'='') 
                      ) $varyIncr";
      $resVaryPrd = $pdo->selectOne($selVaryPrd,$varySchParams);

      if(!empty($resVaryPrd)){
        $validate->setError("products", "Product " . $resVaryPrd["assignedProductCode"] . " is conflicted with another fee for this Advance Commission Rule");
      }

    }
 
    if(!empty($advFeeIds)){
      $fincr = "";
      $fparams = array();
      if(!empty($products)){
        $fincr .= " AND pa.product_id IN(".implode(',',$products).")";
      }
      if(!empty($advFeeId) && $is_clone == 'N'){
        $fincr .= " AND md5(pa.fee_id) != :id";
        $fparams[":id"] = $advFeeId;
      }


      $selRulePrd = "SELECT pm.pricing_effective_date AS effective_date,pm.pricing_termination_date as termination_date,p.product_code as assignedProductCode
            FROM prd_assign_fees pa
            JOIN prd_matrix pm ON(pa.fee_id=pm.product_id AND pm.is_deleted='N')
            JOIN prd_main p ON(p.id=pa.product_id AND p.is_deleted='N')
            WHERE pa.is_deleted='N'
            AND(
              ('".$effective_date."' BETWEEN pm.pricing_effective_date AND pm.pricing_termination_date) 
              OR (pm.pricing_effective_date BETWEEN '".$effective_date."' AND '".$termination_date."')
              OR ('".$effective_date."' >= pm.pricing_effective_date AND pm.pricing_termination_date IS NULL) 
              OR (pm.pricing_effective_date >= '".$effective_date."' AND '".$termination_date."'='') 
            )
            AND pa.fee_id IN(".implode(',',$advFeeIds).") $fincr";
      $resRulePrd = $pdo->selectOne($selRulePrd, $fparams);
      if(!empty($resRulePrd)){
        $validate->setError("products", "Product " . $resRulePrd["assignedProductCode"] . " is conflicted with another fee for this Advance Commission Rule");
      }
  }
}
  // assigned products validation code ends

  $validate->string(array('required' => true, 'field' => 'advance_month', 'value' => $advance_month), array('required' => 'Please select advance months'));


  if($pricingModel == 'VariableAmount'){
     foreach ($range_service_fee as $key => $value) {
      $validate->string(array('required' => true, 'field' => "range_service_fee_$key", 'value' => $value), array('required' => 'Please enter fee'));
    }
    foreach ($order_min_total as $key => $value) {
      $validate->string(array('required' => true, 'field' => "order_min_total_$key", 'value' => $value), array('required' => 'Please enter minimum order total'));
    }
    foreach ($order_max_total as $key => $value) {
      $validate->string(array('required' => true, 'field' => "order_max_total_$key", 'value' => $value), array('required' => 'Please enter maximum order total'));
    }
  }else{
    $validate->string(array('required' => true, 'field' => 'processing_fee', 'value' => $processing_fee), array('required' => 'Please enter processing fee'));
    if($price_calculated_on == 'Percentage'){
        $validate->string(array('required' => true, 'field' => 'price_calculated_type', 'value' => $price_calculated_type), array('required' => 'Please select option')); 
        if($processing_fee > 100) {
          $validate->setError("processing_fee","Maximum 100% is allowed");
        }
    } 
  }
// validation code ends

      
if ($validate->isValid()) {
  $productRow = array();

  // insert fee product code start
    $feePrdParams = array(
      'product_code' => $display_id,
      'name' => "Service Fee",
      'product_type' => "ServiceFee",
      'type' => 'Fees',
      'fee_type' => 'Charged',
      'initial_purchase' => $is_fee_on_new_business,
      'is_fee_on_renewal' => $is_fee_on_renewal,
      'fee_renewal_type' => $fee_renewal_type,
      'fee_renewal_count' => 0,
      'pricing_model' => $pricingModel,
      'advance_month' => $advance_month,
    );

    if($is_fee_on_renewal=="Y"){ 
      if($fee_renewal_type=="Renewals"){
        $feePrdParams['fee_renewal_count']=$fee_renewal_count;
      }
      $feePrdParams['payment_type']= "Recurring";
    }else{
      $feePrdParams['payment_type']= "Single";
    }
    
    $feePrdParams['record_type'] = ($ruleType == "Variation") ? "Variation" : "Primary";
  

    if(!empty($advRuleId)){
      $sqlAdvRule = "SELECT id FROM prd_fees WHERE md5(id)=:id";
      $paramsRule = array(':id' => $advRuleId);
      $resAdvRuleRow = $pdo->selectOne($sqlAdvRule, $paramsRule);
      if(!empty($resAdvRuleRow['id'])){
        $feePrdParams['prd_fee_id']=$resAdvRuleRow['id'];
      }
    }
    
    $feeParamsKey =  implode(",", array_keys($feePrdParams));
    $productSql = "SELECT id,$feeParamsKey FROM prd_main WHERE md5(id)=:advFeeId AND is_deleted='N'";
    $productRow = $pdo->selectOne($productSql, array(":advFeeId" => $advFeeId));

    if(!empty($productRow) && $is_clone == 'N'){
      $advFeeId = $productRow['id'];
      $updWhere = array(
        'clause' => 'id = :id',
        'params' => array(
          ':id' => $advFeeId
        )
      );
      $update_status = $pdo->update('prd_main', $feePrdParams, $updWhere);
      //************* Activity Code Start *************
        $oldVaArray = $productRow;
        $NewVaArray = $feePrdParams;
        $activityFeedDesc=array();
        unset($oldVaArray['id']);
        $checkDiff=array_diff_assoc($NewVaArray, $oldVaArray);

        $activityFeedDesc['ac_message'] =array(
          'ac_red_1'=>array(
            'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
            'title'=>$_SESSION['admin']['display_id']),
          'ac_message_1' =>' Updated Advance Fee ',
          'ac_red_2'=>array(
            'href'=>'',
            'title'=>$display_id,
          ),
        ); 

        if(!empty($checkDiff)){
          foreach ($checkDiff as $key1 => $value1) {
            $activityFeedDesc['key_value']['desc_arr'][$key1]='From '.$oldVaArray[$key1].' To '.$NewVaArray[$key1];
          } 
        }
      //************* Activity Code End   *************
    } else {
      $feePrdParams['status']='Active';
      $feePrdParams['term_back_to_effective'] = 'Y';
      $advFeeId = $pdo->insert("prd_main", $feePrdParams);
      array_push($advFeeIds, $advFeeId);
      //************* Activity Code Start *************
        $description['ac_message'] =array(
          'ac_red_1'=>array(
            'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
            'title'=>$_SESSION['admin']['display_id'],
          ),
          'ac_message_1' =>' Created Advance Fee ',
          'ac_red_2'=>array(
            //'href'=>  '',
            'title'=>$display_id,
          ),
        ); 
        activity_feed(3, $_SESSION['admin']['id'], 'Admin', $advFeeId, 'prd_main','Admin Added Advance Fee', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
      //************* Activity Code End *************
    }

    $response['advFeeIds']= implode(",", $advFeeIds);
  // insert fee product code ends

  // prd assign fee table code start
    if(!empty($advFeeId) && !empty($products)){

      if(!empty($productRow)){
        $assignOldSql = "SELECT GROUP_CONCAT(DISTINCT  (CONCAT(pm.name,'(',pm.product_code,')')))  as product_name 
                         FROM prd_assign_fees pa 
                         JOIN prd_main pm ON (pm.id=pa.product_id AND pm.is_deleted='N')
                         WHERE pa.is_deleted='N' AND pa.fee_id=:fee_id ";
        $assignOldRow = $pdo->selectOne($assignOldSql, array(":fee_id" => $advFeeId));
      }

      foreach ($products as $key => $value) {
        $insert_params = array(
          'product_id' => $value,
          'fee_id' => $advFeeId,
        );
        
        if(!empty($resAdvRuleRow['id'])){
          $insert_params['prd_fee_id']=$resAdvRuleRow['id'];
        }
        
        $assignSql = "SELECT id FROM prd_assign_fees where product_id=:product_id AND fee_id=:fee_id AND is_deleted='N'";
        $assignRow = $pdo->selectOne($assignSql, array(":product_id" => $value,":fee_id" => $advFeeId));
        
        if(!empty($assignRow)){
          $update_where = array(
            'clause' => 'id=:id',
            'params' => array(
              ":id" => $assignRow['id'],
            )
          );
          $pdo->update('prd_assign_fees', $insert_params, $update_where);

          $response['message']= 'Fee Updated successfully';
        } else {
          $prd_assign_fees = $pdo->insert("prd_assign_fees", $insert_params);
        }
      }
      
      if(!empty($agentId)){
        $agent_id = getname('customer', $agentId, 'id', 'md5(id)');

        $agentPrd = "SELECT id FROM agent_product_rule WHERE agent_id=:agent_id AND product_id=:prdId AND is_deleted='N'";
        $resAgentPrd = $pdo->selectOne($agentPrd, array(":agent_id" => $agent_id,":prdId" => $advFeeId));

        if(empty($resAgentPrd['id'])){
          $agentPrdSql = array(
                        "agent_id" => $agent_id,
                        "admin_id" => $_SESSION['admin']['id'],
                        "product_id" => $advFeeId,
                        "status" => "Contracted"
                        );
          $pdo->insert("agent_product_rule",$agentPrdSql);
        }
      }
    }

    if(!empty($advFeeId)){
      $ass_incr="";
      if(!empty($products)){
        $ass_products = "'" . implode("','", makeSafe($products)) . "'";
        $ass_incr = " AND product_id not in (".$ass_products.")";
      }
      $insert_params = array('is_deleted' => 'Y');

      $update_where = array(
        'clause' => 'fee_id=:fee_id'.$ass_incr,
        'params' => array(
          ":fee_id" => $advFeeId,
        )
      );
      $pdo->update('prd_assign_fees', $insert_params, $update_where);

      if(!empty($productRow)){
        $assignNewSql = "SELECT GROUP_CONCAT(DISTINCT  (CONCAT(pm.name,'(',pm.product_code,')')))  as product_name 
                         FROM prd_assign_fees pa 
                         JOIN prd_main pm ON (pm.id=pa.product_id AND pm.is_deleted='N')
                         WHERE pa.is_deleted='N' AND pa.fee_id=:fee_id ";
        $assignNewRow = $pdo->selectOne($assignNewSql, array(":fee_id" => $advFeeId));
        
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

  if(!empty($productRow)){
    $feeOldPlanSql = "SELECT 
                        GROUP_CONCAT(DISTINCT  
                          (CONCAT
                            ( 
                              IF(px.plan_type>0,pt.title,'Fee Price') 
                              ,' => ',
                              IF(px.price_calculated_on='FixedPrice',CONCAT('$',px.price),CONCAT(px.price,'%'))
                            )
                          )
                        ) as price,px.price_calculated_on,px.price_calculated_type,px.pricing_effective_date,px.pricing_termination_date
                      FROM prd_matrix px
                      LEFT JOIN prd_plan_type pt ON (pt.id=px.plan_type AND is_active='Y')
                      WHERE product_id = :product_id AND is_deleted='N'";
    $feeOldPlanRow = $pdo->selectOne($feeOldPlanSql, array(":product_id" => $advFeeId));
  }
  
  // insert fee price code start
  if(!empty($advFeeId)){
    $prdMatIds = array();

    if($pricingModel == 'VariableAmount'){
      if(!empty($range_service_fee)){
        $plan_params = array(
          'product_id' => $advFeeId,
          'plan_type' => 0,  
          'price_calculated_on' => "FixedPrice",
          'price_calculated_type' => checkIsset($price_calculated_type),
          'pricing_effective_date' => $effective_date,
          'pricing_termination_date' => $termination_date,
          'is_deleted'=>'N'
        );

        $plan_params['payment_type'] = ($is_fee_on_renewal == "Y" ? "Recurring" : "Single");
        $plan_params['pricing_model'] = $pricingModel;

        foreach ($range_service_fee as $key => $service_fee) {
          $minTotal = 0;
          $maxTotal = 0;
          $feeMatId = 0;

          $minTotal = $order_min_total[$key];
          $maxTotal = $order_max_total[$key];

          $plan_params['price'] = checkIsset($service_fee);
          $resMatrix = array();
          
          if($key < 0 && $is_clone == 'N'){
            $selMatrix = "SELECT pm.id as pmtId,pmc.id as catId
                          FROM prd_matrix pm 
                          LEFT JOIN prd_matrix_criteria pmc ON(pm.id=pmc.prd_matrix_id AND pmc.is_deleted='N')
                          WHERE pm.id=:id AND pm.is_deleted='N'";
            $resMatrix = $pdo->selectOne($selMatrix,array(":id" => $key));
          }

          if(!empty($resMatrix['pmtId'])){
            $feeMatId = $resMatrix['pmtId'];
            $updWhere = array(
              'clause' => 'id = :id',
              'params' => array(
                ':id' => $feeMatId
              )
            );
            $pdo->update('prd_matrix', $plan_params, $updWhere);
            array_push($prdMatIds,$feeMatId);
          }else{
            $feeMatId = $pdo->insert("prd_matrix", $plan_params);
            array_push($prdMatIds,$feeMatId);
          }

          if(!empty($resMatrix['catId'])){
            $catId = $resMatrix['catId'];
            $updParams = array(
              'min_total' => $minTotal,
              'max_total' => $maxTotal,
            );
            $updWhere = array(
              'clause' => 'id = :id',
              'params' => array(
                ':id' => $catId
              )
            );
            $pdo->update('prd_matrix_criteria', $updParams, $updWhere);
          }else{
            $insParams = array(
              'product_id' => $advFeeId,
              'prd_matrix_id' => $feeMatId,
              'min_total' => $minTotal,
              'max_total' => $maxTotal,
            );
            $catId = $pdo->insert('prd_matrix_criteria',$insParams);

            $catParams = array("matrix_group" => $catId);
            $updWhere = array(
              'clause' => 'id = :id',
              'params' => array(
                ':id' => $feeMatId
              )
            );
            $pdo->update('prd_matrix', $catParams, $updWhere);
          }
        }
      }
    }else{
      $feePlanSql = "SELECT id,product_id FROM prd_matrix WHERE is_deleted='N' AND product_id=:fee_id and plan_type = :plan_type AND is_deleted='N'";
      $feePlanRow = $pdo->selectOne($feePlanSql, array(":fee_id" => $advFeeId,":plan_type" => 0));
    
      $plan_params = array(
        'product_id' => $advFeeId,
        'plan_type' => 0,  
        'price_calculated_on' => checkIsset($price_calculated_on),
        'price_calculated_type' => checkIsset($price_calculated_type),
        'pricing_effective_date' => $effective_date,
        'pricing_termination_date' => $termination_date,
        'price' => checkIsset($processing_fee),
        'is_deleted'=>'N'
      );

      $plan_params['payment_type'] = ($is_fee_on_renewal == "Y" ? "Recurring" : "Single");
      $plan_params['pricing_model'] = $pricingModel;

      if(!empty($feePlanRow["id"])){
        $feeMatId = $feePlanRow['id'];

        $update_plan_where = array(
          'clause' => 'id = :id and product_id = :product_id',
          'params' => array(
            ':id' => $feePlanRow['id'],
            ':product_id' => $feePlanRow['product_id']
          )
        );
        $pdo->update("prd_matrix",$plan_params, $update_plan_where);
         array_push($prdMatIds,$feeMatId);
      } else { 
        $feeMatId = $pdo->insert("prd_matrix", $plan_params);
        array_push($prdMatIds,$feeMatId);
      }
    }

    $matIncr = '';
    if(!empty($prdMatIds)){
      $matIncr .=' AND id NOT IN('.implode(",", $prdMatIds).')';

      $feePlanSql = "SELECT id FROM prd_matrix WHERE product_id = :product_id AND is_deleted='N'";
      $feePlanRow = $pdo->select($feePlanSql, array(":product_id" => $advFeeId));

      if(!empty($feePlanRow)){
        $planParams = array(
          'is_deleted' => 'Y',
        );
        $updPlanWhere = array(
          'clause' => 'is_deleted="N" AND product_id = :product_id '.$matIncr,
          'params' => array(':product_id' => $advFeeId)
        );
        $pdo->update("prd_matrix",$planParams, $updPlanWhere);
      }

      $feeMatrixSql = "SELECT id FROM prd_matrix_criteria WHERE product_id=:product_id AND is_deleted='N'";
      $feeMatrixRes = $pdo->selectOne($feeMatrixSql, array(":product_id" => $advFeeId));

      if(!empty($feeMatrixRes['id'])){
        $fee_incr =' AND prd_matrix_id NOT IN('.implode(",", $prdMatIds).')';
      
        $catParams = array(
          'is_deleted' => 'Y',
        );
        $catWhere = array(
          'clause' => 'is_deleted="N" AND product_id = :product_id '.$fee_incr,
          'params' => array(':product_id' => $advFeeId)
        );
        $pdo->update("prd_matrix_criteria",$catParams, $catWhere);
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
                          ) as price,px.price_calculated_on,px.price_calculated_type,px.pricing_effective_date,px.pricing_termination_date
                        FROM prd_matrix px
                        LEFT JOIN prd_plan_type pt ON (pt.id=px.plan_type AND is_active='Y')
                        WHERE product_id = :product_id AND is_deleted='N'";
      $feeNewPlanRow = $pdo->selectOne($feeNewPlanSql, array(":product_id" => $advFeeId));

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
        activity_feed(3, $_SESSION['admin']['id'], 'Admin', $productRow['id'], 'prd_main','Admin Updated Advance Fee', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($activityFeedDesc));
      }
    //************* Activity Code End   *************
  }

  $response['message']= 'Advance Commission Rule Saved successfully';
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