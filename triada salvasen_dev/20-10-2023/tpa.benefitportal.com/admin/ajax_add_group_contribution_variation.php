<?php
include_once 'layout/start.inc.php';
$REQ_URL = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$validate = new Validation();

$assign_group = !empty($_POST['assign_group']) ? $_POST['assign_group'] : array();

$minimum_group_contribution = !empty($_POST['minimum_group_contribution']) ? $_POST['minimum_group_contribution'] : '';
$products = isset($_POST['products']) ? $_POST['products'] : array();
$contribution_type = !empty($_POST['contribution_type']) ? $_POST['contribution_type'] : array();
$contribution = !empty($_POST['contribution']) ? $_POST['contribution'] : array() ;
$percentage_calculate_by = !empty($_POST['percentage_calculate_by']) ? $_POST['percentage_calculate_by'] : array();

$rule_id = !empty($_POST['rule_id']) ? $_POST['rule_id'] : 0;

if(empty($assign_group)){
  $validate->setError("assign_group","Please Select Group");
}

$validate->string(array('required' => true, 'field' => 'minimum_group_contribution', 'value' => $minimum_group_contribution), array('required' => 'Select Any Option'));
if($minimum_group_contribution=='Y'){
  if(!empty($contribution)){
    foreach ($contribution as $key => $value) {
      if(empty($products[$key])){
        $validate->setError('products_'.$key,"Select Product");
      }
      if(empty($contribution_type[$key])){
        $validate->setError('contribution_type_'.$key,"Select Any Option");
      }
      $validate->string(array('required' => true, 'field' => 'contribution_'.$key, 'value' => $contribution[$key]), array('required' => 'Set Contribution'));

      if(isset($contribution_type[$key]) && $contribution_type[$key] == "Percentage"){
        if(empty($percentage_calculate_by[$key])){
          $validate->setError('percentage_calculate_by_'.$key,"Select Any Option");
        }
      }
    }
  }else{
    $validate->setError("general_minimum_group_contribution","Please Add Group contribution");
  }
}


