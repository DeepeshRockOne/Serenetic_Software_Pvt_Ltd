<?php
include_once 'layout/start.inc.php';
$REQ_URL = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$validate = new Validation();

$assign_group = !empty($_POST['assign_group']) ? $_POST['assign_group'] : array();
$pay_options = !empty($_POST['pay_options']) ? $_POST['pay_options'] : array();

$cc_additional_charge = !empty($_POST['cc_additional_charge']) ? $_POST['cc_additional_charge'] : '';
$cc_charge_type = !empty($_POST['cc_charge_type']) ? $_POST['cc_charge_type'] : '';
$cc_charge = !empty($_POST['cc_charge']) ? $_POST['cc_charge'] : '';

$check_additional_charge = !empty($_POST['check_additional_charge']) ? $_POST['check_additional_charge'] : '';
$check_charge = !empty($_POST['check_charge']) ? $_POST['check_charge'] : '';

$remit_to_address = !empty($_POST['remit_to_address']) ? $_POST['remit_to_address'] : '';

$rule_id = !empty($_POST['rule_id']) ? $_POST['rule_id'] : 0;



if(empty($assign_group)){
  $validate->setError("assign_group","Please Select Group");
}

if(empty($pay_options)){
  $validate->setError("pay_options","Please Select Pay Options");
}else{
  if(in_array("ACH", $pay_options)){
    
  }
  if(in_array("CC", $pay_options)){
    $validate->string(array('required' => true, 'field' => 'cc_additional_charge', 'value' => $cc_additional_charge), array('required' => 'Select Any Option'));
    if($cc_additional_charge=='Y'){
      $validate->string(array('required' => true, 'field' => 'cc_charge_type', 'value' => $cc_charge_type), array('required' => 'Select Charge Type'));
      $validate->string(array('required' => true, 'field' => 'cc_charge', 'value' => $cc_charge), array('required' => 'Enter Charge'));
    }
  }
  if(in_array("Check", $pay_options)){
    $validate->string(array('required' => true, 'field' => 'remit_to_address', 'value' => $remit_to_address), array('required' => 'Remit To Address is required'));
     $validate->string(array('required' => true, 'field' => 'check_additional_charge', 'value' => $check_additional_charge), array('required' => 'Select Any Option'));
    if($check_additional_charge=='Y'){
      $validate->string(array('required' => true, 'field' => 'check_charge', 'value' => $check_charge), array('required' => 'Enter Charge'));
    }
  }
}


if ($validate->isValid()) {
  $updatedGroup = array();
  $updatedArr = array();
    if(!empty($assign_group)){
      foreach ($assign_group as $key => $value) {
          $sqlPayOptions = "SELECT id FROM group_pay_options where rule_type='Variation' and is_deleted='N' and id=:id and group_id = :group_id";
          $resPayOptions = $pdo->selectOne($sqlPayOptions,array(":id"=>$rule_id,":group_id"=>$value));

          $update_param = array(
              "group_id" => $value,
              "rule_type" => 'Variation',
              "is_ach"=>'N',
              "is_cc"=>'N',
              "cc_additional_charge"=>'N',
              "cc_charge_type"=>'N',
              "cc_charge"=>'0',
              "is_check"=>'N',
              "check_additional_charge"=>'N',
              "check_charge"=>'0',
              "remit_to_address"=>'',
          );
          if(in_array("ACH", $pay_options)){
            $update_param['is_ach'] = 'Y';
            
          }
          if(in_array("CC", $pay_options)){
            $update_param['is_cc'] = 'Y';
            $update_param['cc_additional_charge'] = $cc_additional_charge;

            if($cc_additional_charge=='Y'){
              $update_param['cc_charge_type'] = $cc_charge_type;
              $update_param['cc_charge'] = $cc_charge; 
            }
          }
          if(in_array("Check", $pay_options)){
            $update_param['is_check'] = 'Y';
            $update_param['check_additional_charge'] = $check_additional_charge;
            $update_param['remit_to_address'] = $remit_to_address;

            if($check_additional_charge=='Y'){
              $update_param['check_charge'] = $check_charge; 
            }
          }
          if(!empty($resPayOptions)){
            $updatedArr = $update_param;
            $upd_where = array(
              'clause' => 'id = :id',
              'params' => array(
                  ':id' => $resPayOptions['id'],
              ),
            );
            $updated_param['group_pay_options'] = $pdo->update('group_pay_options', $update_param, $upd_where,true);
          }else{      
            $inserted_param['group_pay_options'] = $update_param;
            $pdo->insert("group_pay_options", $update_param);
          } 
      }
    }
    $flg = true;

    if(!empty($inserted_param)){
      $insertedGroup = $pdo->selectOne("SELECT GROUP_CONCAT(CONCAT(business_name,' (',rep_id,')')) as groups from customer where id IN(".implode(',',$assign_group).")");
      
      foreach($inserted_param as $optionName => $groupOption){
       
        if(!empty($groupOption)){
          unset($groupOption['group_id']);
          $description['&nbsp;&nbsp;&nbsp;desc'.$optionName] = 'Inserted Group Pay Variation '.ucwords(str_replace('_',' ',$optionName)) .'<br>';
          if(!empty($insertedGroup['groups'])){
            $description['desc'.$optionName] = '<strong>Inserted Group: '.$insertedGroup['groups'].'</strong>';
          }
          foreach($groupOption as $key2 => $val){
            $description['&nbsp;&nbsp;&nbsp;'.$key2] = ucwords(str_replace('_',' ',$key2)) .": ".$val;
            $flg = "false";
          }
        }
      }
    }

    if(!empty($updated_param)){
      $updatedGroup = $pdo->selectOne("SELECT GROUP_CONCAT(CONCAT(business_name,' (',rep_id,')')) as groups from customer where id IN(".implode(',',$assign_group).")");
      
      foreach($updated_param as $optionName => $groupOption){
        if(!empty($groupOption)){
          unset($groupOption['group_id']);
          unset($groupOption['rule_type']);
          $description['&nbsp;&nbsp;&nbsp;desc'.$optionName] = 'Updated Variation '.ucwords(str_replace('_',' ',$optionName)) .'<br>';
          if(!empty($updatedGroup['groups'])){
            $description['desc'.$optionName] = '<strong>Updated Group: '.$updatedGroup['groups'].'</strong>';
          }
          foreach($groupOption as $key2 => $val){
            $description['&nbsp;&nbsp;&nbsp;'.$key2] = ucwords(str_replace('_',' ',$key2)) .": From ".$val.' to '.$updatedArr[$key2];
            $flg = "false";
          }
        }
      }
    }

    if($flg == "true"){
      $description['description_novalue'] = 'No updates in Variation Group Pay Option.';
    }
    $desc=json_encode($description);
    activity_feed(3,$_SESSION['admin']['id'],'Admin','0','Variation Group Pay Options','Admin Updated Variation Group Pay Options',"","",$desc);
    $response['status'] = 'success';
}
else{
	$response['status'] = "fail";
	$response['errors'] = $validate->getErrors();
}



header('Content-Type: application/json');
echo json_encode($response);
dbConnectionClose();
exit();
?>