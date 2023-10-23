<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$validate = new Validation();
$response = array();

$is_clone = checkIsset($_POST['is_clone']);
$pmpm_id = checkIsset($_POST['pmpm_id']);
$pmpm_fee_ids = !empty($_POST['ids']) ? explode(',',$_POST['ids']) : array();
$product_id = checkIsset($_POST['fee_id']); 
$pmpm_fee_id = !empty($_POST['pmpm_fee_id']) ? $_POST['pmpm_fee_id'] : 0;
$initial_pmpm_fee_id = !empty($_POST['pmpm_fee_id']) ? $_POST['pmpm_fee_id'] : 0;
$display_id = checkIsset($_POST['display_id']);
$fee_type= checkIsset($_POST['fee_type']);
$products = checkIsset($_POST['products']);
$productsArr = checkIsset($_POST['products']);

if(!empty($pmpm_id)){
  $pmpm_data = $pdo->selectOne("SELECT id FROM pmpm_commission where md5(id) = :id",array(':id' => $pmpm_id));
  if($pmpm_data){
    $pmpm_id = $pmpm_data['id'];
  }
}

$effective_date = !empty($_POST['effective_date']) ? date('Y-m-d',strtotime($_POST['effective_date'])) : '';
$termination_date = !empty($_POST['termination_date']) ? date('Y-m-d',strtotime($_POST['termination_date'])) : NULL;

$is_earned_on_new_business = checkIsset($_POST['is_earned_on_new_business']);
$is_fee_on_renewal = checkIsset($_POST['is_fee_on_renewal']);
$fee_renewal_type = checkIsset($_POST['fee_renewal_type']);

$fee_renewal_count = checkIsset($_POST['fee_renewal_count']);

$is_benefit_tier = checkIsset($_POST['is_benefit_tier']);
$pricing_model = checkIsset($_POST['pricing_model']);

$fee_method = checkIsset($_POST['fee_method']);
$percentage_type = checkIsset($_POST['percentage_type']);

$fee_price = $_POST['fee_price'];
$fee_price_plan = !empty($_POST['plan'])?$_POST['plan']:array();
$receiving_agents = !empty($_POST['receiving_agents'])?$_POST['receiving_agents']:array();
$include_loa_checked_status = !empty($_POST['include_loa_checked_status'])?$_POST['include_loa_checked_status']:array();
$downline_checked_status = !empty($_POST['downline_checked_status'])?$_POST['downline_checked_status']:array();


$validate->string(array('required' => true, 'field' => 'display_id', 'value' => $display_id), array('required' => 'Fee ID is required'));

if (!empty($display_id)) {
  $incr="";
  $sch_params=array();
  $sch_params[':display_id']=$display_id;
  if (!empty($pmpm_fee_id)) {
    $incr.=" AND id!=:id";
    $sch_params[':id']=$pmpm_fee_id;
  } 
  $selectVendor = "SELECT id FROM pmpm_commission_rule WHERE display_id=:display_id $incr AND is_deleted='N' ";
  $resultVendor = $pdo->selectOne($selectVendor, $sch_params);
  if ($resultVendor) {
    $validate->setError("display_id", "This Fee ID is already associated with another Fee");
  }
}

$validate->string(array('required' => true, 'field' => 'fee_type', 'value' => $fee_type), array('required' => 'Select Fee Type'));
$validate->string(array('required' => true, 'field' => 'is_fee_on_renewal', 'value' => $is_fee_on_renewal), array('required' => 'Please select options'));
$validate->string(array('required' => true, 'field' => 'is_earned_on_new_business', 'value' => $is_earned_on_new_business), array('required' => 'Please select options'));

if(empty($products)){
  $validate->setError("products","Select Product");
}

$validate->string(array('required' => true, 'field' => 'effective_date', 'value' => $effective_date), array('required' => 'Effective date is required'));  

$validate->string(array('required' => true, 'field' => 'is_benefit_tier', 'value' => $is_benefit_tier), array('required' => 'Please select options'));  
  