if ($validate->isValid()) {
  $updatedArr = array();
    if(!empty($assign_group)){
      foreach ($assign_group as $key => $value) {
          $sqlGroupContribution = "SELECT * FROM group_contribution_rule where is_deleted='N' AND rule_type='Variation' and id=:id and group_id = :group_id";
          $resGroupContribution = $pdo->selectOne($sqlGroupContribution,array(":id"=>$rule_id,":group_id"=>$value));

          $update_param = array(
            'group_id'=>$value,
            "rule_type" => 'Variation',
            "minimum_group_contribution"=>$minimum_group_contribution,
          );
          if(!empty($resGroupContribution)){
            $upd_where = array(
              'clause' => 'id = :id',
              'params' => array(
                  ':id' => $resGroupContribution['id'],
              ),
            );
            $pdo->update('group_contribution_rule', $update_param, $upd_where);
            $group_contribution_rule_id = $resGroupContribution['id'];
          }else{
            $group_contribution_rule_id = $pdo->insert("group_contribution_rule", $update_param);
          }

          if($minimum_group_contribution == 'Y'){
            if(!empty($contribution)){
              foreach ($contribution as $key => $value) {
                  $sqlGroupContributionSetting = "SELECT * FROM group_contribution_setting where is_deleted='N' AND id=:id AND group_contribution_rule_id = :group_contribution_rule_id";
                  $resGroupContributionSetting = $pdo->selectOne($sqlGroupContributionSetting,array(":id"=>$key,":group_contribution_rule_id"=>$group_contribution_rule_id));

                  $update_param = array(
                    'group_contribution_rule_id'=>$group_contribution_rule_id,
                    'products'=>!empty($products[$key]) ? implode(",", $products[$key]) : '',
                    'contribution_type'=>$contribution_type[$key],
                    'contribution'=>$contribution[$key],
                    'percentage_calculate_by'=>'',
                  );
                  if($contribution_type[$key]=="Percentage"){
                    $update_param['percentage_calculate_by']=$percentage_calculate_by[$key];
                  }
                  
                  if(!empty($resGroupContributionSetting)){
                    $upd_where = array(
                      'clause' => 'id = :id',
                      'params' => array(
                          ':id' => $resGroupContributionSetting['id'],
                      ),
                    );
                    $updatedArr = $update_param;
                    $updated_param['group_contribution_setting'] = $pdo->update('group_contribution_setting', $update_param, $upd_where,true);
                    $group_contribution_setting_id = $resGroupContributionSetting['id'];
                  }else{
                    $inserted_param['group_contribution_setting'] = $update_param;
                    $group_contribution_setting_id = $pdo->insert("group_contribution_setting", $update_param);
                  }
              }
            }
          }else{
            $update_param = array(
              'is_deleted'=>'Y',
            );
            $upd_where = array(
              'clause' => 'group_contribution_rule_id = :id',
              'params' => array(
                  ':id' => $group_contribution_rule_id,
              ),
            );
            $pdo->update('group_contribution_setting', $update_param, $upd_where);
          }
      }
    }
    
    $flg = true;

    if(!empty($inserted_param)){
      $insertedGroup = $pdo->selectOne("SELECT GROUP_CONCAT(CONCAT(business_name,' (',rep_id,')')) as groups from customer where id IN(".implode(',',$assign_group).")");
      
      foreach($inserted_param as $optionName => $groupOption){
       
        if(!empty($groupOption)){
          unset($groupOption['group_id']);
          unset($groupOption['group_contribution_rule_id']);
          $description['&nbsp;&nbsp;&nbsp;desc'.$optionName] = 'Inserted Group Contribution Variation '.ucwords(str_replace('_',' ',$optionName)) .'<br>';
          if(!empty($insertedGroup['groups'])){
            $description['desc'.$optionName] = '<strong>Inserted Group: '.$insertedGroup['groups'].'</strong>';
          }
          foreach($groupOption as $key2 => $val){
            if(!empty($key2 == 'products')){
              $insertedProd = $pdo->selectOne("SELECT GROUP_CONCAT(CONCAT(name ,' (' , product_code,')')) as products from prd_main where id IN(".$val.")");
              $description['ins_pro'.$key2] = " &nbsp;&nbsp;&nbsp; Inserted Products: ".$insertedProd['products'];
              $flg = "false";
            }else{
              $description['&nbsp;&nbsp;&nbsp;'.$key2] = ucwords(str_replace('_',' ',$key2)) .": ".ucwords(str_replace('_',' ',$val));
              $flg = "false";
            }
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
          $description['&nbsp;&nbsp;&nbsp;desc'.$optionName] = 'Updated Group Contribution Variation '.ucwords(str_replace('_',' ',$optionName)) .'<br>';
          if(!empty($updatedGroup['groups'])){
            $description['desc'.$optionName] = '<strong>Updated Group: '.$updatedGroup['groups'].'</strong>';
          }
          foreach($groupOption as $key2 => $val){
            if(!empty($key2 == 'products')){
              $insPrd = array_diff(explode(',',$updatedArr[$key2]),explode(',',$val));
              $delPrd = array_diff(explode(',',$val),explode(',',$updatedArr[$key2]));
              if(!empty($insPrd)){
                $insertedProd = $pdo->selectOne("SELECT GROUP_CONCAT(CONCAT(' ',name ,' (' , product_code,')')) as products from prd_main where id IN(".implode(',',$insPrd).")");
                if(!empty($insertedProd['products'])){
                  $description['ins_pro'.$key2] = " &nbsp;&nbsp;&nbsp; Inserted Products: ".$insertedProd['products'];
                }
              }
              if(!empty($delPrd)){
                $deletedProd = $pdo->selectOne("SELECT GROUP_CONCAT(CONCAT(name ,' (' , product_code,')')) as products from prd_main where id IN(".implode(',',$delPrd).")");
                if(!empty($deletedProd['products'])){
                  $description['del_pro'.$key2] = " &nbsp;&nbsp;&nbsp; Deleted Products: ".($deletedProd['products']);
                }
              }
              $flg = "false";
            }else{
              $description['&nbsp;&nbsp;&nbsp;'.$key2] = ucwords(str_replace('_',' ',$key2)) .": From ".ucwords(str_replace('_',' ',$val)).' to '.ucwords(str_replace('_',' ',$updatedArr[$key2]));
              $flg = "false";
            }
          }
        }
      }
    }

    if($flg == "true"){
      $description['description_novalue'] = 'No updates in Variation Group Contribution .';
    }
    $desc=json_encode($description);
    activity_feed(3,$_SESSION['admin']['id'],'Admin','0','Variation Group Contribution','Admin Updated Variation Group Contribution',"","",$desc);
       
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