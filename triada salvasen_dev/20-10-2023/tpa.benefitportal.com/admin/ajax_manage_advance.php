<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$validate = new Validation();
$res = array();

// variable init code start
  $advRuleId = checkIsset($_POST['advRuleId']);
  $advFeeIds = !empty($_POST['advFeeIds']) ? explode(",", $_POST['advFeeIds']) : array();
  $displayId = checkIsset($_POST['display_id']);
  $is_clone = !empty($_POST['is_clone']) ? $_POST['is_clone'] : "N";

  $status = isset($_POST['status']) ? $_POST['status'] : "";
  $chargedTo = checkIsset($_POST['chargedTo']);
  $ruleType = checkIsset($_POST['ruleType']);

  $agentId = isset($_POST['receiving_agent']) ? $_POST['receiving_agent'] : "";
// variable init code ends

// validation code start
  if(isset($_POST['receiving_agent'])){
    $validate->string(array('required' => true, 'field' => 'receiving_agent', 'value' => $agentId), array('required' => 'Please select Agent'));
  }
  $validate->string(array('required' => true, 'field' => 'display_id', 'value' => $displayId), array('required' => 'Advance ID is required'));
    if(!$validate->getError('display_id')){
      $ruleIncr ="";
      $ruleParams = array();
      $ruleParams[':display_id']=$displayId;
      if (!empty($advRuleId)) {
        $ruleIncr.=" AND md5(id)!=:id";
        $ruleParams[':id']=$advRuleId;
      } 

      $sqlAdv = "SELECT id FROM prd_fees WHERE setting_type='ServiceFee' AND display_id=:display_id $ruleIncr AND is_deleted='N' ";
      $resAdv = $pdo->selectOne($sqlAdv, $ruleParams);

      if(!empty($resAdv['id'])){
        $validate->setError("display_id", "This Advance ID is already associated with another Rule");
      }
    }
  $validate->string(array('required' => true, 'field' => 'status', 'value' => $status), array('required' => 'Status is required'));

  if(empty($advFeeIds)){
    $validate->setError("advFeeIds", "Please Add Fee");
  }
  
  // Only one global rule can be added at a time
    $incr = '';
    $schParams = array();

    if(!empty($advRuleId)){
      $incr .= " AND md5(id) !=:id";
      $schParams[":id"] = $advRuleId;
    }
    if(!empty($chargedTo)){
      $incr .= " AND charged_to = :chargedTo";
      $schParams[":chargedTo"] = $chargedTo; 
    }
    if($ruleType == 'Global'){
      $selGlobalRule = "SELECT id,status,display_id,setting_type FROM prd_fees WHERE setting_type='ServiceFee' AND rule_type='Global' AND status='Active' AND is_deleted='N' $incr";
      $resGlobalRule = $pdo->selectOne($selGlobalRule,$schParams);
      if(!empty($resGlobalRule['id'])){
         $validate->setError("display_id", "Global Rule is already exist in System");
      }
    }
// validation code ends