if($is_benefit_tier=='Y'){
  if(!empty($fee_price_plan)){
    foreach ($fee_price_plan as $key => $value) {
      $validate->string(array('required' => true, 'field' => 'fee_plan_price', 'value' => $value), array('required' => 'Please enter amount'));
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
if(empty($receiving_agents)){
  $validate->setError('receiving_agents',"Please select agents");
}
    
if ($validate->isValid()) {
  
  $insert_params = array(
    'display_id' => $display_id,
    'commission_id' => 0,
    'effective_date' => $effective_date,
    'termination_date' => $termination_date,
    'earned_on_new_business' => $is_earned_on_new_business,
    'earned_on_renewal' => $is_fee_on_renewal,
    'is_fee_by_benefit_tier' => $is_benefit_tier,
    'fee_renewal_type' => $fee_renewal_type,
    'amount_calculated_on' => $fee_type,
  );

  $insert_params['pricing_model'] = ($is_benefit_tier == 'Y' ? $pricing_model : "");

  if($is_fee_on_renewal=="Y"){ 
    if($fee_renewal_type=="Renewals"){
      $insert_params['number_of_renewals']=$fee_renewal_count;
    }
  }
  if($fee_type=="Percentage"){ 
    $insert_params['fee_per_calculate_on']=$percentage_type;
  }
  if(!empty($pmpm_id)){
    $insert_params['commission_id']=$pmpm_id;
  }
   
  if(!empty($pmpm_fee_id) && $is_clone == 'N'){
  
    $insert_params_key =  implode(",", array_keys($insert_params));
    $ruleSql = "SELECT id,$insert_params_key FROM pmpm_commission_rule WHERE id=:pmpm_fee_id AND is_deleted='N'";
    $ruleRow = $pdo->selectOne($ruleSql, array(":pmpm_fee_id" => $pmpm_fee_id));

    if(!empty($ruleRow)){
      $pmpm_fee_id = $ruleRow['id'];
      $update_where = array(
        'clause' => 'id = :id',
        'params' => array(
          ':id' => $pmpm_fee_id
        )
      );
      $update_status = $pdo->update('pmpm_commission_rule', $insert_params, $update_where);

      //************* Activity Code Start *************
        $oldVaArray = $ruleRow;
        $NewVaArray = $insert_params;
        $activityFeedDesc=array();
        unset($oldVaArray['id']);
        $checkDiff=array_diff_assoc($NewVaArray, $oldVaArray);
        if(!empty($checkDiff)){

          $activityFeedDesc['ac_message'] =array(
            'ac_red_1'=>array(
              'href'=>$ADMIN_HOST.'/admin_profile.php?id='. $_SESSION['admin']['id'],
              'title'=>$_SESSION['admin']['display_id']),
            'ac_message_1' =>' Updated PMPM Fee ',
            'ac_red_2'=>array(
              //'href'=>'',
              'title'=>$display_id,
            ),
          ); 

          foreach ($checkDiff as $key1 => $value1) {
            $activityFeedDesc['key_value']['desc_arr'][$key1]='From '. ($oldVaArray[$key1] ? $oldVaArray[$key1] : 'blank').' To '.$NewVaArray[$key1];
          } 
        }
      //************* Activity Code End   *************

      $response['message']= 'Fee Updated successfully';
    } 

  }else {
    $pmpm_fee_id = $pdo->insert("pmpm_commission_rule", $insert_params);
    array_push($pmpm_fee_ids, $pmpm_fee_id);

    //************* Activity Code Start *************
      $activityFeedDesc['ac_message'] =array(
        'ac_red_1'=>array(
          'href'=>$ADMIN_HOST.'/admin_profile.php?id='. $_SESSION['admin']['id'],
          'title'=>$_SESSION['admin']['display_id'],
        ),
        'ac_message_1' =>' Created PMPM Fee ',
        'ac_red_2'=>array(
          //'href'=>  '',
          'title'=>$display_id,
        ),
      ); 
      activity_feed(3, $_SESSION['admin']['id'], 'Admin', $product_id, 'pmpm_commission_rule','Admin PMPM Fee', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($activityFeedDesc));
    //************* Activity Code End *************

    $response['message']= 'Fee Inserted successfully';
  }
 
  // $response['pmpm_fee_id']=implode(",", $pmpm_fee_ids);
  $response['pmpm_fee_id']=$pmpm_fee_id;
  
  if(!empty($pmpm_fee_id) && !empty($products)){
    $old_array_value = array();
    $new_array_value = array();
    $assignOldSql = "SELECT GROUP_CONCAT(pa.product_id)  as product_ids 
                       FROM pmpm_commission_rule_assign_product pa 
                       JOIN prd_main pm ON (pm.id=pa.product_id)
                       WHERE pa.is_deleted='N' AND pa.rule_id=:rule_id ";
    $assignOldRow = $pdo->selectOne($assignOldSql, array(":rule_id" => $pmpm_fee_id));

    if($assignOldRow){
      $old_prd = $assignOldRow['product_ids'];
    }

    if($assignOldRow  && $is_clone == 'N'){
      $updateParams = array('is_deleted' => 'Y');
      $update_where = array(
        'clause' => 'rule_id=:id',
        'params' => array(
          ":id" => $pmpm_fee_id,
        )
      );
      $update_status = $pdo->update('pmpm_commission_rule_assign_product', $updateParams, $update_where);
    }

    foreach ($products as $key => $value) {
      $insert_params = array(
        'rule_id' => $pmpm_fee_id,
        'product_id' => $value,
        'is_deleted' => 'N',
      );
      
      if(!empty($pmpm_id)){
        $insert_params['commission_id']=$pmpm_id;
      }
      $prd_assign_fees = $pdo->insert("pmpm_commission_rule_assign_product", $insert_params); 
    }

    $assignNewSql = "SELECT GROUP_CONCAT(pa.product_id) as product_ids 
                       FROM pmpm_commission_rule_assign_product pa 
                       JOIN prd_main pm ON (pm.id=pa.product_id)
                       WHERE pa.is_deleted='N' AND pa.rule_id=:rule_id ";
    $assignNewRow = $pdo->selectOne($assignNewSql, array(":rule_id" => $pmpm_fee_id));

    if($assignNewRow){
      $new_prd = $assignNewRow['product_ids'];
    }
    
    
    $str = '';
    if(!empty($old_prd)){
      $old_prd_array = explode(",",$old_prd);
      $new_prd_array = explode(",",$new_prd);
      $prd_diff = array_diff($new_prd_array,$old_prd_array);
      if(count($prd_diff) > 0 && !empty($prd_diff)){
        $products = $pdo->select("SELECT name,product_code from prd_main where id IN(".implode(",",$prd_diff).")");
        if(count($new_prd_array) > count($old_prd_array)){
          $str.=" Admin added ";
          foreach ($products as $value) {
              $str.=$value['name']." (". $value['product_code'] .")";
              if(count($products) > 1)
              $str.=" ,";
          }
            $str.=" on PMPM ".$display_id."<br>";
        }else{
          
          $old_products = $pdo->select("SELECT name,product_code from prd_main where id IN(".implode(",",$old_prd_array).")");
          $new_products = $pdo->select("SELECT name,product_code from prd_main where id IN(".implode(",",$new_prd_array).")");
          foreach($old_products as $op){
            $str.=" Admin deleted ";
            $str.=$op['name']." (". $op['product_code'] .")";
          }
            $str.=" on PMPM ".$display_id."<br>";
          foreach($new_products as $np){
            $str.=" Admin added ";
            $str.=$np['name']." (". $np['product_code'] .")";
          }
            $str.=" on PMPM ".$display_id."<br>";
        }        
      }else{
        $prd_diff = array_diff($old_prd_array,$new_prd_array);
        if(count($prd_diff) > 0){
          $str.=" Admin deleted ";
          $products = $pdo->select("SELECT name,product_code from prd_main where id IN(".implode(",",$prd_diff).")");
          foreach ($products as $value) {
            $str.=$value['name']." (". $value['product_code'] .")";
              if(count($products) > 1)
              $str.=", ";
          }
            $str.=" on PMPM ".$display_id."<br>";
        }
      }
    }
    if(!empty($str)){
      $activityFeedDesc['key_value']['desc_arr']['Products']=$str;
    }
  }

  $feeOldPlanRow = array();
  $feeOldPlanSql = "SELECT 
                          GROUP_CONCAT(DISTINCT  
                            (CONCAT
                              ( 
                                IF(px.plan_type>0,pt.title,'Fee Price') 
                                ,' => ',
                                IF(pcr.amount_calculated_on='Amount',CONCAT('$',px.amount),CONCAT(px.amount,'%'))
                              )
                            )
                          ) as price,pcr.amount_calculated_on,pcr.fee_per_calculate_on
                        FROM pmpm_commission_rule_plan_type px
                        JOIN pmpm_commission_rule pcr on(pcr.id = px.rule_id AND px.is_deleted = 'N')
                        LEFT JOIN prd_plan_type pt ON (pt.id=px.plan_type AND pt.is_active='Y')
                        WHERE px.rule_id = :rule_id AND px.is_deleted='N'";
  $feeOldPlanRow = $pdo->selectOne($feeOldPlanSql, array(":rule_id" => $pmpm_fee_id));
  if($is_benefit_tier == 'Y'){
    $planRowIdArr = array();
    if(!empty($fee_price_plan) && $pricing_model == "FixedPrice"){
      foreach ($fee_price_plan as $key => $plan_price) {
        $selPrd = "SELECT p.id as prdId,pm.id as prdMatId,pm.plan_type FROM prd_main p JOIN prd_matrix pm ON(p.id=pm.product_id) WHERE p.id IN(".implode(",", $productsArr).") AND pm.plan_type=:planType AND pm.is_deleted='N'";
        $resPrd = $pdo->select($selPrd,array(":planType" => $key));
        
        if(!empty($resPrd)){
          foreach ($resPrd as $key => $product) {
            $plan_params = array(
              'commission_id' => $pmpm_id,
              'rule_id' => $pmpm_fee_id,
              'plan_type' => $product['plan_type'],
              'prd_matrix_id' => $product['prdMatId'],
              'amount' => $plan_price,
              'is_deleted'=> 'N'
            );

            $feePlanSql = "SELECT id,plan_type FROM pmpm_commission_rule_plan_type WHERE rule_id = :rule_id AND prd_matrix_id=:matrix_id";
            $feePlanRow = $pdo->selectOne($feePlanSql, array(':rule_id' => $pmpm_fee_id,":matrix_id" => $product['prdMatId']));

            if(!empty($feePlanRow['id'])  && $is_clone == 'N'){
              $update_plan_where = array(
                'clause' => 'id = :id',
                'params' => array(
                  ':id' => $feePlanRow['id'],
                )
              );
              $planRowIds = $feePlanRow['id'];
              $pdo->update("pmpm_commission_rule_plan_type",$plan_params, $update_plan_where);
            } else { 
              $planRowIds = $pdo->insert("pmpm_commission_rule_plan_type", $plan_params);
            }
            array_push($planRowIdArr, $planRowIds);
          }
        }
      }
    }else{
      if(!empty($fee_price_plan)){
        foreach ($fee_price_plan as $key => $plan_price){
          $keyArr = explode("_",$key);
          $feePMPMId = $keyArr[0];
          $prdMatrixId = $keyArr[1];

          $resPrds = array();
          if(!empty($prdMatrixId)){
            $selPrds = "SELECT p.id as prdId,pm.id as matId,pm.plan_type FROM prd_main p JOIN prd_matrix pm ON(p.id=pm.product_id) WHERE pm.id=:matId";
            $resPrds = $pdo->selectOne($selPrds,array(":matId" =>$prdMatrixId));
          }

          $resFeePlan = array();
          if($feePMPMId > 0){
            $selFeePlan = "SELECT * FROM pmpm_commission_rule_plan_type pt WHERE pt.is_deleted='N' AND pt.id=:id";
            $resFeePlan = $pdo->selectOne($selFeePlan,array(":id" =>$feePMPMId));
          }

          if(!empty($resPrds["prdId"])){
            $plan_params = array(
              'commission_id' => $pmpm_id,
              'rule_id' => $pmpm_fee_id,
              'plan_type' => $resPrds['plan_type'],
              'prd_matrix_id' => $resPrds['matId'],
              'amount' => $plan_price,
              'is_deleted'=> 'N'
            );
            if(!empty($resFeePlan['id']) && $is_clone == 'N'){
              $update_plan_where = array(
                'clause' => 'id = :id',
                'params' => array(
                  ':id' => $resFeePlan['id'],
                )
              );
              $planRowIds = $resFeePlan['id'];
              $pdo->update("pmpm_commission_rule_plan_type",$plan_params, $update_plan_where);
            }else{
              $planRowIds = $pdo->insert("pmpm_commission_rule_plan_type", $plan_params);
            }
            array_push($planRowIdArr, $planRowIds);
          }
        }
      }
    }
   $delPlanIds = '';
    if(!empty($planRowIdArr)){
      $delPlanIds =' AND id NOT IN('.implode(",", $planRowIdArr).')';
    
      $updateParams = array('is_deleted' => 'Y');
      $update_where = array(
        'clause' => 'rule_id = :id'.$delPlanIds,
        'params' => array(
          ":id" => $pmpm_fee_id
        )
      );
      $update_status = $pdo->update('pmpm_commission_rule_plan_type', $updateParams, $update_where);
    }
  }else{
    $feePlanSql = "SELECT id,plan_type FROM pmpm_commission_rule_plan_type WHERE plan_type = :plan_type AND rule_id = :rule_id";
    $feePlanRow = $pdo->selectOne($feePlanSql, array(":plan_type" => 0,":rule_id" => $pmpm_fee_id));
    
    $updateParams = array('is_deleted' => 'Y');
    $update_where = array(
      'clause' => 'rule_id = :id AND plan_type!=0',
      'params' => array(
        ":id" => $pmpm_fee_id
      )
    );
    $update_status = $pdo->update('pmpm_commission_rule_plan_type', $updateParams, $update_where);

    $plan_params = array(
      'commission_id' => $pmpm_id,
      'rule_id' => $pmpm_fee_id,
      'plan_type' => 0,
      'amount' => $fee_price,
      'is_deleted'=> 'N'
    );

    if(!empty($feePlanRow)  && $is_clone == 'N') {
      $update_plan_where = array(
        'clause' => 'id = :id and plan_type = :plan_type',
        'params' => array(
            ':id' => $feePlanRow['id'],
            ':plan_type' => $feePlanRow['plan_type']
        )
      );
      $fee_plan_price = $pdo->update("pmpm_commission_rule_plan_type",$plan_params, $update_plan_where);
    } else {
      $fee_plan_price = $pdo->insert("pmpm_commission_rule_plan_type", $plan_params);
    }
  }

  if($pmpm_fee_id){
    $feeNewPlanSql = "SELECT 
                          GROUP_CONCAT(DISTINCT  
                            (CONCAT
                              ( 
                                IF(px.plan_type>0,pt.title,'Fee Price') 
                                ,' => ',
                                IF(pcr.amount_calculated_on='Amount',CONCAT('$',px.amount),CONCAT(px.amount,'%'))
                              )
                            )
                          ) as price,pcr.amount_calculated_on,pcr.fee_per_calculate_on
                        FROM pmpm_commission_rule_plan_type px
                        JOIN pmpm_commission_rule pcr on(pcr.id = px.rule_id AND px.is_deleted = 'N')
                        LEFT JOIN prd_plan_type pt ON (pt.id=px.plan_type AND pt.is_active='Y')
                        WHERE px.rule_id = :rule_id AND px.is_deleted='N'";
    $feeNewPlanRow = $pdo->selectOne($feeNewPlanSql, array(":rule_id" => $pmpm_fee_id));

    $planCheckDiff=array_diff_assoc($feeOldPlanRow, $feeNewPlanRow);
    if(!empty($planCheckDiff)){ 
      foreach ($planCheckDiff as $key1 => $value1) {
        $activityFeedDesc['key_value']['desc_arr'][$key1]='From '. ($feeOldPlanRow[$key1] ? $feeOldPlanRow[$key1] : 'blank').' To '.$feeNewPlanRow[$key1] ." on PMPM ".$display_id; 
      }
    }
  }

  if(!empty($receiving_agents)){

    $feeOldAgentSql = "SELECT c.id,CONCAT_WS(',',c.id,c.rep_id,pcraa.include_loa,pcraa.include_downline) as agent_setting FROM pmpm_commission_rule_assign_agent pcraa JOIN customer c on(c.id = pcraa.agent_id) WHERE pcraa.is_deleted = 'N' AND pcraa.rule_id = :rule_id";
    $feeOldAgentRow = $pdo->select($feeOldAgentSql, array(":rule_id" => $pmpm_fee_id));

    $feeOldAgentSettingsSql = "SELECT DISTINCT GROUP_CONCAT(DISTINCT c.id) as agent_ids FROM pmpm_commission_rule_assign_agent pcraa JOIN customer c on(c.id = pcraa.agent_id) WHERE pcraa.is_deleted = 'N' AND pcraa.rule_id = :rule_id";
    $feeOldAgentSettingsRow = $pdo->selectOne($feeOldAgentSettingsSql, array(":rule_id" => $pmpm_fee_id));

    if($feeOldAgentSettingsRow){
      $old_prd = $feeOldAgentSettingsRow['agent_ids'];
    }

    $agent_res = $pdo->selectOne("SELECT id FROM pmpm_commission_rule_assign_agent WHERE rule_id = :rule_id AND is_deleted = 'N'",array(':rule_id' => $pmpm_fee_id));

      if($agent_res  && $is_clone == 'N'){
        $insert_params = array('is_deleted' => 'Y');
        $update_plan_where = array(
          'clause' => 'rule_id = :id',
          'params' => array(
              ':id' => $pmpm_fee_id
          )
        );
        $fee_plan_price = $pdo->update("pmpm_commission_rule_assign_agent",$insert_params, $update_plan_where);
      }


    foreach ($receiving_agents as $key => $value) {
      $insert_params = array(
        'commission_id' => $pmpm_id,
        'rule_id' => $pmpm_fee_id,
        'agent_id' => $value,
        'include_loa' => 'N',
        'include_downline' => 'N',
        'is_deleted' => 'N'
      );

      if($include_loa_checked_status){
        foreach ($include_loa_checked_status as $k => $v) {
          if($v == $value){
            $insert_params['include_loa'] = 'Y';
          }
        }
      }

      if($downline_checked_status){
        foreach ($downline_checked_status as $k => $v) {
          if($v == $value){
            $insert_params['include_downline'] = 'Y';
          }
        }
      }

      $insert_params['created_at'] = 'msqlfunc_NOW()';
      $rule_assign_agent_id = $pdo->insert('pmpm_commission_rule_assign_agent',$insert_params);

    }

    $feeNewAgentSql = "SELECT c.id,CONCAT_WS(',',c.id,c.rep_id,pcraa.include_loa,pcraa.include_downline) as agent_setting FROM pmpm_commission_rule_assign_agent pcraa JOIN customer c on(c.id = pcraa.agent_id) WHERE pcraa.is_deleted = 'N' AND pcraa.rule_id = :rule_id";
    $feeNewAgentRow = $pdo->select($feeNewAgentSql, array(":rule_id" => $pmpm_fee_id));

    $feeNewAgentSettingsSql = "SELECT GROUP_CONCAT(DISTINCT c.id) as agent_ids FROM pmpm_commission_rule_assign_agent pcraa JOIN customer c on(c.id = pcraa.agent_id) WHERE pcraa.is_deleted = 'N' AND pcraa.rule_id = :rule_id";
    $feeNewAgentSettingsRow = $pdo->selectOne($feeOldAgentSettingsSql, array(":rule_id" => $pmpm_fee_id));

    if($feeNewAgentSettingsRow){
      $new_prd = $feeNewAgentSettingsRow['agent_ids'];
    }

    $str = '';
    if(!empty($old_prd)){
      $old_prd_array = explode(",",$old_prd);
      $new_prd_array = explode(",",$new_prd);
      $prd_diff = array_diff($new_prd_array,$old_prd_array);
      if(count($prd_diff) > 0 && !empty($prd_diff)){
        $agents = $pdo->select("SELECT rep_id from customer where id IN(".implode(",",$prd_diff).")");
        if(count($new_prd_array) > count($old_prd_array)){
          $str.=" Admin added ";
          foreach ($agents as $value) {
              $str.=$value['rep_id'];
              if(count($agents) > 1)
              $str.=" ,";
          }
            $str.=" on PMPM ".$display_id."<br>";
        }else{
          
          $old_products = $pdo->select("SELECT rep_id from customer where id IN(".implode(",",$old_prd_array).")");
          $new_products = $pdo->select("SELECT rep_id from customer where id IN(".implode(",",$new_prd_array).")");
          foreach($old_products as $op){
            $str.=" Admin deleted ";
            $str.=$op['rep_id'];
          }
            $str.=" <br>";
          foreach($new_products as $np){
            $str.=" Admin added ";
            $str.=$np['rep_id'];
          }
            $str.=" on PMPM ".$display_id."<br>";
        }        
      }else{
        $prd_diff = array_diff($old_prd_array,$new_prd_array);
        if(count($prd_diff) > 0){
          $str.=" Admin deleted ";
          $products = $pdo->select("SELECT rep_id from customer where id IN(".implode(",",$prd_diff).")");
          foreach ($products as $value) {
            $str.=$value['rep_id'];
              if(count($products) > 1)
              $str.=", ";
          }
            $str.=" on PMPM ".$display_id."<br>";
        }
      }
    }
    if(!empty($str)){
      $activityFeedDesc['key_value']['desc_arr']['Agents']=$str;
    }
    $str = "";
    if($feeOldAgentRow){
      $temp_old_agents = array();
      foreach ($feeOldAgentRow as $k => $v) {
        $temp_old_agents[$v['id']] = $v['agent_setting'];
      }
      $agents_updated = array_map('unserialize',array_diff(array_map('serialize', $feeNewAgentRow), array_map('serialize', $feeOldAgentRow)));
      if($agents_updated){
        if($temp_old_agents){
          // $str .= "<br> ";
        }
        
        foreach ($agents_updated as $k => $v) {
          $agents_data = explode(',', $v['agent_setting']);
          if($temp_old_agents){
            $old_agents_data = (isset($temp_old_agents[$v['id']]) ? explode(',', $temp_old_agents[$v['id']]) : array());  
          }
          
          if(isset($old_agents_data[2]) && $old_agents_data[2] != $agents_data[2]){
            $str.= "Admin updated " . $agents_data[1] . " include LOA From " . ($old_agents_data[2] == 'Y' ? 'Yes' : 'No') . " To ". ($agents_data[2] == 'Y' ? 'Yes' : 'No')." on PMPM ".$display_id."<br>";
          }
          if(isset($old_agents_data[2]) && $old_agents_data[3] != $agents_data[3]){
            $str.= "Admin updated " . $agents_data[1] . " include Downline From " . ($old_agents_data[3] == 'Y' ? 'Yes' : 'No') . " To ". ($agents_data[3] == 'Y' ? 'Yes' : 'No')." on PMPM ".$display_id."<br>";
          }
          
          // $str.= $agents_data[1] . " included Downline " . ($agents_data[2] == 'Y' ? 'Yes' : 'No') . "<br>";
        }
      }
    }

    if(!empty($str)){
      // pre_print($str);
      $activityFeedDesc['key_value']['desc_arr']['Agents']=$str;
    }
  }
  
  if(!empty($activityFeedDesc) && !empty($initial_pmpm_fee_id)){
    activity_feed(3, $_SESSION['admin']['id'], 'Admin', $pmpm_fee_id, 'prd_main','Admin updated PMPM commission', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($activityFeedDesc));
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