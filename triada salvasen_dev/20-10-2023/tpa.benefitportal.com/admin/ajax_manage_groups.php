<?php
include_once 'layout/start.inc.php';
$REQ_URL = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$validate = new Validation();

$pay_options = !empty($_POST['pay_options']) ? $_POST['pay_options'] : array();

$cc_additional_charge = !empty($_POST['cc_additional_charge']) ? $_POST['cc_additional_charge'] : '';
$cc_charge_type = !empty($_POST['cc_charge_type']) ? $_POST['cc_charge_type'] : '';
$cc_charge = !empty($_POST['cc_charge']) ? $_POST['cc_charge'] : '';

$check_additional_charge = !empty($_POST['check_additional_charge']) ? $_POST['check_additional_charge'] : '';
$check_charge = !empty($_POST['check_charge']) ? $_POST['check_charge'] : '';

$remit_to_address = !empty($_POST['remit_to_address']) ? $_POST['remit_to_address'] : '';

$group_use_cobra_benefit = !empty($_POST['group_use_cobra_benefit']) ? $_POST['group_use_cobra_benefit'] : '';
$is_additional_surcharge = !empty($_POST['is_additional_surcharge']) ? $_POST['is_additional_surcharge'] : '';
$additional_surcharge = !empty($_POST['additional_surcharge']) ? $_POST['additional_surcharge'] : '';

$minimum_group_contribution = !empty($_POST['minimum_group_contribution']) ? $_POST['minimum_group_contribution'] : '';
$products = isset($_POST['products']) ? $_POST['products'] : array();
$contribution_type = !empty($_POST['contribution_type']) ? $_POST['contribution_type'] : array();
$contribution = !empty($_POST['contribution']) ? $_POST['contribution'] : array() ;
$percentage_calculate_by = !empty($_POST['percentage_calculate_by']) ? $_POST['percentage_calculate_by'] : array();

$save_type = !empty($_POST['save_type']) ? $_POST['save_type'] : 'all_options';

if($save_type == 'all_options' || $save_type =='pay_option'){
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
}


if($save_type == 'all_options' || $save_type =='cobra_benefits'){
  $validate->string(array('required' => true, 'field' => 'group_use_cobra_benefit', 'value' => $group_use_cobra_benefit), array('required' => 'Select any option'));
  if($group_use_cobra_benefit == 'Y'){
    $validate->string(array('required' => true, 'field' => 'is_additional_surcharge', 'value' => $is_additional_surcharge), array('required' => 'Select any option'));
    if($is_additional_surcharge=='Y'){
        $validate->string(array('required' => true, 'field' => 'additional_surcharge', 'value' => $additional_surcharge), array('required' => 'Enter surcharge percentage'));
    }

  }
}
if($save_type == 'all_options' || $save_type =='group_contribution'){
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
}