if ($validate->isValid()) {

  // prd_fees code start
    $insParams = array(
      'display_id' => $displayId,
      'charged_to' => $chargedTo,
      'rule_type' => $ruleType,
      'status' => $status,
      'setting_type' => 'ServiceFee',
    );

    if(!empty($agentId)){
      $insParams['agent_id'] = $agentId;
    }



    $insParamsKey =  implode(",", array_keys($insParams));
    
    $feeSql = "SELECT id,$insParamsKey FROM prd_fees WHERE md5(id)=:id AND is_deleted='N'";
    $feeRow = $pdo->selectOne($feeSql, array(":id" => $advRuleId));

    if(!empty($feeRow) && $is_clone == 'N'){ 

      $advRuleId = $feeRow['id'];
      $updWhere = array(
        'clause' => 'id = :id',
        'params' => array(':id' => $feeRow['id'])
      );
      $pdo->update('prd_fees', $insParams, $updWhere);

      //************* Activity Code Start *************
        $oldValArr = $feeRow;
        $newValArr = $insParams;
        unset($oldValArr['id']);

        $checkDiff=array_diff_assoc($newValArr, $oldValArr);
         
        if(!empty($checkDiff)){
          $activityFeedDesc['ac_message'] =array(
            'ac_red_1'=>array(
              'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
              'title'=>$_SESSION['admin']['display_id']),
            'ac_message_1' =>' Updated Advance Commission Rule ',
            'ac_red_2'=>array(
              'href'=> $ADMIN_HOST.'/advances_commission.php',
              'title'=>$displayId,
            ),
          ); 
          
          if(!empty($checkDiff)){
            foreach ($checkDiff as $key1 => $value1) {
              $activityFeedDesc['key_value']['desc_arr'][$key1]='From '.$oldValArr[$key1].' To '.$newValArr[$key1];
            } 
          }
          
          activity_feed(3, $_SESSION['admin']['id'], 'Admin', $advRuleId, 'prd_fees','Admin Updated Advance Commission Rule', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($activityFeedDesc));
        }
      //************* Activity Code End   *************

      $res['message']="Advance Commission Rule Updated successfully";
    }else{
      $advRuleId = $pdo->insert("prd_fees", $insParams);

      //************* Activity Code Start *************
        $description['ac_message'] =array(
          'ac_red_1'=>array(
            'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
            'title'=>$_SESSION['admin']['display_id'],
          ),
          'ac_message_1' =>' Created Advance Commission Rule',
          'ac_red_2'=>array(
            'href'=> $ADMIN_HOST.'/advances_commission.php',
            'title'=>$displayId,
          ),
        ); 
        activity_feed(3, $_SESSION['admin']['id'], 'Admin', $advRuleId, 'prd_fees','Admin Added Advance Commission Rule', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));
      //************* Activity Code End *************

      $res['message']="Advance Commission Rule created successfully";
    }

    // link advance fee to advance rule 
    if(!empty($advFeeIds)){
      foreach ($advFeeIds as $feePrd) {

        $updParam = array('prd_fee_id'=>$advRuleId);
        $updWhere = array(
          'clause' => 'id = :id',
          'params' => array(':id' => $feePrd)
        ); 
        $pdo->update('prd_main', $updParam, $updWhere); 

        $assignSql = "SELECT id FROM prd_assign_fees WHERE fee_id=:fee_id AND is_deleted='N'";
        $assignRow = $pdo->select($assignSql, array(":fee_id" => $feePrd));
     
        if(!empty($assignRow)){
          foreach ($assignRow as $key => $val) {
            $updParam = array('prd_fee_id'=>$advRuleId);
            $updWhere = array(
              'clause' => 'id = :id',
              'params' => array(':id' => $val['id'])
            ); 
            $pdo->update('prd_assign_fees', $updParam, $updWhere); 
          }
        }

        if(!empty($agentId)){
          $agentPrd = "SELECT id FROM agent_product_rule WHERE agent_id=:agent_id AND product_id=:prdId AND is_deleted='N'";
          $resAgentPrd = $pdo->selectOne($agentPrd, array(":agent_id" => $agentId,":prdId" => $feePrd));

          if(empty($resAgentPrd['id'])){
            $agentPrdSql = array(
                          "agent_id" => $agentId,
                          "admin_id" => $_SESSION['admin']['id'],
                          "product_id" => $feePrd,
                          "status" => "Contracted"
                          );
            $pdo->insert("agent_product_rule",$agentPrdSql);
          }
        }
      }
    }
  $res['status']="success";
  $res['advFeeId']=$advFeeIds;  
} else{
  $res['status'] = "fail";
  $res['errors'] = $validate->getErrors();  
}

header('Content-Type: application/json');
echo json_encode($res);
dbConnectionClose();
exit;
?>