<?php
include_once dirname(__FILE__) . '/layout/start.inc.php';

$validate = new Validation();
$response = array();

$receiving_agents = isset($_POST['receiving_agents']) ? $_POST['receiving_agents'] : 0;
$rule_code = checkIsset($_POST['rule_code']);
$status = isset($_POST['status']) ? $_POST['status'] : "";
$is_clone = isset($_POST['is_clone']) ? $_POST['is_clone'] : "";
$pmpm_id = isset($_POST['pmpm_id']) ? $_POST['pmpm_id'] : "";
$ids = isset($_POST['ids']) ? explode(',', $_POST['ids']) : array();
$ids = array_unique($ids);

if(!empty($pmpm_id)){
  $selectID = $pdo->selectOne("SELECT id FROM pmpm_commission where md5(id) = :id",array(":id" => $pmpm_id));
  if($selectID['id']){
    $pmpm_id = $selectID['id'];
  }
}

if(!$receiving_agents){
  $validate->setError('receiving_agents',"Please select Agents");
}
  $validate->string(array('required' => true, 'field' => 'rule_code', 'value' => $rule_code), array('required' => 'PMPM ID is required'));
  if(!$validate->getError('rule_code')){
    $ruleIncr ="";
    $ruleParams = array();
    $ruleParams[':rule_code']=$rule_code;
    if (!empty($pmpm_id)) {
      $ruleIncr.=" AND id!=:id";
      $ruleParams[':id']=$pmpm_id;
    } 

    $sqlRule = "SELECT id FROM pmpm_commission WHERE rule_code=:rule_code $ruleIncr AND is_deleted='N'";
    $resRule = $pdo->selectOne($sqlRule, $ruleParams);

    if(!empty($resRule['id'])){
      $validate->setError("rule_code", "This PMPM ID is already associated with another Rule");
    }
  }

$validate->string(array('required' => true, 'field' => 'status', 'value' => $status), array('required' => 'Status is required'));