if ($validate->isValid()) {
  $notifyMsg = "Group Options Added Successfully";
  $inserted_param = $updated_param = $updated_options = array();

  if($save_type == 'all_options' || $save_type =='pay_option'){
    $sqlPayOptions = "SELECT id FROM group_pay_options where rule_type='Global' and is_deleted='N'";
    $resPayOptions = $pdo->selectOne($sqlPayOptions);

    $update_param = array(
        "group_id" => 0,
        "rule_type" => 'Global',
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
    $updated_options = $update_param;
    if(!empty($resPayOptions)){
     
      $upd_where = array(
        'clause' => 'id = :id',
        'params' => array(
            ':id' => $resPayOptions['id'],
        ),
    	);
     $updated_param['pay_options'] = $pdo->update('group_pay_options', $update_param, $upd_where,true);
    }else{      
      $inserted_param['pay_options'] = $update_param;
      $group_pay_option_id = $pdo->insert("group_pay_options", $update_param);
    }
    $notifyMsg = 'Pay Options Added Successfully';
  }

  if($save_type == 'all_options' || $save_type =='cobra_benefits'){
    $sqlCobraBenefit = "SELECT * FROM group_cobra_benefits where is_deleted ='N'";
    $resCobraBenefit = $pdo->selectOne($sqlCobraBenefit);

    $update_param = array(
      'group_use_cobra_benefit'=>$group_use_cobra_benefit,
      'is_additional_surcharge'=>'N',
      'additional_surcharge'=>'0',
    );
    if($group_use_cobra_benefit == 'Y'){
      $update_param['is_additional_surcharge'] = $is_additional_surcharge;
      if($is_additional_surcharge == 'Y'){
        $update_param['additional_surcharge'] = $additional_surcharge;
      }
    }
    $updated_options = array_merge($updated_options,$update_param);
    if(!empty($resCobraBenefit)){
      $upd_where = array(
        'clause' => 'id = :id',
        'params' => array(
            ':id' => $resCobraBenefit['id'],
        ),
      );
      $updated_param['cobra_benefits'] = $pdo->update('group_cobra_benefits', $update_param, $upd_where,true);
    }else{
      $inserted_param['cobra_benefits'] = $update_param;
      $group_cobra_benefits_id = $pdo->insert("group_cobra_benefits", $update_param);
    }
    $notifyMsg = 'COBRA Benefits Added Successfully';
  }

  if($save_type == 'all_options' || $save_type =='group_contribution'){
    $sqlGroupContribution = "SELECT * FROM group_contribution_rule where is_deleted='N' AND rule_type='Global'";
    $resGroupContribution = $pdo->selectOne($sqlGroupContribution);
    $update_param = array(
      'group_id'=>0,
      "rule_type" => 'Global',
      "minimum_group_contribution"=>$minimum_group_contribution,
    );
    $updated_options = array_merge($updated_options,$update_param);
    if(!empty($resGroupContribution)){
      $upd_where = array(
        'clause' => 'id = :id',
        'params' => array(
            ':id' => $resGroupContribution['id'],
        ),
      );
      $updated_param['group_contribution_rule'] = $pdo->update('group_contribution_rule', $update_param, $upd_where,true);
      $group_contribution_rule_id = $resGroupContribution['id'];
    }else{
      $inserted_param['group_contribution_rule'] = $update_param;
      $group_contribution_rule_id = $pdo->insert("group_contribution_rule", $update_param);
    }

    if($minimum_group_contribution == 'Y'){
      if(!empty($contribution)){
        foreach ($contribution as $key => $value) {
            $sqlGroupContributionSetting = "SELECT * FROM group_contribution_setting where is_deleted='N' AND id=:id AND group_contribution_rule_id=:group_contribution_rule_id";
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
            $updated_options['group_contribution_setting'][$key] = $update_param;
            if(!empty($resGroupContributionSetting)){
              $upd_where = array(
                'clause' => 'id = :id',
                'params' => array(
                    ':id' => $resGroupContributionSetting['id'],
                ),
              );
              $updated_param['group_contribution_setting'][$key] = $pdo->update('group_contribution_setting', $update_param, $upd_where,true);
              $group_contribution_setting_id = $resGroupContributionSetting['id'];
            }else{
              $inserted_param['minimum_group_contribution'][] = $update_param ;
              $group_contribution_setting_id = $pdo->insert("group_contribution_setting", $update_param);
            }
        }
      }
    }else{
      $updated_options = array_merge($updated_options,$update_param);
      $update_param = array(
        'is_deleted'=>'Y',
      );
      $upd_where = array(
        'clause' => 'group_contribution_rule_id = :id',
        'params' => array(
            ':id' => $group_contribution_rule_id,
        ),
      );
      $updated_param['group_contribution_setting_deleted'] = $pdo->update('group_contribution_setting', $update_param, $upd_where,true);
    }
    $notifyMsg = 'Minimum Group Contribution Added Successfully';
  }

    /*--- Activity Feed -----*/

      $description = array();
      $flg = "true";
      
      $description['ac_message'] = array(
        'ac_red_1'=>array(
            'href' => 'admin_profile.php?id='.md5($_SESSION['admin']['id']),
            'title' => $_SESSION['admin']['display_id'],
        ),
        'ac_message_1' =>' Updated Manage Group Options'
      );

      if(!empty($inserted_param)){
        foreach($inserted_param as $optionName => $groupOption){
          if(!empty($groupOption)){
            $description['desc'.$optionName] = '<strong>Inserted In '.ucwords(str_replace('_',' ',$optionName)) .'</strong><br>';
            if($optionName == 'minimum_group_contribution'){
              foreach($groupOption as $key2 => $val){
                $description['br_'.$key2] = ($key2+1).'.<br>';
                if(!empty($val['products'])){
                  $insertedProd = $pdo->selectOne("SELECT GROUP_CONCAT(CONCAT(name ,' (' , product_code,')')) as products from prd_main where id IN(".$val['products'].")");
                  $description['ins_pro'.$key2] = " &nbsp;&nbsp;&nbsp;Products: ".$insertedProd['products'];
                }
                $description['ins_con_type'.$key2] = " &nbsp;&nbsp;&nbsp;Contribution Type: ".$val['contribution_type'];
                $description['ins_con'.$key2] = " &nbsp;&nbsp;&nbsp;Contribution: ".$val['contribution'];
                if(!empty($val['percentage_calculate_by']))
                  $description['ins_cal_by'.$key2] = " &nbsp;&nbsp;Percentage Caclulate By : ".str_replace('_',' ',$val['percentage_calculate_by']);
                
                $flg = "false";
              }
            }else{
              foreach($groupOption as $key2 => $val){
                unset($groupOption['group_id']);
                unset($groupOption['group_contribution_rule_id']);
                $key2_tmp = ucwords(str_replace('_',' ',$key2));
                $description['key_value']['desc_arr']['&nbsp;&nbsp;'.$key2_tmp] = $inserted_param[$optionName][$key2].".<br>";
                $flg = "false";
              }
            }
          }
        }
      }

      if(!empty($updated_param)){
        foreach($updated_param as $optionName => $groupOption){
          if(!empty($groupOption)){
            if($optionName !='group_contribution_setting'){
              $description['key_value']['desc_arr']['<strong>'.$optionName.'</strong>'] = '';
            }
            $i=1;
            foreach($groupOption as $key2 => $val){
              if($optionName =='group_contribution_setting' && $i==1 && !empty($val)){
                $description['key_value']['desc_arr']['<strong>'.$optionName.'</strong>'] = '';
              }
              if($optionName =='group_contribution_setting' && empty($val)){
                continue;
              }
              if(is_array($val) && $optionName =='group_contribution_setting'){
                if(in_array($key2,array_keys($updated_options['group_contribution_setting'])) && !empty($val)){
                  $description['key_value']['desc_arr']['Settings '.$i] = '<br>';
                }
                foreach($val as $kv => $v1){
                  if(!empty($kv == 'products')){
                    $prdoducts_str = $updated_options['group_contribution_setting'][$key2]['products'];

                    $insPrd = array_diff(explode(',',$prdoducts_str),explode(',',$v1));
                    $delPrd = array_diff(explode(',',$v1),explode(',',$prdoducts_str));
                    if(!empty($insPrd)){
                      $insertedProd = $pdo->selectOne("SELECT GROUP_CONCAT(CONCAT(' ',name ,' (' , product_code,')')) as products from prd_main where id IN(".implode(',',$insPrd).")");
                      if(!empty($insertedProd['products'])){
                        $description['key_value']['desc_arr']['&nbsp;&nbsp; Inserted Products'.$i] = $insertedProd['products'];
                      }
                    }
                    if(!empty($delPrd)){
                      $deletedProd = $pdo->selectOne("SELECT GROUP_CONCAT(CONCAT(name ,' (' , product_code,')')) as products from prd_main where id IN(".implode(',',$delPrd).")");
                      if(!empty($deletedProd['products'])){
                        $description['key_value']['desc_arr']['&nbsp;&nbsp; Deleted Products'.$i] = $deletedProd['products'];
                      }
                    }
                    $flg = "false";
                  }else{
                    if(in_array($key2,array_keys($updated_options['group_contribution_setting']))){
                      $kv_tmp = ucwords(str_replace('_',' ',$kv));
                      $description['key_value']['desc_arr']['&nbsp;&nbsp;'.$kv_tmp.$i] = ' Updated From '.ucwords(str_replace('_',' ',$v1))." To ".ucwords(str_replace('_',' ',$updated_options['group_contribution_setting'][$key2][$kv])).".<br>";
                      $flg = "false";
                    } else {
                        $description['description2'][] = ucwords(str_replace('_',' ',$kv));
                        $flg = "false";
                    }
                  }
                }
                $i++;
              }else{
                if(array_key_exists($key2,$updated_options)){
                  $key2_tmp = ucwords(str_replace('_',' ',$key2));
                  $description['key_value']['desc_arr']['&nbsp;&nbsp;'.$key2_tmp] = ' Updated From '.$val." To ".$updated_options[$key2].".<br>";
                  $flg = "false";
                } else {
                    $description['description2'][] = ucwords(str_replace('_',' ',$val));
                    $flg = "false";
                }
              }
            }
          }
        }
      }
      if($flg == "true"){
        $description['description_novalue'] = 'No updates in Manage Group.';
      }
      $desc=json_encode($description);
      activity_feed(3,$_SESSION['admin']['id'],'Admin','0','Group Options','Admin Updated Group Options',"","",$desc);
    /*---/Activity Feed -----*/
    
    if($save_type == 'all_options'){
      setNotifySuccess("Group Options Added Successfully");
    }
    $response['msg'] = $notifyMsg;
    $response['save_type'] = $save_type;
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