if ($validate->isValid()) {
  $agent_data = $pdo->selectOne("SELECT rep_id FROM customer where id = :id",array(':id' => $receiving_agents));
  $insert_params = array(
      'rule_code' => $rule_code,
      'agent_id' => $receiving_agents,
      'status' => $status,
      'is_deleted' => "N"
  );

  if(!empty($pmpm_id) && $is_clone=='N'){
    $insert_params['updated_at']="msqlfunc_NOW()";
    $update_where = array(
        'clause' => 'id = :id',
        'params' => array(
            ':id' => $pmpm_id
        )
    );
    $oldValue = $pdo->selectOne("SELECT rule_code,agent_id,status,is_deleted FROM pmpm_commission WHERE id = :id",array(':id' => $pmpm_id));
    $newValue = $insert_params;
    $update_status = $pdo->update('pmpm_commission', $insert_params, $update_where);
    $response['message']="PMPM commission Updated successfully";

    unset($newValue['updated_at']);
    $checkDiff=array_diff_assoc($newValue, $oldValue);
  
    if(!empty($checkDiff)){

      $activityFeedDesc['ac_message'] =array(
        'ac_red_1'=>array(
          'href'=>$ADMIN_HOST.'/admin_profile.php?id='.md5($_SESSION['admin']['id']),
          'title'=>$_SESSION['admin']['display_id']),
        'ac_message_1' =>' Updated PMPM commission',
      ); 
      
      // $str = "";
      foreach ($checkDiff as $key1 => $value1) {
        $activityFeedDesc['key_value']['desc_arr'][$key1]= 'From '.$oldValue[$key1].' To '.$newValue[$key1];
        // $str .= $key1 . 'from ' . $oldValue[$key1] . ' to ' . $newValue[$key1] . ' ';
      }

      $activityFeedDesc['ac_message']['ac_red_2']=array(
        'href'=>$ADMIN_HOST.'/add_pmpm_commission.php?id='.md5($pmpm_id),
        'title'=>$rule_code
      ); 

      activity_feed(3, $_SESSION['admin']['id'], 'Admin', $pmpm_id, 'prd_fees','Admin Updated PMPM commission', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($activityFeedDesc));
    }



  }else{
    $insert_params['created_at']="msqlfunc_NOW()";
    $insert_params['updated_at']="msqlfunc_NOW()";
    $pmpm_id = $pdo->insert("pmpm_commission", $insert_params);

    $description['ac_message'] =array(
      'ac_red_1'=>array(
        'href'=>$ADMIN_HOST.'/admin_profile.php?id='.$_SESSION['admin']['id'],
        'title'=>$_SESSION['admin']['display_id'],
      ),
      'ac_message_1' =>' created PMPM commission ',
      'ac_red_2'=>array(
          'href'=>$ADMIN_HOST.'/add_pmpm_commission.php?id='.md5($pmpm_id),
          'title'=>$rule_code,
      ),
    ); 

    activity_feed(3, $_SESSION['admin']['id'], 'Admin', $pmpm_id, 'pmpm_commission','created PMPM commission', $_SESSION['admin']['fname'],$_SESSION['admin']['lname'],json_encode($description));

    $response['message']="PMPM commission created successfully";
  }

  if($ids && $is_clone == 'N'){
    $update_params = array('commission_id' => $pmpm_id);
    foreach ($ids as $k => $v) {
      
      $rules = $pdo->select("SELECT id,commission_id FROM pmpm_commission_rule WHERE id = :rule_id and is_deleted = 'N'",array(':rule_id' => $v));

      if($rules){
        $update_where = array(
          'clause' => "id = :id AND is_deleted = 'N'",
          'params' => array(
              ':id' => $v
          )
        );
        $pdo->update('pmpm_commission_rule',$update_params,$update_where);
      }

      $update_where = array(
        'clause' => "rule_id = :id AND is_deleted = 'N'",
        'params' => array(
            ':id' => $v
        )
      );
      $assign_agents = $pdo->select("SELECT id,commission_id FROM pmpm_commission_rule_assign_agent WHERE rule_id = :rule_id and is_deleted = 'N'",array(':rule_id' => $v));

      if($assign_agents){
        $pdo->update('pmpm_commission_rule_assign_agent',$update_params,$update_where);
      }

      $assign_products = $pdo->select("SELECT id,commission_id FROM pmpm_commission_rule_assign_product WHERE rule_id = :rule_id and is_deleted = 'N'",array(':rule_id' => $v));

      if($assign_products){
        $pdo->update('pmpm_commission_rule_assign_product',$update_params,$update_where);
      }

      $plan_type = $pdo->select("SELECT id,commission_id FROM pmpm_commission_rule_plan_type WHERE rule_id = :rule_id and is_deleted = 'N'",array(':rule_id' => $v));

      if($plan_type){
        $pdo->update('pmpm_commission_rule_plan_type',$update_params,$update_where);
      }
    }
  }

  if($ids && $is_clone == 'Y'){
    foreach ($ids as $k => $v) {
      
      $rules = $pdo->selectOne("SELECT * FROM pmpm_commission_rule WHERE id = :rule_id and is_deleted = 'N'",array(':rule_id' => $v));

      if($rules){
        if(!empty($rules['commission_id'])) {
          $insert_params = $rules;
          unset($insert_params['id']);
          $insert_params['created_at'] = 'msqlfunc_NOW()';
          $insert_params['updated_at'] = 'msqlfunc_NOW()';
          $insert_params['display_id'] = get_pmpm_comm_fee_id();
          $insert_params['commission_id'] = $pmpm_id;        
      
          $rule_id = $pdo->insert('pmpm_commission_rule',$insert_params);

          $plans = $pdo->select("SELECT * FROM pmpm_commission_rule_plan_type WHERE rule_id = :rule_id and is_deleted = 'N'",array(':rule_id' => $v));

          if($plans){
            foreach ($plans as $key => $value) {
              $insert_params = $value;
              unset($insert_params['id']);
              $insert_params['created_at'] = 'msqlfunc_NOW()';
              $insert_params['updated_at'] = 'msqlfunc_NOW()';
              $insert_params['commission_id'] = $pmpm_id;
              $insert_params['rule_id'] = $rule_id;

              $pdo->insert('pmpm_commission_rule_plan_type',$insert_params);
            }
          }

          $products = $pdo->select("SELECT * FROM pmpm_commission_rule_assign_product WHERE rule_id = :rule_id and is_deleted = 'N'",array(':rule_id' => $v));

          if($products){
            foreach ($products as $key => $value) {
             
              $insert_params = $value;
              unset($insert_params['id']);
              $insert_params['created_at'] = 'msqlfunc_NOW()';
              $insert_params['updated_at'] = 'msqlfunc_NOW()';
              $insert_params['commission_id'] = $pmpm_id;
              $insert_params['rule_id'] = $rule_id;

              $pdo->insert('pmpm_commission_rule_assign_product',$insert_params);
            }
          }

          $agents = $pdo->select("SELECT * FROM pmpm_commission_rule_assign_agent WHERE rule_id = :rule_id and is_deleted = 'N'",array(':rule_id' => $v));

          if($agents){
            foreach ($agents as $key => $value) {
              
              $insert_params = $value;
              unset($insert_params['id']);
              $insert_params['created_at'] = 'msqlfunc_NOW()';
              $insert_params['updated_at'] = 'msqlfunc_NOW()';
              $insert_params['commission_id'] = $pmpm_id;
              $insert_params['rule_id'] = $rule_id;

              $pdo->insert('pmpm_commission_rule_assign_agent',$insert_params);
            }
          }
        } else {
          $update_params = array('commission_id' => $pmpm_id);
          $rule_id = $v;
          $update_where = array(
            'clause' => "id = :id AND is_deleted = 'N'",
            'params' => array(
                ':id' => $rule_id
            )
          );
          $pdo->update('pmpm_commission_rule',$update_params,$update_where);

          $update_where = array(
            'clause' => "rule_id = :id AND is_deleted = 'N'",
            'params' => array(
                ':id' => $v
            )
          );
          $assign_agents = $pdo->select("SELECT id,commission_id FROM pmpm_commission_rule_assign_agent WHERE rule_id = :rule_id and is_deleted = 'N'",array(':rule_id' => $v));

          if($assign_agents){
            $pdo->update('pmpm_commission_rule_assign_agent',$update_params,$update_where);
          }

          $assign_products = $pdo->select("SELECT id,commission_id FROM pmpm_commission_rule_assign_product WHERE rule_id = :rule_id and is_deleted = 'N'",array(':rule_id' => $v));

          if($assign_products){
            $pdo->update('pmpm_commission_rule_assign_product',$update_params,$update_where);
          }

          $plan_type = $pdo->select("SELECT id,commission_id FROM pmpm_commission_rule_plan_type WHERE rule_id = :rule_id and is_deleted = 'N'",array(':rule_id' => $v));
          if($plan_type){
            $pdo->update('pmpm_commission_rule_plan_type',$update_params,$update_where);
          }
        } 
      }
    }
  }
  


  setNotifySuccess($response['message']);
  $response['redirect_url']=$ADMIN_HOST.'/pmpm_commission.php';    
  $response['status']="success";
  $response['pmpm_id']=$pmpm_id;
  
} else{
  $response['status'] = "fail";
  $response['errors'] = $validate->getErrors();  
}


header('Content-Type: application/json');
echo json_encode($response);
dbConnectionClose();
exit;
?>